/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

import * as ActionTypes from "../constants/ActionTypes";

const initialState = {
  isFetching: false,
  didInvalidate: false,
  list: [],
};

/**
 * CRM integrations data update
 *
 * @param state
 * @param action
 * @returns {*}
 */
export function integrations(state = initialState, action) {
  switch (action.type) {
    case ActionTypes.REQUEST_CRM_INTEGRATIONS:
      return {
        ...state,
        isFetching: true,
      };

    case ActionTypes.RECEIVE_CRM_INTEGRATIONS:
      return {
        ...state,
        list: action.crmData,
        isFetching: false,
        didInvalidate: false,
      };

    case ActionTypes.INVALIDATE_CRM_INTEGRATIONS:
      return {
        ...state,
        didInvalidate: true,
      };

    case ActionTypes.END_REQUEST_CRM_INTEGRATIONS:
      return {
        ...state,
        didInvalidate: false,
        isFetching: false,
      };

    default:
      return state;
  }
}
