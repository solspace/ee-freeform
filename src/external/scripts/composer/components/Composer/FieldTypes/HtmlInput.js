/*
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
import {TEXT} from "../../../constants/FieldTypes";
import Label from "./Components/Label";
import Instructions from "./Components/Instructions";

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

  getType() {
    return TEXT;
  }

  getCleanProperties() {
    const {properties} = this.props;

    const clean = {...properties};

    for (var key in clean) {
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

  render() {
    const {properties: {label, type, required, instructions}} = this.props;

    return (
      <div>
        <Label label={label} type={type} isRequired={required} />
        <Instructions instructions={instructions}/>
        <input readOnly={true} className="composer-ft-text text fullwidth"
               type={this.getType()}
               {...this.getCleanProperties()}
        />
      </div>
    );
  }
}
