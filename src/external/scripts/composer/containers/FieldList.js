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
import {addFieldToNewRow} from "../actions/Actions";
import FieldGroup from "../components/FieldList/FieldGroup";
import SpecialFieldGroup from "../components/FieldList/SpecialFieldGroup";
import MailingListFieldGroup from "../components/FieldList/MailingListFieldGroup";
import FieldHelper from "../helpers/FieldHelper";
import AlwaysNearbyBox from "../components/AlwaysNearbyBox";

@connect(
  (state) => ({
    fields: state.fields.fields,
    specialFields: state.specialFields,
    mailingListFields: state.mailingLists.list,
    layout: state.composer.layout
  }),
  (dispatch) => ({
    onFieldClick: (hash, properties, pageIndex) => {
      dispatch(addFieldToNewRow(hash, properties, pageIndex));
    }
  })
)
export default class FieldList extends Component {
  static propTypes = {
    fields: PropTypes.array.isRequired,
    specialFields: PropTypes.array.isRequired,
    mailingListFields: PropTypes.array.isRequired,
    onFieldClick: PropTypes.func.isRequired,
  };

  render() {
    const {fields, specialFields, mailingListFields, onFieldClick} = this.props;

    const usedFields = this.getUsedFields();

    return (
      <AlwaysNearbyBox
        className="field-container"
        stickyTop={
          <SpecialFieldGroup
            fields={specialFields}
            onFieldClick={onFieldClick}
          />
        }
      >
        <FieldGroup
          fields={fields}
          usedFields={usedFields}
          onFieldClick={onFieldClick}
        />
        <MailingListFieldGroup
          fields={mailingListFields}
          usedFields={usedFields}
          onFieldClick={onFieldClick}
        />
      </AlwaysNearbyBox>
    );
  }

  getUsedFields() {
    const {layout, fields} = this.props;
    const fieldIds         = fields.map(field => (field.id));

    const usedFields = [];

    for (var rows of layout) {
      for (var row of rows) {
        for (var hash of row.columns) {
          const id = FieldHelper.deHashId(hash);

          if (fieldIds.indexOf(id) !== -1) {
            usedFields.push(id);
          }
        }
      }
    }

    return usedFields;
  }
}
