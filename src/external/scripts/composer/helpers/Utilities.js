/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import Hashids from "hashids";
import {underscored} from "underscore.string";

const minHashLength = 9;
const hashids       = new Hashids("composer", minHashLength);

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
 * @returns {*}
 */
export function getHandleValue(value) {
  let handleValue = underscored(value, true);
  handleValue = handleValue.replace(/[^a-zA-Z0-9_]/g, '');

  return handleValue;
}
