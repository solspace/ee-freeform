/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
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
