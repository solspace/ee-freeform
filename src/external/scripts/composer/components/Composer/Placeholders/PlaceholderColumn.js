/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

import React, { Component } from "react";

export default class PlaceholderColumn extends Component {
  render() {
    return (
      <div className="composer-column composer-column-placeholder">
        <div></div>
      </div>
    );
  }
}
