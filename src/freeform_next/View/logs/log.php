<?php $this->extend('_layouts/table_form_wrapper') ?>

<div class="log-wrapper">
    <?php
    foreach ($content as $line) {
        $date     = $line['date'];
        $level    = $line['level'];
        $category = $line['category'];
        $message  = $line['message'];

        echo '<div class="level-' . htmlspecialchars(strtolower((string) $level), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">';

        echo sprintf(
            '<div class="date" title="%s">%s</div>',
            $date->format('Y-m-d H:i:s'),
            $date->format('Y-m-d H:i:s')
        );
        echo sprintf(
            '<div class="level" title="%s">%s</div>',
            htmlspecialchars((string) $category, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            htmlspecialchars((string) $level, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        );
        echo sprintf('<div class="message">%s</div>', htmlspecialchars((string) $message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

        echo '</div>';
    }
    ?>
</div>

<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/logs.css" />
