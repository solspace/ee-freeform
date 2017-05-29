# Extension Hooks

If you wish to extend the capabilities of Freeform, you can use any of the extension hooks below:

## Events for Freeform Forms <a href="#events-freeform-forms" id="events-freeform-forms" class="docs-anchor">#</a>

* `freeform_next_form_before_save` <a href="#event-onbeforesave-form" id="event-onbeforesave-form" class="docs-anchor">#</a>
	* Called before saving a form
	* Contains these parameters:
		*  `model` - the `FormModel`
		*  `isNew` - boolean value
* `freeform_next_form_after_save` <a href="#event-onaftersave-form" id="event-onaftersave-form" class="docs-anchor">#</a>
	* Called after saving a form
	* Contains these parameters:
		*  `model` - the `FormModel`
		*  `isNew` - boolean value
*  `freeform_next_form_before_delete` <a href="#event-onbeforedelete-form" id="event-onbeforedelete-form" class="docs-anchor">#</a>
	*  Called before deleting a form
	*  Contains these parameters:
		*  `model` - the `FormModel`
*  `freeform_next_form_after_delete` <a href="#event-onafterdelete-event" id="event-onafterdelete-form" class="docs-anchor">#</a>
	*  Called after deleting a form
	*  Contains these parameters:
		*  `model` - the `FormModel`

## Events for Freeform Submissions <a href="#events-freeform-submissions" id="events-freeform-submissions" class="docs-anchor">#</a>

* `freeform_next_submission_before_save` <a href="#event-onbeforesave-submission" id="event-onbeforesave-submission" class="docs-anchor">#</a>
	* Called before saving a submission
	* Contains these parameters:
		*  `model` - the `SubmissionModel`
		*  `isNew` - boolean value
* `freeform_next_submission_after_save` <a href="#event-onaftersave-submission" id="event-onaftersave-submission" class="docs-anchor">#</a>
	* Called after saving a submission
	* Contains these parameters:
		*  `model` - the `SubmissionModel`
		*  `isNew` - boolean value
*  `freeform_next_submission_before_delete` <a href="#event-onbeforedelete-submission" id="event-onbeforedelete-submission" class="docs-anchor">#</a>
	*  Called before deleting a submission
	*  Contains these parameters:
		*  `model` - the `SubmissionModel`
*  `freeform_next_submission_after_delete` <a href="#event-onafterdelete-submission" id="event-onafterdelete-submission" class="docs-anchor">#</a>
	*  Called after deleting a submission
	*  Contains these parameters:
		*  `model` - the `SubmissionModel`

## Events for Freeform Fields <a href="#events-freeform-fields" id="events-freeform-fields" class="docs-anchor">#</a>

* `freeform_next_field_before_save` <a href="#event-onbeforesave-fields" id="event-onbeforesave-fields" class="docs-anchor">#</a>
	* Called before saving a field
	* Contains these parameters:
		*  `model` - the `FieldModel`
		*  `isNew` - boolean value
* `freeform_next_field_after_save` <a href="#event-onaftersave-fields" id="event-onaftersave-fields" class="docs-anchor">#</a>
	* Called after saving a field
	* Contains these parameters:
		*  `model` - the `FieldModel`
		*  `isNew` - boolean value
*  `freeform_next_field_before_delete` <a href="#event-onbeforedelete-fields" id="event-onbeforedelete-fields" class="docs-anchor">#</a>
	*  Called before deleting a field
	*  Contains these parameters:
		*  `model` - the `FieldModel`
*  `freeform_next_field_after_delete` <a href="#event-onafterdelete-fields" id="event-onafterdelete-fields" class="docs-anchor">#</a>
	*  Called after deleting a field
	*  Contains these parameters:
		*  `model` - the `FieldModel`

## Events for Freeform Notifications <a href="#events-freeform-notifications" id="events-freeform-notifications" class="docs-anchor">#</a>

* `freeform_next_notification_before_save` <a href="#event-onbeforesave-notifications" id="event-onbeforesave-notifications" class="docs-anchor">#</a>
	* Called before saving a notification
	* Contains these parameters:
		*  `model` - the `NotificationModel`
		*  `isNew` - boolean value
* `freeform_next_notification_after_save` <a href="#event-onaftersave-notifications" id="event-onaftersave-notifications" class="docs-anchor">#</a>
	* Called after saving a notification
	* Contains these parameters:
		*  `model` - the `NotificationModel`
		*  `isNew` - boolean value
*  `freeform_next_notification_before_delete` <a href="#event-onbeforedelete-notifications" id="event-onbeforedelete-notifications" class="docs-anchor">#</a>
	*  Called before deleting a notification
	*  Contains these parameters:
		*  `model` - the `NotificationModel`
*  `freeform_next_notification_after_delete` <a href="#event-onafterdelete-notifications" id="event-onafterdelete-notifications" class="docs-anchor">#</a>
	*  Called after deleting a notification
	*  Contains these parameters:
		*  `model` - the `NotificationModel`

## Events for Freeform Statuses <a href="#events-freeform-statuses" id="events-freeform-statuses" class="docs-anchor">#</a>

* `freeform_next_status_before_save` <a href="#event-onbeforesave-statuses" id="event-onbeforesave-statuses" class="docs-anchor">#</a>
	* Called before saving a status
	* Contains these parameters:
		*  `model` - the `StatusModel`
		*  `isNew` - boolean value
