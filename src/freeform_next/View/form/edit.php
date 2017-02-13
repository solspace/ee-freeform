<?php /** @var \Solspace\Addons\FreeformNext\Library\Composer\Components\Form $form */ ?>
<div id="freeform-builder"></div>

<script>
    var formId = <?= $form->getId() ?: 'null' ?>;
    var fieldList = <?= json_encode($fields) ?>;
    var mailingList = [];
    var crmIntegrations = [];
    var notificationList = [];
    var solspaceFormTemplates = [];
    var formTemplateList = [];
    var formStatuses = [];
    var assetSources = [];
    var fileKinds = [];
    var composerState = <?= $form->getComposer()->getComposerStateJSON() ?>;

    var baseUrl = "<?= ee('CP/URL', 'addons/settings/') ?>";
    var saveUrl = "<?= ee('CP/URL', 'addons/settings/freeform_next/forms') ?>";
    var formUrl = "<?= ee('CP/URL', 'addons/settings/freeform_next/forms/{id}') ?>";
    var createFieldUrl = "<?= ee('CP/URL', 'addons/settings/freeform_next/fields') ?>";
    var createNotificationUrl = "<?= ee('CP/URL', 'addons/settings/freeform_next/notifications') ?>";
    var createTemplateUrl = "<?= ee('CP/URL', 'addons/settings/freeform_next/templates') ?>";
    var finishTutorialUrl = "<?= ee('CP/URL', 'addons/settings/freeform_next/finish_tutorial') ?>";

    var showTutorial = true;
    var canManageFields = true;
    var canManageNotifications = true;
    var canManageSettings = true;

    var csrfToken = "<?= CSRF_TOKEN ?>";
</script>

<script src="<?= URL_THIRD_THEMES ?>freeform_next/javascript/composer/vendors.js"></script>
<script src="<?= URL_THIRD_THEMES ?>freeform_next/javascript/composer/app.js"></script>
<link rel="stylesheet" href="<?= URL_THIRD_THEMES ?>freeform_next/css/builder.css" />