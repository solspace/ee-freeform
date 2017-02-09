/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
import {CHECKBOX_GROUP} from "../../../constants/FieldTypes";
import HtmlInput from "./HtmlInput";
import Checkbox from "./Components/Checkbox";
import Label from "./Components/Label";
import Instructions from "./Components/Instructions";
import {connect} from "react-redux";

@connect(
  (state) => ({
    hash: state.context.hash,
    composerProperties: state.composer.properties,
  })
)
export default class CheckboxGroup extends HtmlInput {
  static propTypes = {
    properties: PropTypes.shape({
      label: PropTypes.string.isRequired,
      required: PropTypes.bool.isRequired,
      options: PropTypes.array,
      values: PropTypes.array,
    }).isRequired,
  };

  getType() {
    return CHECKBOX_GROUP;
  }

  render() {
    const {properties}                       = this.props;
    const {label, required, options, values, instructions} = properties;

    let checkboxes = [];
    if (options) {
      for (let i = 0; i < options.length; i++) {
        const {label, value} = options[i];

        checkboxes.push(
          <Checkbox
            key={i}
            label={label}
            value={value}
            isChecked={values ? (values.indexOf(value) !== -1) : false}
            properties={properties} />
        );
      }
    }

    return (
      <div>
        <Label label={label} isRequired={required} />
        <Instructions instructions={instructions}/>
        {checkboxes}
      </div>
    );
  }
}
