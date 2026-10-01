/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
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

class SaveButton extends Component {
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

  }

  componentDidMount() {
    document.addEventListener("keydown", this.checkForSaveShortcut, false);
  }

  componentWillUnmount() {
    document.removeEventListener("keydown", this.checkForSaveShortcut, false);
  }

  render() {
    const { isSaving } = this.state;

    const originalTitle = "Save ⌘S";
    const progressTitle = "Saving...";

    let currentTitle = isSaving ? progressTitle : originalTitle;

    return (
        <div className="button-group buttons composer-save" aria-busy={isSaving}>
          <button className="button button--primary" type="button" disabled={isSaving} value={currentTitle} data-work-text="Saving..." onClick={this.save}>{currentTitle}</button>
          <button type="button" className="button button--primary dropdown-toggle js-dropdown-toggle saving-options"
                  disabled={isSaving} aria-label="More save options" data-dropdown-pos="bottom-end">
            <i className="fas fa-angle-down" aria-hidden="true"></i>
          </button>
          <div className="dropdown">
            <div className="dropdown__scroll">
              <button className="button button__within-dropdown gotoFormList" type="button" disabled={isSaving} data-submit-text="Save &amp; Close" data-work-text="Saving..." onClick={this.save}>Save &amp; Close
              </button>
            </div>
          </div>
        </div>
    );
  }

  save(event) {
    event.preventDefault();
    if (this.state.isSaving) {
      return;
    }

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

    const classList = event.type === "click" ? event.currentTarget.classList : null;
    const shouldGotoFormList = classList && classList.contains("gotoFormList");
    const shouldGotoNewForm = classList && classList.contains("gotoNewForm");
    const duplicateForm = classList && classList.contains("duplicateForm");

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

export default connect(
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
)(SaveButton);
