/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
import {CHECKBOX} from "../../../../constants/FieldTypes";
import HtmlInput from "../HtmlInput";

export default class Checkbox extends HtmlInput {
  static propTypes = {
    label: PropTypes.node.isRequired,
    properties: PropTypes.object.isRequired,
    isChecked: PropTypes.bool.isRequired,
    isRequired: PropTypes.bool,
  };

  getType() {
    return CHECKBOX;
  }

  render() {
    const {label, isChecked, value, isRequired} = this.props;

    const labelClass = ["composer-field-checkbox-single"];
    if (isRequired) {
      labelClass.push("composer-field-required");
    }

    return (
      <div>
        <label className={labelClass.join(" ")}>
          <input
            className="composer-ft-checkbox"
            type={this.getType()}
            value={value}
            readOnly={true}
            disabled={true}
            checked={isChecked}
            {...this.getCleanProperties()}
          />
          <span dangerouslySetInnerHTML={{__html: label}} />
          {isRequired ? <span className="required" /> : ""}
        </label>
      </div>
    );
  }
}
