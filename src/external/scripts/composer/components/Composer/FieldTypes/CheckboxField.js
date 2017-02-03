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
import {CHECKBOX} from "../../../constants/FieldTypes";
import HtmlInput from "./HtmlInput";
import Checkbox from "./Components/Checkbox";
import Instructions from "./Components/Instructions";
import {connect} from "react-redux";

@connect(
  (state) => ({
    hash: state.context.hash,
    composerProperties: state.composer.properties,
    mailingListIntegrations: state.mailingLists.list,
  })
)
export default class CheckboxField extends HtmlInput {
  static propTypes = {
    mailingListIntegrations: PropTypes.array.isRequired,
    hash: PropTypes.string,
    properties: PropTypes.shape({
      label: PropTypes.string.isRequired,
      required: PropTypes.bool.isRequired,
      checked: PropTypes.bool,
      value: PropTypes.oneOfType([
        React.PropTypes.string,
        React.PropTypes.number,
        React.PropTypes.bool,
      ]),
    }).isRequired,
  };

  getType() {
    return CHECKBOX;
  }

  render() {
    const {properties} = this.props;

    const {label, required, checked, instructions} = properties;

    return (
      <div>
        <Instructions instructions={instructions}/>
        <Checkbox
          label={label}
          isChecked={!!checked}
          properties={properties}
          isRequired={required}
        />
      </div>
    );
  }
}
