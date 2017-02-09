/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
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
