import PropTypes from "prop-types";
import React, { Component } from "react";

// Keep the toolbar outside the scrolling body so it never shifts when a
// different editor opens or the user reaches the end of a panel.
export default class AlwaysNearbyBox extends Component {
  static propTypes = {
    className: PropTypes.string,
    stickyTop: PropTypes.node,
    children: PropTypes.node,
    scrollKey: PropTypes.string,
  };

  componentDidUpdate(previousProps) {
    if (previousProps.scrollKey !== this.props.scrollKey && this.body) {
      this.body.scrollTop = 0;
    }
  }

  setBody = (body) => {
    this.body = body;
  };

  render() {
    const { className = "", stickyTop, children } = this.props;

    return (
      <div className={`composer-sidebar-content ${className}`}>
        {stickyTop && <div className="composer-sidebar-toolbar">{stickyTop}</div>}
        <div className="composer-sidebar-body" ref={this.setBody}>{children}</div>
      </div>
    );
  }
}
