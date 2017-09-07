# Fields & Field Types

Freeform uses its own set of fields and field types. Using a predefined set of fields also gives us the control to more easily account for how each form field should be displayed in Composer's live preview, and provides a smoother experience.

**NOTE:** There is currently a limitation of 195 Freeform fields for each install of ExpressionEngine, due to the MySQL column limit, since all fields are stored in a single table. However, Freeform fields can be used across all forms, and even be relabelled for each form.

* [Fields](#fields)
* [Field Types](#field-types)
* [Field Specific Properties](#field-specific-props)

## Fields <a href="#fields" id="fields" class="docs-anchor">#</a>

Fields are global and available to all forms, but they can also be overwritten per form. This allows you to save time reusing existing fields when making other forms, but also gives you flexibility to make adjustments to them when needed. So to clarify, you can create fields with labels and options that are common to all forms, but also override those on each form. For example, if you have a field named **Cell Phone**, on the form level, you can rename the field name to **Mobile Phone**, or if you have a Checkbox Group field with options: **Option 1**, **Option 2**, and **Option 3**, you could override it to just have 2 options with values of **Option A** and **Option B**. When the fields are edited at global level (in main Fields area of Freeform control panel), your customizations per form will NOT be lost.

Fields can be created and managed in the main field creation area (**Freeform Next > Fields > New Field**) and can also be created directly within the *Composer* interface as well. Fields created here are available globally as well (they do not just exist for that form).

Some important notes:

* All field properties can be overwritten at form level inside Composer, including the field Handle.
* Once a field is created, you cannot change the field type after.
* Freeform will load fields of [Hidden](#fields-hidden) type at the beginning of the form, regardless of where they are placed in Composer layout.

[![Fields](images/cp_fields-list.png)](images/cp_fields-list.png)

[![Create a Field](images/cp_fields-create.png)](images/cp_fields-create.png)


## Field Types <a href="#field-types" id="field-types" class="docs-anchor">#</a>

The following field types are available:

* **Text** (`text`) <a href="#fields-text" id="fields-text" class="docs-anchor">#</a>
	* A simple input field.
		* Can be marked as required.
		* Can contain default text and/or placeholder.
* **Textarea** (`textarea`) <a href="#fields-textarea" id="fields-textarea" class="docs-anchor">#</a>
	* A simple multi-line input field.
		* Specify the number of rows the textarea should have.
		* Can be marked as required.
		* Can contain default text and/or placeholder.
		* Will automatically convert newlines to line breaks when outputting data in templates or email notifications.
* **Email** (`email`) <a href="#fields-email" id="fields-email" class="docs-anchor">#</a>
	* An input field that is flagged in Freeform to expect an email address value as well as possibility for receiving email notifications.
		* In the Property Editor (right column) in Composer, select a notification template if you want the email entered for this field to receive an email notification.
		* To allow sending of email notifications to more than 1 email address (e.g. in the case of a "Tell-a-Friend" type form), you can add multiple input fields, each with the name `email[]`. This approach would require that you code this part manually however.
		* Required field type if you wish for your users to receive an email notification.
		* Required field type if you're using with a Mailing List API integration.
		* Can be marked as required.
		* Can contain default text and/or placeholder.
* **Hidden** (`hidden`) <a href="#fields-hidden" id="fields-hidden" class="docs-anchor">#</a>
	* A hidden field.
		* Can only include text strings at this time (no variables allowed).
			* If you need you pass a value to your hidden field dynamically, you can do so with the `override_values` parameter, e.g. `override_values:FIELD_NAME="myvalue"`
		* Cannot be marked as required.
		* Freeform will load fields of this type at the beginning of the form, regardless of where they are placed in Composer layout.
* **Select** (`select`) <a href="#fields-select" id="fields-select" class="docs-anchor">#</a>
	* A select dropdown menu field.
		* Can specify labels (with values assumed) or labels and values (that differ).
			* To make the first option empty, use **labels and values** approach with first option having **--** or **Please select...**, etc for the label, and leave option blank.
		* Can be marked as required.
		* Can specify default option to be selected.
* **Checkbox** (`checkbox`) <a href="#fields-checkbox" id="fields-checkbox" class="docs-anchor">#</a>
	* A single checkbox field.
		* Has a default value of **Yes**, which can be overwritten with any value you want. The front end however, will always display the value as `1`, but upon submission, the value will be switched to the one you have set.
		* Can be marked as required, which would essentially require that this checkbox be checked.
		* Can be checked by default.
* **Checkbox Group** (`checkbox_group`) <a href="#fields-checkbox-group" id="fields-checkbox-group" class="docs-anchor">#</a>
	* A group of checkboxes.
		* Can specify labels (with values assumed) or labels and values (that differ).
		* Can be marked as required.
		* Can specify which (if any) options to be checked by default.
* **Radio Group** (`radio_group`) <a href="#fields-radio-group" id="fields-radio-group" class="docs-anchor">#</a>
	* A group of radio options.
		* Can specify labels (with values assumed) or labels and values (that differ).
		* Can be marked as required.
		* Can specify which (if any) option to be selected by default.
* **File Upload** (`file`) <a href="#fields-file-upload" id="fields-file-upload" class="docs-anchor">#</a>
	* A single file upload field, using [EE File Uploads](https://docs.expressionengine.com/v3/add-ons/file/file_tag.html).
		* Must have an Upload Directory Preference where the file will be uploaded to.
		* Be sure that the EE Upload Directory's *Allowed file types?* preference is set to **All file types**, even if you're only using images.
		* Define maximum file size (in KB). Default is 2048 KB (2MB). Is subject to:
			* PHP [memory_limit](http://us3.php.net/manual/en/ini.core.php#ini.memory-limit)
			* PHP [post_max_size](http://us3.php.net/manual/en/ini.core.php#ini.post-max-size)
			* PHP [upload_max_filesize](http://us3.php.net/manual/en/ini.core.php#ini.upload-max-filesize)
		* Select which file types can be uploaded.
			* Leaving all options unchecked will allow ALL file types.
		* In [multi-page forms](multi-page-forms.md), if an earlier page contains file upload field(s), files will actually be uploaded before the form is officially submitted.
			* If the form is never completed, incomplete submissions are stored for 3hrs, and then are removed (along with the file(s)) after that.
		* Can be marked as required.
* **Dynamic Recipients** (`dynamic_recipients`) <a href="#fields-dynamic-recipients" id="fields-dynamic-recipients" class="docs-anchor">#</a>
	* A select dropdown menu field that contains protected email addresses and labels for each.
		* Can be switched to Radio options at form level inside Composer.
		* Specify labels and email address values.
			* Emails are never parsed in source code (they're replaced with **0**, **1**, **2**, etc).
				* **NOTE:** When parsing this field semi-manually, be sure to use `{field:index}` to generate numeric values of options instead of `{field:value}`.

					```<select name="{field:department:handle}" type="dynamic_recipients">
					{field:department:options}
						<option value="{option:index}" {if option:checked}selected{/if}>
							{option:label}
						</option>
					{/field:department:options}
					</select>```

			* To make the first option empty, specify **--** or **Please select...**, etc for the label, and leave option blank.
		* In the Property Editor (right column) in Composer, select a notification template you want the selected recipient for this field to receive.
			* Users/groups need to have permissions access for **Email Notifications** to create new formatting templates.
		* Can be marked as required.
		* Can specify default option to be selected.
		* Currently only 1 recipient can be selected at this time.
		* Can include more than 1 of this field type in your forms, allowing for multiple sets of recipients to be notified.


## Field Specific Properties <a href="#field-specific-props" id="field-specific-props" class="docs-anchor">#</a>

* `text` <a href="#field-text" id="field-text" class="docs-anchor">#</a>
	* `placeholder` <a href="#field-text-placeholder" id="field-text-placeholder" class="docs-anchor">#</a>
* `textarea` <a href="#field-textarea" id="field-textarea" class="docs-anchor">#</a>
	* `placeholder` <a href="#field-textarea-placeholder" id="field-textarea-placeholder" class="docs-anchor">#</a>
* `select` <a href="#field-select" id="field-select" class="docs-anchor">#</a>
	* `options` <a href="#field-select-options" id="field-select-options" class="docs-anchor">#</a>
		* An array of option objects with `label` and `value` properties.
* `checkbox` <a href="#field-checkbox" id="field-checkbox" class="docs-anchor">#</a>
	* Has a default value of **Yes**, which can be overwritten with any value you want. The front end however, will always display the value as `1`, but upon submission, the value will be switched to the one you have set.
* `checkbox_group` <a href="#field-checkbox_group" id="field-checkbox_group" class="docs-anchor">#</a>
	* `options` <a href="#field-checkbox_group-options" id="field-checkbox_group-options" class="docs-anchor">#</a>
		* An array of option objects with `label` and `value` properties.
* `radio_group` <a href="#field-radio_group" id="field-radio_group" class="docs-anchor">#</a>
	* `options` <a href="#field-radio_group-options" id="field-radio_group-options" class="docs-anchor">#</a>
		* An array of option objects with `label` and `value` properties.
* `submit` <a href="#field-submit" id="field-submit" class="docs-anchor">#</a>
	* `label_next` <a href="#field-submit-label-next" id="field-submit-label-next" class="docs-anchor">#</a>
		* A label for the **Next** button. `Submit` by default.
	* `label_prev` <a href="#field-submit-label-prev" id="field-submit-label-prev" class="docs-anchor">#</a>
		* A label for the **Previous** button. `Previous` by default.
	* `disable_prev` <a href="#field-submit-disable-prev" id="field-submit-disable-prev" class="docs-anchor">#</a>
		* A boolean value. If `true` the **Previous** button should not be rendered.
* `dynamic_recipients` <a href="#field-dynamic_recipients" id="field-dynamic_recipients" class="docs-anchor">#</a>
	* `show_as_radio` <a href="#field-dynamic_recipients-show-as-radio" id="field-dynamic_recipients-show-as-radio" class="docs-anchor">#</a>
		* A boolean value. If `true` the dynamic recipients field should be rendered as radio buttons instead of a select field.
	* `notification_id` <a href="#field-dynamic_recipients-notification-id" id="field-dynamic_recipients-notification-id" class="docs-anchor">#</a>
		* The database ID of the assigned Email Notification Template.
	* **NOTE:** When parsing this field semi-manually, be sure to use `field.index` to generate numeric values of options instead of `field.value`.
* `email` <a href="#field-email" id="field-email" class="docs-anchor">#</a>
	* `placeholder` <a href="#field-email-placeholder" id="field-email-placeholder" class="docs-anchor">#</a>
	* `notification_id` <a href="#field-email-notification-id" id="field-email-notification-id" class="docs-anchor">#</a>
		* The database ID of the assigned Email Notification Template.
* `file` <a href="#field-file" id="field-file" class="docs-anchor">#</a>
	* `file_kinds` <a href="#field-file-file-kinds" id="field-file-file-kinds" class="docs-anchor">#</a>
		* An array of allowed file kinds, e.g. `image`, `document`, `audio`, etc.
	* `max_file_size_kb` <a href="#field-file-max-filesize-kb" id="field-file-max-filesize-kb" class="docs-anchor">#</a>
		* The numeric representation of the upload limit in kilobytes.
