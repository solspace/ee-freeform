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

  constructor(props, context) {
    super(props, context);

    this.renderAsSelect = this.renderAsSelect.bind(this);
    this.renderAsRadios = this.renderAsRadios.bind(this);
  }

  getType() {
    return FieldTypes.DYNAMIC_RECIPIENTS;
  }

  render() {
    const {label, notificationId, required, showAsRadio, instructions} = this.props.properties;

    let badges;
    if (!notificationId) {
      badges = <Badge label="No Template" type={Badge.WARNING} />;
    }

    return (
      <div>
        <Label label={label} type={this.getType()} isRequired={required}>{badges}</Label>
        <Instructions instructions={instructions}/>
        {showAsRadio ? this.renderAsRadios() : this.renderAsSelect()}
      </div>
    );
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
