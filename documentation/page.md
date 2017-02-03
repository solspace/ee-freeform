# Page object

The Page object contains all of the [Rows](row.md) assigned to it in the [Composer](composer.md). It also contains the index of the page and its label.


## Properties <a href="#properties" id="properties" class="docs-anchor">#</a>

* `index` <a href="#prop-index" id="prop-index" class="docs-anchor">#</a>
	* The index of the page (Starts from `0`).
* `label` <a href="#prop-label" id="prop-label" class="docs-anchor">#</a>
	* Output the label of the page.


## Methods <a href="#methods" id="methods" class="docs-anchor">#</a>

* `getRows()` <a href="#method-get-rows" id="method-get-rows" class="docs-anchor">#</a>
	* Use this to iterate over all [Rows](row.md) in this page.
	* You can also just iterate over the [Page](page.md) object to yield the same results (examples provided below).


## Usage in Templates <a href="#templates" id="templates" class="docs-anchor">#</a>

Render the page label and its index:

	{{ form.currentPage.label }}
	{{ form.currentPage.index }}

---

Render all form pages and add a class to the currently shown page:

	<ul>
		{% for page in form.pages %}
			<li>
				{% if form.currentPage.index == page.index %}
					<a href="javascript:;">{{ page.label }}</a>
				{% else %}
					{{ page.label }}
				{% endif %}
			</li>
		{% endfor %}
	</ul>

---

Iterate through all rows and its fields of the current page:

	{% for row in form.currentPage %}
		<div class="row">
			{% for field in row %}
				<div class="field">
					{{ field.label }}
				</div>
			{% endfor %}
		</div>
	{% endfor %}


Iterating over the form yields the same results:

	{% for row in form %}
		<div class="row">
			{% for field in row %}
				<div class="field">
					{{ field.label }}
				</div>
			{% endfor %}
		</div>
	{% endfor %}
