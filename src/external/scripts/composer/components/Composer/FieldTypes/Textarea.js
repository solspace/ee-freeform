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
import Text  from "./Text";

export default class Textarea extends Text {
  getClassName() {
    return "Textarea";
  }

  renderInput() {
    const { value, rows } = this.props.properties;

    return (
      <textarea
        readOnly={true}
        disabled={true}
        rows={rows ? rows : 2}
        className={this.prepareInputClass()}
        {...this.getCleanProperties()}
      >{value}</textarea>
    );
  }
}
