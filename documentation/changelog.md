# Changelog

### 1.7.8 (January 30, 2019) <a href="#v1-7-8" id="v1-7-8" class="docs-anchor">#</a>
- Fixed a bug where the 'Automatically Scroll to Form on Errors and Multipage forms?' setting was not working for multi-page form returns.
- Fixed a bug where the Freeform channel entry fieldtype could prematurely trigger submit logic in certain scenarios.

### 1.7.7 (January 21, 2019) <a href="#v1-7-7" id="v1-7-7" class="docs-anchor">#</a>
- Added 'Automatically Scroll to Form on Errors and Multipage forms?' setting to allow the ability to disable this feature.
- Updated Salesforce Lead integration to clear out all empty values before submitting to Salesforce.
- Fixed a potential XSS vulnerability.
- Fixed a bug where editing submission field layouts in Submissions CP list page were sometimes not working.
- Fixed a bug where the Submission CP list page could error if the form contained fields which had no values submitted previously.

### 1.7.6 (December 24, 2018) <a href="#v1-7-6" id="v1-7-6" class="docs-anchor">#</a>
- Updated Classic migration to maintain original submission dates of migrated submissions.
- Fixed a bug where Classic migrations could stop working in rare cases.

### 1.7.5 (December 19, 2018) <a href="#v1-7-5" id="v1-7-5" class="docs-anchor">#</a>
- Added `field_id_prefix` parameter to the Freeform:Form and Render tags to set a prefix value on field output. Helpful if you have more than 1 form on the same template and are sharing fields.
- Fixed a bug where having more than 1 form in the same template would cause one form to take over the other form if the page reloaded from errors being present.
- Fixed a bug where the JS autoscroll feature would act up if there was an error on a form and there was more than 1 form in the same page.
- Fixed a bug where using data feeders from EE Entries, etc in Freeform fields would significantly impact submit wait time for forms on front end.
- Fixed a bug where the US States predefined options list contained more than official states. Also added a States & Territories list that contains official states and territories.
- Fixed a bug where the CP submission list date filters were not including the selected end date range in results.
- Fixed a bug where the `use_action_url="yes"` parameter was not working correctly in Freeform:Form and Render tags.
- Fixed a bug where the CP submission list filters were missing some translations.

### 1.7.4 (December 3, 2018) <a href="#v1-7-4" id="v1-7-4" class="docs-anchor">#</a>
- Fixed a bug where submissions titles were not showing up correctly in some cases in CP Submissions list.

### 1.7.3 (November 30, 2018) <a href="#v1-7-3" id="v1-7-3" class="docs-anchor">#</a>
- Fixed a bug where CP Forms list, Composer and front end forms render would error when using PHP 5.4 (EE3).
- Fixed a bug where rendering a form on front end would error in PHP versions below 7.x (EE3).
- Fixed a bug where extension hooks were not being uninstalled correctly.
- Fixed a bug where CP Submissions filters were not working correctly when using Session ID.

### 1.7.2 (November 21, 2018) <a href="#v1-7-2" id="v1-7-2" class="docs-anchor">#</a>
- Added `search:field="value"` parameter to Submissions template tag to allow filtering of results by field values.
- Fixed a potential XSS vulnerability.
- Fixed a bug where some parts of Freeform would error when using PHP 5.4 (EE3).
- Fixed a bug where editing an existing field's name in Field Editor would automatically rename the handle.
- Fixed a bug where the Field Editor was displaying an extra set of Default Value and Placeholder fields in settings.

