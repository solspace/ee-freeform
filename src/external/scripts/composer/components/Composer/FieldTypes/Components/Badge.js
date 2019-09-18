/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React, { Component } from "react";

export const INFO = "info";
export const WARNING = "warning";
export const ATTRIBUTE = "attribute";
export const TEMPLATE = "template";
export const VISIBILITY = "visibility";
export const LOUDSPEAKER = "loudspeaker";

export default class Badge extends Component {
  static INFO = INFO;
  static WARNING = WARNING;
  static ATTRIBUTE = ATTRIBUTE;
  static TEMPLATE = TEMPLATE;
  static VISIBILITY = VISIBILITY;
  static LOUDSPEAKER = LOUDSPEAKER;

  static propTypes = {
    label: PropTypes.string.isRequired,
    type: PropTypes.oneOf([INFO, WARNING, ATTRIBUTE, TEMPLATE, VISIBILITY, LOUDSPEAKER]),
  };

  render() {
    const { label } = this.props;
    let { type } = this.props;

    if (!type) {
      type = INFO;
    }

    const classes = ["composer-label-badge", "composer-label-badge-" + type];

    return (
      <div className={classes.join(" ")}>
        {label}
      </div>
    );
  }
}
