/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React, { Component } from "react";
import Tab from "./Tab";

const MAX_TABS = 100;

export default class TabList extends Component {
  static propTypes = {
    layout: PropTypes.array.isRequired,
    properties: PropTypes.object.isRequired,
    currentPageIndex: PropTypes.number.isRequired,
    onTabClick: PropTypes.func.isRequired,
    onNewTab: PropTypes.func.isRequired,
    tabCount: PropTypes.number.isRequired,
  };

  static contextTypes = {
    formPropCleanup: PropTypes.bool.isRequired,
  };

  render() {
    const { layout, currentPageIndex, onTabClick, onNewTab, tabCount } = this.props;
    const { formPropCleanup } = this.context;

    return (
      <nav className="tab-list-wrapper tab-bar" aria-label="Form pages">
        <ul className="tab-bar__tabs">
          {layout.map(
            (row, index) => (
              <Tab
                key={index}
                index={index}
                label={this.getLabel(index)}
                onClick={() => onTabClick(index)}
                isSelected={index == currentPageIndex}
              />
            ),
          )}
        </ul>

        {!formPropCleanup && tabCount < MAX_TABS && (
          <div className="tab-list-controls">
            <button type="button" className="button button--secondary composer-add-page-button" onClick={() => onNewTab(layout.length)}>Add Page</button>
          </div>
        )}
      </nav>
    );
  }

  getLabel(pageIndex) {
    const { properties } = this.props;

    if (properties["page" + pageIndex]) {
      return properties["page" + pageIndex].label;
    }

    return null;
  }
}
