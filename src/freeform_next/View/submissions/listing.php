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

$this->extend('_layouts/table_form_wrapper');

/**
 * @var \Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionPreferenceSetting[] $layout
 */

?>

<script>
  var layoutEditorSaveUrl = '<?php echo ee('CP/URL')->make('addons/settings/freeform_next/api/submission_layout') ?>';
  var layoutEditorFormId  = <?php echo (int) $form->getId() ?>;
</script>


<form method="get" action="<?=ee('CP/URL')->getCurrentUrl()?>" id="entry-filters" data-action="<?=isset($entries_filter_uri) ? $entries_filter_uri : ""?>">
    <?php if ($sessionToken): ?>
    <input type="hidden" name="S" value="<?= $sessionToken ?>">
    <?php endif; ?>
	<div class="panel-body">
		<div class="filter-bar filter-bar--collapsible" id="custom-filters">
			<div class="filter-bar__item">
				<button class="has-sub filter-bar__button js-dropdown-toggle button button--default button--small" data-filter-label="status">
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
				</div>
			</div>

			<div class="filter-bar__item">
				<button class="has-sub filter-bar__button js-dropdown-toggle button button--default button--small" data-filter-label="date">
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
				</div>
			</div>

			<div class="filter-bar__item date-range-inputs" style="display: none;">
				<input type="text"
					   name="date_range_start"
					   class="datepicker input--small"
					   rel="date-picker"
					   data-timestamp="<?= $currentDateRangeStart ? strtotime($currentDateRangeStart) : time() ?>"
					   value="<?= $currentDateRangeStart ?>"
					   placeholder="<?= lang('start_date') ?>"
					   style="width: 70px;"
				/>
			</div>
			<div class="filter-bar__item date-range-inputs" style="display: none;">
				<input type="text"
					   name="date_range_end"
					   class="datepicker input--small"
					   rel="date-picker"
					   data-timestamp="<?= $currentDateRangeStart ? strtotime($currentDateRangeStart) : time() ?>"
					   value="<?= $currentDateRangeEnd ?>"
					   placeholder="<?= lang('end_date') ?>"
					   style="width: 70px;"
				/>
			</div>


			<div class="filter-bar__item date-range-inputs">
				<input type="text"
					   name="keywords"
					   placeholder="<?= lang('keywords') ?>"
					   style="width: 90px;"
					   value="<?= $currentKeyword ?>"
					   class="search-input__input input--small"
				/>
			</div>
		</div>
	</div>

    <div class="filters panel-body">
        <b><?= lang('filters') ?>:</b>

        <?php // echo $filters->render($baseUrl); ?>

        <ul>
            <li>
                <input type="hidden" name="search_date_range" value="<?= $currentDateRange ?>">
                <a href="" class="has-sub button--small" data-filter-label="date">
                    <?= lang('entry_date') ?>
                    <span class="faded">
                    <?= $currentDateRange ? '(' . lang($currentDateRange) . ')' : '' ?>
                </span>
                </a>
                <div class="sub-menu">
                    <ul data-target="search_date_range">
                        <li><a data-value="today"><?= lang('today') ?></a></li>
                        <li><a data-value="this_week"><?= lang('this_week') ?></a></li>
                        <li><a data-value="this_month"><?= lang('this_month') ?></a></li>
                        <li><a data-value="last_month"><?= lang('last_month') ?></a></li>
                        <li><a data-value="this_year"><?= lang('this_year') ?></a></li>
                        <li><a data-value="date_range" data-prevent-trigger="1"><?= lang('choose_date_range') ?></a></li>
                    </ul>
                </div>
            </li>

            <li id="date-range-inputs" style="display: none">

                <input type="text"
                       name="search_date_range_start"
                       class="datepicker"
                       rel="date-picker"
                       data-timestamp="<?= $currentDateRangeStart ? strtotime($currentDateRangeStart) : time() ?>"
                       value="<?= $currentDateRangeStart ?>"
                       placeholder="<?= lang('start_date') ?>"
                       style="width: 70px;"
                />
                <input type="text"
                       name="search_date_range_end"
                       class="datepicker"
                       rel="date-picker"
                       data-timestamp="<?= $currentDateRangeStart ? strtotime($currentDateRangeStart) : time() ?>"
                       value="<?= $currentDateRangeEnd ?>"
                       placeholder="<?= lang('end_date') ?>"
                       style="width: 70px;"
                />
            </li>

            <li>
                <input type="text"
                       name="search_keywords"
                       placeholder="<?= lang('keywords') ?>"
                       style="width: 90px;"
                       value="<?= $currentKeyword ?>"
					   class="search-input__input input--small"
                />
            </li>
            <li>
                <input type="hidden" name="search_on_field" value="<?= $currentSearchOnField ?>">
                <a href="" class="has-sub button--small" data-filter-label="date">
                    <?= lang('field') ?>
                    <span class="faded">
                    (<?= $currentSearchOnField ? $columnLabels[$currentSearchOnField] : lang('all_fields') ?>)
                </span>
                </a>
                <div class="sub-menu">
                    <ul data-target="search_on_field">
                        <li>
                            <a data-value=""><?= lang('all_fields') ?></a>
                        </li>
                        <?php foreach ($visibleColumns as $column_name): ?>
                            <li>
                                <a data-value="<?= $column_name ?>">
                                    <?= $columnLabels[$column_name] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </li>

            <li class="filter-clear">
                <a href="<?= $mainUrl ?>"><?= lang('clear_filters') ?></a>
            </li>
        </ul>
    </div>

	<?php $this->embed('ee:_shared/table', $table); ?>

	<fieldset class="bulk-action-bar hidden">
		<select class="">
			<option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove"><?= lang(
					'remove'
				) ?></option>
		</select>
		<input class="btn button--primary submit" data-conditional-modal="confirm-trigger" type="submit" value="<?= lang('submit') ?>">
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

<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/submissions.css"/>
<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/lib/featherlight/featherlight.min.css"/>
