<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2021, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

$this->extend('_layouts/table_form_wrapper')?>
<div class="form-standard">
	<div class="panel-body">
		<h2>Database Notification Templates</h2>
	</div>
</div>

<?php $this->embed('ee:_shared/table', $table); ?>

<?php if ($fileTableHasData) : ?>
	<div class="form-standard">
		<div class="panel-body">
			<h2>File Notification Templates</h2>
		</div>
	</div>
	<?php $this->embed('ee:_shared/table', $fileTable); ?>
<?php endif; ?>


