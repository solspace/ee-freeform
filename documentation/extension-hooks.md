# Extension Hooks

If you wish to extend the capabilities of Freeform, you can use any of the extension hooks below:

## Events for Freeform Forms <a href="#events-freeform-forms" id="events-freeform-forms" class="docs-anchor">#</a>

* `onBeforeSave` <a href="#event-onbeforesave-form" id="event-onbeforesave-form" class="docs-anchor">#</a>
	* Called before saving a form
	* Contains these parameters:
		*  `model` - the `Freeform_FormModel`
		*  `isNew` - boolean value
* `onAfterSave` <a href="#event-onaftersave-form" id="event-onaftersave-form" class="docs-anchor">#</a>
	* Called after saving a form
	* Contains these parameters:
		*  `model` - the `Freeform_FormModel`
		*  `isNew` - boolean value
*  `onBeforeDelete` <a href="#event-onbeforedelete-form" id="event-onbeforedelete-form" class="docs-anchor">#</a>
	*  Called before deleting a form
	*  Contains these parameters:
		*  `model` - the `Freeform_FormModel`
*  `onAfterDelete` <a href="#event-onafterdelete-event" id="event-onafterdelete-form" class="docs-anchor">#</a>
	*  Called after deleting a form
	*  Contains these parameters:
		*  `model` - the `Freeform_FormModel`

## Events for Freeform Submissions <a href="#events-freeform-submissions" id="events-freeform-submissions" class="docs-anchor">#</a>

* `onBeforeSave` <a href="#event-onbeforesave-submission" id="event-onbeforesave-submission" class="docs-anchor">#</a>
	* Called before saving a submission
	* Contains these parameters:
		*  `model` - the `Freeform_SubmissionModel`
		*  `isNew` - boolean value
* `onAfterSave` <a href="#event-onaftersave-submission" id="event-onaftersave-submission" class="docs-anchor">#</a>
	* Called after saving a submission
	* Contains these parameters:
		*  `model` - the `Freeform_SubmissionModel`
		*  `isNew` - boolean value
*  `onBeforeDelete` <a href="#event-onbeforedelete-submission" id="event-onbeforedelete-submission" class="docs-anchor">#</a>
	*  Called before deleting a submission
	*  Contains these parameters:
		*  `model` - the `Freeform_SubmissionModel`
*  `onAfterDelete` <a href="#event-onafterdelete-submission" id="event-onafterdelete-submission" class="docs-anchor">#</a>
	*  Called after deleting a submission
	*  Contains these parameters:
		*  `model` - the `Freeform_SubmissionModel`

## Events for Freeform Fields <a href="#events-freeform-fields" id="events-freeform-fields" class="docs-anchor">#</a>

* `onBeforeSave` <a href="#event-onbeforesave-fields" id="event-onbeforesave-fields" class="docs-anchor">#</a>
	* Called before saving a field
	* Contains these parameters:
		*  `model` - the `Freeform_FieldModel`
		*  `isNew` - boolean value
* `onAfterSave` <a href="#event-onaftersave-fields" id="event-onaftersave-fields" class="docs-anchor">#</a>
	* Called after saving a field
	* Contains these parameters:
		*  `model` - the `Freeform_FieldModel`
		*  `isNew` - boolean value
*  `onBeforeDelete` <a href="#event-onbeforedelete-fields" id="event-onbeforedelete-fields" class="docs-anchor">#</a>
	*  Called before deleting a field
	*  Contains these parameters:
		*  `model` - the `Freeform_FieldModel`
*  `onAfterDelete` <a href="#event-onafterdelete-fields" id="event-onafterdelete-fields" class="docs-anchor">#</a>
	*  Called after deleting a field
	*  Contains these parameters:
		*  `model` - the `Freeform_FieldModel`

## Events for Freeform Notifications <a href="#events-freeform-notifications" id="events-freeform-notifications" class="docs-anchor">#</a>

* `onBeforeSave` <a href="#event-onbeforesave-notifications" id="event-onbeforesave-notifications" class="docs-anchor">#</a>
	* Called before saving a notification
	* Contains these parameters:
		*  `model` - the `Freeform_NotificationModel`
		*  `isNew` - boolean value
