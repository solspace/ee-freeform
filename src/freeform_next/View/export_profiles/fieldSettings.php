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
    @font-face {
        font-family: 'Solspace Font Awesome 5 Solid';
        src: url('<?php echo URL_THIRD_THEMES ?>/freeform_next/font/fontawesome-free-5.15.4-web/webfonts/fa-solid-900.svg?61199501');
        src: url('<?php echo URL_THIRD_THEMES ?>/freeform_next/font/fontawesome-free-5.15.4-web/webfonts/fa-solid-900.eot?61199501#iefix') format('embedded-opentype'),
        url('<?php echo URL_THIRD_THEMES ?>/freeform_next/font/fontawesome-free-5.15.4-web/webfonts/fa-solid-900.woff2?61199501') format('woff2'),
        url('<?php echo URL_THIRD_THEMES ?>/freeform_next/font/fontawesome-free-5.15.4-web/webfonts/fa-solid-900.woff?61199501') format('woff'),
        url('<?php echo URL_THIRD_THEMES ?>/freeform_next/font/fontawesome-free-5.15.4-web/webfonts/fa-solid-900.ttf?61199501') format('truetype'),
        url('<?php echo URL_THIRD_THEMES ?>/freeform_next/font/fontawesome-free-5.15.4-web/webfonts/fa-solid-900.svg?61199501#solspace-freeform') format('svg');
        font-weight: 900;
        font-style: normal;
    }

    @font-face {
        font-family: 'Solspace Font Awesome 5 Regular';
        src: url('<?php echo URL_THIRD_THEMES ?>/freeform_next/font/fontawesome-free-5.15.4-web/webfonts/fa-regular-400.svg?61199501');
        src: url('<?php echo URL_THIRD_THEMES ?>/freeform_next/font/fontawesome-free-5.15.4-web/webfonts/fa-regular-400.eot?61199501#iefix') format('embedded-opentype'),
        url('<?php echo URL_THIRD_THEMES ?>/freeform_next/font/fontawesome-free-5.15.4-web/webfonts/fa-regular-400.woff2?61199501') format('woff2'),
        url('<?php echo URL_THIRD_THEMES ?>/freeform_next/font/fontawesome-free-5.15.4-web/webfonts/fa-regular-400.woff?61199501') format('woff'),
        url('<?php echo URL_THIRD_THEMES ?>/freeform_next/font/fontawesome-free-5.15.4-web/webfonts/fa-regular-400.ttf?61199501') format('truetype'),
        url('<?php echo URL_THIRD_THEMES ?>/freeform_next/font/fontawesome-free-5.15.4-web/webfonts/fa-regular-400.svg?61199501#solspace-freeform') format('svg');
        font-weight: normal;
        font-style: normal;
    }

    a.handle:before {
        display: block;
        content: "\f0c9";
        font-family: 'Solspace Font Awesome 5 Regular', 'Solspace Font Awesome 5 Solid', sans-serif;
        color: black;
    }
</style>
