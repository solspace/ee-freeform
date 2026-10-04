/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import React from "react";
import ReactDOM from "react-dom";
import { DragDropContextProvider } from "react-dnd";
import HTML5Backend from "react-dnd-html5-backend";
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
  {
    type: FieldTypes.SUBMIT,
    label: "Submit",
    labelNext: "Submit",
    labelPrev: "Previous",
    disablePrev: false,
    position: "left",
  },
  {
    type: FieldTypes.HTML,
    label: "HTML",
    value: "<div>Html content</div>",
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

if (captchaProvider !== "none" && !(captchaProvider === "recaptcha" && isRecaptchaV3)) {
  const providerLabels = { recaptcha: "reCAPTCHA", turnstile: "Turnstile", hcaptcha: "hCaptcha" };
  specialFields.push({ type: FieldTypes.RECAPTCHA, label: providerLabels[captchaProvider] || "CAPTCHA" });
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

if (!window.__FF_COMPOSER_MOUNTED__) {
    window.__FF_COMPOSER_MOUNTED__ = true;

    ReactDOM.render(
      <Provider store={store}>
        <DragDropContextProvider backend={HTML5Backend}>
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
        </DragDropContextProvider>
      </Provider>,
      rootElement
    );
}
