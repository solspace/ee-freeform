# Solspace Freeform Changelog

## 3.3.11 - 2026-11-17

### Added
- Added Salesforce Client Credentials authentication, now the default for new integrations. Existing connections retain their authentication method.
- Added control-panel inputs for Salesforce credentials.

### Fixed
- Fixed Salesforce token renewal incorrectly reporting success after authentication failures.

## 3.3.10 - 2026-08-21

### Fixed
- Fixed invalid HTML markup in honeypot field.

## 3.3.9 - 2026-08-13

### Changed
- Updated front-end components and dependencies to improve compatibility, security, and long-term stability.

## 3.3.8 - 2026-06-26

### Fixed
- Fixed an issue with file uploads on EasyApache/cPanel setups.
- Refactored internal helper logic to avoid false-positive malware warnings from Bitdefender.

## 3.3.7 - 2026-05-08

### Fixed
- Fixed a potential submit issue with forms.

## 3.3.6 - 2026-05-07

### Fixed
- Fixed a compatibility issue where third-party extensions (e.g. Snaptcha) could trigger a fatal error by calling `setMarkedAsSpam()` with an outdated method signature.
- Updated EE-specific code to use `ee('Security/XSS')->clean()` instead of the removed `ee()->security->xss_clean()` method for forward compatibility.

## 3.3.5 - 2026-02-02

### Added
- Added the ability to disable the Spam Folder.

### Fixed
- Fixed an error when a submission was flagged as spam and the form's **Store Submission Data** setting is disabled.

## 3.3.4 - 2025-11-13

### Added
- Added `{exp:freeform_next:spam}` template tag for displaying spam submissions.

### Fixed
- Fixed a bug where spam submissions were not filtered out from regular submissions in the Submissions template tag.
- Fixed an issue where CSV exports could fail or show an error on PHP 8 servers. The export process now works correctly even when a form has no header or footer data.

## 3.3.3 - 2025-11-03

### Fixed
- Fixed a PHP type error in the field search logic for the Submissions template tag.

## 3.3.2 - 2025-10-29

### Changed
- Freeform 3.3+ no longer supports PHP 7.4.

### Fixed
- Fixed compatibility issues with PHP 8.0 and 8.1.

## 3.3.1 - 2025-10-08

### Fixed
- Fixed an issue with installing and uninstalling the add-on.
- Fixed some visual and experience issues in the form builder.

## 3.3.0 - 2025-09-22

### Added
- Added a built-in **Spam Folder** to capture submissions that fail spam checks.
- Added control panel menu items for **Submissions** and **Spam**.
- Added an option to send email notifications as plain text.

### Changed
- Updated the form builder styling to better match the EE7 UI.
- Updated the **Access/Manage Submissions** permissions to include the new Submissions and Spam pages.

### Fixed
- Fixed a _duplicate dependency hash_ error in the form builder.
- Fixed PHP 8.3 and 8.4 compatibility warnings.
- Fixed a Flatpickr compatibility issue with the Mo Variables add-on.
- Fixed a migration so Freeform extension priorities are set accurately.

## 3.2.0 - 2025-06-30

### Added
- Added the ability to render form variables within the Channel:Entries tag, e.g. `{freeform_form_fieldname:name}`, etc.
- Added the ability to include `{field:max_files}`, `{field:max_filesize:kb}` and `{field:max_filesize:mb}` in File Upload fields when rendering forms.

### Fixed
- Fixed a bug where selecting the default value for entries and predefined options in fields was not working.
- Fixed a bug where **Number** fields weren't returning `number` for `{field:type}` when rendering forms.
- Refactored the `FreeformHelper.php` file to prevent being flagged as infected with a virus by _Bitdefender Antivirus_.

## 3.1.4 - 2025-04-02

### Added
- Added new Constant Contact v3 integration with OAuth2.

### Changed
- Updated the add-on icon.

## 3.1.3 - 2025-01-14

### Fixed
- Fixed a bug where the file upload preview modal for images was not working correctly.
- Fixed a bug where using the same form in a template wouldn't work with captchas. Now, it accounts for custom ID attributes when creating form anchors.

## 3.1.2 - 2024-10-15

### Fixed
- Fixed a bug that prevented reCAPTCHA v3 from working correctly when more than one form was loaded on the same page.

