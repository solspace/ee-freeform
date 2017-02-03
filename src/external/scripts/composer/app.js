/*
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
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
      fields: fieldList
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
export const notificator = (type, message) => (Craft.cp.displayNotification(type, message));
export const urlBuilder = (url) => (Craft.getCpUrl(url));

ReactDOM.render(
  <Provider store={store}>
    <ComposerApp
      saveUrl={Craft.getCpUrl('freeform/forms/save')}
      formUrl={Craft.getCpUrl('freeform/forms/{id}')}
      createFieldUrl={Craft.getCpUrl('freeform/api/quickCreateField')}
      createNotificationUrl={Craft.getCpUrl('freeform/api/quickCreateNotification')}
      createTemplateUrl={Craft.getCpUrl('freeform/settings/addDemoTemplate')}
      finishTutorialUrl={Craft.getCpUrl('freeform/api/finish-tutorial')}
      showTutorial={showTutorial}
      notificator={notificator}
      canManageFields={canManageFields}
      canManageNotifications={canManageNotifications}
      canManageSettings={canManageSettings}
      csrf={{
        name: Craft.csrfTokenName ? Craft.csrfTokenName : "csrfToken",
        token: Craft.csrfTokenValue ? Craft.csrfTokenValue : "",
      }}
    />
  </Provider>,
  rootElement
);
