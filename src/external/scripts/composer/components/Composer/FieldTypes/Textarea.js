/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
import Label from "./Components/Label";
import Instructions from "./Components/Instructions";
import HtmlInput from "./HtmlInput";

export default class Textarea extends HtmlInput {
  render() {
    const {value, label, type, required, rows, instructions} = this.props.properties;

    return (
      <div>
        <Label
          label={label}
          type={type}
          isRequired={required}
        />
        <Instructions instructions={instructions} />
        <textarea
          readOnly={true}
          disabled={true}
          rows={rows ? rows : 2}
          className="composer-ft-textarea text fullwidth"
          {...this.getCleanProperties()}
        >{value}</textarea>
      </div>
    )
  }
}
