# Salesforce CRM API Integration

This documentation page assumes you have read over the [CRM Integration Overview page](crm-integrations.md). If you have not yet read it, please do so now. We also assume that you have a [Salesforce](https://www.salesforce.com) account already. This integration requires that you have *Freeform Pro*. If you currently have Freeform Lite, you can purchase an upgrade to Freeform Pro by visiting your account area.

Includes support for the following:

* Field mapping to standard and custom *Lead* object fields:
	* Text fields:
		* String
		* Textarea
		* Email
		* URL
		* Address
		* Checkbox
		* Picklist
		* Multipicklist
	* Numeric fields:
		* Number
		* Phone
		* Currency
	* There are some limitations to types of fields that can be mapped, such as **Lookup** fields.
* Available fields are pulled from the *Lead* object in Salesforce, which is located at:
	1. **Setup** (top right)
	2. **Objects & Fields** (left nav)
	3. **Object Manager**
	4. **Lead**
	5. **Fields & Relationships** (top middle menu)


## Setup Instructions <a href="#setup" id="setup" class="docs-anchor">#</a>

1. Prepare Salesforce's end for Integration:
	* Open another browser tab and go to [Salesforce website](https://login.salesforce.com) and log into your account.
	* On the left navigation menu, click on **Apps**, then click **App Manager**.
	* At the top right corner of the page, click the **New Connected App** button.
	* Fill out the fields in the **Basic Information** section.
	* In the **API (Enable OAuth Settings)** section, click the **Enable OAuth Settings** checkbox.
	* More fields will appear. In the **Callback URL** field, enter any valid URL that begins with **https** (it could even be **https://google.com**, as we don't use this part).
	* In the **Selected OAuth Scopes** field, select the following permissions from the list and click **Add** arrow button:
		* **Allow access to your unique identifier (openid)**
		* **Perform requests on your behalf at any time (refresh_token, offline_access)**
	* You shouldn't need to fill out any further fields, and then click **Save** button.
	* You will be taken to a new page that lists info about your newly created app, including **Consumer Key** and **Consumer Secret** values. You will need to copy each of these values.
		* Salesforce gets tricky to navigate, so do yourself a favor and copy and paste these 2 values into a text editor for now, being sure to label each too. You'll save yourself some extra steps later on.
	* At the top middle of the page, click on the **Manage** button.
	* At the top middle of the next page, click the **Edit Policies** button.
	* Under the **OAuth policies** section, adjust the following settings:
		* In the **Permitted Users** field, be sure that it is set to **All users may self-authorize**.
		* In the **IP Relaxation** field, change the setting to **Relaxed IP restrictions**.
		* Click **Save** button at bottom of page.
	* If you copy and pasted the **Consumer Key** and **Consumer Secret** values in a text editor, you can skip these next couple steps:
		* To go back to your app to see these values, click on the **App Manager** navigation item (under **Apps**)
		* Find your app in the list. Then in the right column, click the down arrow, and then click **View**.
2. Prepare your site's end for Integration:
	* Open up the EE **config.php** file located in the **/system/user/config/** directory and create a Freeform config item with the following settings:
		* **salesforce_client_id** is the Salesforce **Consumer Key** value.
		* **salesforce_client_secret** is the Salesforce **Consumer Secret** value.
		* **salesforce_username** is your Salesforce account username/email address.
		* **salesforce_password** is your Salesforce account password.
	* Your final EE **config.php** file should contain something like this:

			$config['freeform_next'] = [
				'salesforce_client_id'     => '7SDf7GFDG6O76sd798FdG98s9897F9G7dSFDF9G7sd980G8dfG9FG_aSD650g8dsh7D98g79Fs98ds0788Ps',
				'salesforce_client_secret' => '1234567890123456789',
				'salesforce_username'      => 'you@youremail.com',
				'salesforce_password'      => 'yourSalesforcePassword',
			];

3. Prepare the Connection:
	* Go to the [CRM section in Freeform Integrations area](crm-integrations.md) (**Freeform > Integrations > CRM**)
	* Click the **New Integration** at the top right.
	* Select *Salesforce Lead* from the **Service Provider** select dropdown.
	* Enter a name and handle for the integration.
	* At the bottom of the page, click the **Save** button.
4. Verify Authorization:
	* After the integration is saved, it'll return you to the list of CRM integrations.
	* Click into the newly created integration.
	* Confirm that there is green circle with **Authorized** in the middle of the page.
	* That's it! You can now use this integration inside Composer.


## Seeing an Error? <a href="#errors" id="errors" class="docs-anchor">#</a>

* No 'refresh_token' present in auth response for SalesforceLead. Enable offline-access for your app.
	* Make sure that the **Perform requests on your behalf at any time (refresh_token, offline_access)** setting is added to the **Selected OAuth Scopes** field for your app in Salesforce.
