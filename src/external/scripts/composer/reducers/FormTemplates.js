/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import * as ActionTypes from "../constants/ActionTypes";

const initialState = {
  isFetching: false,
  didInvalidate: false,
  solspaceTemplates: [],
  list: [],
};

/**
 * Deals with AJAX state changes
 *
 * @param state
 * @param action
 *
 * @returns {object}
 */
export function templates(state = initialState, action) {
  switch (action.type) {
    case ActionTypes.REQUEST_FORM_TEMPLATES:
      return state;

    case ActionTypes.RECEIVE_FORM_TEMPLATES:
      return {
        ...state,
        list: action.templateData,
      };

    case ActionTypes.INVALIDATE_FORM_TEMPLATES:
      return {
        ...state,
        didInvalidate: true,
      };

    default:
      return state;
  }
}
