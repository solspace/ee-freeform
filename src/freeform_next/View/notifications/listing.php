<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

$this->extend('_layouts/table_form_wrapper')?>
<div class="panel">
	<div class="tbl-ctrls">
		<form>
			<?php $this->embed('ee:_shared/table', $table); ?>

			<?php if ($fileTableHasData) : ?>
				<h1>File Templates</h1>
				<?php $this->embed('ee:_shared/table', $fileTable); ?>
			<?php endif; ?>

			<fieldset class="bulk-action-bar hidden">
				<select class="select-popup button--small">
					<option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove"><?=lang('remove')?></option>
				</select>
				<input class="btn button--primary button--small submit" data-conditional-modal="confirm-trigger" type="submit" value="<?=lang('submit')?>">
			</fieldset>

		</form>
	</div>
</div>
