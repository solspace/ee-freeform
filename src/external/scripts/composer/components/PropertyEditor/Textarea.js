/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import PropTypes from "prop-types";
import React from "react";
import BasePropertyEditor from "./BasePropertyEditor";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import TextProperty from "./PropertyItems/TextProperty";

export default class Textarea extends BasePropertyEditor {
  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
      id: PropTypes.number.isRequired,
      type: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      handle: PropTypes.string.isRequired,
      value: PropTypes.string,
      placeholder: PropTypes.string,
      required: PropTypes.bool,
      rows: PropTypes.number,
      maxLength: PropTypes.number,
    }).isRequired,
  };

  render() {
    const { properties: { label, value, handle, placeholder, required, rows, instructions, maxLength } } = this.context;

    return (
      <div>
        <TextProperty
          label="Handle"
          instructions="How you’ll refer to this field in the templates."
          name="handle"
          value={handle}
          onChangeHandler={this.updateHandle}
        />

        <hr />

        <CheckboxProperty
          label="This field is required?"
          name="required"
          checked={required}
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

        <hr />

        <TextProperty
          label="Default Value"
          instructions="If present, this will be the value pre-populated when the form is rendered."
          name="value"
          value={value}
          onChangeHandler={this.update}
        />

        <TextProperty
          label="Placeholder"
          instructions="The text that will be shown if the field doesn’t have a value."
          name="placeholder"
          value={placeholder}
          onChangeHandler={this.update}
        />

        <TextProperty
          label="Rows"
          instructions="The number of rows in height for this field."
          name="rows"
          value={rows ? rows : 0}
          isNumeric={true}
          onChangeHandler={this.update}
        />

        <TextProperty
          label="Maximum Length"
          instructions="The maximum number of characters for this field."
          name="maxLength"
          value={maxLength ? maxLength : ""}
          isNumeric={true}
          onChangeHandler={this.update}
        />
      </div>
    );
  }
}
