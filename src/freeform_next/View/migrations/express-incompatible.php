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

$this->extend('_layouts/table_form_wrapper')?>
<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/migrations.css"/>

<div id="reinstall-wrapper">
    <div id="reinstall-title"><i class="fa fa-ban" aria-hidden="true"></i>Cannot Perform Migration</div>
    <div id="reinstall-text">
        Freeform Classic migration cannot be performed. The Freeform Express edition only allows a maximum of 1 form and 15 fields.
        Your Freeform Classic installation contains <?php echo $formCount ?> forms and <?php echo $fieldCount ?> fields.
        To resolve this issue please consider using Freeform Lite edition instead, or remove forms and fields you do not require.
    </div>
</div>
