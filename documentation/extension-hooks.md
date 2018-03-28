# Extension Hooks

If you wish to extend the capabilities of Freeform, you can use any of the extension hooks below:

## Hooks for Freeform Forms <a href="#hooks-freeform-forms" id="hooks-freeform-forms" class="docs-anchor">#</a>

* `freeform_next_form_before_submit` <a href="#hook-onbeforesubmit-form" id="hook-onbeforesubmit-form" class="docs-anchor">#</a>
	* Called before submitting a form
	* Contains these parameters:
		*  `form` - the `Form` object
* `freeform_next_form_after_submit` <a href="#hook-onaftersubmit-form" id="hook-onaftersubmit-form" class="docs-anchor">#</a>
	* Called after submitting a form
	* Contains these parameters:
		*  `form` - the `Form` object
		*  `submission` - the `SubmissionModel` object if the form is set to store data
* `freeform_next_form_before_save` <a href="#form_before_save" id="form_before_save" class="docs-anchor">#</a>
	* Called before saving a form.
	* Contains these parameters:
		*  `model` - the `FormModel`
		*  `isNew` - boolean value
* `freeform_next_form_after_save` <a href="#form_after_save" id="form_after_save" class="docs-anchor">#</a>
	* Called after saving a form.
	* Contains these parameters:
		*  `model` - the `FormModel`
		*  `isNew` - boolean value
* `freeform_next_form_before_submit` <a href="#form_before_submit" id="form_before_submit" class="docs-anchor">#</a>
	* Called before submitting a form.
	* Contains these parameters:
		*  `model` - the `FormModel`
		*  `isNew` - boolean value
* `freeform_next_form_after_submit` <a href="#form_after_submit" id="form_after_submit" class="docs-anchor">#</a>
	* Called after submitting a form.
	* Contains these parameters:
		*  `model` - the `FormModel`
		*  `isNew` - boolean value
*  `freeform_next_form_before_delete` <a href="#form_before_delete" id="form_before_delete" class="docs-anchor">#</a>
	*  Called before deleting a form.
	*  Contains these parameters:
		*  `model` - the `FormModel`
*  `freeform_next_form_after_delete` <a href="#form_after_delete" id="form_after_delete" class="docs-anchor">#</a>
	*  Called after deleting a form.
	*  Contains these parameters:
		*  `model` - the `FormModel`

## Hooks for Freeform Submissions <a href="#hooks-freeform-submissions" id="hooks-freeform-submissions" class="docs-anchor">#</a>

* `freeform_next_submission_before_save` <a href="#submission_before_save" id="submission_before_save" class="docs-anchor">#</a>
	* Called before saving a submission.
	* Contains these parameters:
		*  `model` - the `SubmissionModel`
		*  `isNew` - boolean value
* `freeform_next_submission_after_save` <a href="#submission_after_save" id="submission_after_save" class="docs-anchor">#</a>
	* Called after saving a submission.
	* Contains these parameters:
		*  `model` - the `SubmissionModel`
		*  `isNew` - boolean value
*  `freeform_next_submission_before_delete` <a href="#submission_before_delete" id="submission_before_delete" class="docs-anchor">#</a>
	*  Called before deleting a submission.
	*  Contains these parameters:
		*  `model` - the `SubmissionModel`
*  `freeform_next_submission_after_delete` <a href="#submission_after_delete" id="submission_after_delete" class="docs-anchor">#</a>
	*  Called after deleting a submission.
	*  Contains these parameters:
		*  `model` - the `SubmissionModel`

## Hooks for Freeform Fields <a href="#hooks-freeform-fields" id="hooks-freeform-fields" class="docs-anchor">#</a>

* `freeform_next_field_before_save` <a href="#field_before_save" id="field_before_save" class="docs-anchor">#</a>
	* Called before saving a field.
	* Contains these parameters:
		*  `model` - the `FieldModel`
		*  `isNew` - boolean value
