# Formatting Template Examples

Freeform includes several example [formatting templates](formatting-templates.md) to choose from. You can use these as a starting point and adjust them to suit your needs, or simply create your own from scratch. The following formatting templates are located in the **/freeform_next/Templates/form/** directory. If you wish to create your own modified version, just copy the code below, or the template file (ex: **foundation.html**) and paste it into your EE Templates directory or template in the EE Template Manager.

* [Bootstrap](#bootstrap)
* [Foundation](#foundation)
* [Materialize](#materialize)
* [Grid](#grid)
* [Flexbox](#flexbox)


## Bootstrap <a href="#bootstrap" id="bootstrap" class="docs-anchor">#</a>

The following example assumes you're including necessary [Bootstrap JS and CSS](http://getbootstrap.com).

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
		</div>
	{/if}

	{rows}
		<div class="row {form:row_class}">
		{fields}
			<div class="{form:column_class} col-xs-12 col-lg-{column:grid_width} {if field:type == 'checkbox' OR field:type == 'mailing_list'}checkbox{if:else}form-group{/if}{if field:has_errors} has-error{/if}{if field:type == 'submit'} submit-align-{field:position}{/if}">
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

			{if:elseif field:type == 'radio_group' OR (field:type == 'dynamic_recipients' AND field:show_as_radio)}

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


## Foundation <a href="#foundation" id="foundation" class="docs-anchor">#</a>

The following example assumes you're including necessary [Foundation JS and CSS](https://foundation.zurb.com).

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

	{if form:page_count > 1}
		<ul class="menu pagemenu">
		{pages}
			<li class="{if page:index == current_page:index}active{/if}">
				<a href="javascript:;"{if page:index == current_page:index} class="is-active"{/if}>
					{page:label}
				</a>
			</li>
		{/pages}
		</ul>
	{/if}

	{if form:has_errors}
		<div class="callout alert">
			There was an error submitting this form
		</div>
	{/if}

	{rows}
		<div class="row {form:row_class}">
		{fields}
			<div class="{form:column_class} medium-{column:grid_width} columns {if field:type == 'checkbox' OR field:type == 'mailing_list'}checkbox{if:else}form-group{/if}{if field:has_errors} has-error{/if}{if field:type == 'submit'} submit submit-align-{field:position}{/if}">
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

			{if:elseif field:type == 'radio_group' OR (field:type == 'dynamic_recipients' AND field:show_as_radio)}

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

			{if:elseif field:type == 'submit'}

				{field:render}

			{if:else}

				{field:render
					class="{if field:type == 'checkbox' OR field:type == 'mailing_list'}checkbox{if:else}form-control{/if}"
					label_class="{if field:required}required{/if}"
					instructions_class="help-text"
					instructions_below_field="yes"
				}

			{/if}
			</div>
		{/fields}
		</div>
	{/rows}


## Materialize <a href="#materialize" id="materialize" class="docs-anchor">#</a>

The following example assumes you're including necessary [Materialize JS and CSS](http://materializecss.com).

	<style>.brand-logo{margin-left:20px}label.required:after{display:inline-block!important;content:"*"!important;color:#d00!important;margin-left:5px!important;position:relative!important;top:0!important;opacity:1!important}.errors,ul.errors>li{color:red}.submit-align-left{text-align:left}.submit-align-right{text-align:right}.submit-align-center{text-align:center}.submit-align-center button:not(:first-of-type),.submit-align-left button:not(:first-of-type),.submit-align-right button:not(:first-of-type){margin-left:5px}.submit-align-spread button:first-child{float:left}.submit-align-spread button:last-child{float:right}</style>

	{if form:page_count > 1}
		<ul class="pagination">
			{pages}
				<li class="{if page:index == current_page:index}active{if:else}disabled{/if}">
					<a href="javascript:;">{page:label}</a>
				</li>
			{/pages}
		</ul>
	{/if}

	{if form:has_errors}
		<div class="alert alert-danger errors">
			There was an error submitting this form
		</div>
	{/if}

	{rows}
		<div class="row {form:row_class}">
			{fields}

				{if field:type == 'checkbox_group'}
					<div class="{form:column_class} s12 col m{column:grid_width}{if field:has_errors} has-error{/if}" style="margin-bottom: 20px;">
						{field:render_label label_class="{if field:required}required{/if}"}
						{field:options}
							<p>
								<input type="checkbox"
									   id="{form:hash}{field:handle}{option:value}"
									   name="{field:handle}[]"
									   value="{option:value}"
									   {if option:checked}checked{/if}
								/>
								<label for="{form:hash}{field:handle}{option:value}">{option:label}</label>
							</p>
						{/field:options}
						{field:render_instructions}
						{field:render_errors}
					</div>
				{if:elseif field:type == 'radio_group' OR (field:type == 'dynamic_recipients' AND field:show_as_radio)}

					<div class="{form:column_class} s12 col m{column:grid_width}{if field:has_errors} has-error{/if}" style="margin-bottom: 20px;">
						{field:render_label label_class="{if field:required}required{/if}"}
						{field:options}
								<p>
									<input type="radio"
										   id="{form:hash}{field:handle}{option:value}"
										   name="{field:handle}"
										   value="{option:value}"
										   {if option:checked}checked{/if}
									/>
									<label for="{form:hash}{field:handle}{option:value}">{option:label}</label>
								</p>
						{/field:options}
						{field:render_instructions}
						{field:render_errors}
					</div>

				{if:elseif field:type == 'textarea'}

					<div class="{form:column_class} s12 col m{column:grid_width}">
						{field:render class="materialize-textarea"}
					</div>

				{if:elseif field:type == 'checkbox'}

					<div class="{form:column_class} s12 col m{column:grid_width}">
						{field:render_input}
						{field:render_label
							instructions_class="help-block"
							error_class="help-block"
						}
						{field:render_instructions}
						{field:render_errors}
					</div>

				{if:elseif field:type == 'mailing_list'}

					<div class="{form:column_class} s12 col m{column:grid_width}">
						{field:render_input}
						{field:render_label
							instructions_class="help-block"
							error_class="help-block"
						}
						{field:render_instructions}
						{field:render_errors}
					</div>

				{if:elseif field:type == 'file'}

					<div class="{form:column_class} file-field input-field s12 col m{column:grid_width}">
						<div class="btn">
							<span>File</span>
							{field:render_input}
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate"
								   type="text"
								   placeholder="{field:label}"
							/>
						</div>

						{field:render_instructions}
						{field:render_errors}
					</div>

				{if:elseif field:type == 'html'}

					<div class="{form:column_class} s12 col m{column:grid_width}">
						{field:render_input}
					</div>

				{if:elseif field:type == 'submit'}

					<div class="{form:column_class} s12 col m{column:grid_width} submit-align-{field:position}">
						{field:render}
					</div>

				{if:else}

					<div class="{form:column_class} s12 col m{column:grid_width} input-field{if field:has_errors} has-error{/if}{if field:type == 'submit'} submit-align-{field:position}{/if}">
						{field:render_input}
						{field:render_label label_class="{if field:required}required{/if}"}

						{field:render_instructions instructions_class="help-block"}
						{field:render_errors error_class="help-block"}
					</div>
				{/if}
			{/fields}
		</div>
	{/rows}


## Grid <a href="#grid" id="grid" class="docs-anchor">#</a>

	<style>.freeform-pages{padding:0;margin:0 0 10px;list-style:none}.freeform-pages:after{content:"";display:table;clear:both}.freeform-pages li{float:left;margin:0 10px 0 0}.freeform-row{display:block;margin:0 -15px}.freeform-row:after{content:"";display:table;clear:both}.freeform-row .freeform-column{display:block;padding:10px 15px;float:left;box-sizing:border-box}.freeform-row .freeform-column:after{content:"";display:table;clear:both}.freeform-row .freeform-column label{display:block}.freeform-row .freeform-column .freeform-label{font-weight:bold}.freeform-row .freeform-column .freeform-label.freeform-required:after{content:"*";margin-left:5px;color:red}.freeform-row .freeform-column .freeform-input{width:100%;display:block;box-sizing:border-box}.freeform-row .freeform-column .freeform-input[type=checkbox],.freeform-row .freeform-column .freeform-input[type=radio]{width:auto;display:inline;margin-right:5px}.freeform-row .freeform-column .freeform-input-only-label{font-weight:normal}.freeform-row .freeform-column .freeform-input-only-label>.freeform-input{display:inline-block;width:auto;margin-right:5px}.freeform-row .freeform-column .freeform-errors{list-style:none;padding:0;margin:5px 0 0}.freeform-row .freeform-column .freeform-errors>li{color:red}.freeform-row .freeform-column .freeform-instructions{margin:0 0 5px;font-size:13px;color:#ABA7A7}.freeform-row .freeform-column.freeform-column-content-align-left{text-align:left}.freeform-row .freeform-column.freeform-column-content-align-left button:not(:first-of-type){margin-left:5px}.freeform-row .freeform-column.freeform-column-content-align-center{text-align:center}.freeform-row .freeform-column.freeform-column-content-align-center button:not(:first-of-type){margin-left:5px}.freeform-row .freeform-column.freeform-column-content-align-right{text-align:right}.freeform-row .freeform-column.freeform-column-content-align-right button:not(:first-of-type){margin-left:5px}.freeform-row .freeform-column.freeform-column-content-align-spread button:first-child{float:left}.freeform-row .freeform-column.freeform-column-content-align-spread button:last-child{float:right}.freeform-row .freeform-column-1{width:8.33333%}.freeform-row .freeform-column-2{width:16.66667%}.freeform-row .freeform-column-3{width:25%}.freeform-row .freeform-column-4{width:33.33333%}.freeform-row .freeform-column-5{width:41.66667%}.freeform-row .freeform-column-6{width:50%}.freeform-row .freeform-column-7{width:58.33333%}.freeform-row .freeform-column-8{width:66.66667%}.freeform-row .freeform-column-9{width:75%}.freeform-row .freeform-column-10{width:83.33333%}.freeform-row .freeform-column-11{width:91.66667%}.freeform-row .freeform-column-12{width:100%}.freeform-form-has-errors{color:red}</style>

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
			<div class="freeform-column {form:column_class} freeform-column-{column:grid_width} {if field:type == 'submit'} freeform-column-content-align-{field:position}{/if}">
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


## Flexbox <a href="#flexbox" id="flexbox" class="docs-anchor">#</a>

	<style>.freeform-pages{display:-webkit-box;display:-ms-flexbox;display:flex;padding:0;margin:0 0 10px;list-style:none}.freeform-pages li{margin:0 10px 0 0}.freeform-row{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;margin:0 -15px}.freeform-row .freeform-column{-webkit-box-flex:1;-ms-flex:1 0 0;flex:1 0 0;padding:10px 0;margin:0 15px;box-sizing:border-box}.freeform-row .freeform-column label{display:block}.freeform-row .freeform-column .freeform-label{font-weight:bold}.freeform-row .freeform-column .freeform-label.freeform-required:after{content:"*";margin-left:5px;color:red}.freeform-row .freeform-column .freeform-input{width:100%;display:block;box-sizing:border-box}.freeform-row .freeform-column .freeform-input[type=checkbox],.freeform-row .freeform-column .freeform-input[type=radio]{width:auto;display:inline;margin-right:5px}.freeform-row .freeform-column .freeform-input-only-label{font-weight:normal}.freeform-row .freeform-column .freeform-input-only-label>.freeform-input{display:inline-block;width:auto;margin-right:5px}.freeform-row .freeform-column .freeform-errors{list-style:none;padding:0;margin:5px 0 0}.freeform-row .freeform-column .freeform-errors>li{color:red}.freeform-row .freeform-column .freeform-instructions{margin:0 0 5px;font-size:13px;color:#ABA7A7}.freeform-row .freeform-column.freeform-column-content-align-left{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:start;-ms-flex-pack:start;justify-content:flex-start}.freeform-row .freeform-column.freeform-column-content-align-left>button:not(:first-of-type){margin-left:5px}.freeform-row .freeform-column.freeform-column-content-align-center{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center}.freeform-row .freeform-column.freeform-column-content-align-center>button:not(:first-of-type){margin-left:5px}.freeform-row .freeform-column.freeform-column-content-align-right{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:end;-ms-flex-pack:end;justify-content:flex-end}.freeform-row .freeform-column.freeform-column-content-align-right>button:not(:first-of-type){margin-left:5px}.freeform-row .freeform-column.freeform-column-content-align-spread{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between}.freeform-row .freeform-column.freeform-column-content-align-spread>button:not(:first-of-type){margin-left:5px}.freeform-form-has-errors{color:red}</style>

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
