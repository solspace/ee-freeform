/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import * as ActionTypes from "../constants/ActionTypes";
import fetch from 'isomorphic-fetch'
import {urlBuilder} from "../app";

function requestFields() {
  return {
    type: ActionTypes.REQUEST_FIELDS,
  }
}

function receiveFields(fieldData) {
  return {
    type: ActionTypes.RECEIVE_FIELDS,
    fieldData,
  }
}

export function invalidateFields() {
  return {
    type: ActionTypes.INVALIDATE_FIELDS,
  }
}

export function fetchFieldsIfNeeded() {
  return function(dispatch, getState) {
    if (shouldFetchFields(getState())) {
      dispatch(requestFields());

      const url = urlBuilder("freeform_next/fields");
      console.log(url);
      return fetch(url, {credentials: 'same-origin'})
        .then(response => response.json())
        .then(json => dispatch(receiveFields(json)));
    } else {
      Promise.resolve();
    }
  }
}

function shouldFetchFields(state) {
  const fields = state.fields.fields;

  if (!fields) {
    return true;
  } else if (state.fields.isFetching) {
    return false;
  } else {
    return state.fields.didInvalidate;
  }
}
