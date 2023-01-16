/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import BasePropertyEditor from "./BasePropertyEditor";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import CustomProperty from "./PropertyItems/CustomProperty";
import MatrixEditorProperty from "./PropertyItems/MatrixEditorProperty";
import { TYPE_SELECT, TYPE_STRING } from "./PropertyItems/Table/Column.js"
import TextareaProperty from "./PropertyItems/TextareaProperty";
import TextProperty from "./PropertyItems/TextProperty";

export default class Table extends BasePropertyEditor {
  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
      id: PropTypes.number.isRequired,
      hash: PropTypes.string.isRequired,
      type: PropTypes.string.isRequired,
      handle: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      required: PropTypes.bool.isRequired,
      useScript: PropTypes.bool,
    }).isRequired,
  };

  render() {
    const { properties } = this.context;
    const { hash, label, handle, required, instructions } = properties;
    const { useScript, layout } = properties;

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

        <CheckboxProperty
          label="Use built in script?"
          instructions="Check this to enable built in javascript for handling adding new rows."
          name="useScript"
          checked={!!useScript}
          onChangeHandler={this.update}
        />

        <CustomProperty
          label="Table Layout"
          instructions={`Use semicolon ";" separated values for select options.`}
          content={
            <MatrixEditorProperty
              hash={hash}
              attribute={"layout"}
              columns={[
                { handle: "label", label: "Label" },
                {
                  handle: "type",
                  label: "type",
                  type: TYPE_SELECT,
                  options: [
                    { key: "text", label: "Text" },
                    { key: "checkbox", label: "Checkbox" },
                    { key: "select", label: "Select" },
                  ],
                },
                { handle: "value", label: "Value" },
              ]}
              values={layout}
            />
          }
        />

      </div>
    );
  }
}
