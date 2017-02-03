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
import Label from "./Components/Label";
import Badge from "./Components/Badge";

export default class Html extends Component {
  static propTypes = {
    properties: PropTypes.shape({
      type: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      value: PropTypes.string.isRequired,
    }).isRequired,
  };

  render() {
    const {properties: {label, value}} = this.props;

    return (
      <div>
        <Label type="html">
          <Badge label="HTML" type={Badge.TEMPLATE} />
        </Label>
        <div className="composer-html-content" dangerouslySetInnerHTML={{__html: value}}></div>
      </div>
    )
  }
}
