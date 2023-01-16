/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import React, { Component } from "react";
import { fetchFields } from "../../../actions/Actions";
import FieldProperties from "./FieldProperties";

export default class AddNewField extends Component {
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

    const className = "composer-add-new-field-wrapper" + (showFieldForm ? " active" : "");

    return (
      <div className={className}>
        {!showFieldForm &&
        <button className="button button--default button--small" onClick={this.toggleFieldForm}>
          Add New Field
        </button>
        }

        {showFieldForm &&
        <FieldProperties toggleFieldForm={this.toggleFieldForm} />
        }
      </div>
    );
  }

  toggleFieldForm() {
    this.setState({
      showFieldForm: !this.state.showFieldForm,
    });
  }
}