## 3.1.1 - 2024-09-27

### Fixed
- Fixed a bug where spam protection wasn't working correctly.

## 3.1.0 - 2024-08-24

### Added
- Added support for reCAPTCHA v3 spam protection.

## 3.0.5 - 2023-05-29

### Fixed
- Fixed several exporting issues with PHP8+.

## 3.0.4 - 2023-03-13

### Fixed
- Fixed a bug where a blank integration name would show up in the CRM service provider dropdown.

## 3.0.3 - 2023-02-16

### Fixed
- Fixed a bug where a HashIds error would display when using the Freeform field type in PHP8+.

## 3.0.2 - 2023-01-18

### Fixed
- Fixed a bug where searching within submissions in the control panel was not always working correctly.

## 3.0.1 - 2023-01-13

### Changed
- Moved vendor autoload (e.g. Guzzle) to areas where Freeform is more specifically being loaded to help prevent potential conflicts with other add-ons using a different version.
- Updated the Salesforce Lead integration to use a newer API version and improved the reliability of the Salesforce access token.

## 3.0.0 - 2022-12-13

### Changed
- Freeform 3 is no longer in beta.

## 3.0.0-b.3 - 2022-10-27

### Added
- Added a new HubSpot integration to support the new Private Key requirement.

### Fixed
- Fixed a bug where the icon for the Delete button on Export Profiles filters area was missing.

## 3.0.0-b.2 - 2022-08-29

### Fixed
- Fixed several icon and button display issues in ExpressionEngine 7.x.
- Fixed several PHP-related bugs throughout CP and front end.

## 3.0.0-b.1 - 2022-08-26

### Added
- Added and verified support for ExpressionEngine 7.x.
- Added full support for PHP 8.0 and 8.1.

### Fixed
- Fixed several PHP-related bugs throughout CP and front end.
- Fixed a bug where pagination in the CP was not working correctly.

## 2.0.2 - 2023-01-16

### Changed
- Updated the Salesforce Lead integration to use a newer API version and improved the reliability of the Salesforce access token.

### Fixed
- Fixed a bug where pagination in the CP was not working correctly.

## 2.0.1 - 2022-03-29

### Added
- Added the ability to upload files to a submission inside the CP (if that submission currently has no files).

### Fixed
- Fixed a bug where the CP and templates that load Freeform would show PHP8 warning errors.
- Fixed some bugs where some icons were not displaying in the form builder.
- Fixed a bug where Freeform was not compatible with PHP 7.0.x.

## 2.0.0 - 2021-05-27

### Changed
- Updated the Freeform honeypot to include `tabindex='-1'` on the input.
- Updated the CP Submissions index and edit pages to link file uploads to the file viewer for easy previewing of images.

### Fixed
- Fixed a bug where File field 'Edit' and 'Download' buttons were not visible on CP Submission edit pages.
- Fixed a bug where the Custom Values toggle inside the Field Create/Edit page was not working.

## 2.0.0-b.3 - 2021-02-12

### Fixed
- Fixed a compatibility issue with AJAX and PHP 7.4.
- Fixed a bug where the Freeform honeypot was still being inserted even when the 'Freeform Honeypot' toggle setting was disabled.
- Fixed a variety of icon display issues in the form builder.
- Fixed various visual errors throughout the Freeform CP and form builder.

## 2.0.0-b.2 - 2020-12-23

### Fixed
- Fixed a bug where the CP Forms and Submissions pages would error under certain scenarios.
- Fixed a bug where errors could sometimes be triggered when exporting submission data.
- Fixed a bug where default or selected values were not visually displayed correctly for Rating fields.
- Fixed a bug where the form builder 'Custom Values' checkbox for multi-option fields was not working correctly.
- Fixed various visual errors throughout the Freeform CP and form builder.

## 2.0.0-b.1 - 2020-11-27

### Added
- Added support for ExpressionEngine 6.
- Added support for the EE6 Jump Menu.
- Added support for the EE6 Menu Manager.

### Changed
- Updated all Freeform fields to now use `TEXT` for the column type in the database, thus allowing for many more fields to be created.
- Updated the built-in Honeypot feature to now have the JavaScript enhancement decoupled from it. You can now disable the JavaScript part of the Honeypot if it is causing issues for you.

## 1.8.4 - 2021-05-27

