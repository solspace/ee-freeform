# Demo Templates
Freeform includes a complete set of demo templates. These demo templates let you have a fully functioning area on your website with just a couple clicks! Further to this, it allows you to see real world examples of the template code in action, which will help you get acquainted with Freeform quicker.

[![Demo Templates Install](images/cp_settings-demo-templates.png)](images/cp_settings-demo-templates.png)

* [Installing the Demo Templates](#install)
* [Troubleshooting](#troubleshooting)
* [Manual Install](#manual-install)

## Installing the Demo Templates <a href="#install" id="install" class="docs-anchor">#</a>

To install the Demo Templates, simply visit the **Demo Templates** nav item in Freeform (**Freeform Next > Settings > Demo Templates**).

By default, the Demo Templates page will be set to **freeform_next_demo** for template group name. You can change this to whatever you like.

During installation of demo templates, Freeform will copy the template folders and files (as shown on page) to the **/craft/templates/** directory. So using the above example, your templates would be located at **/craft/templates/freeform_demo/**.

Freeform will also look for an **assets** folder in your **public** directory and attempt to load files such as CSS, JS, fonts and image files (as shown on page) to a folder inside that, named **freeform_demo** (using the example above). If an **/assets/** directory does not exist, Freeform will attempt to create that directory as well.

And lastly, Freeform will create several template routes for you (as shown on page) for the demo templates to work correctly.

If you encounter any issues while attempting to install the demo templates, please see the [Troubleshooting](#troubleshooting) guide.


## Troubleshooting <a href="#troubleshooting" id="troubleshooting" class="docs-anchor">#</a>

If you encounter any issues trying to install the demo templates, it's likely that your database user does not have sufficient privileges or your server configuration does not allow some or all of the actions.

If you cannot change or adjust your permissions or configuration, you can install the demo templates manually. To install them manually, refer to the [Manual Install](#manual-install) instructions below.


## Manual Install <a href="#manual-install" id="manual-install" class="docs-anchor">#</a>

To install the demo templates manually, just follow the instructions below:

1. Inside the **/craft/plugins/freeform/codepack/** directory, copy the **templates** folder into the **/craft/templates/** directory, and rename the Freeform **templates** folder to **demo** (so it should now be located at **/craft/templates/demo/**).
2. In your root public directory, create a directory called **assets** if it does not already exist.
3. Inside the **/craft/plugins/freeform/codepack/** directory, copy the **assets** folder into the public **/assets/** directory, and rename the Freeform **assets** folder to **demo** (so it should now be located at **/assets/demo/**).
4. Copy and paste the following routes into your *routes.php* configuration file (**/craft/config/routes.php**):

`'demo/bootstrap/(?P<slug>[^\/]+)/submissions/(?P<id>\d+)/success' => 'demo/bootstrap/view_submission.html',
'demo/bootstrap/(?P<slug>[^\/]+)/submissions/(?P<id>\d+)' => 'demo/bootstrap/view_submission.html',
'demo/bootstrap/(?P<slug>[^\/]+)/submissions' => 'demo/bootstrap/submissions.html',
'demo/bootstrap/(?P<slug>[^\/]+)' => 'demo/bootstrap/view.html',
'demo/bootstrap/(?P<slug>[^\/]+)/success' => 'demo/bootstrap/view.html',
'demo/foundation/(?P<slug>[^\/]+)' => 'demo/foundation/view.html',`

Visit your demo templates at: **yoursite.com/demo**
