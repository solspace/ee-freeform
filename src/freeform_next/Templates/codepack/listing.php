<?php
/** @var \Solspace\Addons\FreeformNext\Library\Codepack\Codepack $codepack */
?>
<table>
    <tr>
        <th data-prefix class="folder">
            <?php echo $prefix ?>
        </th>
    </tr>
    <?php foreach ($codepack->getTemplates()->getContents() as $file) : ?>
        <tr>
            <td>
                <?= $file->getName() ?>
            </td>
        </tr>
    <?php endforeach; ?>
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

    .folder:before {
        content: '\f07c';
        font-family: 'Solspace Font Awesome 5 Regular', 'Solspace Font Awesome 5 Solid', sans-serif;
        color: #77bce6;
        font-weight: normal;
        margin-right: 5px;
    }
</style>


<script>

</script>
