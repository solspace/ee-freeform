<?php
/** @var \Solspace\Addons\FreeformNext\Model\FormModel $form */
/** @var \Solspace\Addons\FreeformNext\Model\FieldModel[] $fields */
/** @var \Solspace\Addons\FreeformNext\Model\NotificationModel[] $notifications */
/** @var \Solspace\Addons\FreeformNext\Model\StatusModel[] $statuses */
/** @var \Solspace\Addons\FreeformNext\Model\IntegrationModel[] $mailingLists */
/** @var \Solspace\Addons\FreeformNext\Model\IntegrationModel[] $statuses */
/** @var array $assetSources */
/** @var array $fileKinds */
/** @var array $formTemplates */
/** @var array $solspaceFormTemplates */
/** @var bool $showTutorial */
?>
<div id="freeform-builder"></div>

<script>
    var formId = <?php echo $form->getId() ?: 'null' ?>;
    var fieldList = <?php echo json_encode($fields) ?>;
    var mailingList = <?php echo json_encode($mailingLists) ?>;
    var crmIntegrations = <?php echo json_encode($crmIntegrations) ?>;
    var notificationList = <?php echo json_encode($notifications) ?>;
    var solspaceFormTemplates = <?php echo json_encode($solspaceFormTemplates) ?>;
    var formTemplateList = <?php echo json_encode($formTemplates) ?>;
    var formStatuses = <?php echo json_encode($statuses) ?>;
    var assetSources = <?php echo json_encode($assetSources) ?>;
    var fileKinds = <?php echo json_encode($fileKinds) ?>;
    var composerState = <?php echo $form->getComposer()->getComposerStateJSON() ?>;

    var baseUrl = "<?php echo ee('CP/URL', 'addons/settings/') ?>";
    var saveUrl = "<?php echo ee('CP/URL', 'addons/settings/freeform_next/forms') ?>";
    var formUrl = "<?php echo ee('CP/URL', 'addons/settings/freeform_next/forms/{id}') ?>";
    var createFieldUrl = "<?php echo ee('CP/URL', 'addons/settings/freeform_next/api/fields') ?>";
    var createNotificationUrl = "<?php echo ee('CP/URL', 'addons/settings/freeform_next/notifications') ?>";
    var createTemplateUrl = "<?php echo ee('CP/URL', 'addons/settings/freeform_next/templates') ?>";
    var finishTutorialUrl = "<?php echo ee('CP/URL', 'addons/settings/freeform_next/finish_tutorial') ?>";

    var showTutorial = <?php echo $showTutorial ? 'true' : 'false' ?>;
    var canManageFields = true;
    var canManageNotifications = true;
    var canManageSettings = true;

    var csrfToken = "<?php echo CSRF_TOKEN ?>";
</script>

<script src="<?php echo URL_THIRD_THEMES ?>freeform_next/javascript/composer/vendors.js"></script>
<script src="<?php echo URL_THIRD_THEMES ?>freeform_next/javascript/composer/app.js"></script>
<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/builder.css" />
