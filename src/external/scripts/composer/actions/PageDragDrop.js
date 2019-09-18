/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
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
