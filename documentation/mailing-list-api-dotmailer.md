# dotmailer Mailing List API Integration

This documentation page assumes you have read over the [Mailing List Integration Overview page](mailing-list-integrations.md). If you have not yet read it, please do so now. We also assume that you have a [dotmailer](http://dotmailer.com) account already, along with mailing list(s) already created. This integration requires that you have *Freeform Pro*. If you currently have Freeform Lite, you can purchase an upgrade to Freeform Pro by visiting your account area.

Includes support for the following:

* Field mapping to standard and custom fields.

## Setup Instructions <a href="#setup" id="setup" class="docs-anchor">#</a>

1. Create & get API Key from dotmailer:
	* Go to [dotmailer website](http://dotmailer.com) and log into your account.
	* At the bottom left corner, click on the profile with cog icon, then click **Access** menu option.
	* Click on the **API Users** tab, and then click **New User** button.
	* Enter an confirm a password and take note of it for yourself.
	* After the page reloads, copy the auto generated API connector email address under the **Email** column.
2. Setup Integration on your site:
	* Go to the [Mailing Lists section in Freeform Integrations area](mailing-list-integrations.md) (**Freeform > Integrations > Mailing Lists**)
	* Click the **New Integration** at the top right.
	* Select *dotmailer* from the **Service Provider** select dropdown.
	* Enter a name and handle for the integration.
	* Paste the dotmailer API connector email address into the **API User Email** field in Freeform.
	* Enter the chosen password for that API user in the **Password** field in Freeform.
	* At the bottom of the page, click the **Save** button.
3. Verify Authorization:
	* After the integration is saved, it'll return you to the list of mailing list integrations.
	* Click into the newly created integration.
	* Confirm that there is green circle with **Authorized** in the middle of the page.
