<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

$this->extend('_layouts/table_form_wrapper');

/**
 * @var \Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionPreferenceSetting[] $layout
 */

?>

<script>
  var layoutEditorSaveUrl = '<?php echo ee('CP/URL')->make('addons/settings/freeform_next/api/submission_layout') ?>';
  var layoutEditorFormId  = <?php echo (int) $form->getId() ?>;
</script>


<?php if ($sessionToken): ?>
<input type="hidden" name="S" value="<?= $sessionToken ?>">
<?php endif; ?>
<div class="panel-heading freeform-submissions-filters">
	<div class="filter-bar filter-bar--collapsible" id="custom-filters">
        <div class="filter-bar__item">
            <button type="button" class="has-sub filter-bar__button js-dropdown-toggle button button--default button--small">
                <?= lang('form') ?>
                <span class="faded">
                    (<?= htmlspecialchars($currentFormLabel, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8') ?>)
                </span>
            </button>
            <div class="dropdown">
                <?php foreach ($formSwitches as $handle => $item): ?>
                    <a href="<?= $item['url'] ?>" class="dropdown__link" data-prevent-trigger="1">
                        <?= htmlspecialchars($item['label'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

		<div class="filter-bar__item">
			<button type="button" class="has-sub filter-bar__button js-dropdown-toggle button button--default button--small" data-filter-label="status">
				<?= lang('status') ?>
				<span class="faded">
				<?= $currentSearchStatus ? '(' . lang($currentSearchStatus) . ')' : '' ?>
			</span>
			</button>
			<div class="dropdown">
				<div data-target="search_status">
					<?php foreach ($formStatuses as $status_id => $status): ?>
						<a href="<?=$status['url']?>" class="dropdown__link" data-value="<?= $status_id ?>">
							<?= $status['label'] ?>
						</a>
					<?php endforeach; ?>
				</div>
				<input type="hidden" data-filter="status" name="status" value="<?=$currentSearchStatusId?>">
			</div>
		</div>

		<div class="filter-bar__item">
			<button type="button" class="has-sub filter-bar__button js-dropdown-toggle button button--default button--small" data-filter-label="date">
				<?= lang('entry_date') ?>
				<span class="faded">
				<?= $currentDateRange ? '(' . lang($currentDateRange) . ')' : '' ?>
			</span>
			</button>
			<div class="dropdown">
				<?php foreach ($formDateRanges as $date_key => $date_range): ?>
				<a href="<?=$date_range['url']?>" class="dropdown__link" data-value="today"><?=$date_range['label']?></a>
				<?php endforeach ?>
				<a class="dropdown__link" data-target="date_range" data-prevent-trigger="1"><?= lang('choose_date_range') ?></a>
				<input type="hidden" data-filter="date_range" name="date_range" value="<?=$currentDateRange?>">
			</div>
		</div>

		<div class="filter-bar__item date-range-inputs" <?php if($currentDateRange != 'date_range') : ?>style="display: none;"<?php endif ?>>
			<input type="text"
				   name="date_range_start"
				   class="datepicker input--small"
				   rel="date-picker"
				   data-timestamp="<?= $currentDateRangeStart ? strtotime($currentDateRangeStart) : time() ?>"
				   value="<?= $currentDateRangeStart && $currentDateRange == 'date_range' ? $currentDateRangeStart : '' ?>"
				   placeholder="<?= lang('start_date') ?>"
				   style="width: 70px;"
			/>
		</div>
		<div class="filter-bar__item date-range-inputs" <?php if($currentDateRange != 'date_range') : ?>style="display: none;"<?php endif ?>>
			<input type="text"
				   name="date_range_end"
				   class="datepicker input--small"
				   rel="date-picker"
				   data-timestamp="<?= $currentDateRangeStart ? strtotime($currentDateRangeStart) : time() ?>"
				   value="<?= $currentDateRangeEnd && $currentDateRange == 'date_range' ? $currentDateRangeEnd : '' ?>"
				   placeholder="<?= lang('end_date') ?>"
				   style="width: 70px;"
			/>
		</div>


		<div class="filter-bar__item">
			<input type="text"
				   name="keywords"
				   placeholder="<?= lang('keywords') ?>"
				   style="width: 90px;"
				   value="<?= $currentKeyword ?>"
				   class="search-input__input input--small"
			/>
		</div>

		<div class="filter-bar__item">
			<button type="button" class="has-sub filter-bar__button js-dropdown-toggle button button--default button--small" data-filter-label="search_on_field">
				<?= lang('field') ?>
				<span class="faded">
				(<?= $currentSearchOnField ? $columnLabels[$currentSearchOnField] : lang('all_fields') ?>)
				</span>
			</button>
			<div class="dropdown">
				<div data-target="search_on_field">
					<?php foreach ($visibleColumns as $column_name): ?>
						<a class="dropdown__link" data-target="search_on_field" data-value="<?= $column_name ?>" data-prevent-trigger="1"><?= $columnLabels[$column_name] ?></a>
					<?php endforeach; ?>
				</div>
				<input type="hidden" data-filter="search_on_field" name="search_on_field" value="<?=$currentSearchOnField?>">
			</div>
		</div>

		<div class="filter-bar__item">
			<a href="<?= $mainUrl ?>" class="filter-bar__button filter-bar__button--clear button button--default button--small"><i class="fas fa-minus-circle fa-sm"></i> <?= lang('clear_filters') ?></a>
		</div>
		<div class="freeform-list-controls">
			<div class="filter-bar__item freeform-columns-menu">
				<button type="button" id="change-layout-trigger" class="has-sub filter-bar__button js-dropdown-toggle button button--default button--small" aria-label="Columns" title="Columns">
					<svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" focusable="false"><rect x="2.5" y="3" width="15" height="14" rx="1.5"/><path d="M2.5 7.5h15M10 7.5V17"/></svg>
				</button>
				<div class="dropdown freeform-columns-dropdown">
					<div class="freeform-columns-heading">Columns</div>
					<ul class="very-sortable">
						<?php foreach ($layout as $setting): ?>
							<li data-id="<?= htmlspecialchars((string) $setting->getId(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" data-handle="<?= htmlspecialchars((string) $setting->getHandle(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" data-label="<?= htmlspecialchars((string) $setting->getLabel(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
								<label><input type="checkbox" <?= $setting->isChecked() ? 'checked' : '' ?>> <?= htmlspecialchars((string) $setting->getLabel(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></label>
							</li>
						<?php endforeach; ?>
					</ul>
					<div class="freeform-columns-actions"><button type="button" class="button button--primary button--small" data-layout-save><?= lang('save') ?></button></div>
				</div>
			</div>
			<div class="filter-bar__item freeform-per-page-menu">
				<button type="button" class="has-sub filter-bar__button js-dropdown-toggle button button--default button--small">Show <span class="faded">(<?= (int) $perpage ?>)</span></button>
				<div class="dropdown">
					<?php foreach ([25, 50, 100] as $count): ?>
						<a class="dropdown__link" href="<?= ee('CP/URL')->getCurrentUrl()->addQueryStringVariables(['perpage' => $count, 'page' => 1])->compile() ?>"><?= $count ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="freeform-submissions-table-scroll" role="region" aria-label="<?= htmlspecialchars(lang('spam'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" tabindex="0">
    <?php $this->embed('ee:_shared/table', $table); ?>
</div>


<?php $this->startBlock('addonModals') ?>

	<div id="quick-export-modal" class="hidden">
		<form id="export-csv-modal" method="post" action="<?php echo $exportLink ?>">
			<input type="hidden" name="csrf_token" value="<?php echo CSRF_TOKEN ?>">

			<h1 class="dialog__header">Export data</h1>

			<div class="dialog__body">
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
			<div class="buttons dialog__actions">
					<input type="button" class="btn button button--default button--small cancel" value="Cancel"/>
					<input type="submit" class="btn button button--primary button--small submit" value="Export"/>
					<div class="spinner" style="display: none;"></div>
			</div>
		</form>
	</div>

<?php $this->endBlock() ?>

<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/submissions.css"/>
<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/lib/featherlight/featherlight.min.css"/>
