/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import * as ActionTypes from "../constants/ActionTypes";

export const addConnection = () => ({
  type: ActionTypes.ADD_CONNECTION,
});

export const removeConnection = (index) => ({
  type: ActionTypes.REMOVE_CONNECTION,
  index,
});

export const updateConnection = (index, properties) => ({
  type: ActionTypes.UPDATE_CONNECTION,
  index,
  properties,
});
