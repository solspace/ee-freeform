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
$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE;
?>
<div id="freeform-builder"></div>

<script>
    var formId = <?php echo $form->getId() ? (int) $form->getId() : 'null' ?>;
    var fieldList = <?php echo json_encode($fields, $jsonFlags) ?>;
    var fieldTypeList = <?php echo json_encode($fieldTypeList, $jsonFlags) ?>;
    var mailingList = <?php echo json_encode($mailingLists, $jsonFlags) ?>;
    var crmIntegrations = <?php echo json_encode($crmIntegrations, $jsonFlags) ?>;
    var notificationList = <?php echo json_encode($notifications, $jsonFlags) ?>;
    var solspaceFormTemplates = <?php echo json_encode($solspaceFormTemplates, $jsonFlags) ?>;
    var formTemplateList = <?php echo json_encode($formTemplates, $jsonFlags) ?>;
    var formStatuses = <?php echo json_encode($statuses, $jsonFlags) ?>;
    var assetSources = <?php echo json_encode($assetSources, $jsonFlags) ?>;
    var fileKinds = <?php echo json_encode($fileKinds, $jsonFlags) ?>;
    var composerState = <?php echo $form->getComposer()->getComposerStateJSON($jsonFlags) ?>;
    var sourceTargets = <?php echo json_encode($sourceTargets, $jsonFlags) ?>;
    var generatedOptions = <?php echo json_encode($generatedOptions, $jsonFlags) ?>;
    var channelFields = <?php echo json_encode($channelFields, $jsonFlags) ?>;
    var categoryFields = <?php echo json_encode($categoryFields, $jsonFlags) ?>;
    var memberFields = <?php echo json_encode($memberFields, $jsonFlags) ?>;

    var baseUrl = <?php echo json_encode((string) ee('CP/URL', 'addons/settings/'), $jsonFlags) ?>;
    var saveUrl = <?php echo json_encode((string) ee('CP/URL', 'addons/settings/freeform_next/forms'), $jsonFlags) ?>;
    var formUrl = <?php echo json_encode((string) ee('CP/URL', 'addons/settings/freeform_next/forms/{id}'), $jsonFlags) ?>;
    var createFieldUrl = <?php echo json_encode((string) ee('CP/URL', 'addons/settings/freeform_next/api/fields'), $jsonFlags) ?>;
    var createNotificationUrl = <?php echo json_encode((string) ee('CP/URL', 'addons/settings/freeform_next/api/notifications/create'), $jsonFlags) ?>;
    var createTemplateUrl = <?php echo json_encode((string) ee('CP/URL', 'addons/settings/freeform_next/templates'), $jsonFlags) ?>;
    var finishTutorialUrl = <?php echo json_encode((string) ee('CP/URL', 'addons/settings/freeform_next/finish_tutorial'), $jsonFlags) ?>;

    var showTutorial = <?php echo $showTutorial ? 'true' : 'false' ?>;
    var defaultTemplates = <?php echo $defaultTemplates ? 'true' : 'false' ?>;
    var canManageFields = true;
    var canManageNotifications = true;
    var canManageSettings = true;
    var isRecaptchaEnabled = <?php echo $isRecaptchaEnabled ? 'true' : 'false' ?>;
    var isRecaptchaV3 = <?php echo $isRecaptchaV3 ? 'true' : 'false' ?>;

    var isDbEmailTemplateStorage = <?php echo $isDbEmailTemplateStorage ? 'true' : 'false' ?>;
    var isWidgetsInstalled       = <?php echo $isWidgetsInstalled ? 'true' : 'false' ?>;

    var formPropCleanup = <?php echo \Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper::isExpressEdition() ? 'true' : 'false' ?>;

    var csrfToken = <?php echo json_encode((string) CSRF_TOKEN, $jsonFlags) ?>;
</script>

<script src="<?php echo htmlspecialchars((string) URL_THIRD_THEMES, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>freeform_next/javascript/composer/app.js?v=4-html-editor"></script>
<link rel="stylesheet" href="<?php echo htmlspecialchars((string) URL_THIRD_THEMES, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>freeform_next/css/builder.css?v=4-html-editor" />
