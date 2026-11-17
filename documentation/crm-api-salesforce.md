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


## Client Credentials Setup (External Client Apps)

Freeform 3.x supports OAuth 2.0 Client Credentials authentication for Salesforce
External Client Apps and Connected Apps with this flow enabled. Existing
integrations continue to use Username-Password authentication unless their
**Authentication Method** is changed. New integrations default to **Client
Credentials**, listed first in the authentication radio choices. Web Server
authentication with PKCE is not implemented.

1. In Salesforce **Setup > External Client App Manager**, create or configure an
   External Client App. Enable OAuth and **Client Credentials Flow**, and grant
   the **Manage user data via APIs (api)** scope.
2. Configure the app's policies: use **Admin approved users are pre-authorized**,
   authorize the integration user through the appropriate permission set, and set
   **Run As (Username)** to that integration user's exact username. Give the user
   API access and the Lead/field permissions needed by your forms.
3. Copy the app's **Consumer Key** and **Consumer Secret** from **Manage Consumer
   Details**. From **Setup > My Domain**, copy the organization's full HTTPS
   My Domain URL.
4. In **Freeform > Integrations > CRM**, create or edit a **Salesforce Lead**
   integration. Choose **Client Credentials** under **Authentication Method**.
   This is selected by default for new integrations. Existing integrations keep
   their previous method, including old records with no explicit method stored.
5. Enter **My Domain URL**, **Consumer Key**, and **Consumer Secret** in the
   inputs below the authentication choices. Only fields for the selected method
   are shown. Salesforce's Consumer Key and Consumer Secret correspond to the
   OAuth Client ID and Client Secret.
6. Save, confirm that the integration shows **Authorized**, refresh its field
   mappings in Composer, and submit a test form. Verify the created Lead's field
   values and ownership.

Credentials entered here apply only to that integration. Saved values remain
populated when you edit it. Consumer Secret, Password, and Security Token use
password inputs with bullets and a show/hide control. These values are encrypted
in the database using ExpressionEngine's site encryption key. Authorized admins
can reveal saved control-panel secrets in the editor. Keep your EE encryption key
when moving the site or restoring a database backup.

Enter a new value to replace a saved secret. Clearing the field removes that
saved override, allowing its config.php fallback to apply.

### Optional config.php fallback

You can still configure values in EE's `system/user/config/config.php`. An empty
credential field falls back to its config value when no value is saved for that
integration. Config.php credentials are not populated into the form, and saving
other settings does not copy them into the database.

```php
$config['freeform_next']['salesforce_client_credentials_id'] = 'YOUR_CONSUMER_KEY';
$config['freeform_next']['salesforce_client_credentials_secret'] = 'YOUR_CONSUMER_SECRET';
$config['freeform_next']['salesforce_my_domain'] = 'https://example.my.salesforce.com';
```

These keys are separate from the legacy credentials, so both modes can run
alongside each other. Existing `salesforce_client_id`,
`salesforce_client_secret`, `salesforce_username`, and `salesforce_password`
config values keep working for Username-Password connections. Connections relying
on config.php share those values; credentials entered in the control panel are
specific to each integration. Client Credentials does not use a Salesforce
username, password, or security token.

For a sandbox, configure its full My Domain URL, for example
`https://example--uat.sandbox.my.salesforce.com`. The **Sandbox mode** setting
is shown only for legacy Username-Password authentication; Client Credentials
uses the entered or configured My Domain URL. Generic `login.salesforce.com` and
`test.salesforce.com` URLs, paths, and query strings are not accepted.

Client Credentials does not issue refresh tokens. When an access token expires,
Freeform obtains another token using the app's client credentials through its
existing token renewal mechanism. Test the migration in a Salesforce sandbox
before switching a production connection. Creating an External Client App beside
the original Connected App allows testing while the legacy connection continues
to operate.

See Salesforce's [Client Credentials flow documentation](https://help.salesforce.com/s/articleView?id=xcloud.remoteaccess_oauth_client_credentials_flow_ca.htm&language=en_US&type=5)
and [migration guidance](https://help.salesforce.com/s/articleView?id=005461976&language=en_US&type=1).

## Legacy Username-Password Setup <a href="#setup" id="setup" class="docs-anchor">#</a>

This remains the default for existing integrations. To use it in a new
integration, select **Username-Password (legacy)** under **Authentication Method**.
Enter the **Consumer Key**, **Consumer Secret**, **Username**, and **Password**
in the fields shown below it. An optional **Security Token** input is appended to
the password automatically. Leave it blank if the password already includes the
token. These fields also support the config.php fallback described above. Salesforce is
[retiring this flow on February 20, 2027](https://help.salesforce.com/s/articleView?id=005461279&language=en_US&type=1).
Use Client Credentials for new connections and migrate existing connections before
that deadline. The instructions below describe the legacy configuration.

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
