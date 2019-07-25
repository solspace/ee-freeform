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
