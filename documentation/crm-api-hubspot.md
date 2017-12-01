# HubSpot CRM API Integration

This documentation page assumes you have read over the [CRM Integration Overview page](crm-integrations.md). If you have not yet read it, please do so now. We also assume that you have a [HubSpot](http://hubspot.com) account already. The *Freeform HubSpot* API integration requires that you have *Freeform Pro*. If you currently have Freeform Lite, you can [purchase an upgrade to Freeform Pro here](https://solspace.com/expressionengine/freeform/pro).

HubSpot integration includes support for the following:

* Field mapping to standard and custom fields (of text/number string type only).
* Maps data to [Deals](http://developers.hubspot.com/docs/methods/deals/deals_overview), [Contacts](http://developers.hubspot.com/docs/methods/contacts/contacts-overview) and [Companies](http://developers.hubspot.com/docs/methods/companies/companies-overview) API's.

## Setup Instructions <a href="#setup" id="setup" class="docs-anchor">#</a>

1. Create & get API Key from HubSpot:
	* Go to [HubSpot](http://hubspot.com) and log into your account.
	* At the top right corner, click on the profile icon and select **Integrations**.
	* On the next page, click the **Get your HubSpot API Key** link at the bottom left.
	* Click the **Generate New Key** button in the middle of the page.
	* After the page reloads, copy the newly created key.
2. Setup Integration on your site:
	* Go to the [CRM section in Freeform Integrations area](crm-integrations.md) (**Freeform > Integrations > CRM**)
	* Click the **New Integration** at the top right.
	* Select *HubSpot* from the **Service Provider** select dropdown.
	* Enter a name and handle for the integration.
	* Paste the HubSpot API key into the **API Key** field in Freeform.
	* At the bottom of the page, click the **Save** button.
3. Verify Authorization:
	* After the integration is saved, it'll return you to the list of CRM integrations.
	* Click into the newly created integration.
	* Confirm that there is green circle with **Authorized** in the middle of the page.
	* That's it! You can now use this integration inside Composer.
