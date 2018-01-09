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
import BasePropertyItem from "./BasePropertyItem";

export default class CustomProperty extends BasePropertyItem {
  static propTypes = {
    ...BasePropertyItem.propTypes,
    content: PropTypes.element,
  };

  renderInput() {
    return this.props.content;
  }
}