* `freeform_next_status_after_save` <a href="#event-onaftersave-statuses" id="event-onaftersave-statuses" class="docs-anchor">#</a>
	* Called after saving a status
	* Contains these parameters:
		*  `model` - the `StatusModel`
		*  `isNew` - boolean value
*  `freeform_next_status_before_delete` <a href="#event-onbeforedelete-statuses" id="event-onbeforedelete-statuses" class="docs-anchor">#</a>
	*  Called before deleting a status
	*  Contains these parameters:
		*  `model` - the `StatusModel`
*  `freeform_next_status_after_delete` <a href="#event-onafterdelete-statuses" id="event-onafterdelete-statuses" class="docs-anchor">#</a>
	*  Called after deleting a status
	*  Contains these parameters:
		*  `model` - the `StatusModel`

## Events for Freeform File Uploads <a href="#events-freeform-file-uploads" id="events-freeform-file-uploads" class="docs-anchor">#</a>

* `freeform_next_file_before_upload` <a href="#event-onbeforeupload-file-uploads" id="event-onbeforeupload-file-uploads" class="docs-anchor">#</a>
	* Called before uploading a file
	* Contains these parameters:
		*  `field` - the `FileUploadField`
* `freeform_next_file_after_upload` <a href="#event-onafterupload-file-uploads" id="event-onafterupload-file-uploads" class="docs-anchor">#</a>
	* Called after uploading a file
	* Contains these parameters:
		*  `field` - the `FileUploadField`
		*  `fileId` - integer value

## Events for Freeform Mailing <a href="#events-freeform-mailing" id="events-freeform-mailing" class="docs-anchor">#</a>

* `freeform_next_mailer_before_send` <a href="#event-onbeforesend-mailing" id="event-onbeforesend-mailing" class="docs-anchor">#</a>
	* Called before sending an email
	* Contains these parameters:
		*  `model` - the `EmailModel`
* `freeform_next_mailer_after_send` <a href="#event-onaftersend-mailing" id="event-onaftersend-mailing" class="docs-anchor">#</a>
	* Called after sending an email
	* Contains these parameters:
		*  `model` - the `EmailModel`
		*  `isSent` - boolean value

## Events for Freeform CRM Integrations <a href="#events-freeform-crm-integrations" id="events-freeform-crm-integrations" class="docs-anchor">#</a>

* `freeform_next_crm_before_save` <a href="#event-onbeforesave-crm-integrations" id="event-onbeforesave-crm-integrations" class="docs-anchor">#</a>
	* Called before saving an integration
	* Contains these parameters:
		*  `model` - the `IntegrationModel`
		*  `isNew` - boolean value
* `freeform_next_crm_after_save` <a href="#event-onaftersave-crm-integrations" id="event-onaftersave-crm-integrations" class="docs-anchor">#</a>
	* Called after saving an integration
	* Contains these parameters:
		*  `model` - the `IntegrationModel`
		*  `isNew` - boolean value
*  `freeform_next_crm_before_delete` <a href="#event-onbeforedelete-crm-integrations" id="event-onbeforedelete-crm-integrations" class="docs-anchor">#</a>
	*  Called before deleting an integration
	*  Contains these parameters:
		*  `model` - the `IntegrationModel`
*  `freeform_next_crm_after_delete` <a href="#event-onafterdelete-crm-integrations" id="event-onafterdelete-crm-integrations" class="docs-anchor">#</a>
	*  Called after deleting an integration
	*  Contains these parameters:
		*  `model` - the `IntegrationModel`

## Events for Freeform Mailing-List Integrations <a href="#events-freeform-mailing-list-integrations" id="events-freeform-mailing-list-integrations" class="docs-anchor">#</a>

* `freeform_next_mailing_lists_before_save` <a href="#event-onbeforesave-mailing-list-integrations" id="event-onbeforesave-mailing-list-integrations" class="docs-anchor">#</a>
	* Called before saving an integration
	* Contains these parameters:
		*  `model` - the `IntegrationModel`
		*  `isNew` - boolean value
* `freeform_next_mailing_lists_after_save` <a href="#event-onaftersave-mailing-list-integrations" id="event-onaftersave-mailing-list-integrations" class="docs-anchor">#</a>
	* Called after saving an integration
	* Contains these parameters:
		*  `model` - the `IntegrationModel`
		*  `isNew` - boolean value
*  `freeform_next_mailing_lists_before_delete` <a href="#event-onbeforedelete-mailing-list-integrations" id="event-onbeforedelete-mailing-list-integrations" class="docs-anchor">#</a>
	*  Called before deleting an integration
	*  Contains these parameters:
		*  `model` - the `IntegrationModel`
*  `freeform_next_mailing_lists_after_delete` <a href="#event-onafterdelete-mailing-list-integrations" id="event-onafterdelete-mailing-list-integrations" class="docs-anchor">#</a>
	*  Called after deleting an integration
	*  Contains these parameters:
		*  `model` - the `IntegrationModel`

### Usage instructions <a href="#events-freeform-events-usage" id="events-freeform-events-usage" class="docs-anchor">#</a>

Register to any of the hooks listed above with your extension.  
An example below using `freeform_next_form_before_save` hook using `form_before_save` method in our example extension object:

	<?php

    use Solspace\Addons\FreeformNext\Model\FormModel;

	class Example_ext
	{
	    // ... other code
	    
	    /**
	     * Set all new Freeform Next forms to have random hash as their name and handle 
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
