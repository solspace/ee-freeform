# SharpSpring CRM API Integration

This documentation page assumes you have read over the [CRM Integration Overview page](crm-integrations.md). If you have not yet read it, please do so now. We also assume that you have a [SharpSpring](https://sharpspring.com) account already. The *SharpSpring* API integration requires that you have *Freeform Next Pro*. If you currently have Freeform Next Basic, you can [purchase an upgrade to Freeform Next Pro here](https://solspace.com/expressionengine/freeform/pro).

SharpSpring integration includes support for the following:

* Field mapping to standard and custom fields (of text/number string type only).
* Maps data to **Contacts**.

## Setup Instructions <a href="#setup" id="setup" class="docs-anchor">#</a>

1. Create & get API Key from SharpSpring:
	* Go to [SharpSpring](https://sharpspring.com) and log into your account.
	* At the top right corner, click on the profile icon and select **Settings**.
	* On the next page, on the left side Settings panel, under **SharpSpring API**, click the **API Settings** link.
	* You'll then see your keys for SharpSpring API under Account ID and Secret Key. Click the **Generate New API Keys** button if you wish to generate new ones (optional).
	* Copy the Account ID and Secret Key.
2. Setup Integration on your site:
	* Go to the [CRM section in Freeform Integrations area](crm-integrations.md) (**Freeform Next > Integrations > CRM**)
	* Click the **New Integration** at the top right.
	* Select *SharpSpring* from the **Service Provider** select dropdown.
	* Enter a name and handle for the integration.
	* Paste the SharpSpring **Account ID** and **Secret Key** into the **Account ID** and **Secret Key** field, respectively, in Freeform.
	* At the top right corner of Freeform page, click **Save** button.
3. Verify Authorization:
	* After the integration is saved, it'll return you to the list of CRM integrations.
	* Click into the newly created integration.
	* Confirm that there is green circle with **Authorized** in the middle of the page.
	* That's it! You can now use this integration inside Composer.
