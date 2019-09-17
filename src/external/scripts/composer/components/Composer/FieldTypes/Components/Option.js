/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import HtmlInput from "../HtmlInput";

export default class Radio extends HtmlInput {
  static propTypes = {
    label: PropTypes.string.isRequired,
    properties: PropTypes.object.isRequired,
  };

  getType() {
    return "option";
  }

  render() {
    const { label, value } = this.props;

    return (
      <option value={value}>{label}</option>
    );
  }
}
