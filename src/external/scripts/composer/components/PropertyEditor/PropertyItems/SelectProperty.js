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

export default class SelectProperty extends BasePropertyItem {
  static propTypes = {
    ...BasePropertyItem.propTypes,
    emptyOption: PropTypes.string,
    options: PropTypes.arrayOf(
      PropTypes.shape({
        key: PropTypes.node.isRequired,
        value: PropTypes.node.isRequired,
      })
    ),
    optionGroups: PropTypes.arrayOf(
      PropTypes.shape({
        label: PropTypes.string.isRequired,
        options: PropTypes.arrayOf(
          PropTypes.shape({
            key: PropTypes.node.isRequired,
            value: PropTypes.node.isRequired,
          })
        ).isRequired,
      })
    ),
  };

  renderInput() {
    const {name, readOnly, disabled, onChangeHandler, value, className, isNumeric, couldBeNumeric, emptyOption, options, optionGroups} = this.props;

    return (
      <div className="select">
        <select
          className={className}
          name={name}
          value={value}
          readOnly={readOnly}
          disabled={disabled}
          data-is-numeric={!!isNumeric}
          data-could-be-numeric={!!couldBeNumeric}
          onChange={onChangeHandler}
        >
          {emptyOption && <option value={isNumeric ? 0 : ""}>{emptyOption}</option>}
          {options && options.map((item, i) => (
            <option
              key={item.key}
              value={item.key}>
              {item.value}
            </option>
          ))}
          {optionGroups && optionGroups.map((item, i) => (
            <optgroup label={item.label} key={i}>
              {item.options.map((opt, j) => (
                <option
                  key={j}
                  value={opt.key}>
                  {opt.value}
                </option>
              ))}
            </optgroup>
          ))}
        </select>
      </div>
    );
  }
}
