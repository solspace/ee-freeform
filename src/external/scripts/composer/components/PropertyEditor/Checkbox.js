/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import BasePropertyEditor from "./BasePropertyEditor";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import TextProperty from "./PropertyItems/TextProperty";
import LightSwitchProperty from "./PropertyItems/LightSwitchProperty";

export default class Checkbox extends BasePropertyEditor {
  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
      id: PropTypes.number.isRequired,
      type: PropTypes.string.isRequired,
      handle: PropTypes.string.isRequired,
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

  render() {
    const { properties: { handle, required, checked, value, instructions } } = this.context;

    return (
      <div>
        <TextProperty
          label="Handle"
          name="handle"
          instructions="How you’ll refer to this field in the templates."
          value={handle}
          onChangeHandler={this.updateHandle}
        />

        <hr />

        <LightSwitchProperty
          label="This field is required"
          name="required"
          checked={required}
          onChangeHandler={this.update}
        />

        <LightSwitchProperty
          label="Checked by default"
          name="checked"
          checked={checked}
          onChangeHandler={this.update}
        />

        <TextProperty
          label="Value"
          instructions="The value for this field."
          name="value"
          value={value}
          onChangeHandler={this.update}
        />

        <hr />

        <TextareaProperty
          label="Instructions"
          instructions="Field specific user instructions."
          name="instructions"
          value={instructions}
          onChangeHandler={this.update}
        />
      </div>
    );
  }
}
