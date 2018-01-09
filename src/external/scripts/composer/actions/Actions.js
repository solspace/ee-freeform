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
    index: index,
  }
}

export function addPage(index) {
  return {
    type: ActionTypes.ADD_PAGE,
    index: index,
    properties: {
      type: "page",
      label: `Page ${index + 1}`
    }
  }
}

export function removePage(index) {
  return {
    type: ActionTypes.REMOVE_PAGE,
    index,
  }
}

export function switchHash(hash) {
  return {
    type: ActionTypes.SWITCH_HASH,
    hash: hash,
  }
}

export function addFieldToNewRow(hash, properties, pageIndex) {
  return {
    type: ActionTypes.ADD_FIELD_TO_NEW_ROW,
    hash: hash,
    properties: properties,
    pageIndex: pageIndex,
  }
}

export function addColumnToRow(rowIndex, columnIndex, hash, properties, pageIndex) {
  return {
    type: ActionTypes.ADD_COLUMN_TO_ROW,
    rowIndex: rowIndex,
    columnIndex: columnIndex,
    hash: hash,
    properties: properties,
    pageIndex: pageIndex,
  }
}

export function addColumnToNewRow(rowIndex, hash, properties, pageIndex) {
  return {
    type: ActionTypes.ADD_COLUMN_TO_NEW_ROW,
    rowIndex: rowIndex,
    hash: hash,
    properties: properties,
    pageIndex: pageIndex,
  }
}

export function repositionColumn(columnIndex, rowIndex, newColumnIndex, newRowIndex, pageIndex) {
  return {
    type: ActionTypes.REPOSITION_COLUMN,
    columnIndex: columnIndex,
    rowIndex: rowIndex,
    newColumnIndex: newColumnIndex,
    newRowIndex: newRowIndex,
    pageIndex: pageIndex,
  }
}

export function removeColumn(hash, columnIndex, rowIndex, pageIndex) {
  return {
    type: ActionTypes.REMOVE_COLUMN,
    columnIndex: columnIndex,
    rowIndex: rowIndex,
    pageIndex: pageIndex,
    hash: hash,
  }
}

export function updateProperty(hash, keyValueObject) {
  return {
    type: ActionTypes.UPDATE_PROPERTY,
    hash: hash,
    keyValueObject: keyValueObject,
  }
}

export function removeProperty(hash) {
  return {
    type: ActionTypes.REMOVE_PROPERTY,
    hash: hash,
  }
}

export function addValueSet(hash) {
  return {
    type: ActionTypes.ADD_VALUE_SET,
    hash: hash,
  }
}

export function cleanUpValues(hash) {
  return {
    type: ActionTypes.CLEAN_UP_VALUES,
    hash: hash,
  }
}

export function updateValueSet(hash, index, value, label) {
  return {
    type: ActionTypes.UPDATE_VALUE_SET,
    hash: hash,
    index: index,
    value: value,
    label: label,
  }
}

export function updateIsChecked(hash, index, isChecked) {
  return {
    type: ActionTypes.UPDATE_IS_CHECKED,
    hash: hash,
    index: index,
    isChecked: isChecked,
  }
}

export function toggleCustomValues(hash, isChecked) {
  return {
    type: ActionTypes.TOGGLE_CUSTOM_VALUES,
    hash: hash,
    isChecked: isChecked,
  }
}

export function reorderValueSet(hash, index, newIndex) {
  return {
    type: ActionTypes.REORDER_VALUE_SET,
    hash,
    index,
    newIndex,
  }
}

export function removeValueSet(hash, index) {
  return {
    type: ActionTypes.REMOVE_VALUE_SET,
    hash,
    index,
  }
}

export function updateFormId(formId) {
  return {
    type: ActionTypes.UPDATE_FORM_ID,
    id: formId,
  }
}

export function addPlaceholderRow(rowIndex, targetHash) {
  return {
    type: ActionTypes.ADD_PLACEHOLDER_ROW,
    rowIndex,
    targetHash,
  }
}

export function addPlaceholderColumn(rowIndex, index, targetHash) {
  return {
    type: ActionTypes.ADD_PLACEHOLDER_COLUMN,
    rowIndex,
    index,
    targetHash,
  }
}

export function clearPlaceholders() {
  return {
    type: ActionTypes.CLEAR_PLACEHOLDERS,
  }
}
