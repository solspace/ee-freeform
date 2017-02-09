/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
import Tab from "./Tab";

const MAX_TABS = 9;

export default class TabList extends Component {
  static propTypes = {
    layout: PropTypes.array.isRequired,
    properties: PropTypes.object.isRequired,
    currentPageIndex: PropTypes.number.isRequired,
    onTabClick: PropTypes.func.isRequired,
    onNewTab: PropTypes.func.isRequired,
    tabCount: PropTypes.number.isRequired,
  };

  render() {
    const {layout, currentPageIndex, onTabClick, onNewTab, tabCount} = this.props;

    return (
      <div className="tab-list-wrapper">
        <ul>
          {layout.map(
            (row, index) => (
              <Tab
                key={index}
                index={index}
                label={this.getLabel(index)}
                onClick={() => onTabClick(index)}
                isSelected={index == currentPageIndex}
              />
            )
          )}
        </ul>

        {tabCount < MAX_TABS && (
          <div className="tab-list-controls">
            <a className="new" onClick={() => onNewTab(layout.length)}></a>
          </div>
        )}
      </div>
    )
  }

  getLabel(pageIndex) {
    const {properties} = this.props;

    if (properties["page" + pageIndex]) {
      return properties["page" + pageIndex].label;
    }

    return null;
  }
}
