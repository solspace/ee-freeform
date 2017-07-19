/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
import BasePropertyItem from "./BasePropertyItem";
import {SketchPicker} from "react-color";
import {connect} from "react-redux";

export default class ColorProperty extends BasePropertyItem {
  static initialState = {
    displayColorPicker: false,
  };

  constructor(props, context) {
    super(props, context);
    this.state = {
      ...ColorProperty.initialState,
    };

    this.handleClick = this.handleClick.bind(this);
    this.handleChange = this.handleChange.bind(this);
    this.handleClose = this.handleClose.bind(this);
  }

  renderInput() {
    const {name, value, onChangeHandler, readOnly, disabled, className} = this.props;
    const {displayColorPicker, color} = this.state;

    const classes = [className];
    if (readOnly && disabled) {
      classes.push("code");
    }

    return (
      <div>
        <div className="freeform-colorpicker-preview-wrapper" onClick={this.handleClick}>
          <div
            className="freeform-colorpicker-preview"
            style={{backgroundColor: value}}
          ></div>
        </div>

        { displayColorPicker && !readOnly && !disabled &&
        <div className="freeform-colorpicker-wrapper">
          <div className="freeform-colorpicker-cover" onClick={this.handleClose}/>
          <SketchPicker
            color={value}
            onChange={this.handleChange}
            disableAlpha={true}
          />
        </div>
        }
      </div>
    );
  }

  handleClick = () => {
    this.setState({displayColorPicker: !this.state.displayColorPicker});
  };

  handleClose = () => {
    this.setState({displayColorPicker: false});
  };

  handleChange = (color) => {
    this.props.onChangeHandler('color', color.hex);
  };
}
