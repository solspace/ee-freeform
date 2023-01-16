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
