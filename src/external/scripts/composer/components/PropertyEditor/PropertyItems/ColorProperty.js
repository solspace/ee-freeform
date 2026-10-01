import React from "react";
import BasePropertyItem from "./BasePropertyItem";

export default class ColorProperty extends BasePropertyItem {
  renderInput() {
    const { name, value, readOnly, disabled, instructions, onChangeHandler } = this.props;

    return (
      <input
        id={this.inputId}
        type="color"
        name={name}
        value={/^#[0-9a-f]{6}$/i.test(value) ? value : "#000000"}
        disabled={disabled || readOnly}
        aria-describedby={instructions ? this.hintId : undefined}
        onChange={event => onChangeHandler(name, event.target.value)}
      />
    );
  }
}
