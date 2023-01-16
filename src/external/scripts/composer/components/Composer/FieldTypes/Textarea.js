/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import React from "react";
import Text from "./Text";

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
