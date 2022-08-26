/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React, { Component } from "react";
import { connect } from "react-redux";
import FieldHelper from "../../helpers/FieldHelper";
import AddNewField from "./Components/AddNewField";
import Field from "./Field";

@connect(state => ({
  currentPage: state.context.page,
  fieldCount: state.fields.fields.length,
}))
export default class SpecialFieldGroup extends Component {
  static propTypes = {
    fieldCount: PropTypes.number.isRequired,
    fields: PropTypes.arrayOf(
      PropTypes.shape({
        type: PropTypes.string.isRequired,
        label: PropTypes.string.isRequired,
      }).isRequired,
    ).isRequired,
    onFieldClick: PropTypes.func,
    currentPage: PropTypes.number.isRequired,
  };

  static contextTypes = {
    canManageFields: PropTypes.bool.isRequired,
    canManageNotifications: PropTypes.bool.isRequired,
    formPropCleanup: PropTypes.bool.isRequired,
  };

  render() {
    const { fieldCount, fields, currentPage, onFieldClick } = this.props;
    let { canManageFields, formPropCleanup } = this.context;

    if (canManageFields) {
      canManageFields = !formPropCleanup || fieldCount < 15;
    }

    return (
      <div className="composer-special-fields">
        <h3>Special Fields</h3>
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

        {canManageFields && <AddNewField />}
      </div>
    );
  }
}
