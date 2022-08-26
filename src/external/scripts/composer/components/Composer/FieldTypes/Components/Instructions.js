/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React, { Component } from "react";

export default class Instructions extends Component {
  static propTypes = {
    instructions: PropTypes.string,
  };

  render() {
    const { instructions } = this.props;

    if (!instructions) {
      return null;
    }

    return (
      <div className="composer-column-instructions">
        {instructions}
      </div>
    );
  }
}
