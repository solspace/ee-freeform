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
import PropTypes from 'prop-types';
import {connect} from "react-redux";
import {DropTarget} from "react-dnd";
import {FIELD, COLUMN, ROW} from "../constants/DraggableTypes";
import Row from "../components/Composer/Row";
import {
  addFieldToNewRow,
  addColumnToRow,
  repositionColumn,
  addColumnToNewRow,
  addPlaceholderRow,
  addPlaceholderColumn,
  clearPlaceholders
} from "../actions/Actions";
import PlaceholderRow from "../components/Composer/Placeholders/PlaceholderRow";
import {hashFromTime} from "../helpers/Utilities";
import TabListContainer from "./TabListContainer";

const composerTarget = {
  hover(props, monitor) {
    if (!monitor.isOver({shallow: true})) {
      return;
    }

    if (props.placeholders.type === ROW && props.placeholders.rowIndex === -1) {
      return;
    }

    props.addRowPlaceholder(-1, monitor.getItem().hash);
  },
  drop(props, monitor) {
    const hasDroppedOnChild = monitor.didDrop();
    if (hasDroppedOnChild) {
      return;
    }

    const {hash, properties} = monitor.getItem();

    if (hash && !properties) {
      props.columnToNewRow(-1, hash, properties, props.pageIndex);
      return;
    }

    props.addFieldToNewRow(hash, properties, props.pageIndex);
  }
};


@connect(
  state => ({
    layout: state.composer.layout,
    properties: state.composer.properties,
    pageIndex: state.context.page,
    placeholders: state.placeholders,
  }),
  dispatch => ({
    addColumn: (rowIndex, columnIndex, hash, properties, pageIndex) => {
      if (!properties.id) {
        hash = hashFromTime();
      }

      dispatch(addColumnToRow(rowIndex, columnIndex, hash, properties, pageIndex));
    },
    moveColumn: (columnIndex, rowIndex, newColumnIndex, newRowIndex, pageIndex) => (
      dispatch(repositionColumn(columnIndex, rowIndex, newColumnIndex, newRowIndex, pageIndex))
    ),
    columnToNewRow: (rowIndex, hash, properties = null, pageIndex) => dispatch(addColumnToNewRow(rowIndex, hash, properties, pageIndex)),
    removeColumn: (columnIndex, rowIndex, pageIndex) => dispatch(removeColumn(columnIndex, rowIndex, pageIndex)),
    addRowPlaceholder: (index, hash) => dispatch(addPlaceholderRow(index, hash)),
    addColumnPlaceholder: (rowIndex, index, hash) => dispatch(addPlaceholderColumn(rowIndex, index, hash)),
    addFieldToNewRow: (hash, properties, pageIndex) => dispatch(addFieldToNewRow(hash, properties, pageIndex)),
    clearPlaceholders: () => dispatch(clearPlaceholders()),
  })
)
@DropTarget([FIELD, COLUMN], composerTarget, (connect) => ({connectDropTarget: connect.dropTarget()}))
export default class Composer extends Component {
  static propTypes = {
    layout: PropTypes.array.isRequired,
    pageIndex: PropTypes.number.isRequired,
    connectDropTarget: PropTypes.func.isRequired,
    addColumn: PropTypes.func.isRequired,
    moveColumn: PropTypes.func.isRequired,
    columnToNewRow: PropTypes.func.isRequired,
    addFieldToNewRow: PropTypes.func.isRequired,
    removeColumn: PropTypes.func.isRequired,
    addRowPlaceholder: PropTypes.func.isRequired,
    addColumnPlaceholder: PropTypes.func.isRequired,
    clearPlaceholders: PropTypes.func.isRequired,
    properties: PropTypes.object.isRequired,
    placeholders: PropTypes.object.isRequired,
  };

  static contextTypes = {
    store: PropTypes.object.isRequired
  };

  constructor(props, context) {
    super(props, context);

    this.moveColumn     = this.moveColumn.bind(this);
    this.addColumn      = this.addColumn.bind(this);
    this.columnToNewRow = this.columnToNewRow.bind(this);
    this.removeColumn   = this.removeColumn.bind(this);
  }

  render() {
    const {pageIndex, layout, connectDropTarget} = this.props;

    const rows = layout[pageIndex] ? layout[pageIndex] : [];

    const {type, rowIndex}      = this.props.placeholders;
    const shouldShowPlaceholder = type === ROW && rowIndex === -1;

    return connectDropTarget(
      <div style={{minHeight: 600}}>
        <div className="tabs">
          <TabListContainer />
        </div>

        <div className="layout">
          {rows.map(
            (row, index) => (
              <Row
                key={row.id}
                properties={this.props.properties}
                index={index}
                columns={row.columns}
                moveColumn={this.moveColumn}
                addColumn={this.addColumn}
                columnToNewRow={this.columnToNewRow}
                addRowPlaceholder={this.props.addRowPlaceholder}
                addColumnPlaceholder={this.props.addColumnPlaceholder}
                clearPlaceholders={this.props.clearPlaceholders}
              />
            )
          )}

          <PlaceholderRow active={shouldShowPlaceholder}/>
        </div>
      </div>
    );
  }

  addColumn(rowIndex, columnIndex, hash, properties) {
    this.props.addColumn(rowIndex, columnIndex, hash, properties, this.props.pageIndex);
  }

  moveColumn(columnIndex, rowIndex, newColumnIndex, newRowIndex) {
    this.props.moveColumn(columnIndex, rowIndex, newColumnIndex, newRowIndex, this.props.pageIndex);
  }

  columnToNewRow(rowIndex, hash, properties = null) {
    this.props.columnToNewRow(rowIndex, hash, properties, this.props.pageIndex);
  }

  removeColumn(columnIndex, rowIndex) {
    this.props.removeColumn(columnIndex, rowIndex, this.props.pageIndex);
  }
}
