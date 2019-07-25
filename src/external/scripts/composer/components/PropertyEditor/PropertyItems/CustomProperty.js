/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

import PropTypes from "prop-types";
import React from "react";
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
