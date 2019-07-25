/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
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
        className={classes.join(" ")}
        name={name}
        readOnly={readOnly}
        disabled={disabled}
        rows={rows ? rows : 2}
        onChange={onChangeHandler}
        value={value}
        data-nullable={!!nullable}
      />
    );
  }
}
