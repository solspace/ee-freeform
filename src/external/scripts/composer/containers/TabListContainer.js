/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React from "react";
import {connect} from "react-redux";
import TabList from "../components/Composer/TabList";
import {switchPage, addPage} from "../actions/Actions";

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
    }
  })
)(TabList);
