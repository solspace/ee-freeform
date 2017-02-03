/*
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
import FieldProperties from "./FieldProperties";
import {fetchFields} from "../../../actions/Actions"

export default class AddNewField extends Component {
    static initialState = {
        showFieldForm: false,
    };

    constructor(props, context) {
        super(props, context);

        this.state           = AddNewField.initialState;
        this.toggleFieldForm = this.toggleFieldForm.bind(this);
    }

    render() {
        const {showFieldForm} = this.state;

        const className = "composer-add-new-field-wrapper" + (showFieldForm ? " active" : "");

        return (
            <div className={className}>
                {!showFieldForm &&
                <button className="btn add icon" onClick={this.toggleFieldForm}>
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
