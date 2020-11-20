/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React, { Component } from "react";
import { connect } from "react-redux";
import FieldHelper from "../../helpers/FieldHelper";
import Field from "./Field";

@connect(state => ({
  currentPage: state.context.page,
}))
export default class FieldGroup extends Component {
  static propTypes = {
    fields: PropTypes.arrayOf(
      PropTypes.shape({
        type: PropTypes.string.isRequired,
        label: PropTypes.string.isRequired,
        handle: PropTypes.string.isRequired,
      }).isRequired,
    ).isRequired,
    usedFields: PropTypes.array.isRequired,
    onFieldClick: PropTypes.func,
    currentPage: PropTypes.number.isRequired,
  };

  static contextTypes = {
    canManageFields: PropTypes.bool.isRequired,
    canManageNotifications: PropTypes.bool.isRequired,
  };

  render() {
    const { title, fields, currentPage, usedFields, onFieldClick } = this.props;

    return (
      <div className="composer-fields">
        <h3>Fields</h3>
        <ul>
          {fields.map((field, index) =>
            <Field
              key={index}
              hash={FieldHelper.hashField(field)}
              {...field}
              isUsed={usedFields.indexOf(field.id) !== -1}
              onClick={() => onFieldClick(FieldHelper.hashField(field), field, currentPage)}
            />,
          )}
        </ul>
      </div>
    );
  }
}
