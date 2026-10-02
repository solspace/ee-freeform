/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes          from "prop-types";
import React              from "react";
import BasePropertyEditor from "./BasePropertyEditor";
import TextareaProperty   from "./PropertyItems/TextareaProperty";
import TextProperty       from "./PropertyItems/TextProperty";
import LightSwitchProperty from "./PropertyItems/LightSwitchProperty";

export default class Password extends BasePropertyEditor {
  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    hash: PropTypes.string.isRequired,
    properties: PropTypes.shape({
      type: PropTypes.string.isRequired,
      label: PropTypes.string,
      handle: PropTypes.string,
      required: PropTypes.bool,
      placeholder: PropTypes.string,
    }).isRequired,
  };

  render() {
    const { properties: { handle, placeholder = "", required = false, instructions = "" } } = this.context;

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

        <TextProperty
          label="Placeholder"
          instructions="The text that will be shown if the field doesn’t have a value."
          name="placeholder"
          value={placeholder}
          onChangeHandler={this.update}
        />
      </div>
    );
  }
}