### Fixed
- Fixed a compatibility issue with AJAX and PHP 7.4.

## 1.8.3 - 2019-12-11

### Added
- Added ability to duplicate forms.

### Security
- Fixed a potential security vulnerability with submitting of forms.

## 1.8.2 - 2019-11-15

### Fixed
- Fixed a bug where the 'Automatically Scroll to Form on Errors and Multipage forms' setting was no longer working.
- Fixed a bug where data for Dynamic Recipients fields was not correctly displaying in the Submissions template tag and email notification templates.
- Fixed a bug where formatting Table field data in the Submissions template tag and email notifications was not possible.
- Fixed a bug where the 'token' parameter was not working for the Submissions template tag.
- Fixed a bug where some number field types were not showing up for the Salesforce API integration.

## 1.8.1 - 2019-09-18

### Fixed
- Fixed a bug where the Lite and Express editions were displaying errors in the control panel and forms were not submitting correctly on the front end.

## 1.8.0 - 2019-09-17

### Added
- Added Table field type, which allows users to enter repeating data rows into predefined columns (Pro).
- Added ability to edit submissions on the front end (Pro).
- Added submission tokens, allowing you to more securely display (and edit) submission data in front end templates.

### Fixed
- Fixed a bug where line returns in Textarea fields wouldn't show up correctly in front end templates.

## 1.7.13 - 2019-06-20

### Added
- Added support for Bloqs add-on.
- Added basic Portuguese and French translations files.

### Fixed
- Fixed a bug where Textarea fields were no longer properly parsing line breaks in email notifications.
- Fixed a bug where hidden fields couldn't be searched upon in the Submissions CP page.
- Fixed a bug where fields with predefined options containing numeric values were excluded from email notifications.
- Fixed a bug where the Classic migration utility would choke on form names that started with a number.
- Fixed a bug where EE data feeders were not including data from all fields when used to populate Freeform multi-option fields.
- Fixed a bug where adding a sample template to the Formatting Templates section would not work if you have a relative path set.

## 1.7.12 - 2019-04-25

### Changed
- Updated file upload field values in email notification templates to return as pipe-delimited IDs to accommodate usage with File:Entries tag.

## 1.7.11 - 2019-04-22

### Added
- Added Insightly CRM Lead integration for Freeform Pro edition.

### Security
- Fixed a potential XSS security vulnerability when displaying form submissions.

### Fixed
- Fixed a bug where the Migration utility could sometimes error.
- Fixed a bug where the Freeform fieldtype would display errors in Live Preview if no form was assigned to the entry.

## 1.7.10 - 2019-03-19

### Fixed
- Fixed a bug where the CP submissions single detail view would error if a mailing list field was set to hidden.

## 1.7.9 - 2019-03-11

### Fixed
- Fixed a bug where setting the `maxlength` option for text and textarea fields to a value inside Composer, and then clearing the value, would result in the form rendering with `maxlength` of `0`.
- Fixed a bug where form rendering was automatically inserting empty `maxlength` and `placeholder` attributes.
- Fixed a bug where Freeform Permissions did not include controls for the Email Notifications section.
- Fixed a bug where checkboxes were not showing as checked in the CP submission individual view.
- Fixed a bug where CP Submissions searching did not include field types like Hidden, Select, etc.

## 1.7.8 - 2019-01-30

### Fixed
- Fixed a bug where the 'Automatically Scroll to Form on Errors and Multipage forms?' setting was not working for multi-page form returns.
- Fixed a bug where the Freeform channel entry fieldtype could prematurely trigger submit logic in certain scenarios.

## 1.7.7 - 2019-01-21

### Added
- Added 'Automatically Scroll to Form on Errors and Multipage forms?' setting to allow the ability to disable this feature.

### Changed
- Updated Salesforce Lead integration to clear out all empty values before submitting to Salesforce.

### Security
- Fixed a potential XSS vulnerability.

### Fixed
- Fixed a bug where editing submission field layouts in the Submissions CP list page were sometimes not working.
- Fixed a bug where the Submission CP list page could error if the form contained fields that had no values submitted previously.

## 1.7.6 - 2018-12-24

### Changed
- Updated Classic migration to maintain original submission dates of migrated submissions.

### Fixed
- Fixed a bug where Classic migrations could stop working in rare cases.

