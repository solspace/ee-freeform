# Freeform_Next:Form tag

The *Freeform_Next:Form* template tag displays your form contents. You can either loop through automated rendering of form pages/rows/columns/fields (based on what is in Composer), or manually enter each field if you need full comtrol for complex forms.

If you're wanting to simply render a complete form based on Composer layout and the assigned formatting template, you can just use the [Freeform_Next:Render](render.md) template tag.

[![Form](images/templates_form-errors.png)](images/templates_form-errors.png)


## Parameters <a href="#parameters" id="parameters" class="docs-anchor">#</a>

* `form` <a href="#param-form" id="param-form" class="docs-anchor">#</a>
	* Specify the handle of the form you'd like to be displayed.
* `form_id` <a href="#param-form-id" id="param-form-id" class="docs-anchor">#</a>
	* Specify the ID of the form you'd like to be displayed.
* `input_class` <a href="#param-inputclass" id="param-inputclass" class="docs-anchor">#</a>
	* Overrides the class name of all input elements.
* `submit_class` <a href="#param-submitclass" id="param-submitclass" class="docs-anchor">#</a>
	* Overrides the class name of all submit elements.
* `row_class` <a href="#param-rowclass" id="param-rowclass" class="docs-anchor">#</a>
	* Overrides the class name of all row `<div>` elements.
* `column_class` <a href="#param-columnclass" id="param-columnclass" class="docs-anchor">#</a>
	* Overrides the class name of all field column `<div>` elements.
* `label_class` <a href="#param-labelclass" id="param-labelclass" class="docs-anchor">#</a>
	* Overrides the class name of all `<label>` elements.
* `error_class` <a href="#param-errorclass" id="param-errorclass" class="docs-anchor">#</a>
	* Overrides the class name of all error `<ul>` elements.
* `instructions_class` <a href="#param-instructionsclass" id="param-instructionsclass" class="docs-anchor">#</a>
	* Overrides the class name of all instruction `<div>` elements.
* `instructions_below_field` <a href="#param-instructionsbelowfield" id="param-instructionsbelowfield" class="docs-anchor">#</a>
	* A `boolean` value, if set to `true` - will render field instructions below the `<input>` element.
* `class` <a href="#param-class" id="param-class" class="docs-anchor">#</a>
	* Overrides the `<form>` class name.
* `id` <a href="#param-id" id="param-id" class="docs-anchor">#</a>
	* Overrides the `<form>` ID attribute.
* `return_url` or `return` <a href="#param-returnurl" id="param-returnurl" class="docs-anchor">#</a>
	* Overrides the return URL for the form.
* `method` <a href="#param-method" id="param-method" class="docs-anchor">#</a>
	* Overrides the `<form>` method attribute. `POST` by default.
* `name` <a href="#param-name" id="param-name" class="docs-anchor">#</a>
	* Overrides the `<form>` name attribute. `POST` by default.
* `action` <a href="#param-action" id="param-action" class="docs-anchor">#</a>
	* Overrides the `<form>` action attribute.
* `override_values` <a href="#param-overridevalues" id="param-overridevalues" class="docs-anchor">#</a>
	* Allows overriding the default values for any field:
		* Specify the field `handle` as key, and provide the custom value override as its value.
		* E.g. `{overrideValues: {firstName: currentUser.name}}`.
		* If a [Field](field.md) uses an `overrideValue` attribute, it will take precedence over the value specified in this attribute.
* `form_attributes` <a href="#param-formattributes" id="param-formattributes" class="docs-anchor">#</a>
	* An object of attributes which will be added to the form.
	* Ex: `formAttributes: { "novalidate": true, "data-form-id": "test" }`
* `input_attributes` <a href="#param-inputattributes" id="param-inputattributes" class="docs-anchor">#</a>
	* An object of attributes which will be added to all input fields.
	* Ex: `inputAttributes: { "readonly": true, "data-field-id": "test" }`
* `use_required_attribute: true` <a href="#param-userequiredattribute" id="param-userequiredattribute" class="docs-anchor">#</a>
	* Adds `required` attribute to input fields that have been set to be required in Composer.
* `dynamic_notification: { recipients: ["admin@example.com", "support@example.com"], template: "test.html" }` <a href="#param-dynamicnotification" id="param-dynamicnotification" class="docs-anchor">#</a>
	* Allows using a dynamic template level notification for a more fine-grained control.
	* Hard code values or pass a value from another element such as an Entry.
	* For Database entry based templates, specify the handle for `template`.
	* For Twig file based templates, specify the full file name including **.html** for `template`.


## Variables <a href="#variables" id="variables" class="docs-anchor">#</a>

* `name` <a href="#var-name" id="var-name" class="docs-anchor">#</a>
	* Outputs the name of the form.
* `handle` <a href="#var-handle" id="var-handle" class="docs-anchor">#</a>
	* Outputs the handle of the form.
