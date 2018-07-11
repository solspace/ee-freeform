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

export const swapPage = (newIndex, oldIndex) => ({
  type: ActionTypes.SWAP_PAGE,
  newIndex,
  oldIndex,
  properties: "pageswap",
});

export const placeholderPage = (pageIndex) => ({
  type: ActionTypes.ADD_PLACEHOLDER_PAGE,
  pageIndex,
});
