# Updates

## Updating Instructions <a href="#update" id="update" class="docs-anchor">#</a>
**NOTE:** There is currently no migration path from classic Freeform Classic to Freeform. Freeform can be used alongside classic Freeform/Freeform Pro (Freeform Classic) installations if you're wanting to try out and/or switch to Freeform. We recommend you read the [Switching from Freeform Classic](switching-from-classic.md) documentation to learn more about the differences.

**NOTE:** If you're upgrading from Freeform **Lite** to Freeform **Pro**, you can follow these same instructions below. There will be no **update** button/process as it is not required, though you may see the **Update** button if the version number is greater (e.g. Lite 1.1.3 to Pro 1.1.6).

Freeform has its own **Update Service** built in, which means that every time there's an update available, an **Updates Available** nav item will show up in the Freeform add-on area of the EE control panel. You can then review the changelog there, or [view it here](changelog.md).

[![Freeform's Built in Update Service](images/cp_updates.png)](images/cp_updates.png)

To download the update, simply click the **Get Latest Version** button, and you'll be taken to your account area on the Solspace site where you can log in and download the updated package for Freeform. Then follow the steps below:

1. Within your EE sites **/system/user/addons/** and **/themes/user/** directories, delete the *freeform_next* folders.
2. Unzip the download package:
	* Copy the *freeform_next* folder to your EE sites **/system/user/addons/** directory.
	* Open the **themes** folder and copy the *freeform_next* folder to your EE sites **/themes/user/** directory.
2. Go to the **Developer > Add-on Manager** page and click **Update** button for the *Freeform Pro* (or *Freeform Lite*) add-on.
