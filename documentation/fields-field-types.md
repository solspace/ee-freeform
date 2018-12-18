# Fields & Field Types

Freeform uses its own set of fields and field types. Using a predefined set of fields also gives us the control to more easily account for how each form field should be displayed in Composer's live preview, and provides a smoother experience. The *Freeform Express* edition has a limit of 15 fields.

**NOTE:** There is currently a limitation of 195 Freeform fields for each install of ExpressionEngine, due to the MySQL column limit, since all fields are stored in a single table. However, Freeform fields can be used across all forms, and even be relabelled for each form.

* [Fields Overview](#fields)
* [Field Types](#field-types)
	* [Text](#fields-text)
	* [Textarea](#fields-textarea)
	* [Email](#fields-email)
	* [Hidden](#fields-hidden)
	* [Select](#fields-select)
	* [Multiple Select](#fields-multiple-select)
	* [Checkbox](#fields-checkbox)
	* [Checkbox Group](#fields-checkbox-group)
	* [Radio Group](#fields-radio-group)
	* [File Upload](#fields-file-upload)
	* [Dynamic Recipients](#fields-dynamic-recipients)
* [Pro Field Types](#pro-field-types)
	* [Confirmation](#fields-confirmation)
	* [Date & Time](#fields-date-time)
	* [Number](#fields-number)
	* [Phone](#fields-phone)
	* [Rating](#fields-rating)
	* [Regex](#fields-regex)
	* [Website](#fields-website)
	* [reCAPTCHA](#fields-recaptcha)
* [Special Fields](#special-fields)
	* [Submit](#fields-submit)
	* [HTML](#fields-html)
	* [reCAPTCHA (Pro)](#fields-recaptcha)
* [Field Specific Properties](#field-specific-properties)
* [Populating Fields with Elements & Predefined Options](#feeders)
	* [Available Fields](#feeders-fields)
	* [Available Craft Elements](#feeders-elements)
	* [Available Predefined Options](#feeders-predefined)


## Fields Overview <a href="#fields" id="fields" class="docs-anchor">#</a>

Fields are global and available to all forms, but they can also be overwritten per form. This allows you to save time reusing existing fields when making other forms, but also gives you flexibility to make adjustments to them when needed. So to clarify, you can create fields with labels and options that are common to all forms, but also override those on each form. For example, if you have a field named **Cell Phone**, on the form level, you can rename the field name to **Mobile Phone**, or if you have a Checkbox Group field with options: **Option 1**, **Option 2**, and **Option 3**, you could override it to just have 2 options with values of **Option A** and **Option B**. When the fields are edited at global level (in main Fields area of Freeform control panel), your customizations per form will NOT be lost.

Fields can be created and managed in the main field creation area (**Freeform > Fields > New Field**) and can also be created directly within the *Composer* interface as well. Fields created here are available globally as well (they do not just exist for that form).

Some important notes:

* All field properties can be overwritten at form level inside Composer, including the field Handle.
* Once a field is created, you cannot change the field type after.
* Freeform will load fields of [Hidden](#fields-hidden) type at the beginning of the form, regardless of where they are placed in Composer layout.

[![Fields](images/cp_fields-list.png)](images/cp_fields-list.png)

[![Create a Field](images/cp_fields-create.png)](images/cp_fields-create.png)


## Field Types <a href="#field-types" id="field-types" class="docs-anchor">#</a>

The following field types are available ([see below for additional Pro Field Types](#pro-field-types)):

* **Text** (`text`) <a href="#fields-text" id="fields-text" class="docs-anchor">#</a>
	* A simple input field.
		* Can be marked as required.
		* Can contain default text and/or placeholder.
		* Can set `maxlength` property.
* **Textarea** (`textarea`) <a href="#fields-textarea" id="fields-textarea" class="docs-anchor">#</a>
	* A simple multi-line input field.
		* Specify the number of rows the textarea should have.
		* Can be marked as required.
		* Can contain default text and/or placeholder.
		* Can set `maxlength` property.
		* Will automatically convert newlines to line breaks when outputting data in templates or email notifications.
* **Email** (`email`) <a href="#fields-email" id="fields-email" class="docs-anchor">#</a>
	* An input field that is flagged in Freeform to expect an email address value as well as possibility for receiving email notifications.
		* In the Property Editor (right column) in Composer, select a notification template if you want the email entered for this field to receive an email notification.
		* To allow sending of email notifications to more than 1 email address (e.g. in the case of a "Tell-a-Friend" type form), you can add multiple input fields, each with the name `email[]`. This approach would require that you code this part manually however.
		* Required field type if you wish for your users to receive an email notification.
		* Required field type if you're using with a Mailing List API integration.
		* Can be marked as required.
		* Can contain default text and/or placeholder.
* **Hidden** (`hidden`) <a href="#fields-hidden" id="fields-hidden" class="docs-anchor">#</a>
	* A hidden field.
		* Can only include text strings at this time (no variables allowed).
			* If you need you pass a value to your hidden field dynamically, you can do so with the `override_values` parameter, e.g. `override_values:FIELD_NAME="myvalue"`
		* Cannot be marked as required.
		* Freeform will load fields of this type at the beginning of the form, regardless of where they are placed in Composer layout.
* **Select** (`select`) <a href="#fields-select" id="fields-select" class="docs-anchor">#</a>
	* A select dropdown menu field.
		* Can specify labels (with values assumed) or labels and values (that differ).
			* To make the first option empty, use **labels and values** approach with first option having **--** or **Please select...**, etc for the label, and leave option blank.
		* Can be automatically populated with select [EE Data or Pre-defined list options](#feeders).
		* Can be marked as required.
		* Can specify default option to be selected.
* **Multiple Select** (`multiple_select`) <a href="#fields-multiple-select" id="fields-multiple-select" class="docs-anchor">#</a>
	* A multiple select field.
		* Can specify labels (with values assumed) or labels and values (that differ).
		* Can be automatically populated with select [EE Data or Pre-defined list options](#feeders).
		* Can be marked as required.
		* Can specify default option(s) to be selected.
* **Checkbox** (`checkbox`) <a href="#fields-checkbox" id="fields-checkbox" class="docs-anchor">#</a>
	* A single checkbox field.
		* Has a default value of **Yes**, which can be overwritten with any value you want. The front end however, will always display the value as `1`, but upon submission, the value will be switched to the one you have set.
		* Can be marked as required, which would essentially require that this checkbox be checked.
		* Can be checked by default.
* **Checkbox Group** (`checkbox_group`) <a href="#fields-checkbox-group" id="fields-checkbox-group" class="docs-anchor">#</a>
	* A group of checkboxes.
		* Can specify labels (with values assumed) or labels and values (that differ).
		* Can be automatically populated with select [EE Data or Pre-defined list options](#feeders).
		* Can be marked as required.
		* Can specify which (if any) options to be checked by default.
* **Radio Group** (`radio_group`) <a href="#fields-radio-group" id="fields-radio-group" class="docs-anchor">#</a>
	* A group of radio options.
		* Can specify labels (with values assumed) or labels and values (that differ).
		* Can be automatically populated with select [EE Data or Pre-defined list options](#feeders).
		* Can be marked as required.
		* Can specify which (if any) option to be selected by default.
* **File Upload** (`file`) <a href="#fields-file-upload" id="fields-file-upload" class="docs-anchor">#</a>
	* A file upload field, using [EE File Uploads](https://docs.expressionengine.com/v3/add-ons/file/file_tag.html).
	* Can allow a single file or multiple files (applies `multiple` attribute to the single file upload input) to be uploaded.
		* Specify a number larger than `1` in the **File Count** setting to allow multiple files to be uploaded at the same time.
		* Must have an Upload Directory Preference where the file will be uploaded to.
		* Be sure that the EE Upload Directory's *Allowed file types?* preference is set to **All file types**, even if you're only using images.
		* Define maximum file size (in KB). Default is 2048 KB (2MB). Is subject to:
			* PHP [memory_limit](http://us3.php.net/manual/en/ini.core.php#ini.memory-limit)
			* PHP [post_max_size](http://us3.php.net/manual/en/ini.core.php#ini.post-max-size)
			* PHP [upload_max_filesize](http://us3.php.net/manual/en/ini.core.php#ini.upload-max-filesize)
		* Select which file types can be uploaded.
			* Leaving all options unchecked will allow ALL file types.
		* In [multi-page forms](multi-page-forms.md), if an earlier page contains file upload field(s), files will actually be uploaded before the form is officially submitted.
			* If the form is never completed, incomplete submissions are stored for 3hrs, and then are removed (along with the file(s)) after that.
		* Can be marked as required.
* **Dynamic Recipients** (`dynamic_recipients`) <a href="#fields-dynamic-recipients" id="fields-dynamic-recipients" class="docs-anchor">#</a>
	* A select dropdown menu field that contains protected email addresses and labels for each.
		* Can be switched to Radio or Checkbox group options at form level inside Composer.
		* Specify labels and email address values.
			* Emails are never parsed in source code (they're replaced with **0**, **1**, **2**, etc).
				* **NOTE:** When parsing this field semi-manually, be sure to use `{field:index}` to generate numeric values of options instead of `{field:value}`.

					```<select name="{field:department:handle}" type="dynamic_recipients">
					{field:department:options}
						<option value="{option:index}" {if option:checked}selected{/if}>
							{option:label}
						</option>
					{/field:department:options}
					</select>```

			* To make the first option empty, specify `--` or `Please select...`, etc for the label, and leave option blank.
		* In the Property Editor (right column) in Composer, select a notification template you want the selected recipient for this field to receive.
			* Users/groups need to have permissions access for **Email Notifications** to create new formatting templates.
		* Can be marked as required.
		* Can specify default option(s) to be selected.
		* Can specify 1 or more recipient options at a time (when using as Checkboxes).
		* Multiple email addresses can be specified for each option, separated by commas.
		* Can include more than 1 of this field type in your forms, allowing for multiple sets of recipients to be notified.


## Pro Field Types <a href="#pro-field-types" id="pro-field-types" class="docs-anchor">#</a>

The following extra field types are available with Freeform Pro:

* **Confirmation** <a href="#fields-confirmation" id="fields-confirmation" class="docs-anchor">#</a>
	* Allows you to force a user to enter a matching value for another field (e.g. "Confirm Email Address").
		* Select a target field to compare with.
		* Can be marked as required.
		* Can contain default text and/or placeholder.

[![Confirmation fieldtype](images/cp_field-confirm.png)](images/cp_field-confirm.png)

* **Date & Time** <a href="#fields-date-time" id="fields-date-time" class="docs-anchor">#</a>
	* A complex date and/or time field. Can be used as Date only, Time only, or both. Many configuration and validation options available as well:
		* Set a default value.
			* Can use `now`, `today`, `5 days ago`, `2017-01-01 20:00:00`, etc, which will format the default value according to the chosen format as a localized value.
		* Select if the field should use the default Freeform datepicker.
			* Can include your own manually in the template if you wish.
		* Generate a placeholder from your date format settings showing the accepted format.
			* Can include your own placeholder if you wish.
		* Date Order - the formatting order you'd like. Options are:
			* Year month day
			* Month day year
			* Day month year
		* Select if the year should be displayed/validated as 4 digits.
		* Select if the day and month numbers should have a leading `0` for single digit values (e.g. August will display as `08` instead of `8`).
		* Date separator - the character used between each year, month, day value:
			* None
			* Space (` `)
			* `/`
			* `-`
			* `.`
		* Select if time and datepicker should use 24 hour clock.
		* Clock separator - the character used to separate hours and minutes:
			* None
			* Space (` `)
			* `:`
			* `-`
			* `.`
		* Choose if placeholder should display lowercase AM/PM (for 12hr clock).
		* Choose if placeholder should separate AM/PM with a space (for 12hr clock).
		* Can be marked as required.

[![Date & Time fieldtype](images/cp_field-datetime.png)](images/cp_field-datetime.png)

* **Number** <a href="#fields-number" id="fields-number" class="docs-anchor">#</a>
	* An input field that is validated to contain numbers only, based on several configuration options.
		* Choose if validation should allow negative numbers.
		* Optionally set Min/Max values.
			* Both are optional, you can have both, just one or neither.
		* Optionally set Min/Max character length.
			* Both are optional, you can have both, just one or neither.
		* Set the number of decimals allowed.
		* Decimal Separator - the character used to separate decimals:
			* `.`
			* `,`
		* Thousands Separator - the character used to separate thousands:
			* None
			* Space (` `)
			* `,`
			* `.`
		* Can be marked as required.
		* Can contain default text and/or placeholder.

[![Number fieldtype](images/cp_field-number.png)](images/cp_field-number.png)

* **Phone** <a href="#fields-phone" id="fields-phone" class="docs-anchor">#</a>
	* An input field that is validated to contain phone numbers only, based on pattern configured.
		* Set pattern to desired format, where `x` is a digit between `0` and `9`, e.g:
			* `(xxx) xxx xxxx`
			* `xxx-xxx-xxxx`
			* If no pattern specified, Freeform will default to a universal phone number validation pattern.
		* Can be marked as required.
		* Can contain default text and/or placeholder.

[![Phone fieldtype](images/cp_field-phone.png)](images/cp_field-phone.png)

* **Rating** <a href="#fields-rating" id="fields-rating" class="docs-anchor">#</a>
	* A special field that allows for star ratings using Freeform's built in CSS and JS.
		* Set a default star rating value (based on Maximum Number of Stars configuration option)
		* Set the maximum number of stars allowed.
		* Select an "Unselected" display color.
		* Select a "Hover" display color.
		* Select a "Selected" display color.
		* Can be marked as required.

[![Rating fieldtype](images/cp_field-rating.png)](images/cp_field-rating.png)

* **Regex** <a href="#fields-regex" id="fields-regex" class="docs-anchor">#</a>
	* An input field that is validated based on the specified regex pattern (e.g. `/^[a-zA-Z0-9]*$/`).
		* Set error message a user will see if an incorrect value is supplied.
			* Any occurrences of `{{pattern}}` will be replaced with specified regex pattern inside the error message, if any are found.
		* Can be marked as required.
		* Can contain default text and/or placeholder.

[![Regex fieldtype](images/cp_field-regex.png)](images/cp_field-regex.png)

* **Website** <a href="#fields-website" id="fields-website" class="docs-anchor">#</a>
	* A simple input field that checks to see if the URL specified has valid syntax (`http://`, `https://`, `ftp://`, etc).
		* Can be marked as required.
		* Can contain default text and/or placeholder.

[![Website fieldtype](images/cp_field-website.png)](images/cp_field-website.png)


## Special Fields <a href="#special-fields" id="special-fields" class="docs-anchor">#</a>

Special fields are ones that either every form needs (such as Submit button) and ones that aid in the layout and content of forms (such as the HTML block). None of these fields store any submission data in the database. The following special fields are available to use:

* **Submit** <a href="#fields-submit" id="fields-submit" class="docs-anchor">#</a>
	* Settings allow you to edit the button label.
	* You may adjust the positioning of the submit button:
		* Aligned to Left
		* Aligned to Center
		* Aligned to Right
	* When using with multi-page forms, Freeform will detect when you're on a page after first page, and provide you with additional options:
		* It will include a Previous button by default, allowing the user to go back to previous pages in the form.
			* Previous button can be disabled.
		* Positioning options now include:
			* Apart at Left and Right
			* Together at Left
			* Together at Center
			* Together at Right
	* You may include 1 per page in your form.
* **HTML** <a href="#fields-html" id="fields-html" class="docs-anchor">#</a>
	* Property Editor will load an HTML area for you to type or paste your code into.
	* Layout column will live parse your HTML.
	* All HTML is allowed here.
	* You can include as many of these in your form as you wish.
* **reCAPTCHA** (Pro) <a href="#fields-recaptcha" id="fields-recaptcha" class="docs-anchor">#</a>
	* Built in support for [reCAPTCHA](https://www.google.com/recaptcha).
	* See [reCAPTCHA documentation](spam-protection.md#recaptcha) for more information.
	* *reCAPTCHA* fields will render automatically like the rest of your fields in your form, but if you're building a form manually, you'd call it like this (using the Hash value for *reCAPTCHA* field in Property Editor of Composer):


## Field Specific Properties <a href="#field-specific-properties" id="field-specific-properties" class="docs-anchor">#</a>

The following are field specific properties for the basic and Pro field types:

* `checkbox` <a href="#field-checkbox" id="field-checkbox" class="docs-anchor">#</a>
	* Has a default value of **Yes**, which can be overwritten with any value you want. The front end however, will always display the value as `1`, but upon submission, the value will be switched to the one you have set.
	* `{field:checked}` - will display whether the field is checked by default.
* `dynamic_recipients` <a href="#field-dynamic_recipients" id="field-dynamic_recipients" class="docs-anchor">#</a>
	* `{field:show_as_radio}`
		* A boolean value. If `true` the dynamic recipients field should be rendered as radio buttons instead of a select field.
* `datetime` <a href="#field-datetime" id="field-datetime" class="docs-anchor">#</a>
	* `{field:date_time_type}` (e.g. `both`)
	* `{field:generate_placeholder}` (e.g. `true`)
	* `{field:date_order}` (e.g. `ymd`)
	* `{field:date_4_digit_year}` (e.g. `true`)
	* `{field:date_leading_zero}` (e.g. `true`)
	* `{field:date_separator}` (e.g. `/`)
	* `{field:clock_24h}` (e.g. `false`)
	* `{field:lowercase_ampm}` (e.g. `true`)
	* `{field:clock_separator}` (e.g. `:`)
	* `{field:clock_ampm_separate}` (e.g. `true`)
	* `{field:use_datepicker}` (e.g. `true`)
* `number` <a href="#field-number" id="field-number" class="docs-anchor">#</a>
	* `{field:min_length}`
	* `{field:max_length}`
	* `{field:min_value}`
	* `{field:max_value}`
	* `{field:decimal_count}`
	* `{field:decimal_separator}` (e.g. `.`)
	* `{field:thousands_separator}` (e.g. `,`)
	* `{field:allow_negative}` (e.g. `false`)
* `phone` <a href="#field-phone" id="field-phone" class="docs-anchor">#</a>
	* `{field:pattern}`
* `rating` <a href="#field-rating" id="field-rating" class="docs-anchor">#</a>
	* `{field:max_value}` (e.g. `5`)
	* `{field:color_idle}` (e.g. `#dddddd`)
	* `{field:color_hover}` (e.g. `#ffd700`)
	* `{field:color_selected}` (e.g. `#77ff00`)
* `regex` <a href="#field-regex" id="field-regex" class="docs-anchor">#</a>
	* `{field:pattern}`
* `submit` <a href="#field-submit" id="field-submit" class="docs-anchor">#</a>
	* `{field:label_next}`
		* A label for the **Next** button. `Submit` by default.
	* `{field:label_prev}`
		* A label for the **Previous** button. `Previous` by default.
	* `{field:disable_prev}`
		* A boolean value. If `true` the **Previous** button should not be rendered.


## Populating Fields with EE Data & Predefined Options <a href="#feeders" id="feeders" class="docs-anchor">#</a>

Inside Composer (only), basic field types with options have the ability to be automatically fed options from EE Data or Predefined Options. This allows you to quickly build forms by having fields auto-generated.

### Available Fields <a href="#feeders-fields" id="feeders-fields" class="docs-anchor">#</a>
The following field types can be auto-populated:

* [Select](#fields-select)
	* Optional **Empty Option Label** to have first option be something like `Please select...`
* [Multiple Select](#fields-multiple-select)
* [Checkbox Group](#fields-checkbox-group)
* [Radio Group](#fields-radio-group)

### Available EE Data types <a href="#feeders-ee-data" id="feeders-elements" class="docs-anchor">#</a>
The following EE Data types can be fed to the above field types:

* **[EE Entries](https://docs.expressionengine.com/latest/cp/publish/edit.html)**
	* **Target** channel or all channels.
	* **Option Label** and **Option Value** choices:
		* ID
		* Title
		* URL Title
		* Fields (simple values)
* **[EE Members](https://docs.expressionengine.com/latest/member/index.html)**
	* **Target** member group or all groups.
	* **Option Label** and **Option Value** choices:
		* ID
		* Username
		* Screen Name
		* Email
		* Fields (simple values)
* **[EE Categories](https://docs.expressionengine.com/latest/cp/channel/cat/index.html)**
	* **Target** category groups or all groups.
	* **Option Label** and **Option Value** choices:
		* ID
		* Title
		* URL Title
		* Fields (simple values)

### Available Predefined Options <a href="#feeders-predefined" id="feeders-predefined" class="docs-anchor">#</a>
The following Freeform predefined options can be fed to the above field types:

* **States**
	* Official USA States
	* **Option Label** and **Option Value** choices:
		* Full
		* Abbreviated (upper case 2 letters)
* **States & Territories**
	* Official USA States and territories
	* **Option Label** and **Option Value** choices:
		* Full
		* Abbreviated (upper case 2 letters)
* **Provinces**
	* Canadian Provinces and territories
	* **Option Label** and **Option Value** choices:
		* Full
		* Abbreviated (upper case 2 letters)
* **Countries**
	* All world countries
	* **Option Label** and **Option Value** choices:
		* Full
		* Abbreviated (upper case 2 letters)
* **Languages**
	* All world languages
	* **Option Label** and **Option Value** choices:
		* Full
		* Abbreviated (lower case 2 letters)
* **Numbers** (range)
	* A custom range of numbers
	* **Range Start** and **Range End**
		* E.g. `60` - `65` would return list: `60, 61, 62, 63, 64, 65`
* **Years** (range)
	* A custom range of years
	* **Range Start** - number of years in PAST from current year
	* **Range End** - number of years in FUTURE from current year
		* E.g. `5` (start) - `0` (end) would return list: `2018, 2017, 2016, 2015, 2014, 2013`
	* **Sort Direction**:
		* Ascending
		* Descending
* **Months**
	* All 12 months of the year.
	* **Option Label** and **Option Value** choices:
		* Full, e.g. `September`
		* Abbreviated (Capitalized 3 letters), e.g. `Sep`
		* Single Number, e.g. `9`
		* 2-digit Number, e.g. `09`
* **Days**
	* List of days `1` to `31`.
	* **Option Label** and **Option Value** choices:
		* Single Number, e.g. `3`
		* 2-digit Number, e.g. `03`
* **Days of Week**
	* List of all days of week.
	* **Option Label** and **Option Value** choices:
		* Full, e.g. `Thursday`
		* Abbreviated (Capitalized 3 letters), e.g. `Thu`
		* Single Number, e.g. `4`
