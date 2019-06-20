/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

import React from "react";
import { TEXT } from "../../../constants/FieldTypes";
import Badge from "./Components/Badge";
import Text from "./Text";

export default class Hidden extends Text {
  getClassName() {
    return "Hidden";
  }

  getType() {
    return TEXT;
  }

  getBadges() {
    const badges = super.getBadges();

    badges.push(<Badge key={"hidden"} label="Hidden field" type={Badge.VISIBILITY} />);

    return badges;
  }
}
