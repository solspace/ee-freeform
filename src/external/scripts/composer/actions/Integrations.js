/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import fetch from "isomorphic-fetch";
import { notificator, urlBuilder } from "../app";
import * as ActionTypes from "../constants/ActionTypes";

const requestCrmIntegrations = () => ({
  type: ActionTypes.REQUEST_CRM_INTEGRATIONS,
});

const receiveCrmIntegrations = (crmData) => ({
  type: ActionTypes.RECEIVE_CRM_INTEGRATIONS,
  crmData,
});

export const invalidateCrmIntegrations = () => ({
  type: ActionTypes.INVALIDATE_CRM_INTEGRATIONS,
});

const endRequestCrmIntegrations = () => ({
  type: ActionTypes.END_REQUEST_CRM_INTEGRATIONS,
});

export function fetchCrmIntegrationsIfNeeded() {
  return function (dispatch, getState) {
    if (shouldFetchCrmIntegrations(getState())) {
      dispatch(requestCrmIntegrations());

      const url = urlBuilder("freeform_next/integrations/crm/get");
      return fetch(url, { credentials: "same-origin" })
        .then(response => response.json())
        .then(json => {
          if (json.errors || json.error) {
            let error = "";
            if (json.errors) {
              error = json.errors.join(", ");
            } else {
              error = json.error;
            }
            notificator("error", error);
            dispatch(endRequestCrmIntegrations());
          } else {
            dispatch(receiveCrmIntegrations(json));
          }
        });
    } else {
      dispatch(endRequestCrmIntegrations());
      Promise.resolve();
    }
  };
}

const shouldFetchCrmIntegrations = (state) => {
  const integrations = state.integrations.list;

  if (!integrations) {
    return true;
  } else if (state.integrations.isFetching) {
    return false;
  } else {
    return state.integrations.didInvalidate;
  }
};