* `id` <a href="#var-id" id="var-id" class="docs-anchor">#</a>
	* Outputs the unique ID of the form.
* `description` <a href="#var-description" id="var-description" class="docs-anchor">#</a>
	* Outputs the description of the form.
* `submissionTitleFormat` <a href="#var-submissionTitleFormat" id="var-submissionTitleFormat" class="docs-anchor">#</a>
	* Outputs the submissionTitleFormat used when creating new submissions based on this form.
* `returnUrl` <a href="#var-returnUrl" id="var-returnUrl" class="docs-anchor">#</a>
	* Outputs the returnUrl of the form.
* `pages` <a href="#var-pages" id="var-pages" class="docs-anchor">#</a>
	* Returns a list of [Page objects](page.md) each containing its label and index.
* `currentPage` <a href="#var-currentPage" id="var-currentPage" class="docs-anchor">#</a>
	* Returns the current [Page object](page.md) containing its label and index.
* `customAttributes` <a href="#var-custom-attributes" id="var-custom-attributes" class="docs-anchor">#</a>
	* An object of customizable attributes to ease the customizability of field rendering.
	* Contains the following properties (each one is `null` if not set):
		* `id` <a href="#var-custattr-id" id="var-custattr-id" class="docs-anchor">#</a>
			* The ID attribute of the HTML form tag.
		* `class` <a href="#var-custattr-class" id="var-custattr-class" class="docs-anchor">#</a>
			* The CLASS attribute of the HTML form tag.
		* `method` <a href="#var-custattr-method" id="var-custattr-method" class="docs-anchor">#</a>
			* The METHOD attribute for the form tag.
		* `action` <a href="#var-custattr-action" id="var-custattr-action" class="docs-anchor">#</a>
			* The ACTION attribute for the form tag.
		* `returnUrl` <a href="#var-custattr-returnurl" id="var-custattr-returnurl" class="docs-anchor">#</a>
			* Allows overriding the return URL of the form upon successful submit.
		* `rowClass` <a href="#var-custattr-rowclass" id="var-custattr-rowclass" class="docs-anchor">#</a>
			* The CLASS attribute of all HTML row tags.
		* `columnClass` <a href="#var-custattr-columnclass" id="var-custattr-columnclass" class="docs-anchor">#</a>
			* The CLASS attribute of all HTML column tags.
		* `submitClass` <a href="#var-custattr-submitclass" id="var-custattr-submitclass" class="docs-anchor">#</a>
			* The CLASS attribute of submit field input elements.
		* `inputClass` <a href="#var-custattr-inputclass" id="var-custattr-inputclass" class="docs-anchor">#</a>
			* The CLASS attribute of all HTML input fields.
		* `labelClass` <a href="#var-custattr-labelclass" id="var-custattr-labelclass" class="docs-anchor">#</a>
			* The CLASS attribute of all HTML label fields.
		* `errorClass` <a href="#var-custattr-errorclass" id="var-custattr-errorclass" class="docs-anchor">#</a>
			* The CLASS attribute of all HTML error lists.
		* `instructionsClass` <a href="#var-custattr-instructionsclass" id="var-custattr-instructionsclass" class="docs-anchor">#</a>
			* The CLASS attribute of all instruction fields.
		* `instructionsBelowField` <a href="#var-custattr-instructionsbelowfield" id="var-custattr-instructionsbelowfield" class="docs-anchor">#</a>
			* A boolean value. Set to true to render instructions below, not above the input field.
		* `overrideValues` <a href="#var-custattr-overridevalues" id="var-custattr-overridevalues" class="docs-anchor">#</a>
			* An object of override values for any field's `defaultValue` in case you need a context specific default value. Examples provided below...
		* `formAttributes` <a href="#var-custattr-formattributes" id="var-custattr-formattributes" class="docs-anchor">#</a>
			* An object of attributes which will be added to the form.
			* Ex: `formAttributes: { "novalidate": true, "data-form-id": "test" }`
		* `inputAttributes` <a href="#var-custattr-inputattributes" id="var-custattr-inputattributes" class="docs-anchor">#</a>
			* An object of attributes which will be added to all input fields.
			* Ex: `inputAttributes: { "readonly": true, "data-field-id": "test" }`
		* `useRequiredAttribute: true` <a href="#var-custattr-userequiredattribute" id="var-custattr-userequiredattribute" class="docs-anchor">#</a>
			* Adds `required` attribute to input fields that have been set to be required in Composer.


When iterating over the form, you will iterate through [Row](row.md) objects for the currently active [Page](page.md), each [Row](row.md) can be iterated over to get [Field](field.md) objects. Check the [Field](field.md) documentation to see available parameters for those objects.



## Usage in Templates <a href="#templates" id="templates" class="docs-anchor">#</a>

Render the form using its formatting template:

	{{ craft.freeform.form("composerForm").render() }}

---

