/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
import {connect} from "react-redux";
import Field from "./Field";
import FieldHelper from "../../helpers/FieldHelper";
import AddNewField from "./Components/AddNewField";

@connect(state => ({
  currentPage: state.context.page
}))
export default class FieldGroup extends Component {
  static propTypes = {
    fields: PropTypes.arrayOf(
      PropTypes.shape({
        type: PropTypes.string.isRequired,
        label: PropTypes.string.isRequired,
        handle: PropTypes.string.isRequired,
      }).isRequired
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
    const {title, fields, currentPage, usedFields, onFieldClick} = this.props;
    const {canManageFields}                                      = this.context;

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
            />
          )}
        </ul>

        {canManageFields && <AddNewField />}
      </div>
    );
  }
}