* `freeform_next_field_after_save` <a href="#field_after_save" id="field_after_save" class="docs-anchor">#</a>
	* Called after saving a field.
	* Contains these parameters:
		*  `model` - the `FieldModel`
		*  `isNew` - boolean value
*  `freeform_next_field_before_delete` <a href="#field_before_delete" id="field_before_delete" class="docs-anchor">#</a>
	*  Called before deleting a field.
	*  Contains these parameters:
		*  `model` - the `FieldModel`
*  `freeform_next_field_after_delete` <a href="#field_after_delete" id="field_after_delete" class="docs-anchor">#</a>
	*  Called after deleting a field.
	*  Contains these parameters:
		*  `model` - the `FieldModel`

## Hooks for Freeform Notifications <a href="#hooks-freeform-notifications" id="hooks-freeform-notifications" class="docs-anchor">#</a>

* `freeform_next_notification_before_save` <a href="#notification_before_save" id="notification_before_save" class="docs-anchor">#</a>
	* Called before saving a notification.
	* Contains these parameters:
		*  `model` - the `NotificationModel`
		*  `isNew` - boolean value
* `freeform_next_notification_after_save` <a href="#notification_after_save" id="notification_after_save" class="docs-anchor">#</a>
	* Called after saving a notification.
	* Contains these parameters:
		*  `model` - the `NotificationModel`
		*  `isNew` - boolean value
*  `freeform_next_notification_before_delete` <a href="#notification_before_delete" id="notification_before_delete" class="docs-anchor">#</a>
	*  Called before deleting a notification.
	*  Contains these parameters:
		*  `model` - the `NotificationModel`
*  `freeform_next_notification_after_delete` <a href="#notification_after_delete" id="notification_after_delete" class="docs-anchor">#</a>
	*  Called after deleting a notification.
	*  Contains these parameters:
		*  `model` - the `NotificationModel`

## Hooks for Freeform Statuses <a href="#hooks-freeform-statuses" id="hooks-freeform-statuses" class="docs-anchor">#</a>

* `freeform_next_status_before_save` <a href="#status_before_save" id="status_before_save" class="docs-anchor">#</a>
	* Called before saving a status.
	* Contains these parameters:
		*  `model` - the `StatusModel`
		*  `isNew` - boolean value
* `freeform_next_status_after_save` <a href="#status_after_save" id="status_after_save" class="docs-anchor">#</a>
	* Called after saving a status.
	* Contains these parameters:
		*  `model` - the `StatusModel`
		*  `isNew` - boolean value
*  `freeform_next_status_before_delete` <a href="#status_before_delete" id="status_before_delete" class="docs-anchor">#</a>
	*  Called before deleting a status.
	*  Contains these parameters:
		*  `model` - the `StatusModel`
*  `freeform_next_status_after_delete` <a href="#status_after_delete" id="status_after_delete" class="docs-anchor">#</a>
	*  Called after deleting a status.
	*  Contains these parameters:
		*  `model` - the `StatusModel`

## Hooks for Freeform File Uploads <a href="#hooks-freeform-file-uploads" id="hooks-freeform-file-uploads" class="docs-anchor">#</a>

* `freeform_next_file_before_upload` <a href="#file_before_upload" id="file_before_upload" class="docs-anchor">#</a>
	* Called before uploading a file.
	* Contains these parameters:
		*  `field` - the `FileUploadField`
* `freeform_next_file_after_upload` <a href="#file_after_upload" id="file_after_upload" class="docs-anchor">#</a>
	* Called after uploading a file.
	* Contains these parameters:
		*  `field` - the `FileUploadField`
		*  `fileId` - integer value

## Hooks for Freeform Mailing <a href="#hooks-freeform-mailing" id="hooks-freeform-mailing" class="docs-anchor">#</a>

