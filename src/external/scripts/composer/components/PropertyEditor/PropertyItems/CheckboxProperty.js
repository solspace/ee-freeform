/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import BasePropertyItem from "./BasePropertyItem";

export default class CheckboxProperty extends BasePropertyItem {
  static propTypes = {
    ...BasePropertyItem.propTypes,
    checked: PropTypes.bool,
    bold: PropTypes.bool,
  };

  render() {
    const { instructions } = this.props;

    return (
      <div className="composer-property-item">
        {instructions &&
        <div className="composer-property-heading">
          <div id={this.hintId} className="composer-property-instructions">
            <p>{instructions}</p>
          </div>
        </div>
        }
        <div className="composer-property-input">
          {this.renderInput()}
        </div>
      </div>
    );
  }

  renderInput() {
    const { label, name, readOnly, disabled, onChangeHandler, className, checked, bold } = this.props;

    let style = { fontWeight: "normal" };

    if (!!bold) {
      style.fontWeight = "bold";
    }

    return (
      <div className="composer-property-checkbox">
        <input
          id={this.inputId}
          aria-describedby={this.props.instructions ? this.hintId : undefined}
          type="checkbox"
          className={className}
          name={name}
          readOnly={readOnly}
          disabled={disabled || readOnly}
          checked={!!checked}
          onChange={onChangeHandler}
          value={true}
        />
        <label htmlFor={this.inputId} style={style}>
          {label}
        </label>
      </div>
    );
  }
}
