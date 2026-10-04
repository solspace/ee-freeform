/* Preview for the visible CAPTCHA provider selected in Spam Protection. */
import PropTypes from "prop-types";
import React, { Component } from "react";

export default class Recaptcha extends Component {
  static propTypes = {
    properties: PropTypes.shape({
      type: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
    }).isRequired,
  };

  render() {
    const labels = { recaptcha: "reCAPTCHA", turnstile: "Turnstile", hcaptcha: "hCaptcha" };
    return (
      <div className="composer-recaptcha" aria-label="CAPTCHA preview">
        <span>{labels[window.captchaProvider] || "CAPTCHA"}</span>
        <span aria-hidden="true">◻</span>
      </div>
    );
  }
}
