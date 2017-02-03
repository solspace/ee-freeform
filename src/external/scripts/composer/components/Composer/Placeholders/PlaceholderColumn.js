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

export default class PlaceholderColumn extends Component {
  render() {
    return (
      <div className="composer-column composer-column-placeholder">
        <div></div>
      </div>
    );
  }
}
