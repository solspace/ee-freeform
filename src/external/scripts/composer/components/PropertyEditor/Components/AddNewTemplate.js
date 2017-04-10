/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
import TemplateProperties from "./TemplateProperties";

export default class AddNewTemplate extends Component {
  static initialState = {
    showForm: false,
  };

  constructor(props, context) {
    super(props, context);

    this.state      = AddNewTemplate.initialState;
    this.toggleForm = this.toggleForm.bind(this);
  }

  render() {
    const {showForm} = this.state;

    const className = "composer-add-new-template-wrapper" + (showForm ? " active" : "");

    return (
      <div className={className}>
        {!showForm &&
        <button className="btn action" data-icon="add" onClick={this.toggleForm}>
          Add new template
        </button>
        }

        {showForm &&
        <TemplateProperties toggleForm={this.toggleForm} />
        }
      </div>
    );
  }

  toggleForm() {
    this.setState({
      showForm: !this.state.showForm
    });
  }
}
