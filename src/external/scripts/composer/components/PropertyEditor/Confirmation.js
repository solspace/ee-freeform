/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
import {connect} from "react-redux";
import * as FieldTypes from "../../constants/FieldTypes";
import BasePropertyEditor from "./BasePropertyEditor";
import TextProperty from "./PropertyItems/TextProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import SelectProperty from "./PropertyItems/SelectProperty";

@connect(
  (state) => ({
    composerProperties: state.composer.properties,
  })
)
export default class Confirmation extends BasePropertyEditor {
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
      targetFieldHash: PropTypes.node,
    }).isRequired,
  };

  static propTypes = {
    composerProperties: PropTypes.object.isRequired,
  };

  render() {
    const {composerProperties} = this.props;

    const {properties: {label, value, handle, placeholder, required, instructions, targetFieldHash}} = this.context;

    let allowedFields = [];
    for (let key in composerProperties) {
      if (!composerProperties.hasOwnProperty(key)) continue;

      const prop = composerProperties[key];
      if (FieldTypes.CONFIRMATION_SUPPORTED_TYPES.indexOf(prop.type) === -1) continue;

      allowedFields.push({
        key: key,
        value: prop.label,
      });
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

        <SelectProperty
          label="Target Field"
          instructions="The target Freeform field to be confirmed by re-entering its value."
          name="targetFieldHash"
          onChangeHandler={this.update}
          value={targetFieldHash}
          emptyOption="Select a field..."
          options={allowedFields}
        />
      </div>
    );
  }
}
