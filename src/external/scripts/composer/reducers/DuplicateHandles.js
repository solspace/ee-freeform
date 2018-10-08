/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
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
