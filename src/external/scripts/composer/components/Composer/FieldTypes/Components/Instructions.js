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

export default class Instructions extends Component {
  static propTypes = {
    instructions: PropTypes.string,
  };

  render() {
    const {instructions} = this.props;

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
