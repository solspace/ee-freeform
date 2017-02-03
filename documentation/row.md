# Row object

The Row object contains all of the [Fields](field.md) assigned to it in the composer. It can contain up to 4 fields/columns. The Row object doesn't contain any properties or methods. Instead, it's an iterable object where you have to iterate over it to get the [Fields](field.md) contained within.


## Usage in Templates <a href="#templates" id="templates" class="docs-anchor">#</a>

Render all rows and fields of the currently active page within a form:

	{% for row in form %}
		<div class="row">
			{% for field in row %}
				<div class="field">
					{{ field.label }}
				</div>
			{% endfor %}
		</div>
	{% endfor %}