### 1.7.1 (November 7, 2018) <a href="#v1-7-1" id="v1-7-1" class="docs-anchor">#</a>
- Fixed a bug where default selections for multi-option fields populated with predefined and EE data were not respecting default option selections on front end.
- Fixed a bug where the Classic Migration would stall while migrating submissions if a form contained no submissions.
- Fixed a bug where Freeform was converting number strings to actual numbers when saving the form.
- Fixed a bug where 'reCAPTCHA' was available as a fieldtype choice (it's only available as a special field).
- Fixed a bug where the Dynamic Recipients fieldtype was parsing as `0`, `1` etc instead of the option value.
- Fixed a bug where Composer would error when using PHP 5.4 (EE3).

### 1.7.0 (November 1, 2018) <a href="#v1-7-0" id="v1-7-0" class="docs-anchor">#</a>
- Added field option Data Feeders for Checkbox group, Radio group, Select and Multi-select fieldtypes. You can now populate these fields with Entries, Categories, Members, or one of our many predefined options: States, Provinces, Countries, Languages, Number ranges, Year ranges, Months, Days and Days of the Week. Freeform Data Feeders also offer flexible control over formatting and/or which data fills option labels and option values.
- Added Multi-select fieldtype.
- Added filtering options in CP Submissions list view.
- Added reCAPTCHA support for Freeform Pro edition.
- Added dotmailer mailing list integration for Freeform Pro edition.
- Added Member Group Permissions controls - allow or deny access to various pages and parts of Freeform control panel.
- Added ability to render Dynamic Recipients as checkboxes, and now allows submitter to select more than 1 option.
- Added ability to set Mailing List fields as hidden fields (automatically opting in users).
- Added a variety of thorough AJAX examples to demo templates!
- Added support for mapping to website, URL, dropdown, radio, date and zip fields in MailChimp integration.
- Updated SharpSpring integration to work with all custom field types.
- Updated column breakpoint for Bootstrap formatting templates to be `sm` instead of `lg`.
- Updated Freeform's automatically inserted JS to no longer include `type="text/javascript"`.
- Fixed a bug where Classic Migration wouldn't work correctly in some cases.
- Fixed a bug where the `{if field:has_errors}` conditional was not working.
- Fixed a bug where an error would show if using PHP 7.2 with error debugging enabled.
- Fixed a bug where mapping to MailChimp fields named as numbers would result in an error.
- Fixed a bug where Freeform would error for PHP 5.4.
- Fixed minor visual errors throughout control panel.

### 1.6.4 (July 19, 2018) <a href="#v1-6-4" id="v1-6-4" class="docs-anchor">#</a>
- Added `date_created` variable for availability in email notification templates.
- Updated form validation to no longer allow a single space as a valid value for required fields.
- Updated HubSpot integration to load custom fields from Contacts, Companies and Deals endpoints now, not just Deals.
- Fixed a bug where the Salesforce fetch token URL regex restriction was not allowing for less common URLs to pass through.
- Fixed a bug where the 'unfinalized_files' database table was not correctly removing rows when a multi-page form was successfully submitted, causing uploaded files to be removed.
- Fixed a bug where multi-page forms were losing track of field data for autogenerated titles in form submissions.

### 1.6.3 (May 17, 2018) <a href="#v1-6-3" id="v1-6-3" class="docs-anchor">#</a>
- Added `beforePush` and `afterPush` developer hooks for API integrations.
- Added `use_action_url="yes"` parameter to Form tag as a workaround when loading a form with AJAX and submitting normally.
- Fixed a bug where the Salesforce fetch token URL regex restriction was not allowing for less common URLs to pass through.
- Fixed a bug where saving a channel entry with no Freeform form selected in the fieldtype would store a `0` instead of `NULL`.
- Fixed a bug where viewing submissions in the CP with an upload field with missing files could result in an error shown.
- Fixed a bug where the 'Express' edition check was writing files to the add-on directory and not the cache directory.
- Fixed a bug where the hidden Spam honeypot field label was missing the 'for' attribute.

### 1.6.2 (April 25, 2018) <a href="#v1-6-2" id="v1-6-2" class="docs-anchor">#</a>
- Fixed an XSS security vulnerability with submitting forms.
- Fixed a bug where a 'Handle Missing' error would display incorrectly for Mailing List fields in Composer layout.

### 1.6.1 (April 4, 2018) <a href="#v1-6-1" id="v1-6-1" class="docs-anchor">#</a>
- Updated Constant Contact API integration to no longer use OAuth and fixed a bug with it not connecting correctly.
- Fixed a bug where upgrading from Express edition to Lite/Pro edition would not install the Freeform fieldtype for channel entries.

### 1.6.0 (March 29, 2018) <a href="#v1-6-0" id="v1-6-0" class="docs-anchor">#</a>
- Added Pipedrive CRM integration for Freeform Pro edition.
- Added new settings for Salesforce CRM integration for assignment rules and accounts with custom URLs in Salesforce.
- Added a setting for spam protection that allows you to control the behavior of submit return (to simulate a successful submit).
- Added warnings in Composer to show if a field has a blank handle.
- Added Freeform fieldtype support for the Grid fieldtype.
- Added a {field:option_value} variable for multi-option field types in email notification templates (for accessing the field option value instead of option label).
- Added 'form_before_submit' and 'form_after_submit' extension hooks.
- Fixed a bug where checkbox an radio group field type option labels were not translatable.
- Fixed a bug where the File Upload fieldtype was not fully compatible with PHP 5.4.
- Fixed a bug where Freeform might conflict with other add-ons.
- Fixed a bug where the Edit Layout modal in CP Submissions list page was not loading correctly.

### 1.5.2 (March 7, 2018) <a href="#v1-5-2" id="v1-5-2" class="docs-anchor">#</a>
- Updated multi-page limit in Composer to 100 pages.
- Fixed a bug where Radio Group fields would have their last option selected no matter what, when using sample formatting templates.
- Fixed a bug where Select fields would not send data in email notifications.

### 1.5.1 (February 27, 2018) <a href="#v1-5-1" id="v1-5-1" class="docs-anchor">#</a>
- Updated File Upload fields to have the ability to accept multiple files.
- Updated Classic migration utility to migrate File Upload fields with multiple files correctly now.
- Updated Dynamic Recipients field to have the ability to accept multiple recipients.
- Updated demo templates and default formatting templates to correctly handle Dynamic Recipients field as a radio group.
- Updated Checkbox fieldtype to show a warning in Composer when no value is set.
- Fixed a bug where using Dynamic Recipients fieldtype as Radio display would not send email notifications.
- Fixed a bug where multi-option field types had ID attributes that were not unique for each option by default.

### 1.5.0 (February 9, 2018) <a href="#v1-5-0" id="v1-5-0" class="docs-anchor">#</a>
- Added Migration utility (beta) for migrating Freeform Classic data.
- Added a 'Use Double Opt-in?' setting for MailChimp integrations.
- Included an update that adjusts database key lengths of fields for compatibility with utf8mb4 character set in EE4.
- Fixed a bug where Dynamic Recipients notifications sometimes would not work correctly.
- Fixed a bug where the default value and placeholder attributes were not applied to Website fields when added to form in Composer.
- Fixed a bug where deleting fields was not removing them from forms.

### 1.4.2 (January 9, 2018) <a href="#v1-4-2" id="v1-4-2" class="docs-anchor">#</a>
- Added setting to remove newlines from textarea fields for exporting.
- Fixed a visual bug for right column buttons in Composer interface for EE3.

### 1.4.1 (December 21, 2017) <a href="#v1-4-1" id="v1-4-1" class="docs-anchor">#</a>
- Added Fluid fieldtype compatibility for the Freeform channel entry fieldtype.
- Updated Salesforce integration to work with checkbox fields.
- Fixed some visual bugs in Composer interface between EE3 and EE4 versions.
- Fixed a bug where creating new fields with options would not save any default selections.
- Fixed a bug where the toggle switch was not displaying correctly for allowing values for fieldtypes with options in EE4.

### 1.4.0 (December 20, 2017) <a href="#v1-4-0" id="v1-4-0" class="docs-anchor">#</a>
- Added 'Express' edition of Freeform, which is a lower cost option for smaller/simpler sites.
- Improved Composer interface to have sticky/locking auto-scrolling side columns. This makes searching through available fields and working with longer forms easier.

### 1.3.2 (December 18, 2017) <a href="#v1-3-2" id="v1-3-2" class="docs-anchor">#</a>
- Fixed a bug where Freeform would not install due to a 'max key length' error for some customers on EE4.
- Fixed a bug where the Spam protection feature's "count" in control panel was incrementing incorrectly with each successful submission.

### 1.3.1 (December 12, 2017) <a href="#v1-3-1" id="v1-3-1" class="docs-anchor">#</a>
- Updated the auto-scroll for error return handling to be more reliable.
- Fixed a bug where Freeform would not install and error for some customers on EE4.
- Fixed a bug where CP pages were not being styled correctly in EE4.
- Fixed a bug where Freeform was not translatable with language files.

### 1.3.0 (December 4, 2017) <a href="#v1-3-0" id="v1-3-0" class="docs-anchor">#</a>
- Updated for compatibility with both ExpressionEngine 4 and ExpressionEngine 3.
- Renamed Freeform Next and Freeform Next Pro to just Freeform Lite and Freeform Pro, respectively. Folder and namespace are still 'freeform_next' to preserve legacy with Freeform Classic.
- Fixed a bug where the automated handle generator JS would make you wrestle with it sometimes.
- Fixed a bug where Composer would choke on form handles and field handles that contained `true` or `false` strings in them.

### 1.2.2 (November 10, 2017) <a href="#v1-2-2" id="v1-2-2" class="docs-anchor">#</a>
- Added Max Length option to be set inside Composer for Text and Textarea fields.
- Updated CP views to be compatible with ExpressionEngine 4.
- Updated Date & Time field to no longer switch to being handled by browser in Mobile view due to chances of incorrect formatting.
- Fixed a bug where the Dynamic Recipients field would not remember which selection was chosen if the page reloaded after triggering an error.
- Fixed a bug where the Date & Time field date picker was not working correctly for IE 11.
- Fixed a bug where the 'instructions_below_field' parameter was not correctly accepting 'yes' and 'no'.

### 1.2.1 (November 1, 2017) <a href="#v1-2-1" id="v1-2-1" class="docs-anchor">#</a>
- Fixed a bug where Pro fieldtype fields would not show as options for mapping to API integrations.
- Fixed a bug where the SharpSpring integration would error in some cases.
- Fixed a bug where editing a field in main fields area (Freeform -> Fields) would error on the fieldtype selection.

### 1.2.0 (October 26, 2017) <a href="#v1-2-0" id="v1-2-0" class="docs-anchor">#</a>
- Added new [SharpSpring CRM](https://sharpspring.com) API integration for Pro version.
- Added auto-disable Submit button feature once a user clicks Submit (and a setting for it in control panel).
- Updated `form_attributes` parameter to allow no attribute value (to achieve `novalidate=` instead of `novalidate="true"`, etc).
- Updated form submissions in Submissions list of control panel to be ordered by newest date first as default.
- Fixed a bug where Freeform was allowing double (or more) submitting of forms if users kept clicking the Submit button while it was loading.
- Fixed a bug where instruction text would overlap the "remove" link (if long enough) for single Checkbox fields in Composer interface.

### 1.1.2 (October 18, 2017) <a href="#v1-1-2" id="v1-1-2" class="docs-anchor">#</a>
- Fixed a bug where single Checkbox fields would not remember their selection if the form errored or if the submitter went back a page in multi-page forms.
- Fixed a bug where fields with array data would display as a literal array when exporting in CSV.
- Fixed a bug where an array of data in select fields for Salesforce API integration could cause an error.

### 1.1.1 (October 6, 2017) <a href="#v1-1-1" id="v1-1-1" class="docs-anchor">#</a>
- Fixed a bug where fresh installs would error after install.
- Fixed a bug where some field specific property variables were unavailable for parsing on front end in manual implementations.

### 1.1.0 (October 3, 2017) <a href="#v1-1-0" id="v1-1-0" class="docs-anchor">#</a>
- Added new Field types for Pro version: Confirmation, Date & Time, Number, Phone, Rating, Regex, Website.
- Added advanced Quick Export and Export Profiles options in Pro version.
- Added a setting to hide Solspace default formatting templates from showing in Formatting Template dropdown list in Composer.
- Added an example formatting template and demo template for Materialize framework.
- Added form anchor to be automatically generated so inline error returns can focus down to form, if it's lower down on page.
- Updated Freeform to use EE's Email service instead of it's own.
- Fixed a bug where `{field:checked}` variable was not working for manual implementations of checkbox fields.
- Fixed a bug where date_range_start/end="" and date_range="" parameters were not working in Submissions template tag.
- Fixed a bug where status filtering would result in an error in the Submissions template tag.
- Fixed a bug where no errors would be triggered if attempting to create a blank field (no name or handle, etc).

### 1.0.7 (September 18, 2017) <a href="#v1-0-7" id="v1-0-7" class="docs-anchor">#</a>
- Fixed a bug where creating a new File Upload field (Fields area) would trigger an error if a value isn't specified for Maximum Filesize.
- Fixed a bug where single Checkbox fields would always return the default value for the field in Submissions tag and email notifications, whether or not the checkbox is checked.
- Fixed a bug where quick creating fields, email templates and formatting templates in Composer would not autoload them into the list.

### 1.0.6 (August 28, 2017) <a href="#v1-0-6" id="v1-0-6" class="docs-anchor">#</a>
- Updated Composer to show hash handles for Submit and HTML blocks so they can be accessible manually in frontend templates.
- Updated textareas to now automatically convert newlines to line breaks when outputting data in templates or email notifications.
- Fixed a bug where selected values for fields with options would not output correctly.
- Fixed a bug where tabbing through labels and values in Field creation/editing area would reset values.
- Fixed a bug where the honeypot field for spam protection did not have spaces in-between attributes.
- Fixed a bug where Composer would have JS errors when creating new fields while using Sessions only in CP.

### 1.0.5 (July 25, 2017) <a href="#v1-0-5" id="v1-0-5" class="docs-anchor">#</a>
- Fixed a bug where 1.0.4 upgrade would show errors in CP for some installs.

### 1.0.4 (July 25, 2017) <a href="#v1-0-4" id="v1-0-4" class="docs-anchor">#</a>
- Added a Sandbox option for Salesforce integration in Pro version.
- Added option to use database instead of PHP sessions for storing state of form submissions.
- Fixed a bug where forms would not submit correctly for some users.
- Fixed a bug where single checkbox fields were not saving the user's selection if errors were triggered.
- Fixed a bug where single checkboxes were not displaying the field value in email notifications.
- Fixed a bug where email validation was allowing some invalid syntax.
- Fixed a bug where the Update Service would error if it couldn't connect to Solspace.com.
- Fixed a bug where the Mailer extension hooks were not working correctly.

### 1.0.3 (July 3, 2017) <a href="#v1-0-3" id="v1-0-3" class="docs-anchor">#</a>
- Fixed a bug where Freeform Updates Service was writing to add-on folder instead of EE Cache directory.
- Fixed a bug where unchecking ALL filetype options for File Upload fields in Fields area would not save preferences.
- Fixed a bug where {field_name} syntax would NOT parse in Subject/From Name/From Email/Reply-to fields when not Storing Submissions in DB.
- Fixed a bug where an error might display when viewing submissions list in the CP in some circumstances.
- Fixed a bug with MailChimp integration where it would only load a maximum of 10 MailChimp custom fields.
- Fixed a bug where orphaned mailing list setups would error in Composer.

### 1.0.2 (June 6, 2017) <a href="#v1-0-2" id="v1-0-2" class="docs-anchor">#</a>
- Fixed a bug where Composer would error and show a blank white page in some cases.
- Fixed a bug where Mailing List and CRM pages would sometimes display an error.
- Fixed a bug where the version number update process wouldn't work correctly.

### 1.0.1 (June 5, 2017) <a href="#v1-0-1" id="v1-0-1" class="docs-anchor">#</a>
- Fixed a bug where Freeform Next would not install on sites using MySQL 5.5 and lower.
- Fixed a bug where new statuses could not be created.
- Fixed a bug where some sites might see an error when attempting to create new fields.

### 1.0.0 (June 1, 2017) <a href="#v1-0-0" id="v1-0-0" class="docs-anchor">#</a>
- Initial release.
- **NOTE:** There is currently no migration path from classic Freeform/Freeform Pro to Freeform Next.
