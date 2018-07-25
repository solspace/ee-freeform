/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import PropTypes from "prop-types";
import React, { Component } from "react";
import Checkbox from "./FieldTypes/CheckboxField";
import CheckboxGroup from "./FieldTypes/CheckboxGroup";
import Confirmation from "./FieldTypes/Confirmation";
import Datetime from "./FieldTypes/Datetime";
import DynamicRecipients from "./FieldTypes/DynamicRecipients";
import Email from "./FieldTypes/Email";
import File from "./FieldTypes/File";
import Hidden from "./FieldTypes/Hidden";
import Html from "./FieldTypes/Html";
import MailingList from "./FieldTypes/MailingList";
import MultipleSelect from "./FieldTypes/MultipleSelect";
import Number from "./FieldTypes/Number";
import Password from "./FieldTypes/Password";
import Phone from "./FieldTypes/Phone";
import RadioGroup from "./FieldTypes/RadioGroup";
import Rating from "./FieldTypes/Rating";
import Recaptcha from "./FieldTypes/Recaptcha";
import Regex from "./FieldTypes/Regex";
import Select from "./FieldTypes/Select";
import Submit from "./FieldTypes/Submit";
import Text from "./FieldTypes/Text";
import Textarea from "./FieldTypes/Textarea";
import Website from "./FieldTypes/Website";

const fieldTypes = {
  checkbox: Checkbox,
  checkbox_group: CheckboxGroup,
  text: Text,
  textarea: Textarea,
  email: Email,
  hidden: Hidden,
  html: Html,
  submit: Submit,
  radio_group: RadioGroup,
  select: Select,
  multiple_select: MultipleSelect,
  dynamic_recipients: DynamicRecipients,
  mailing_list: MailingList,
  file: File,
  datetime: Datetime,
  number: Number,
  phone: Phone,
  rating: Rating,
  website: Website,
  regex: Regex,
  confirmation: Confirmation,
  recaptcha: Recaptcha,
  password: Password,
};

export default class Field extends Component {
  static propTypes = {
    type: PropTypes.string.isRequired,
    properties: PropTypes.shape({
      type: PropTypes.string.isRequired,
      id: PropTypes.number,
      placeholder: PropTypes.string,
    }).isRequired,
    hash: PropTypes.string.isRequired,
    index: PropTypes.number.isRequired,
    rowIndex: PropTypes.number.isRequired,
  };

  static childContextTypes = {
    hash: PropTypes.string.isRequired,
    index: PropTypes.number.isRequired,
    rowIndex: PropTypes.number.isRequired,
  };

  getChildContext = () => ({
    hash: this.props.hash,
    index: this.props.index,
    rowIndex: this.props.rowIndex,
  });

  render() {
    const { type, properties } = this.props;

    if (fieldTypes[type]) {
      const DynamicClassName = fieldTypes[type];

      return <DynamicClassName properties={properties} />;
    }

    return <div>Field type "{type}" not found</div>;
  }
}