## 1.7.5 - 2018-12-19

### Added
- Added `field_id_prefix` parameter to the Freeform:Form and Render tags to set a prefix value on field output. Helpful if you have more than 1 form on the same template and are sharing fields.

### Fixed
- Fixed a bug where having more than 1 form in the same template would cause one form to take over the other form if the page reloaded due to errors being present.
- Fixed a bug where the JS autoscroll feature would act up if there was an error on a form and there was more than 1 form on the same page.
- Fixed a bug where using data feeders from EE Entries, etc. in Freeform fields would significantly impact submit wait time for forms on the front end.
- Fixed a bug where the US States predefined options list contained more than official states. Also added a States & Territories list that contains official states and territories.
- Fixed a bug where the CP submission list date filters were not including the selected end date range in results.
- Fixed a bug where the `use_action_url="yes"` parameter was not working correctly in Freeform:Form and Render tags.
- Fixed a bug where the CP submission list filters were missing some translations.

## 1.7.4 - 2018-12-03

### Fixed
- Fixed a bug where submission titles were not showing up correctly in some cases in the CP Submissions list.

## 1.7.3 - 2018-11-30

### Fixed
- Fixed a bug where CP Forms list, Composer, and front end forms render would error when using PHP 5.4 (EE3).
- Fixed a bug where rendering a form on the front end would error in PHP versions below 7.x (EE3).
- Fixed a bug where extension hooks were not being uninstalled correctly.
- Fixed a bug where CP Submissions filters were not working correctly when using Session ID.

## 1.7.2 - 2018-11-21

### Added
- Added `search:field="value"` parameter to Submissions template tag to allow filtering of results by field values.

### Security
- Fixed a potential XSS vulnerability.

### Fixed
- Fixed a bug where some parts of Freeform would error when using PHP 5.4 (EE3).
- Fixed a bug where editing an existing field's name in the Field Editor would automatically rename the handle.
- Fixed a bug where the Field Editor was displaying an extra set of Default Value and Placeholder fields in settings.

## 1.7.1 - 2018-11-07

