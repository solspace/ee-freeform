# Getting Started

This page provides you with instructions to get started quickly with Freeform. It's not exhaustive by any means, but it will get your feet wet if you're new to Freeform, and help you understand a typical flow for creating forms.

First things first, Freeform centers itself around the idea of letting admins and/or clients enjoy the experience of building and managing simple or complex forms in an intuitive interface that lets you see a live preview of the forms you're building. We call this [Composer](forms-composer.md), where almost everything is at your fingertips as it aims to stay out of your way and let you do as much as possible without having to move around to other areas in the control panel. At the same time, Freeform is very powerful and flexible, so there is also a wide variety of other features, settings and approaches to building forms as well.


## Step 1: Fields <a href="#fields" id="fields" class="docs-anchor">#</a>
Freeform uses its own set of [fields and field types](fields-field-types.md). Fields are global and available to all forms, but they can also be overwritten per form. This allows you to save time reusing existing fields when making other forms, but also gives you flexibility to make adjustments to them when needed. So to clarify, you can create fields with labels and options that are common to all forms, but also override those on each form.

Go to the Fields area inside Freeform. By default, it comes with a handful of common fields you might use. If there's any other fields you think you might need, you can add them here. If you need to add some later, you can always come back to this area, or create fields directly inside your form too.


## Step 2: Form <a href="#form" id="form" class="docs-anchor">#</a>

1. Go to the Forms area inside Freeform. Click on the New Form button at the top right.
2. You'll now see a form building interface we call Composer, where you can begin creating and configuring your form. Composer is a workspace divided into 3 columns:
	* *Fields*: the left column, where you can see the fields available (and create new ones).
	* *Layout*: the middle column, where you can drag and drop fields (from left column) into, and can rearrange each field however you please.
	* *Property Editor*: the right column, which is where you are able to configure the form, email notifications, API integrations, and properties for each field and page, etc.
3. Begin creating your first form by changing the name and handle of the form from "Composer form" to something else. The Form Settings will load in the Property Editor by default when the form is first created, but you can get to it by clicking the *Form Settings* button at top right area. Let's call the form `Contact` with handle of `contact`. Once that's done, click **Save** button at top (you don't have to right away, but good practice, like when you're working on a Word document, etc).
4. In the left column, look for some fields you need. You can add them to your form by clicking and holding, and then dragging them onto the middle Layout column and let go. You can rearrange the fields however you please. They can even be placed side-by-side, and Freeform will automatically create columns for the field layout.
5. Once you have a handful of fields placed and arranged, click on some of the fields (one by one) and view the properties for them in the right Property Editor column. Here is where you can set a field to be required, set placeholder values, etc. If you're working with a more complex field such as the *Date & Time* fieldtype available in Freeform Pro, be sure to scroll down in the Property Editor column to see more available options. Once done all that, click the **Save** button at top.


## Step 3: Email Notifications <a href="#notifications" id="notifications" class="docs-anchor">#</a>
A [variety of email notification options](email-notifications.md) are available with Freeform. For the purpose of this example, we'll keep it simple and create an email notification for Admin(s) and an email notification for the user submitting the form.

1. To add an email notifications for Admin(s), click on the *Notify* button at the top right.
2. Assuming you don't yet have any email notification templates created, you can quickly create one here by clicking the **Add New Template** button. Let's name it `Admin` and click the **Save** button below. In the *Admin Recipients* field that appears underneath, enter the email address(es) here. Separate multiples on new lines. Then click the **Save** button at top.
3. To set an email notification for the user submitting the form, find your *Email* field in the middle Layout column and click on it.
4. In the Property Editor column on the right, you'll see an *Email Template* field. You can quickly create another email notification template by clicking the **Add New Template** button. Let's name it `Submitter` and click the **Save** button below.

To customize the email notification templates you quickly created in Composer, visit the *Email Notifications* page in the Freeform control panel later.


## Step 4: Formatting Templates <a href="#formatting" id="formatting" class="docs-anchor">#</a>
Freeform attempts to do all the heavy lifting when it comes to templating. Our looping templating approach allows you to automate all or almost all of your form formatting. This can be achieved by using ready-made available options, or custom building your own [formatting templates](formatting-templates.md) for your forms.

Form rendering can be done two different ways - through a formatting template that can be reused by multiple forms, or entering all of the formatting code directly inside your page template. For the purpose of this example, we'll use a ready-made example formatting template.

1. Click on the *Form Settings* button at top right area of Composer.
2. In the Property Editor column on right, look for *Formatting Template* field. Select any one of the several formatting template examples and click the **Save** button at top. If you select one such as *Bootstrap*, be sure you call the necessary Bootstrap CSS and JS files in your page template.


## Step 5: Templating <a href="#templating" id="templating" class="docs-anchor">#</a>

Your form should now be ready to go. Before you hit the templates, take note of the handle name of the form you just created (you can view it in the *Form Settings* area of Property Editor in the right column). If you followed our example, it will be `contact`.

1. Create a new template or open up an existing one.
2. Paste the following code into your template:

	{exp:freeform_next:render form="contact"}

That's it!


## Demo Templates <a href="#demo" id="demo" class="docs-anchor">#</a>
Be sure to check out your form in the [demo templates](demo-templates.md) that come with Freeform if you want to quickly see your forms in action. These demo templates let you have a fully functioning area on your website with just a couple clicks!


## Customizing Rendering of Form <a href="#customizing" id="customizing" class="docs-anchor">#</a>
If you choose to use the [`Render`](form.md) approach as used in the example above in step 5, you can customize the rendering of your form by applying overrides.

For example, instead of calling your form like this:

	{exp:freeform_next:render form="contact"}

You can customize your form by applying some overrides:

	{exp:freeform_next:render
		form="contact"
		label_class="my-label-class"
		input_class="my-input-class"
		submit_class="my-button-class"
		instructions_below_field="yes"
		override_values:location="{segment_3}"
	}
		{if form:no_results}Sorry, no form was found.{/if}
	{/exp:freeform_next:render}

In the example above, all of the field labels will globally be given the CSS class of `my-label-class`, and inputs given the CSS class of `my-input-class`, but your submit button(s) will be given the class of `my-button-class`. If using **instructions** for fields, Freeform will place them below the input instead of above. And finally, your hidden field with a handle of `location` will be given a value from the **third segment in the URL** (if supplied).

Be sure to check out the [Form](form.md) template tag documentation for more information.
