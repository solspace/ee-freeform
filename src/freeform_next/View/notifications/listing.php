<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

$this->extend('_layouts/table_form_wrapper')?>

<form>
    <?php $this->embed('ee:_shared/table', $table); ?>

    <?php if ($fileTableHasData) : ?>
        <h1>File Templates</h1>
        <?php $this->embed('ee:_shared/table', $fileTable); ?>
    <?php endif; ?>

    <fieldset class="tbl-bulk-act hidden">
        <select>
            <option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove"><?=lang('remove')?></option>
        </select>
        <input class="btn submit" data-conditional-modal="confirm-trigger" type="submit" value="<?=lang('submit')?>">
    </fieldset>

</form>
