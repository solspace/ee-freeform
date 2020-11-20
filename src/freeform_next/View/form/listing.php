<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

$this->extend('_layouts/table_form_wrapper')?>

<form>
	<?php $this->embed('ee:_shared/table', $table); ?>

	<fieldset class="bulk-action-bar hidden">
		<select class="">
			<option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove"><?=lang('remove')?></option>
		</select>
		<input class="btn button--primary submit" data-conditional-modal="confirm-trigger" type="submit" value="<?=lang('submit')?>">
	</fieldset>

</form>

<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/statuses.css"/>
