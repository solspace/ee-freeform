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
import OptionTable from "./Components/OptionTable/OptionTable";
import TextProperty from "./PropertyItems/TextProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import CustomProperty from "./PropertyItems/CustomProperty";

export default class CheckboxGroup extends BasePropertyEditor {
  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
      id: PropTypes.number.isRequired,
      type: PropTypes.string.isRequired,
      handle: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      required: PropTypes.bool.isRequired,
      showCustomValues: PropTypes.bool.isRequired,
      values: PropTypes.array,
      options: PropTypes.array.isRequired,
    }).isRequired,
  };

  render() {
    const {properties: {label, handle, values, options, required, showCustomValues, instructions}} = this.context;

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

        <CustomProperty
          label="Options"
          instructions="Options for this checkbox group"
          content={
            <OptionTable
              values={values}
              options={options}
              triggerCustomValues={this.update}
              showCustomValues={showCustomValues}
            />
          }
        />
      </div>
    );
  }
}
