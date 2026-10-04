/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @license       https://docs.solspace.com/license-agreement/
 */

import React from "react";
import BasePropertyItem from "./BasePropertyItem";

// Preserve the existing newline-separated composer property. Accept ordinary
// unquoted email characters; the mailer validates the complete address too.
const cleanAddress = value => value.replace(/[^A-Za-z0-9.!#$%&'*+/=?^_`{|}~@-]/g, "");
const looksLikeEmail = value => /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(value);

export default class EmailRecipientsProperty extends BasePropertyItem {
  constructor(props, context) {
    super(props, context);
    this.state = { touched: {} };
    this.inputs = [];
    this.focusRow = null;
  }

  componentDidUpdate() {
    if (this.focusRow !== null) {
      const input = this.inputs[this.focusRow];
      this.focusRow = null;
      if (input) input.focus();
    }
  }

  getRows() {
    return String(this.props.value || "").split(/\r\n|\r|\n/);
  }

  saveRows(rows) {
    this.props.onChangeHandler({
      target: { name: this.props.name, value: rows.join("\n"), type: "text", dataset: {} },
    });
  }

  changeRow(index, value) {
    const rows = this.getRows();
    rows[index] = cleanAddress(value);
    this.saveRows(rows);
  }

  addRow(index, event) {
    if (event.nativeEvent && event.nativeEvent.isComposing) return;
    event.preventDefault();
    const rows = this.getRows();
    rows.splice(index + 1, 0, "");
    this.focusRow = index + 1;
    this.saveRows(rows);
  }

  removeRow(index) {
    const rows = this.getRows();
    rows.splice(index, 1);
    this.focusRow = Math.max(0, index - 1);
    this.setState(({ touched }) => {
      const shifted = {};
      Object.keys(touched).forEach(key => {
        const row = Number(key);
        if (row < index) shifted[row] = touched[key];
        if (row > index) shifted[row - 1] = touched[key];
      });
      return { touched: shifted };
    });
    this.saveRows(rows.length ? rows : [""]);
  }

  pasteRows(index, event) {
    const text = event.clipboardData && event.clipboardData.getData("text");
    const rows = this.getRows();
    if (!text || !/[\r\n,;]/.test(text)) return;

    const pasted = text.split(/[\r\n,;]+/).map(cleanAddress).filter(Boolean);
    if (!pasted.length) return;
    event.preventDefault();
    rows.splice(index, 1, ...pasted);
    this.focusRow = index + pasted.length - 1;
    this.saveRows(rows);
  }

  renderInput() {
    const rows = this.getRows();
    this.inputs = [];

    return (
      <div className="composer-email-recipient-list">
        {rows.map((address, index) => {
          const id = index ? `${this.inputId}-${index}` : this.inputId;
          const errorId = `${id}-error`;
          const invalid = !!address && !!this.state.touched[index] && !looksLikeEmail(address);

          return (
            <div className="composer-email-recipient-row" key={index}>
              <div className="composer-email-recipient-field">
                <input
                  ref={input => { this.inputs[index] = input; }}
                  id={id}
                  type="email"
                  name={this.props.name}
                  value={address}
                  aria-label={`Admin recipient ${index + 1}`}
                  aria-describedby={invalid ? `${this.hintId} ${errorId}` : this.hintId}
                  aria-invalid={invalid}
                  autoComplete="off"
                  autoCapitalize="off"
                  autoCorrect="off"
                  spellCheck={false}
                  placeholder="name@example.com"
                  onChange={event => this.changeRow(index, event.target.value)}
                  onKeyDown={event => { if (event.key === "Enter") this.addRow(index, event); }}
                  onPaste={event => this.pasteRows(index, event)}
                  onBlur={() => this.setState(({ touched }) => ({ touched: { ...touched, [index]: true } }))}
                />
                {invalid && <span id={errorId} className="composer-email-recipient-error">Enter a valid email address.</span>}
              </div>
              {rows.length > 1 &&
                <button
                  type="button"
                  className="composer-email-recipient-remove"
                  aria-label={`Remove admin recipient ${index + 1}`}
                  title="Remove recipient"
                  onClick={() => this.removeRow(index)}
                >×</button>
              }
            </div>
          );
        })}
      </div>
    );
  }
}
