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
import BasePropertyEditor from "./BasePropertyEditor";
import TextProperty from "./PropertyItems/TextProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";

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
    const {properties: {label, handle, required, checked, value, instructions}} = this.context;

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

        <CheckboxProperty
          label="This field is Required?"
          name="required"
          checked={required}
          onChangeHandler={this.update}
        />

        <CheckboxProperty
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

        <TextProperty
          label="Label"
          instructions="Field label used to describe the field."
          name="label"
          value={label}
          onChangeHandler={this.update}
        />

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
