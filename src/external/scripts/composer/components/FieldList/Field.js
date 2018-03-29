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
import {FIELD} from "../../constants/DraggableTypes";
import {connect} from "react-redux";
import {DragSource} from "react-dnd";
import Badge from "../Composer/FieldTypes/Components/Badge";
import FieldHelper from "../../helpers/FieldHelper";
import * as FieldTypes from "../../constants/FieldTypes";
import PropertyHelper from "../../helpers/PropertyHelper";
import {clearPlaceholders} from "../../actions/Actions";

const fieldSource = {
  canDrag(props, monitor) {
    return !props.isUsed;
  },
  beginDrag(props) {
    let hash       = props.hash;
    let properties = PropertyHelper.getCleanProperties(props);

    if (!hash) {
      hash = FieldHelper.hashField(properties);
    }

    return {
      type: FIELD,
      hash: hash,
      properties: properties,
    };
  },
  endDrag(props) {
    props.clearPlaceholders();
  },
};


@connect(
  null,
  (dispatch) => ({
    clearPlaceholders: () => dispatch(clearPlaceholders()),
  })
)
@DragSource(FIELD, fieldSource, (connect, monitor) => ({
  connectDragSource: connect.dragSource(),
  isDragging: monitor.isDragging()
}))
export default class Field extends Component {
  static propTypes = {
    hash: PropTypes.string,
    type: PropTypes.string.isRequired,
    isUsed: PropTypes.bool.isRequired,
    label: PropTypes.string.isRequired,
    badge: PropTypes.string,
    connectDragSource: PropTypes.func.isRequired,
    clearPlaceholders: PropTypes.func.isRequired,
    isDragging: PropTypes.bool.isRequired,
  };

  render() {
    const {type, isUsed, label, onClick, connectDragSource, isDragging, badge} = this.props;

    if (isUsed) {
      return null;
    }

    const classList = ["icon-solspace-" + type];
    if (isDragging) {
      classList.push("is-dragging");
    }

    return connectDragSource(
      <li className={classList.join(" ")}
          disabled={isUsed}
          onClick={!isUsed ? onClick : null}>
        {label}
        {badge && <Badge label={badge} />}
      </li>
    );
  }
}
