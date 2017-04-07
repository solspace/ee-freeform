<?php
/**
 * Freeform Next for Expression Engine
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

$this->extend('_layouts/table_form_wrapper') ?>

<form>
    <?php $this->embed('ee:_shared/table', $table); ?>


    <fieldset class="tbl-bulk-act hidden">
        <select>
            <option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove">
                <?= lang('remove') ?>
            </option>
        </select>
        <input class="btn submit" data-conditional-modal="confirm-trigger" type="submit" value="<?= lang('submit') ?>">
    </fieldset>

</form>

<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/statuses.css"/>