* `onAfterSave` <a href="#event-onaftersave-notifications" id="event-onaftersave-notifications" class="docs-anchor">#</a>
	* Called after saving a notification
	* Contains these parameters:
		*  `model` - the `Freeform_NotificationModel`
		*  `isNew` - boolean value
*  `onBeforeDelete` <a href="#event-onbeforedelete-notifications" id="event-onbeforedelete-notifications" class="docs-anchor">#</a>
	*  Called before deleting a notification
	*  Contains these parameters:
		*  `model` - the `Freeform_NotificationModel`
*  `onAfterDelete` <a href="#event-onafterdelete-notifications" id="event-onafterdelete-notifications" class="docs-anchor">#</a>
	*  Called after deleting a notification
	*  Contains these parameters:
		*  `model` - the `Freeform_NotificationModel`

## Events for Freeform Statuses <a href="#events-freeform-statuses" id="events-freeform-statuses" class="docs-anchor">#</a>

* `onBeforeSave` <a href="#event-onbeforesave-statuses" id="event-onbeforesave-statuses" class="docs-anchor">#</a>
	* Called before saving a status
	* Contains these parameters:
		*  `model` - the `Freeform_StatusModel`
		*  `isNew` - boolean value
* `onAfterSave` <a href="#event-onaftersave-statuses" id="event-onaftersave-statuses" class="docs-anchor">#</a>
	* Called after saving a status
	* Contains these parameters:
		*  `model` - the `Freeform_StatusModel`
		*  `isNew` - boolean value
*  `onBeforeDelete` <a href="#event-onbeforedelete-statuses" id="event-onbeforedelete-statuses" class="docs-anchor">#</a>
	*  Called before deleting a status
	*  Contains these parameters:
		*  `model` - the `Freeform_StatusModel`
*  `onAfterDelete` <a href="#event-onafterdelete-statuses" id="event-onafterdelete-statuses" class="docs-anchor">#</a>
	*  Called after deleting a status
	*  Contains these parameters:
		*  `model` - the `Freeform_StatusModel`

## Events for Freeform File Uploads <a href="#events-freeform-file-uploads" id="events-freeform-file-uploads" class="docs-anchor">#</a>

* `onBeforeUpload` <a href="#event-onbeforeupload-file-uploads" id="event-onbeforeupload-file-uploads" class="docs-anchor">#</a>
	* Called before uploading a file
	* Contains these parameters:
		*  `field` - the `FileUploadField`
* `onAfterUpload` <a href="#event-onafterupload-file-uploads" id="event-onafterupload-file-uploads" class="docs-anchor">#</a>
	* Called after uploading a file
	* Contains these parameters:
		*  `field` - the `FileUploadField`
		*  `assetId` - boolean value

## Events for Freeform Mailing <a href="#events-freeform-mailing" id="events-freeform-mailing" class="docs-anchor">#</a>

* `onBeforeSend` <a href="#event-onbeforesend-mailing" id="event-onbeforesend-mailing" class="docs-anchor">#</a>
	* Called before sending an email
	* Contains these parameters:
		*  `model` - the `EmailModel`
* `onAfterSend` <a href="#event-onaftersend-mailing" id="event-onaftersend-mailing" class="docs-anchor">#</a>
	* Called after sending an email
	* Contains these parameters:
		*  `model` - the `EmailModel`
		*  `isSent` - boolean value

## Events for Freeform CRM Integrations <a href="#events-freeform-crm-integrations" id="events-freeform-crm-integrations" class="docs-anchor">#</a>

* `onBeforeSave` <a href="#event-onbeforesave-crm-integrations" id="event-onbeforesave-crm-integrations" class="docs-anchor">#</a>
	* Called before saving an integration
	* Contains these parameters:
		*  `model` - the `Freeform_IntegrationModel`
		*  `isNew` - boolean value
* `onAfterSave` <a href="#event-onaftersave-crm-integrations" id="event-onaftersave-crm-integrations" class="docs-anchor">#</a>
	* Called after saving an integration
	* Contains these parameters:
		*  `model` - the `Freeform_IntegrationModel`
		*  `isNew` - boolean value
