# freeform.submissions function

The *freeform.submissions* template function fetches a list of submissions based on some or no criteria.

[![Submissions](images/templates_submission.png)](images/templates_submission.png)


## Parameters <a href="#parameters" id="parameters" class="docs-anchor">#</a>

* `form` <a href="#param-form" id="param-form" class="docs-anchor">#</a>
	* Handle of the form, e.g. `"composerForm"`, or an array of handles: `["composerForm", "clientSurvey"]`.
	* Use `"not composerForm"` to select all submissions EXCEPT the ones for **Composer Form** form.
* `formId` <a href="#param-formid" id="param-formid" class="docs-anchor">#</a>
	* An ID of the form, or array of ID's, e.g. `[1, 2, 3]`.
	* If you want to select all form submissions EXCEPT the form with an ID of **1**, use `"not 1"`.
* `limit` <a href="#param-limit" id="param-limit" class="docs-anchor">#</a>
	* Supply an `int` to limit the amount of submissions returned.
* `order` <a href="#param-order" id="param-order" class="docs-anchor">#</a>
	* Use any field handle to order by that value and include the `ASC` or `DESC` parameter in the string, e.g. `order: "firstName ASC"`.
* `status` <a href="#param-status" id="param-status" class="docs-anchor">#</a>
	* Specify status to fetch submissions with a certain status.
	* `status: "open"` if you have a status with a handle `open`.


## Usage in Templates <a href="#templates" id="templates" class="docs-anchor">#</a>

Display a simple list of submissions:

	{% set submissions = craft.freeform.submissions({
		order: "firstName ASC, lastName DESC",
		status: ["pending", "closed"],
	}) %}

	{% for submission in submissions %}
		<div>
			{{ submission.title }} - {{ submission.firstName }}
		</div>
	{% endfor %}

---

Print out all submissions and check if fields exist for the submitted form, before printing them out:

	{% set submissions = craft.freeform.submissions({
		order: "firstName ASC, lastName DESC",
		status: ["pending", "closed"],
	}) %}

	{% for submission in submissions %}
		<div>
			<div>{{ submission.title }} - {{ submission.form.name }}</div>

			{% if submission.firstName is not null %}
				{{ submission.firstName.label }}: {{ submission.firstName }}<br>
			{% endif %}

			{% if submission.lastName is not null %}
				{{ submission.lastName.label }}: {{ submission.lastName }}<br>
			{% endif %}
		</div>
	{% endfor %}

---

To paginate submissions, use Craft's [Pagination](https://craftcms.com/docs/templating/paginate). Here's an example:


	{% paginate craft.freeform.submissions({limit: 5}) as pageInfo, submissions %}

	{% for submission in submissions %}
		<div>
			<div>{{ submission.title }} - {{ submission.form.name }}</div>
		</div>
	{% endfor %}

	{% if pageInfo.prevUrl %}
		<a href="{{ pageInfo.prevUrl }}">Previous Page</a>
	{% endif %}
	{% if pageInfo.nextUrl %}
		<a href="{{ pageInfo.nextUrl }}">Next Page</a>
	{% endif %}

---

To display a single submission (see [Submission object](submission.md) for more info):

	{% set submissionId = craft.request.segment(5) %}
	{% set submission = craft.freeform.submissions({id: submissionId}).first() %}

	{% if submission %}
		{% set form = submission.form %}

		<h3>{{ form.name }} - {{ submission.title }}</h3>

		<table class="table table-striped">
			{% for field in submission.fieldMetadata %}
				<tr>
					<th style="width: 20%;">{{ field.label ? field.label : "no-label" }}</th>
					<td>
						{% set fieldValue = attribute(submission, field.handle).value %}
						{% if fieldValue is iterable %}
							<ul>
								{% for value in fieldValue %}
									<li>{{ value }}</li>
								{% endfor %}
							</ul>
						{% else %}
							{{ fieldValue }}
						{% endif %}
					</td>
				</tr>
			{% endfor %}
		</table>

	{% else %}

		<div class="alert alert-danger">
			<p class="lead">
				Sorry, no submission was found.
			</p>
		</div>

	{% endif %}

---

The following is an example that shows how to render uploaded [Assets](https://craftcms.com/docs/assets) in your form submissions:

	{% set form = craft.freeform.form('formHandle') %}
	{% set submissions = craft.freeform.submissions({
		form: 'formHandle',
	}) %}

	<h3>{{ form.name }}</h3>

	{% if submissions is empty %}
		<div>There are no submissions</div>
	{% else %}
		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>Title</th>
					{% for field in (submissions|first).fieldMetadata %}
						<th>{{ field.label }}</th>
					{% endfor %}
				</tr>
			</thead>
			<tbody>
			{% for submission in submissions %}
				<tr>
					<td>{{ submission.id }}</td>
					<td>
						<a href="{{ siteUrl }}freeform_demo/bootstrap/{{ form.handle }}/submissions/{{ submission.id }}">
							{{ submission.title }}
						</a>
					</td>
					{% for field in submission.fieldMetadata %}
						<td>
							{% if field.type == "file" %}
								{% set assetId = attribute(submission, field.handle) %}
								{% set asset = craft.assets.id(assetId).first() %}
								{% if asset %}
									<img src="{{ craft.assets.id(assetId).first().url }}" />
								{% endif %}
							{% else %}
								{{ attribute(submission, field.handle) }}
							{% endif %}
						</td>
					{% endfor %}
				</tr>
			{% endfor %}
			</tbody>
		</table>
	{% endif %}
