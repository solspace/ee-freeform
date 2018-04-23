# Guide to GDPR Compliance

This documentation page is a general guide to help you understand how to get Freeform forms to be compliant with the European Union's [General Data Protection Regulation (GDPR)](https://ec.europa.eu/info/law/law-topic/data-protection_en) in most cases, which becomes effective May 25, 2018. GDPR compliance goes beyond forms (e.g. notification of data breaches, etc), so please be sure to review [this official guide to GDPR](https://ico.org.uk/for-organisations/guide-to-the-general-data-protection-regulation-gdpr).

*Disclaimer: In no way does this resource guarantee compliance or serve as legal advice. You may need to seek legal counsel to ensure your website fully complies with GDPR.*


## Who does GDPR impact?
GDPR affects every company that uses personal data from EU citizens. If you’re collecting data from users in the EU, you need to comply with GDPR regardless of where you’re based.


## What does it mean for me?
Our understanding of GDPR compliance within the context of Freeform, is that you need explicit consent from the person filling out the form to agree to you having their data, as well as allowing the user to request access to the information you have stored on them, and the ability to request to have that data removed promptly. We often receive requests from customers wondering if there's a way to have Freeform not store the data but still send an email notification to the admin(s). This is not sufficient, as email notifications containing personal information about customers is still you or your organization storing their sensitive data somewhere on a server, etc.

It's also worth noting that not all types of forms require consent. If you're conducting an anonymous survey, quiz or poll, that does not collect any personally identifying information, you likely shouldn't need to worry about GDPR.

That said, updating your forms to be compliant shouldn't be too much work. Just follow this guide below:


- [Definition of Stored Data](#stored-data)
- [Ask for Consent](#ask-consent)
- [Withdrawal of Consent](#withdrawal-consent)
- [Review](#review)


## Definition of Stored Data <a href="#stored-data" id="stored-data" class="docs-anchor">#</a>

We define *stored data* as any data that is collected from a Freeform form and stored on your website, email notifications and API integrations such as CRM and Mailing List services. It's important to understand that asking for consent essentially means you require the user to consent to any/all of the above or nothing at all (can't submit the form).


## Ask for Consent <a href="#ask-consent" id="ask-consent" class="docs-anchor">#</a>

### Overview
Essentially, the easiest blanket approach you can take will be adding a checkbox to your form and setting it to be required to be compliant with GDPR. While almost cut and dry, please read below for the important specifics about doing this.

### The Checkbox
In your form, simply create an additional field of the *Checkbox* fieldtype, and set it to be required. It's important however, that the following is adhered to:

- The checkbox needs to be clearly labelled and easy to understand. A good example would be something like:
> I consent to Company Name collecting and storing my data from this form.

- The consent needs to be separate and cannot be bundled with other consent, terms and conditions, notices, etc.
- The checkbox must be a positive opt-in, and cannot be pre-checked by default.
- Set the checkbox to be **required** so that the form cannot submit without consent being given.

If you'd like to add additional disclaimer information above or below the checkbox, you can do this by using an *HTML Block* that is a special fieldtype available inside Composer.

### Proof of Consent
While we can probably all agree this part might be somewhat meaningless since the data could be easily manipulated, it's required that you have the ability to "prove" that the user consented to the data being stored. The checkbox field you created will store a `y` value (or whatever you set for it) in the database, so you're covered here. No other action is necessary.


## Withdrawal of Consent <a href="#withdrawal-consent" id="withdrawal-consent" class="docs-anchor">#</a>

### Overview
You must make it easy for users to withdrawal consent (and have you remove all of their data). You must also tell them how they can do this. To cover all angles here, you might consider some or all of the following:

- Include instructions and/or Consent Withdrawal form on the success page after they've submitted your form.
- Include an option/instructions to remove consent in any email notifications generated.
- Include an option/instructions to remove consent in any future promotional marketing email communications.

### Withdrawal Form
The withdrawal of consent does not need to be automated (but that might help if your sites deals with a high volume of users). To setup a form to handle this, it's required that the process:

- Only requires the user submit their email address.
- Does not require the user to log into your site.
- Does not require the user to visit more than 1 page to submit their request (needs to be very simple and fast).

A form is not required however. You could also include instructions for the user to send an email to you asking to have their data removed, etc.

### Removing a User's Data
Removing a user's data is simple. If you're requested to remove data about a user, simply delete the Freeform submission(s) associated with them from the Freeform control panel.

- You have 1 month to comply with removal of the user's data.


## Review <a href="#review" id="review" class="docs-anchor">#</a>
So in review here's a summary of the steps required:

1. Create an additional field of the *Checkbox* fieldtype, and set it to be required. Leave it unchecked by default and label it something like **I consent to Company Name collecting and storing my data from this form.**
2. Place instructions in email notifications or form success pages explaining how a customer can go about having their data removed from your site.
3. When requested to remove a user's data, be sure to remove all associated submission(s) within 30 days or less.

Again, be sure to review [this official guide to GDPR](https://ico.org.uk/for-organisations/guide-to-the-general-data-protection-regulation-gdpr).