* `freeform_next_mailer_before_send` <a href="#mailer_before_send" id="mailer_before_send" class="docs-anchor">#</a>
	* Called before sending an email.
	* Contains these parameters:
		*  `notification` - the `NotificationModel`
		*  `submission` - the `SubmissionModel` if data is being stored for this form
* `freeform_next_mailer_after_send` <a href="#mailer_after_send" id="mailer_after_send" class="docs-anchor">#</a>
	* Called after sending an email.
	* Contains these parameters:
		*  `isSent` - boolean value
		*  `notification` - the `NotificationModel`
		*  `submission` - the `SubmissionModel` if data is being stored for this form

## Hooks for Freeform CRM Integrations <a href="#hooks-freeform-crm-integrations" id="hooks-freeform-crm-integrations" class="docs-anchor">#</a>

* `freeform_next_crm_before_save` <a href="#crm_before_save" id="crm_before_save" class="docs-anchor">#</a>
	* Called before saving an integration.
	* Contains these parameters:
		*  `model` - the `IntegrationModel`
		*  `isNew` - boolean value
* `freeform_next_crm_after_save` <a href="#crm_after_save" id="crm_after_save" class="docs-anchor">#</a>
	* Called after saving an integration.
	* Contains these parameters:
		*  `model` - the `IntegrationModel`
		*  `isNew` - boolean value
*  `freeform_next_crm_before_delete` <a href="#crm_before_delete" id="crm_before_delete" class="docs-anchor">#</a>
	*  Called before deleting an integration.
	*  Contains these parameters:
		*  `model` - the `IntegrationModel`
*  `freeform_next_crm_after_delete` <a href="#crm_after_delete" id="crm_after_delete" class="docs-anchor">#</a>
	*  Called after deleting an integration.
	*  Contains these parameters:
		*  `model` - the `IntegrationModel`

## Hooks for Freeform Mailing-List Integrations <a href="#hooks-freeform-mailing-list-integrations" id="hooks-freeform-mailing-list-integrations" class="docs-anchor">#</a>

* `freeform_next_mailing_lists_before_save` <a href="#mailing_lists_before_save" id="mailing_lists_before_save" class="docs-anchor">#</a>
	* Called before saving an integration.
	* Contains these parameters:
		*  `model` - the `IntegrationModel`
		*  `isNew` - boolean value
* `freeform_next_mailing_lists_after_save` <a href="#mailing_lists_after_save" id="mailing_lists_after_save" class="docs-anchor">#</a>
	* Called after saving an integration.
	* Contains these parameters:
		*  `model` - the `IntegrationModel`
		*  `isNew` - boolean value
*  `freeform_next_mailing_lists_before_delete` <a href="#mailing_lists_before_delete" id="mailing_lists_before_delete" class="docs-anchor">#</a>
	*  Called before deleting an integration.
	*  Contains these parameters:
		*  `model` - the `IntegrationModel`
*  `freeform_next_mailing_lists_after_delete` <a href="#mailing_lists_after_delete" id="mailing_lists_after_delete" class="docs-anchor">#</a>
	*  Called after deleting an integration.
	*  Contains these parameters:
		*  `model` - the `IntegrationModel`

### Usage instructions <a href="#hooks-freeform-events-usage" id="hooks-freeform-events-usage" class="docs-anchor">#</a>

Register to any of the hooks listed above with your extension. An example below using `freeform_next_form_before_save` hook using `form_before_save` method in our example extension object:

	<?php

    use Solspace\Addons\FreeformNext\Model\FormModel;

	class Example_ext
	{
	    // ... other code

	    /**
	     * Set all new Freeform forms to have random hash as their name and handle
	     *
	     * @param FormModel $model
	     * @param bool      $isNew
	     */
	    public function form_before_save(FormModel $model, $isNew)
	    {
	        if ($isNew) {
                // Set the form's name and handle to some random hash
                $model->name = md5(time());
                $model->handle = $model->name;
	        }

	        // the model gets saved later on, no need to call save() here
	    }
	}
