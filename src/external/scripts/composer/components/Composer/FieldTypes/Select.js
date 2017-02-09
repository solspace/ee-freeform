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
import {SELECT} from "../../../constants/FieldTypes";
import HtmlInput from "./HtmlInput";
import Option from "./Components/Option";
import Label from "./Components/Label";
import Instructions from "./Components/Instructions";
import {connect} from "react-redux";

@connect(
  (state) => ({
    globalProps: state.composer.properties,
  })
)
export default class Select extends HtmlInput {
  static propTypes = {
    properties: PropTypes.shape({
      label: PropTypes.string.isRequired,
      required: PropTypes.bool.isRequired,
      options: PropTypes.array.isRequired,
      value: PropTypes.string,
    }).isRequired,
  };

  getType() {
    return SELECT;
  }

  render() {
    const {properties}               = this.props;
    const {label, required, options, instructions} = properties;

    if (!options) {
      return;
    }

    let selectOptions = [];
    for (let i = 0; i < options.length; i++) {
      const {label, value} = options[i];

      selectOptions.push(
        <Option
          key={i}
          label={label}
          value={value}
          properties={properties} />
      );
    }

    return (
      <div>
        <Label label={label} type={this.getType()} isRequired={required} />
        <Instructions instructions={instructions}/>
        <div className="select">
          <select readOnly={true} disabled={true} value={this.props.properties.value}>
            {selectOptions}
          </select>
        </div>
      </div>
    );
  }
}
