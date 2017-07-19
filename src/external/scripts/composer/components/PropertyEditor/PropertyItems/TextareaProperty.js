/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
import BasePropertyItem from "./BasePropertyItem";

export default class TextareaProperty extends BasePropertyItem {
  renderInput() {
    const {name, readOnly, disabled, onChangeHandler, value, className, rows} = this.props;

    const classes = [className];
    if (readOnly && disabled) {
      classes.push("code");
    }

    return (
      <textarea
        className={classes.join(" ")}
        name={name}
        readOnly={readOnly}
        disabled={disabled}
        rows={rows ? rows : 2}
        onChange={onChangeHandler}
        value={value}
      />
    );
  }
}