*  `onBeforeDelete` <a href="#event-onbeforedelete-crm-integrations" id="event-onbeforedelete-crm-integrations" class="docs-anchor">#</a>
	*  Called before deleting an integration
	*  Contains these parameters:
		*  `model` - the `Freeform_IntegrationModel`
*  `onAfterDelete` <a href="#event-onafterdelete-crm-integrations" id="event-onafterdelete-crm-integrations" class="docs-anchor">#</a>
	*  Called after deleting an integration
	*  Contains these parameters:
		*  `model` - the `Freeform_IntegrationModel`

## Events for Freeform Mailing-List Integrations <a href="#events-freeform-mailing-list-integrations" id="events-freeform-mailing-list-integrations" class="docs-anchor">#</a>

* `onBeforeSave` <a href="#event-onbeforesave-mailing-list-integrations" id="event-onbeforesave-mailing-list-integrations" class="docs-anchor">#</a>
	* Called before saving an integration
	* Contains these parameters:
		*  `model` - the `Freeform_IntegrationModel`
		*  `isNew` - boolean value
* `onAfterSave` <a href="#event-onaftersave-mailing-list-integrations" id="event-onaftersave-mailing-list-integrations" class="docs-anchor">#</a>
	* Called after saving an integration
	* Contains these parameters:
		*  `model` - the `Freeform_IntegrationModel`
		*  `isNew` - boolean value
*  `onBeforeDelete` <a href="#event-onbeforedelete-mailing-list-integrations" id="event-onbeforedelete-mailing-list-integrations" class="docs-anchor">#</a>
	*  Called before deleting an integration
	*  Contains these parameters:
		*  `model` - the `Freeform_IntegrationModel`
*  `onAfterDelete` <a href="#event-onafterdelete-mailing-list-integrations" id="event-onafterdelete-mailing-list-integrations" class="docs-anchor">#</a>
	*  Called after deleting an integration
	*  Contains these parameters:
		*  `model` - the `Freeform_IntegrationModel`

### Usage instructions <a href="#events-freeform-events-usage" id="events-freeform-events-usage" class="docs-anchor">#</a>

In your plugin's `::init()` method, subscribe to any of these events by using the `craft()->on()` method:

	<?php

	namespace Craft;

	class YourPlugin extends BasePlugin
	{
		public function init()
		{
			parent::init();

			// Forms
			craft()->on(
				"freeform_forms.onBeforeSave",
				function (Event $event) {
					$form = $event->params["model"];
					// Do something with this data
				}
			);

			// Submissions
			craft()->on(
				"freeform_submissions.onAfterSave",
				function (Event $event) {
					$submission = $event->params["model"];
					$isNew	  = $event->params["isNew"];
					// Do something with this data
				}
			);

			// Fields
			craft()->on(
				"freeform_fields.onBeforeDelete",
				function (Event $event) {
					$field = $event->params["model"];
					// Do something with this data
				}
			);

			// Notifications
			craft()->on(
				"freeform_notifications.onAfterDelete",
				function (Event $event) {
					$notification = $event->params["model"];
					// Do something with this data
				}
			);

			// Statuses
			craft()->on(
				"freeform_statuses.onBeforeSave",
				function (Event $event) {
					$status = $event->params["model"];
					// Do something with this data
				}
			);

			// File Uploads
			craft()->on(
				"freeform_files.onAfterUpload",
				function (Event $event) {
					$field   = $event->params["field"];
					$assetId = $event->params["assetId"];
					// Do something with this data
				}
			);

			// Mailing
			craft()->on(
				"freeform_mailer.onAfterSend",
				function (Event $event) {
					$model  = $event->params["model"];
					$isSent = $event->params["isSent"];
					// Do something with this data
				}
			);

			// CRM Integrations
			craft()->on(
				"freeform_crm.onBeforeSave",
				function (Event $event) {
					$crmIntegration = $event->params["model"];
					// Do something with this data
				}
			);

			// Mailing List Integrations
			craft()->on(
				"freeform_mailingLists.onBeforeSave",
				function (Event $event) {
					$mailingListIntegration = $event->params["model"];
					// Do something with this data
				}
			);
		}
	}
