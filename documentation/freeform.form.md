# freeform.form function

The *freeform.form* template function returns a [Form object](form.md) containing its metadata and fields objects. From there you can either render the form using the pre-selected formatting template by calling `form.render()` or take control over it by iterating over its fields and using `form.renderTag` and `form.renderClosingTag` methods.

[![Form](images/templates_form-errors.png)](images/templates_form-errors.png)


## Parameters <a href="#parameters" id="parameters" class="docs-anchor">#</a>

The *freeform.form* template function is always constructed the same way. The first assumed parameter should contain the form ID or handle, and the second parameter (optional) should contain an object of overrides (typically used for applying a class globally to specific types of inputs, etc).

So following this format: `{{ craft.freeform.form("FORMHANDLE", {OVERRIDES}) }}`, your code might look something like this:

	{{ craft.freeform.form("composerForm", {
		labelClass: "form-label",
		inputClass: "form-control",
		instructionsBelowField: true,
		overrideValues: {
			hiddenFieldHandle: entry.id,
		}
	}).render() }}

* First parameter: `formID` or `formHandle` <a href="#param-first-param" id="param-first-param" class="docs-anchor">#</a>
* Second parameter (optional) is an object of the following overriding options:
	* `inputClass` <a href="#param-inputclass" id="param-inputclass" class="docs-anchor">#</a>
		* Overrides the class name of all input elements.
	* `submitClass` <a href="#param-submitclass" id="param-submitclass" class="docs-anchor">#</a>
		* Overrides the class name of all submit elements.
	* `rowClass` <a href="#param-rowclass" id="param-rowclass" class="docs-anchor">#</a>
		* Overrides the class name of all row `<div>` elements.
	* `columnClass` <a href="#param-columnclass" id="param-columnclass" class="docs-anchor">#</a>
		* Overrides the class name of all field column `<div>` elements.
	* `labelClass` <a href="#param-labelclass" id="param-labelclass" class="docs-anchor">#</a>
		* Overrides the class name of all `<label>` elements.
	* `errorClass` <a href="#param-errorclass" id="param-errorclass" class="docs-anchor">#</a>
		* Overrides the class name of all error `<ul>` elements.
	* `instructionsClass` <a href="#param-instructionsclass" id="param-instructionsclass" class="docs-anchor">#</a>
		* Overrides the class name of all instruction `<div>` elements.
	* `instructionsBelowField` <a href="#param-instructionsbelowfield" id="param-instructionsbelowfield" class="docs-anchor">#</a>
	 	* A `boolean` value, if set to `true` - will render field instructions below the `<input>` element.
	* `class` <a href="#param-class" id="param-class" class="docs-anchor">#</a>
		* Overrides the `<form>` class name.
	* `id` <a href="#param-id" id="param-id" class="docs-anchor">#</a>
		* Overrides the `<form>` ID attribute.
	* `method` <a href="#param-method" id="param-method" class="docs-anchor">#</a>
		* Overrides the `<form>` method attribute. `POST` by default.
	* `name` <a href="#param-name" id="param-name" class="docs-anchor">#</a>
		* Overrides the `<form>` name attribute. `POST` by default.
	* `action` <a href="#param-action" id="param-action" class="docs-anchor">#</a>
		* Overrides the `<form>` action attribute.
	* `overrideValues` <a href="#param-overridevalues" id="param-overridevalues" class="docs-anchor">#</a>
		* Allows overriding the default values for any field:
			* Specify the field `handle` as key, and provide the custom value override as its value.
			* E.g. `{overrideValues: {firstName: currentUser.name}}`.
			* If a [Field](field.md) uses an `overrideValue` attribute, it will take precedence over the value specified in this attribute.
	* `formAttributes` <a href="#param-formattributes" id="param-formattributes" class="docs-anchor">#</a>
		* An object of attributes which will be added to the form.
		* Ex: `formAttributes: { "novalidate": true, "data-form-id": "test" }`
	* `inputAttributes` <a href="#param-inputattributes" id="param-inputattributes" class="docs-anchor">#</a>
		* An object of attributes which will be added to all input fields.
		* Ex: `inputAttributes: { "readonly": true, "data-field-id": "test" }`
	* `useRequiredAttribute: true` <a href="#param-userequiredattribute" id="param-userequiredattribute" class="docs-anchor">#</a>
		* Adds `required` attribute to input fields that have been set to be required in Composer.


## Usage in Templates <a href="#templates" id="templates" class="docs-anchor">#</a>

Render the form using its formatting template:

	{{ craft.freeform.form("composerForm").render() }}

---

Render the form using its formatting template, but overriding some classes:

	{{ craft.freeform.form("composerForm", {
		labelClass: "form-label",
		inputClass: "form-control",
		instructionsBelowField: true,
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