### Fixed
- Fixed a bug where default selections for multi-option fields populated with predefined and EE data were not respecting default option selections on the front end.
- Fixed a bug where the Classic Migration would stall while migrating submissions if a form contained no submissions.
- Fixed a bug where Freeform was converting number strings to actual numbers when saving the form.
- Fixed a bug where 'reCAPTCHA' was available as a fieldtype choice (it's only available as a special field).
- Fixed a bug where the Dynamic Recipients fieldtype was parsing as `0`, `1`, etc. instead of the option value.
- Fixed a bug where Composer would error when using PHP 5.4 (EE3).

## 1.7.0 - 2018-11-01

### Added
- Added field option Data Feeders for Checkbox group, Radio group, Select, and Multi-select fieldtypes. You can now populate these fields with Entries, Categories, Members, or one of our many predefined options: States, Provinces, Countries, Languages, Number ranges, Year ranges, Months, Days, and Days of the Week. Freeform Data Feeders also offer flexible control over formatting and/or which data fills option labels and option values.
- Added Multi-select fieldtype.
- Added filtering options in CP Submissions list view.
- Added reCAPTCHA support for Freeform Pro edition.
- Added dotmailer mailing list integration for Freeform Pro edition.
- Added Member Group Permissions controls - allow or deny access to various pages and parts of Freeform control panel.
- Added ability to render Dynamic Recipients as checkboxes, and now allows the submitter to select more than 1 option.
- Added ability to set Mailing List fields as hidden fields (automatically opting in users).
- Added a variety of thorough AJAX examples to demo templates!
- Added support for mapping to website, URL, dropdown, radio, date and zip fields in MailChimp integration.

### Changed
- Updated SharpSpring integration to work with all custom field types.
- Updated column breakpoint for Bootstrap formatting templates to be `sm` instead of `lg`.
- Updated Freeform's automatically inserted JS to no longer include `type="text/javascript"`.

### Fixed
- Fixed a bug where Classic Migration wouldn't work correctly in some cases.
- Fixed a bug where the `{if field:has_errors}` conditional was not working.
- Fixed a bug where an error would show if using PHP 7.2 with error debugging enabled.
- Fixed a bug where mapping to MailChimp fields named as numbers would result in an error.
- Fixed a bug where Freeform would error for PHP 5.4.
- Fixed minor visual errors throughout the control panel.

## 1.6.4 - 2018-07-19

### Added
- Added `date_created` variable for availability in email notification templates.

### Changed
- Updated form validation to no longer allow a single space as a valid value for required fields.
- Updated HubSpot integration to load custom fields from Contacts, Companies, and Deals endpoints now, not just Deals.

### Fixed
- Fixed a bug where the Salesforce fetch token URL regex restriction was not allowing for less common URLs to pass through.
- Fixed a bug where the 'unfinalized_files' database table was not correctly removing rows when a multi-page form was successfully submitted, causing uploaded files to be removed.
- Fixed a bug where multi-page forms were losing track of field data for autogenerated titles in form submissions.

## 1.6.3 - 2018-05-17

### Added
- Added `beforePush` and `afterPush` developer hooks for API integrations.
- Added `use_action_url="yes"` parameter to Form tag as a workaround when loading a form with AJAX and submitting normally.

### Fixed
- Fixed a bug where the Salesforce fetch token URL regex restriction was not allowing for less common URLs to pass through.
- Fixed a bug where saving a channel entry with no Freeform form selected in the fieldtype would store a `0` instead of `NULL`.
- Fixed a bug where viewing submissions in the CP with an upload field with missing files could result in an error shown.
- Fixed a bug where the 'Express' edition check was writing files to the add-on directory and not the cache directory.
- Fixed a bug where the hidden Spam honeypot field label was missing the 'for' attribute.

## 1.6.2 - 2018-04-25

### Security
- Fixed an XSS security vulnerability with submitting forms.

### Fixed
- Fixed a bug where a 'Handle Missing' error would display incorrectly for Mailing List fields in Composer layout.

## 1.6.1 - 2018-04-04

### Changed
- Updated Constant Contact API integration to no longer use OAuth and fixed a bug with it not connecting correctly.

### Fixed
- Fixed a bug where upgrading from Express edition to Lite/Pro edition would not install the Freeform fieldtype for channel entries.

## 1.6.0 - 2018-03-29

### Added
- Added Pipedrive CRM integration for Freeform Pro edition.
- Added new settings for Salesforce CRM integration for assignment rules and accounts with custom URLs in Salesforce.
- Added a setting for spam protection that allows you to control the behavior of submit return (to simulate a successful submit).
- Added warnings in Composer to show if a field has a blank handle.
- Added Freeform fieldtype support for the Grid fieldtype.
- Added a \{field:option_value} variable for multi-option field types in email notification templates (for accessing the field option value instead of option label).
- Added 'form_before_submit' and 'form_after_submit' extension hooks.

### Fixed
- Fixed a bug where checkbox and radio group field type option labels were not translatable.
- Fixed a bug where the File Upload fieldtype was not fully compatible with PHP 5.4.
- Fixed a bug where Freeform might conflict with other add-ons.
- Fixed a bug where the Edit Layout modal in CP Submissions list page was not loading correctly.

## 1.5.2 - 2018-03-07

### Changed
- Updated multi-page limit in Composer to 100 pages.

### Fixed
- Fixed a bug where Radio Group fields would have their last option selected no matter what, when using sample formatting templates.
- Fixed a bug where Select fields would not send data in email notifications.

## 1.5.1 - 2018-02-27

### Changed
- Updated File Upload fields to have the ability to accept multiple files.
- Updated Classic migration utility to migrate File Upload fields with multiple files correctly now.
- Updated Dynamic Recipients field to have the ability to accept multiple recipients.
- Updated demo templates and default formatting templates to correctly handle Dynamic Recipients field as a radio group.
- Updated Checkbox fieldtype to show a warning in Composer when no value is set.

### Fixed
- Fixed a bug where using Dynamic Recipients fieldtype as Radio display would not send email notifications.
- Fixed a bug where multi-option field types had ID attributes that were not unique for each option by default.

## 1.5.0 - 2018-02-09

### Added
- Added Migration utility (beta) for migrating Freeform Classic data.
- Added a 'Use Double Opt-in?' setting for MailChimp integrations.

### Changed
- Included an update that adjusts database key lengths of fields for compatibility with `utf8mb4` character set in EE4.

### Fixed
- Fixed a bug where Dynamic Recipients notifications sometimes would not work correctly.
- Fixed a bug where the default value and placeholder attributes were not applied to Website fields when added to form in Composer.
- Fixed a bug where deleting fields was not removing them from forms.

## 1.4.2 - 2018-01-09

### Added
- Added setting to remove newlines from textarea fields for exporting.

### Fixed
- Fixed a visual bug for right column buttons in Composer interface for EE3.

## 1.4.1 - 2017-12-21

### Added
- Added Fluid fieldtype compatibility for the Freeform channel entry fieldtype.

### Changed
- Updated Salesforce integration to work with checkbox fields.

### Fixed
- Fixed some visual bugs in Composer interface between EE3 and EE4 versions.
- Fixed a bug where creating new fields with options would not save any default selections.
- Fixed a bug where the toggle switch was not displaying correctly for allowing values for fieldtypes with options in EE4.

## 1.4.0 - 2017-12-20

### Added
- Added 'Express' edition of Freeform, which is a lower cost option for smaller/simpler sites.

### Changed
- Improved Composer interface to have sticky/locking auto-scrolling side columns. This makes searching through available fields and working with longer forms easier.

## 1.3.2 - 2017-12-18

### Fixed
- Fixed a bug where Freeform would not install due to a 'max key length' error for some customers on EE4.
- Fixed a bug where the Spam protection feature's "count" in control panel was incrementing incorrectly with each successful submission.

## 1.3.1 - 2017-12-12

### Changed
- Updated the auto-scroll for error return handling to be more reliable.

### Fixed
- Fixed a bug where Freeform would not install and error for some customers on EE4.
- Fixed a bug where CP pages were not being styled correctly in EE4.
- Fixed a bug where Freeform was not translatable with language files.

## 1.3.0 - 2017-12-04

### Changed
- Updated for compatibility with both ExpressionEngine 4 and ExpressionEngine 3.
- Renamed _Freeform Next_ and _Freeform Next_ Pro to just _Freeform Lite_ and _Freeform Pro_, respectively. Folder and namespace are still 'freeform_next' to preserve legacy with Freeform Classic.

### Fixed
- Fixed a bug where the automated handle generator JS would make you wrestle with it sometimes.
- Fixed a bug where Composer would choke on form handles and field handles that contained `true` or `false` strings in them.

## 1.2.2 - 2017-11-10

### Added
- Added Max Length option to be set inside Composer for Text and Textarea fields.

### Changed
- Updated CP views to be compatible with ExpressionEngine 4.
- Updated Date & Time field to no longer switch to being handled by browser in Mobile view due to chances of incorrect formatting.

### Fixed
- Fixed a bug where the Dynamic Recipients field would not remember which selection was chosen if the page reloaded after triggering an error.
- Fixed a bug where the Date & Time field date picker was not working correctly for IE 11.
- Fixed a bug where the 'instructions_below_field' parameter was not correctly accepting 'yes' and 'no'.

## 1.2.1 - 2017-11-01

### Fixed
- Fixed a bug where Pro fieldtype fields would not show as options for mapping to API integrations.
- Fixed a bug where the SharpSpring integration would error in some cases.
- Fixed a bug where editing a field in main fields area (Freeform -> Fields) would error on the fieldtype selection.

## 1.2.0 - 2017-10-26

### Added
- Added new [SharpSpring CRM](https://sharpspring.com) API integration for Pro version.
- Added auto-disable Submit button feature once a user clicks Submit (and a setting for it in control panel).

### Changed
- Updated `form_attributes` parameter to allow no attribute value (to achieve `novalidate=` instead of `novalidate="true"`, etc).
- Updated form submissions in the Submissions list of control panel to be ordered by newest date first as default.

### Fixed
- Fixed a bug where Freeform was allowing double (or more) submitting of forms if users kept clicking the Submit button while it was loading.
- Fixed a bug where instruction text would overlap the "remove" link (if long enough) for single Checkbox fields in Composer interface.

## 1.1.2 - 2017-10-18

### Fixed
- Fixed a bug where single Checkbox fields would not remember their selection if the form errored or if the submitter went back a page in multi-page forms.
- Fixed a bug where fields with array data would display as a literal array when exporting in CSV.
- Fixed a bug where an array of data in select fields for Salesforce API integration could cause an error.

## 1.1.1 - 2017-10-06

### Fixed
- Fixed a bug where fresh installs would error after install.
- Fixed a bug where some field-specific property variables were unavailable for parsing on front end in manual implementations.

## 1.1.0 - 2017-10-03

### Added
- Added new Field types for Pro version: Confirmation, Date & Time, Number, Phone, Rating, Regex, Website.
- Added advanced Quick Export and Export Profiles options in Pro version.
- Added a setting to hide Solspace's default formatting templates from showing in the Formatting Template dropdown list in Composer.
- Added an example formatting template and demo template for Materialize framework.
- Added form anchor to be automatically generated so inline error returns can focus down to the form, if it's lower down on the page.

### Changed
- Updated Freeform to use EE's Email service instead of its own.

### Fixed
- Fixed a bug where `{field:checked}` variable was not working for manual implementations of checkbox fields.
- Fixed a bug where date_range_start/end="" and date_range="" parameters were not working in the Submissions template tag.
- Fixed a bug where status filtering would result in an error in the Submissions template tag.
- Fixed a bug where no errors would be triggered if attempting to create a blank field (no name or handle, etc).

## 1.0.7 - 2017-09-18

### Fixed
- Fixed a bug where creating a new File Upload field (Fields area) would trigger an error if a value isn't specified for Maximum Filesize.
- Fixed a bug where single Checkbox fields would always return the default value for the field in Submissions tag and email notifications, whether or not the checkbox is checked.
- Fixed a bug where quick creating fields, email templates, and formatting templates in Composer would not autoload them into the list.

## 1.0.6 - 2017-08-28

### Changed
- Updated Composer to show hash handles for Submit and HTML blocks so they can be accessible manually in frontend templates.
- Updated textareas to now automatically convert newlines to line breaks when outputting data in templates or email notifications.

### Fixed
- Fixed a bug where selected values for fields with options would not output correctly.
- Fixed a bug where tabbing through labels and values in the Field creation/editing area would reset values.
- Fixed a bug where the honeypot field for spam protection did not have spaces in-between attributes.
- Fixed a bug where Composer would have JS errors when creating new fields while using Sessions only in CP.

## 1.0.5 - 2017-07-25

### Fixed
- Fixed a bug where the 1.0.4 upgrade would show errors in CP for some installs.

## 1.0.4 - 2017-07-25

### Added
- Added a Sandbox option for Salesforce integration in Pro version.
- Added option to use the database instead of PHP sessions for storing the state of form submissions.

### Fixed
- Fixed a bug where forms would not submit correctly for some users.
- Fixed a bug where single checkbox fields were not saving the user's selection if errors were triggered.
- Fixed a bug where single checkboxes were not displaying the field value in email notifications.
- Fixed a bug where email validation was allowing some invalid syntax.
- Fixed a bug where the Update Service would error if it couldn't connect to Solspace.com.
- Fixed a bug where the Mailer extension hooks were not working correctly.

## 1.0.3 - 2017-07-03

### Fixed
- Fixed a bug where Freeform Updates Service was writing to the add-on folder instead of the EE Cache directory.
- Fixed a bug where unchecking ALL filetype options for File Upload fields in Fields area would not save preferences.
- Fixed a bug where \{field_name} syntax would NOT parse in Subject/From Name/From Email/Reply-to fields when not Storing Submissions in DB.
- Fixed a bug where an error might display when viewing submissions list in the CP in some circumstances.
- Fixed a bug with MailChimp integration where it would only load a maximum of 10 MailChimp custom fields.
- Fixed a bug where orphaned mailing list setups would error in Composer.

## 1.0.2 - 2017-06-06

### Fixed
- Fixed a bug where Composer would error and show a blank white page in some cases.
- Fixed a bug where Mailing List and CRM pages would sometimes display an error.
- Fixed a bug where the version number update process wouldn't work correctly.

## 1.0.1 - 2017-06-05

### Fixed
- Fixed a bug where Freeform Next would not install on sites using MySQL 5.5 and lower.
- Fixed a bug where new statuses could not be created.
- Fixed a bug where some sites might see an error when attempting to create new fields.

## 1.0.0 - 2017-06-01

> **Warning:** There is currently no migration path from classic Freeform/Freeform Pro to Freeform Next.

### Added
- Initial release.
