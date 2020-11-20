/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React, { Component } from "react";
import { HIDDEN } from "../../../../constants/FieldTypes";

export default class Label extends Component {
  static propTypes = {
    label: PropTypes.string,
    isRequired: PropTypes.bool,
    type: PropTypes.string,
  };

  render() {
    const { label, isRequired, type } = this.props;

    const labelClass = ["composer-field-label"];
    if (isRequired) {
      labelClass.push("composer-field-required");
    }

    if (!label) {
      labelClass.push("badge-only");
    }

    return (
      <label className={labelClass.join(" ")}>
        <span dangerouslySetInnerHTML={{ __html: label }} /> {type === HIDDEN ? " (Hidden field)" : ""}
        {label && isRequired ? <span className="required" /> : ""}
        {this.props.children}
        {!label && isRequired ? <span className="required" /> : ""}
      </label>
    );
  }
}
