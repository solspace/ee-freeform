# Email Notifications

Freeform allows you to send email notifications upon submittal of a form. They are global and can be reused for multiple forms, saving you time when you are managing many forms.

* [Types of Email Notifications](#email-notification-types)
* [Overview of Email Notification Template Options](#notification-template-options)
* [Managing Email Templates within EE CP (Database)](#notification-template-database)
* [Managing Email Templates as HTML Files](#notification-template-files)
* [Template Examples](#examples)


## Types of Email Notifications <a href="#email-notification-types" id="email-notification-types" class="docs-anchor">#</a>

Freeform allows you to send email notifications 5 different ways (all of them each being able to have their own notification templates, etc):

* [Admin Notifications](#type-admin)
* [Dynamic Recipients](#type-dynamic-recipients)
* [Dynamic Template Notifications](#type-dynamic-template)
* [User/Submitter Notifications](#type-user-submitter)
* [User Defined](#type-user-defined)


### Admin Notifications <a href="#type-admin" id="type-admin" class="docs-anchor">#</a>

Email notifications can be sent to one or more admin email addresses. To setup:

1. In Composer interface for the form, click on the Admin Notifications (envelope icon) tab at the top right.
2. Select and/or add an email template.
3. Specify admin email address(es) in the text area below. Separated multiples by line breaks only.

### Dynamic Recipients <a href="#type-dynamic-recipients" id="type-dynamic-recipients" class="docs-anchor">#</a>

Email notifications can be sent to one or more pre-defined admin email addresses that are selected by the user filling out the form using the [Dynamic Recipients](fields-field-types.md#fields-dynamic-recipients) field. For example, you might have a select dropdown field that contains different departments for the user to address the email to. To setup:

1. In Field Editor (**Freeform -> Fields**) or Composer Quick Field (**Add New Field** button at left), create a new field of the *Dynamic Recipient* field type.
2. In Composer interface for the form, drag that field into field layout.
3. Click on field inside field layout and look over to the Property Editor (right column).
4. Select and/or add an email template.
5. Choose how you wish to display the field (Select, Radios, Checkboxes).
6. Specify email addresses (and corresponding labels) for each option you wish to make available for users to select.
	* You can specify more than 1 email address per option - just separate multiples with comma (`,` no space).
	* Email addresses will NOT be rendered in the front end form, but rather a corresponding ID value will show up.

> **NOTE:** You currently cannot specify more than 1 option with the same email address. It will appear to display somewhat correctly, but you'll notice some odd behaviors when the user submits the form. The only workaround for this currently is to create email address aliases for each duplicate option.

### Dynamic Template Notifications <a href="#type-dynamic-template" id="type-dynamic-template" class="docs-anchor">#</a>

Email notifications can be setup dynamically at template level using the [dynamic_notification_recipients](form.md#param-dynamicnotificationrecipients) parameter in your template. This allows you to hard code values or dynamically pass a value from another element such as a Channel Entry. To setup:

1. In your EE template, add the following parameters to your [Freeform_Next:Form](form.md) template tag:
`dynamic_notification_recipients="admin@example.com|support@example.com"`
`dynamic_notification_template="test.html"`

* For Database entry based templates, specify the handle for `template`.
* For Twig file based templates, specify the full file name including **.html** for `template`.

> **NOTE:** This feature uses Session data. It will likely not work properly if the page is cached with something like Varnish, etc.

### User/Submitter Notifications <a href="#type-user-submitter" id="type-user-submitter" class="docs-anchor">#</a>

Email notifications can be sent to the user submitting the form using the [Email](fields-field-types.md#fields-email) field type. This is often used to send an email confirmation for the user. To setup:

1. In Field Editor (**Freeform -> Fields**) or Composer Quick Field (**Add New Field** button at left), create a new field of the *Email* field type.
2. In Composer interface for the form, drag that field into field layout.
3. Click on field inside field layout and look over to the Property Editor (right column).
4. Select and/or add an email template.

When the form submitter enters their email address in this field, Freeform will use that email address to send the email notification to.

### User Defined <a href="#type-user-defined" id="type-user-defined" class="docs-anchor">#</a>

Email notifications can be sent to email addresses of the submitters choosing using the [Email](fields-field-types.md#fields-email) field type. This would be commonly used for "tell-a-friend" type forms, or forms to send out any other type of invites. The user submitting the form would enter email address(es) in the form and Freeform can send an email notification to them. This essentially works the same way as [User/Submitter Notifications](#type-user-submitter). Just be careful as this could be abused by spammers. To setup:

1. In Field Editor (**Freeform -> Fields**) or Composer Quick Field (**Add New Field** button at left), create a new field of the *Email* field type.
2. In Composer interface for the form, drag that field into field layout.
3. Click on field inside field layout and look over to the Property Editor (right column).
4. Select and/or add an email template.

To allow sending of email notifications to more than 1 email address (e.g. in the case of a "tell-a-friend" type form), you will need to add multiple input fields, each with the input name `email[]`. This approach would require that you code this part manually however.

> **NOTE:** This feature could be abused by spammers.


## Overview of Email Notification Template Options <a href="#notification-template-options" id="notification-template-options" class="docs-anchor">#</a>

[![Email Notifications List](images/cp_notifications-list.png)](images/cp_notifications-list.png)

Email notification templates can be managed 2 different ways:

1. As *[database entries](#notification-template-database)* within the EE control panel in the **Notifications** page in Freeform (**Freeform > Notifications**).
2. As *[HTML template files](#notification-template-files)* within the EE Templates directory.

In addition to this, email notification templates can be created directly at form level within Composer. Email templates created this way are subject to the [Default Email Notification Creation Method](settings.md#default-email-method) preference in Freeform settings. Email notification templates that are created within Composer will contain basic default content and should be checked and updated once finished building your form.


## Managing Email Templates within EE CP (Database) <a href="#notification-template-database" id="notification-template-database" class="docs-anchor">#</a>

[![Create an Email Notification](images/cp_notifications-create.png)](images/cp_notifications-create.png)

Database templates are managed within the EE control panel in the **Notifications** page in Freeform (**Freeform > Notifications**). Email Notification templates contain the following options:

* **Name** <a href="#name" id="name" class="docs-anchor">#</a>
	* A common name for the notification template to identify it easier.
* **Handle** <a href="#handle" id="handle" class="docs-anchor">#</a>
	* The unique identifier for the notification template, used when in your regular templates when specifying a notification template.
* **Description** <a href="#description" id="description" class="docs-anchor">#</a>
	* A description for the notification template to help identify what it's used for, etc.
* **Subject** <a href="#subject" id="subject" class="docs-anchor">#</a>
	* The subject line for the email notification.
		* Can include any Freeform field variables (`{field_name}`) as well as `{submission:id}` and `{form:name}` (where `{form:name}` is the name of the form, not a custom field).
* **From Email** <a href="#from-email" id="from-email" class="docs-anchor">#</a>
	* The email address the email notification will appear from.
		* Can include any Freeform field variable (`{field_name}`).
			* **NOTE:** Using dynamic approach with a variable could have your emails marked as spam.
* **From Name** <a href="#from-name" id="from-name" class="docs-anchor">#</a>
	* The email address the email notification will appear from.
		* Can include any Freeform field variables (`{field_name}`).
			* Ex: `{first_name} {last_name}`
			* **NOTE:** Using dynamic approach with variables could have your emails marked as spam.
* **Reply-to Email** <a href="#replyto-email" id="replyto-email" class="docs-anchor">#</a>
	* The email address the email notification will has set for Reply-to.
		* Can include any Freeform field variable (`{field_name}`).
			* **NOTE:** Using dynamic approach with a variable could have your emails marked as spam.
		* Leave blank to use the **From Email** address.
* **Include Attachments** <a href="#include-attachments" id="include-attachments" class="docs-anchor">#</a>
	* Include uploaded files as attachments in email notification.
* **Email Body** <a href="#email-body" id="email-body" class="docs-anchor">#</a>
	* The HTML body of the email notification to be sent.
		* Can include any Freeform field variable (`{field_name}`) as well as `{form:name}` (the name of the form, not a custom field), `{form:id}`, `{form:handle}`, `{submission:id}` and `{date_created format="%l, %F %j, %Y at %g:%i%a"}`.
			* Available field options:
				* `{field:field_name:label}` - displays the label (name) of the field.
				* `{field:field_name:value}` - displays the option label(s) submitted.
					* Example: `Apples`
					* Array of data example: `Apples, Oranges`
				* `{field:field_name:option_value}` - displays the option value(s) submitted for multi-option fields (checkbox groups, radio groups, select fields).
					* Example: `CA`
					* Array of data example: `CA, ID, NY`
				* `{field:field_name:handle}` - displays the handle of the field.
				* `{field:field_name:type}` - displays the type of field it is.
			*  <a id="email-body-file-uploads" class="docs-anchor"></a>For displaying URL's to uploaded files, you'll want to pair this with the [EE File Entries tag](https://docs.expressionengine.com/v3/add-ons/file/file_tag.html). The file upload field value will parse as the uploaded file ID, which can then be fed to the `{exp:file:entries}` template tag. A final solution might look something like this:

				```{if my_file_upload_field}
					{exp:file:entries file_id="{my_file_upload_field}"}
						{file_url}
					{/exp:file:entries}
				{/if}```

		* Can also use `{form:fields}{/form:fields}` variable pair to automate parsing of fields.
			* Will only parse fields that contain data.
				* File upload fields will be excluded from this, but can be displayed manually. See [example above](#email-body-file-uploads).
			* Fields will be displayed in order of how they are laid out in Composer.
			* Available fields:
				* `{field:label}` - displays the label (name) of the field.
				* `{field:value}` - displays the option label(s) submitted.
					* Example: `Apples`
					* Array of data example: `Apples, Oranges`
				* `{field:option_value}` - displays the option value(s) submitted for multi-option fields (checkbox groups, radio groups, select fields).
					* Example: `CA`
					* Array of data example: `CA, ID, NY`
				* `{field:handle}` - displays the handle of the field.
				* `{field:type}` - displays the type of field it is.
					* Likely used as a conditional which can be helpful for displaying different formatting for certain field types.


## Managing Email Templates as HTML Files <a href="#notification-template-files" id="notification-template-files" class="docs-anchor">#</a>

HTML template files have the same options as [Database templates](#notification-template-database), but since all of the content is stored within a template (and nothing in the database), these template files will have a heading comment block of code with configuration options.

To clarify, your template code would look no different than how it does for Database template method, except you'd have a comment block at the top of the template with config options like this:

	{!-- subject: New submission from the {form:name} form --}
	{!-- fromEmail: {webmaster_email} --}
	{!-- fromName: {site_name} --}
	{!-- replyToEmail: {webmaster_email} --}
	{!-- includeAttachments: y --}
	{!-- description: A description of what this template does. --}

That is the default set of config data, but you can of course adjust it to whatever you like.


## Examples <a href="#examples" id="examples" class="docs-anchor">#</a>

Below is a basic automated example for database method:

	<h2>New submission from the {form:name} form</h2>

	<p>Submitted on: {date_created format="%l, %F %j, %Y at %g:%i%a"}</p>
	<ul>
	{form:fields}
		<li>{field:label}: {field:value}</li>
	{/form:fields}
	</ul>

And here is the same example but for HTML file method:

	{!-- subject: New submission from the {form:name} form --}
	{!-- fromEmail: {webmaster_email} --}
	{!-- fromName: {site_name} --}
	{!-- replyToEmail: {webmaster_email} --}
	{!-- includeAttachments: y --}
	{!-- description: New submission sample email template --}

	<h2>New submission from the {form:name} form</h2>

	<p>Submitted on: {date_created format="%l, %F %j, %Y at %g:%i%a"}</p>
	<ul>
	{form:fields}
		<li>{field:label}: {field:value}</li>
	{/form:fields}
	</ul>

---

Below is a manually built example for database method:

	<p>The following submission came in on {date_created format="%l, %F %j, %Y at %g:%i%a"}.</p>
	<p>Here are the details:</p>
	<ul>
		<li>Name: {first_name} {last_name}
		<li>Email: {email}
		<li>Home Phone: {home_phone}
		<li>Cell Phone: {cell_phone}
		<li>
			Services interested in:
			{interested_in}
		</li>
		<li>
			Message:<br />
			{message}
		</li>
	</ul>
