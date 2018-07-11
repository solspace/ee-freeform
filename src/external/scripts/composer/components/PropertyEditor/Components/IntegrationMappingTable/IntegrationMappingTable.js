/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import PropTypes            from "prop-types";
import React, { Component } from "react";
import MappingRow           from "./MappingRow";

export default class IntegrationMappingTable extends Component {
  static propTypes = {
    formFields: PropTypes.arrayOf(
      PropTypes.shape({
        handle: PropTypes.string.isRequired,
        label: PropTypes.string.isRequired,
      }).isRequired,
    ).isRequired,
    fields: PropTypes.array.isRequired,
    mapping: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
    ]),
  };

  static contextTypes = {
    updateField: PropTypes.func.isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.updateMappings = this.updateMappings.bind(this);
  }

  render() {
    return (
      <div className="composer-option-table">
        <table>
          <thead>
          <tr>
            <th>CRM Field</th>
            <th>FF Field</th>
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
    const { fields, mapping, formFields } = this.props;

    const children = [];
    fields.map((field, i) => {
      children.push(
        <MappingRow
          key={i}
          handle={field.handle}
          label={field.label}
          required={field.required}
          formFields={formFields}
          mappedFormField={mapping && mapping[field.handle] ? mapping[field.handle] : ""}
          onChangeHandler={this.updateMappings}
        />,
      );
    });

    return children;
  }

  updateMappings() {
    const { updateField } = this.context;
    const selectList      = this.refs.items.querySelectorAll("select");

    const mapping = {};
    for (let i = 0; i < selectList.length; i++) {
      const select = selectList[i];

      if (!select.value) {
        continue;
      }

      mapping[select.name] = select.value;
    }

    updateField({ mapping: mapping });
  }
}
