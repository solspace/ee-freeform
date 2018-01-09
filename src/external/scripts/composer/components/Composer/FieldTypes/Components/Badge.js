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

export const INFO       = "info";
export const WARNING    = "warning";
export const ATTRIBUTE  = "attribute";
export const TEMPLATE   = "template";
export const VISIBILITY = "visibility";

export default class Badge extends Component {
  static INFO       = INFO;
  static WARNING    = WARNING;
  static ATTRIBUTE  = ATTRIBUTE;
  static TEMPLATE   = TEMPLATE;
  static VISIBILITY = VISIBILITY;

  static propTypes = {
    label: PropTypes.string.isRequired,
    type: PropTypes.oneOf([INFO, WARNING, ATTRIBUTE, TEMPLATE, VISIBILITY]),
  };

  render() {
    const {label} = this.props;
    let {type}    = this.props;

    if (!type) {
      type = INFO;
    }

    const classes = ["composer-label-badge", "composer-label-badge-" + type];

    return (
      <div className={classes.join(" ")}>
        {label}
      </div>
    )
  }
}
