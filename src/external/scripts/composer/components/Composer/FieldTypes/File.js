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
import {FILE} from "./../../../constants/FieldTypes";
import Label from "./Components/Label";
import Instructions from "./Components/Instructions";
import Badge from "./Components/Badge";
import HtmlInput from "./HtmlInput";

export default class File extends HtmlInput {
  static propTypes = {
    properties: PropTypes.shape({
      label: PropTypes.string.isRequired,
      required: PropTypes.bool.isRequired,
      assetSourceId: PropTypes.number,
    }).isRequired,
  };

  getType() {
    return FILE;
  }

  render() {
    const {properties: {label, required, assetSourceId, instructions}} = this.props;

    let badges;
    if (!assetSourceId) {
      badges = <Badge label="No Asset Source" type={Badge.WARNING} />;
    }

    return (
      <div>
        <Label label={label} isRequired={required}>{badges}</Label>
        <Instructions instructions={instructions}/>
        <input type={this.getType()} disabled={true} readOnly={true} />
      </div>
    );
  }
}
