<?php
/**
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

$this->extend('_layouts/table_form_wrapper')?>
<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/migrations.css"/>

<div id="reinstall-wrapper">
    <div id="reinstall-title"><i class="fa fa-ban" aria-hidden="true"></i>Cannot Perform Migration</div>
    <div id="reinstall-text">Freeform Classic migration cannot be performed. The Freeform Express version allows for only 1 form and a maximum of 20 fields.
        Your Freeform Classic installation contains <?php echo $formCount ?> forms and <?php echo $fieldCount ?> fields.
        To resolve this issue please upgrade your Freeform Next installation or remove forms and fields you do not require.
    </div>
</div>
