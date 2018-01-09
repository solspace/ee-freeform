/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
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

  getClassName() {
    return 'File';
  }

  getType() {
    return FILE;
  }

  getBadges() {
    const {properties: {assetSourceId}} = this.props;

    if (!assetSourceId) {
      return <Badge label="No Asset Source" type={Badge.WARNING} />;
    }
  }
}
