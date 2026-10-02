import PropTypes from "prop-types";
import React, { Component } from "react";

export default class ConfirmRemoval extends Component {
  static propTypes = {
    label: PropTypes.string.isRequired,
    kind: PropTypes.oneOf(["page", "field"]).isRequired,
    onCancel: PropTypes.func.isRequired,
    onConfirm: PropTypes.func.isRequired,
  };

  componentDidMount() {
    // Native dialog supplies modal semantics and an inert background. Listen
    // directly because React 15 does not expose the dialog's cancel event.
    this.dialog.addEventListener("cancel", this.handleCancel);
    this.dialog.showModal();
    this.cancelButton.focus();
  }

  componentWillUnmount() {
    this.dialog.removeEventListener("cancel", this.handleCancel);
    this.dialog.close();
  }

  handleCancel = (event) => {
    event.preventDefault();
    this.props.onCancel();
  };

  handleKeyDown = (event) => {
    if (event.key !== "Tab") {
      return;
    }
    if (event.shiftKey && document.activeElement === this.cancelButton) {
      event.preventDefault();
      this.deleteButton.focus();
    } else if (!event.shiftKey && document.activeElement === this.deleteButton) {
      event.preventDefault();
      this.cancelButton.focus();
    }
  };

  render() {
    const { label, kind, onCancel, onConfirm } = this.props;
    const titleId = `freeform-delete-${kind}-title`;
    const descriptionId = `freeform-delete-${kind}-description`;
    const description = kind === "page"
      ? "and all fields on this page will be removed"
      : "will be removed from this page";
    return (
      <dialog className="composer-delete-dialog panel"
              ref={dialog => { this.dialog = dialog; }}
              onKeyDown={this.handleKeyDown}
              onClick={event => event.stopPropagation()}
              onDragStart={event => { event.preventDefault(); event.stopPropagation(); }}
              role="alertdialog" aria-modal="true"
              aria-labelledby={titleId}
              aria-describedby={descriptionId}>
        <div className="panel-heading">
          <h2 id={titleId}>Delete {kind}?</h2>
        </div>
        <div className="panel-body">
          <p id={descriptionId}>
            <strong>{label}</strong> {description}. You can restore it with Undo while editing.
          </p>
        </div>
        <div className="panel-footer">
          <button type="button" className="button button--default"
                  ref={button => { this.cancelButton = button; }} onClick={onCancel}>Cancel</button>
          <button type="button" className="button button--danger"
                  ref={button => { this.deleteButton = button; }} onClick={onConfirm}>Delete {kind === "page" ? "Page" : "Field"}</button>
        </div>
      </dialog>
    );
  }
}
