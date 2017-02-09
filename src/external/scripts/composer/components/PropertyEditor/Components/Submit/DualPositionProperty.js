/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
import CustomProperty from "../../PropertyItems/CustomProperty";
import * as SubmitPositions from "../../../../constants/SubmitPositions";

export default class DualPositionProperty extends Component {
  static propTypes = {
    position: PropTypes.string.isRequired,
    onChangeHandler: PropTypes.func.isRequired,
  };

  render() {
    let {position, onChangeHandler} = this.props;

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
