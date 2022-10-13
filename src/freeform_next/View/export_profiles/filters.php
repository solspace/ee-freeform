<?php /** @var \Solspace\Addons\FreeformNext\Model\ExportProfileModel $profile */ ?>
<table class="shadow-box editable value-group" id="filter-table"
       data-type="checkbox_group">
    <thead>
    <tr>
        <th class="header thin filter-field">Field</th>
        <th class="header thin filter-type">Filter Type</th>
        <th class="header thin filter-value" colspan="2">Value</th>
    </tr>
    </thead>
    <tbody>

    <?php $iterator = 0; ?>
    <?php if (!empty($profile->filters)) : ?>
        <?php foreach ($profile->filters as $filter) : ?>

            <tr data-iterator="<?php echo $iterator ?>">
                <td width="50">
                    <div class="select">
                        <select name="filters[<?php echo $iterator ?>][field]">
                            <?php foreach ($profile->getFieldSettings() as $fieldId => $fieldData) : ?>

                                <option value="<?php echo $fieldId ?>"
                                    <?php if ($fieldId == $filter['field']) : ?>
                                        selected
                                    <?php endif; ?>
                                >
                                    <?php echo $fieldData['label']; ?>
                                </option>

                            <?php endforeach; ?>
                        </select>
                    </div>
                </td>
                <td width="50">
                    <select name="filters[<?php echo $iterator ?>][type]">
                        <option value="="<?php echo $filter['type'] === '=' ? ' selected' : '' ?>>Equal To</option>
                        <option value="!="<?php echo $filter['type'] === '!=' ? ' selected' : '' ?>>Not Equal To
                        </option>
                        <option value="like"<?php echo $filter['type'] === 'like' ? ' selected' : '' ?>>Like
                        </option>
                    </select>
                </td>
                <td>
                    <input type="text"
						   class="value"
                           name="filters[<?php echo $iterator ?>][value]"
                           value="<?php echo $filter['value'] ?>"/>
                </td>
                <td width="10">
                    <div class="toolbar-wrap">
                        <ul class="toolbar">
                            <li class="delete">
                                <a title="Delete"></a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>

            <?php $iterator++; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <template>
        <tr data-iterator="__iterator__">
            <td width="50">
                <div class="select">
                    <select name="filters[__iterator__][field]">
                        <?php foreach ($profile->getFieldSettings() as $fieldId => $fieldData): ?>
                            <option value="<?php echo $fieldId ?>"><?php echo $fieldData['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </td>
            <td width="50">
                <select name="filters[__iterator__][type]">
                    <option value="=">Equal To</option>
                    <option value="!=">Not Equal To</option>
                    <option value="like">Like</option>
                </select>
            </td>
            <td>
                <input type="text" class="value" name="filters[__iterator__][value]"/>
            </td>
            <td width="10">
                <div class="toolbar-wrap">
                    <ul class="toolbar">
                        <li class="delete">
                            <a title="Delete"></a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </template>
    </tbody>
</table>

<a class="btn" id="add-filter">Add a row</a>

<style>
    li.delete a:after {
        font-family: 'Solspace Font Awesome 5 Regular', 'Solspace Font Awesome 5 Solid', sans-serif;
        content: '\f1f8';
		color: #bc4848;
    }

    input.value {
        width: 100%;
    }

    .editable select {
        margin-bottom: 0 !important;
    }
</style>
