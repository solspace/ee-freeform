/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
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
