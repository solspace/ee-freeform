/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

import PropTypes from "prop-types";
import React, { Component } from "react";
import { connect } from "react-redux";
import { resetProperties, switchHash, updateProperty, } from "../actions/Actions";
import AlwaysNearbyBox from "../components/AlwaysNearbyBox";
import AdminNotifications from "../components/PropertyEditor/AdminNotifications";
import Checkbox from "../components/PropertyEditor/Checkbox";
import CheckboxGroup from "../components/PropertyEditor/CheckboxGroup";
import FormSettings from "../components/PropertyEditor/Components/FormSettings";
import Confirmation from "../components/PropertyEditor/Confirmation";
import DateTime from "../components/PropertyEditor/Datetime";
import DynamicRecipients from "../components/PropertyEditor/DynamicRecipients";
import Email from "../components/PropertyEditor/Email";
import File from "../components/PropertyEditor/File";
import Form from "../components/PropertyEditor/Form";
import Hidden from "../components/PropertyEditor/Hidden";
import Html from "../components/PropertyEditor/Html";
import Integrations from "../components/PropertyEditor/Integrations";
import MailingList from "../components/PropertyEditor/MailingList";
import Number from "../components/PropertyEditor/Number";
import Page from "../components/PropertyEditor/Page";
import Password from "../components/PropertyEditor/Password";
import Phone from "../components/PropertyEditor/Phone";
import RadioGroup from "../components/PropertyEditor/RadioGroup";
import Rating from "../components/PropertyEditor/Rating";
import Recaptcha from "../components/PropertyEditor/Recaptcha";
import Regex from "../components/PropertyEditor/Regex";
import Select from "../components/PropertyEditor/Select";
import Submit from "../components/PropertyEditor/Submit";
import Table from "../components/PropertyEditor/Table";
import Text from "../components/PropertyEditor/Text";
import Textarea from "../components/PropertyEditor/Textarea";
import Website from "../components/PropertyEditor/Website";
import * as FieldTypes from "../constants/FieldTypes";

const propertyTypes = {
  admin_notifications: AdminNotifications,
  page: Page,
  text: Text,
  textarea: Textarea,
  hidden: Hidden,
  email: Email,
  html: Html,
  submit: Submit,
  select: Select,
  multiple_select: CheckboxGroup,
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
  recaptcha: Recaptcha,
  password: Password,
  table: Table,
};

@connect(
  (state) => ({
    properties: state.composer.properties,
    formStatuses: state.formStatuses,
    hash: state.context.hash,
    integrationCount: state.integrations.list.length,
    fields: state.fields.fields,
  }),
  (dispatch) => ({
    updateProperties: (hash, keyValueObject) => dispatch(updateProperty(hash, keyValueObject)),
    resetProperties: (hash, defaultProperties) => dispatch(resetProperties(hash, defaultProperties)),
    editForm: () => dispatch(switchHash(FieldTypes.FORM)),
    editAdminNotifications: () => dispatch(switchHash(FieldTypes.ADMIN_NOTIFICATIONS)),
    editIntegrations: () => dispatch(switchHash(FieldTypes.INTEGRATION)),
  }),
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
    const { hash, properties, formStatuses, editForm, editAdminNotifications, editIntegrations, integrationCount } = this.props;

    let title = "Field Property Editor";

    const props = (properties[hash] && properties[hash].type) ? properties[hash] : { type: null };

    let form = null;
    switch (props.type) {
      case FieldTypes.FORM:
        title = "Form Settings";
        form = <Form formStatuses={formStatuses} />;
        break;

      default:
        if (props.type === FieldTypes.INTEGRATION) {
          title = "CRM Integrations";
        } else if (props.type === FieldTypes.ADMIN_NOTIFICATIONS) {
          title = "Admin Notifications";
        }

        if (props.type && propertyTypes[props.type]) {
          let DynamicClassName = propertyTypes[props.type];

          form = <DynamicClassName />;
        }

        break;
    }

    const showReset = [
      FieldTypes.FORM,
      FieldTypes.INTEGRATION,
      FieldTypes.ADMIN_NOTIFICATIONS,
      FieldTypes.PAGE,
      FieldTypes.SUBMIT,
      FieldTypes.CONNECTIONS,
      FieldTypes.HTML,
      FieldTypes.MAILING_LIST,
      FieldTypes.RECAPTCHA,
      FieldTypes.PASSWORD,
      FieldTypes.CONFIRMATION,
    ].indexOf(props.type) === -1;

    return (
        <AlwaysNearbyBox
          stickyTop={
            <FormSettings
              editForm={editForm}
              editIntegrations={editIntegrations}
              editAdminNotifications={editAdminNotifications}
              hash={hash}
              integrationCount={integrationCount}
            />
          }
        >
          <h3>
            <span>{title}</span>

            {showReset &&
            <button
              className={"btn small property-reset"}
              title={"Reset to default values"}
              onClick={this.resetField}
            >
              Reset
            </button>
            }
          </h3>
          <h4 dangerouslySetInnerHTML={{ __html: props.label }} />

          <hr style={{ marginBottom: 10 }} />

          {form ? form : <p>Please select an element</p>}


        </AlwaysNearbyBox>
    );
  }

  updateField = (keyValueObject) => {
    const { hash, updateProperties } = this.props;

    updateProperties(hash, keyValueObject);
  };

  resetField = () => {
    const { hash, resetProperties, fields } = this.props;

    for (const field of fields) {
      if (field.hash === hash) {
        resetProperties(hash, field);

        return;
      }
    }
  };
}
