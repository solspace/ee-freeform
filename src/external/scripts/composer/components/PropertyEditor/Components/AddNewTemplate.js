/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import React, { Component } from "react";
import TemplateProperties from "./TemplateProperties";

export default class AddNewTemplate extends Component {
  static initialState = {
    showForm: false,
  };

  constructor(props, context) {
    super(props, context);

    this.state = AddNewTemplate.initialState;
    this.toggleForm = this.toggleForm.bind(this);
  }

  render() {
    const { showForm } = this.state;

    const className = "composer-add-new-template-wrapper" + (showForm ? " active" : "");

    return (
      <div className={className}>
        {!showForm &&
        <button className="button button--default button--small" data-icon="add" onClick={this.toggleForm}>
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
      showForm: !this.state.showForm,
    });
  }
}
