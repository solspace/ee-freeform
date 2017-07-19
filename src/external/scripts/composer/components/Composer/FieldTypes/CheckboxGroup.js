/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React from "react";
import PropTypes from "prop-types";
import {CHECKBOX_GROUP} from "../../../constants/FieldTypes";
import HtmlInput from "./HtmlInput";
import Checkbox from "./Components/Checkbox";
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

  getClassName() {
    return 'CheckboxGroup';
  }

  getType() {
    return CHECKBOX_GROUP;
  }

  renderInput() {
    const {properties}      = this.props;
    const {options, values} = properties;

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
            properties={properties}/>
        );
      }
    }

    return checkboxes;
  }
}
