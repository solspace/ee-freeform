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

    <div id="reinstall-wrapper">
        <div id="reinstall-title"><i class="fa fa-ban" aria-hidden="true"></i>Cannot Perform Migration</div>
        <div id="reinstall-text">
            Freeform Classic migration cannot be performed. The Freeform Express edition only allows a maximum of 1 form and 15 fields.
            Your Freeform Classic installation contains <?php echo $formCount ?> forms and <?php echo $fieldCount ?> fields.
            To resolve this issue, please consider using Freeform Lite edition instead, or remove forms and fields you do not require.
        </div>
    </div>
</div>
