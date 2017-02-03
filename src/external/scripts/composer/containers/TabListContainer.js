/*
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
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
