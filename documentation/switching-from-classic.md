# Switching from Freeform Classic to Freeform Lite / Pro ("Next")

**NOTE:** For the purpose of clarity in this documentation resource, any prior version of *Freeform Pro 4.x* and *Freeform Pro 5.x* are referred to as *Freeform Classic*. The **new** versions of *Freeform Lite* and *Freeform Pro* are referred to as *Freeform Next*.

Freeform has been completely rewritten from ground up. Whether you're considering switching from Freeform Classic to Freeform Next, or interesting in using Freeform Next on your next site build, this page is a resource to help you understand the differences and changes made with Freeform Next.

* [Changes to Composer](#composer)
* [Changes to Composer Templates](#formatting-templates)
* [Changes to Freeform Entries](#submissions)
* [Changes to Email Notifications](#email-notifications)
* [Changes to Multi-Page Forms](#multi-page-forms)
* [Changes to Spam Protection](#spam-protection)
* [Changes to Permissions](#permissions)
* [Addition of API Integrations](#api-integrations)
* [Are There Migration Options?](#migration)


## Changes to Composer <a href="#composer" id="composer" class="docs-anchor">#</a>
Freeform Next centers itself around the idea of letting admins and/or clients enjoy the experience of building and managing simple or complex forms in an intuitive interface that lets you see a live preview of the forms you're building. We call this [Composer](forms-composer.md), where almost everything is at your fingertips as it aims to stay out of your way and let you do as much as possible without having to move around to other areas in the control panel. At the same time, Freeform Next is very powerful and flexible, so there is also a wide variety of other features, settings and options.

For Freeform Next, we now only include a single method for building forms. Freeform Classic used to allow **EE Template** and **Composer** methods. The very manual EE Template-based approach is no longer a choice. We strongly believe you will be so impressed with the power and flexibility of the new Composer, that you won't want to go back to EE Template approach. And even if you do, [you still can build out your forms completely manually](form.md#manual-example) if you desire (but you'll still need to quickly assemble a form layout inside Composer).

[Formatting options inside templates](form.md) or [Formatting Templates](formatting-templates.md) are so flexible, powerful, and easy to use. We carefully came up with a new, intuitive and consistent syntax for templating.


## Changes to Composer Templates <a href="#formatting-templates" id="formatting-templates" class="docs-anchor">#</a>
Composer Templates are now referred to as **Formatting Templates** in Freeform Next.

While Freeform Next offers a very intuitive Composer interface to give you a live preview of the form you're building, there of course isn't exactly a magic way to do this on the front end for your templates. However, we have created an automated way for Freeform to figure out as much of this as possible for you.

As mentioned above, [Formatting options inside templates](form.md) or [Formatting Templates](formatting-templates.md) are so flexible, powerful, and easy to use. We carefully came up with a new, intuitive and consistent syntax for templating.

Formatting Templates are also now stored as HTML files. The benefit here is that they can work nicely with version control and/or staging environments, etc, rather than being stored in the database.

Forms can be generated on the front end templates 2 different ways. There is no worse or better way, but here's an overview:

1. With the [Freeform_Next:Form](form.md) template tag.
	* Your form formatting code is contain directly within the template that you want the form to appear in.
	* No matter what formatting template your form may have assigned to it in Composer, the form always conforms to the template formatting used in this template.
2. Using [Freeform_Next:Render](form.md#render-examples) method.
	* Your form formatting code is stored in a separate template, but is very portable / DRY, and works similar to an include.
		* Formatting templates are HTML files stored in the EE Templates directory (benefit is that they can work nicely with version control and/or staging environments, etc).
		* Freeform includes several [formatting template examples](formatting-template-examples.md) for you to start out with as well.
	* In template(s) that you want your form(s) to show up in, you simply just insert 1 line of code: `{exp:freeform_next:render form="contact"}`

Formatting templates are optional, and only necessary if using the [Freeform_Next:Render tag method](form.md#render-examples), which essentially allows you to attach a formatting template to a form so that you don't need to include formatting inside the template(s) you place the form inside.

The code and formatting in these templates looks exactly like the code in a [Freeform_Next:Form](form.md) tag, but excludes the opening and closing `{exp:freeform_next:form}` tags, as the *Render* tag does this for you.


## Changes to Freeform Entries <a href="#submissions" id="submissions" class="docs-anchor">#</a>
Freeform Entries are now referred to as [**Submissions**](submissions.md) in Freeform Next. You'll now use the `{exp:freeform_next:submissions}` template tag to display an individual submission or list of submissions based on some or no criteria.

Freeform Next also now includes an option to NOT store submissions in the database, per form.

Freeform Next currently only supports basic CSV exporting. In the future, more exporting options will be added.


## Changes to Email Notifications <a href="#email-notifications" id="email-notifications" class="docs-anchor">#</a>
[Email Notification templates](email-notifications.md) work generally the same way, but now include the option of storing them as HTML files. The benefit here is that they can work nicely with version control and/or staging environments, etc, rather than being stored in the database.


## Changes to Multi-Page Forms <a href="#multi-page-forms" id="multi-page-forms" class="docs-anchor">#</a>
Multi-page forms still work similar to how they did with Freeform Classic, with the exception that there is no unique URI segment per page at this time, and users cannot jump ahead to different pages, only forward and backward 1 page.


## Changes to Spam Protection <a href="#spam-protection" id="spam-protection" class="docs-anchor">#</a>
One of the greatest shortcomings in Freeform Classic was spam control. It used to be that you had to either live with out of control spam submissions, or purchase and install another add-on to combat the issue. This is why Freeform Next no longer has *Ban Keywords* feature and does NOT use [EE's native CAPTCHA feature](https://docs.expressionengine.com/latest/security/captchas.html) as it has always proven ineffective. Instead, it includes its own [Javascript-based honeypot spam protection](spam-protection.md), which is immeasurably more effective (and we've had excellent success with it in Freeform for Expression Engine CMS). This is enabled by default, but can be disabled in the [Freeform Settings](settings.md#spam-protection).


## Changes to Permissions <a href="#permissions" id="permissions" class="docs-anchor">#</a>
Freeform Next currently does not have any module-level permissions like Freeform Classic. However, this is a feature that will likely be added back in a future version.


## Addition of API Integrations <a href="#api-integrations" id="api-integrations" class="docs-anchor">#</a>
The *Pro* version of Freeform Next includes integrations with several popular [Mailing List](mailing-list-integrations.md) and [CRM (Customer Relationship Management)](crm-integrations.md) API's, including [MailChimp](mailing-list-api-mailchimp.md), [Constant Contact](https://solspace.com/expressionengine/freeform/marketplace/mailinglist), [Campaign Monitor](https://solspace.com/expressionengine/freeform/marketplace/mailinglist), [Salesforce](https://solspace.com/expressionengine/freeform/marketplace/crm) and [HubSpot](https://solspace.com/expressionengine/freeform/marketplace/crm). We plan to add support for other API's in the future as well.


## Are There Migration Options? <a href="#migration" id="migration" class="docs-anchor">#</a>
There currently is no migration options from Freeform Classic to Freeform Next. This is something we are looking into developing in the future though. However, Freeform Next can be used alongside classic Freeform/Freeform Pro installations if you're wanting to try out and/or switch to Freeform Next.
