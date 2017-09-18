# Changelog

### 1.0.7 (September 18, 2017) <a href="#v1-0-7" id="v1-0-7" class="docs-anchor">#</a>
* Fixed a bug where creating a new File Upload field (Fields area) would trigger an error if a value isn't specified for Maximum Filesize.
* Fixed a bug where single Checkbox fields would always return the default value for the field in Submissions tag and email notifications, whether or not the checkbox is checked.
* Fixed a bug where quick creating fields, email templates and formatting templates in Composer would not autoload them into the list.

### 1.0.6 (August 28, 2017) <a href="#v1-0-6" id="v1-0-6" class="docs-anchor">#</a>
* Updated Composer to show hash handles for Submit and HTML blocks so they can be accessible manually in frontend templates.
* Updated textareas to now automatically convert newlines to line breaks when outputting data in templates or email notifications.
* Fixed a bug where selected values for fields with options would not output correctly.
* Fixed a bug where tabbing through labels and values in Field creation/editing area would reset values.
* Fixed a bug where the honeypot field for spam protection did not have spaces in-between attributes.
* Fixed a bug where Composer would have JS errors when creating new fields while using Sessions only in CP.

### 1.0.5 (July 25, 2017) <a href="#v1-0-5" id="v1-0-5" class="docs-anchor">#</a>
* Fixed a bug where 1.0.4 upgrade would show errors in CP for some installs.

### 1.0.4 (July 25, 2017) <a href="#v1-0-4" id="v1-0-4" class="docs-anchor">#</a>
* Added a Sandbox option for Salesforce integration in Pro version.
* Added option to use database instead of PHP sessions for storing state of form submissions.
* Fixed a bug where forms would not submit correctly for some users.
* Fixed a bug where single checkbox fields were not saving the user's selection if errors were triggered.
* Fixed a bug where single checkboxes were not displaying the field value in email notifications.
* Fixed a bug where email validation was allowing some invalid syntax.
* Fixed a bug where the Update Service would error if it couldn't connect to Solspace.com.
* Fixed a bug where the Mailer extension hooks were not working correctly.

### 1.0.3 (July 3, 2017) <a href="#v1-0-3" id="v1-0-3" class="docs-anchor">#</a>
* Fixed a bug where Freeform Updates Service was writing to add-on folder instead of EE Cache directory.
* Fixed a bug where unchecking ALL filetype options for File Upload fields in Fields area would not save preferences.
* Fixed a bug where {field_name} syntax would NOT parse in Subject/From Name/From Email/Reply-to fields when not Storing Submissions in DB.
* Fixed a bug where an error might display when viewing submissions list in the CP in some circumstances.
* Fixed a bug with MailChimp integration where it would only load a maximum of 10 MailChimp custom fields.
* Fixed a bug where orphaned mailing list setups would error in Composer.

### 1.0.2 (June 6, 2017) <a href="#v1-0-2" id="v1-0-2" class="docs-anchor">#</a>
* Fixed a bug where Composer would error and show a blank white page in some cases.
* Fixed a bug where Mailing List and CRM pages would sometimes display an error.
* Fixed a bug where the version number update process wouldn't work correctly.

### 1.0.1 (June 5, 2017) <a href="#v1-0-1" id="v1-0-1" class="docs-anchor">#</a>
* Fixed a bug where Freeform Next would not install on sites using MySQL 5.5 and lower.
* Fixed a bug where new statuses could not be created.
* Fixed a bug where some sites might see an error when attempting to create new fields.

### 1.0.0 (June 1, 2017) <a href="#v1-0-0" id="v1-0-0" class="docs-anchor">#</a>
* Initial release.
* **NOTE:** There is currently no migration path from classic Freeform/Freeform Pro to Freeform Next.
