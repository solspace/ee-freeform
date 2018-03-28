# Pipedrive CRM API Integration

This documentation page assumes you have read over the [CRM Integration Overview page](crm-integrations.md). If you have not yet read it, please do so now. We also assume that you have a [Pipedrive](http://pipedrive.com) account already. This integration requires that you have *Freeform Pro*. If you currently have Freeform Lite, you can purchase an upgrade to Freeform Pro by visiting your account area.

Includes support for the following:

* Field mapping to standard and custom fields.
* Maps data to [Deals](https://developers.pipedrive.com/docs/api/v1/#!/Deals), [Persons](https://developers.pipedrive.com/docs/api/v1/#!/Persons), [Organizations](https://developers.pipedrive.com/docs/api/v1/#!/Organizations) and [Notes](https://developers.pipedrive.com/docs/api/v1/#!/Notes) endpoints.

## Setup Instructions <a href="#setup" id="setup" class="docs-anchor">#</a>

1. Create & get API Key from HubSpot:
	* Go to [Pipedrive](http://pipedrive.com) and log into your account.
	* At the top right corner, click on the profile icon and select **Settings**.
	* On the next page, under the **Personal** navigation option, click the **API** option from the secondary navigation menu near the bottom on the left.
	* Click the **Generate new token** link and copy the newly created token.
2. Setup Integration on your site:
	* Go to the [CRM section in Freeform Integrations area](crm-integrations.md) (**Freeform > Integrations > CRM**)
	* Click the **New Integration** at the top right.
	* Select *Pipedrive* from the **Service Provider** select dropdown.
	* Enter a name and handle for the integration.
	* Paste the Pipedrive API token into the **API Token** field in Freeform.
	* At the bottom of the page, click the **Save** button.
3. Verify Authorization:
	* After the integration is saved, it'll return you to the list of CRM integrations.
	* Click into the newly created integration.
	* Confirm that there is green circle with **Authorized** in the middle of the page.
	* That's it! You can now use this integration inside Composer.
