/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import fetch from "isomorphic-fetch";
import { urlBuilder } from "../app";
import * as ActionTypes from "../constants/ActionTypes";

const requestMailingLists = () => ({
  type: ActionTypes.REQUEST_MAILING_LISTS,
});

const receiveMailingLists = (mailingListData) => ({
  type: ActionTypes.RECEIVE_MAILING_LISTS,
  sourceTargetData: mailingListData,
});

export const invalidateMailingLists = () => ({
  type: ActionTypes.INVALIDATE_MAILING_LISTS,
});

export function fetchMailingListsIfNeeded() {
  return function (dispatch, getState) {
    if (shouldFetchMailingLists(getState())) {
      dispatch(requestMailingLists());

      const url = urlBuilder("freeform_next/integrations/mailing_lists/get");
      return fetch(url, { credentials: "same-origin" })
        .then(response => response.json())
        .then(json => dispatch(receiveMailingLists(json)));
    } else {
      Promise.resolve();
    }
  };
}

const shouldFetchMailingLists = (state) => {
  const mailingLists = state.mailingLists.list;

  if (!mailingLists) {
    return true;
  } else if (state.mailingLists.isFetching) {
    return false;
  } else {
    return state.mailingLists.didInvalidate;
  }
};
