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
import NotificationProperties from "./NotificationProperties";

export default class AddNewNotification extends Component {
  static initialState = {
    showForm: false,
  };

  constructor(props, context) {
    super(props, context);

    this.state      = AddNewNotification.initialState;
    this.toggleForm = this.toggleForm.bind(this);
  }

  render() {
    const {showForm} = this.state;

    const className = "composer-add-new-notification-wrapper" + (showForm ? " active" : "");

    return (
      <div className={className}>
        {!showForm &&
        <button className="btn add icon" onClick={this.toggleForm}>
          Add new template
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
      showForm: !this.state.showForm
    });
  }
}
