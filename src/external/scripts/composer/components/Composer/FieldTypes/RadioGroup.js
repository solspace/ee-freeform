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

  getType() {
    return RADIO_GROUP;
  }

  render() {
    const {properties}               = this.props;
    const {label, required, options, instructions} = properties;

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

    return (
      <div>
        <Label label={label} isRequired={required} />
        <Instructions instructions={instructions}/>
        {radios}
      </div>
    );
  }
}
