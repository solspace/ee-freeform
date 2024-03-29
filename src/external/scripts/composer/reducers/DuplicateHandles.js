/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import { UPDATE_DUPLICATE_HANDLE_LIST } from "../constants/ActionTypes";

export const duplicateHandles = (state = [], action) => {
  switch (action.type) {
    case UPDATE_DUPLICATE_HANDLE_LIST:
      return action.duplicateHandles;

    default:
      return state;
  }
};
