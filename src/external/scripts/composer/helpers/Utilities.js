/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import Hashids from "hashids";
import { underscored } from "underscore.string";

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
  notification.innerHTML = text;

  const block = document.querySelectorAll(".ee-main__content")[0];

  block.appendChild(notification);

  setTimeout(() => {
    block.removeChild(notification);
  }, 3000);
}
