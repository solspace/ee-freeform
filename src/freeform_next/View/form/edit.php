<?php
/** @var \Solspace\Addons\FreeformNext\Model\FormModel $form */
/** @var \Solspace\Addons\FreeformNext\Model\FieldModel[] $fields */
/** @var \Solspace\Addons\FreeformNext\Model\NotificationModel[] $notifications */
/** @var \Solspace\Addons\FreeformNext\Model\StatusModel[] $statuses */
/** @var \Solspace\Addons\FreeformNext\Model\IntegrationModel[] $mailingLists */
/** @var \Solspace\Addons\FreeformNext\Model\IntegrationModel[] $crmIntegrations */
/** @var \Solspace\Addons\FreeformNext\Model\StatusModel[] $statuses */
/** @var array $assetSources */
/** @var array $fileKinds */
/** @var array $formTemplates */
/** @var array $solspaceFormTemplates */
/** @var array $defaultTemplates */
/** @var bool $showTutorial */
?>
<div id="freeform-builder"></div>

<script>
    var formId = <?php echo $form->getId() ?: 'null' ?>;
    var fieldList = <?php echo json_encode($fields) ?>;
    var fieldTypeList = <?php echo json_encode($fieldTypeList) ?>;
    var mailingList = <?php echo json_encode($mailingLists) ?>;
    var crmIntegrations = <?php echo json_encode($crmIntegrations) ?>;
    var notificationList = <?php echo json_encode($notifications) ?>;
    var solspaceFormTemplates = <?php echo json_encode($solspaceFormTemplates) ?>;
    var formTemplateList = <?php echo json_encode($formTemplates) ?>;
    var formStatuses = <?php echo json_encode($statuses) ?>;
    var assetSources = <?php echo json_encode($assetSources) ?>;
    var fileKinds = <?php echo json_encode($fileKinds) ?>;
    var composerState = <?php echo $form->getComposer()->getComposerStateJSON() ?>;
    var sourceTargets = <?php echo json_encode($sourceTargets) ?>;
    var generatedOptions = <?php echo json_encode($generatedOptions) ?>;
    var channelFields = <?php echo json_encode($channelFields) ?>;
    var categoryFields = <?php echo json_encode($categoryFields) ?>;
    var memberFields = <?php echo json_encode($memberFields) ?>;

    var baseUrl = "<?php echo ee('CP/URL', 'addons/settings/') ?>";
    var saveUrl = "<?php echo ee('CP/URL', 'addons/settings/freeform_next/forms') ?>";
    var formUrl = "<?php echo ee('CP/URL', 'addons/settings/freeform_next/forms/{id}') ?>";
    var createFieldUrl = "<?php echo ee('CP/URL', 'addons/settings/freeform_next/api/fields') ?>";
    var createNotificationUrl = "<?php echo ee('CP/URL', 'addons/settings/freeform_next/api/notifications/create') ?>";
    var createTemplateUrl = "<?php echo ee('CP/URL', 'addons/settings/freeform_next/templates') ?>";
    var finishTutorialUrl = "<?php echo ee('CP/URL', 'addons/settings/freeform_next/finish_tutorial') ?>";

    var showTutorial = <?php echo $showTutorial ? 'true' : 'false' ?>;
    var defaultTemplates = <?php echo $defaultTemplates ? 'true' : 'false' ?>;
    var canManageFields = true;
    var canManageNotifications = true;
    var canManageSettings = true;
    var isRecaptchaEnabled = <?php echo $isRecaptchaEnabled ? 'true' : 'false' ?>;
    var isRecaptchaV3 = <?php echo $isRecaptchaV3 ? 'true' : 'false' ?>;

    var isDbEmailTemplateStorage = <?php echo $isDbEmailTemplateStorage ? 'true' : 'false' ?>;
    var isWidgetsInstalled       = <?php echo $isWidgetsInstalled ? 'true' : 'false' ?>;

    var formPropCleanup = <?php echo \Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper::get('props') ? 'true' : 'false' ?>;

    var csrfToken = "<?php echo CSRF_TOKEN ?>";
</script>

<script src="<?php echo URL_THIRD_THEMES ?>freeform_next/javascript/composer/vendors.js"></script>
<script src="<?php echo URL_THIRD_THEMES ?>freeform_next/javascript/composer/app.js"></script>
<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/builder.css" />
