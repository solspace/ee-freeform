# Mailing List Integrations

Freeform supports some popular mailing list integrations. Inside the **Integrations** area of Freeform, there is a Mailing List API Integration Manager, which allows you to manage your mailing list API integrations.

[![Connect Mailing List](images/cp_api-mailinglist-create.png)](images/cp_api-mailinglist-create.png)

The following Mailing List integrations are currently available for **Freeform Pro** (click each one for individual setup instructions):

* [MailChimp](mailing-list-api-mailchimp.md)
* [dotmailer](mailing-list-api-dotmailer.md)
* [Constant Contact](mailing-list-api-constant-contact.md)
* [Campaign Monitor](mailing-list-api-campaign-monitor.md)

Some important things to know about Mailing List integrations are:

* Mailing list integrations are globally available to all forms, but are configured per form inside the Composer interface.
* Most - if not all - integrations attempt to map all available fields and custom fields, but some may have limitations if the API is too complex or doesn't allow it.
* Mailing list integrations appear as a checkbox that can be drag and dropped into the Composer interface:
	* Can only be displayed as a single checkbox, or as hidden field (as an automatic opt-in when using specifically for mailing list signups). If you want more than 1 mailing list, you can drag and drop another field into your layout (but the checkboxes cannot be displayed as a group, unless of course you made some manual adjustments to the formatting template).
		* Label of the checkbox is customizable per form.
		* Checkbox can be checked by default.
	* You can specify the mailing list to be used for the mailing list integration.
	* Your form must include an [Email](fields-field-types.md#fields-email) field type, which must then be assigned to the **Target Email Field** setting.
	* When available, **Field Mapping** setting allows you to map Freeform fields to available mailing list integration fields.
* To get access to all integrations, purchase (or purchase an upgrade to) *Freeform Pro*.

> **NOTE:** While data is passed along to the Mailing List provider, Freeform does not store whether or not Mailing List fields were opted in, so CP submission views will not display whether or not the user subscribed.

Every integration is a little bit different, so we have detailed instructions for setting up each integration on their own page.
