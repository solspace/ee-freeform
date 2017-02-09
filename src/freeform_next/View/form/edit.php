<div id="freeform-builder"></div>

<script>
    var formId = null;
    var fieldList = [];
    var mailingList = [];
    var crmIntegrations = [];
    var notificationList = [];
    var solspaceFormTemplates = [];
    var formTemplateList = [];
    var formStatuses = [];
    var assetSources = [];
    var fileKinds = [];
    var composerState = <?= $form->getComposer()->getComposerStateJSON() ?>;

    var saveUrl = "";
    var formUrl = "";
    var createFieldUrl = "";
    var createNotificationUrl = "";
    var createTemplateUrl = "";
    var finishTutorialUrl = "";

    var showTutorial = true;
    var canManageFields = true;
    var canManageNotifications = true;
    var canManageSettings = true;
</script>

<script src="<?= URL_THIRD_THEMES ?>freeform_next/javascript/composer/vendors.js"></script>
<script src="<?= URL_THIRD_THEMES ?>freeform_next/javascript/composer/app.js"></script>
<link rel="stylesheet" href="<?= URL_THIRD_THEMES ?>freeform_next/css/builder.css" />