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
import ReactDOM from "react-dom";
import { connect } from "react-redux";
import HistoryButtons from "./HistoryButtons";
import SaveButton from "./SaveButton";

class HeaderActions extends Component {
  static propTypes = {
    saveUrl: PropTypes.string.isRequired,
    formUrl: PropTypes.string.isRequired,
    formName: PropTypes.string.isRequired,
  };

  constructor(props) {
    super(props);
    this.toolbar = document.querySelector(".main-nav__toolbar");
  }

  componentDidMount() {
    this.updateTitle();
    if (!this.toolbar) {
      return;
    }

    this.mount = document.createElement("div");
    this.mount.className = "composer-header-actions";
    this.toolbar.appendChild(this.mount);
    this.renderActions();
  }

  componentDidUpdate() {
    this.updateTitle();
    this.renderActions();
  }

  componentWillUnmount() {
    if (this.mount) {
      ReactDOM.unmountComponentAtNode(this.mount);
      this.mount.remove();
    }
  }

  renderActions() {
    if (this.mount) {
      // React 15's portal API retains Redux, CSRF and notification context.
      ReactDOM.unstable_renderSubtreeIntoContainer(this, <div className="composer-header-buttons"><HistoryButtons /><SaveButton {...this.props} /></div>, this.mount);
    }
  }

  updateTitle() {
    const name = this.props.formName || "New Form";
    const heading = document.querySelector(".main-nav__title h1");
    if (heading) {
      // EE includes the license badge in this heading; change only its text.
      let title = Array.from(heading.childNodes).find(node => node.nodeType === 3 && node.textContent.trim());
      if (!title) {
        title = document.createTextNode("");
        heading.insertBefore(title, heading.firstChild);
      }
      title.textContent = name + (heading.querySelector(".license-status-badge") ? " " : "");
    }
    document.title = name + " | ExpressionEngine";
  }

  render() {
    return this.toolbar ? null : (
      <div className="composer-header-fallback">
        <HistoryButtons />
        <SaveButton {...this.props} />
      </div>
    );
  }
}

export default connect(state => ({
  formName: state.composer.properties.form.name,
}))(HeaderActions);
