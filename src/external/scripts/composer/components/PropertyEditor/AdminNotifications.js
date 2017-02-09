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
import BasePropertyEditor from "./BasePropertyEditor";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import AddNewNotification from "./Components/AddNewNotification";
import {connect} from "react-redux";

@connect(
  (state) => ({
    hash: state.context.hash,
    globalProperties: state.composer.properties,
    notifications: state.notifications.list,
  })
)
export default class AdminNotifications extends BasePropertyEditor {
  static propTypes = {
    globalProperties: PropTypes.object.isRequired,
    notifications: PropTypes.arrayOf(
      PropTypes.shape({
        id: PropTypes.number.isRequired,
        name: PropTypes.string.isRequired,
        handle: PropTypes.string.isRequired,
        description: PropTypes.string,
      })
    ).isRequired,
  };

  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
      type: PropTypes.string.isRequired,
      notificationId: PropTypes.number.isRequired,
      recipients: PropTypes.string.isRequired,
    }).isRequired,
    canManageNotifications: PropTypes.bool.isRequired,
  };

  render() {
    const {properties: {notificationId, recipients}} = this.context;

    const {canManageNotifications} = this.context;

    const {notifications}  = this.props;
    const notificationList = [];
    notifications.map((notification) => {
      notificationList.push({
        key: notification.id,
        value: notification.name,
      });
    });

    return (
      <div>
        <SelectProperty
          label="Email Template"
          instructions="The notification template used to send an email to the email value entered into this field (optional)."
          name="notificationId"
          value={notificationId}
          isNumeric={true}
          onChangeHandler={this.update}
          emptyOption="Select a template..."
          options={notificationList}
        >
          {canManageNotifications && <AddNewNotification />}
        </SelectProperty>

        {notificationId ? (
            <TextareaProperty
              label="Admin Recipients"
              instructions="Email address(es) to receive an email notification. Enter each on a new line."
              name="recipients"
              rows={10}
              value={recipients}
              onChangeHandler={this.update}
            />
          ) : ""
        }
      </div>
    );
  }
}
