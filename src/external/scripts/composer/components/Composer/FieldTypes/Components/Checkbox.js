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
          {label}
          {isRequired ? <span>*</span> : ""}
        </label>
      </div>
    );
  }
}
