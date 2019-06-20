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
import * as SubmitPositions from "../../../../constants/SubmitPositions";
import CustomProperty from "../../PropertyItems/CustomProperty";

export default class DualPositionProperty extends Component {
  static propTypes = {
    position: PropTypes.string.isRequired,
    onChangeHandler: PropTypes.func.isRequired,
  };

  render() {
    let { position, onChangeHandler } = this.props;

    return (
      <CustomProperty
        label="Positioning"
        instructions="Choose how the previous and submit buttons should be placed."
      >
        <div>
          <div>
            <label>
              <input
                type="radio"
                name="position"
                value={SubmitPositions.SPREAD}
                checked={position === SubmitPositions.SPREAD}
                onChange={onChangeHandler}
              />
              Apart at Left and Right
            </label>
          </div>
          <div>
            <label>
              <input
                type="radio"
                name="position"
                value={SubmitPositions.LEFT}
                checked={position === SubmitPositions.LEFT}
                onChange={onChangeHandler}
              />
              Together at Left
            </label>
          </div>
          <div>
            <label>
              <input
                type="radio"
                name="position"
                value={SubmitPositions.CENTER}
                checked={position === SubmitPositions.CENTER}
                onChange={onChangeHandler}
              />
              Together at Center
            </label>
          </div>
          <div>
            <label>
              <input
                type="radio"
                name="position"
                value={SubmitPositions.RIGHT}
                checked={position === SubmitPositions.RIGHT}
                onChange={onChangeHandler}
              />
              Together at Right
            </label>
          </div>
        </div>
      </CustomProperty>
    );
  }
}
