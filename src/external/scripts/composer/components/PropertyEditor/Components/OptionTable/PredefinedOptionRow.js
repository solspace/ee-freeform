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
import { connect } from "react-redux";
import { insertValue, removeValue } from "../../../../actions/Actions";

class PredefinedOptionRow extends Component {
  static propTypes = {
    hash: PropTypes.string.isRequired,
    label: PropTypes.node.isRequired,
    value: PropTypes.node.isRequired,
    index: PropTypes.number.isRequired,
    isChecked: PropTypes.bool,
    insertValue: PropTypes.func.isRequired,
    removeValue: PropTypes.func.isRequired,
  };

  render() {
    const { label, value, isChecked } = this.props;

    return (
      <tr>
        <td>
          <input
            type="text"
            aria-label={`Option ${this.props.index + 1} label`}
            value={label}
            ref="label"
            data-type="label"
            readOnly={true}
          />
        </td>
        <td>
          <input
            type="text"
            aria-label={`Option ${this.props.index + 1} value`}
            value={value}
            data-type="value"
            ref="value"
            className="code"
            readOnly={true}
          />
        </td>
        <td className="composer-option-row-checkbox">
          <input
            type="checkbox"
            aria-label={`Select option ${this.props.index + 1} by default`}
            checked={isChecked}
            onChange={this.updateIsChecked}
          />
        </td>
        <td>&nbsp;</td>
      </tr>
    );
  }

  updateIsChecked = (event) => {
    const { hash, value, insertValue, removeValue } = this.props;
    const { target: { checked } } = event;

    if (checked) {
      insertValue(hash, value);
    } else {
      removeValue(hash, value);
    }
  }
}

export default connect(
  null,
  (dispatch) => ({
    insertValue: (hash, value) => dispatch(insertValue(hash, value)),
    removeValue: (hash, value) => dispatch(removeValue(hash, value)),
  })
)(PredefinedOptionRow);
