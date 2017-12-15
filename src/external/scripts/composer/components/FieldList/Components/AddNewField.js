/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
import FieldProperties from "./FieldProperties";
import {fetchFields} from "../../../actions/Actions"

export default class AddNewField extends Component {
  static EVENT_AFTER_UPDATE = 'freeform_add_new_field_after_render';

  static initialState = {
    showFieldForm: false,
  };

    constructor(props, context) {
        super(props, context);

        this.state           = AddNewField.initialState;
        this.toggleFieldForm = this.toggleFieldForm.bind(this);
    }

  componentDidUpdate() {
    window.dispatchEvent(new Event(AddNewField.EVENT_AFTER_UPDATE));
  }

  render() {
    const {showFieldForm} = this.state;

        const className = "composer-add-new-field-wrapper" + (showFieldForm ? " active" : "");

        return (
            <div className={className}>
                {!showFieldForm &&
                <button className="btn action" data-icon="add" onClick={this.toggleFieldForm}>
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
            showFieldForm: !this.state.showFieldForm
        });
    }
}
