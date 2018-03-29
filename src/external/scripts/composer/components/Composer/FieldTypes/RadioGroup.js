/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React from "react";
import PropTypes from 'prop-types';
import {RADIO_GROUP} from "../../../constants/FieldTypes";
import HtmlInput from "./HtmlInput";
import Radio from "./Components/Radio";
import Label from "./Components/Label";
import Instructions from "./Components/Instructions";
import {connect} from "react-redux";

@connect(
  (state) => ({
    hash: state.context.hash,
    composerProperties: state.composer.properties,
  })
)
export default class RadioGroup extends HtmlInput {
  static propTypes = {
    properties: PropTypes.shape({
      label: PropTypes.string.isRequired,
      required: PropTypes.bool.isRequired,
      options: PropTypes.array,
      value: PropTypes.string,
    }).isRequired,
  };

  getClassName() {
    return 'RadioGroup';
  }

  getType() {
    return RADIO_GROUP;
  }

  renderInput() {
    const {properties} = this.props;
    const {options}    = properties;

    let radios = [];
    if (options) {
      for (let i = 0; i < options.length; i++) {
        const {label, value} = options[i];

        radios.push(
          <Radio
            key={i}
            label={label}
            value={value}
            isChecked={value === this.props.properties.value}
            properties={properties} />
        );
      }
    }

    return radios;
  }
}
