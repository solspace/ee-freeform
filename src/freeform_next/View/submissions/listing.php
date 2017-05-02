<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
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

<script>
    var layoutEditorSaveUrl = '<?php echo ee('CP/URL')->make('addons/settings/freeform_next/api/submission_layout') ?>';
    var layoutEditorFormId  = <?php echo (int) $form->getId() ?>;
</script>

<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/submissions.css"/>
<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/lib/featherlight/featherlight.min.css"/>
