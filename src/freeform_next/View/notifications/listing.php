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
<div class="form-standard">

<form>
	<div class="panel-body">
		<h2>Database Notification Templates</h2>
	</div>

	<?php $this->embed('ee:_shared/table', $table); ?>

	<fieldset class="bulk-action-bar hidden">
		<select class="">
			<option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove"><?=lang('remove')?></option>
		</select>
		<input class="btn button--primary submit" data-conditional-modal="confirm-trigger" type="submit" value="<?=lang('submit')?>">
	</fieldset>

	<?php if ($fileTableHasData) : ?>
		<div class="panel-body">
			<h2>File Notification Templates</h2>
		</div>
		<?php $this->embed('ee:_shared/table', $fileTable); ?>
	<?php endif; ?>



</form>
</div>
