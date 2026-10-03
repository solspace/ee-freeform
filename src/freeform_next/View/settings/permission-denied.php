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
?>
<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/migrations.css"/>

<div class="form-standard <?php echo version_compare(APP_VER, '4.0.0', '<') ? 'box' : '' ?>">
    <div class="form-btns form-btns-top">
        <h1><?= htmlspecialchars((string) $cp_page_title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>
    </div>
</div>
