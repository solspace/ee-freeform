/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import PropTypes       from "prop-types";
import React           from "react";
import * as FieldTypes from "../../../constants/FieldTypes";
import Badge           from "./Components/Badge";
import Text            from "./Text";

export default class Email extends Text {
  static propTypes = {
    ...Text.propTypes,
    notificationId: PropTypes.number,
  };

  getClassName() {
    return "Email";
  }

  getType() {
    return FieldTypes.EMAIL;
  }

  getBadges() {
    const badges                             = super.getBadges();
    const { properties: { notificationId } } = this.props;

    if (!notificationId) {
      badges.push(<Badge key={"template"} label="No Template" />);
    }

    return badges;
  }
}
