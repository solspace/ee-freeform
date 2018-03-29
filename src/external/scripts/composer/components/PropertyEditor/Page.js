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
import BasePropertyEditor from "./BasePropertyEditor";
import TextProperty from "./PropertyItems/TextProperty";

export default class Page extends BasePropertyEditor {
  render() {
    const {properties: {label}} = this.context;

    return (
      <div>
        <TextProperty
          label="Label"
          instructions="Field label used to describe the field."
          name="label"
          value={label}
          onChangeHandler={this.update}
        />
      </div>
    );
  }
}
