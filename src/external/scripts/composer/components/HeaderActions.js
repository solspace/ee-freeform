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
import SaveButton from "./SaveButton";

export default class HeaderActions extends Component {
  static propTypes = {
    saveUrl: PropTypes.string.isRequired,
    formUrl: PropTypes.string.isRequired,
  };

  constructor(props) {
    super(props);
    this.toolbar = document.querySelector(".main-nav__toolbar");
  }

  componentDidMount() {
    if (!this.toolbar) {
      return;
    }

    this.mount = document.createElement("div");
    this.mount.className = "composer-header-actions";
    this.toolbar.appendChild(this.mount);
    this.renderActions();
  }

  componentDidUpdate() {
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
      ReactDOM.unstable_renderSubtreeIntoContainer(this, <SaveButton {...this.props} />, this.mount);
    }
  }

  render() {
    return this.toolbar ? null : (
      <div className="composer-header-fallback">
        <SaveButton {...this.props} />
      </div>
    );
  }
}
