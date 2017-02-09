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
import {removePage, switchHash, switchPage} from "../../actions/Actions";
import {connect} from "react-redux";

@connect(
  state => ({
    layout: state.composer.layout,
  }),
  dispatch => ({
    removePage: (pageIndex) => {
      dispatch(removePage(pageIndex));
      dispatch(switchHash("form"));
      dispatch(switchPage(0));
    },
  })
)
export default class Tab extends Component {
  static propTypes = {
    index: PropTypes.number.isRequired,
    isSelected: PropTypes.bool.isRequired,
    label: PropTypes.string,
    onClick: PropTypes.func.isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.tabClickHandler   = this.tabClickHandler.bind(this);
    this.removePageHandler = this.removePageHandler.bind(this);
  }

  render() {
    const {index, isSelected, label, layout} = this.props;
    const pageCount = layout.length;

    return (
      <li className={isSelected ? "active" : ""} onClick={this.tabClickHandler}>
        {label ? label : `Page ${index + 1}`}

        {isSelected && (pageCount > 1) ? (
          <ul className="composer-actions composer-page-actions">
            <li className="composer-action-remove" onClick={this.removePageHandler}></li>
          </ul>
        ) : ""}
      </li>
    );
  }

  tabClickHandler(event) {
    if (!event.target.className.match(/composer-action-remove/)) {
      this.props.onClick();
    }
  }

  removePageHandler(event) {
    const {index, removePage} = this.props;

    if (confirm("Are you sure you want to remove this page and all fields on it?")) {
      removePage(index);
    }

    event.preventDefault();
    return false;
  }
}
