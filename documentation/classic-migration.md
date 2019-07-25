# Freeform Classic Migration Utility
It's important to note that the new Freeform is not just merely an update or even an overhaul of classic Freeform. It was completely rewritten from scratch, and built without attempting to conform to legacy requirements. Because of this, the new Freeform is a completely different add-on with its own namespace / folder name, allowing you to install it alongside Freeform Classic. In order to migrate your Classic data, you'll have to use this migration utility. And because not everything is the same in the new Freeform, not everything can be migrated. This page will go into details on what to expect, and how you might need to prep your site before attempting migration, and then what to clean up afterwards.

> **NOTE:** You must have the appropriate EE-compatible version of Freeform Classic installed and upgraded for the new Freeform to properly detect and migrate it. For example, if you moved from EE2 to EE4, but only have the EE2 version of Freeform Classic (4.x) installed, the migration won't work correctly. You'll need to update to the 5.x (EE3) or 6.x (EE4/EE5) version of Freeform Classic first. If you're switching from Classic to the new Freeform, we don't expect you to pay an upgrade fee to access the EE3 or EE4 compatible versions of Classic to only use it for 15 mins to run the migration utility, so feel free to temporarily "borrow and reuse" a newer Classic license from another site if you have, or [contact us for a temporary copy](http://docs.solspace.com/expressionengine/freeform/v1/support.html).

* [Quick Overview of Limitations](#limitations)
* [Fields](#fields)
* [Email Notification Templates](#notifications)
* [Formatting Templates](#formatting-templates)
* [Statuses](#statuses)
* [Forms](#forms)
* [Submissions](#submissions)
* [Preferences](#preferences)
* [Permissions](#permissions)
* [Clean-up](#cleanup)


## Quick Overview of Limitations <a href="#limitations" id="limitations" class="docs-anchor">#</a>
We figured it'd be helpful to place some of the large limitations of the migration utility at the start, so you could quickly see if migration is currently an option for you.

* Your new Freeform install needs to be fresh and untouched. The migration will overwrite any of your existing Freeform data. If you have modified your new Freeform install (created fields, etc) and you don't mind losing the data, you can reinstall the new Freeform to run the migration.
* Classic Freeform fields that are loaded with Channel Entry data will be migrated over as empty fields, as support for this does not yet exist in the new Freeform.
* Manually built EE Template based forms will be loaded into a new Composer-based form for the new Freeform, as all forms are now setup inside Composer.
* Composer Templates (formatting templates for Composer-based forms) in Classic will not be migrated over. The new Freeform has these as well, but the templating syntax is much more improved and very different.
* File Upload fields with *multiple files* will be converted to having a single file upload input that can select multiple files at once (rather than showing multiple inputs).
* All associated *member data* in Classic will not be migrated over, as the new Freeform does not currently store any of that information.
* Currently not compatible with Multiple Site Manager (MSM). The migration will only migrate Classic data from Site 1. A future version might allow MSM compatibility.
* Freeform Express edition has a limitation of 1 form and up to 15 fields. If you're migrating from Classic to Express, you'll need to prepare your Classic site to only have 1 form and no more than 15 fields, or otherwise consider using Freeform Lite edition instead.
* If something goes wrong with the migration, it will only affect your new Freeform install. This means it's fairly low risk to attempt the migration, as a botched migration can be resolved by reinstalling the new Freeform and trying again (perhaps after a bug fix, or something in Classic adjusted).


## Fields <a href="#fields" id="fields" class="docs-anchor">#</a>
All fields will be attempted to be migrated over to the appropriate field equivalent. However, there will be some exceptions to this as follows:

* **Email** fields: the new Freeform contains a special Email fieldtype that is like regular input fields, but includes an option to assign an email notification to it. The following Classic fields will be converted to Email fields (and cannot be undone):
	* All fields that contain the string `email` in it's label or short name.
	* All **Text** fields that contain the *Field Content Type* of *Email*.
	* **NOTE:** If you want to make sure fields are designated as *Email* fieldtype, be sure they fall into the options listed above. If you do NOT want some fields to be converted to *Email* fieldtype, be sure to rename (temporarily for migration) or reassign your fields' *Field Content Type*.
* **Multi-select** fields: initially there was no multi-select fieldtype in the new Freeform. As for Freeform 1.7+, the Multi-select fieldtype is available again. However, the migration does not yet account for this...
	* All Classic *Multi-select* fields will be converted to *Checkbox Groups*.
* **Country** / **State** / **Province** Select fields: there are none of these in the new Freeform.
	* All fields of this type will be converted to Select fields with those existing data options populated as their options.
	* As of Freeform 1.7+, field option 'Data Feeders' for Checkbox group, Radio group, Select and Multi-select fieldtypes are available. You can now populate these fields with Entries, Categories, Members, or one of our many predefined options: States, Provinces, Countries, Languages, Number ranges, Year ranges, Months, Days and Days of the Week. Freeform Data Feeders also offer flexible control over formatting and/or which data fills option labels and option values. The migration will NOT automatically convert your existing fields to this new style.

The following mapping of data will happen for each field during migration (*Classic* -> **New Freeform**):

* *Field type* -> **Type**
* *Field Label* -> **Label**
* *Field Name* -> **Handle**
* *Description* -> [IGNORED]
* *Show field on submissions CP page?* -> [IGNORED]
* *Show field on moderation CP page?* -> [IGNORED]
* *Allow field to be used in Freeform Composer?* -> [IGNORED]
* *Add To Form(s)* -> [IGNORED]
* Field type Settings:
	* Classic allows a variety of ways to place the data for options:
		* *List (labels only)* -> **List of Labels**
		* *Value/Label List* -> **List of Labels and Options**
		* *Load from Channel Field* -> [IGNORED]
		* *Newline Delimited Textarea* -> **List of Labels**
	* **File Upload** fields:
		* *Allowed upload count* -> **File Count**
		* *File Upload Location* -> **Upload Directory**
		* *Allowed File Types** (string) -> **Allowed File Types** (checkboxes)
		* *Overwrite On Edit* -> [IGNORED]
		* *Disable XSS Clean* -> [IGNORED]


## Email Notification Templates <a href="#notifications" id="notifications" class="docs-anchor">#</a>
Email Notification templates are different in the new Freeform, but the migration will bring them over, attempt to do some basic syntax updating, and attempt to map notification templates to forms (for Admin notifications). Be prepared to check this over after and update as necessary.

The following mapping of data will happen for each Email Notification Template during migration (*Classic* -> **New Freeform**):

* *Label* -> **Name**
* *Name* (aka "short_name") -> **Handle**
* *Description* -> **Description**
* *Subject* -> **Subject**
* *From Name* -> **From Name**
* *From Email* -> **From Email**
* *Reply To Email* -> **Reply-to Email**
* *Include Attachments* -> **Include Attachments**
* *Word Wrap* -> [IGNORED]
* *Allow HTML* -> [IGNORED] (all emails are HTML)
* *Template Data* -> **Email Body**
	* Migration will attempt to make some basic string find/replace, but you'll want to check things over after the migration:
		* `{all_form_fields}{/all_form_fields}` -> `{form:fields}{/form:fields}`
		* `{field_label}` -> `{field:label}`
		* `{field_data}` -> `{field:value}`
		* `{entry_date format=""}` -> `{date_created format=""}`
		* `{freeform_entry_id}` -> `{submission:id}`
		* `{form_label}` -> `{form:name}`
		* `{form_name}` -> `{form:handle}`
		* `{form_id}` -> `{form:id}`


## Formatting Templates <a href="#formatting-templates" id="formatting-templates" class="docs-anchor">#</a>
Formatting templates, or *Composer Templates* as they're called in Classic, are all stored in database, whereas the new Freeform stores only in HTML files. The template code is much more improvement and very different, so these will be excluded from migration and need to be rebuilt. The new Freeform includes several sample formatting templates including popular HTML frameworks to get you started.


## Statuses <a href="#statuses" id="statuses" class="docs-anchor">#</a>
Classic stores statuses in the Preferences page, whereas the new Freeform has an interface for creating and editing them (including colors). Any additional statuses (if present) in Classic preferences will be migrated over:

* *Classic [status_name]* -> mapped to **Name** and **Handle**, with gray color assigned.
	* If you're switching to Freeform Express, custom statuses are not available, but the migration will grandfather in any custom statuses you have. You wont however, be able to edit/remove them, so you might want to consider removing these from Classic before running the migration.


## Forms <a href="#forms" id="forms" class="docs-anchor">#</a>
The biggest improvement to the new Freeform is the way forms are handled and the intuitive Composer interface. Freeform Classic allowed *Composer*-based forms and *EE Template*-based forms, whereas the new Freeform only offers *Composer*-based forms (though you can similarly built very manual forms if you still wish to). Because of this, the migration has a little more work to do, but it should get most of the work done for you.

Freeform Classic manual *EE Template*-based forms still have to have fields assigned to the form, so the Migration utility will know which fields to grab and assign to forms (and place into Composer). The following mapping of data will happen for each Form during migration (*Classic* -> **New Freeform**):

* *Form Type* -> [IGNORED]
* *Form Label* -> **Name**
* *Form Name* -> **Handle**
* *Description* -> **Description**
* *Default Status* -> **Default Status**
* *Notify User* -> [IGNORED] (now applied directly to **Email** fieldtype instead)
* *User Email Field* -> [IGNORED]
* *User Email Notification Template* -> [IGNORED]
* *Notify Admin* -> if YES, attempt to connect form the options below:
	* *Admin Notification Template* -> **Email Template**
	* *Admin Email Notification Email Address* -> **Admin Recipients**
* *Form Fields* (if EE Template-based) -> **Inserted into Composer layout** in order of how fields were set in Classic.
	* Migration adds a **Submit button** at the end of the Composer form.
	* Fields can of course be rearrange later as desired.
* *Form Fields in Composer interface* (if Composer-based) -> **Inserted into Composer layout** in position order from Classic.
	* Rows and columns will attempt to be respected.
	* *Dynamic Recipients* and *User Recipients* fields -> [IGNORED] (due to how things are different in new Freeform - will have to be recreated.
	* *Title block* in Composer -> [IGNORED]
	* *Paragraph block* in Composer -> Converted to **HTML block**
	* *Page breaks* in Classic -> Converted to **Page tabs**
		* If you're using Freeform Express, it does not allow multi-page forms, but multi-page forms will be grandfathered in during migration. However, while you can remove grandfathered pages in Express edition, you cannot add new ones.
* *View Freeform Entry URL* -> [IGNORED]


## Submissions <a href="#submissions" id="submissions" class="docs-anchor">#</a>
Submissions can be migrated from Classic as well. However, this is an optional step. If you wish to have your submissions migrated to the new Freeform, be sure to check the **Migrate Submissions** checkbox on the Migration utility page.

All field data will be mapped over. Some exceptions to this are:

* Classic Freeform fields that are **loaded with Channel Entry data** will be migrated over as empty fields, as support for this does not yet exist in the new Freeform.
* **File Upload** fields with *multiple files* will be converted to having a single file upload input that can select multiple files at once (rather than showing multiple inputs).
* All associated **member data** in Classic will not be migrated over, as the new Freeform does not currently store any of that information.
* **Field Layouts** (submissions list page) in Classic will be ignored and need to be recreated.

Migrated submissions will have a title of something like `Legacy Submission #7 (Migrated)`. This is because the new Freeform introduces a title generating feature for submissions, based on your fields (e.g. *Submission Title* = `{first_name}` + `{last_name}`). The migration utility cannot guess what you might want your titles to be, so we used something obvious. These can manually be updated if you wish (but no way to retroactively update these unfortunately).


## Preferences <a href="#preferences" id="preferences" class="docs-anchor">#</a>
The preferences/settings are very different between Classic and the new Freeform. No action will happen here during migration, with the exception of migration of [statuses](#statuses).


## Permissions <a href="#permissions" id="permissions" class="docs-anchor">#</a>
The new Freeform currently does not have permission controls. A future version will, but until then, no Classic permissions will be migrated over.


## Clean-up <a href="#cleanup" id="cleanup" class="docs-anchor">#</a>
Once the migration utility has completed, review your forms, fields, notification templates and submission data to ensure everything is as expected.

If there are any issues you suspect are a bug, please let us know through a [support ticket](http://docs.solspace.com/expressionengine/freeform/v1/support.html) before proceeding any further. We may be able to resolve an issue and you can try running migration again.

Proceed to adjusting your forms, fields, notification templates, etc and update your EE templates to use the new Freeform template tags. You can install the demo templates included with the new Freeform to see everything in action right away. Refer to the [Switching from Classic](switching-from-classic.md) documentation for more information.

It's recommended that you leave your old Classic install and its data in place until a few weeks after you've moved on with the new Freeform. That way, if something did go wrong along the way, we may be able to resolve a bug or issue, and then you can redo the migration.
