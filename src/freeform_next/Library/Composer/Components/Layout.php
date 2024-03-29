<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxGroupField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\FileUploadInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MailingListInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\NoRenderInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\RecipientInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\MailingListField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\TextField;
use Solspace\Addons\FreeformNext\Library\Exceptions\Composer\ComposerException;
use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Library\Factories\ComposerFieldFactory;
use Solspace\Addons\FreeformNext\Library\Session\FormValueContext;
use Solspace\Addons\FreeformNext\Library\Translations\TranslatorInterface;

class Layout implements \JsonSerializable, \Iterator
{
    /** @var Form */
    private $form;

    /** @var Page[] */
    private $pages;

    /** @var Row[] */
    private $rows;

    /** @var AbstractField[]|TextField[]|CheckboxGroupField[] */
    private $fields;

    /** @var AbstractField[] */
    private $fieldsById;

    /** @var AbstractField[] */
    private $fieldsByHandle;

    /** @var AbstractField[] */
    private $fieldsByHash;

    /** @var AbstractField[]|RecipientInterface[] */
    private $recipientFields;

    /** @var NoRenderInterface[]|AbstractField[] */
    private $hiddenFields;

    /** @var AbstractField[]|FileUploadInterface[] */
    private $fileUploadFields;

    /** @var AbstractField[]|MailingListInterface[] */
    private $mailingListFields;

    /** @var Properties */
    private $properties;

    /** @var array */
    private $layoutData;

    /** @var TranslatorInterface */
    private $translator;

    /** @var bool */
    private $hasDatepickerEnabledFields;

    /** @var bool */
    private $hasTableFields;

    /**
     * Layout constructor.
     *
     * @param Form                $form
     * @param array               $layoutData
     * @param Properties          $properties
     * @param FormValueContext    $formValueContext
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Form $form,
        array $layoutData,
        Properties $properties = null,
        FormValueContext $formValueContext,
        TranslatorInterface $translator
    ) {
        $this->form       = $form;
        $this->properties = $properties;
        $this->layoutData = $layoutData;
        $this->translator = $translator;
        $this->buildLayout($formValueContext);
    }

    /**
     * @return bool
     */
    public function hasDatepickerEnabledFields()
    {
        return $this->hasDatepickerEnabledFields;
    }

    /**
     * @return bool
     */
    public function hasTableFields()
    {
        return $this->hasTableFields;
    }

    /**
     * @return Page[]
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @return AbstractField[]|NoRenderInterface[]
     */
    public function getHiddenFields()
    {
        return $this->hiddenFields;
    }

    /**
     * @return AbstractField[]|FileUploadInterface[]
     */
    public function getFileUploadFields()
    {
        return $this->fileUploadFields;
    }

    /**
     * @return AbstractField[]|MailingListInterface[]
     */
    public function getMailingListFields()
    {
        return $this->mailingListFields;
    }

    /**
     * @return AbstractField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return AbstractField[]
     */
    public function getFieldsByHandle()
    {
        if (null === $this->fieldsByHandle) {
            $fields = [];
            foreach ($this->getFields() as $field) {
                if (!$field->getHandle()) {
                    continue;
                }

                $fields[$field->getHandle()] = $field;
            }

            $this->fieldsByHandle = $fields;
        }

        return $this->fieldsByHandle;
    }

    /**
     * @param int $id
     *
     * @return AbstractField
     * @throws FreeformException
     */
    public function getFieldById($id)
    {
        if (null === $this->fieldsById) {
            $fields = [];
            foreach ($this->getFields() as $field) {
                $fields[$field->getId()] = $field;
            }

            $this->fieldsById = $fields;
        }

        if (isset($this->fieldsById[$id])) {
            return $this->fieldsById[$id];
        }

        throw new FreeformException(
            $this->translate('Field with ID {id} not found', ['id' => $id])
        );
    }

    /**
     * Removes a given field form the layoutData if it's present
     * ** DOES NOT REMOVE IT FROM LAYOUT OBJECT **
     * !!This is meant only for cleaning up the export JSON data!!
     *
     * @param AbstractField $field
     */
    public function removeFieldFromData(AbstractField $field)
    {
        foreach ($this->layoutData as $pageIndex => $page) {
            foreach ($page as $rowIndex => $row) {
                foreach ($row['columns'] as $columnIndex => $column) {
                    if ($column === $field->getHash()) {
                        unset($this->layoutData[$pageIndex][$rowIndex]['columns'][$columnIndex]);
                        $this->layoutData[$pageIndex][$rowIndex]['columns'] = array_values(
                            $this->layoutData[$pageIndex][$rowIndex]['columns']
                        );
                        break;
                    }
                }
            }
        }

        foreach ($this->layoutData as $pageIndex => $page) {
            foreach ($page as $rowIndex => $row) {
                if (count($row['columns']) === 0) {
                    unset($this->layoutData[$pageIndex][$rowIndex]);
                    $this->layoutData[$pageIndex] = array_values($this->layoutData[$pageIndex]);
                }
            }
        }
    }

    /**
     * @param int $handle
     *
     * @return AbstractField
     * @throws FreeformException
     */
    public function getFieldByHandle($handle)
    {
        $fieldsByHandle = $this->getFieldsByHandle();

        if (isset($fieldsByHandle[$handle])) {
            return $fieldsByHandle[$handle];
        }

        throw new FreeformException(
            $this->translate("Field with handle '{handle}' not found", ['handle' => $handle])
        );
    }

