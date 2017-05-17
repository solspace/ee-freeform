# Introduction to Freeform

This page provides you with an overview of how Freeform works without going into to much detail about each feature so it's easy to digest. You can of course click the links to learn more about each feature.

Freeform centers itself around the idea of letting admins and/or clients enjoy the experience of building and managing simple or complex forms in an intuitive interface that lets you see a live preview of the forms you're building. We call this [Composer](forms-composer.md), where almost everything is at your fingertips as it aims to stay out of your way and let you do as much as possible without having to move around to other areas in the control panel. At the same time, Freeform is very powerful and flexible, so there is also a wide variety of other features, settings and options.


* [Forms](#forms)
* [Fields](#fields)
* [Email Notifications](#email-notifications)
* [Formatting Templates](#formatting-templates)
* [Mailing List API Integrations](#mailing-list-integrations)
* [CRM API Integrations](#crm-integrations)
* [Templating](#templating)
* [Submissions](#submissions)


## Forms <a href="#forms" id="forms" class="docs-anchor">#</a>

When [creating forms](forms-composer.md), you sometimes need to look at things backwards. It often helps to prepare fields, email notification templates and other things BEFORE beginning to create your form. However, you don't have to do it this way. Freeform Composer allows you to do this directly inside the Composer form interface as well (but limited approach for it). Freeform also comes with several common fields preinstalled for you, so that should get you started.

Be sure to review the settings in the **Form Settings** area to adjust things such as form name and handle, formatting template, default status and return URL.

Freeform also allows for true [multi-page forms](multi-page-forms.md), and has its own built in [spam protection](spam-protection.md) service.


## Fields <a href="#fields" id="fields" class="docs-anchor">#</a>

Freeform uses its own set of [fields and field types](fields-field-types.md). Fields are global and available to all forms, but they can also be overwritten per form. This allows you to save time reusing existing fields when making other forms, but also gives you flexibility to make adjustments to them when needed. So to clarify, you can create fields with labels and options that are common to all forms, but also override those on each form.

Fields can be created and managed in the main field creation area (**Freeform > Fields > New Field**) and can also be created directly within the *Composer* interface as well.

The following field types are available:

* **Text** - a simple input field.
* **Textarea** - a simple multi-line input field.
* **Email** - an input field that is flagged in Freeform to expect an email address value as well as possibility for receiving email notifications.
* **Hidden** - a hidden field.
* **Select** - a select dropdown menu field.
* **Checkbox** - a single checkbox field.
* **Checkbox Group** - a group of checkboxes.
* **Radio Group** - a group of radio options.
* **File Upload** - a single file upload field, using [Craft Assets](https://craftcms.com/docs/assets).
* **Dynamic Recipients** - a select dropdown menu field that contains protected email addresses and labels for each.

Additionally, you may also insert **HTML** areas into your form for you to type or paste your code into. The HTML will also be live parsed in the Composer layout area.


## Email Notifications <a href="#email-notifications" id="email-notifications" class="docs-anchor">#</a>

[Email notifications](email-notifications.md) are global and can be reused for multiple forms, saving you time when you are managing many forms. Email templates can be managed within Craft control panel (saved to database), or as HTML template files. Database entry templates are created and customized at **Freeform > Email Notifications**. They can also be created directly at form level within Composer. Email notification templates that are created within Composer will contain basic default content, and should be checked and updated once finished building your form.

Freeform allows you to send email notifications upon submittal of a form 4 different ways, each with their own content/template:

* To admin email address(es) for the form.
* To a predefined select menu/radios of email addresses (and labels) for the user to choose ([Dynamic Recipients](fields-field-types.md#fields-dynamic-recipients) field type).
* To the user submitting the form, with the email addresses specified/selected in the [Email](fields-field-types.md#fields-email) field type.
* To a user-defined email address (e.g Tell-a-Friend form), with the email addresses specified/selected in the [Email](fields-field-types.md#fields-email) field type.


## Formatting Templates <a href="#formatting-templates" id="formatting-templates" class="docs-anchor">#</a>

Freeform attempts to do all the heavy lifting when it comes to templating. Our looping templating approach allows you to automate all or almost all of your form formatting. This can be achieved by building [formatting templates](formatting-templates.md) for your forms.

Forms can be generated on the front end templates 2 different ways:

1. By coding the formatting directly within the template that you want the form to appear in, using the [freeform.form](freeform.form.md) template function.
2. By assigning one of an unlimited number of predefined formatting templates (stored as a regular Twig template in **craft/templates** directory) inside Composer and using the [freeform.form render()](freeform.form.md#render) method. This approach is very portable / DRY, and works similar to an include. Then simply just insert 1 line of code: `{{ craft.freeform.form("formHandle").render() }}` in the template you want your form to load in.


## Mailing List API Integrations <a href="#mailing-list-integrations" id="mailing-list-integrations" class="docs-anchor">#</a>

Freeform supports some popular [Mailing List API integrations](mailing-list-integrations.md). The following mailing list integrations are currently available:

* [MailChimp](mailing-list-api-mailchimp.md) (included with Freeform)
* [Constant Contact](mailing-list-api-constant-contact.md) (available for purchase in [Freeform Marketplace](https://solspace.com/craft/freeform/marketplace/mailinglist))
* [Campaign Monitor](mailing-list-api-campaign-monitor.md) (available for purchase in [Freeform Marketplace](https://solspace.com/craft/freeform/marketplace/mailinglist))


## CRM API Integrations <a href="#crm-integrations" id="crm-integrations" class="docs-anchor">#</a>

Freeform supports some popular [CRM (Customer Relationship Management) API integrations](crm-integrations.md). The following CRM integrations are currently available:

* [Salesforce](crm-api-salesforce.md) (available for purchase in [Freeform Marketplace](https://solspace.com/craft/freeform/marketplace/crm))
* [HubSpot](crm-api-hubspot.md) (available for purchase in [Freeform Marketplace](https://solspace.com/craft/freeform/marketplace/crm))


## Templating <a href="#templating" id="templating" class="docs-anchor">#</a>

As for templating, we strived to make things as flexible as possible, while also coming up with templating that can automate much of the handling of parsing your forms. Freeform is capable of figuring out as much of this as possible for you. This can be achieved by building [formatting templates](formatting-templates.md) for your forms, or simply building forms directly within the page template(s) you're placing your forms into:

* [freeform.form template function](freeform.form.md) - for displaying/parsing your forms.
* [freeform.submissions template function](freeform.submissions.md) - for displaying an individual submission or list of them.

Freeform also includes a complete set of [demo templates](demo-templates.md). These demo templates let you have a fully functioning area on your website with just a couple clicks! Further to this, it allows you to see real world examples of the template code in action, which will help you get acquainted with Freeform quicker.


## Submissions <a href="#submissions" id="submissions" class="docs-anchor">#</a>

Similar to Craft Entries, every time a user submits a form, we refer to those as [submissions](submissions.md). Currently, submissions can be viewed and edited in the control panel, and displayed on the front end in templates as a list and individually. Within the control panel, you can filter the view by form (or show across all forms), search into submissions, adjust which field columns are shown, and click into any of the submissions to edit them.
