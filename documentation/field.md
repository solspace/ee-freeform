# Field object

Each field object contains the metadata specified in [Composer](forms-composer.md) for the specific form it resides in. It can render its label, input field, instructions and errors out of the box.

## Properties <a href="#properties" id="properties" class="docs-anchor">#</a>

* `id` <a href="#prop-id" id="prop-id" class="docs-anchor">#</a>
* `handle` <a href="#prop-handle" id="prop-handle" class="docs-anchor">#</a>
* `label` <a href="#prop-label" id="prop-label" class="docs-anchor">#</a>
* `instructions` <a href="#prop-instructions" id="prop-instructions" class="docs-anchor">#</a>
* `required: true` <a href="#prop-required" id="prop-required" class="docs-anchor">#</a>
	* A boolean value. `true` if the field is required.
* `errors` <a href="#prop-errors" id="prop-errors" class="docs-anchor">#</a>
	* An array of error message strings if any are present after submitting the form.
* `pageIndex` <a href="#prop-page-index" id="prop-page-index" class="docs-anchor">#</a>
	* A number representing the page index this field resides on.
* `customAttributes` <a href="#prop-custom-attributes" id="prop-custom-attributes" class="docs-anchor">#</a>
	* An object of customizable attributes to ease the customizability of `render` methods.
	* Contains the following properties (each one is `null` if not set).
		* `id` <a href="#prop-custattr-id" id="prop-custattr-id" class="docs-anchor">#</a>
			* The ID attribute of the HTML input field for `renderInput()`.
			* When used on `renderLabel()`, it will replace the default `for` attribute value.
		* `class` <a href="#prop-custattr-class" id="prop-custattr-class" class="docs-anchor">#</a>
			* The CLASS attribute of the HTML input field for `renderInput()`.
		* `labelClass` <a href="#prop-custattr-inputclass" id="prop-custattr-inputclass" class="docs-anchor">#</a>
			* The CLASS attribute of the HTML label field for `renderLabel()`.
		* `errorClass` <a href="#prop-custattr-errorclass" id="prop-custattr-errorclass" class="docs-anchor">#</a>
			* The CLASS attribute of the errors HTML list for `renderErrors()`.
		* `instructionsClass` <a href="#prop-custattr-instructionsclass" id="prop-custattr-instructionsclass" class="docs-anchor">#</a>
			* The CLASS attribute of the instructions HTML field for `renderInstructions()`.
		* `instructionsBelowField` <a href="#prop-custattr-instructionsbelowfield" id="prop-custattr-instructionsbelowfield" class="docs-anchor">#</a>
			* A boolean value.
			* Set to `true` to render instructions below, not above the input field when using the `render()` method.
		* `overrideValue` <a href="#prop-custattr-overridevalue" id="prop-custattr-overridevalue" class="docs-anchor">#</a>
			* An override value for the field's `defaultValue` in case you need a context specific default value.
		* `inputAttributes` <a href="#prop-custattr-inputattributes" id="prop-custattr-inputattributes" class="docs-anchor">#</a>
			* An object of attributes which will be added to the input field. If the form has `inputAttributes` specified, the attributes will be merged together with field `inputAttributes` taking precedence over form's `inputAttributes`.
			* Ex: `inputAttributes: { "readonly": true, "data-field-id": "test" }`
		* `useRequiredAttribute: true` <a href="#prop-custattr-userequiredattribute" id="prop-custattr-userequiredattribute" class="docs-anchor">#</a>
			* Adds `required` attribute to input fields that have been set to be required in Composer.
