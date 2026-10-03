<?php
// Run with `php scripts/check-field-type-save.php`; EE services and DB writes are simulated.
namespace EllisLab\ExpressionEngine\Service\Model {
    class Model {
        public function __get($key) { return $this->$key ?? null; }
        public function __set($key, $value) { $this->$key = $value; }
        public function set($values) { foreach ($values as $key => $value) $this->$key = $value; return $this; }
        public function save() {
            $db = \ee()->db;
            if ($this instanceof \Solspace\Addons\FreeformNext\Model\FieldModel) {
                $db->records['field'] = $this->jsonSerialize();
            } else {
                if ($db->failForm === $this->id) throw new \RuntimeException('Mock form write failed');
                $db->records['form'.$this->id] = $this->layoutJson;
            }
            return $this;
        }
    }
}
namespace {
    error_reporting(E_ALL);
    set_error_handler(function($s,$m,$f,$l) { throw new ErrorException($m,0,$s,$f,$l); });
    define('PATH_THIRD', dirname(__DIR__).'/src/');
    define('URL_THIRD_THEMES', '/themes/');
    define('APP_VER', '7.5.27');
    function lang($text) { return $text; }
    class MockDB {
        public array $records = [], $snapshot = [], $log = [];
        public ?int $failForm = null;
        public bool $healthy = true, $failRead = false;
        public ?int $failSubmission = null;
        private int $lastId = 0, $updateId = 0, $batchLimit = 500;
        public function select($columns) { return $this; }
        public function from($table) { return $this; }
        public function where($column, $value = null) { if ($column === 'id >') $this->lastId = $value; elseif ($column === 'id') $this->updateId = $value; return $this; }
        public function order_by(...$args) { return $this; }
        public function limit($limit) { $this->batchLimit = $limit; return $this; }
        public function get() {
            if ($this->failRead) return false;
            $rows = array_values(array_filter($this->records['submissions'] ?? [], fn($row) => $row['id'] > $this->lastId));
            return new class(array_slice($rows, 0, $this->batchLimit)) {
                public function __construct(private array $rows) {}
                public function result_array() { return $this->rows; }
            };
        }
        public function update($table, $values) {
            if ($this->updateId === $this->failSubmission) return false;
            foreach ($this->records['submissions'] as &$row) if ($row['id'] === $this->updateId) $row = array_replace($row, $values);
            return true;
        }
        public function trans_begin() { $this->snapshot = $this->records; $this->log[] = 'begin'; return true; }
        public function trans_status() { return $this->healthy; }
        public function trans_commit() { $this->log[] = 'commit'; return true; }
        public function trans_rollback() { $this->records = $this->snapshot; $this->log[] = 'rollback'; return true; }
        public function __call($name,$args) { if ($name === 'result_array') return []; $this->log[]=$name; return $this; }
    }
    class MockQuery {
        public function __construct(public string $model) {}
        public function filter(...$args) { return $this; }
        public function first() { return $GLOBALS['field']; }
        public function all() { return $this; }
        public function asArray() { return $GLOBALS['forms']; }
    }
    class MockModelService { public function get($name) { return new MockQuery($name); } }
    class MockAlert {
        public array $messages = [];
        public function __call($name,$args) { if ($name === 'withTitle') $this->messages[]=$args[0]; return $this; }
    }
    $GLOBALS['alerts'] = new MockAlert();
    $GLOBALS['ee'] = (object) [
        'db' => new MockDB(),
        'session' => new class { public function userdata($name) { return 1; } },
        'config' => new class { public function item($name) { return $name==='site_id' ? 1 : 'n'; } },
        'cp' => new class { public function add_js_script(...$args) {} },
        'extensions' => new class { public bool $end_script=false; public function call(...$args) {} },
    ];
    function ee($service=null,...$args) {
        return match($service) { null => $GLOBALS['ee'], 'Model' => new MockModelService(), 'CP/Alert' => $GLOBALS['alerts'], default => throw new RuntimeException($service) };
    }
    require dirname(__DIR__).'/src/freeform_next/vendor/autoload.php';
    $controller = new class extends \Solspace\Addons\FreeformNext\Controllers\FieldController {
        protected function getPermissionsService() { return new class { public function canAccessFields($id): bool { return true; } }; }
        protected function getFieldsService() { return $GLOBALS['fieldService'] ?? parent::getFieldsService(); }
        protected function getLink($target) { return '/'.$target; }
    };
    function check($condition,$label) { if (!$condition) throw new RuntimeException($label); echo "PASS: $label\n"; }
    function setup($type='phone') {
        $GLOBALS['field'] = (new \Solspace\Addons\FreeformNext\Model\FieldModel())->set(['id'=>42,'type'=>$type,'label'=>'Contact','handle'=>'contact','value'=>'0123','options'=>[['value'=>'0','label'=>'Zero'],['value'=>'002','label'=>'Two']],'values'=>['0','002'],'additionalProperties'=>['pattern'=>'(xxx) xxx-xxxx']]);
        $properties = ['id'=>42,'type'=>$type,'hash'=>'hash42','label'=>'Custom label','handle'=>'contact','value'=>'0','values'=>['0','002'],'options'=>[['label'=>'Zero','value'=>'0'],['label'=>'Two','value'=>'002']], 'pattern'=>'(xxx) xxx-xxxx'];
        $layout = json_encode(['composer'=>['properties'=>(object)['hash42'=>$properties], 'layout'=>[['hash42']]], 'context'=>(object)[]]);
        $GLOBALS['forms'] = [];
        foreach ([1,2] as $id) $GLOBALS['forms'][]=(new \Solspace\Addons\FreeformNext\Model\FormModel())->set(['id'=>$id,'layoutJson'=>$layout]);
        ee()->db = new MockDB();
        ee()->db->records = ['field'=>$GLOBALS['field']->jsonSerialize(), 'form1'=>$layout, 'form2'=>$layout, 'submission42'=>"0123\nmultiline"];
        $_POST=['label'=>'Contact','handle'=>'contact','type'=>'text','types'=>['text'=>['value'=>'0123','placeholder'=>'Your contact']]];
    }
    setup();
    $controller->save(42);
    check(ee()->db->records['field']['type']==='text', 'controller saves compatible target');
    foreach ([1,2] as $id) check(json_decode(ee()->db->records['form'.$id])->composer->properties->hash42->type === 'text', 'saved form '.$id.' updated');
    check(ee()->db->records['submission42']==="0123\nmultiline", 'submission data untouched');
    check(ee()->db->log===['begin','commit'], 'type change uses one transaction without DDL');
    setup(); $original=ee()->db->records; ee()->db->failForm=2; $controller->save(42);
    check(ee()->db->records===$original && ee()->db->log===['begin','rollback'], 'form write failure rolls back field and both forms');
    setup(); $original=ee()->db->records; ee()->db->healthy=false; $controller->save(42);
    check(ee()->db->records===$original && ee()->db->log===['begin','rollback'], 'database failure rolls back whole change');
    foreach (['file','multiple_select',['text']] as $target) {
        setup(); $original=ee()->db->records; $_POST['type']=$target; $controller->save(42);
        check(ee()->db->records===$original && ee()->db->log===[], 'incompatible or malformed POST rejected before writes');
    }
    setup('multiple_select'); $_POST['type']='checkbox_group'; $_POST['types']=['checkbox_group'=>['custom_values'=>'1','values'=>['0','002'],'labels'=>['Zero','Two'],'checked_by_default'=>['1','1']]]; $controller->save(42);
    check(ee()->db->records['field']['values']===['0','002'], 'multiple defaults retained by controller');
    setup('select'); $_POST['type']='radio_group'; $_POST['types']=['radio_group'=>['custom_values'=>'1','values'=>['0','002'],'labels'=>['Zero','Two'],'checked_by_default'=>['1','0']]]; $controller->save(42);
    check(ee()->db->records['field']['value']==='0', 'zero-valued selection retained by controller');
    setup();
    $vars=$controller->edit(42)->getTemplateVariables();
    if (getenv('FREEFORM_FIELD_FIXTURE')) file_put_contents(getenv('FREEFORM_FIELD_FIXTURE'), json_encode($vars['sections'],JSON_PRETTY_PRINT));
    $picker=$vars['sections'][0][4]['fields']['type'];
    check(!$picker['disabled'] && count($picker['choices'])===9 && !array_diff(['text','textarea','phone','regex','website'],array_keys($picker['choices'])), 'actual edit page has compatible picker');
    check(count($picker['group_toggle'])>count($picker['choices']), 'native toggle also hides incompatible setting groups');
    setup('email'); $picker=$controller->edit(42)->getTemplateVariables()['sections'][0][4]['fields']['type'];
    check(!$picker['disabled'] && count($picker['choices'])===9, 'Email picker offers all approved text types');
    setup('file'); $picker=$controller->edit(42)->getTemplateVariables()['sections'][0][4]['fields']['type'];
    check($picker['disabled'] && array_keys($picker['choices'])===['file'], 'nonconvertible field picker remains disabled');
    // Exercise the actual controller, services, form JSON and transactional DB
    // writes together, including a second batch and failures after partial writes.
    foreach (['select', 'radio_group'] as $source) {
        foreach (['multiple_select', 'checkbox_group', 'dynamic_recipients'] as $target) {
            setup($source);
            $_POST['type'] = $target;
            $_POST['types'] = [$target => ['custom_values'=>'1', 'values'=>['0','002'], 'labels'=>['Zero','Two'], 'checked_by_default'=>['1','0']]];
            ee()->db->records['submissions'] = array_map(static fn($id) => ['id'=>$id, 'formId'=>1, 'field_42'=>$id % 2 ? '0' : '002', 'dateUpdated'=>'2020-01-01 00:00:00'], range(1,501));
            $controller->save(42);
            $rows = ee()->db->records['submissions'];
            check($rows[500]['field_42'] === ($target === 'dynamic_recipients' ? '[0]' : '["0"]'), "$source -> $target migrates every batch");
            check($rows[1]['field_42'] === ($target === 'dynamic_recipients' ? '[1]' : '["002"]') && $rows[0]['dateUpdated'] === '2020-01-01 00:00:00', 'selection content and submission timestamps survive');
            $instance=json_decode(ee()->db->records['form1'])->composer->properties->hash42;
            check($instance->values === ($target === 'dynamic_recipients' ? [0] : ['0']), 'per-form default selection has the new storage shape');
            check(ee()->db->records['field']['type'] === $target && end(ee()->db->log) === 'commit', 'approved choice conversion commits');
        }
    }
    setup('text'); $_POST['type']='email'; $_POST['types']=['email'=>['value'=>'a@example.com', 'placeholder'=>'Email']];
    ee()->db->records['submissions']=[['id'=>1,'formId'=>1,'field_42'=>'0'],['id'=>2,'formId'=>2,'field_42'=>'["literal"]']];
    $controller->save(42);
    check(ee()->db->records['submissions'][0]['field_42']==='["0"]' && json_decode(ee()->db->records['submissions'][1]['field_42'], true)===['["literal"]'], 'Text -> Email wraps literal historical values');
    check(ee()->db->records['field']['values']===['a@example.com'] && !isset(ee()->db->records['field']['value']), 'Email global defaults use an array');
    setup('email'); $_POST['type']='textarea'; $_POST['types']=['textarea'=>['value'=>"a@example.com\nb@example.com"]];
    ee()->db->records['submissions']=[['id'=>1,'formId'=>1,'field_42'=>'["a@example.com","b@example.com"]']];
    $controller->save(42);
    check(ee()->db->records['submissions'][0]['field_42']==="a@example.com\nb@example.com", 'Email -> Textarea retains every submitted address');
    foreach (['missing', 'write', 'read', 'malformed-email'] as $failure) {
        setup($failure === 'malformed-email' ? 'email' : 'select');
        $_POST['type']=$failure === 'malformed-email' ? 'text' : 'dynamic_recipients';
        $_POST['types']=[];
        ee()->db->records['submissions']=[['id'=>1,'formId'=>1,'field_42'=>'0'], ['id'=>2,'formId'=>2,'field_42'=>$failure === 'missing' ? 'removed' : '002']];
        if ($failure === 'write') ee()->db->failSubmission=2;
        if ($failure === 'read') ee()->db->failRead=true;
        $original=ee()->db->records;
        $controller->save(42);
        check(ee()->db->records===$original && end(ee()->db->log)==='rollback', "$failure rolls back field, form layouts, and previously converted rows");
    }
    setup('select'); $_POST['type']='dynamic_recipients'; $_POST['types']=[];
    // Different option order across forms must produce different stored indexes.
    $layout=json_decode($GLOBALS['forms'][1]->layoutJson);
    $layout->composer->properties->hash42->options=array_reverse($layout->composer->properties->hash42->options);
    $GLOBALS['forms'][1]->layoutJson=json_encode($layout);
    ee()->db->records['submissions']=[['id'=>1,'formId'=>1,'field_42'=>'0'], ['id'=>2,'formId'=>2,'field_42'=>'0']];
    $controller->save(42);
    check(ee()->db->records['submissions'][0]['field_42']==='[0]' && ee()->db->records['submissions'][1]['field_42']==='[1]', 'recipient conversion uses each form’s original option order');
    setup('select'); $_POST['type']='dynamic_recipients'; $_POST['types']=[];
    foreach ($GLOBALS['forms'] as $form) {
        $layout=json_decode($form->layoutJson);
        $layout->composer->properties->hash42->source='entries';
        $layout->composer->properties->hash42->target=7;
        $layout->composer->properties->hash42->configuration=(object)['labelField'=>'title'];
        $layout->composer->properties->hash42->options=[];
        $form->layoutJson=json_encode($layout);
    }
    $GLOBALS['fieldService']=new class extends \Solspace\Addons\FreeformNext\Services\FieldsService {
        public function getOptionsFromSource($source, $target, array $configuration=[], $selectedValues=[]) {
            return [new \Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option('Zero','0'), new \Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\DataContainers\Option('Two','002')];
        }
    };
    ee()->db->records['submissions']=[['id'=>1,'formId'=>1,'field_42'=>'002']];
    $controller->save(42);
    $instance=json_decode(ee()->db->records['form1'])->composer->properties->hash42;
    check(count($instance->options)===2 && !isset($instance->source,$instance->configuration) && $instance->notificationId===0, 'data-fed recipient options become a fixed list with notifications disabled');
    check(ee()->db->records['submissions'][0]['field_42']==='[1]', 'data-fed historical selection maps to its materialized option');
    unset($GLOBALS['fieldService']);
    $events=(new ReflectionClass(\Solspace\Addons\FreeformNext\Model\FieldModel::class))->getStaticPropertyValue('_events');
    check(in_array('afterInsert',$events,true) && !in_array('afterSave',$events,true), 'submission column hook runs only for new fields');
}
