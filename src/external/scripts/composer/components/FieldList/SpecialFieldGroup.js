/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
import {connect} from "react-redux";
import Field from "./Field";
import FieldHelper from "../../helpers/FieldHelper";
import AddNewField from "./Components/AddNewField";
import ReactDOM from "react-dom";

@connect(state => ({
  currentPage: state.context.page
}))
export default class SpecialFieldGroup extends Component {
    static propTypes = {
        fields: PropTypes.arrayOf(
            PropTypes.shape({
                type: PropTypes.string.isRequired,
                label: PropTypes.string.isRequired,
            }).isRequired
        ).isRequired,
        onFieldClick: PropTypes.func,
        currentPage: PropTypes.number.isRequired
    };

  static contextTypes = {
    canManageFields: PropTypes.bool.isRequired,
    canManageNotifications: PropTypes.bool.isRequired,
  };

  render() {
    const {fields, currentPage, onFieldClick} = this.props;
    const {canManageFields}                   = this.context;

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
            />
          )}
        </ul>

        {canManageFields && <AddNewField />}
      </div>
    )
  }
}
