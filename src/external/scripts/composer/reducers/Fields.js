/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import * as ActionTypes from "../constants/ActionTypes";

const initialState = {
  isFetching: false,
  didInvalidate: false,
  fields: [],
};

/**
 * Deals with AJAX state changes
 *
 * @param state
 * @param action
 *
 * @returns {object}
 */
export function fields(state = initialState, action) {
  switch (action.type) {
    case ActionTypes.REQUEST_FIELDS:
      return state;

    case ActionTypes.RECEIVE_FIELDS:
      return {
        ...state,
        fields: action.fieldData,
      };

    case ActionTypes.INVALIDATE_FIELDS:
      return {
        ...state,
        didInvalidate: true,
      };

    default:
      return state;
  }
}

/**
 * @param state
 * @returns {Array}
 */
export function specialFields(state = []) {
  return state;
}

/**
 * @param state
 * @returns {Array}
 */
export function formStatuses(state = []) {
  return state;
}

/**
 * @param state
 * @returns {Array}
 */
export function assetSources(state = []) {
  return state;
}

/**
 * @param state
 * @returns {Array}
 */
export function fileKinds(state = []) {
  return state;
}
