/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React, { Component } from "react";

export default class BasePropertyItem extends Component {
  static propTypes = {
    label: PropTypes.string.isRequired,
    instructions: PropTypes.string,
    name: PropTypes.string,
    readOnly: PropTypes.bool,
    disabled: PropTypes.bool,
    value: PropTypes.node,
    onChangeHandler: PropTypes.func,
    className: PropTypes.string,
    placeholder: PropTypes.string,
    isNumeric: PropTypes.bool,
    couldBeNumeric: PropTypes.bool,
    required: PropTypes.bool,
    nullable: PropTypes.bool,
  };

  constructor(props, context) {
    super(props, context);

    this.renderInput = this.renderInput.bind(this);
  }

  render() {
    const { label, instructions, required } = this.props;

    return (
      <div className="composer-property-item">
        <div className="composer-property-heading">
          <label className={required ? "required" : ""}>{label}</label>
          {instructions &&
          <div className="composer-property-instructions">
            <p>{instructions}</p>
          </div>
          }
        </div>
        <div className="composer-property-input">
          {this.renderInput()}
        </div>
        {this.props.children}
      </div>
    );
  }

  renderInput() {
    return "You should not use the 'BasePropertyItem'";
  }
}
