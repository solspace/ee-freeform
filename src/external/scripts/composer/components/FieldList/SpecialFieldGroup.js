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
import { connect } from "react-redux";
import FieldHelper from "../../helpers/FieldHelper";
import Field from "./Field";

class SpecialFieldGroup extends Component {
  static propTypes = {
    fields: PropTypes.arrayOf(
      PropTypes.shape({
        type: PropTypes.string.isRequired,
        label: PropTypes.string.isRequired,
      }).isRequired,
    ).isRequired,
    onFieldClick: PropTypes.func,
    currentPage: PropTypes.number.isRequired,
  };

  render() {
    const { fields, currentPage, onFieldClick } = this.props;

    return (
      <div className="composer-special-fields">
        <div className="composer-palette-heading sidebar__section-title">
          <h3>Special Fields</h3>
        </div>
        <ul>
          {fields.map((field, index) =>
            <Field
              key={index}
              {...field}
              isUsed={false}
              onClick={() => onFieldClick(FieldHelper.hashField(field), field, currentPage)}
            />,
          )}
        </ul>
      </div>
    );
  }
}

export default connect(state => ({
  currentPage: state.context.page,
}))(SpecialFieldGroup);
