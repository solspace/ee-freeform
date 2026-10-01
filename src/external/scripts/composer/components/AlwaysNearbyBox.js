import PropTypes from "prop-types";
import React from "react";

// CSS sticky positioning keeps the sidebars nearby without scroll listeners,
// measured widths, or delayed updates after adding fields.
const AlwaysNearbyBox = ({ className = "", stickyTop, children }) => (
  <div className={`composer-sidebar-content ${className}`}>
    {stickyTop && <div className="sticky">{stickyTop}</div>}
    <div>{children}</div>
  </div>
);

AlwaysNearbyBox.propTypes = {
  className: PropTypes.string,
  stickyTop: PropTypes.node,
  children: PropTypes.node,
};

export default AlwaysNearbyBox;
