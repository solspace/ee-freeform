# Form object

The Form object contains its metadata and field objects. You can either render the form using the pre-selected formatting template by calling `form.render()` or achieve a more fine-grained control over it by iterating over its rows and fields and using `form.renderTag` and `form.renderClosingTag` methods.


## Properties <a href="#properties" id="properties" class="docs-anchor">#</a>

* `name` <a href="#prop-name" id="prop-name" class="docs-anchor">#</a>
	* Outputs the name of the form.
* `handle` <a href="#prop-handle" id="prop-handle" class="docs-anchor">#</a>
	* Outputs the handle of the form.
* `id` <a href="#prop-id" id="prop-id" class="docs-anchor">#</a>
	* Outputs the unique ID of the form.
* `description` <a href="#prop-description" id="prop-description" class="docs-anchor">#</a>
	* Outputs the description of the form.
* `submissionTitleFormat` <a href="#prop-submissionTitleFormat" id="prop-submissionTitleFormat" class="docs-anchor">#</a>
	* Outputs the submissionTitleFormat used when creating new submissions based on this form.
* `returnUrl` <a href="#prop-returnUrl" id="prop-returnUrl" class="docs-anchor">#</a>
	* Outputs the returnUrl of the form.
* `pages` <a href="#prop-pages" id="prop-pages" class="docs-anchor">#</a>
	* Returns a list of [Page objects](page.md) each containing its label and index.
* `currentPage` <a href="#prop-currentPage" id="prop-currentPage" class="docs-anchor">#</a>
	* Returns the current [Page object](page.md) containing its label and index.
* `customAttributes` <a href="#prop-custom-attributes" id="prop-custom-attributes" class="docs-anchor">#</a>
	* An object of customizable attributes to ease the customizability of field rendering.
	* Contains the following properties (each one is `null` if not set):
		* `id` <a href="#prop-custattr-id" id="prop-custattr-id" class="docs-anchor">#</a>
			* The ID attribute of the HTML form tag.
		* `class` <a href="#prop-custattr-class" id="prop-custattr-class" class="docs-anchor">#</a>
			* The CLASS attribute of the HTML form tag.
		* `method` <a href="#prop-custattr-method" id="prop-custattr-method" class="docs-anchor">#</a>
			* The METHOD attribute for the form tag.
		* `action` <a href="#prop-custattr-action" id="prop-custattr-action" class="docs-anchor">#</a>
			* The ACTION attribute for the form tag.
		* `returnUrl` <a href="#prop-custattr-returnurl" id="prop-custattr-returnurl" class="docs-anchor">#</a>
			* Allows overriding the return URL of the form upon successful submit.
		* `rowClass` <a href="#prop-custattr-rowclass" id="prop-custattr-rowclass" class="docs-anchor">#</a>
			* The CLASS attribute of all HTML row tags.
		* `columnClass` <a href="#prop-custattr-columnclass" id="prop-custattr-columnclass" class="docs-anchor">#</a>
			* The CLASS attribute of all HTML column tags.
		* `submitClass` <a href="#prop-custattr-submitclass" id="prop-custattr-submitclass" class="docs-anchor">#</a>
			* The CLASS attribute of submit field input elements.
		* `inputClass` <a href="#prop-custattr-inputclass" id="prop-custattr-inputclass" class="docs-anchor">#</a>
			* The CLASS attribute of all HTML input fields.
		* `labelClass` <a href="#prop-custattr-labelclass" id="prop-custattr-labelclass" class="docs-anchor">#</a>
			* The CLASS attribute of all HTML label fields.
		* `errorClass` <a href="#prop-custattr-errorclass" id="prop-custattr-errorclass" class="docs-anchor">#</a>
			* The CLASS attribute of all HTML error lists.
		* `instructionsClass` <a href="#prop-custattr-instructionsclass" id="prop-custattr-instructionsclass" class="docs-anchor">#</a>
			* The CLASS attribute of all instruction fields.
		* `instructionsBelowField` <a href="#prop-custattr-instructionsbelowfield" id="prop-custattr-instructionsbelowfield" class="docs-anchor">#</a>
			* A boolean value. Set to true to render instructions below, not above the input field.
		* `overrideValues` <a href="#prop-custattr-overridevalues" id="prop-custattr-overridevalues" class="docs-anchor">#</a>
			* An object of override values for any field's `defaultValue` in case you need a context specific default value. Examples provided below...
		* `formAttributes` <a href="#prop-custattr-formattributes" id="prop-custattr-formattributes" class="docs-anchor">#</a>
			* An object of attributes which will be added to the form.
			* Ex: `formAttributes: { "novalidate": true, "data-form-id": "test" }`
		* `inputAttributes` <a href="#prop-custattr-inputattributes" id="prop-custattr-inputattributes" class="docs-anchor">#</a>
			* An object of attributes which will be added to all input fields.
			* Ex: `inputAttributes: { "readonly": true, "data-field-id": "test" }`
		* `useRequiredAttribute: true` <a href="#prop-custattr-userequiredattribute" id="prop-custattr-userequiredattribute" class="docs-anchor">#</a>
			* Adds `required` attribute to input fields that have been set to be required in Composer.


When iterating over the form, you will iterate through [Row](row.md) objects for the currently active [Page](page.md), each [Row](row.md) can be iterated over to get [Field](field.md) objects. Check the [Field](field.md) documentation to see available parameters for those objects.


## Usage in Templates <a href="#templates" id="templates" class="docs-anchor">#</a>

Render the form using its formatting template:

	{{ form.render() }}

---

Render the form using its formatting template, but overriding some classes and default values:

	{{ form.render({
		labelClass: "form-label",
		inputClass: "form-control",
		instructionsBelowField: true,
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
#
