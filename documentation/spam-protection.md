# Spam Protection

Freeform includes 2 choices for robust spam control features to make managing forms easier.

- [Javascript Honeypot](#honeypot)
- [reCAPTCHA](#recaptcha) (Pro)


## Javascript Honeypot <a href="#honeypot" id="honeypot" class="docs-anchor">#</a>

Freeform does NOT use [EE's native CAPTCHA feature](https://docs.expressionengine.com/latest/security/captchas.html) as it has always proven ineffective. Instead, it includes its own Javascript-based honeypot spam protection, which will be immeasurably more effective. This is enabled by default, but can be disabled in the [Freeform Settings](settings.md#spam-protection).

* Each time a form is loaded, it stores a unique honeypot per session, which has a timestamp, unique name and unique hash value.
	* Both name and hash must match to successfully submit the form or advance to the next page.
	* A user is allowed 100 honeypot values per user session (highly unnecessary but in case your site has a form in a common place like a footer, etc it'll help prevent unwanted blocking of legitimate users).
	* Honeypots are stored in the session for 3 hours, and then are removed.
* When the form opens, the value is wrong by default, and then javascript swaps in the correct value.
	* If the submission fails the honeypot test, the form will appear to submit successfully, but will not store the data. An error is not displayed so as not to give away the spam controls.
		* To troubleshoot, you can view the list of **Forms** in *Freeform* control panel area and see if the spam column count is incrementing.
* The honeypot is not a hidden field, but is positioned absolutely with height `0` and width `0`, so the field is not visible.
* This spam protection method requires javascript be enabled for the user's browser, otherwise it will fail.


## reCAPTCHA (Pro) <a href="#recaptcha" id="recaptcha" class="docs-anchor">#</a>

If you wish to use *reCAPTCHA* for your forms, enable the [reCAPTCHA](settings.md#recaptcha) setting and fill in the reCAPTCHA *Site Key* and *Secret Key*. To add reCAPTCHA to your forms, open up and edit each form and drag over the **reCAPTCHA** special field anywhere you like into your form layout.

*reCAPTCHA* fields will render automatically like the rest of your fields in your form, but if you're building a form manually, you'd call it like this (using the Hash value for *reCAPTCHA* field in Property Editor of Composer, e.g. `MD1KzPw68`):

	{field:grecaptcha_MD1KzPw68:render}
