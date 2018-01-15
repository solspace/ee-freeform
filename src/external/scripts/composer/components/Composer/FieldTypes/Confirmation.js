/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import React from "react";
import PropTypes from "prop-types";
import Text from "./Text";
import * as FieldTypes from "../../../constants/FieldTypes";
import Badge from "./Components/Badge";

export default class Confirmation extends Text {
  static propTypes = {
    ...Text.propTypes,
    targetFieldHash: PropTypes.string,
  };

  getClassName() {
    return 'Confirmation';
  }

  getType() {
    return FieldTypes.TEXT;
  }

  getBadges() {
    const {properties: {targetFieldHash}} = this.props;

    if (!targetFieldHash) {
      return <Badge label="No Target Field" type={Badge.WARNING} />
    }
  }
}
