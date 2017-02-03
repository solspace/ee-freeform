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
import {connect} from "react-redux";
import * as FieldTypes from "../constants/FieldTypes";
import Form from "../components/PropertyEditor/Form";
import AdminNotifications from "../components/PropertyEditor/AdminNotifications";
import Page from "../components/PropertyEditor/Page";
import Integrations from "../components/PropertyEditor/Integrations";
import Text from "../components/PropertyEditor/Text";
import Textarea from "../components/PropertyEditor/Textarea";
import Hidden from "../components/PropertyEditor/Hidden";
import Email from "../components/PropertyEditor/Email";
import Html from "../components/PropertyEditor/Html";
import Submit from "../components/PropertyEditor/Submit";
import Checkbox from "../components/PropertyEditor/Checkbox";
import CheckboxGroup from "../components/PropertyEditor/CheckboxGroup";
import RadioGroup from "../components/PropertyEditor/RadioGroup";
import DynamicRecipients from "../components/PropertyEditor/DynamicRecipients";
import MailingList from "../components/PropertyEditor/MailingList";
import File from "../components/PropertyEditor/File";
import {updateProperty, switchHash} from "../actions/Actions";
import {titleize} from "underscore.string";

@connect(
  (state) => ({
    properties: state.composer.properties,
    formStatuses: state.formStatuses,
    hash: state.context.hash,
    integrationCount: state.integrations.list.length,
  }),
  (dispatch) => ({
    updateProperties: (hash, keyValueObject) => dispatch(updateProperty(hash, keyValueObject)),
    editForm: () => dispatch(switchHash(FieldTypes.FORM)),
    editAdminNotifications: () => dispatch(switchHash(FieldTypes.ADMIN_NOTIFICATIONS)),
    editIntegrations: () => dispatch(switchHash(FieldTypes.INTEGRATION)),
  })
)
export default class PropertyEditor extends Component {
  static propTypes = {
    properties: PropTypes.object.isRequired,
    hash: PropTypes.string.isRequired,
    updateProperties: PropTypes.func.isRequired,
    editForm: PropTypes.func.isRequired,
    editAdminNotifications: PropTypes.func.isRequired,
    editIntegrations: PropTypes.func.isRequired,
    integrationCount: PropTypes.number.isRequired,
  };

  static childContextTypes = {
    hash: PropTypes.string.isRequired,
    properties: PropTypes.object,
    updateField: PropTypes.func.isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.updateField = this.updateField.bind(this);
  }

  getChildContext = () => ({
    hash: this.props.hash,
    properties: this.props.properties[this.props.hash],
    updateField: this.updateField,
  });

  render() {
    const {hash, properties, formStatuses, editForm, editAdminNotifications, editIntegrations, integrationCount} = this.props;

    let title = "Field Property Editor";

    const props = (properties[hash] && properties[hash].type) ? properties[hash] : {type: null};

    let form = null;
    switch (props.type) {
      case FieldTypes.FORM:
        title = "Form Settings";
        form  = <Form formStatuses={formStatuses} />;
        break;

      case FieldTypes.ADMIN_NOTIFICATIONS:
        title = "Admin Notifications";
        form  = <AdminNotifications />;
        break;

      case FieldTypes.PAGE:
        title = "Page Property Editor";
        form  = <Page />;
        break;

      case FieldTypes.INTEGRATION:
        title = "CRM Integration Settings";
        form  = <Integrations />;
        break;

      case FieldTypes.HIDDEN:
        form = <Hidden />;
        break;

      case FieldTypes.TEXT:
        form = <Text />;
        break;

      case FieldTypes.TEXTAREA:
        form = <Textarea />;
        break;

      case FieldTypes.EMAIL:
        form = <Email />;
        break;

      case FieldTypes.CHECKBOX:
        form = <Checkbox />;
        break;

      case FieldTypes.CHECKBOX_GROUP:
        form = <CheckboxGroup />;
        break;

      case FieldTypes.RADIO_GROUP:
      case FieldTypes.SELECT:
        form = <RadioGroup />;
        break;

      case FieldTypes.HTML:
        form = <Html />;
        break;

      case FieldTypes.SUBMIT:
        form = <Submit />;
        break;

      case FieldTypes.DYNAMIC_RECIPIENTS:
        form = <DynamicRecipients />;
        break;

      case FieldTypes.MAILING_LIST:
        form = <MailingList />;
        break;

      case FieldTypes.FILE:
        form = <File />;
        break;
    }

    return (
      <div>
        <div className="composer-form-settings">
          <a onClick={editForm} className={"btn small form-settings" + (hash === FieldTypes.FORM ? " active" : "")} data-icon="settings">
            Form Settings
          </a>

          <a onClick={editAdminNotifications} className={"btn small notification-settings" + (hash === FieldTypes.ADMIN_NOTIFICATIONS ? " active" : "")} data-icon="mail">
            Notify
          </a>

          {integrationCount ?
            (
              <a onClick={editIntegrations} className={"btn small crm-settings" + (hash === FieldTypes.INTEGRATION ? " active" : "")} data-icon="settings">
                CRM
              </a>
            )
            : ""}
        </div>

        <h3>{title}</h3>
        <h4>{props.label}</h4>

        <hr />

        {form ? form : <p>Please select an element</p>}
      </div>
    );
  }

  updateField(keyValueObject) {
    const {hash, updateProperties} = this.props;

    updateProperties(hash, keyValueObject);
  }
}
