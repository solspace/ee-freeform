/*
 * Freeform for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expression-engine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import qwest from "qwest";
import { urlBuilder } from "../app";
import * as ActionTypes from "../constants/ActionTypes";

const requestGeneratedOptions = () => ({
  type: ActionTypes.REQUEST_GENERATED_OPTIONS,
});

const receiveGeneratedOptions = (hash, generatedOptions) => ({
  type: ActionTypes.RECEIVE_GENERATED_OPTIONS,
  hash,
  generatedOptions,
});

export const invalidateGeneratedOptions = (hash) => ({
  type: ActionTypes.INVALIDATE_GENERATED_OPTIONS,
  hash,
});

export function fetchGeneratedOptionsIfNeeded(hash, source, target, configuration) {
  return function (dispatch, getState) {
    if (shouldFetchMailingLists(hash, getState())) {
      dispatch(requestGeneratedOptions());

      const url = urlBuilder("freeform_next/options-from-source");
      return qwest.post(
        url,
        {
          [getState().csrfToken.name]: getState().csrfToken.value,
          source,
          target,
          configuration,
        },
        { responseType: "json" }
        )
        .then((xhr, response) => {
          if (response.data) {
            dispatch(receiveGeneratedOptions(hash, response.data));

            // Trigger a window resize event, so that the UI adjusts itself
            const event = window.document.createEvent('UIEvents');
            event.initUIEvent('resize', true, false, window, 0);
            window.dispatchEvent(event);

            return true;
          }
        });
    } else {
      Promise.resolve();
    }
  };
}

const shouldFetchMailingLists = (hash, state) => {
  const generatedOptions = state.generatedOptionLists.cache;

  if (!generatedOptions || !generatedOptions[hash]) {
    return true;
  } else if (state.generatedOptionLists.isFetching) {
    return false;
  } else {
    return state.generatedOptionLists.didInvalidate;
  }
};
