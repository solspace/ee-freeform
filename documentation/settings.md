# Settings

Freeform includes several settings that allow you to customize your form management experience. To adjust your settings, click the **Settings** menu item while in the Freeform add-on, or go to **CP > Developer > Add-On Manager > Freeform > Settings**.

[![General Settings](images/cp_settings-general.png)](images/cp_settings-general.png)

The settings allow you to adjust:

* *License* <a href="#license" id="license" class="docs-anchor">#</a>
	* **License key** <a href="#license-key" id="license-key" class="docs-anchor">#</a>
		* This isn't a required field, but it allows you to keep track of your licenses easier. Simply enter the Freeform license key you received here.
* *General* <a href="#general" id="general" class="docs-anchor">#</a>
	* **Spam Protection** <a href="#spam-protection" id="spam-protection" class="docs-anchor">#</a>
		* Freeform includes its own Javascript-based honeypot spam protection. This is enabled by default, but can be disabled here.
		* For more information, visit the [Spam Protection documentation](spam-protection.md).
	* **Spam protection simulates a successful submission?** <a href="#spam-simulate-success" id="spam-simulate-success" class="docs-anchor">#</a>
		* Enable this to change the spam protection behavior to simulate a successful submission instead of just reloading the form.
	* **Session Storage Mechanism** <a href="#session-storage" id="session-storage" class="docs-anchor">#</a>
		* Choose the mechanism with which session data is stored on front end submissions:
			* **PHP Sessions** (default)
			* **Database**
	* **Display Order of Fields in Composer** <a href="#display-order" id="display-order" class="docs-anchor">#</a>
		* This setting allows you to set the display order for the list of available fields in Composer.
	* **Include Default Freeform Formatting Templates** <a href="#include-default-templates" id="include-default-templates" class="docs-anchor">#</a>
		* Disable this to hide the default Freeform formatting templates in the Formatting Template options list inside Composer.
	* **Remove Newlines from Textareas for Exporting** <a href="#remove-newlines-exporting" id="remove-newlines-exporting" class="docs-anchor">#</a>
		* Enable this to have newlines removed from Textarea fields in submissions when exporting.
	* **Disable Submit Button on Form Submit?** <a href="#disable-submit" id="disable-submit" class="docs-anchor">#</a>
		* Enable this to automatically disable the form's submit button when the user submits the form. This will prevent the form from double-submitting.
	* **Automatically Scroll to Form on Errors and Multipage forms?** <a href="#auto-scroll" id="auto-scroll" class="docs-anchor">#</a>
		* Enable this to have Freeform use JS to automatically scroll the page down to the form upon submit when there are errors or the form is continuing to the next page in multipage forms.
* *Formatting Templates* <a href="#formatting-templates" id="formatting-templates" class="docs-anchor">#</a>
	* **Directory Path** <a href="#formatting-directory-path" id="formatting-directory-path" class="docs-anchor">#</a>
		* When using custom formatting templates for your forms, you'll need to specify where your HTML templates are stored.
		* Provide a relative path from the EE `/system/user/templates/` directory, or full path to the folder where your custom formatting templates directory is. Examples:
		* `/home/username/www/public_html/freeform-formatting`
		* `/home/username/www/system/user/templates/freeform-formatting`
		* `freeform-formatting/`
		* To add a starter example template, click the "Add a sample template" button, and then edit the template after.

[![Formatting Templates](images/cp_settings-formatting-templates.png)](images/cp_settings-formatting-templates.png)

* *Email Templates* <a href="#email-templates" id="email-templates" class="docs-anchor">#</a>
	* This area is for users that wish to use HTML template files for email notifications. See [Email Notifications](email-notifications.md) documentation for more information about implementation.
	* **Directory Path** <a href="#email-directory-path" id="email-directory-path" class="docs-anchor">#</a>
		* Provide a relative path from the EE `/system/user/templates/` directory, or full path to the folder where your Freeform email notifications templates directory is. Examples:
			* `/home/username/www/public_html/freeform-emails`
			* `/home/username/www/system/user/templates/freeform-emails`
			* `freeform-emails/`
		* To add a starter example template, click the "Add a sample template" button, and then edit the template after.
	* **Default Email Notification Creation Method** <a href="#default-email-method" id="default-email-method" class="docs-anchor">#</a>
		* Select which storage method to use when creating new email notifications with **Add New Notification** option in Composer.
			* **Database Entry** - Use CP-based database template editor.
			* **Template File** - Use HTML template files.

[![Email Templates](images/cp_settings-notification-templates.png)](images/cp_settings-notification-templates.png)

* *Statuses* <a href="#statuses" id="statuses" class="docs-anchor">#</a>
	* This area allows you to manage and create new statuses for your forms.
		* You can set the default status to be set for all forms here.
		* Ability to add/edit/remove statuses is not available in the *Freeform Express* edition.

[![Statuses](images/cp_settings-statuses-list.png)](images/cp_settings-statuses-list.png)

[![Create a Status](images/cp_settings-statuses-create.png)](images/cp_settings-statuses-create.png)

* *Demo Templates* <a href="#demo-templates" id="demo-templates" class="docs-anchor">#</a>
	* Allows you to install the [Demo Templates](demo-templates.md) to get Freeform up and running on the front end with just a couple clicks!

* *reCAPTCHA* (Pro only) <a href="#recaptcha" id="recaptcha" class="docs-anchor">#</a>
	* **Enable reCAPTCHA** <a href="#enable-recaptcha" id="enable-recaptcha" class="docs-anchor">#</a>
		* Enable this setting and fill in the reCAPTCHA *Site Key* and *Secret Key* to enable reCAPTCHA for Freeform. Then, to add reCAPTCHA to your forms, open up and edit each form and drag over the **reCAPTCHA** special field anywhere you like into your form layout.
		* Visit [Google reCAPTCHA site](https://www.google.com/recaptcha) to register your site and get your *Site Key* and *Secret Key*.
