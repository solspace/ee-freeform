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
        {label} {type == HIDDEN ? " (Hidden field)" : ""}
        {label && isRequired ? <span>*</span> : ""}
        {this.props.children}
        {!label && isRequired ? <span>*</span> : ""}
      </label>
    );
  }
}
