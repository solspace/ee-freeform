/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

import PropTypes from "prop-types";
import React from "react";
import BasePropertyEditor from "./BasePropertyEditor";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import ExternalOptionsProperty from "./PropertyItems/ExternalOptionsProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import TextProperty from "./PropertyItems/TextProperty";

export default class Select extends BasePropertyEditor {
  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
      id: PropTypes.number.isRequired,
      type: PropTypes.string.isRequired,
      handle: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      required: PropTypes.bool.isRequired,
      showCustomValues: PropTypes.bool.isRequired,
      value: PropTypes.node,
      options: PropTypes.array,
      source: PropTypes.string,
      target: PropTypes.node,
      configuration: PropTypes.object,
    }).isRequired,
  };

  render() {
    const { label, handle, value, options, required, showCustomValues, instructions } = this.context.properties;
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
          showEmptyOptionInput={true}
          value={value}
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
