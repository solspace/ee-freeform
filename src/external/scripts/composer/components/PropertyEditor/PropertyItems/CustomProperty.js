/*
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
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
