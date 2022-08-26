/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import * as ActionTypes from "../constants/ActionTypes";
import { COLUMN, PAGE, ROW } from "../constants/DraggableTypes";

const initialState = {
  type: null,
  rowIndex: null,
  index: null,
  targetHash: null,
  pageIndex: null,
};

export function placeholders(state = initialState, action) {
  switch (action.type) {
    case ActionTypes.ADD_PLACEHOLDER_ROW:
      return {
        ...initialState,
        type: ROW,
        rowIndex: action.rowIndex,
        targetHash: action.targetHash,
      };

    case ActionTypes.ADD_PLACEHOLDER_COLUMN:
      return {
        ...initialState,
        type: COLUMN,
        rowIndex: action.rowIndex,
        index: action.index,
        targetHash: action.targetHash,
      };

    case ActionTypes.ADD_PLACEHOLDER_PAGE:
      return {
        ...initialState,
        type: PAGE,
        pageIndex: action.pageIndex,
      };

    case ActionTypes.CLEAR_PLACEHOLDERS:
      return { ...initialState };

    default:
      return state;
  }
}
