/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
import BasePropertyEditor from "./BasePropertyEditor";
import TextProperty from "./PropertyItems/TextProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import countries from "./Data/countries";

export default class Phone extends BasePropertyEditor {
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
      pattern: PropTypes.string,
      countryCode: PropTypes.string,
    }).isRequired,
  };

  render() {
    const {properties: {label, value, handle, placeholder, required, instructions, pattern, countryCode}} = this.context;

    let countryInput = '';
    if (!pattern || !pattern.length) {
      countryInput = (
        <SelectProperty
          label="Default Country"
          instructions="Used to validate local phone numbers. International numbers will work regardless."
          name="countryCode"
          value={countryCode ? countryCode : 'US'}
          onChangeHandler={this.update}
          options={countries}
        />
      );
    }

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

        <hr />

        <TextProperty
          label="Pattern"
          instructions="Custom phone pattern (i.e. '(xxx) xxx xxxx'). The letter 'x' stands for a digit between 0-9. if left blank - will default to a universal phone number validation pattern."
          name="pattern"
          placeholder="Optional"
          value={pattern}
          onChangeHandler={this.update}
        />

        {countryInput}
      </div>
    );
  }
}
