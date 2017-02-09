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
import {TEXT} from "../../../constants/FieldTypes";
import Badge from "./Components/Badge";
import Label from "./Components/Label";
import HtmlInput from "./HtmlInput";

export default class Hidden extends HtmlInput {
  getType() {
    return TEXT;
  }

  render() {
    const {properties: {label, type, required}} = this.props;

    return (
      <div>
        <Label isRequired={required}>
          <Badge label="Hidden field" type={Badge.VISIBILITY} />
        </Label>
        <input
          readOnly={true}
          className="composer-ft-text text fullwidth"
          type={this.getType()}
          {...this.getCleanProperties()}
        />
      </div>
    );
  }
}
