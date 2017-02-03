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
