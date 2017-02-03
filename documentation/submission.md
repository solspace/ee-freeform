# Submission

## Properties <a href="#properties" id="properties" class="docs-anchor">#</a>

* `id` <a href="#prop-id" id="prop-id" class="docs-anchor">#</a>
	* The event's unique ID, which is also the element ID.
* `title` <a href="#prop-title" id="prop-title" class="docs-anchor">#</a>
	* The submission's title.
* `dateCreated` <a href="#prop-date-created" id="prop-date-created" class="docs-anchor">#</a>
	* The DateTime object of when the submission was submitted.
* `status` <a href="#prop-status" id="prop-status" class="docs-anchor">#</a>
	* The submission's status.
* `formId` <a href="#prop-form-id" id="prop-form-id" class="docs-anchor">#</a>
	* Related form's ID.
* `form` <a href="#prop-form" id="prop-form" class="docs-anchor">#</a>
	* The Freeform_FormModel.
* `fieldMetadata` <a href="#prop-field-metadata" id="prop-field-metadata" class="docs-anchor">#</a>
	* A list containing all fields who store values (doesn't include HTML fields, submit fields, mailing-list fields).
	* Each of the objects is a [Field](field.md) object, and contains the submitted value.
	* You can access all of the field properties for each field specific to the current submissions related Form.
* You can access any field in the submission's form by the field's handle:
	* If you have a field with a handle `firstName`, you can access its value by calling:
		* `{{ submission.firstName }}` or
		* Get its label `{{ submission.firstName.label }}`
			* Any other property such as `placeholder`, `options`, `defaultValue`, etc is also available.
			* Some properties are field type specific. For example, you wouldn't be able to get `rows` from a `select` field, or `options` from a `textarea` field.
