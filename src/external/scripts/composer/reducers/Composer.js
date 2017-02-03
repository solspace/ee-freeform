/*
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import {hashFromTime} from "../helpers/Utilities";
import * as ActionTypes from "../constants/ActionTypes";
import * as FieldTypes from "../constants/FieldTypes";
import {modifyGroupValues} from "./PropertyEditor";

function layout(state = [[]], action) {
  let clonedState = null;

  switch (action.type) {
    case ActionTypes.ADD_FIELD_TO_NEW_ROW:
      clonedState  = [...state];
      const newRow = {
        id: hashFromTime(),
        columns: [action.hash]
      };

      if (!clonedState[action.pageIndex]) {
        clonedState[action.pageIndex] = [];
      }

      clonedState[action.pageIndex].push(newRow);
      return clonedState;

    case ActionTypes.ADD_COLUMN_TO_ROW:
      clonedState = [...state];
      clonedState[action.pageIndex][action.rowIndex].columns.splice(action.columnIndex, 0, action.hash);

      return clonedState;

    case ActionTypes.ADD_COLUMN_TO_NEW_ROW:
      return addColumnToNewRow(state, action);

    case ActionTypes.ADD_PAGE:
      return [...state, []];

    case ActionTypes.REMOVE_PAGE:
      return removePageFromLayout(state, action);

    default:
      return state;
  }
}

function properties(state = {}, action) {
  switch (action.type) {
    case ActionTypes.ADD_FIELD_TO_NEW_ROW:
    case ActionTypes.ADD_COLUMN_TO_ROW:
    case ActionTypes.ADD_COLUMN_TO_NEW_ROW:
      return {...state, [action.hash]: action.properties};

    case ActionTypes.ADD_PAGE:
      return {
        ...state,
        [`page${action.index}`]: action.properties,
      };

    case ActionTypes.REMOVE_PAGE:
      return state;

    case ActionTypes.UPDATE_PROPERTY:
      return {
        ...state,
        [action.hash]: {
          ...state[[action.hash]],
          ...action.keyValueObject,
        }
      };

    case ActionTypes.REMOVE_PROPERTY:
      const clonedState = {...state};
      const propData    = clonedState[action.hash];

      // If this is an email field property
      // Clean out all MailingList fields which have it
      // Set as the email field to use
      if (propData.type === FieldTypes.EMAIL) {
        for (let key in clonedState) {
          const prop = clonedState[key];
          if (prop.type === FieldTypes.MAILING_LIST) {
            if (prop.emailFieldHash === action.hash) {
              clonedState[key].emailFieldHash = "";
            }
          }
        }
      }

      delete clonedState[action.hash];

      return clonedState;

    default:
      return state;
  }
}

function repositionColumn(state = [], action) {
  const clonedState = [...state];

  const {pageIndex, rowIndex, columnIndex, newRowIndex, newColumnIndex} = action;
  const column                                                          = clonedState[pageIndex][rowIndex].columns[columnIndex];

  clonedState[pageIndex][rowIndex].columns.splice(columnIndex, 1);
  clonedState[pageIndex][newRowIndex].columns.splice(newColumnIndex, 0, column);

  cleanRows(clonedState);

  return clonedState;
}

function addColumnToNewRow(state = [], action) {
  const clonedState                 = [...state];
  const {pageIndex, rowIndex, hash} = action;

  for (var pageRowIndex of clonedState[pageIndex].keys()) {
    const hashIndex = clonedState[pageIndex][pageRowIndex].columns.indexOf(hash);
    if (hashIndex !== -1) {
      clonedState[pageIndex][pageRowIndex].columns.splice(hashIndex, 1);
    }
  }

  const newRow = {id: hashFromTime(), columns: [hash]};

  if (rowIndex === -1) {
    clonedState[pageIndex].push(newRow);
  } else {
    clonedState[pageIndex].splice(rowIndex, 0, newRow);
  }

  cleanRows(clonedState);

  return clonedState;
}

function cleanRows(layout) {
  for (var pageIndex of layout.keys()) {
    for (var rowIndex of layout[pageIndex].keys()) {
      const row = layout[pageIndex][rowIndex];

      if (row.columns.length === 0) {
        layout[pageIndex].splice(rowIndex, 1);
      }
    }
  }
}

function removeColumn(state = [], action) {
  const clonedState = [...state];

  const {pageIndex, rowIndex, columnIndex} = action;

  clonedState[pageIndex][rowIndex].columns.splice(columnIndex, 1);

  if (clonedState[pageIndex][rowIndex].columns.length === 0) {
    clonedState[pageIndex].splice(rowIndex, 1);
  }

  return clonedState;
}

function removePage(state = [], action) {
  const index = action.index;

  const pageFieldHashes = [];
  state.layout[index].map(
    (row) => {
      row.columns.map((hash) => pageFieldHashes.push(hash));
    }
  );

  const layout = [
    ...state.layout.slice(0, index),
    ...state.layout.slice(index + 1),
  ];

  const properties = {...state.properties};

  delete properties[`page${index}`];

  pageFieldHashes.map((hash) => {
    if (properties[hash]) {
      delete properties[hash];
    }
  });

  // If the deleted page isn't the last one,
  // We move all trailing pages back by 1 index in the properties
  for(var key in properties) {
    if (!properties.hasOwnProperty(key)) continue;

    const matches = key.match(/^page(\d+)$/);
    if (matches && matches[1]) {
      const pageIndex = matches[1];
      if (pageIndex > index) {
        properties[`page${pageIndex - 1}`] = properties[`page${pageIndex}`];
        delete properties[`page${pageIndex}`];
      }
    }
  }

  return {
    ...state,
    layout,
    properties,
  };
}

export function composer(state = [], action) {
  let idx         = null;
  let clonedState = null;

  switch (action.type) {
    case ActionTypes.ADD_FIELD_TO_NEW_ROW:
    case ActionTypes.ADD_PAGE:
    case ActionTypes.ADD_COLUMN_TO_ROW:
    case ActionTypes.ADD_COLUMN_TO_NEW_ROW:
      clonedState        = {...state};
      clonedState.layout = layout(state.layout, action);
      if (action.properties) {
        clonedState.properties = properties(state.properties, action);
      }

      return clonedState;

    case ActionTypes.REMOVE_PAGE:
      return removePage(state, action);

    case ActionTypes.REPOSITION_COLUMN:
      idx = action.pageIndex;

      if (state.layout[idx]) {
        return {...state, layout: repositionColumn(state.layout, action)};
      }

      return state;

    case ActionTypes.REMOVE_COLUMN:
      idx = action.pageIndex;

      if (state.layout[idx]) {
        return {...state, layout: removeColumn(state.layout, action)}
      }

      return state;

    case ActionTypes.UPDATE_PROPERTY:
    case ActionTypes.REMOVE_PROPERTY:
      return {...state, properties: properties(state.properties, action)};

    case ActionTypes.ADD_VALUE_SET:
    case ActionTypes.CLEAN_UP_VALUES:
    case ActionTypes.UPDATE_VALUE_SET:
    case ActionTypes.UPDATE_IS_CHECKED:
    case ActionTypes.TOGGLE_CUSTOM_VALUES:
    case ActionTypes.REORDER_VALUE_SET:
    case ActionTypes.REMOVE_VALUE_SET:
      return {...state, properties: modifyGroupValues(state.properties, action)};

    default:
      return state;
  }
}

export function context(state = [], action) {
  switch (action.type) {
    case ActionTypes.SWITCH_PAGE:
      return {...state, page: action.index, hash: `page${action.index}`};

    case ActionTypes.SWITCH_HASH:
      return {...state, hash: action.hash};

    default:
      return state;
  }
}

export function formId(state = null, action) {
  switch (action.type) {
    case ActionTypes.UPDATE_FORM_ID:
      return parseInt(action.id);

    default:
      return state;
  }
}
