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
import AddNewField from "./Components/AddNewField";

class FieldGroup extends Component {
  static propTypes = {
    fieldCount: PropTypes.number.isRequired,
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
    formPropCleanup: PropTypes.bool.isRequired,
  };

  render() {
    const { fieldCount, fields, currentPage, usedFields, onFieldClick } = this.props;
    const { canManageFields, formPropCleanup } = this.context;
    const canCreate = canManageFields && (!formPropCleanup || fieldCount < 15);

    return (
      <div className="composer-fields">
        <AddNewField canCreate={canCreate} />
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

export default connect(state => ({
  currentPage: state.context.page,
  fieldCount: state.fields.fields.length,
}))(FieldGroup);
