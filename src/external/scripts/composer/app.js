/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import "babel-polyfill";
import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
import { applyMiddleware, compose, createStore } from "redux";
import thunkMiddleware from "redux-thunk";
import * as FieldTypes from "./constants/FieldTypes";
import ComposerApp from "./containers/ComposerApp";
import { showNotification } from "./helpers/Utilities";
import composerReducers from "./reducers/index";

const enhancer = compose(
  applyMiddleware(thunkMiddleware),
  window.devToolsExtension ? window.devToolsExtension() : f => f
);

const specialFields = [
  { type: FieldTypes.HTML, label: "HTML", value: "<div>Html content</div>" },
  {
    type: FieldTypes.SUBMIT,
    label: "Submit",
    labelNext: "Submit",
    labelPrev: "Previous",
    disablePrev: false,
    position: "left",
  },
  {
    type: FieldTypes.CONFIRMATION,
    label: "Confirm",
    handle: "confirm",
    placeholder: "",
  },
  {
    type: FieldTypes.PASSWORD,
    label: "Password",
    handle: "password",
    placeholder: "",
  },
];

if (isRecaptchaEnabled) {
  specialFields.push({ type: FieldTypes.RECAPTCHA, label: "reCAPTCHA" });
}

let store = createStore(
  composerReducers, {
    csrfToken: {
      name: "csrf_token",
      value: csrfToken,
    },
    formId,
    fields: {
      isFetching: false,
      didInvalidate: false,
      fields: fieldList,
      types: fieldTypeList,
    },
    specialFields,
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
    sourceTargets,
    generatedOptionLists: {
      isFetching: false,
      didInvalidate: false,
      cache: generatedOptions,
    },
    formStatuses,
    assetSources,
    fileKinds,
    channelFields,
    categoryFields,
    memberFields,
    ...composerState,
  },
  enhancer
);

const rootElement = document.getElementById("freeform-builder");
export const notificator = (type, message) => (showNotification(message, type));
export const urlBuilder = (url) => {
  const index = baseUrl.indexOf("&");
  if (index === -1 || index === false) {
    return baseUrl + "/" + url;
  }

  return baseUrl.substring(0, index) + "/" + url + baseUrl.substring(index, baseUrl.length);
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
      formPropCleanup={formPropCleanup}
      csrf={{
        name: "csrf_token",
        token: csrfToken,
      }}
    />
  </Provider>,
  rootElement
);
