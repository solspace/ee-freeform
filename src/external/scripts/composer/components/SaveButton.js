/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import qwest from "qwest";
import React, { Component } from "react";
import { connect } from "react-redux";
import { updateFormId, updateProperty } from "../actions/Actions";
import { FORM } from "../constants/FieldTypes";

const initialState = {
  isSaving: false,
};

@connect(
  (state) => ({
    formId: state.formId,
    composer: state.composer,
    context: state.context,
    currentFormHandle: state.composer.properties.form.handle,
  }),
  (dispatch) => ({
    updateFormId: (formId) => dispatch(updateFormId(formId)),
    updateFormHandle: (newHandle) => dispatch(updateProperty(FORM, { handle: newHandle })),
  }),
)
export default class SaveButton extends Component {
  static propTypes = {
    saveUrl: PropTypes.string.isRequired,
    formUrl: PropTypes.string.isRequired,
    updateFormId: PropTypes.func.isRequired,
    updateFormHandle: PropTypes.func.isRequired,
    formId: PropTypes.number,
    currentFormHandle: PropTypes.string,
  };

  static contextTypes = {
    store: PropTypes.object.isRequired,
    csrf: PropTypes.shape({
      name: PropTypes.string.isRequired,
      token: PropTypes.string.isRequired,
    }).isRequired,
    notificator: PropTypes.func.isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.save = this.save.bind(this);
    this.checkForSaveShortcut = this.checkForSaveShortcut.bind(this);
    this.state = initialState;

    document.addEventListener("keydown", this.checkForSaveShortcut, false);
  }

  render() {
    const { isSaving } = this.state;

    const originalTitle = "Save ⌘S";
    const progressTitle = "Saving...";

    let currentTitle = isSaving ? progressTitle : originalTitle;

    return (
      <div className="buttons composer-save">
        <div className="btngroup submit">
          <input
            type="submit"
            value={currentTitle}
            onClick={this.save}
            className="btn submit"
          />

          <input
            type="submit"
            value="Save and close"
            onClick={this.save}
            className="btn submit formsubmit gotoFormList"
          />
        </div>
      </div>
    );
  }

  save(event) {
    const { saveUrl, formUrl, formId, composer, context } = this.props;
    const { currentFormHandle, updateFormId, updateFormHandle } = this.props;
    const { csrf, notificator } = this.context;

    let savableState = {
      [csrf.name]: csrf.token,
      formId,
      composerState: JSON.stringify({
        composer,
        context,
      }),
    };

    const shouldGotoFormList = event.target.className.match(/gotoFormList/);
    const shouldGotoNewForm = event.target.className.match(/gotoNewForm/);
    const duplicateForm = event.target.className.match(/duplicateForm/);

    if (duplicateForm) {
      savableState.formId = "";
      savableState.duplicate = true;
    }

    this.setState({ isSaving: true });

    return qwest.post(saveUrl, savableState, { responseType: "json" })
      .then((xhr, response) => {
        this.setState({ isSaving: false });

        if (!response.errors) {
          let url = formUrl.replace("{id}", response.id);
          history.pushState(response.id, "", url);

          updateFormId(response.id);
          if (currentFormHandle !== response.handle) {
            updateFormHandle(response.handle);
          }

          notificator("notice", "Saved successfully");
          if (shouldGotoFormList) {
            window.location.href = formUrl.replace("{id}", "");
          } else if (shouldGotoNewForm) {
            window.location.href = formUrl.replace("{id}", "new");
          }

          return true;
        }

        response.errors.map((message) => notificator("error", message));
      })
      .catch(exception => {
        notificator("error", exception);
        this.setState({ isSaving: false });
      });
  }

  checkForSaveShortcut(event) {
    const sKey = 83;
    const keyCode = event.which;

    if (keyCode == sKey && this.isModifierKeyPressed(event)) {
      event.preventDefault();

      this.save(event);

      return false;
    }
  }

  isModifierKeyPressed(event) {
    // metaKey maps to ⌘ on Macs
    if (window.navigator.platform.match(/Mac/)) {
      return event.metaKey;
    }

    // Both altKey and ctrlKey == true on some Windows keyboards when the right-hand ALT key is pressed
    // so just be safe and make sure altKey == false
    return (event.ctrlKey && !event.altKey);
  }
}
