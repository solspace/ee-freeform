<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

$this->extend('_layouts/table_form_wrapper');

/**
 * @var \Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionPreferenceSetting[] $layout
 */

?>

<form>
    <?php $this->embed('ee:_shared/table', $table); ?>


    <fieldset class="tbl-bulk-act hidden">
        <select>
            <option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove"><?= lang(
                    'remove'
                ) ?></option>
        </select>
        <input class="btn submit" data-conditional-modal="confirm-trigger" type="submit" value="<?= lang('submit') ?>">
    </fieldset>

</form>

<div class="choice-panel hidden">
    <h1><?php echo lang('Edit Layout') ?></h1>

    <ul class="very-sortable">
        <?php foreach ($layout as $setting) : ?>
            <li data-id="<?php echo $setting->getId() ?>"
                data-handle="<?php echo $setting->getHandle() ?>"
                data-label="<?php echo $setting->getLabel() ?>">
                <label>
                    <span class="handle"></span>
                    <input type="checkbox"<?php echo $setting->isChecked() ? ' checked' : '' ?> />
                    <?php echo $setting->getLabel() ?>
                </label>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="buttons">
        <button class="btn action" data-layout-save>Save</button>
        <button class="btn action" data-featherlight-close>Cancel</button>
    </div>
</div>

<div id="quick-export-modal" class="hidden">
    <form id="export-csv-modal" method="post" action="<?php echo $exportLink ?>">
        <input type="hidden" name="csrf_token" value="<?php echo CSRF_TOKEN ?>">

        <h1>Export data</h1>

        <div class="body">
            <div class="field">
                <div class="heading">
                    <label>Export as</label>
                </div>
                <div class="radio-list">
                    <label>
                        <input type="radio" name="export_type" value="csv" checked/>
                        CSV
                    </label>
                    <label>
                        <input type="radio" name="export_type" value="text"/>
                        Text
                    </label>
                    <label>
                        <input type="radio" name="export_type" value="json"/>
                        JSON
                    </label>
                    <label>
                        <input type="radio" name="export_type" value="xml"/>
                        XML
                    </label>
                </div>
            </div>

            <div class="field">
                <div class="heading">
                    <label>Form</label>
                </div>
                <div class="select">
                    <select class="select" name="form_id">
                        <?php foreach ($forms as $form): ?>
                            <option value="<?php echo $form->getId() ?>"<?php echo $form->getId(
                            ) == $selectedFormId ? ' selected' : '' ?>>
                                <?php echo $form->getName() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php foreach ($settings as $settingItem): ?>

                <?php
                $form   = $settingItem['form'];
                $fields = $settingItem['fields'];
                ?>

                <div class="form-field-list field<?php echo $form->getId() != $selectedFormId ? ' hidden' : '' ?>"
                     data-id="<?php echo $form->getId() ?>">
                    <div class="heading">
                        <label>Fields for <?php echo $form->getName() ?></label>
                    </div>
                    <ul class="checkbox-select">
                        <?php foreach ($fields as $fieldId => $fieldSetting): ?>
                            <?php
                            $label     = $fieldSetting['label'];
                            $isChecked = $fieldSetting['checked'];
                            ?>

                            <li>
                                <div class="icon move"></div>

                                <input type="hidden"
                                       name="export_fields[<?php echo $form->getId() ?>][<?php echo $fieldId ?>][label]"
                                       value="<?php echo $label ?>"
                                />

                                <input type="hidden"
                                       name="export_fields[<?php echo $form->getId() ?>][<?php echo $fieldId ?>][checked]"
                                       value="0"
                                />
                                <input type="checkbox"
                                       class="checkbox"
                                       name="export_fields[<?php echo $form->getId() ?>][<?php echo $fieldId ?>][checked]"
                                       value="1"
                                       <?php echo $isChecked ? 'checked' : '' ?>
                                       id="<?php echo $form->getId() . '-' . $fieldId ?>"
                                />
                                <label for="<?php echo $form->getId() . '-' . $fieldId ?>"><?php echo $label ?></label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            <?php endforeach; ?>

        </div>
        <div class="footer">
            <div class="buttons right last">
                <input type="button" class="btn cancel" value="Cancel"/>
                <input type="submit" class="btn submit" value="Export"/>

                <div class="spinner" style="display: none;"></div>
            </div>
        </div>
    </form>
</div>

<script>
  var layoutEditorSaveUrl = '<?php echo ee('CP/URL')->make('addons/settings/freeform_next/api/submission_layout') ?>';
  var layoutEditorFormId  = <?php echo (int) $form->getId() ?>;
</script>

<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/submissions.css"/>
<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/lib/featherlight/featherlight.min.css"/>
