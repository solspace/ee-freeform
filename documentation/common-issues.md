# Common Issues & Questions

Check out this documentation resource to troubleshoot common problems or find answers to common questions.

### Common Issues <a href="#issues" id="issues" class="docs-anchor">#</a>

* [Emails not sending](#emails-not-sending)
* [Form not submitting](#form-not-submitting)
* [Updated field options aren't showing](#field-options)
* [Form submits very slowly](#form-submits-slow)
* [Errors about missing files/pages in Freeform CP](#missing-files)
* [Errors about Update Service](#update-service-errors)
* [Composer not loading correctly](#composer-issues)
* [Data not sending to API integrations](#api-integration-issues)
* [Issues with Salesforce API connection](#salesforce-issues)


### Common Questions <a href="#questions" id="questions" class="docs-anchor">#</a>

* [How many sites can I use Freeform on?](#site-license)
* [If I switch from Classic to Next, will all of my data move over with it too?](#classic-next-transition)
* [Can I run Freeform Classic and Next at the same time?](#classic-next)
* [What kind of email notifications can I send?](#email-notification-options)
* [How many fields can I have?](#how-many-fields)
* [Can I show POST data on the success page?](#form-post-data)
* [How do I return form submit to same page with success message?](#form-return-success)


## Emails not sending <a href="#emails-not-sending" id="emails-not-sending" class="docs-anchor">#</a>

The most common issue users run into is email notifications not sending out properly. Most commonly, this is due to a syntax error in their notification template, or an email server configuration issue.

* Are you 100% certain the email notification is not in a spam folder?
* Is the email address correct?
* Is the form successfully being submitted? You can verify this by checking for submissions in the Freeform control panel. If you have the **Store Submitted Data** setting disabled for the form, consider enabling it temporarily to confirm this.
* Have you checked the Freeform error log? The Freeform error log is located inside the Freeform control panel area in the navigation menu. If there are no logged errors, you will not see the Freeform error log.
* Have you assigned an email notification template to the *Email* field, *Dynamic Recipients* field, or admin *Notify* area inside Composer for the given form? For all types of email notifications, they are assigned to fields or the form itself inside the Property Editor column (right side) in Composer.
	* If using the [`dynamic_notification_recipients` parameter](https://solspace.com/expressionengine/freeform/docs/form/#param-dynamicnotificationrecipients) at template level, be sure also be specifying the `dynamic_notification_template` parameter and have the proper syntax and the proper email notification template name (with `.html` file extension if using HTML file).
* Verify that your email notification template is using the correct syntax. Using incorrect syntax will result in a silent error and failure of the email notification sending. For all inputs such as **Subject** and **From Name**, as well as the **Email Body** area, the syntax is as follows:
	* All custom Freeform fields are `{field_name}`
		* E.g. `{first_name}`, `{email}`
		* Do not prefix the variables with `form:`, as that refers to info about the form itself (see below)...
	* Anything related to **info** about the form itself is as follows:
		* `{form:name}` - the Name of the form
		* `{form:handle}` - the Handle for the form
		* `{form:id}` - the ID of the form
* Verify that the fields you're manually calling (if applicable) actually exist for the form and contain data. For example, if you include a field such as `{full_name}` in the **From Name** input field, but your form uses something like `{first_name}` and `{last_name}` instead, it will result in a silent error and failure of the email notification sending.
* Have you tried testing this inside the demo templates that come included with Freeform? This will at least help narrow things down to possibly an error in your template code if applicable.
* Does your base email address for your site's outgoing email (**CP** -> **Settings** -> **Outgoing Email** -> **Address** field) have an email address in it? It's a required field, but we've seen many sites with a blank value.
* Are you sending **to** and **from** the same email address or email address domain? For example, sending FROM *bob@acmewidgets.com* TO *bob@acmewidgets.com*, OR FROM *bob@acmewidgets.com* TO *larry@acmewidgets.com*. Sometimes the email server has very aggressive spam protection and knows that the email is not actually coming from that email address, and blocks the email altogether.
* Can you send email with EE's Communicate feature? Freeform uses ExpressionEngine's Email service, so if that doesn't work, then Freeform won't either. To check this, in the Control Panel go to **Developer** -> **Utilities** -> **Communicate**, and then try sending an email. You can read more about [troubleshooting email issues on EE's documentation page](https://docs.expressionengine.com/v3/troubleshooting/email/emails_not_reaching_destination.html).
* To be extra thorough, you can also try sending notifications with EE's simple [Contact Form](https://docs.expressionengine.com/latest/add-ons/email/contact_form.html) feature.
* Are you getting redirected correctly after form submission? Be sure that you don't have any routes or .htaccess rewrites that conflict with the form submission.


## Form not submitting <a href="#form-not-submitting" id="form-not-submitting" class="docs-anchor">#</a>

A common issue customers run into is their forms not submitting successfully. There's a variety of reasons this might be.

* Are you sure the form is not being successfully submitted? You can verify this by checking for submissions in the Freeform control panel. If you have the **Store Submitted Data** setting disabled for the form, consider enabling it temporarily to confirm this.
* Have you checked the Freeform error log? The Freeform error log is located inside the Freeform control panel area in the navigation menu. If there are no logged errors, you will not see the Freeform error log.
* Have you tried testing this inside the demo templates that come included with Freeform? This will at least help narrow things down to possibly an error in your template code if applicable. If your form doesn't work inside the demo templates, try creating an additional form that is simple (e.g. Name, Email, and Message fields), and try submitting again.
* Do you have caching on your template and/or site? Heavy caching will prevent the EE CSRF token and Freeform hash from reloading and stop the form from working. If you need to cache your page/site, you'll need to find a way to manually refresh these for each page load.
* Do you have Javascript enabled for your browser? If you don't, and you're using Freeform's built in [Spam Protection](spam-protection.md) feature, it won't submit the form successfully.
* Have a look at the *Blocked Spam* count for your form in the **CP** -> **Freeform** -> **Forms** list. If the count is increasing each time you attempt to submit the form, Freeform's [Spam Protection](spam-protection.md) feature is blocking your submissions.
* Try disabling the [Spam Protection](spam-protection.md) feature (**CP** -> **Developer** -> **Add-On Manager** -> **Freeform** -> **Settings** -> **General** and set *Spam Protection* setting to disabled) and test again.
* Are you getting redirected correctly after form submission? Be sure that you don't have any routes or .htaccess rewrites that conflict with the form submission.
* To be extra thorough, you can also try submitting EE's simple [Contact Form](https://docs.expressionengine.com/latest/add-ons/email/contact_form.html) feature.


## Updated field options aren't showing <a href="#field-options" id="field-options" class="docs-anchor">#</a>

Freeform takes a bit of a unique - but flexible - approach to [fields](fields-field-types.md). Fields are global and available to all forms, but they can also be overwritten per form. This allows you to save time reusing existing fields when making other forms, but also gives you flexibility to make adjustments to them when needed. So to clarify, you can create fields with labels and options that are common to all forms, but also override those on each form. For example, if you have a field named Cell Phone, on the form level, you can rename the field name to Mobile Phone, or if you have a Checkbox Group field with options: Option A, Option B, and Option C, you could override it to just have 2 options with values of Option A and Option B, and/or add Option D.

The possibly confusing part here is that when you edit/add/remove options at Composer level for each form, they will NOT update the "master" field options/labels. And likewise, if you edit/add/remove options at the "master" field level (**Freeform** -> **Fields**), they will NOT update any existing usage of this field in the forms they're assigned to. It would be chaos if it did in either case, and prevent you from being able to tweak field labels and options per form.

If you plan on building several forms with matching fields (that have matching options, etc), we strongly encourage you to create the field(s) in main Field area (**Freeform** -> **Fields**) first with all the options you'd like. Then when you construct your forms, you'll see all the default options available. It's better to think of the main fields area as defaults for new forms (or new assignments of that field to existing forms).


## Form submits very slowly <a href="#form-submits-slow" id="form-submits-slow" class="docs-anchor">#</a>

If you're experiencing performance issues with Freeform, it could be due to a variety and combination of reasons. If forms are taking a long time to submit, consider the following options, and create a support ticket with the info below if necessary:

* Are you using an older version of ExpressionEngine? Please update to the latest available version.
* Does your form have a lot of fields in it? If so, try testing with a smaller and simpler form (e.g. Name, Email, and Message fields) and see if it submits faster.
* Are you sending email notifications with the form? If so, does disabling some or all of them make things go faster?
* Are any other parts of the site (especially form submits) slow or slow ish? If so, the issue might have more to do with your hosting environment.
* Try placing the form on a fresh/blank template and see if the issue still happens (this rules out conflicts with any forms or JS on the page).


## Errors about missing files/pages in Freeform CP <a href="#missing-files" id="missing-files" class="docs-anchor">#</a>

A surprisingly common issue is that customers will see errors in the Freeform control panel area saying that a file or page doesn't exist / is missing (e.g. when attempting to create a new email notification template, etc). This is usually a result of the file actually being missing on the site, or being blocked for some reason. Make sure that your server, site, or FTP client is not ignoring or excluding certain files and/or directories for any reason (e.g. an **.htaccess** rewrite rule that manipulates URL's can interfere with Freeform's POST to `/save` by redirecting it to `/save/`, etc). In all known cases of this, the aforementioned solutions always resolved the issue.


## Errors about Update Service <a href="#update-service-errors" id="update-service-errors" class="docs-anchor">#</a>

If you're seeing errors referencing the Freeform Update Service, it might just be a temporary glitch. To resolve this issue right away, delete the **freeform_next** folder in the `/system/user/cache/` directory, which would let Freeform try downloading the Changelog again.


## Composer not loading correctly <a href="#composer-issues" id="composer-issues" class="docs-anchor">#</a>

If the Composer interface inside the Freeform control panel is not loading correctly, it's very likely to do with the Freeform **themes** folder missing / being old / being corrupt. Remove the **freeform_next** themes folder and try uploading it again.


## Data not sending to API integrations <a href="#api-integration-issues" id="api-integration-issues" class="docs-anchor">#</a>

The most common reason why data doesn't get sent to an API integration is because of a field mapping error, whether it's a required field on the API integration side that isn't being fulfilled on the Freeform submission, or the data formatting not matching expectations, etc.

* Have you checked the Freeform error log? The Freeform error log is located inside the Freeform control panel area in the navigation menu. If there are no logged errors, you will not see the Freeform error log.
* Have you tried testing this inside the demo templates that come included with Freeform? This will at least help narrow things down to possibly an error in your template code if applicable. If your form doesn't work inside the demo templates, try creating an additional form that is simple (e.g. Name, Email, and Message fields), and try submitting again.
* Is the field type / data formatting supported in the API integration? Not all field types are supported for each integration. This is usually noted near the top of the page of each API integration setup documentation page.


## Issues with Salesforce API connection <a href="#salesforce-issues" id="salesforce-issues" class="docs-anchor">#</a>

Salesforce usually disables the API access entirely with their Trial version. You will need to contact Salesforce support to manually enable it for you.

If you're seeing any errors as follows:

> 'Client error response [status code] 400 [reason phrase] Bad Request [url] https://login.salesforce.com/services/oauth2/token'

* This is because Salesforce runs differently when in Sandbox mode, so be sure to enable the **Sandbox Mode** option inside the CRM integration setting in the Freeform control panel.

> No 'refresh_token' present in auth response for SalesforceLead. Enable offline-access for your app.

* Make sure that the **Perform requests on your behalf at any time (refresh_token, offline_access)** setting is added to the **Selected OAuth Scopes** field for your app in Salesforce.


## How many sites can I use Freeform on? <a href="#site-license" id="site-license" class="docs-anchor">#</a>

Each purchase of Freeform entitles you to use it on one site (including dev/staging copies of the site). For every additional site you wish to use it on, you'll need to purchase additional licenses.


## If I switch from Classic to the new Freeform version ("Next"), will all of my data move over with it too? <a href="#classic-next-transition" id="classic-next-transition" class="docs-anchor">#</a>

The new Freeform was completely rebuilt from scratch, and while it of course contains much of the same functionality as Freeform Classic, it's actually a whole new add-on. Because of this, along with the drastic changes we made in the architecture of "Next", there currently is no migration path. We are however in the process of making a migration path that will be available in the not-to-distant future. In the meantime, because the new Freeform has a different module name and the add-on folder is named `freeform_next`, it means that it won't conflict with classic Freeform installs in anyway. This allows you to explore and try the new Freeform out and/or transition from Freeform Classic to "Next" at a comfortable pace. You can even keep Freeform Classic around for legacy viewing of submissions too.


## Can I run Freeform Classic and the new Freeform version ("Next") at the same time? <a href="#classic-next" id="classic-next" class="docs-anchor">#</a>

The simple answer is, YES! The longer answer is... the new Freeform has a different module name and the add-on folder is named `freeform_next`. This means that it won't conflict with classic Freeform installs in anyway. This allows you to explore and try the new Freeform out and/or transition from Classic to "Next" at a comfortable pace. You can even keep Classic around for legacy viewing of submissions too (as there currently isn't a migration path from Classic to the new Freeform yet).


## What kind of email notifications can I send? <a href="#email-notification-options" id="email-notification-options" class="docs-anchor">#</a>

Freeform allows you to send [email notifications](email-notifications.md) upon submittal of a form 5 different ways, each with their own content/template:

* To admin email address(es) for the form (set inside *Notify* area of Composer)
* To a predefined select menu/radios of email addresses (and labels) for the user to choose ([Dynamic Recipients](fields-field-types.md#fields-dynamic-recipients) field type).
* To the user submitting the form, with the email addresses specified/selected in the [Email](fields-field-types.md#fields-email) field type.
* To a user-defined email address (e.g Tell-a-Friend form), with the email addresses specified/selected in the [Email](fields-field-types.md#fields-email) field type.
* At template level with the [dynamic_notification_recipients](form.md#param-dynamicnotificationrecipients) parameter.


## How many fields can I have? <a href="#how-many-fields" id="how-many-fields" class="docs-anchor">#</a>

There is currently a limitation of 195 Freeform fields for each install of ExpressionEngine, due to the MySQL column limit, since all fields are stored in a single table. However, it's very important to note that Freeform fields can be used across all forms, and even be relabelled for each form.

If you plan on having many forms on your site, it may make sense to have some generic naming of fields, and relabel them for each individual form inside Composer. For example, if you need a checkbox for each form that essentially has your site user accept terms and conditions (but the label needs to be a bit different for each form), it'd be better to create a checkbox field once with the name **Accept Terms**, and then relabel it for each form as necessary, rather than creating 10 different variations of fields.

You can read more about [Freeform Fields and Field Types here](fields-field-types.md).


## Can I show POST data on the success page? <a href="#form-post-data" id="form-post-data" class="docs-anchor">#</a>

Unfortunately you cannot because that data is cleared upon submission, which is done because for security reasons and preventing forms from being submitted more than once, etc.

You can however, consider another approach. Freeform allows you to [display submissions on the front end](submissions.md). It also allows you to set the return URL to include the future submission ID. You can set this either in the **Return URL** field for the form in Composer, or at template level like:

    return="{site_url}your-page/success/SUBMISSION_ID"

**NOTE:** Using this approach can be a security risk as site visitors could try out other ID's in the URL and view submission data for those submissions. It's strongly recommended that you refrain from displaying any sensitive data, but instead use this for anonymous polls or something simple like:

> Thanks {submission:first_name:value}, we've received your message and will get back to you shortly!


## How do I return form submit to same page with success message? <a href="#form-return-success" id="form-return-success" class="docs-anchor">#</a>

Aside from the obvious of using AJAX, you can achieve this by adding a "fake" segment at the end of the URL in your return URL. Then setup a conditional based on that:

	{if last_segment == "success"}

		<div class="callout success">Your message has been sent.</div>

	{if:else}

		{exp:freeform_next:render
			form="contact_form"
			return="{current_url}/success"
		}

	{/if}
