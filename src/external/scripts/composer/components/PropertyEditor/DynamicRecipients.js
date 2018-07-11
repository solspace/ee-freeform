import PropTypes          from "prop-types";
/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */
import React              from "react";
import { connect }        from "react-redux";
import PropertyHelper     from "../../helpers/PropertyHelper";
import BasePropertyEditor from "./BasePropertyEditor";
import AddNewNotification from "./Components/AddNewNotification";
import OptionTable        from "./Components/OptionTable/OptionTable";
import CheckboxProperty   from "./PropertyItems/CheckboxProperty";
import CustomProperty     from "./PropertyItems/CustomProperty";
import SelectProperty     from "./PropertyItems/SelectProperty";
import TextareaProperty   from "./PropertyItems/TextareaProperty";
import TextProperty       from "./PropertyItems/TextProperty";
import RadioProperty       from "./PropertyItems/RadioProperty";

@connect(
  (state) => ({
    hash: state.context.hash,
    properties: state.composer.properties,
    notifications: state.notifications.list,
  }),
)
export default class DynamicRecipients extends BasePropertyEditor {
  static propTypes = {
    notifications: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
    ]).isRequired,
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
      notificationId: PropTypes.oneOfType([
        PropTypes.string,
        PropTypes.number,
      ]),
      showAsRadio: PropTypes.bool,
    }).isRequired,
    canManageNotifications: PropTypes.bool.isRequired,
  };

  render() {
    const {properties: {required, label, handle, value, options, showAsRadio, notificationId, instructions}} = this.context;

    const {canManageNotifications} = this.context;
    const {notifications}          = this.props;

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
          couldBeNumeric={true}
          onChangeHandler={this.update}
          emptyOption="Select a template..."
          optionGroups={PropertyHelper.getNotificationList(notifications)}
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
