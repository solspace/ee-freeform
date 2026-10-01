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

export default class LightSwitchProperty extends BasePropertyItem {
    static propTypes = {
        ...BasePropertyItem.propTypes,
        checked: PropTypes.bool,
        bold: PropTypes.bool,
    };

    render() {
        const { label, instructions, name, readOnly, disabled, className, checked, bold, onChangeHandler } = this.props;

        const style = { fontWeight: bold ? "bold" : "normal" };

        return (
            <div className="composer-property-item composer-property-item-lightswitch">
                <div className="composer-property-heading">
                    <label
                        htmlFor={this.inputId}
                        style={style}
                    >
                        {label}
                    </label>
                    {instructions && (
                        <div
                            className="composer-property-instructions"
                            id={this.hintId}
                        >
                            <p>{instructions}</p>
                        </div>
                    )}
                </div>
                <div className="composer-property-input">
                    <label className={`composer-property-lightswitch ${readOnly ? "is-readonly" : ""}`}>
                        <input
                            type="checkbox"
                            id={this.inputId}
                            className={`lightswitch-input ${className || ""}`}
                            name={name}
                            disabled={disabled || readOnly}
                            readOnly={readOnly}
                            checked={!!checked}
                            onChange={onChangeHandler}
                            aria-describedby={instructions ? this.hintId : undefined}
                            value="1"
                        />
                        <span
                            className="lightswitch-visual"
                            aria-hidden="true"
                        >
                        </span>
                    </label>
                </div>
            </div>
        );
    }
}
