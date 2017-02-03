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
import BasePropertyEditor from "./BasePropertyEditor";
import OptionTable from "./Components/OptionTable/OptionTable";
import TextProperty from "./PropertyItems/TextProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import CustomProperty from "./PropertyItems/CustomProperty";
import AddNewNotification from "./Components/AddNewNotification";
import {connect} from "react-redux";

@connect(
  (state) => ({
    hash: state.context.hash,
    properties: state.composer.properties,
    notifications: state.notifications.list,
  })
)
export default class DynamicRecipients extends BasePropertyEditor {
  static propTypes = {
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
      handle: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      required: PropTypes.bool,
      value: PropTypes.node,
      options: PropTypes.array,
      notificationId: PropTypes.number.isRequired,
      showAsRadio: PropTypes.bool,
    }).isRequired,
    canManageNotifications: PropTypes.bool.isRequired,
  };

  render() {
    const {properties: {required, label, handle, value, options, showAsRadio, notificationId, instructions}} = this.context;

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

        <CheckboxProperty
          label="Show as radio buttons?"
          name="showAsRadio"
          checked={!!showAsRadio}
          onChangeHandler={this.update}
        />

        <hr />

        <CustomProperty
          label="Options"
          instructions="Options for this checkbox group"
          content={
            <OptionTable
              value={value}
              options={options}
              labelTitle="Label"
              valueTitle="Email"
            />
          }
        />
      </div>
    );
  }
}
