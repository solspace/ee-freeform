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

const requestMailingLists = () => ({
  type: ActionTypes.REQUEST_MAILING_LISTS,
});

const receiveMailingLists = (mailingListData) => ({
  type: ActionTypes.RECEIVE_MAILING_LISTS,
  mailingListData,
});

export const invalidateMailingLists = () => ({
  type: ActionTypes.INVALIDATE_MAILING_LISTS,
});

export function fetchMailingListsIfNeeded() {
  return function(dispatch, getState) {
    if (shouldFetchMailingLists(getState())) {
      dispatch(requestMailingLists());

      const url = urlBuilder("freeform/api/mailing-lists");
      return fetch(url, {credentials: 'same-origin'})
        .then(response => response.json())
        .then(json => dispatch(receiveMailingLists(json)));
    } else {
      Promise.resolve();
    }
  }
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
