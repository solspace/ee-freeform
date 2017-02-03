# Changelog

### 1.3.1 (January 19, 2017) <a href="#v1-3-1" id="v1-3-1" class="docs-anchor">#</a>
* Fixed a bug where demo templates and sample formatting templates didn't correctly account for Submit button class from 1.3 release. Existing Freeform users may need to include a conditional in their formatting template(s) for Submit fields.
* Fixed a bug where errors would sometimes show in property editor for integrations.
* Fixed a bug with SQL Strict Mode and API integrations.
* Fixed a bug where the Tutorial walkthrough in Composer was not working correctly.

### 1.3.0 (January 12, 2017) <a href="#v1-3-0" id="v1-3-0" class="docs-anchor">#</a>
* Added permissions per form for accessing submissions.
* Added Submissions field type (for relating Freeform submissions to Craft Entries, etc).
* Added ArrayAccess for form elements.
* Added support for "picklist" and "multi-select picklist" field types in Freeform Salesforce Lead add-on.
* Updated field editor for fields with options to use the Craft field editor table (to remove or rearrange order of options).
* Updated all form labels to have the `for` attribute and a default corresponding value.
* Updated API integration plugins to use DB Schema versions so minor updates don't trigger Craft Update process.
* Optimized field database column types.
* Fixed a bug where Textarea field types were using VARCHAR 255 instead of TEXT for database column type.
* Fixed a bug where calling renderInput() on a checkbox field rendered the label as well as the input.
* Fixed a bug where applying a `class` as a `customAttribute` on a field of `submit` type wasn't adding a class.
* Fixed a bug where the `name` form attribute override parameter didn't work in `freeform.form` function and Form object.
* Fixed a bug where field's values and labels were being evaluated before being rendered, so using values like `true` and `false` would parse as `1` and `0`.
* Fixed a bug where `form.get` would not fail gracefully.
* Fixed a bug where the Freeform fieldtype did not fully support SQL Strict mode.
* Fixed a bug where Hidden fields would not show up in the Submission Edit layout.
* Fixed a bug where the success message after deleting Freeform submissions would display an incorrect message.
* Fixed a bug where users / user groups that didn't have access to Manage Forms were still seeing the "New Form" button in Submissions area.

### 1.2.0 (November 22, 2016) <a href="#v1-2-0" id="v1-2-0" class="docs-anchor">#</a>
* Added ability to change the value for single Checkbox fields. By default, it now has a value of **Yes**, which can be overwritten with any value you want. The front end however, will always display the value as `1`, but upon submission, the value will be switched to the one you have set.
* Added `formAttributes` and `inputAttributes` properties in `customAttributes` for things like `novalidate`, `data-form-id`, etc.
* Added `useRequiredAttribute: true` property to Form and Field object custom attributes. Fields that have been set to be required with get the `required` attribute added to their input.
* Added Submission object variables to email notification templates.
* Updated Freeform demo templates to use 'slug' instead of 'handle' for template routes.
* Fixed some bugs where Freeform and API Integrations were not fully compatible with PHP 5.4 and 5.5.
* Fixed a bug where Email fields that were required were being automatically assigned 'required' attribute. To add this back, use `useRequiredAttribute: true` override.
* Fixed a bug where Freeform would conflict with other plugins' navigation tabs.
* Fixed a bug where Radio Group and Select fields would display "Checked?" instead of "Selected?".
* Fixed a bug where default Submit button markup would cause issues with AJAX.
* Fixed a visual bug where Checkbox group fields were automatically adding an "All" option in CP Edit view, and checking off all values if none were selected.
* Fixed a visual bug with Composer field placeholder that would stick when using Chrome.

### 1.1.0 (November 2, 2016) <a href="#v1-1-0" id="v1-1-0" class="docs-anchor">#</a>
* Added option per form to store data in the database or not (send emails or API integration only).
* Added ability to duplicate forms.
* Updated Email Notification Template editor to match the HTML editor inside Composer.
* Updated field handles to now be overridable per form inside Composer.
* Updated forms to improve AJAX handling and added AJAX implementation documentation.
* Updated Composer page tabs to be scrollable when they extend past the available width, moved Add Page (+) button to be right aligned.
* Fixed a bug where quick create fields, notifications and formatting templates options would be displayed inside Composer to users/groups without sufficient privileges.
* Fixed a bug where clicking the main Freeform nav item would show an 'Unauthorized' message to users/groups that did not have access to Freeform Settings page.
* Fixed a bug where inline errors would only display if `form.hasErrors` conditional was used inside the formatting template.
* Fixed a bug where saving multi-page forms with no fields on it inside Composer would cause the form to no longer display when opening it again.
* Fixed a bug where `id` and `overrideValues` (for hidden fields) parameters did not work for `freeform.form.render()` function.
* Fixed a bug where `freeform.form` function would not work with overrides when `renderTag` was used.

### 1.0.1 (October 27, 2016) <a href="#v1-0-1" id="v1-0-1" class="docs-anchor">#</a>
* Fixed a bug where logged out guests would receive a permission error when submitting forms.
* Fixed a bug where Salesforce integration failed when sending empty required fields.

### 1.0.0 (October 24, 2016) <a href="#v1-0-0" id="v1-0-0" class="docs-anchor">#</a>
* Initial release.
