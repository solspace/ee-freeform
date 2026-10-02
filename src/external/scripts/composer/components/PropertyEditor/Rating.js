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
import ColorProperty from "./PropertyItems/ColorProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import TextProperty from "./PropertyItems/TextProperty";
import LightSwitchProperty from "./PropertyItems/LightSwitchProperty";

export default class Rating extends BasePropertyEditor {
  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
      id: PropTypes.number.isRequired,
      type: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      handle: PropTypes.string.isRequired,
      value: PropTypes.number,
      placeholder: PropTypes.string,
      required: PropTypes.bool,
      maxValue: PropTypes.number,
      colorIdle: PropTypes.string,
      colorHover: PropTypes.string,
      colorSelected: PropTypes.string,
    }).isRequired,
  };

  render() {
    const { properties: { value, handle, required, instructions, maxValue } } = this.context;
    const { properties: { colorIdle, colorHover, colorSelected } } = this.context;

    let starOptions = [];
    for (let i = 3; i <= 10; i++) {
      starOptions.push({
        key: i,
        value: i,
      });
    }

    let defaultValueOptions = [];
    for (let i = 1; i <= maxValue; i++) {
      defaultValueOptions.push({
        key: i,
        value: i,
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

        <LightSwitchProperty
          label="This field is required?"
          name="required"
          bold={true}
          checked={required}
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

        <hr />

        <SelectProperty
          label="Default Value"
          instructions="If present, this will be the value pre-populated when the form is rendered."
          name="value"
          value={value ? value : 0}
          onChangeHandler={this.update}
          options={defaultValueOptions}
          emptyOption="--"
          isNumeric={true}
        />

        <hr />

        <SelectProperty
          label="Maximum Number of Stars"
          instructions="Set how many stars there should be for this rating."
          name="maxValue"
          value={maxValue ? maxValue : 5}
          onChangeHandler={this.update}
          options={starOptions}
          isNumeric={true}
        />

        <ColorProperty
          label="Unselected Color"
          name="colorIdle"
          value={colorIdle}
          onChangeHandler={this.updateKeyValue}
        />

        <ColorProperty
          label="Hover Color"
          name="colorHover"
          value={colorHover}
          onChangeHandler={this.updateKeyValue}
        />

        <ColorProperty
          label="Selected Color"
          name="colorSelected"
          value={colorSelected}
          onChangeHandler={this.updateKeyValue}
        />
      </div>
    );
  }
}
