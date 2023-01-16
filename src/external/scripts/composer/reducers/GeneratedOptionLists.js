/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import * as ActionTypes from "../constants/ActionTypes";

const initialState = {
  isFetching: false,
  didInvalidate: false,
  cache: {},
};

/**
 * Deals with AJAX state changes
 *
 * @param state
 * @param action
 *
 * @returns {object}
 */
export function generatedOptionLists(state = initialState, action) {
  switch (action.type) {
    case ActionTypes.REQUEST_GENERATED_OPTIONS:
      return {
        ...state,
        isFetching: true,
      };

    case ActionTypes.RECEIVE_GENERATED_OPTIONS:
      return {
        ...state,
        cache: {
          ...state.cache,
          [action.hash]: action.generatedOptions,
        },
        isFetching: false,
        didInvalidate: false,
      };

    case ActionTypes.INVALIDATE_GENERATED_OPTIONS:
      const clonedState = {
        ...state,
        didInvalidate: true,
      };

      delete clonedState.cache[action.hash];

      return clonedState;

    default:
      return state;
  }
}
