# Campaign Monitor Mailing List API Integration

This documentation page assumes you have read over the [Mailing List Integration Overview page](mailing-list-integrations.md). If you have not yet read it, please do so now. We also assume that you have a [Campaign Monitor](http://campaignmonitor.com) account already, along with mailing list(s) already created.

Campaign Monitor integration includes support for the following:

* Field mapping to standard and custom fields (of text type only), per list.

## Setup Instructions <a href="#setup" id="setup" class="docs-anchor">#</a>

1. Purchase & Install Freeform Campaign Monitor API plugin:
	* Purchase and download Campaign Monitor API plugin from the [Freeform Marketplace](https://solspace.com/craft/freeform/marketplace/mailinglist)
	* Install Campaign Monitor API plugin:
		1. Unzip the download package and copy the *freeformcampaignmonitor* folder to your Craft sites **/craft/plugins/** directory.
		2. Go to the **Settings > Plugins** page and click **Install** for the *Freeform Campaign Monitor* plugin.
2. Create & get API Key from Campaign Monitor:
	* Go to [Campaign Monitor website](http://campaignmonitor.com) and log into your account.
	* At the top right corner, click on the profile icon and select **Manage Account**.
	* On the next page, click the **API keys** link near the bottom of the page.
	* After the page reloads, click the **Show API Key** link to reveal your API key.
	* Leave this page open and open a new tab to go to Craft control panel...
3. Setup Integration on your site:
	* Go to the [Mailing Lists section in Freeform Settings](settings.md#mailing-lists) (**Freeform > Settings > Mailing Lists**)
	* Click the **New Mailing List Integration** at the top right.
	* Select *Campaign Monitor* from the **Service Provider** select dropdown.
	* Enter a name and handle for the integration.
	* Copy the value in the **API Key** field from Campaign Monitor and paste it into the **API Key** field in Freeform.
	* Copy the value in the **Client ID** field from Campaign Monitor and paste it into the **Client ID** field in Freeform.
	* At the top right corner of Freeform page, click **Save** button.
4. Verify Authorization:
	* After the integration is saved, it'll return you to the list of mailing list integrations.
	* Click into the newly created integration.
	* Confirm that there is green circle with **Authorized** in the middle of the page.
