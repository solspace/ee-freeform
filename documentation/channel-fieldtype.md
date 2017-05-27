# Field type

Freeform includes a channel field type that allows you to assign/relate forms to Channel Entries.

Here's an overview on how to use this field type:

* [Creating a Freeform channel field](#create)
* [How the Fieldtype works](#how-works)
* [Template Variables](#variables)
* [Example Usage in Templates](#examples)


## Creating a Freeform field <a href="#create" id="create" class="docs-anchor">#</a>
Creating a Freeform Form field is done just like any other fieldtype, here's an overview of the process:

1. Go to the **Settings** area in Craft control panel. Click on **Fields**.
2. Click the **New field** button in the top right corner.
3. Name the field as you wish. For example: *Related Form* with a handle of *relatedForm*.
4. For the **Field Type** option, select *Freeform Form* or *Freeform Submissions* from the list.
6. **Selection Label** is the text that will appear on the button which opens the Freeform Form selection pop-up dialog.
7. Click the **Save** button in the top right corner to save the new field.

Your Freeform Form/Submissions field is now available to be assigned to other sections.

[![Create New Fieldtype](images/cp_fieldtype-create.png)](images/cp_fieldtype-create.png)


## How the Fieldtype works <a href="#how-works" id="how-works" class="docs-anchor">#</a>
The Freeform *Form* (or *Submissions*) fieldtype lets the user assign any Freeform form (or form submissions) to any element: a section entry, categories, assets, etc.

[![Using Fieldtype](images/cp_fieldtype-entry.png)](images/cp_fieldtype-entry.png)


## Template Variables <a href="#variables" id="variables" class="docs-anchor">#</a>

The *Submissions* field type can access anything in the [Submission object](submission.md). For the *Form* field type, the following are template properties are available:

* `name` <a href="#param-name" id="param-name" class="docs-anchor">#</a>
	* Outputs the name of the form
* `handle` <a href="#param-handle" id="param-handle" class="docs-anchor">#</a>
	* Outputs the handle of the form
* `id` <a href="#param-id" id="param-id" class="docs-anchor">#</a>
	* Outputs the unique ID of the form
* `description` <a href="#param-description" id="param-description" class="docs-anchor">#</a>
	* Outputs the description of the form
* `render()` <a href="#param-render" id="param-render" class="docs-anchor">#</a>
	* Outputs the full form, rendering it with the [Formatting Template](formatting-templates.md) specified in Composer for the form.


## Example Usage in Templates <a href="#examples" id="examples" class="docs-anchor">#</a>
An example of template code you would use to display a Freeform form (with field handle of *myFreeformfieldname*) that is attached to a Craft Entry would look something like this:

	{% for entry in craft.entries.section('news').limit(10) %}
		<div class="entry">
			<h2><a href="{{ entry.url }}">{{ entry.title }}</a></h2>
			{{ entry.summary }}
			{% if entry.myFreeformfieldname is defined and entry.myFreeformfieldname is not empty %}
				<h3>{{ entry.myFreeformfieldname.name }}</h3>
				{{ entry.myFreeformfieldname.render() }}
			{% endif %}
		</div>
	{% endfor %}

---

If you'd like to automatically pass content from another element (such as a Craft Entry) into Freeform field(s), you'd have to use the [overrideValues](form.md#prop-custattr-overridevalues) property inside your formatting template.

For example, if you want to pass a title of an entry to a Freeform field, and the entry slug was in the second segment of your URL, you should set that in your formatting template. Also be sure to include a hidden Freeform field in your form (in this case called `entryTitle`). Your formatting template code might look something like this:

	{% set entry = craft.entries.slug(craft.request.getSegment(2)).first() %}

	{{ form.renderTag({
		overrideValues: { entryTitle: entry.title }
	}) }}

---

Below is example code for displaying form submissions that have been attached to a blog entry (from a field called **formSubmissions**).

	{% set blogs = craft.entries.section("blog").limit(20) %}

	{% for blog in blogs %}
		<h2>{{ blog.title }}</h2>
		<ul>
		{% for submission in blog.formSubmissions %}
			<li>{{ submission.title }}</li>
		{% endfor %}
		</ul>
	{% endfor %}
