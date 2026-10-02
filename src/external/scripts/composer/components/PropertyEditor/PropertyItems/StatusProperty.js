/*
 * Freeform for ExpressionEngine
 *
 * @package Solspace:Freeform
 * @license https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import BasePropertyItem from "./BasePropertyItem";

// Match EE's Entry Status control while keeping the form's numeric status ID.
export default class StatusProperty extends BasePropertyItem {
  static propTypes = {
    ...BasePropertyItem.propTypes,
    statuses: PropTypes.arrayOf(PropTypes.shape({
      id: PropTypes.number.isRequired,
      name: PropTypes.string.isRequired,
      color: PropTypes.string,
    })).isRequired,
  };

  constructor(props, context) {
    super(props, context);
    this.state = { open: false };
    this.options = [];
  }

  componentDidMount() {
    document.addEventListener("mousedown", this.onDocumentMouseDown);
  }

  componentWillUnmount() {
    document.removeEventListener("mousedown", this.onDocumentMouseDown);
  }

  onDocumentMouseDown = event => {
    if (this.container && !this.container.contains(event.target)) {
      this.setState({ open: false });
    }
  };

  onBlur = event => {
    if (!event.relatedTarget || !event.currentTarget.contains(event.relatedTarget)) {
      this.setState({ open: false });
    }
  };

  focusOption = index => {
    this.setState({ open: true }, () => {
      if (this.options[index]) this.options[index].focus();
    });
  };

  selectStatus = status => {
    this.props.onChangeHandler(status.id);
    this.setState({ open: false }, () => this.trigger.focus());
  };

  onTriggerKeyDown = event => {
    const statuses = this.props.statuses;
    const selected = statuses.findIndex(status => String(status.id) === String(this.props.value));
    if (event.key === "ArrowDown" || event.key === "ArrowUp" || event.key === "Home" || event.key === "End") {
      event.preventDefault();
      const index = event.key === "Home" ? 0 : event.key === "End" ? statuses.length - 1 :
        event.key === "ArrowUp" ? Math.max(0, selected - 1) : Math.min(statuses.length - 1, selected + 1);
      this.focusOption(index);
    } else if (event.key === "Escape") {
      this.setState({ open: false });
    }
  };

  onOptionKeyDown = (event, index) => {
    const last = this.props.statuses.length - 1;
    if (event.key === "ArrowDown" || event.key === "ArrowUp" || event.key === "Home" || event.key === "End") {
      event.preventDefault();
      const next = event.key === "Home" ? 0 : event.key === "End" ? last :
        event.key === "ArrowUp" ? Math.max(0, index - 1) : Math.min(last, index + 1);
      this.options[next].focus();
    } else if (event.key === "Escape") {
      event.preventDefault();
      this.setState({ open: false }, () => this.trigger.focus());
    } else if (event.key === "Tab") {
      this.setState({ open: false });
    }
  };

  renderInput() {
    const { statuses, value, disabled, instructions } = this.props;
    const selected = statuses.find(status => String(status.id) === String(value));
    const colorFor = status => {
      const color = status.color || "";
      return /^(#[0-9a-f]{3,8}|[a-z]+)$/i.test(color) ? color : "var(--ee-text-secondary)";
    };

    return (
      <div className="composer-status-select" ref={node => this.container = node} onBlur={this.onBlur}>
        <button type="button" id={this.inputId} ref={node => this.trigger = node}
                className="composer-status-trigger" disabled={disabled || !statuses.length}
                aria-haspopup="listbox" aria-expanded={this.state.open}
                aria-controls={`${this.inputId}-options`}
                aria-labelledby={this.labelId} aria-describedby={instructions ? this.hintId : undefined}
                onKeyDown={this.onTriggerKeyDown}
                onClick={() => this.setState({ open: !this.state.open })}>
          {selected && <span className="composer-status-tag" style={{ color: colorFor(selected), borderColor: colorFor(selected) }}>{selected.name}</span>}
          <span className="composer-status-caret" aria-hidden="true" />
        </button>
        {this.state.open &&
          <div className="composer-status-options" id={`${this.inputId}-options`} role="listbox" aria-labelledby={this.labelId}>
            {statuses.map((status, index) => (
              <button type="button" key={status.id} role="option" aria-selected={String(status.id) === String(value)}
                      ref={node => this.options[index] = node}
                      className="composer-status-option" onKeyDown={event => this.onOptionKeyDown(event, index)}
                      onClick={() => this.selectStatus(status)}>
                <span className="composer-status-tag" style={{ color: colorFor(status), borderColor: colorFor(status) }}>{status.name}</span>
              </button>
            ))}
          </div>
        }
      </div>
    );
  }
}
