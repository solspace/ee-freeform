# Constant Contact Mailing List API Integration

This documentation page assumes you have read over the [Mailing List Integration Overview page](mailing-list-integrations.md). If you have not yet read it, please do so now. We assume that you have a [Constant Contact ](http://constantcontact.com) account already, along with mailing list(s) already created. This integration requires that you have *Freeform Pro*. If you currently have Freeform Lite, you can purchase an upgrade to Freeform Pro by visiting your account area.

Includes support for the following:

* Field mapping to standard fields.

## Setup Instructions <a href="#setup" id="setup" class="docs-anchor">#</a>

Constant Contact API integrations are a little uglier since they include another layer of API calls by using *Mashery* API Management. You essentially have to connect Constant Contact to Mashery, and then Mashery to Freeform. Don't trust your instincts and just follow the steps below closely and it won't be as bad as it looks.

1. Sign up for a *Mashery* API Management account:
	* Constant Contact runs it's API through Mashery, so you will actually not be setting any of this up inside your Constant Contact account.
	* [Visit the Mashery site to sign up for a Mashery account](https://constantcontact.mashery.com/member/register)
		* OR, if you have one already, [log in to your Mashery account](https://constantcontact.mashery.com/login).
	* Fill out the form and then click **Register** button.
	* You'll receive a confirmation email with a link to click to verify your account.
2. Create Mashery API Application:
	* After verifying your account, [create/register a new application](https://constantcontact.mashery.com/apps/register) to begin creating your API app.
	* Fill out the form and click **Register Application** button.
	* On the success page, it'll display your app's **Key** and **Secret**. Ignore the **Secret** and copy down the value for the **Key** somewhere in a note file.
3. Connect Mashery API Application to Constant Contact:
	* Click on the **API tester tab**.
	* In the select dropdown menu, select your app/key from the list and click the **Get Access Token** button.
	* You'll be taken to a Constant Contact page where it asks you to register. Click the **I already have an account** link at the top of the page, and then log into Constant Contact (if not already).
	* Once logged in, you'll be presented an OAuth form, asking if you want to allow access. Click **Allow** button.
	* When you're shown the **Access Token**, be sure to copy it down somewhere in your note file. Click **Done** button.
	* You have now connected Constant Contact to Mashery.
4. Prepare your site's end for Integration:
	* Go to the [Mailing Lists section in Freeform Integrations area](mailing-list-integrations.md) (**Freeform > Integrations > Mailing Lists**)
	* Click the **New Integration** at the top right.
	* Select *Constant Contact* from the **Service Provider** select dropdown.
	* Enter a name and handle for the integration.
	* Copy the Constant Contact Mashery **Key** value you saved to a note file earlier (should be a long alphanumeric hash) and paste into the **API Key** field in Freeform.
	* Copy the Constant Contact Mashery **Access Token** value you saved to a note file earlier (should be a long alphanumeric hash) and paste into the **Access Token** field in Freeform.
	* At the bottom of the page, click the **Save** button.
5. Verify Authorization:
	* After the integration is saved, it'll return you to the list of mailing list integrations.
	* Click into the newly created integration.
	* Confirm that there is green circle with **Authorized** in the middle of the page.
