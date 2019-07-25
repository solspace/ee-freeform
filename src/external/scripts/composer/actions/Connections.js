/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
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