Render the form using its formatting template, but overriding some classes:

	{{ craft.freeform.form("composerForm", {
		labelClass: "form-label",
		inputClass: "form-control",
		instructionsBelowField: true,
		submitClass: "btn btn-success",
		overrideValues: {
			hiddenFieldHandle: entry.id,
		}
	}).render() }}

---

Get the form object and manually iterate through fields:

	{% set form = craft.freeform.form("composerForm", {
		id: "myform",
		class: "form-class",
		rowClass: "sample-row-class"
		submitClass: "button",
	}) %}

	{{ form.renderTag }}

	{% if form.hasErrors %}
		<div class="freeform-form-has-errors">
			{{ "There was an error submitting this form"|t }}
		</div>
	{% endif %}

	{% for row in form %}
		<div class="{{ form.customAttributes.rowClass }}">
			{% for field in row %}
				{% set columnClass = "sample-column " ~ form.customAttributes.columnClass %}
				{% if field.type == "submit" %}
					{% set columnClass = columnClass ~ " submit-column" %}
				{% endif %}

				<div class="{{ columnClass }}">
					{{ field.render({
						class: field.type != "submit" ? "freeform-input" : "",
						labelClass: "sample-label" ~ (field.required ? " required" : ""),
						errorClass: "sample-errors",
						instructionsClass: "sample-instructions",
					}) }}
				</div>
			{% endfor %}
		</div>
	{% endfor %}


	{{ form.renderClosingTag }}

---

Form formatting can also be extremely manual, if that is something you prefer. Here's an example of different levels of manual you can use:

	{% set form = craft.freeform.form("composerForm") %}

	{{ form.renderTag({returnUrl: "contact/success"}) }}

		{% if form.hasErrors %}
			<div class="freeform-form-has-errors">
				{{ "There was an error submitting this form"|t }}
			</div>
		{% endif %}

		{% set firstName = form.get("firstName") %}
		{% set company = form.get("company") %}
		{% set lastName = form.get("lastName") %}
		{% set recipients = form.get("recipients") %}

		<label>{{ firstName.label }}</label>
		<input name="{{ firstName.handle }}" value="{{ firstName.value }}" />
		{{ firstName.renderErrors() }}

		<label>{{ lastName.label }}</label>
		<input name="{{ lastName.handle }}" value="{{ lastName.value }}" />
		{{ lastName.renderErrors() }}

		{{ company.renderLabel() }}
		{{ company.renderInput() }}
		{{ company.renderErrors() }}

		<label>Email Address</label>
		<input name="email" />
		{{ form.get("email").renderErrors() }}

		<label>Phone</label>
		<input name="phone" />
		{% if form.get("phone").hasErrors %}
			This field is required!
		{% endif %}

		<label>Recipient</label>
		<select name="{{ recipients.handle }}" type="dynamic_recipients">
		{% for recipients in recipients.options %}
			<option value="{{ loop.index0 }}">{{ recipients.label }}</option>
		{% endfor %}
		</select>

		<button type="submit">Submit</button>

	{{ form.renderClosingTag }}



## Usage in Templates <a href="#templates" id="templates" class="docs-anchor">#</a>

Render the form using its formatting template:

	{{ form.render() }}

---

Render the form using its formatting template, but overriding some classes and default values:

	{{ form.render({
		labelClass: "form-label",
		inputClass: "form-control",
		instructionsBelowField: true,
		submitClass: "btn btn-success",
		overrideValues: {
			hiddenFieldHandle: entry.id,
			stateSelect: "AZ",
		},
		formAttributes: {
		   "novalidate": true,
		   "data-form-id": "whatever",
		},
		inputAttributes: {
		   "readonly": true,
		   "data-field-id": field.id,
		}
	}) }}

---

Manually iterate through form fields:

	{{ form.renderTag({rowClass: "sample-row-class"}) }}

	{% if form.hasErrors %}
		<div class="freeform-form-has-errors">
			{{ "There was an error submitting this form"|t }}
		</div>
	{% endif %}

	{% for row in form %}
		<div class="{{ form.customAttributes.rowClass }}">
			{% for field in row %}
				{% set columnClass = "sample-column " ~ form.customAttributes.columnClass %}
				{% if field.type == "submit" %}
					{% set columnClass = columnClass ~ " submit-column" %}
				{% endif %}

				<div class="{{ columnClass }}">
					{{ field.render({
						class: field.type != "submit" ? "freeform-input" : "",
						labelClass: "sample-label" ~ (field.required ? " required" : ""),
						errorClass: "sample-errors",
						instructionsClass: "sample-instructions",
					}) }}
				</div>
			{% endfor %}
		</div>
	{% endfor %}

	{{ form.renderClosingTag }}

---

Use session success flash message variable (displays only once) for when form is successfully submitted:

	{% set form = craft.freeform.form("composerForm") %}

	{% if form.submittedSuccessfully %}
		<div>You've successfully submitted this form!</div>
	{% endif %}

	{{ form.render }}
