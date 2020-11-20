/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
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
          <div className="composer-property-instructions">
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

    const randId = Math.random().toString(36).substring(2, 9);

    let style = { fontWeight: "normal" };

    if (!!bold) {
      style.fontWeight = "bold";
      style.color = "#576574";
    }

    return (
      <div className="composer-property-checkbox">
        <input
          id={randId}
          type="checkbox"
          className={className}
          name={name}
          readOnly={readOnly}
          disabled={disabled}
          checked={!!checked}
          onChange={onChangeHandler}
          value={true}
        />
        <label htmlFor={randId} style={style}>
          {label}
        </label>
      </div>
    );
  }
}
