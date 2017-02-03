/*
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import * as ActionTypes from "../constants/ActionTypes";
import fetch from 'isomorphic-fetch';
import {urlBuilder} from "../app";

function requestFormTemplates() {
  return {
    type: ActionTypes.REQUEST_FORM_TEMPLATES,
  }
}

function receiveFormTemplates(templateData) {
  return {
    type: ActionTypes.RECEIVE_FORM_TEMPLATES,
    templateData,
  }
}

export function invalidateFormTemplates() {
  return {
    type: ActionTypes.INVALIDATE_FORM_TEMPLATES,
  }
}

export function fetchFormTemplatesIfNeeded() {
  return function(dispatch, getState) {
    if (shouldFetchFormTemplates(getState())) {
      dispatch(requestFormTemplates());

      const url = urlBuilder("freeform/api/formTemplates");
      return fetch(url, {credentials: 'same-origin'})
        .then(response => response.json())
        .then(json => {
          dispatch(receiveFormTemplates(json));
        });
    } else {
      Promise.resolve();
    }
  }
}

function shouldFetchFormTemplates(state) {
  const templates = state.templates.list;

  if (!templates) {
    return true;
  } else if (state.templates.isFetching) {
    return false;
  } else {
    return state.templates.didInvalidate;
  }
}
