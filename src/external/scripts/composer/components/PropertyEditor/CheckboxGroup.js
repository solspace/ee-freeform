/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import BasePropertyEditor from "./BasePropertyEditor";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import ExternalOptionsProperty from "./PropertyItems/ExternalOptionsProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import TextProperty from "./PropertyItems/TextProperty";

export default class CheckboxGroup extends BasePropertyEditor {
  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
      id: PropTypes.number.isRequired,
      type: PropTypes.string.isRequired,
      handle: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      required: PropTypes.bool.isRequired,
      showCustomValues: PropTypes.bool,
      values: PropTypes.array,
      options: PropTypes.array,
      source: PropTypes.string,
      target: PropTypes.node,
      configuration: PropTypes.object,
    }).isRequired,
  };

  render() {
    const { label, handle, values, options = [], required, showCustomValues = false, instructions } = this.context.properties;
    const { source, target, configuration } = this.context.properties;

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

        <ExternalOptionsProperty
          values={values}
          customOptions={options}
          showCustomValues={showCustomValues}
          source={source}
          target={target}
          configuration={configuration}
          onChangeHandler={this.update}
        />
      </div>
    );
  }
}
