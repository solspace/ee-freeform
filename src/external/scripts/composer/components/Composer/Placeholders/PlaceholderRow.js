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

export default class PlaceholderRow extends Component {
  static propTypes = {
    active: PropTypes.bool,
  };

  render() {
    const active = !!this.props.active;

    const classes = ["composer-row-placeholder"];
    if (active) {
      classes.push("active");
    }

    return (
      <div className={classes.join(" ")}>
        <div></div>
      </div>
    );
  }
}
