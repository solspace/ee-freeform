/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
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
export default class MailingListFieldGroup extends Component {
  static propTypes = {
    fields: PropTypes.arrayOf(
      PropTypes.shape({
        type: PropTypes.string.isRequired,
        name: PropTypes.string.isRequired,
        lists: PropTypes.array.isRequired,
      }).isRequired,
    ).isRequired,
    usedFields: PropTypes.array.isRequired,
    onFieldClick: PropTypes.func,
    currentPage: PropTypes.number.isRequired,
  };

  render() {
    const { fields, currentPage, usedFields, onFieldClick } = this.props;

    if (!fields.length) {
      return null;
    }

    return (
      <div className="composer-mailing-list-fields">
        <h3>Mailing Lists</h3>
        <ul>
          {fields.map((field, index) =>
            <Field
              key={index}
              {...field}
              label={field.name}
              badge={field.source}
              type="mailing_list"
              isUsed={usedFields.indexOf(field.id) !== -1}
              onClick={() => onFieldClick(FieldHelper.hashField(field), field, currentPage)}
            />,
          )}
        </ul>
      </div>
    );
  }
}
