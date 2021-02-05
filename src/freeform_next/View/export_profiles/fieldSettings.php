<?php /** @var \Solspace\Addons\FreeformNext\Model\ExportProfileModel $profile */ ?>
<table id="field-settings" class="data fullwidth collapsible">
    <thead>
    <th width="10"></th>
    <th></th>
    <th>Field Name</th>
    </thead>
    <tbody>
    <?php foreach ($profile->getFieldSettings() as $fieldId => $fieldData) : ?>
        <tr>
            <td width="10">
                <a class="handle" title="Reorder"></a>
            </td>
            <td width="10">
                <input type="hidden"
                       name="fieldSettings[<?php echo $fieldId ?>][checked]"
                       value="0"
                />
                <input type="checkbox"
                       name="fieldSettings[<?php echo $fieldId ?>][checked]"
                       value="1"
                       <?php echo $fieldData['checked'] ? 'checked' : '' ?>
                />
            </td>
            <td>
                <?php echo $fieldData['label']; ?>
                <input type="hidden"
                       name="fieldSettings[<?php echo $fieldId ?>][label]"
                       value="<?php echo $fieldData['label']; ?>"
                />
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<style>
    a.handle:before {
        display: block;
        content: "\f0c9";
        font-family: 'Font Awesome 5 Free', sans-serif;
        color: black;
    }
</style>
