# MailChimp Mailing List API Integration

This documentation page assumes you have read over the [Mailing List Integration Overview page](mailing-list-integrations.md). If you have not yet read it, please do so now. We also assume that you have a [MailChimp](http://mailchimp.com) account already, along with mailing list(s) already created. The *Freeform MailChimp* API integration requires that you have *Freeform Next Pro*. If you currently have Freeform Next Basic, you can [purchase an upgrade to Freeform Next Pro here](https://solspace.com/expressionengine/freeform/pro).

MailChimp integration includes support for the following:

* Field mapping to standard and custom fields (of text type only), per list.

## Setup Instructions <a href="#setup" id="setup" class="docs-anchor">#</a>

1. Create & get API Key from MailChimp:
	* Go to [MailChimp website](http://mailchimp.com) and log into your account.
	* Go to the **Extras > API keys** page.
	* Click the **Create A Key** button at the bottom of the page.
	* After the page reloads, copy the newly created key under the **API key** column.
2. Setup Integration on your site:
	* Go to the [Mailing Lists section in Freeform Integrations area](mailing-list-integrations.md) (**Freeform Next > Integrations > Mailing Lists**)
	* Click the **New Integration** at the top right.
	* Select *MailChimp* from the **Service Provider** select dropdown.
	* Enter a name and handle for the integration.
	* Paste the MailChimp API key into the **API Key** field in Freeform.
	* At the bottom of the page, click the **Save** button.
3. Verify Authorization:
	* After the integration is saved, it'll return you to the list of mailing list integrations.
	* Click into the newly created integration.
	* Confirm that there is green circle with **Authorized** in the middle of the page.
