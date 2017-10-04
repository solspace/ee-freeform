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
import {TEXT} from "../../../constants/FieldTypes";
import Label from "./Components/Label";
import Instructions from "./Components/Instructions";
import {slugify, underscored} from "underscore.string";

const allowedProperties = [
  "name",
  "placeholder",
  "align",
  "alt",
  "autocomplete",
  "autofocus",
  "checked",
  "disabled",
  "height",
  "max",
  "maxlength",
  "min",
  "pattern",
  "readonly",
  "required",
  "size",
  "step",
  "value",
  "width",
  "inputId"
];

export default class HtmlInput extends Component {
  static propTypes = {
    properties: PropTypes.object.isRequired
  };

  constructor(props, context) {
    super(props, context);

    this.getBadges            = this.getBadges.bind(this);
    this.getWrapperClassNames = this.getWrapperClassNames.bind(this);
    this.prepareWrapperClass  = this.prepareWrapperClass.bind(this);
    this.renderInput          = this.renderInput.bind(this);
    this.getClassName         = this.getClassName.bind(this);
  }

  getClassName() {
    return 'HtmlInput';
  }

  getType() {
    return TEXT;
  }

  getCleanProperties() {
    const {properties} = this.props;

    const clean = {...properties};

    for (let key in clean) {
      if (allowedProperties.indexOf(key) === -1) {
        delete clean[key];
      }
    }

    if (clean.inputId) {
      clean.id = clean.inputId;
      delete clean.inputId;
    }

    return clean;
  }

  renderInput() {
    return (
      <input readOnly={true} className={this.prepareInputClass()}
             type={this.getType()}
             {...this.getCleanProperties()}
      />
    );
  }

  render() {
    const {properties: {label, type, required, instructions}} = this.props;

    return (
      <div className={this.prepareWrapperClass()}>
        <Label label={label} type={type} isRequired={required}>{this.getBadges()}</Label>
        <Instructions instructions={instructions}/>
        {this.renderInput()}
      </div>
    );
  }

  /**
   * Return any Badge objects if applicable
   */
  getBadges() {
  }

  /**
   * Return any additional wrapper class names as an array
   *
   * @return {string[]}
   */
  getWrapperClassNames() {
    return [];
  }

  /**
   * @return {string}
   */
  prepareWrapperClass() {
    let wrapperClassNames = this.getWrapperClassNames();

    wrapperClassNames.push('composer-ft-' + slugify(underscored(this.getClassName())) + '-wrapper');

    return wrapperClassNames.join(' ');
  }

  /**
   * Return any additional input class names as an array
   *
   * @return {string[]}
   */
  getInputClassNames() {
    return [];
  }

  /**
   * @return {string}
   */
  prepareInputClass() {
    let inputClassNames = this.getInputClassNames();

    inputClassNames.push('composer-ft-' + slugify(underscored(this.getClassName())));

    return inputClassNames.join(' ');
  }
}
