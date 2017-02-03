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
import BasePropertyItem from "./BasePropertyItem";

export default class TextProperty extends BasePropertyItem {
  renderInput() {
    const {name, readOnly, disabled, onChangeHandler, value, className, placeholder, isNumeric} = this.props;

    const classes = [className];
    if (readOnly && disabled) {
      classes.push("code");
    }

    return (
      <input
        type="text"
        className={classes.join(" ")}
        name={name}
        placeholder={placeholder ? placeholder : ""}
        readOnly={readOnly}
        disabled={disabled}
        onChange={onChangeHandler}
        data-is-numeric={!!isNumeric}
        value={value}
      />
    );
  }
}
