# Formatting Templates

While Freeform offers a very intuitive Composer interface to give you a live preview of the form you're building, there of course isn't exactly a magic way to do this on the front end for your templates. However, we have created an automated way for Freeform to figure out as much of this as possible for you.

Forms can be generated on the front end templates 2 different ways. There is no worse or better way, but here's an overview:

1. With the [freeform.form](freeform.form.md) template function.
	* Your form formatting code is contain directly within the template that you want the form to appear in.
	* No matter what formatting template your form may have assigned to it in Composer, the form always conforms to the template formatting used in this template.
2. Using [freeform.form render()](freeform.form.md#render) method.
	* Your form formatting code is stored in a separate template, but is very portable / DRY, and works similar to an include.
		* Formatting templates are Twig template files and are stored in a separate directory inside your **craft/templates** directory, so they can still work nicely with version control.
		* Freeform includes several [formatting template examples](formatting-template-examples.md) for you to start out with as well.
	* In template(s) that you want your form(s) to show up in, you simply just insert 1 line of code: `{{ craft.freeform.form("formHandle").render() }}`
	* You can have as many formatting templates as you wish, and with a couple clicks, you can switch the formatting of a form to use a different template, without ever having to adjust the page the form is placed into.
	* Lends itself well when attaching/relating forms to entries or other element types.
	* **NOTE:** Be sure to switch or unassign a formatting template from any/all Forms using it BEFORE deleting the actual formatting template file from your **craft/templates** directory. If you delete the template file while it is still assigned to a form, when loading the form in your template on the front end it'll display an error that needs to be corrected.
		* If you're already experiencing this issue, simple update the form to use a different formatting template and it will resolve the issue.

Formatting templates are optional, and only necessary if using the [render() method](freeform.form.md#render), which essentially allows you to attach/relate a formatting template to a form so that you don't need to include formatting inside the template(s) you place the form inside.

Have a look at [freeform.form function](freeform.form.md) and [Form object](form.md) for a full list of properties and parameters.

### Examples <a href="#examples" id="examples" class="docs-anchor">#</a>

The following is a basic example of what your formatting template can look like to generate form display. A starting template like this (along with CSS) can be generated for you by visiting the [Formatting Templates](settings.md#formatting-templates) section in Freeform Settings.

	{{ form.renderTag }}

	{% if form.pages|length > 1 %}
		<ul class="freeform-pages">
			{% for page in form.pages %}
				<li>
					{% if form.currentPage.index == loop.index0 %}
						<a href="javascript:;">{{ page.label }}</a>
					{% else %}
						{{ page.label }}
					{% endif %}
				</li>
			{% endfor %}
		</ul>
	{% endif %}

	{% for row in form %}
		<div class="freeform-row {{ form.customAttributes.rowClass }}">
			{% for field in row %}
				{% set columnClass = "freeform-column " ~ form.customAttributes.columnClass %}
				{% if field.type == "submit" %}
					{% set columnClass = columnClass ~ " freeform-column-content-align-" ~ field.position %}
				{% endif %}
				<div class="{{ columnClass }}">
					{{ field.render({
						class: field.type != "submit" ? "freeform-input" : "",
						labelClass: "freeform-label" ~ (field.inputOnly ? " freeform-input-only-label" : "") ~ (field.required ? " freeform-required" : ""),
						errorClass: "freeform-errors",
						instructionsClass: "freeform-instructions",
					}) }}
				</div>
			{% endfor %}
		</div>
	{% endfor %}

	{{ form.renderClosingTag }}

---

A more complex example (accounting for checkbox groups, radios, etc) with Bootstrap formatting may look like this:

	{{ form.renderTag }}

	{% if form.pages|length > 1 %}
		<ul class="nav nav-tabs">
			{% for page in form.pages %}
				<li class="{{ form.currentPage.index == loop.index0 ? "active" : "disabled" }}">
					{% if form.currentPage.index == loop.index0 %}
						<a href="javascript:;">{{ page.label }}</a>
					{% else %}
						<a href="javascript:;">{{ page.label }}</a>
					{% endif %}
				</li>
			{% endfor %}
		</ul>
	{% endif %}

	{% for row in form %}
		<div class="row {{ form.customAttributes.rowClass }}">
			{% for field in row %}
				{% set width = (12 / (row|length)) %}

				{% set isCheckbox = field.type in ["checkbox"] %}

				{% set columnClass = isCheckbox ? "checkbox" : "form-group" %}
				{% set columnClass = columnClass ~ (field.errors|length ? " has-error" : "") %}
				{% set columnClass = columnClass ~ form.customAttributes.columnClass %}
				{% set columnClass = columnClass ~ " col-lg-" ~ width ~ " col-xs-12" %}

				{% if field.type == "submit" %}
					{% set columnClass = columnClass ~ " submit-align-" ~ field.position %}
				{% endif %}

				{% if field.type == "checkbox_group" %}

					<div class="{{ columnClass }}">
						{{ field.renderLabel({
							labelClass: (field.required ? " required" : ""),
							instructionsClass: "help-block",
							errorClass: "help-block",
						}) }}

						{% for option in field.options %}
							<div class="checkbox">
								<label>
									<input type="checkbox"
										name="{{ field.handle }}[]"
										value="{{ option.value }}"
										{{ option.value in field.value ? "checked" : "" }}
									/>
									{{ option.label }}
								</label>
							</div>
						{% endfor %}

						{{ field.renderInstructions() }}
						{{ field.renderErrors() }}
					</div>

				{% elseif field.type == "radio_group" or (field.type == "dynamic_recipients" and field.showAsRadio) %}

					<div class="{{ columnClass }}">
						{{ field.renderLabel({
							labelClass: (field.required ? " required" : ""),
							instructionsClass: "help-block",
							errorClass: "help-block",
						}) }}

						{% for option in field.options %}
							<div class="radio">
								<label>
									<input type="radio"
										name="{{ field.handle }}"
										value="{{ option.value }}"
										{{ option.value in field.value ? "checked" : "" }}
									/>
									{{ option.label }}
								</label>
							</div>
						{% endfor %}

						{{ field.renderInstructions() }}
						{{ field.renderErrors() }}
					</div>

				{% elseif field.type == "submit" %}

					<div class="{{ columnClass }}">
						{{ field.render() }}
					</div>

				{% else %}

					<div class="{{ columnClass }}">
						{{ field.render({
							class: isCheckbox ? "checkbox" : "form-control",
							instructionsClass: "help-block",
							instructionsBelowField: true,
							labelClass: (field.required ? " required" : ""),
							errorClass: "help-block",
						}) }}
					</div>

				{% endif %}
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

		<button type="submit">Submit</button>

	{{ form.renderClosingTag }}

---

If you'd like to populate a Freeform field with data from another element such as [Craft Entries](https://craftcms.com/docs/templating/craft.entries), you might introduce a conditional with code that looks something like this:

	{% elseif field.handle == "myFieldHandle") %}

		<select name="{{ field.handle }}">
			{% for entry in craft.entries.section('news').limit(10) %}
				<option value="{{ entry.handle }}"{% if field.value == entry.handle %} selected{% endif %}>
					{{ entry.title }}
				</option>
			{% endfor %}
		</select>

	{% else %}
