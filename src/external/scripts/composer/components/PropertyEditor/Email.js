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
import TextProperty from "./PropertyItems/TextProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import AddNewNotification from "./Components/AddNewNotification";
import {connect} from "react-redux";

@connect(
  (state) => ({
    hash: state.context.hash,
    globalProperties: state.composer.properties,
    notifications: state.notifications.list,
  })
)
export default class Email extends BasePropertyEditor {
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
      id: PropTypes.number.isRequired,
      type: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      handle: PropTypes.string.isRequired,
      value: PropTypes.string,
      placeholder: PropTypes.string,
      required: PropTypes.bool,
      notificationId: PropTypes.number.isRequired,
    }).isRequired,
    canManageNotifications: PropTypes.bool.isRequired,
  };

  render() {
    const {properties: {label, handle, placeholder, required, notificationId, instructions}} = this.context;

    const {canManageNotifications} = this.context;

    const {notifications} = this.props;
    const notificationList = [];
    notifications.map((notification) => {
      notificationList.push({
        key: notification.id,
        value: notification.name,
      });
    });

    return (
      <div>
        <TextProperty
          label="Handle"
          instructions="How you’ll refer to this field in the templates."
          name="handle"
          value={handle}
          onChangeHandler={this.updateHandle}
        />

        <hr />

        <CheckboxProperty
          label="This field is required?"
          name="required"
          checked={required}
          onChangeHandler={this.update}
        />

        <hr />

        <SelectProperty
          label="Email Template"
          instructions="The notification template used to send an email to the email value entered into this field (optional). Leave empty to just store the email address without sending anything."
          name="notificationId"
          value={notificationId}
          isNumeric={true}
          onChangeHandler={this.update}
          emptyOption="Select a template..."
          options={notificationList}
        >
          {canManageNotifications && <AddNewNotification />}
        </SelectProperty>

        <TextProperty
          label="Label"
          instructions="Field label used to describe the field."
          name="label"
          value={label}
          onChangeHandler={this.update}
        />

        <TextareaProperty
          label="Instructions"
          instructions="Field specific user instructions."
          name="instructions"
          value={instructions}
          onChangeHandler={this.update}
        />

        <TextProperty
          label="Placeholder"
          instructions="The text that will be shown if the field doesn’t have a value."
          name="placeholder"
          value={placeholder}
          onChangeHandler={this.update}
        />
      </div>
    );
  }
}
