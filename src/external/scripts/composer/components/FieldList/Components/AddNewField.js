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
import React, { Component } from "react";
import FieldProperties from "./FieldProperties";

export default class AddNewField extends Component {
  static propTypes = {
    canCreate: PropTypes.bool.isRequired,
  };

  static EVENT_AFTER_UPDATE = "freeform_add_new_field_after_render";

  static initialState = {
    showFieldForm: false,
  };

  constructor(props, context) {
    super(props, context);

    this.state = AddNewField.initialState;
    this.toggleFieldForm = this.toggleFieldForm.bind(this);
  }

  componentDidUpdate() {
    window.dispatchEvent(new Event(AddNewField.EVENT_AFTER_UPDATE));
  }

  render() {
    const { showFieldForm } = this.state;

    return (
      <div>
        <div className="composer-palette-heading sidebar__section-title">
          <h3>Fields</h3>
          {this.props.canCreate &&
          <button
            type="button"
            className="button button--primary button--small composer-new-field-button"
            aria-label="Add New Field"
            aria-expanded={showFieldForm}
            aria-controls="freeform-new-field-panel"
            ref={button => { this.newFieldButton = button; }}
            onClick={this.toggleFieldForm}
          >
            New
          </button>
          }
        </div>
        {this.props.canCreate && showFieldForm &&
        <div id="freeform-new-field-panel" className="composer-add-new-field-wrapper active">
          <FieldProperties toggleFieldForm={this.toggleFieldForm} />
        </div>
        }
      </div>
    );
  }

  toggleFieldForm() {
    this.setState(state => ({
      showFieldForm: !state.showFieldForm,
    }), () => {
      if (!this.state.showFieldForm && this.newFieldButton) {
        this.newFieldButton.focus();
      }
    });
  }
}