    /**
     * @param string $hash
     *
     * @return mixed|AbstractField
     * @throws FreeformException
     */
    public function getFieldByHash($hash)
    {
        if (null === $this->fieldsByHash) {
            $fields = [];
            foreach ($this->getFields() as $field) {
                $fields[$field->getHash()] = $field;
            }

            $this->fieldsByHash = $fields;
        }

        if (isset($this->fieldsByHash[$hash])) {
            return $this->fieldsByHash[$hash];
        }

        throw new FreeformException(
            $this->translate("Field with hash '{hash}' not found", ['hash' => $hash])
        );
    }

    /**
     * @return AbstractField[]|RecipientInterface[]
     */
    public function getRecipientFields()
    {
        return $this->recipientFields;
    }

    /**
     * Builds all page, row and field objects and inflates them
     *
     * @param FormValueContext $formValueContext
     *
     * @throws ComposerException
     */
    private function buildLayout(FormValueContext $formValueContext)
    {
        $datetimeClass  = 'Solspace\Addons\FreeformNext\Library\Pro\Fields\DatetimeField';
        $datetimeExists = class_exists($datetimeClass);

        $tableClass  = 'Solspace\Addons\FreeformNext\Library\Pro\Fields\TableField';
        $tableExists = class_exists($tableClass);

        $hasDatepickerEnabledFields = $hasTableFields = false;
        $pageObjects                = [];
        $allRows                    = [];
        $allFields                  = [];
        $hiddenFields               = [];
        $recipientFields            = [];
        $fileUploadFields           = [];
        $mailingListFields          = [];

        foreach ($this->layoutData as $pageIndex => $rows) {
            if (!is_array($rows)) {
                throw new ComposerException(
                    $this->translate(
                        'Layout page {pageIndex} does not contain a row array',
                        ['pageIndex' => $pageIndex]
                    )
                );
            }

            $rowObjects = $pageFields = [];
            foreach ($rows as $rowIndex => $rowData) {
                if (!isset($rowData['id'])) {
                    throw new ComposerException(
                        $this->translate(
                            'Layout page {pageIndex} row {rowIndex} does not contain its ID',
                            ['pageIndex' => $pageIndex, 'rowIndex' => $rowIndex]
                        )
                    );
                }

                if (!isset($rowData['columns']) || !is_array($rowData['columns'])) {
                    throw new ComposerException(
                        $this->translate(
                            'Layout page {pageIndex} row {rowIndex} does not contain a list of columns',
                            ['pageIndex' => $pageIndex, 'rowIndex' => $rowIndex]
                        )
                    );
                }

                $columns = $rowData['columns'];

                $fields = [];
                foreach ($columns as $fieldHash) {
                    $fieldProperties = $this->properties->getFieldProperties($fieldHash);

                    $field = ComposerFieldFactory::createFromProperties(
                        $this->form,
                        $fieldProperties,
                        $formValueContext,
                        $pageIndex
                    );

                    if ($field instanceof NoRenderInterface || ($field instanceof MailingListField && $field->isHidden())) {
                        $hiddenFields[] = $field;
                    } else {
                        $fields[] = $field;
                    }

                    if ($field instanceof FileUploadInterface) {
                        $fileUploadFields[] = $field;
                    }

                    if ($field instanceof MailingListInterface) {
                        $mailingListFields[] = $field;
                    }

                    if ($field instanceof RecipientInterface && $field->shouldReceiveEmail()) {
                        $recipientFields[] = $field;
                    }

                    if ($datetimeExists && get_class($field) === $datetimeClass) {
                        if ($field->isUseDatepicker()) {
                            $hasDatepickerEnabledFields = true;
                        }
                    }

                    if ($tableExists && get_class($field) === $tableClass) {
                        if ($field->isUseScript()) {
                            $hasTableFields = true;
                        }
                    }

                    $pageFields[] = $field;
                    $allFields[]  = $field;
                }

                if (empty($fields)) {
                    continue;
                }

                $rowId = $rowData['id'];
                $row   = new Row($rowId, $fields);

                $rowObjects[] = $row;
                $allRows[]    = $row;
            }

            $pageProperties = $this->properties->getPageProperties($pageIndex);
            $page           = new Page($pageIndex, $pageProperties->getLabel(), $rowObjects, $pageFields);

            $pageObjects[] = $page;
        }

        $this->pages                      = $pageObjects;
        $this->rows                       = $allRows;
        $this->fields                     = $allFields;
        $this->hiddenFields               = $hiddenFields;
        $this->recipientFields            = $recipientFields;
        $this->fileUploadFields           = $fileUploadFields;
        $this->mailingListFields          = $mailingListFields;
        $this->hasDatepickerEnabledFields = $hasDatepickerEnabledFields;
        $this->hasTableFields             = $hasTableFields;
    }

    /**
     * @param string $string
     * @param array  $variables
     *
     * @return string
     */
    private function translate($string, array $variables = [])
    {
        return $this->translator->translate($string, $variables);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): array
	{
        return $this->layoutData;
    }

    /**
     * Return the current element
     *
     * @link  http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
	#[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->pages);
    }

    /**
     * Move forward to next element
     *
     * @link  http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
	#[\ReturnTypeWillChange]
    public function next()
    {
        next($this->pages);
    }

    /**
     * Return the key of the current element
     *
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
	#[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->pages);
    }

    /**
     * Checks if current position is valid
     *
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *        Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return null !== $this->key() && $this->key() !== false;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
	#[\ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->pages);
    }
}
