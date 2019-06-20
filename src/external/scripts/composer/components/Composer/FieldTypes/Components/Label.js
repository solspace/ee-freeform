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
