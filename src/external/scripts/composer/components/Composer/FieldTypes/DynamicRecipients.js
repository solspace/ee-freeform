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
import HtmlInput from "./HtmlInput";
import * as FieldTypes from "../../../constants/FieldTypes";
import Label from "./Components/Label";
import Instructions from "./Components/Instructions";
import Badge from "./Components/Badge";
import Radio from "./Components/Radio";
import Option from "./Components/Option";
import {connect} from "react-redux";

@connect(
  (state) => ({
    hash: state.context.hash,
    composerProperties: state.composer.properties,
  })
)
export default class DynamicRecipients extends HtmlInput {
  static propTypes = {
    ...HtmlInput.propTypes,
    notificationId: PropTypes.number,
    showAsRadio: PropTypes.bool,
  };

  getClassName() {
    return 'DynamicRecipients';
  }

  constructor(props, context) {
    super(props, context);

    this.renderAsSelect = this.renderAsSelect.bind(this);
    this.renderAsRadios = this.renderAsRadios.bind(this);
  }

  getType() {
    return FieldTypes.DYNAMIC_RECIPIENTS;
  }

  getBadges() {
    const {notificationId} = this.props.properties;

    if (!notificationId) {
      return <Badge label="No Template" type={Badge.WARNING} />;
    }
  }

  renderInput() {
    const {showAsRadio} = this.props.properties;

    return showAsRadio ? this.renderAsRadios() : this.renderAsSelect();
  }

  renderAsSelect() {
    const {properties} = this.props;
    const {options}    = properties;

    let selectOptions = [];
    if (options) {
      for (let i = 0; i < options.length; i++) {
        const {label, value} = options[i];

        selectOptions.push(
          <Option
            key={i}
            label={label}
            value={value}
            properties={properties}
          />
        );
      }
    }

    return (
      <div className="select">
        <select readOnly={true} disabled={true} value={properties.value}>
          {selectOptions}
        </select>
      </div>
    );
  }

  renderAsRadios() {
    const {properties} = this.props;
    const {options}    = properties;

    let radioOptions = [];
    if (options) {
      for (let i = 0; i < options.length; i++) {
        const {label, value} = options[i];

        radioOptions.push(
          <Radio
            key={i}
            label={label}
            value={value}
            properties={properties}
            isChecked={value === properties.value}
          />
        );
      }
    }

    return (
      <div>
        {radioOptions}
      </div>
    );
  }
}
