/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import PropTypes       from "prop-types";
import React           from "react";
import { connect }     from "react-redux";
import * as FieldTypes from "../../../constants/FieldTypes";
import Badge           from "./Components/Badge";
import Option          from "./Components/Option";
import Radio           from "./Components/Radio";
import HtmlInput       from "./HtmlInput";

@connect(
  (state) => ({
    hash: state.context.hash,
    composerProperties: state.composer.properties,
  }),
)
export default class DynamicRecipients extends HtmlInput {
  static propTypes = {
    ...HtmlInput.propTypes,
    notificationId: PropTypes.number,
    showAsRadio: PropTypes.bool,
  };

  getClassName() {
    return "DynamicRecipients";
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
    const badges = super.getBadges();
    const { notificationId } = this.props.properties;

    if (!notificationId) {
      badges.push(<Badge key={"template"} label="No Template" type={Badge.WARNING} />);
    }

    return badges;
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
