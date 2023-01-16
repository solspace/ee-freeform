/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import { CHECKBOX } from "../../../../constants/FieldTypes";
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
    const { label, isChecked, value, isRequired } = this.props;

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
          <span dangerouslySetInnerHTML={{ __html: label }} />
          {isRequired ? <span className="required" /> : ""}
          {this.props.children}
        </label>
      </div>
    );
  }
}
