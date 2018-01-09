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
import PropTypes from "prop-types";
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
        PropTypes.string,
        PropTypes.number,
        PropTypes.bool,
      ]),
    }).isRequired,
  };

  getClassName() {
    return 'CheckboxField';
  }

  getType() {
    return CHECKBOX;
  }

  render() {
    const {properties} = this.props;

    const {label, required, checked, instructions} = properties;

    return (
      <div className={this.prepareWrapperClass()}>
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
