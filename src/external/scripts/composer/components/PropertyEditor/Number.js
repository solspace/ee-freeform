/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import BasePropertyEditor from "./BasePropertyEditor";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import CustomProperty from "./PropertyItems/CustomProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import TextProperty from "./PropertyItems/TextProperty";

export default class Number extends BasePropertyEditor {
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
      minLength: PropTypes.number,
      maxLength: PropTypes.number,
      minValue: PropTypes.number,
      maxValue: PropTypes.number,
      decimalCount: PropTypes.number,
      decimalSeparator: PropTypes.string.isRequired,
      thousandsSeparator: PropTypes.string,
      allowNegative: PropTypes.bool.isRequired,
    }).isRequired,
  };

  render() {
    const { properties: { label, value, handle, placeholder, required, instructions } } = this.context;
    const { properties: { minLength, maxLength, minValue, maxValue } } = this.context;
    const { properties: { decimalCount, decimalSeparator, thousandsSeparator, allowNegative } } = this.context;

    let decimalSeparatorInput = "";
    if (decimalCount && parseInt(decimalCount)) {
      decimalSeparatorInput = (
        <SelectProperty
          label="Decimal Separator"
          instructions="Used to separate decimals."
          name="decimalSeparator"
          value={decimalSeparator}
          onChangeHandler={this.update}
          options={[
            { key: ".", value: "." },
            { key: ",", value: "," },
          ]}
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

        <CheckboxProperty
          label="Allow negative numbers?"
          name="allowNegative"
          checked={allowNegative}
          onChangeHandler={this.update}
        />

        <CustomProperty
          label="Min/Max Values"
          instructions="The minimum and/or maximum numeric value this field is allowed to have (optional)."
        >
          <div className="composer-property-input composer-property-flex">
            <input name="minValue"
                   value={minValue ? minValue : ""}
                   placeholder="Min"
                   className=""
                   onChange={this.update}
                   data-is-numeric={true}
            />
            <input name="maxValue"
                   value={maxValue ? maxValue : ""}
                   placeholder="Max"
                   onChange={this.update}
                   data-is-numeric={true}
            />
          </div>
        </CustomProperty>

        <CustomProperty
          label="Min/Max Length"
          instructions="The minimum and/or maximum character length this field is allowed to have (optional)."
        >
          <div className="composer-property-input composer-property-flex">
            <input name="minLength"
                   value={minLength ? minLength : ""}
                   placeholder="Min"
                   className=""
                   onChange={this.update}
                   data-is-numeric={true}
            />
            <input name="maxLength"
                   value={maxLength ? maxLength : ""}
                   placeholder="Max"
                   onChange={this.update}
                   data-is-numeric={true}
            />
          </div>
        </CustomProperty>


        <TextProperty
          label="Decimal Count"
          instructions="The number of decimal places allowed."
          name="decimalCount"
          placeholder="Leave blank for no decimals."
          value={decimalCount ? decimalCount : ""}
          isNumeric={true}
          onChangeHandler={this.update}
        />

        {decimalSeparatorInput}

        <SelectProperty
          label="Thousands Separator"
          instructions="Used to separate thousands."
          name="thousandsSeparator"
          value={thousandsSeparator}
          onChangeHandler={this.update}
          emptyOption="None"
          options={[
            { key: " ", value: "Space" },
            { key: ",", value: "," },
            { key: ".", value: "." },
          ]}
        />
      </div>
    );
  }
}
