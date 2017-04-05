/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
import {HIDDEN} from "../../../../constants/FieldTypes";

export default class Label extends Component {
  static propTypes = {
    label: PropTypes.string,
    isRequired: PropTypes.bool,
    type: PropTypes.string,
  };

  render() {
    const {label, isRequired, type} = this.props;

    const labelClass = ["composer-field-label"];
    if (isRequired) {
      labelClass.push("composer-field-required");
    }

    if (!label) {
      labelClass.push("badge-only");
    }

    return (
      <label className={labelClass.join(" ")}>
        <span dangerouslySetInnerHTML={{__html: label}} /> {type == HIDDEN ? " (Hidden field)" : ""}
        {label && isRequired ? <span className="required" /> : ""}
        {this.props.children}
        {!label && isRequired ? <span className="required" /> : ""}
      </label>
    );
  }
}
