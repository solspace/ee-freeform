<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use Exception;
use EllisLab\ExpressionEngine\Library\CP\Table;
use GuzzleHttp\Exception\BadResponseException;
use Solspace\Addons\FreeformNext\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Addons\FreeformNext\Library\Helpers\ExtensionHelper;
use Solspace\Addons\FreeformNext\Library\Integrations\MailingLists\MailingListOAuthConnector;
use Solspace\Addons\FreeformNext\Library\Integrations\SettingBlueprint;
use Solspace\Addons\FreeformNext\Library\Integrations\TokenRefreshInterface;
use Solspace\Addons\FreeformNext\Model\IntegrationModel;
use Solspace\Addons\FreeformNext\Repositories\MailingListRepository;
use Solspace\Addons\FreeformNext\Services\MailingListsService;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Extras\ConfirmRemoveModal;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;

class MailingListsController extends Controller
{
    /**
     * @param int|null $id
     *
     * @return View
     */
    public function handle(null|string|int $id = null): CpView|AjaxView|RedirectView
    {
        if (null === $id) {
            return $this->index();
        }

        if ($id === "check") {
            return $this->check();
        }

        if ($id === "get") {
            return $this->getIntegrationsAjax();
        }

        if ($id === "delete") {
            return $this->batchDelete();
        }

        if ($id === "authorize") {
            return $this->authorize();
        }

        return $this->edit($id);
    }

    /**
     * @return CpView
     */
    public function index(): CpView
    {
        /** @var Table $table */
        $table = ee("CP/Table", ["sortable" => false, "searchable" => false]);

        $table->setColumns(
            [
                "id"               => ["type" => Table::COL_ID],
                "Name"             => ["type" => Table::COL_TEXT],
                "Handle"           => ["type" => Table::COL_TEXT],
                "Service Provider" => ["type" => Table::COL_TEXT],
                ["type" => Table::COL_CHECKBOX, "name" => "selection"],
            ]
        );

        $integrations = MailingListRepository::getInstance()->getAllIntegrations();

        $tableData = [];
        foreach ($integrations as $integration) {
            $tableData[] = [
                $integration->id,
                [
                    "content" => $integration->name,
                    "href"    => $this->getLink("integrations/mailing_lists/" . $integration->id),
                ],
                $integration->handle,
                $integration->getIntegrationObject()->getServiceProvider(),
                [
                    "name"  => "id_list[]",
                    "value" => $integration->id,
                    "data"  => [
                        "confirm" => lang("Integration") . ": <b>" . htmlentities(
                                $integration->name,
                                ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'
                            ) . "</b>",
                    ],
                ],
            ];
        }
        $table->setData($tableData);
        $table->setNoResultsText("No results");

        $removeModal = new ConfirmRemoveModal($this->getLink("integrations/mailing_lists/delete"));
        $removeModal->setKind("Mailing List Integrations");

        $serviceProviderTypes = $this->getMailingListService()->getAllMailingListServiceProviders();

        if (count($serviceProviderTypes)) {
            $formRightLinks = [
                [
                    "title" => lang("New Integration"),
                    "link"  => $this->getLink("integrations/mailing_lists/new"),
                ],
            ];
        } else {
            $formRightLinks = [
                [
                    "title" => lang("Upgrade to Pro to Enable"),
                    "link"  => "https://docs.solspace.com/expressionengine/freeform/v3/",
                ],
            ];
        }

        $view = new CpView(
            "integrations/table",
            [
                "table"            => $table->viewData(),
                "cp_page_title"    => lang("Mailing List Integrations"),
                "form_right_links" => $formRightLinks,
            ]
        );
        $view->setHeading(lang("Mailing List Integrations"));
        $view->addModal($removeModal);

        return $view;
    }

