/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";

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
  };

  constructor(props, context) {
    super(props, context);

    this.renderInput = this.renderInput.bind(this);
  }

  render() {
    const {label, instructions, required} = this.props;

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
