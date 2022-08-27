<table>
    <tr>
        <th class="folder">
            <?php echo $path ?>

            <a class="btn action add-template button--small" href="<?php echo $url ?>">
                Add sample template
            </a>
        </th>
    </tr>
    <?php foreach ($files as $file) : ?>
        <tr>
            <td>
                <?= $file ?>
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

    .folder {
        position: relative !important;
    }

    .folder:before {
        content: '\f07c';
        font-family: 'Solspace Font Awesome 5 Regular', 'Solspace Font Awesome 5 Solid', sans-serif;
        color: #77bce6;
        font-weight: normal;
        margin-right: 5px;
    }

    a.add-template {
        position: absolute;
        right: 5px;
        top: <?php echo version_compare(APP_VER, '4.0.0', '<') ? '5px' : '-6px' ?>;

        display: inline-block;
        font-weight:normal;
    }

    a.add-template:before {
        content: '\f067';
        font-family: 'Solspace Font Awesome 5 Regular', 'Solspace Font Awesome 5 Solid', sans-serif;
    }
</style>
