/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import React, { Component } from "react";
import PropTypes from "prop-types";
import NotificationProperties from "./NotificationProperties";

export default class AddNewNotification extends Component {
  static propTypes = { buttonLabel: PropTypes.string };
  static initialState = {
    showForm: false,
  };

  constructor(props, context) {
    super(props, context);

    this.state = AddNewNotification.initialState;
    this.toggleForm = this.toggleForm.bind(this);
  }

  render() {
    const { showForm } = this.state;

    const className = "composer-add-new-notification-wrapper" + (showForm ? " active" : "");

    return (
      <div className={className}>
        {!showForm &&
        <button type="button" className="button button--default button--small" onClick={this.toggleForm} aria-label="Create new email template">
          {this.props.buttonLabel || "Add New Template"}
        </button>
        }

        {showForm &&
        <NotificationProperties toggleForm={this.toggleForm} />
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
