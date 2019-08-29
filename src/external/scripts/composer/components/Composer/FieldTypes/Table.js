/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

import React from "react";
import { TYPE_CHECKBOX, TYPE_SELECT, TYPE_STRING } from "../../PropertyEditor/PropertyItems/Table/Column";
import HtmlInput from "./HtmlInput";

export default class Table extends HtmlInput {
  getClassName() {
    return "Table";
  }

  renderTable() {
    const { properties: { layout } } = this.props;

    if (!layout) {
      return <table>
        <thead>
        <tr>
          <th>Empty Table</th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <td>---</td>
        </tr>
        </tbody>
      </table>
    }

    return (
      <table>
        <thead>
        <tr>
          {layout.map(({ label }, i) => (
            <th key={`${i}th`}>{label}</th>
          ))}
        </tr>
        </thead>
        <tbody>
        <tr>
          {layout.map(({ value, type }, i) => {
            let input;
            switch (type) {
              case TYPE_CHECKBOX:
                input = <input type="checkbox" value={value} readOnly />
                break;

              case TYPE_SELECT:
                let options = [];
                if (value) {
                  options = value.split(';')
                }

                input = (
                  <select>
                    {options.map((item, j) => <option key={j} value={item}>{item}</option>)}
                  </select>
                )
                break;

              case TYPE_STRING:
              default:
                input = <input type="text" value={value ? value : ''} readOnly />
                break;
            }

            return (
              <td key={i}>{input}</td>
            )
          })}
        </tr>
        </tbody>
      </table>
    );
  }

  renderInput = () => <div className="table">{this.renderTable()}</div>
}
