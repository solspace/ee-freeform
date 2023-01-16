/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes       from "prop-types";
import React           from "react";
import { connect }     from "react-redux";
import * as FieldTypes from "../../../constants/FieldTypes";
import Badge           from "./Components/Badge";
import Checkbox        from "./Components/Checkbox";
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
    showAsCheckboxes: PropTypes.bool,
  };

  getClassName() {
    return "DynamicRecipients";
  }

  constructor(props, context) {
    super(props, context);

    this.renderAsSelect = this.renderAsSelect.bind(this);
    this.renderAsRadios = this.renderAsRadios.bind(this);
    this.renderAsCheckboxes = this.renderAsCheckboxes.bind(this);
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
    const { showAsRadio, showAsCheckboxes } = this.props.properties;

    if (showAsRadio) {
      return this.renderAsRadios();
    } else if (showAsCheckboxes) {
      return this.renderAsCheckboxes();
    }

    return this.renderAsSelect();
  }

  renderAsSelect() {
    const { properties } = this.props;
    const { options, values = [] } = properties;
    const firstValue = values && values.length > 0 ? values[0] : "";

    let selectOptions = [];
    if (options) {
      for (let i = 0; i < options.length; i++) {
        const { label, value } = options[i];

        selectOptions.push(
          <Option
            key={i}
            label={label}
            value={value}
            properties={properties}
          />,
        );
      }
    }

    return (
      <div className="select">
        <select readOnly={true} disabled={true} value={firstValue}>
          {selectOptions}
        </select>
      </div>
    );
  }

  renderAsRadios() {
    const { properties } = this.props;
    const { options, values = [] } = properties;
    const firstValue = values && values.length > 0 ? values[0] : "";

    let radioOptions = [];
    if (options) {
      for (let i = 0; i < options.length; i++) {
        const { label, value } = options[i];

        radioOptions.push(
          <Radio
            key={i}
            label={label}
            value={value}
            properties={properties}
            isChecked={firstValue === value}
          />,
        );
      }
    }

    return (
      <div>
        {radioOptions}
      </div>
    );
  }

  renderAsCheckboxes() {
    const { properties } = this.props;
    const { options, values = [] } = properties;

    let checkboxOptions = [];
    if (options) {
      for (let i = 0; i < options.length; i++) {
        const { label, value } = options[i];

        checkboxOptions.push(
          <Checkbox
            key={i}
            label={label}
            value={value}
            properties={properties}
            isChecked={value ? (values && values.indexOf(value) !== -1) : false}
          />,
        );
      }
    }

    return (
      <div>
        {checkboxOptions}
      </div>
    );
  }
}
