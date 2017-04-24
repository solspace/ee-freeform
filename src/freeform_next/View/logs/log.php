<?php $this->extend('_layouts/table_form_wrapper') ?>

<div class="log-wrapper">
    <?php
    foreach ($content as $line) {
        $date     = $line['date'];
        $level    = $line['level'];
        $category = $line['category'];
        $message  = $line['message'];

        echo '<div class="level-' . strtolower($level) . '">';

        echo sprintf(
            '<div class="date" title="%s">%s</div>',
            $date->format('Y-m-d H:i:s'),
            $date->format('Y-m-d H:i:s')
        );
        echo sprintf('<div class="level" title="%s">%s</div>', $category, $level);
        echo sprintf('<div class="message">%s</div>', $message);

        echo '</div>';
    }
    ?>
</div>

<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/logs.css" />