* `value` <a href="#prop-value" id="prop-value" class="docs-anchor">#</a>
	* The default or posted value of the field.
	* Can be a string, number or array (it's an array only for checkbox_group and email fields)
* `valueAsString` <a href="#prop-value-as-string" id="prop-value-as-string" class="docs-anchor">#</a>
	* The `value` value cast as a string.
	* Array values get joined via a `,` separator.
	* Uses the selected option labels for `checkbox_group` and `radio_group`.
		* Use `getValueAsString(false)` to use selected option values instead.
* `type` <a href="#prop-type" id="prop-type" class="docs-anchor">#</a>
	* Type of the field:
		* `text`
		* `textarea`
		* `hidden`
		* `select`
		* `checkbox`
		* `checkbox_group`
		* `radio_group`
		* `email`
		* `dynamic_recipients`
		* `file`
		* `mailing_list`
		* `html`
		* `submit`


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
	* `labelNext` <a href="#field-submit-label-next" id="field-submit-label-next" class="docs-anchor">#</a>
		* A label for the **Next** button. `Submit` by default.
	* `labelPrev` <a href="#field-submit-label-prev" id="field-submit-label-prev" class="docs-anchor">#</a>
		* A label for the **Previous** button. `Previous` by default.
	* `disablePrev` <a href="#field-submit-disable-prev" id="field-submit-disable-prev" class="docs-anchor">#</a>
		* A boolean value. If `true` the **Previous** button should not be rendered.
* `dynamic_recipients` <a href="#field-dynamic_recipients" id="field-dynamic_recipients" class="docs-anchor">#</a>
	* `showAsRadio` <a href="#field-dynamic_recipients-show-as-radio" id="field-dynamic_recipients-show-as-radio" class="docs-anchor">#</a>
		* A boolean value. If `true` the dynamic recipients field should be rendered as radio buttons instead of a select field.
	* `notificationId` <a href="#field-dynamic_recipients-notification-id" id="field-dynamic_recipients-notification-id" class="docs-anchor">#</a>
		* The database ID of the assigned Email Notification Template.
	* **NOTE:** When parsing this field semi-manually, be sure to use `loop.index0` to generate numeric values of options instead of `fieldName.value`.
* `email` <a href="#field-email" id="field-email" class="docs-anchor">#</a>
	* `placeholder` <a href="#field-email-placeholder" id="field-email-placeholder" class="docs-anchor">#</a>
	* `notificationId` <a href="#field-email-notification-id" id="field-email-notification-id" class="docs-anchor">#</a>
		* The database ID of the assigned Email Notification Template.
* `file` <a href="#field-file" id="field-file" class="docs-anchor">#</a>
	* `fileKinds` <a href="#field-file-file-kinds" id="field-file-file-kinds" class="docs-anchor">#</a>
		* An array of allowed file kinds, e.g. `image`, `document`, `audio`, etc.
	* `maxFileSizeKB` <a href="#field-file-max-filesize-kb" id="field-file-max-filesize-kb" class="docs-anchor">#</a>
		* The numeric representation of the upload limit in kilobytes.


## Methods <a href="#methods" id="methods" class="docs-anchor">#</a>

* `render()` <a href="#method-render" id="method-render" class="docs-anchor">#</a>
	* Use this method to render a predefined, minimal markup html block containing the field's label, input field, instructions and list of errors.
	* Can receive an object of [`customAttributes`](#prop-custom-attributes) as the first and only argument (optional).
* `renderLabel()` <a href="#method-render-label" id="method-render-label" class="docs-anchor">#</a>
	* Use this method to only render the label html field with the field's label inside.
	* The label class can be overwritten via form's custom attributes or the field's custom attributes.
* `renderInput()` <a href="#method-render-input" id="method-render-input" class="docs-anchor">#</a>
	* Use this method to only render the field's html input field.
	* The class can be overwritten  via form's custom attributes or the field's custom attributes
* `renderInstructions()` <a href="#method-render-instructions" id="method-render-instructions" class="docs-anchor">#</a>
	* Use this method to only render the field's html instructions field. (Renders only if there are instructions present).
	* The instructions field class can be overwritten via form's custom attributes or the field's custom attributes.
* `renderErrors()` <a href="#method-render-errors" id="method-render-errors" class="docs-anchor">#</a>
	* Use this method to only render the error message block. (Renders only if there are errors present).
	* The error list class can be overwritten via form's custom attributes or the field's custom attributes.
* `isValid()`  <a href="#method-is-valid" id="method-is-valid" class="docs-anchor">#</a>
	* Returns a boolean value of true if the form has been posted and this field doesn't contain any errors.

## Usage in Templates <a href="#templates" id="templates" class="docs-anchor">#</a>

Render the whole field (label, input field, instructions and errors) with a single line:

	{{ field.render() }}

---

Render each part of the field separately:

	{{ field.renderLabel() }}
	{{ field.renderInstructions() }}
	{{ field.renderInput() }}
	{{ field.renderErrors() }}

---

Fully customize the output of your field:

	<label data-label class="label">
		{{ field.label }}
		{% if field.instructions %}
			<span class="instructions">{{ field.instructions }}</span>
		{% endif %}
	</label>
	<input type="text" name="{{ field.handle }}" value="{{ field.valueAsString }}" data-some-value="my_custom_value_here" />
	{% if field.errors %}
		{% for errorString in field.errors %}
			<div class="error">{{ errorString }}</div>
		{% endfor %}
	{% endif %}

---

Render the whole field but override some of the HTML element classes:

	{{ field.render({
		class: "freeform-input",
		labelClass: "freeform-label" ~ (field.required ? " freeform-required" : ""),
		errorClass: "freeform-errors",
		instructionsClass: "freeform-instructions",
		instructionsBelowField: true,
		overrideValue: "This is now the new default value",
		inputAttributes: {
			"data-field-id": field.id,
		}
	}) }}
