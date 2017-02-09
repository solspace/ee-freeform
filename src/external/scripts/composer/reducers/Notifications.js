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
export function notifications(state = initialState, action) {
  switch (action.type) {
    case ActionTypes.REQUEST_NOTIFICATIONS:
      return state;

    case ActionTypes.RECEIVE_NOTIFICATIONS:
      return {
        ...state,
        list: action.notificationData
      };

    case ActionTypes.INVALIDATE_NOTIFICATIONS:
      return {
        ...state,
        didInvalidate: true,
      };

    default:
      return state;
  }
}
