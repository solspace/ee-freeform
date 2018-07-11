/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React from "react";
import BasePropertyItem from "./BasePropertyItem";

export default class TextProperty extends BasePropertyItem {
  renderInput() {
    const { name, readOnly, disabled, onChangeHandler, value, className, placeholder, isNumeric, nullable } = this.props;

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
        data-nullable={!!nullable}
        value={value}
      />
    );
  }
}
