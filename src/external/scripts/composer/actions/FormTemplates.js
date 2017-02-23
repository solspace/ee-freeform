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

      const url = urlBuilder("freeform_next/form_templates");
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
