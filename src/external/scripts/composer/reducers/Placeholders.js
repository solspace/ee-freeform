/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import * as ActionTypes from "../constants/ActionTypes";
import {ROW, COLUMN} from "../constants/DraggableTypes";

const initialState = {
  type: null,
  rowIndex: null,
  index: null,
  targetHash: null,
};

export function placeholders(state = initialState, action) {
  switch (action.type) {
    case ActionTypes.ADD_PLACEHOLDER_ROW:
      return {
        type: ROW,
        rowIndex: action.rowIndex,
        targetHash: action.targetHash,
      };

    case ActionTypes.ADD_PLACEHOLDER_COLUMN:
      return {
        type: COLUMN,
        rowIndex: action.rowIndex,
        index: action.index,
        targetHash: action.targetHash,
      };

    case ActionTypes.CLEAR_PLACEHOLDERS:
      return {...initialState};

    default:
      return state;
  }
}
