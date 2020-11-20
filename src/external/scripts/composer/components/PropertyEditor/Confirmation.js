/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import * as FieldTypes from "../../constants/FieldTypes";
import BasePropertyEditor from "./BasePropertyEditor";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import TextProperty from "./PropertyItems/TextProperty";

@connect(
  (state) => ({
    composerProperties: state.composer.properties,
    hash: state.context.hash,
  }),
)
export default class Confirmation extends BasePropertyEditor {
  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
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
    const { composerProperties } = this.props;

    const { properties: { label, value, handle, placeholder, required, instructions, targetFieldHash } } = this.context;

    let allowedFields = [];
    for (let key in composerProperties) {
      if (!composerProperties.hasOwnProperty(key)) {
        continue;
      }

      const prop = composerProperties[key];
      if (FieldTypes.CONFIRMATION_SUPPORTED_TYPES.indexOf(prop.type) === -1) {
        continue;
      }

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
