# Freeform_Next:Forms tag

The *Freeform_Next:Forms* template tag displays a list of available forms on your site.

* [Parameters](#parameters)
* [Variables](#variables)
* [Conditionals](#conditionals)
* [Examples](#examples)


## Parameters <a href="#parameters" id="parameters" class="docs-anchor">#</a>

* `form` <a href="#param-form" id="param-form" class="docs-anchor">#</a>
	* Specify the handle of the form you'd like to be displayed.
* `form_id` <a href="#param-form-id" id="param-form-id" class="docs-anchor">#</a>
	* Specify the ID of the form you'd like to be displayed.


## Variables <a href="#variables" id="variables" class="docs-anchor">#</a>

* `{form:name}` <a href="#var-name" id="var-name" class="docs-anchor">#</a>
	* Outputs the name of the form.
* `{form:handle}` <a href="#var-handle" id="var-handle" class="docs-anchor">#</a>
	* Outputs the handle of the form.
* `{form:id}` <a href="#var-id" id="var-id" class="docs-anchor">#</a>
	* Outputs the unique ID of the form.
* `{form:description}` <a href="#var-description" id="var-description" class="docs-anchor">#</a>
	* Outputs the description of the form.
* `{form:return_url}` <a href="#var-returnUrl" id="var-returnUrl" class="docs-anchor">#</a>
	* Outputs the return URL of the form.


## Conditionals <a href="#conditionals" id="conditionals" class="docs-anchor">#</a>

* `{if form:no_results}{/if}` <a href="#cond-no-results" id="cond-no-results" class="docs-anchor">#</a>
	* Displays its contents when there are no results found for this template tag with the given set of parameters.


## Example Usage in Templates <a href="#examples" id="examples" class="docs-anchor">#</a>

The following is a simple example of how to display a list of available forms:

	<ul>
	{exp:freeform_next:forms}
		<li>
			<a href="{path='freeform/form/{form:handle}'}">{form:name}</a>
			<a href="{path='freeform/form/{form:handle}/submissions'}">
				({form:submission_count} submissions)
			</a>
		</li>
	{if form:no_results}
		<li>
			There are currently no forms for this site.
		</li>
	{/if}
	{/exp:freeform_next:forms}
	</ul>

---

The following example is similar to the one in the demo templates. It shows a list of available forms and number of submissions, likely used as a way of administrating forms/submissions.

	<table class="table">
		<thead>
			<tr>
				<th>#</th>
				<th>Form Name</th>
				<th>Description</th>
			{if logged_in_group_id == "1"}
				<th>Submissions</th>
			{/if}
			</tr>
		</thead>
		<tbody>
		{exp:freeform_next:forms}
			<tr>
				<td>{form:id}</td>
				<td>
					<a href="{path='freeform/form/{form:handle}'}">
						{form:name}
					</a>
				</td>
				<td>{form:description}</td>
			{if logged_in_group_id == "1"}
				<td>
					<a href="{path='freeform/form/{form:handle}/submissions'}">
						{form:submission_count} submissions
					</a>
				</td>
			{/if}
			</tr>
		{if form:no_results}
			<tr>
				<th colspan="{if logged_in_group_id=='1'}4{if:else}3{/if}">
					There are currently no forms for this site.
				</th>
			</tr>
		{/if}
		{/exp:freeform_next:forms}
		</tbody>
	</table>
