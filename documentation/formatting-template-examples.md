# Formatting Template Examples

Freeform includes several example [formatting templates](formatting-templates.md) to choose from. You can use these as a starting point and adjust them to suit your needs, or simply create your own from scratch. The following formatting templates are located in the **/freeform/templates/\_defaultFormTemplates/** directory. If you wish to create your own modified version, just copy the code below, or the template file (ex: **foundation.html**) and paste it into your Formatting Templates directory in the specified Craft Templates directory (ex: **/craft/templates/freeform/**), and rename it to whatever you like.

* [Bootstrap](#bootstrap)
* [Foundation](#foundation)
* [Grid](#grid)
* [Flexbox](#flexbox)

## Bootstrap <a href="#bootstrap" id="bootstrap" class="docs-anchor">#</a>

The following example assumes you're including necessary Bootstrap JS and CSS.

	<style>
		label.required:after {
			content:"*";
			color:#d00;
			margin-left:5px;
		}
		.submit-align-left {
			text-align:left;
		}
		.submit-align-right {
			text-align:right;
		}
		.submit-align-center {
			text-align:center;
		}
		.submit-align-center button:not(:first-of-type),
		.submit-align-left button:not(:first-of-type),
		.submit-align-right button:not(:first-of-type) {
			margin-left:5px;
		}
		.submit-align-spread button:first-child {
			float:left;
		}
		.submit-align-spread button:last-child {
			float:right;
		}
	</style>

	{{ form.renderTag }}

	{% if form.pages|length > 1 %}
		<ul class="nav nav-tabs">
			{% for page in form.pages %}
				<li class="{{ form.currentPage.index == page.index ? "active" : "disabled" }}">
					{% if form.currentPage.index == page.index %}
						<a href="javascript:;">{{ page.label }}</a>
					{% else %}
						<a href="javascript:;">{{ page.label }}</a>
					{% endif %}
				</li>
			{% endfor %}
		</ul>
	{% endif %}

	{% if form.hasErrors %}
		<div class="alert alert-danger">
			{{ "There was an error submitting this form"|t }}
		</div>
	{% endif %}

	{% for row in form %}
		<div class="row {{ form.customAttributes.rowClass }}">
			{% for field in row %}
				{% set width = (12 / (row|length)) %}

				{% set isCheckbox = field.type in ["checkbox","mailing_list"] %}

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

## Foundation <a href="#foundation" id="foundation" class="docs-anchor">#</a>

The following example assumes you're including necessary Foundation JS and CSS.

	<style>
		label.required:after {
			content:"*";
			color:#d00;
			margin-left:5px;
			font-size:12px;
			font-family:serif;
			font-weight:700;
		}
		.submit {
			margin-top:15px;
		}
		.submit-align-left {
			text-align:left;
		}
		.submit-align-right {
			text-align:right;
		}
		.submit-align-center {
			text-align:center;
		}
		.submit-align-center button:not(:first-of-type),
		.submit-align-left button:not(:first-of-type),
		.submit-align-right button:not(:first-of-type) {
			margin-left:5px;
		}
		.submit-align-spread button:first-child {
			float:left;
		}
		.submit-align-spread button:last-child {
			float:right;
		}
	</style>

	{{ form.renderTag }}

	{% if form.pages|length > 1 %}

		<ul class="menu pagemenu">
			{% for page in form.pages %}
				<li class="{{ form.currentPage.index == page.index ? "active" : "" }}">
					{% if form.currentPage.index == page.index %}
						<a href="javascript:;" class="is-active">{{ page.label }}</a>
					{% else %}
						<a href="javascript:;">{{ page.label }}</a>
					{% endif %}
				</li>
			{% endfor %}
		</ul>
	{% endif %}

	{% if form.hasErrors %}
		<div class="callout alert">
			{{ "There was an error submitting this form"|t }}
		</div>
	{% endif %}


	{% for row in form %}
		<div class="row {{ form.customAttributes.rowClass }}">
			{% for field in row %}
				{% set width = (12 / (row|length)) %}

				{% set isCheckbox = field.type in ["checkbox","mailing_list"] %}

				{% set columnClass = "" %}
				{% set columnClass = columnClass ~ form.customAttributes.columnClass %}
				{% set columnClass = columnClass ~ " medium-" ~ width ~ " columns" %}

				{% if field.type == "submit" %}
					{% set columnClass = columnClass ~ " submit submit-align-" ~ field.position %}
				{% endif %}

				{% if field.type == "checkbox_group" %}

					<div class="{{ columnClass }}">
						{{ field.renderLabel({
							labelClass: (field.required ? " required" : ""),
							instructionsClass: "help-text",
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
							instructionsClass: "help-text",
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
							instructionsClass: "help-text",
							instructionsBelowField: true,
							labelClass: (field.required ? " required" : ""),
						}) }}
					</div>

				{% endif %}
			{% endfor %}
		</div>
	{% endfor %}

	{{ form.renderClosingTag }}

## Grid <a href="#grid" id="grid" class="docs-anchor">#</a>

	<style>.freeform-pages{padding:0;margin:0 0 10px;list-style:none}.freeform-pages:after{content:"";display:table;clear:both}.freeform-pages li{float:left;margin:0 10px 0 0}.freeform-row{display:block;margin:0 -15px}.freeform-row:after{content:"";display:table;clear:both}.freeform-row .freeform-column{display:block;padding:10px 15px;float:left;box-sizing:border-box}.freeform-row .freeform-column:after{content:"";display:table;clear:both}.freeform-row .freeform-column label{display:block}.freeform-row .freeform-column .freeform-label{font-weight:bold}.freeform-row .freeform-column .freeform-label.freeform-required:after{content:"*";margin-left:5px;color:red}.freeform-row .freeform-column .freeform-input{width:100%;display:block;box-sizing:border-box}.freeform-row .freeform-column .freeform-input[type=checkbox],.freeform-row .freeform-column .freeform-input[type=radio]{width:auto;display:inline;margin-right:5px}.freeform-row .freeform-column .freeform-input-only-label{font-weight:normal}.freeform-row .freeform-column .freeform-input-only-label>.freeform-input{display:inline-block;width:auto;margin-right:5px}.freeform-row .freeform-column .freeform-errors{list-style:none;padding:0;margin:5px 0 0}.freeform-row .freeform-column .freeform-errors>li{color:red}.freeform-row .freeform-column .freeform-instructions{margin:0 0 5px;font-size:13px;color:#ABA7A7}.freeform-row .freeform-column.freeform-column-content-align-left{text-align:left}.freeform-row .freeform-column.freeform-column-content-align-left button:not(:first-of-type){margin-left:5px}.freeform-row .freeform-column.freeform-column-content-align-center{text-align:center}.freeform-row .freeform-column.freeform-column-content-align-center button:not(:first-of-type){margin-left:5px}.freeform-row .freeform-column.freeform-column-content-align-right{text-align:right}.freeform-row .freeform-column.freeform-column-content-align-right button:not(:first-of-type){margin-left:5px}.freeform-row .freeform-column.freeform-column-content-align-spread button:first-child{float:left}.freeform-row .freeform-column.freeform-column-content-align-spread button:last-child{float:right}.freeform-row .freeform-column-1{width:8.33333%}.freeform-row .freeform-column-2{width:16.66667%}.freeform-row .freeform-column-3{width:25%}.freeform-row .freeform-column-4{width:33.33333%}.freeform-row .freeform-column-5{width:41.66667%}.freeform-row .freeform-column-6{width:50%}.freeform-row .freeform-column-7{width:58.33333%}.freeform-row .freeform-column-8{width:66.66667%}.freeform-row .freeform-column-9{width:75%}.freeform-row .freeform-column-10{width:83.33333%}.freeform-row .freeform-column-11{width:91.66667%}.freeform-row .freeform-column-12{width:100%}.freeform-form-has-errors{color:red}</style>

	{{ form.renderTag }}

	{% if form.pages|length > 1 %}
		<ul class="freeform-pages">
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
	{% endif %}

	{% if form.hasErrors %}
		<div class="freeform-form-has-errors">
			{{ "There was an error submitting this form"|t }}
		</div>
	{% endif %}

	{% for row in form %}
		<div class="freeform-row {{ form.customAttributes.rowClass }}">
			{% for field in row %}
				{% set columnClass = "freeform-column " ~ form.customAttributes.columnClass %}
				{% set columnClass = columnClass ~ " freeform-column-" ~ (12 / (row|length)) %}
				{% if field.type == "submit" %}
					{% set columnClass = columnClass ~ " freeform-column-content-align-" ~ field.position %}
				{% endif %}
				<div class="{{ columnClass }}">
					{{ field.render({
						class: field.type != "submit" ? "freeform-input" : "",
						labelClass: "freeform-label" ~ (field.inputOnly ? " freeform-input-only-label" : "") ~ (field.required ? " freeform-required" : ""),
						errorClass: "freeform-errors",
					}) }}
				</div>
			{% endfor %}
		</div>
	{% endfor %}

	{{ form.renderClosingTag }}

## Flexbox <a href="#flexbox" id="flexbox" class="docs-anchor">#</a>

	<style>.freeform-pages{display:-webkit-box;display:-ms-flexbox;display:flex;padding:0;margin:0 0 10px;list-style:none}.freeform-pages li{margin:0 10px 0 0}.freeform-row{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;margin:0 -15px}.freeform-row .freeform-column{-webkit-box-flex:1;-ms-flex:1 0 0;flex:1 0 0;padding:10px 0;margin:0 15px;box-sizing:border-box}.freeform-row .freeform-column label{display:block}.freeform-row .freeform-column .freeform-label{font-weight:bold}.freeform-row .freeform-column .freeform-label.freeform-required:after{content:"*";margin-left:5px;color:red}.freeform-row .freeform-column .freeform-input{width:100%;display:block;box-sizing:border-box}.freeform-row .freeform-column .freeform-input[type=checkbox],.freeform-row .freeform-column .freeform-input[type=radio]{width:auto;display:inline;margin-right:5px}.freeform-row .freeform-column .freeform-input-only-label{font-weight:normal}.freeform-row .freeform-column .freeform-input-only-label>.freeform-input{display:inline-block;width:auto;margin-right:5px}.freeform-row .freeform-column .freeform-errors{list-style:none;padding:0;margin:5px 0 0}.freeform-row .freeform-column .freeform-errors>li{color:red}.freeform-row .freeform-column .freeform-instructions{margin:0 0 5px;font-size:13px;color:#ABA7A7}.freeform-row .freeform-column.freeform-column-content-align-left{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:start;-ms-flex-pack:start;justify-content:flex-start}.freeform-row .freeform-column.freeform-column-content-align-left>button:not(:first-of-type){margin-left:5px}.freeform-row .freeform-column.freeform-column-content-align-center{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center}.freeform-row .freeform-column.freeform-column-content-align-center>button:not(:first-of-type){margin-left:5px}.freeform-row .freeform-column.freeform-column-content-align-right{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:end;-ms-flex-pack:end;justify-content:flex-end}.freeform-row .freeform-column.freeform-column-content-align-right>button:not(:first-of-type){margin-left:5px}.freeform-row .freeform-column.freeform-column-content-align-spread{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between}.freeform-row .freeform-column.freeform-column-content-align-spread>button:not(:first-of-type){margin-left:5px}.freeform-form-has-errors{color:red}</style>

	{{ form.renderTag }}

	{% if form.pages|length > 1 %}
		<ul class="freeform-pages">
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
	{% endif %}

	{% if form.hasErrors %}
		<div class="freeform-form-has-errors">
			{{ "There was an error submitting this form"|t }}
		</div>
	{% endif %}

	{{ form.currentPage.label }}

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
