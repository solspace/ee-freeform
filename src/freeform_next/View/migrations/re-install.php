<?php
/**
 * Freeform for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */
?>
<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/migrations.css"/>

<div class="form-standard <?php echo version_compare(APP_VER, '4.0.0', '<') ? 'box' : '' ?>">
    <div class="form-btns form-btns-top">
        <h1><?= $cp_page_title ?></h1>
    </div>

    <div id="reinstall-wrapper" class="migration-info-block" style="margin: 10px 0 30px;">
        <div id="reinstall-title"><i class="fa fa-ban" aria-hidden="true"></i>Cannot Perform Migration</div>
        <div id="reinstall-text">
            Freeform Classic migration cannot be performed. The new Freeform version must be a fresh install.
            To resolve this issue please uninstall the new Freeform version and install it again. Then attempt to
            run the Migration utility again without modifying Freeform. If this error persists, please contact Solspace
            support.
        </div>
    </div>
</div>
