/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

const validProperties = [
  "hash",
  "id",
  "handle",
  "label",
  "required",
  "value",
  "checked",
  "placeholder",
  "instructions",
  "values",
  "options",
  "showAsRadio",
  "notificationId",
  "assetSourceId",
  "integrationId",
  "resourceId",
  "emailFieldHash",
  "position",
  "labelNext",
  "labelPrev",
  "disablePrev",
  "mapping",
  "fileKinds",
  "maxFileSizeKB",
  "rows",
  "showCustomValues",
  "source",
  "name",
  "type",
  "storeData",
];

export default class PropertyHelper {
  static getProperties(hash, store) {
    const state        = store.getState();
    const propertyList = state.composer.properties;

    if (propertyList[hash]) {
      return propertyList[hash];
    }

    return null;
  }

  static getCleanProperties(properties) {
    let cleanProps = {};
    for (let key in properties) {
      if (!properties.hasOwnProperty(key)) continue;
      if (validProperties.indexOf(key) === -1) continue;

      cleanProps[key] = properties[key];
    }

    return cleanProps;
  }
}
