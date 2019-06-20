/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
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
