<?php /** @var \Solspace\Addons\FreeformNext\Model\ExportProfileModel $profile */ ?>
<table class="freeform-export-filters" id="filter-table">
    <thead>
    <tr>
        <th scope="col" class="filter-field">Field</th>
        <th scope="col" class="filter-type">Filter Type</th>
        <th scope="col" class="filter-value">Value</th>
        <th scope="col" aria-label="Actions"></th>
    </tr>
    </thead>
    <tbody>

    <?php $iterator = 0; ?>
    <?php if (!empty($profile->filters)) : ?>
        <?php foreach ($profile->filters as $filter) : ?>

            <tr data-iterator="<?php echo $iterator ?>">
                <td data-label="Field">
                    <select name="filters[<?php echo $iterator ?>][field]">
                            <?php foreach ($profile->getFieldSettings() as $fieldId => $fieldData) : ?>

                                <option value="<?php echo htmlspecialchars((string) $fieldId, ENT_QUOTES, 'UTF-8') ?>"
                                    <?php if ($fieldId == $filter['field']) : ?>
                                        selected
                                    <?php endif; ?>
                                >
                                    <?php echo htmlspecialchars((string) $fieldData['label'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>

                            <?php endforeach; ?>
                    </select>
                </td>
                <td data-label="Filter Type">
                    <select name="filters[<?php echo $iterator ?>][type]">
                        <option value="="<?php echo $filter['type'] === '=' ? ' selected' : '' ?>>Equal To</option>
                        <option value="!="<?php echo $filter['type'] === '!=' ? ' selected' : '' ?>>Not Equal To
                        </option>
                        <option value="like"<?php echo $filter['type'] === 'like' ? ' selected' : '' ?>>Like
                        </option>
                    </select>
                </td>
                <td data-label="Value">
                    <input type="text"
						   class="value"
                           name="filters[<?php echo $iterator ?>][value]"
                           value="<?php echo htmlspecialchars((string) $filter['value'], ENT_QUOTES, 'UTF-8') ?>"/>
                </td>
                <td class="filter-action">
                    <button type="button" class="freeform-filter-delete" aria-label="Delete filter row" title="Delete filter row">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 4h4m4 3-.8 13H6.8L6 7m4 4v6m4-6v6"/></svg>
                    </button>
                </td>
            </tr>

            <?php $iterator++; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <template>
        <tr data-iterator="__iterator__">
            <td data-label="Field">
                <select name="filters[__iterator__][field]">
                        <?php foreach ($profile->getFieldSettings() as $fieldId => $fieldData): ?>
                            <option value="<?php echo htmlspecialchars((string) $fieldId, ENT_QUOTES, 'UTF-8') ?>"><?php echo htmlspecialchars((string) $fieldData['label'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                </select>
            </td>
            <td data-label="Filter Type">
                <select name="filters[__iterator__][type]">
                    <option value="=">Equal To</option>
                    <option value="!=">Not Equal To</option>
                    <option value="like">Like</option>
                </select>
            </td>
            <td data-label="Value">
                <input type="text" class="value" name="filters[__iterator__][value]"/>
            </td>
            <td class="filter-action">
                <button type="button" class="freeform-filter-delete" aria-label="Delete filter row" title="Delete filter row">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 4h4m4 3-.8 13H6.8L6 7m4 4v6m4-6v6"/></svg>
                </button>
            </td>
        </tr>
    </template>
    </tbody>
</table>

<button type="button" class="button button--default freeform-filter-add" id="add-filter">Add A Row</button>
