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

<script type="text/javascript">
    window.runUrl = "<?=$migrate_url?>";
    window.firstStage =<?php echo json_encode($first_stage );?>;
    window.finishedRedirectUrl = "<?=$finished_redirect_url?>";
</script>


<div class="form-standard <?php echo version_compare(APP_VER, '4.0.0', '<') ? 'box' : '' ?>">
    <div class="form-btns form-btns-top">
        <h1><?= $cp_page_title ?></h1>
    </div>

    <div id="ready-wrapper" class="migration-info-block">
        <div id="ready-title">Ready for Migration</div>
        <div id="ready-text">
            Freeform has detected Freeform Classic on this site the migration utility is available!<br/>
            Be sure to read and follow the <a href="https://solspace.com/expressionengine/freeform/docs/classic-migration/">migration documentation</a>
            carefully in order to properly prepare and clean up your site afterward.
        </div>
        <div id="ready-text">
            The migration utility will attempt to migrate your Freeform Classic forms, fields and email notification templates.
            Formatting templates (a.k.a. Composer Templates) will not be migrated, and need to be recreated after. Migration of submissions is optional.
        </div>
        <div>
            <input class='user_roles' id="migrate-submissions" type="checkbox" name="migrate-submissions" />
            <label for="migrate-submissions">Migrate Submissions</label>
        </div>

        <fieldset class="form-ctrls">

            <div id="ready-buttons-wrapper">
                <button class="btn submit" id="start-migration-button">Start Migration</button>
            </div>
        </fieldset>
    </div>

    <div id="in-progress-wrapper" class="migration-info-block" style="display: none">
        <div id="in-progress-title">Migration In Progress</div>
        <div id="in-progress-text">
            Freeform is now migrating Freeform Classic. Please do not click anywhere else on this page,
            and wait until the migration is finished. If you have a lot of data, you may see the migration performed in batches.
        </div>
        <div id="in-progress-status-wrapper">

        </div>
        <div id="in-progress-status-footer">

        </div>

        <div id="in-progress-error-wrapper" style="display: none">
            <div id="in-progress-error-title"><i class="fa fa-ban" aria-hidden="true"></i>Cannot Perform Migration</div>
            <div id="in-progress-error-text">
                Freeform Classic migration cannot be performed. The new Freeform version must be a fresh install.
                To resolve this issue please uninstall the new Freeform version and install it again. Then attempt to run the
                Migration utility again without modifying Freeform. If this error persists, please contact Solspace support.
            </div>
        </div>

        <div id="in-progress-success-wrapper" style="display: none">
            <div id="in-progress-success-title">Migration successful!</div>
            <div id="in-progress-success-text">
                All forms, fields, email notification templates, and form statuses
                were successfully migrated.
            </div>
        </div>
    </div>

</div>


