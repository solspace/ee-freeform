/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
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
    const {layout, currentPageIndex, onTabClick, onNewTab, tabCount} = this.props;
    const {formPropCleanup} = this.context;

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

        {!formPropCleanup && tabCount < MAX_TABS && (
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
