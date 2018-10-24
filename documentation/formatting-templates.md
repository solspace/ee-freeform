# Formatting Templates

While Freeform offers a very intuitive Composer interface to give you a live preview of the form you're building, there of course isn't exactly a magic way to do this on the front end for your templates. However, we have created an automated way for Freeform to figure out as much of this as possible for you.

Forms can be generated on the front end templates 2 different ways. There is no worse or better way, but here's an overview:

1. With the [Freeform_Next:Form](form.md) template tag.
	* Your form formatting code is contain directly within the template that you want the form to appear in.
	* No matter what formatting template your form may have assigned to it in Composer, the form always conforms to the template formatting used in this template.
2. Using [Freeform_Next:Render](form.md#render-examples) method.
	* Your form formatting code is stored in a separate template, but is very portable / DRY, and works similar to an include.
		* Formatting templates are HTML files stored in the EE Templates directory (benefit is that they can work nicely with version control and/or staging environments, etc).
		* Freeform includes several [formatting template examples](formatting-template-examples.md) for you to start out with as well.
	* In template(s) that you want your form(s) to show up in, you simply just insert 1 line of code: `{exp:freeform_next:render form="contact"}`
	* You can have as many formatting templates as you wish, and with a couple clicks, you can switch the formatting of a form to use a different template, without ever having to adjust the template page the form is placed into.
	* Lends itself well when attaching forms to channel entries.
	* **NOTE:** Be sure to switch or unassign a formatting template from any/all Forms using it BEFORE deleting the actual formatting template file from your EE Templates directory. If you delete the template file while it is still assigned to a form, when loading the form in your template on the front end it'll display an error that needs to be corrected.
		* If you're already experiencing this issue, simple update the form to use a different formatting template and it will resolve the issue.

Formatting templates are optional, and only necessary if using the [Freeform_Next:Render tag method](form.md#render-examples), which essentially allows you to attach a formatting template to a form so that you don't need to include formatting inside the template(s) you place the form inside.

The code and formatting in these templates looks exactly like the code in a [Freeform_Next:Form](form.md) tag, but excludes the opening and closing `{exp:freeform_next:form}` tags, as the *Render* tag does this for you.


### Examples <a href="#examples" id="examples" class="docs-anchor">#</a>

The following is a basic example of what your formatting template can look like to generate form display. A starting template like this (along with CSS) can be generated for you by visiting the [Formatting Templates](settings.md#formatting-templates) section in Freeform Settings.

	{if form:page_count > 1}
		<ul class="freeform-pages">
		{pages}
			<li>
			{if page:index == current_page:index}
				<a href="javascript:;">{page:label}</a>
			{if:else}
				{page:label}
			{/if}
			</li>
		{/pages}
		</ul>
	{/if}

	{if form:has_errors}
		<div class="freeform-form-has-errors">
			There was an error submitting this form
		</div>
	{/if}

	{rows}
		<div class="freeform-row {form:row_class}">
		{fields}
			<div class="freeform-column {form:column_class}{if field:type == 'submit'} freeform-column-content-align-{field:position}{/if}">
				{field:render
					class="{if field:type != 'submit'}freeform-input{/if}"
					label_class="freeform-label{if field:required} freeform-required{/if}{if field:input_only} freeform-input-only-label{/if}"
					error_class="freeform-errors"
					instructions_class="freeform-instructions"
				}
			</div>
		{/fields}
		</div>
	{/rows}

---

A more complex example (accounting for checkbox groups, radios, etc) with Bootstrap formatting may look like this:

	<style>label.required:after {content:"*";color:#d00;margin-left:5px;}.submit-align-left{text-align:left}.submit-align-right{text-align:right}.submit-align-center{text-align:center}.submit-align-center button:not(:first-of-type),.submit-align-left button:not(:first-of-type),.submit-align-right button:not(:first-of-type){margin-left:5px}.submit-align-spread button:first-child{float:left}.submit-align-spread button:last-child{float:right}</style>

	{if form:page_count > 1}
		<ul class="nav nav-tabs">
		{pages}
			<li class="{if page:index == current_page:index}active{if:else}disabled{/if}">
				<a href="javascript:;">{page:label}</a>
			</li>
		{/pages}
		</ul>
	{/if}

	{if form:has_errors}
		<div class="alert alert-danger">
			There was an error submitting this form

			<ul>
				{form:errors}
					<li>{error}</li>
				{/form:errors}
			</ul>
		</div>
	{/if}

	{rows}
		<div class="row {form:row_class}">
		{fields}
			<div class="{form:column_class} col-xs-12 col-sm-{column:grid_width} {if field:type == 'checkbox' OR field:type == 'mailing_list'}checkbox{if:else}form-group{/if}{if field:has_errors} has-error{/if}{if field:type == 'submit'} submit-align-{field:position}{/if}">
			{if field:type == 'checkbox_group'}

				{field:render_label label_class="{if field:required}required{/if}"}

				{field:options}
					<div class="checkbox">
						<label>
							<input type="checkbox"
								name="{field:handle}[]"
								value="{option:value}"
								{if option:checked}checked{/if}
							/>
							{option:label}
						</label>
					</div>
				{/field:options}

				{field:render_instructions instructions_class="help-block"}
				{field:render_errors error_class="help-block"}

			{if:elseif field:type == 'radio_group'}

				{field:render_label label_class="{if field:required}required{/if}"}

				{field:options}
					<div class="radio">
						<label>
							<input type="radio"
								name="{field:handle}"
								value="{option:value}"
								{if option:checked}checked{/if}
							/>
							{option:label}
						</label>
					</div>
				{/field:options}

				{field:render_instructions instructions_class="help-block"}
				{field:render_errors error_class="help-block"}

			{if:elseif field:type == 'dynamic_recipients' AND field:show_as_radio}

				{field:render_label label_class="{if field:required}required{/if}"}
				{field:options}
					<div class="radio">
						<label>
							<input type="radio"
								   name="{field:handle}"
								   value="{option:index}"
								   {if option:checked}checked{/if}
							/>
							{option:label}
						</label>
					</div>
				{/field:options}
				{field:render_instructions instructions_class="help-block"}
				{field:render_errors error_class="help-block"}

			{if:elseif field:type == 'dynamic_recipients' AND field:show_as_checkboxes}

				{field:render_label label_class="{if field:required}required{/if}"}

				{field:options}
				<div class="checkbox">
					<label>
						<input type="checkbox"
							   name="{field:handle}[]"
							   value="{option:value}"
							   {if option:checked}checked{/if}
						/>
						{option:label}
					</label>
				</div>
				{/field:options}

				{field:render_instructions instructions_class="help-block"}
				{field:render_errors error_class="help-block"}

			{if:elseif field:type == 'submit'}

				{field:render}

			{if:else}

				{field:render
					class="{if field:type == 'checkbox' OR field:type == 'mailing_list'}checkbox{if:else}form-control{/if}"
					label_class="{if field:required}required{/if}"
					error_class="help-block"
					instructions_class="help-block"
					instructions_below_field="yes"
				}

			{/if}
			</div>
		{/fields}
		</div>
	{/rows}

---

Form formatting can also be extremely manual, if that is something you prefer. Here's an example of different levels of manual you can use:

	{if form:has_errors}
		<div class="error">There was an error submitting this form.</div>
	{/if}

	<h3>{form:name}</h3>

	<ul>
		<li>
			<label for="first_name">Name (required)</label>
			<input type="text" name="first_name" value="{field:first_name:value}" />
			<input type="text" name="last_name" value="{field:last_name:value}" />
			{field:first_name:render_errors error_class="field-error"}
			{field:last_name:render_errors error_class="field-error"}
		</li>
		<li>
			{field:email:render
				class="email-field"
				placeholder="you@youremail.com"
			}
		</li>
		<li>
			<label for="city">City</label>
			<input type="text" name="city" value="{field:fcity:value}" />
			{if field:city:render_errors}
				<div class="field-error">This field is required!</div>
			{/if}
		</li>
		<li>
			{field:state:label}
			{field:last_name:render_instructions instructions_class="field-instructions"}
			<select name="{field:state:handle}">
				{field:state:options}
				<option value="{option:value}" {if option:checked}selected{/if}>
					{option:label}
				</option>
				{/field:state:options}
			</select>
			{field:last_name:render_errors error_class="field-error"}
		</li>
		<li>
			{field:how_you_found_us:render}
		</li>
		<li>
			{field:department:label}
			<select name="{field:department:handle}" type="dynamic_recipients">
			{field:department:options}
				<option value="{option:index}" {if option:checked}selected{/if}>
					{option:label}
				</option>
			{/field:department:options}
			</select>
		</li>
		<li>
			<button type="submit">Submit</button>
		</li>
	</ul>

	{if form:no_results}
		<div class="error">This form does not exist.</div>
	{/if}
