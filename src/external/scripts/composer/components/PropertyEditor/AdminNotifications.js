/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import PropertyHelper from "../../helpers/PropertyHelper";
import BasePropertyEditor from "./BasePropertyEditor";
import AddNewNotification from "./Components/AddNewNotification";
import SelectProperty from "./PropertyItems/SelectProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";

class AdminNotifications extends BasePropertyEditor {
  static propTypes = {
    globalProperties: PropTypes.object.isRequired,
    notifications: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
    ]).isRequired,
  };

  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
      format: PropTypes.string.isRequired,
      type: PropTypes.string.isRequired,
      notificationId: PropTypes.oneOfType([
        PropTypes.string,
        PropTypes.number,
      ]),
      recipients: PropTypes.string.isRequired,
    }).isRequired,
    canManageNotifications: PropTypes.bool.isRequired,
  };

  render() {
    const { properties: { format, notificationId, recipients } } = this.context;

    const { canManageNotifications } = this.context;

    const { notifications } = this.props;

    const formatList = [{
      key: 'html',
      value: 'HTML',
    }, {
      key: 'text',
      value: 'Plain Text',
    }];

    return (
      <div>
        <SelectProperty
          label="Email Template"
          instructions="The notification template used to send an email to the email value entered into this field (optional)."
          name="notificationId"
          value={notificationId}
          couldBeNumeric={true}
          onChangeHandler={this.update}
          emptyOption="--"
          optionGroups={PropertyHelper.getNotificationList(notifications)}
          inlineAction={canManageNotifications}
        >
          {canManageNotifications && <AddNewNotification buttonLabel="New" />}
        </SelectProperty>

        {notificationId ? (
          <TextareaProperty
            label="Admin Recipients"
            instructions="Email address(es) to receive an email notification. Enter each on a new line."
            name="recipients"
            rows={5}
            value={recipients}
            onChangeHandler={this.update}
          />
        ) : ""
        }

        {notificationId ? (
          <SelectProperty
            label="Format"
            instructions="Choose the format in which the email notification will be sent."
            name="format"
            value={format}
            onChangeHandler={this.update}
            isNumeric={false}
            options={formatList}
          />
        ) : ""
        }
      </div>
    );
  }
}

export default connect(
  (state) => ({
    hash: state.context.hash,
    globalProperties: state.composer.properties,
    notifications: state.notifications.list,
  }),
)(AdminNotifications);
