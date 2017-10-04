/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React from "react";
import {compose, createStore, applyMiddleware} from "redux";
import ReactDOM from "react-dom";
import {Provider} from "react-redux";
import ComposerApp from "./containers/ComposerApp";
import thunkMiddleware from "redux-thunk";
import composerReducers from "./reducers/index";
import * as FieldTypes from "./constants/FieldTypes";
import "babel-polyfill";
import {showNotification} from "./helpers/Utilities";

const enhancer = compose(
  applyMiddleware(thunkMiddleware),
  window.devToolsExtension ? window.devToolsExtension() : f => f
);

//noinspection JSUnresolvedVariable
let store = createStore(
  composerReducers, {
    formId: formId,
    fields: {
      isFetching: false,
      didInvalidate: false,
      fields: fieldList,
      types: fieldTypeList,
    },
    specialFields: [
      {type: FieldTypes.HTML, label: "HTML", value: "<div>Html content</div>"},
      {
        type: FieldTypes.SUBMIT,
        label: "Submit",
        labelNext: "Submit",
        labelPrev: "Previous",
        disablePrev: false,
        position: "left",
      },
    ],
    mailingLists: {
      isFetching: false,
      didInvalidate: false,
      list: mailingList,
    },
    integrations: {
      isFetching: false,
      didInvalidate: false,
      list: crmIntegrations,
    },
    notifications: {
      isFetching: false,
      didInvalidate: true,
      list: notificationList,
    },
    templates: {
      isFetching: false,
      didInvalidate: false,
      solspaceTemplates: solspaceFormTemplates,
      list: formTemplateList,
    },
    formStatuses: formStatuses,
    assetSources: assetSources,
    fileKinds: fileKinds,
    ...composerState,
  },
  enhancer
);

const rootElement = document.getElementById("freeform-builder");
export const notificator = (type, message) => (showNotification(message, type));
export const urlBuilder = (url) => {
  const index = baseUrl.indexOf('&');
  if (index === -1 || index === false) {
    return baseUrl + '/' + url;
  }

  return baseUrl.substring(0, index) + '/' + url + baseUrl.substring(index, baseUrl.length);
};

ReactDOM.render(
  <Provider store={store}>
    <ComposerApp
      saveUrl={saveUrl}
      formUrl={formUrl}
      createFieldUrl={createFieldUrl}
      createNotificationUrl={createNotificationUrl}
      createTemplateUrl={createTemplateUrl}
      finishTutorialUrl={finishTutorialUrl}
      showTutorial={showTutorial}
      defaultTemplates={defaultTemplates}
      notificator={notificator}
      canManageFields={canManageFields}
      canManageNotifications={canManageNotifications}
      canManageSettings={canManageSettings}
      isDbEmailTemplateStorage={isDbEmailTemplateStorage}
      isWidgetsInstalled={isWidgetsInstalled}
      csrf={{
        name: "csrf_token",
        token: csrfToken,
      }}
    />
  </Provider>,
  rootElement
);
