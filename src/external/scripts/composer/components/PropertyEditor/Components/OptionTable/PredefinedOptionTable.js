/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React, { Component } from "react";
import { connect } from "react-redux";
import PredefinedOptionRow from "./PredefinedOptionRow";

@connect(
  (state) => ({
    generatedOptions: state.generatedOptionLists.cache,
    properties: state.composer.properties,
  }),
  (dispatch) => ({}),
)
export default class PredefinedOptionTable extends Component {
  static propTypes = {
    values: PropTypes.array,
    value: PropTypes.node,
  };

  static contextTypes = {
    hash: PropTypes.string.isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.renderRows = this.renderRows.bind(this);
  }

  render() {
    return (
      <div className="composer-option-table">
        <table>
          <thead>
          <tr>
            <th>Label</th>
            <th colSpan={2}>Value</th>
          </tr>
          </thead>

          <tbody ref="items">
          {this.renderRows()}
          </tbody>
        </table>
      </div>
    );
  }

  /**
   * Render each ROW element
   *
   * @returns {Array}
   */
  renderRows() {
    const { options, values } = this.props;
    const { hash } = this.context;
    const children = [];

    if (!options) {
      return children;
    }

    for (let i = 0; i < options.length; i++) {
      const { label, value } = options[i];

      let isChecked = false;
      if (values) {
        isChecked = values.indexOf(value) !== -1;
      } else {
        isChecked = (value + "") === (this.props.value + "");
      }

      children.push(
        <PredefinedOptionRow
          key={i}
          hash={hash}
          label={label}
          value={value}
          index={i}
          isChecked={isChecked}
        />,
      );
    }

    return children;
  }
}
