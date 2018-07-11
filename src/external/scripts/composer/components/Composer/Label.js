/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import PropTypes            from "prop-types";
import React, { Component } from "react";

export default class Label extends Component {
  static propTypes = {
    fieldId: PropTypes.number.isRequired,
    label: PropTypes.string.isRequired,
  };

  render() {
    const { fieldId, label } = this.props;

    return (
      <label for={"composer-input-" + fieldId}>
        {label}
      </label>
    );
  }
}
