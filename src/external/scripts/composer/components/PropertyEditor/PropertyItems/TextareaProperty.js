/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import React from "react";
import BasePropertyItem from "./BasePropertyItem";

export default class TextareaProperty extends BasePropertyItem {
  renderInput() {
    const { name, readOnly, disabled, onChangeHandler, value, className, rows, nullable } = this.props;

    const classes = [className];
    if (readOnly && disabled) {
      classes.push("code");
    }

    return (
      <textarea
        id={this.inputId}
        aria-describedby={this.props.instructions ? this.hintId : undefined}
        aria-required={!!this.props.required}
        className={classes.join(" ")}
        name={name}
        readOnly={readOnly}
        disabled={disabled}
        rows={rows ? rows : 3}
        onChange={onChangeHandler}
        value={value}
        data-nullable={!!nullable}
      />
    );
  }
}
