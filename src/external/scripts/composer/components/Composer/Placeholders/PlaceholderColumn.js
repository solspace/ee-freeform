/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
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
