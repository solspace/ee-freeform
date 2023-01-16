/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
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
