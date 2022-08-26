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
import React from "react";
import { RADIO } from "../../../../constants/FieldTypes";
import HtmlInput from "../HtmlInput";

export default class Radio extends HtmlInput {
  static propTypes = {
    label: PropTypes.string.isRequired,
    properties: PropTypes.object.isRequired,
    isChecked: PropTypes.bool.isRequired,
  };

  getType() {
    return RADIO;
  }

  render() {
    const { label, isChecked, value } = this.props;

    return (
      <div>
        <label>
          <input className="composer-ft-radio"
                 type={this.getType()}
                 value={value}
                 readOnly={true}
                 disabled={true}
                 checked={isChecked}
                 {...this.getCleanProperties()}
          />
          <span dangerouslySetInnerHTML={{ __html: label }} />
        </label>
      </div>
    );
  }
}
