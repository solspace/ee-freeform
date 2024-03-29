/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import React from "react";
import { connect } from "react-redux";
import { addPage, switchPage } from "../actions/Actions";
import TabList from "../components/Composer/TabList";

export default connect(
  (state) => ({
    layout: state.composer.layout,
    properties: state.composer.properties,
    currentPageIndex: state.context.page,
    tabCount: state.composer.layout.length,
  }),
  (dispatch) => ({
    onTabClick: (index) => dispatch(switchPage(index)),
    onNewTab: (index) => {
      dispatch(addPage(index));
      dispatch(switchPage(index));
    },
  }),
)(TabList);
