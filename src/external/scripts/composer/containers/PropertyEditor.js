/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
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
import DateTime from "../components/PropertyEditor/Datetime";
import Number from "../components/PropertyEditor/Number";
import Phone from "../components/PropertyEditor/Phone";
import Regex from "../components/PropertyEditor/Regex";
import Rating from "../components/PropertyEditor/Rating";
import Website from "../components/PropertyEditor/Website";
import Confirmation from "../components/PropertyEditor/Confirmation";
import {updateProperty, switchHash} from "../actions/Actions";
import AlwaysNearbyBox from "../components/AlwaysNearbyBox";

const propertyTypes = {
  admin_notifications: AdminNotifications,
  page: Page,
  text: Text,
  textarea: Textarea,
  hidden: Hidden,
  email: Email,
  html: Html,
  submit: Submit,
  select: RadioGroup,
  checkbox: Checkbox,
  checkbox_group: CheckboxGroup,
  radio_group: RadioGroup,
  dynamic_recipients: DynamicRecipients,
  mailing_list: MailingList,
  integration: Integrations,
  file: File,
  datetime: DateTime,
  number: Number,
  phone: Phone,
  rating: Rating,
  website: Website,
  regex: Regex,
  confirmation: Confirmation,
};

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

      default:
        if (props.type && propertyTypes[props.type]) {
          let DynamicClassName = propertyTypes[props.type];

          form = <DynamicClassName />;
        }

        break;
    }

    return (
      <AlwaysNearbyBox
        stickyTop={
          <div className="composer-form-settings">
            <a onClick={editForm} className={"btn action form-settings" + (hash === FieldTypes.FORM ? " active" : "")} data-icon="settings">
              Form Settings
            </a>

            <a onClick={editAdminNotifications} className={"btn action notification-settings" + (hash === FieldTypes.ADMIN_NOTIFICATIONS ? " active" : "")} data-icon="mail">
              Notify
            </a>

            {integrationCount ?
              (
                <a onClick={editIntegrations} className={"btn action crm-settings" + (hash === FieldTypes.INTEGRATION ? " active" : "")} data-icon="settings">
                  CRM
                </a>
              )
              : ""}
          </div>
        }
      >
        <h3>{title}</h3>
        <h4 dangerouslySetInnerHTML={{__html: props.label}} />

        <hr />

        {form ? form : <p>Please select an element</p>}
      </AlwaysNearbyBox>
    );
  }

  updateField(keyValueObject) {
    const {hash, updateProperties} = this.props;

    updateProperties(hash, keyValueObject);
  }
}