    /**
     * @param int|string $id
     *
     * @return View
     * @throws IntegrationException
     */
    public function edit($id): RedirectView|CpView
    {
        $serviceProviderTypes = $this->getMailingListService()->getAllMailingListServiceProviders();

        if (empty($serviceProviderTypes)) {
            return new RedirectView("https://docs.solspace.com/expressionengine/freeform/v3/");
        }

        if ($id === "new") {
            $model        = IntegrationModel::create(IntegrationModel::TYPE_MAILING_LIST);
            $model->class = array_keys($serviceProviderTypes)[0];
        } else {
            $model = MailingListRepository::getInstance()->getIntegrationById($id);
        }

        if (!$model) {
            throw new IntegrationException("Integration does not exist");
        }

        $errors = null;
        if (isset($_POST["class"])) {
            $errors = $this->save($model);

            if (empty($errors)) {
                $view = new RedirectView($this->getLink("integrations/mailing_lists/"));

                return $view;
            }
        }

        if (ee()->input->get("code")) {
            $this->handleAuthorization($model);
        }

        $integration = $model->getIntegrationObject();
        $settings    = $integration->getSettings();

        $blueprints = $this->getMailingListService()->getAllMailingListSettingBlueprints();

        $types = $targets = $settingGroups = [];
        foreach ($serviceProviderTypes as $className => $name) {
            $hash                = md5($className);
            $types[$className]   = $name;
            $targets[$className] = $hash;
        }

        foreach ($blueprints as $className => $rows) {
            /** @var SettingBlueprint $item */
            foreach ($rows as $item) {
                if (!$item->isEditable()) {
                    continue;
                }

                $hash = md5($className);

                $settingGroups[] = [
                    "title"  => $item->getLabel(),
                    "desc"   => $item->getInstructions(),
                    "group"  => $hash,
                    "fields" => [
                        $hash . "-" . $item->getHandle() => [
                            "type"     => $item->getType() === SettingBlueprint::TYPE_BOOL ? "yes_no" : "text",
                            "required" => $item->isRequired(),
                            "value"    => $settings[$item->getHandle()] ?? $item->getValue(),
                            "attrs"    => $item->getAttributes(),
                        ],
                    ],
                ];
            }
        }

        $sectionData = [
            [
                [
                    "title"  => lang("Service Provider"),
                    "fields" => [
                        "class" => [
                            "type"         => "select",
                            "value"        => $model->class,
                            "choices"      => $types,
                            "group_toggle" => $targets,
                        ],
                    ],
                ],
                [
                    "title"  => "Name",
                    "desc"   => "What this integration will be called in the CP.",
                    "fields" => [
                        "name" => [
                            "type"  => "text",
                            "value" => $model->name,
                            "attrs" => "data-generator-base",
                        ],
                    ],
                ],
                [
                    "title"  => "Handle",
                    "desc"   => "The unique name used to identify this integration.",
                    "fields" => [
                        "handle" => [
                            "type"  => "text",
                            "value" => $model->handle,
                            "attrs" => "data-generator-target",
                        ],
                    ],
                ],
            ],
            "Settings" => $settingGroups,
        ];

        if ($model->id) {
            $link = $this->getLink("integrations/mailing_lists/check");
            if (empty($integration->getAccessToken())) {
                $html = '
                    Not authorized yet.
                    <button style="color:var(--ee-link);padding:0;margin:0;background:transparent;border:none;cursor:pointer;text-decoration:none;line-height:1.6" type="submit" data-shortcut="s" value="Save" data-submit-text="Save" data-work-text="Saving">Click here to authorize</button>
                ';
            } else {
                $html = '
                    Not able to authorize.
                    <a href="' . $link . '" class="">Click here to re-authorize</a>
                    <div class="errors"></div>
                ';
            }

            $sectionData[0][] = [
                "title"  => "Is Authorized?",
                "desc"   => "Is the connection authorized?",
                "fields" => [
                    "handle" => [
                        "type"    => "html",
                        "content" => '
                            <div id="auth-checker" data-url-stub="' . $link . '">
                                <div class="authorized" style="display: none;">
                                    Authorized
                                </div>
                                <div class="not-authorized" style="display: none;">
                                    ' . $html  .'
                                </div>
                                <div class="pending-status-check" 
                                     data-id="' . $model->id . '" 
                                     data-type="mailing_lists">
                                    Checking credentials...
                                </div>
                            </div>
                        ',
                    ],
                ],
            ];
        }

        ee()->cp->add_js_script(
            [
                "file" => ["cp/form_group"],
            ]
        );

        $view = new CpView(
            "integrations/edit",
            [
                "cp_page_title"         => "Mailing List Integration",
                "base_url"              => $this->getLink("integrations/mailing_lists/" . $id),
                "sections"              => $sectionData,
                "save_btn_text"         => "Save",
                "save_btn_text_working" => "Saving",
                "errors"                => $errors,
            ]
        );

        $view
            ->setHeading($model->name ?: "New Mailing List Integration")
            ->addBreadcrumb(new NavigationLink("Mailing List Integrations", "integrations/mailing_lists"))
            ->addJavascript("integrations")
            ->addJavascript("handleGenerator");

        return $view;
    }

    /**
     * @return bool
     */
    public function save(IntegrationModel $model)
    {
        $isNew = !$model->id;

        $class  = ee()->input->post("class");
        $hash   = md5($class);
        $name   = ee()->input->post("name");
        $handle = ee()->input->post("handle");

        $rules = [
            "class"  => "required",
            "name"   => "required",
            "handle" => "required",
        ];

        $postedSettings = [];
        foreach ($_POST as $key => $value) {
            if (str_starts_with($key, $hash)) {
                $postedSettings[str_replace($hash . "-", "", $key)] = $value;
            }
        }

        $settings = $model->settings ? json_decode($model->settings, true) : [];

        $blueprints = $this->getMailingListService()->getMailingListSettingBlueprints($class);

        foreach ($blueprints as $blueprint) {
            if (!$blueprint->isEditable()) {
                continue;
            }

            $blueprintHandle = $blueprint->getHandle();
            $value           = $postedSettings[$blueprintHandle];

            $settings[$blueprintHandle] = $value;
            if ($blueprint->isRequired()) {
                $rules["{$hash}-{$blueprint->getHandle()}"] = "required";
            }
        }

        $validation = ee("Validation")->make($rules)->validate($_POST);
        if (!$validation->isValid()) {
            return $validation;
        }

        $model->updateSettings($settings);
        $model->name        = $name;
        $model->handle      = $handle;
        $model->class       = $class;
        $model->forceUpdate = true;

        $integration = $model->getIntegrationObject();
        $integration->onBeforeSave($model);

        ExtensionHelper::call(ExtensionHelper::HOOK_MAILING_LISTS_BEFORE_SAVE, $model, $isNew);

        if (!$isNew) {
            $integration->initiateAuthentication();
        }

        $model->save();

        ee("CP/Alert")
            ->makeInline("shared-form")
            ->asSuccess()
            ->withTitle(lang("Success"))
            ->defer();

        ExtensionHelper::call(ExtensionHelper::HOOK_MAILING_LISTS_AFTER_SAVE, $model, $isNew);

        header("Location: " . $this->getLink("integrations/mailing_lists/" . $model->id));
        die();
    }

