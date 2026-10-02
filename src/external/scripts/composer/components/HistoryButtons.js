/*
 * Freeform for ExpressionEngine
 *
 * @package Solspace:Freeform
 * @license https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React, { Component } from "react";
import { connect } from "react-redux";
import { redo, undo } from "../actions/Actions";

class HistoryButtons extends Component {
  static propTypes = {
    canUndo: PropTypes.bool.isRequired,
    canRedo: PropTypes.bool.isRequired,
    undo: PropTypes.func.isRequired,
    redo: PropTypes.func.isRequired,
  };

  componentDidMount() {
    document.addEventListener("keydown", this.onKeyDown);
  }

  componentWillUnmount() {
    document.removeEventListener("keydown", this.onKeyDown);
  }

  onKeyDown = event => {
    const editable = event.target.closest && event.target.closest("input, textarea, select, [contenteditable]:not([contenteditable='false'])");
    if ((!event.metaKey && !event.ctrlKey) || event.altKey || editable) {
      return;
    }

    const key = event.key.toLowerCase();
    const redoShortcut = key === "y" || key === "z" && event.shiftKey;
    if (redoShortcut && this.props.canRedo) {
      event.preventDefault();
      this.props.redo();
    } else if (key === "z" && !event.shiftKey && this.props.canUndo) {
      event.preventDefault();
      this.props.undo();
    }
  };

  render() {
    const { canUndo, canRedo, undo, redo } = this.props;
    return (
      <div className="composer-history-buttons" role="group" aria-label="Form history">
        <button type="button" className="button button--secondary composer-history-button"
                aria-label="Undo" title="Undo (⌘Z / Ctrl+Z)" disabled={!canUndo} onClick={undo}>
          <svg viewBox="0 0 96 80" aria-hidden="true" focusable="false"><path d="M37 1 1 30l36 29V41h18c15 0 23 8 23 22 0 6-2 11-5 16 12-8 19-19 19-31 0-20-14-31-37-31H37V1Z" /></svg>
        </button>
        <button type="button" className="button button--secondary composer-history-button"
                aria-label="Redo" title="Redo (⌘Shift+Z / Ctrl+Y)" disabled={!canRedo} onClick={redo}>
          <svg viewBox="0 0 96 80" aria-hidden="true" focusable="false"><path transform="translate(96 0) scale(-1 1)" d="M37 1 1 30l36 29V41h18c15 0 23 8 23 22 0 6-2 11-5 16 12-8 19-19 19-31 0-20-14-31-37-31H37V1Z" /></svg>
        </button>
      </div>
    );
  }
}

export default connect(
  state => ({ canUndo: state.history.past.length > 0, canRedo: state.history.future.length > 0 }),
  dispatch => ({ undo: () => dispatch(undo()), redo: () => dispatch(redo()) }),
)(HistoryButtons);
