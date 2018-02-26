# Extension Hooks

If you wish to extend the capabilities of Freeform, you can use any of the extension hooks below:

## Hooks for Freeform Forms <a href="#hooks-freeform-forms" id="hooks-freeform-forms" class="docs-anchor">#</a>

* `freeform_next_form_before_save` <a href="#hook-onbeforesave-form" id="hook-onbeforesave-form" class="docs-anchor">#</a>
	* Called before saving a form
	* Contains these parameters:
		*  `model` - the `FormModel`
		*  `isNew` - boolean value
* `freeform_next_form_after_save` <a href="#hook-onaftersave-form" id="hook-onaftersave-form" class="docs-anchor">#</a>
	* Called after saving a form
	* Contains these parameters:
		*  `model` - the `FormModel`
		*  `isNew` - boolean value
*  `freeform_next_form_before_delete` <a href="#hook-onbeforedelete-form" id="hook-onbeforedelete-form" class="docs-anchor">#</a>
	*  Called before deleting a form
	*  Contains these parameters:
		*  `model` - the `FormModel`
*  `freeform_next_form_after_delete` <a href="#hook-onafterdelete-event" id="hook-onafterdelete-form" class="docs-anchor">#</a>
	*  Called after deleting a form
	*  Contains these parameters:
		*  `model` - the `FormModel`

## Hooks for Freeform Submissions <a href="#hooks-freeform-submissions" id="hooks-freeform-submissions" class="docs-anchor">#</a>

* `freeform_next_submission_before_save` <a href="#hook-onbeforesave-submission" id="hook-onbeforesave-submission" class="docs-anchor">#</a>
	* Called before saving a submission
	* Contains these parameters:
		*  `model` - the `SubmissionModel`
		*  `isNew` - boolean value
* `freeform_next_submission_after_save` <a href="#hook-onaftersave-submission" id="hook-onaftersave-submission" class="docs-anchor">#</a>
	* Called after saving a submission
	* Contains these parameters:
		*  `model` - the `SubmissionModel`
		*  `isNew` - boolean value
*  `freeform_next_submission_before_delete` <a href="#hook-onbeforedelete-submission" id="hook-onbeforedelete-submission" class="docs-anchor">#</a>
	*  Called before deleting a submission
	*  Contains these parameters:
		*  `model` - the `SubmissionModel`
*  `freeform_next_submission_after_delete` <a href="#hook-onafterdelete-submission" id="hook-onafterdelete-submission" class="docs-anchor">#</a>
	*  Called after deleting a submission
	*  Contains these parameters:
		*  `model` - the `SubmissionModel`

## Hooks for Freeform Fields <a href="#hooks-freeform-fields" id="hooks-freeform-fields" class="docs-anchor">#</a>

* `freeform_next_field_before_save` <a href="#hook-onbeforesave-fields" id="hook-onbeforesave-fields" class="docs-anchor">#</a>
	* Called before saving a field
	* Contains these parameters:
		*  `model` - the `FieldModel`
		*  `isNew` - boolean value
* `freeform_next_field_after_save` <a href="#hook-onaftersave-fields" id="hook-onaftersave-fields" class="docs-anchor">#</a>
	* Called after saving a field
	* Contains these parameters:
		*  `model` - the `FieldModel`
		*  `isNew` - boolean value
*  `freeform_next_field_before_delete` <a href="#hook-onbeforedelete-fields" id="hook-onbeforedelete-fields" class="docs-anchor">#</a>
	*  Called before deleting a field
	*  Contains these parameters:
		*  `model` - the `FieldModel`
*  `freeform_next_field_after_delete` <a href="#hook-onafterdelete-fields" id="hook-onafterdelete-fields" class="docs-anchor">#</a>
	*  Called after deleting a field
	*  Contains these parameters:
		*  `model` - the `FieldModel`

## Hooks for Freeform Notifications <a href="#hooks-freeform-notifications" id="hooks-freeform-notifications" class="docs-anchor">#</a>

* `freeform_next_notification_before_save` <a href="#hook-onbeforesave-notifications" id="hook-onbeforesave-notifications" class="docs-anchor">#</a>
	* Called before saving a notification
	* Contains these parameters:
		*  `model` - the `NotificationModel`
		*  `isNew` - boolean value
* `freeform_next_notification_after_save` <a href="#hook-onaftersave-notifications" id="hook-onaftersave-notifications" class="docs-anchor">#</a>
	* Called after saving a notification
	* Contains these parameters:
		*  `model` - the `NotificationModel`
		*  `isNew` - boolean value
*  `freeform_next_notification_before_delete` <a href="#hook-onbeforedelete-notifications" id="hook-onbeforedelete-notifications" class="docs-anchor">#</a>
	*  Called before deleting a notification
	*  Contains these parameters:
		*  `model` - the `NotificationModel`
*  `freeform_next_notification_after_delete` <a href="#hook-onafterdelete-notifications" id="hook-onafterdelete-notifications" class="docs-anchor">#</a>
	*  Called after deleting a notification
	*  Contains these parameters:
		*  `model` - the `NotificationModel`

## Hooks for Freeform Statuses <a href="#hooks-freeform-statuses" id="hooks-freeform-statuses" class="docs-anchor">#</a>

* `freeform_next_status_before_save` <a href="#hook-onbeforesave-statuses" id="hook-onbeforesave-statuses" class="docs-anchor">#</a>
	* Called before saving a status
	* Contains these parameters:
		*  `model` - the `StatusModel`
		*  `isNew` - boolean value
