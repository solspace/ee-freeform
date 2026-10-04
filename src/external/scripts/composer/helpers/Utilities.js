/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import HashidsModule from "hashids";
import { underscored } from "underscore.string";

// hashids ships a Babel-compiled CommonJS default export. esbuild follows Node's
// ESM interop (default === module.exports) and does not auto-unwrap `__esModule`
// default exports the way the old Browserify/Babel build did, so unwrap it here.
const Hashids = HashidsModule.default || HashidsModule;

const minHashLength = 9;
const hashids = new Hashids("composer", minHashLength);

/**
 * Get a hash from the current time
 *
 * @returns {*}
 */
export function hashFromTime() {
  const time = new Date().getTime();

  return hashids.encode(time);
}

/**
 * Hash an ID
 *
 * @param id
 * @returns {*}
 */
export function hashId(id) {
  return hashids.encode(id);
}

/**
 * Get the int value of a hashed ID
 *
 * @param hash
 * @returns {*}
 */
export function deHashId(hash) {
  if (!hash) {
    return null;
  }

  return hashids.decode(hash).pop();
}

/**
 * Strips out all invalid characters from the handle string
 *
 * @param value
 * @param autoUnderscore
 * @returns {*}
 */
export function getHandleValue(value, autoUnderscore = true) {
  let handleValue = value;

  if (autoUnderscore) {
    handleValue = underscored(value, true);
  }

  handleValue = handleValue.replace(/[^a-zA-Z0-9\-_]/g, "");

  return handleValue;
}

/**
 * Shows a notification
 *
 * @param text
 * @param type
 */
export function showNotification(text, type) {
  switch (type) {
    case "error":
      type = "issue";
      break;

    default:
      type = "success";
  }

  let notification = document.createElement("div");
  notification.className = "composer-alert alert alert--" + type + " " + type;
  notification.textContent = String(text);
  notification.setAttribute("role", type === "issue" ? "alert" : "status");
  notification.setAttribute("aria-atomic", "true");

  const block = document.querySelector(".ee-main__content") || document.getElementById("freeform-builder");

  block.appendChild(notification);

  setTimeout(() => {
    if (notification.parentNode === block) {
      block.removeChild(notification);
    }
  }, type === "issue" ? 10000 : 5000);
}