    /**
     * @return AjaxView
     */
    public function getIntegrationsAjax(): AjaxView
    {
        $integrations = MailingListRepository::getInstance()->getAllIntegrationObjects();

        foreach ($integrations as $integration) {
            $integration->setForceUpdate(true);
        }

        $ajaxView = new AjaxView();
        $ajaxView->setVariables($integrations);

        return $ajaxView;
    }

    /**
     * @return RedirectView
     */
    public function batchDelete(): RedirectView
    {
        if (isset($_POST["id_list"])) {
            $ids = [];
            foreach ($_POST["id_list"] as $id) {
                $ids[] = (int) $id;
            }

            $models = MailingListRepository::getInstance()->getIntegrationsByIdList($ids);

            foreach ($models as $model) {
                if (!ExtensionHelper::call(ExtensionHelper::HOOK_MAILING_LISTS_BEFORE_DELETE, $model)) {
                    continue;
                }

                $model->delete();

                ExtensionHelper::call(ExtensionHelper::HOOK_MAILING_LISTS_AFTER_DELETE, $model);
            }
        }

        return new RedirectView($this->getLink("integrations/mailing_lists/"));
    }

    /**
     * Handle OAuth2 authorization
     *
     * @return void|RedirectView
     * @throws IntegrationException
     */
    public function authorize(): RedirectView
    {
        $code = ee()->input->get("code");
        if (empty($code)) {
            throw new IntegrationException("Missing OAuth2 Authorization Code.");
        }

        $state = ee()->input->get("state");
        if (empty($state)) {
            throw new IntegrationException("Missing OAuth2 Authorization State. Possible CSRF attack.");
        }

        $model = MailingListRepository::getInstance()->getIntegrationById($state);
        if (!$model) {
            throw new IntegrationException("Integration not found");
        }

        $this->handleAuthorization($model);

        return new RedirectView($this->getLink("integrations/mailing_lists/" . $model->getIntegrationObject()->getId()));
    }

    /**
     * Handle OAuth2 authorization
     */
    private function handleAuthorization(IntegrationModel $model): void
    {
        $integration = $model->getIntegrationObject();
        $code        = ee()->input->get("code");

        if (!$integration instanceof MailingListOAuthConnector || empty($code)) {
            return;
        }

        $accessToken = $integration->fetchAccessToken();
        $model->accessToken = $accessToken;
        $model->settings    = $integration->getSettings();
        $model->settings    = json_encode($model->settings);

        if (!ExtensionHelper::call(ExtensionHelper::HOOK_MAILING_LISTS_BEFORE_SAVE, $model)) {
            return;
        }

        $model->save();

        ExtensionHelper::call(ExtensionHelper::HOOK_MAILING_LISTS_AFTER_SAVE, $model);
    }

    /**
     * @return MailingListsService
     */
    private function getMailingListService()
    {
        static $instance;

        if (null === $instance) {
            $instance = new MailingListsService();
        }

        return $instance;
    }

    /**
     * @return AjaxView
     */
    private function check(): AjaxView
    {
        $view = new AjaxView();

        $id = ee()->input->post("id");
        $model = MailingListRepository::getInstance()->getIntegrationById($id);
        $integration = $model->getIntegrationObject();

        if (!$model) {
            $view->addVariable("success", false);
            $view->addError("Integration does not exist");
        }

        try {
            if ($integration->checkConnection()) {
                $view->addVariable("success", true);
            } else {
                $view->addVariable("success", false);
                $view->addError("Could not connect");
            }
        } catch (BadResponseException $e) {
            if ($integration instanceof TokenRefreshInterface) {
                try {
                    if ($integration->refreshToken() && $integration->isAccessTokenUpdated()) {
                        $mailingListService = new MailingListsService();
                        $mailingListService->updateAccessToken($integration);

                        $view->addVariable("success", true);
                    } else {
                        $view->addVariable("success", false);
                        $view->addError($e->getResponse()->getBody(true));
                    }
                } catch (Exception $e) {
                    $view->addVariable("success", false);
                    $view->addError($e->getMessage());
                }
            } else {
                $view->addVariable("success", false);
                $view->addError($e->getMessage());
            }

            $view->addVariable("success", false);
            $view->addError($e->getResponse()->getBody(true));
        }

        return $view;
    }
}
