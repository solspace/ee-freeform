/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
import HtmlInput from "./HtmlInput";
import * as FieldTypes from "../../../constants/FieldTypes";
import Label from "./Components/Label";
import Instructions from "./Components/Instructions";
import Badge from "./Components/Badge";

export default class Email extends HtmlInput {
  static propTypes = {
    ...HtmlInput.propTypes,
    notificationId: PropTypes.number,
  };

  getClassName() {
    return 'Email';
  }

  getType() {
    return FieldTypes.EMAIL;
  }

  getBadges() {
    const {properties: {notificationId}} = this.props;

    if (!notificationId) {
      return <Badge label="No Template" />;
    }
  }
}
