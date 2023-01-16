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
export function mailingLists(state = initialState, action) {
  switch (action.type) {
    case ActionTypes.REQUEST_MAILING_LISTS:
      return {
        ...state,
        isFetching: true,
      };

    case ActionTypes.RECEIVE_MAILING_LISTS:
      return {
        ...state,
        list: action.sourceTargetData,
        isFetching: false,
        didInvalidate: false,
      };

    case ActionTypes.INVALIDATE_MAILING_LISTS:
      return {
        ...state,
        didInvalidate: true,
      };

    default:
      return state;
  }
}
