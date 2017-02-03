# Translating

Currently, all of the Freeform control panel area except for the Composer interface, is translatable. Support for translations of field names, etc inside Composer will come in a future release.

Front end template translating of fields and error messages is also available.

To make a translation, you can do this in a variety of ways:

* **Recommended**:
	* Follow Craft's instructions for [Translating Static Text](https://craftcms.com/support/static-translations).
		* Copy the contents inside the Solspace Freeform *en.php* file located at the **/craft/plugins/freeform/translations** folder, and paste those language keys inside your translation file(s) located at **/craft/translations**.

---

* **Alternate Options** (will get overwritten when performing updates):
	* Duplicate the *en.php* file inside the **/craft/plugins/freeform/translations** folder and rename the file to the 2-letter country/language code (e.g. **German** = *de*), and begin translating.
	* Copy the contents inside the Solspace Freeform *en.php* file located at the **/craft/plugins/freeform/translations** folder, and paste those language keys inside your alternate translation file(s) located at **/craft/app/translations**.
		* In either case above, be sure to make backups of your translations before updating Craft or Freeform.

Visit the Craft documentation for more information about [Setting Up a Localized Site](https://craftcms.com/docs/localization-guide).

If you'd like to share your translation with others, email us ([support@solspace.com](mailto:support@solspace.com)) a copy of the translation file, and we'll consider including it in the main Solspace Freeform plugin package.
