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
import Badge from "./Components/Badge";
import Label from "./Components/Label";

export default class Html extends Component {
  static propTypes = {
    properties: PropTypes.shape({
      type: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      value: PropTypes.string.isRequired,
    }).isRequired,
  };

  getClassName() {
    return "Html";
  }

  render() {
    const { properties: { label, value } } = this.props;

    return (
      <div>
        <Label type="html">
          <Badge label="HTML" type={Badge.TEMPLATE} />
        </Label>
        <div className="composer-html-content" dangerouslySetInnerHTML={{ __html: value }} />
      </div>
    );
  }
}
