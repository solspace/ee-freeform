import PropTypes from "prop-types";
import React, { Component } from "react";

export default class ConfirmPageRemoval extends Component {
  static propTypes = {
    pageLabel: PropTypes.string.isRequired,
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
    const { pageLabel, onCancel, onConfirm } = this.props;
    return (
      <dialog className="composer-page-delete-dialog panel"
              ref={dialog => { this.dialog = dialog; }}
              onKeyDown={this.handleKeyDown}
              onDragStart={event => { event.preventDefault(); event.stopPropagation(); }}
              role="alertdialog" aria-modal="true"
              aria-labelledby="freeform-delete-page-title"
              aria-describedby="freeform-delete-page-description">
        <div className="panel-heading">
          <h2 id="freeform-delete-page-title">Delete page?</h2>
        </div>
        <div className="panel-body">
          <p id="freeform-delete-page-description">
            <strong>{pageLabel}</strong> and all fields on this page will be removed. This cannot be undone.
          </p>
        </div>
        <div className="panel-footer">
          <button type="button" className="button button--default"
                  ref={button => { this.cancelButton = button; }} onClick={onCancel}>Cancel</button>
          <button type="button" className="button button--danger"
                  ref={button => { this.deleteButton = button; }} onClick={onConfirm}>Delete Page</button>
        </div>
      </dialog>
    );
  }
}