* `freeform_next_status_after_save` <a href="#hook-onaftersave-statuses" id="hook-onaftersave-statuses" class="docs-anchor">#</a>
	* Called after saving a status
	* Contains these parameters:
		*  `model` - the `StatusModel`
		*  `isNew` - boolean value
*  `freeform_next_status_before_delete` <a href="#hook-onbeforedelete-statuses" id="hook-onbeforedelete-statuses" class="docs-anchor">#</a>
	*  Called before deleting a status
	*  Contains these parameters:
		*  `model` - the `StatusModel`
*  `freeform_next_status_after_delete` <a href="#hook-onafterdelete-statuses" id="hook-onafterdelete-statuses" class="docs-anchor">#</a>
	*  Called after deleting a status
	*  Contains these parameters:
		*  `model` - the `StatusModel`

## Hooks for Freeform File Uploads <a href="#hooks-freeform-file-uploads" id="hooks-freeform-file-uploads" class="docs-anchor">#</a>

* `freeform_next_file_before_upload` <a href="#hook-onbeforeupload-file-uploads" id="hook-onbeforeupload-file-uploads" class="docs-anchor">#</a>
	* Called before uploading a file
	* Contains these parameters:
		*  `field` - the `FileUploadField`
* `freeform_next_file_after_upload` <a href="#hook-onafterupload-file-uploads" id="hook-onafterupload-file-uploads" class="docs-anchor">#</a>
	* Called after uploading a file
	* Contains these parameters:
		*  `field` - the `FileUploadField`
		*  `fileId` - integer value

## Hooks for Freeform Mailing <a href="#hooks-freeform-mailing" id="hooks-freeform-mailing" class="docs-anchor">#</a>

* `freeform_next_mailer_before_send` <a href="#hook-onbeforesend-mailing" id="hook-onbeforesend-mailing" class="docs-anchor">#</a>
	* Called before sending an email
	* Contains these parameters:
		*  `notification` - the `NotificationModel`
		*  `submission` - the `SubmissionModel` if data is being stored for this form
* `freeform_next_mailer_after_send` <a href="#hook-onaftersend-mailing" id="hook-onaftersend-mailing" class="docs-anchor">#</a>
	* Called after sending an email
	* Contains these parameters:
		*  `isSent` - boolean value
		*  `notification` - the `NotificationModel`
		*  `submission` - the `SubmissionModel` if data is being stored for this form

## Hooks for Freeform CRM Integrations <a href="#hooks-freeform-crm-integrations" id="hooks-freeform-crm-integrations" class="docs-anchor">#</a>

* `freeform_next_crm_before_save` <a href="#hook-onbeforesave-crm-integrations" id="hook-onbeforesave-crm-integrations" class="docs-anchor">#</a>
	* Called before saving an integration
	* Contains these parameters:
		*  `model` - the `IntegrationModel`
		*  `isNew` - boolean value
* `freeform_next_crm_after_save` <a href="#hook-onaftersave-crm-integrations" id="hook-onaftersave-crm-integrations" class="docs-anchor">#</a>
	* Called after saving an integration
	* Contains these parameters:
		*  `model` - the `IntegrationModel`
		*  `isNew` - boolean value
*  `freeform_next_crm_before_delete` <a href="#hook-onbeforedelete-crm-integrations" id="hook-onbeforedelete-crm-integrations" class="docs-anchor">#</a>
	*  Called before deleting an integration
	*  Contains these parameters:
		*  `model` - the `IntegrationModel`
*  `freeform_next_crm_after_delete` <a href="#hook-onafterdelete-crm-integrations" id="hook-onafterdelete-crm-integrations" class="docs-anchor">#</a>
	*  Called after deleting an integration
	*  Contains these parameters:
		*  `model` - the `IntegrationModel`

## Hooks for Freeform Mailing-List Integrations <a href="#hooks-freeform-mailing-list-integrations" id="hooks-freeform-mailing-list-integrations" class="docs-anchor">#</a>

* `freeform_next_mailing_lists_before_save` <a href="#hook-onbeforesave-mailing-list-integrations" id="hook-onbeforesave-mailing-list-integrations" class="docs-anchor">#</a>
	* Called before saving an integration
	* Contains these parameters:
		*  `model` - the `IntegrationModel`
		*  `isNew` - boolean value
* `freeform_next_mailing_lists_after_save` <a href="#hook-onaftersave-mailing-list-integrations" id="hook-onaftersave-mailing-list-integrations" class="docs-anchor">#</a>
	* Called after saving an integration
	* Contains these parameters:
		*  `model` - the `IntegrationModel`
		*  `isNew` - boolean value
*  `freeform_next_mailing_lists_before_delete` <a href="#hook-onbeforedelete-mailing-list-integrations" id="hook-onbeforedelete-mailing-list-integrations" class="docs-anchor">#</a>
	*  Called before deleting an integration
	*  Contains these parameters:
		*  `model` - the `IntegrationModel`
*  `freeform_next_mailing_lists_after_delete` <a href="#hook-onafterdelete-mailing-list-integrations" id="hook-onafterdelete-mailing-list-integrations" class="docs-anchor">#</a>
	*  Called after deleting an integration
	*  Contains these parameters:
		*  `model` - the `IntegrationModel`

### Usage instructions <a href="#hooks-freeform-events-usage" id="hooks-freeform-events-usage" class="docs-anchor">#</a>

Register to any of the hooks listed above with your extension.  
An example below using `freeform_next_form_before_save` hook using `form_before_save` method in our example extension object:

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
