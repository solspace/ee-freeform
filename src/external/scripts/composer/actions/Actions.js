/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import * as ActionTypes from "../constants/ActionTypes";

export function switchPage(index) {
  return {
    type: ActionTypes.SWITCH_PAGE,
    index,
  };
}

export function addPage(index) {
  return {
    type: ActionTypes.ADD_PAGE,
    index,
    properties: {
      type: "page",
      label: `Page ${index + 1}`,
    },
  };
}

export function removePage(index) {
  return {
    type: ActionTypes.REMOVE_PAGE,
    index,
  };
}

export function switchHash(hash) {
  return {
    type: ActionTypes.SWITCH_HASH,
    hash,
  };
}

export function addFieldToNewRow(hash, properties, pageIndex) {
  return {
    type: ActionTypes.ADD_FIELD_TO_NEW_ROW,
    hash,
    properties,
    pageIndex,
  };
}

export function addColumnToRow(rowIndex, columnIndex, hash, properties, pageIndex) {
  return {
    type: ActionTypes.ADD_COLUMN_TO_ROW,
    rowIndex,
    columnIndex,
    hash,
    properties,
    pageIndex,
  };
}

export function addColumnToNewRow(rowIndex, hash, properties, pageIndex, prevPageIndex = null) {
  return {
    type: ActionTypes.ADD_COLUMN_TO_NEW_ROW,
    rowIndex,
    hash,
    properties,
    pageIndex,
    prevPageIndex,
  };
}

export function repositionColumn(columnIndex, rowIndex, newColumnIndex, newRowIndex, pageIndex) {
  return {
    type: ActionTypes.REPOSITION_COLUMN,
    columnIndex,
    rowIndex,
    newColumnIndex,
    newRowIndex,
    pageIndex,
  };
}

export function removeColumn(hash, columnIndex, rowIndex, pageIndex) {
  return {
    type: ActionTypes.REMOVE_COLUMN,
    columnIndex,
    rowIndex,
    pageIndex,
    hash,
  };
}

export function updateProperty(hash, keyValueObject) {
  return {
    type: ActionTypes.UPDATE_PROPERTY,
    hash,
    keyValueObject,
  };
}

export function resetProperties(hash, defaultProperties) {
  return {
    type: ActionTypes.RESET_PROPERTIES,
    hash,
    defaultProperties,
  };
}

export function removeProperty(hash) {
  return {
    type: ActionTypes.REMOVE_PROPERTY,
    hash,
  };
}

export function addValueSet(hash) {
  return {
    type: ActionTypes.ADD_VALUE_SET,
    hash,
  };
}

export function cleanUpValues(hash) {
  return {
    type: ActionTypes.CLEAN_UP_VALUES,
    hash,
  };
}

export function updateValueSet(hash, index, value, label) {
  return {
    type: ActionTypes.UPDATE_VALUE_SET,
    hash,
    index,
    value,
    label,
  };
}

export function updateIsChecked(hash, index, isChecked) {
  return {
    type: ActionTypes.UPDATE_IS_CHECKED,
    hash,
    index,
    isChecked,
  };
}

export const insertValue = (hash, value) => ({
  type: ActionTypes.INSERT_VALUE,
  hash,
  value,
});

export const removeValue = (hash, value) => ({
  type: ActionTypes.REMOVE_VALUE,
  hash,
  value,
});

export function toggleCustomValues(hash, isChecked) {
  return {
    type: ActionTypes.TOGGLE_CUSTOM_VALUES,
    hash,
    isChecked,
  };
}

export function reorderValueSet(hash, index, newIndex) {
  return {
    type: ActionTypes.REORDER_VALUE_SET,
    hash,
    index,
    newIndex,
  };
}

export function removeValueSet(hash, index) {
  return {
    type: ActionTypes.REMOVE_VALUE_SET,
    hash,
    index,
  };
}

export function updateFormId(formId) {
  return {
    type: ActionTypes.UPDATE_FORM_ID,
    id: formId,
  };
}

export function addPlaceholderRow(rowIndex, targetHash) {
  return {
    type: ActionTypes.ADD_PLACEHOLDER_ROW,
    rowIndex,
    targetHash,
  };
}

export function addPlaceholderColumn(rowIndex, index, targetHash) {
  return {
    type: ActionTypes.ADD_PLACEHOLDER_COLUMN,
    rowIndex,
    index,
    targetHash,
  };
}

export function clearPlaceholders() {
  return {
    type: ActionTypes.CLEAR_PLACEHOLDERS,
  };
}
