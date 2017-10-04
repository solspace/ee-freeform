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
import {TEXT} from "../../../constants/FieldTypes";
import Badge from "./Components/Badge";
import Label from "./Components/Label";
import HtmlInput from "./HtmlInput";

export default class Hidden extends HtmlInput {
  getClassName() {
    return 'Hidden';
  }

  getBadges() {
    return <Badge label="Hidden field" type={Badge.VISIBILITY}/>;
  }
}
