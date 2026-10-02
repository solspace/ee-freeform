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

export default class MappingRow extends Component {
  static propTypes = {
    handle: PropTypes.string.isRequired,
    label: PropTypes.string.isRequired,
    required: PropTypes.bool.isRequired,
    formFields: PropTypes.arrayOf(
      PropTypes.shape({
        handle: PropTypes.string.isRequired,
        label: PropTypes.string.isRequired,
      }).isRequired,
    ).isRequired,
    mappedFormField: PropTypes.string,
    onChangeHandler: PropTypes.func.isRequired,
  };

  render() {
    const { handle, label, required, formFields, mappedFormField, onChangeHandler } = this.props;
    const inputId = `composer-mapping-${handle}`;
    const selectedField = formFields.find(field => field.handle === mappedFormField);

    return (
      <tr>
        <td className="read-only code">
          <label htmlFor={inputId} className={required ? "required" : ""}>
            {label}
          </label>
        </td>
        <td>
          <div className="select">
            <select id={inputId} name={handle} value={mappedFormField} title={selectedField && selectedField.label} onChange={onChangeHandler}>
              <option key="--" value="">--</option>
              {formFields.map((item, i) => (
                <option key={item.handle} value={item.handle}>{item.label}</option>
              ))}
            </select>
          </div>
        </td>
      </tr>
    );
  }
}
