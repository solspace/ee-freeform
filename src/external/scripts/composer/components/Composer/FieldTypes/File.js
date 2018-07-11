/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import PropTypes from "prop-types";
import React from "react";
import { FILE } from "./../../../constants/FieldTypes";
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

  getClassName() {
    return "File";
  }

  getType() {
    return FILE;
  }

  getBadges() {
    const badges = super.getBadges();
    const { properties: { assetSourceId } } = this.props;

    if (!assetSourceId) {
      badges.push(<Badge key={"asset"} label="No Asset Source" type={Badge.WARNING} />);
    }

    return badges;
  }
}
