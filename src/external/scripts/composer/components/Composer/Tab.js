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
import { DragSource, DropTarget } from "react-dnd";
import { findDOMNode } from "react-dom";
import { connect } from "react-redux";
import { addColumnToNewRow, clearPlaceholders, removePage, switchHash, switchPage } from "../../actions/Actions";
import { placeholderPage, swapPage } from "../../actions/PageDragDrop";
import { COLUMN, PAGE } from "../../constants/DraggableTypes";
import ConfirmPageRemoval from "./ConfirmPageRemoval";

const passablePageDragOffset = 15;

const pageSource = {
  beginDrag(props) {
    return {
      type: PAGE,
      index: props.index,
    };
  },
  endDrag(props) {
    props.clearPlaceholders();
  },
};

const pageTarget = {
  hover(props, monitor, component) {
    const item = monitor.getItem();

    switch (item.type) {
      case PAGE:
        if (item.index === props.index) {
          return false;
        }

        const box = findDOMNode(component).getBoundingClientRect();
        const mouse = monitor.getClientOffset();

        if (item.index < props.index && (mouse.x - passablePageDragOffset) < box.x) {
          return false;
        }

        if (item.index > props.index && (box.x + box.width) < (mouse.x + passablePageDragOffset)) {
          return false;
        }

        const newIndex = item.index;
        const oldIndex = props.index;

        item.index = props.index;
        props.swapPage(newIndex, oldIndex);

        return;

      case COLUMN:
        const { index } = props;

        if (index === props.placeholderPageIndex) {
          return false;
        }

        props.placeholderPage(index);

        return;
    }
  },
  drop(props, monitor) {
    const item = monitor.getItem();

    switch (item.type) {
      case COLUMN:
        return props.columnToNewRow(0, item.hash, null, props.index, item.pageIndex);
    }
  },
};

class Tab extends Component {
  static propTypes = {
    index: PropTypes.number.isRequired,
    isSelected: PropTypes.bool.isRequired,
    placeholderPageIndex: PropTypes.number,
    label: PropTypes.string,
    onClick: PropTypes.func.isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.tabClickHandler = this.tabClickHandler.bind(this);
    this.removePageHandler = this.removePageHandler.bind(this);
    this.cancelRemoval = this.cancelRemoval.bind(this);
    this.confirmRemoval = this.confirmRemoval.bind(this);
    this.state = { confirmingRemoval: false };
  }

  render() {
    const { index, isSelected, placeholderPageIndex, label, layout } = this.props;
    const { connectDragSource, connectDropTarget } = this.props;
    const pageCount = layout.length;

    const classNames = ["tab-bar__tab"];
    if (isSelected) {
      classNames.push("active");
    }

    if (index === placeholderPageIndex) {
      classNames.push("has-hover");
    }

    return connectDropTarget(
      connectDragSource(
        <li className={classNames.join(" ")}>
          <button type="button" className="composer-page-button"
                  title={label || `Page ${index + 1}`}
                  aria-current={isSelected ? "page" : undefined}
                  onClick={this.tabClickHandler}>
            {label || `Page ${index + 1}`}
          </button>

          <ul className={`composer-actions composer-page-actions${isSelected && pageCount > 1 ? " is-visible" : ""}`}
              aria-hidden={!isSelected || pageCount < 2}>
            <li>
              {isSelected && pageCount > 1 &&
                <button type="button" className="composer-action-remove"
                          ref={button => { this.removeButton = button; }}
                          aria-label={`Remove ${label || `Page ${index + 1}`}`}
                          onClick={this.removePageHandler} />
              }
            </li>
          </ul>
          {this.state.confirmingRemoval &&
            <ConfirmPageRemoval pageLabel={label || `Page ${index + 1}`}
                                onCancel={this.cancelRemoval}
                                onConfirm={this.confirmRemoval} />
          }
        </li>
      )
    );
  }

  tabClickHandler(event) {
    this.props.onClick();
  }

  removePageHandler(event) {
    event.preventDefault();
    event.stopPropagation();
    this.setState({ confirmingRemoval: true });
  }

  cancelRemoval() {
    this.setState({ confirmingRemoval: false }, () => {
      if (this.removeButton) {
        this.removeButton.focus();
      }
    });
  }

  confirmRemoval() {
    this.setState({ confirmingRemoval: false });
    this.props.removePage(this.props.index);
    requestAnimationFrame(() => {
      const selectedPage = document.querySelector('.composer-page-button[aria-current="page"]');
      if (selectedPage) {
        selectedPage.focus();
      }
    });
  }
}

export default connect(
  state => ({
    layout: state.composer.layout,
    placeholderPageIndex: state.placeholders.pageIndex,
  }),
  dispatch => ({
    removePage: (pageIndex) => {
      dispatch(removePage(pageIndex));
      dispatch(switchHash("form"));
      dispatch(switchPage(0));
    },
    clearPlaceholders: () => dispatch(clearPlaceholders()),
    swapPage: (newIndex, oldIndex) => dispatch(swapPage(newIndex, oldIndex)),
    placeholderPage: (pageIndex) => dispatch(placeholderPage(pageIndex)),
    columnToNewRow: (rowIndex, hash, properties, pageIndex, prevPageIndex) => dispatch(
      addColumnToNewRow(rowIndex, hash, properties, pageIndex, prevPageIndex)
    ),
  }),
)(DropTarget(
  [PAGE, COLUMN],
  pageTarget,
  (connect) => ({ connectDropTarget: connect.dropTarget() })
)(DragSource(PAGE, pageSource, (connect, monitor) => ({
  connectDragSource: connect.dragSource(),
  connectDragPreview: connect.dragPreview(),
  isDragging: monitor.isDragging(),
}))(Tab)));
