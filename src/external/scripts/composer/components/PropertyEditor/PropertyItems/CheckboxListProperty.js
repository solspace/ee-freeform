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
import BasePropertyItem from "./BasePropertyItem";

export default class CheckboxListProperty extends BasePropertyItem {
  static propTypes = {
    ...BasePropertyItem.propTypes,
    isNumeric: PropTypes.bool,
    options: PropTypes.arrayOf(
      PropTypes.shape({
        key: PropTypes.node.isRequired,
        value: PropTypes.node.isRequired,
      })
    ),
    values: PropTypes.array,
    updateField: PropTypes.func.isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.handleUpdate = this.handleUpdate.bind(this);
  }

  renderInput() {
    const {name, readOnly, disabled, values, className, isNumeric, options} = this.props;

    const classNames = ["composer-property-checkbox-list"];
    if (className) {
      classNames.push(className);
    }

    return (
      <div className={classNames.join(" ")} ref="container">
        {options.map(item => (
          <label key={item.key}>
            <input
              name={name}
              type="checkbox"
              value={item.key}
              disabled={!!disabled}
              readOnly={!!readOnly}
              onChange={this.handleUpdate}
              data-is-numeric={!!isNumeric}
              checked={values && values.indexOf(item.key) !== -1}
            />
            {item.value}
          </label>
        ))}
      </div>
    );
  }

  handleUpdate() {
    const {updateField, name} = this.props;
    const checkboxList = this.refs.container.querySelectorAll("input");

    const values = [];
    for (let i = 0; i < checkboxList.length; i++) {
      const checkbox = checkboxList[i];

      if (!checkbox.checked) continue;

      values.push(checkbox.value);
    }

    updateField({[name]: values});
  }
}
