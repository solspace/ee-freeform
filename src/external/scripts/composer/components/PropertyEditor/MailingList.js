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
import BasePropertyEditor from "./BasePropertyEditor";
import * as FieldTypes from "../../constants/FieldTypes";
import {connect} from "react-redux";
import TextProperty from "./PropertyItems/TextProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import CustomProperty from "./PropertyItems/CustomProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import IntegrationMappingTable from "./Components/IntegrationMappingTable/IntegrationMappingTable";
import {invalidateMailingLists, fetchMailingListsIfNeeded} from "../../actions/MailingLists";

@connect(
  (state) => ({
    composerProperties: state.composer.properties,
    hash: state.context.hash,
    mailingLists: state.mailingLists.list,
    isFetching: state.mailingLists.isFetching,
  }),
  (dispatch) => ({
    fetchMailingLists: () => {
      dispatch(invalidateMailingLists());
      dispatch(fetchMailingListsIfNeeded());
    }
  })
)
export default class MailingList extends BasePropertyEditor {
  static propTypes = {
    fetchMailingLists: PropTypes.func.isRequired,
    isFetching: PropTypes.bool.isRequired,
    composerProperties: PropTypes.object.isRequired,
    mailingLists: PropTypes.arrayOf(
      PropTypes.shape({
        integrationId: PropTypes.number.isRequired,
        resourceId: PropTypes.node,
        emailFieldHash: PropTypes.string,
        type: PropTypes.string.isRequired,
        source: PropTypes.string.isRequired,
        name: PropTypes.string.isRequired,
        label: PropTypes.string,
      })
    ).isRequired,
  };

  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    hash: PropTypes.string.isRequired,
    properties: PropTypes.shape({
      type: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      integrationId: PropTypes.number.isRequired,
      resourceId: PropTypes.node,
      emailFieldHash: PropTypes.string,
      mapping: PropTypes.object,
    }).isRequired
  };

  constructor(props, context) {
    super(props, context);

    this.updateIntegration = this.updateIntegration.bind(this);
  }

  render() {
    const {hash, properties: {value, label, integrationId, resourceId, emailFieldHash, mapping, instructions}} = this.context;

    const {composerProperties, mailingLists, fetchMailingLists, isFetching} = this.props;

    let selectedIntegration = null;
    let lists = [];
    for (let i in mailingLists) {
      const integration = mailingLists[i];
      if (integration.integrationId === integrationId) {
        selectedIntegration = mailingLists[i];
        selectedIntegration.lists.map(item => {
          lists.push({
            key: item.id,
            value: item.name,
          });
        });
        break;
      }
    }

    let emailFields = [];
    for (var key in composerProperties) {
      if (!composerProperties.hasOwnProperty(key)) continue;

      const prop = composerProperties[key];

      if (prop.type !== FieldTypes.EMAIL) continue;

      emailFields.push({
        key: key,
        value: prop.label,
      });
    }


    let mappingField = "";
    if (resourceId && selectedIntegration) {
      const selectedMailingList = selectedIntegration.lists.find((item) => {
        return item.id == resourceId;
      });

      const formFields = [];
      for (let key in composerProperties) {
        if (!composerProperties.hasOwnProperty(key)) continue;

        const prop = composerProperties[key];
        if (FieldTypes.INTEGRATION_SUPPORTED_TYPES.indexOf(prop.type) === -1) continue;

        formFields.push({
          handle: prop.handle,
          label: prop.label,
        });
      }

      let fieldList = selectedMailingList.fields;

      mappingField = (
        <CustomProperty
          label="Field Mapping"
          instructions="Map Mailing List fields to your Freeform fields."
          content={
            <IntegrationMappingTable
              formFields={formFields}
              fields={fieldList}
              mapping={mapping}
            />
          }
        />
      );
    }

    return (
      <div>
        <TextProperty
          label="Handle"
          instructions="How you’ll refer to this field in the templates."
          name="handle"
          value={hash}
          onChangeHandler={this.updateHandle}
          className="code"
        />

        <hr />

        <TextProperty
          label="Label"
          instructions="Field label used to describe the field."
          name="label"
          value={label}
          onChangeHandler={this.update}
        />

        <TextareaProperty
          label="Instructions"
          instructions="Field specific user instructions."
          name="instructions"
          value={instructions}
          onChangeHandler={this.update}
        />

        <CheckboxProperty
          label="Checked by default"
          name="value"
          checked={value}
          onChangeHandler={this.update}
        />

        <hr />

        <SelectProperty
          label="Mailing Lists"
          instructions="Choose the opt-in mailing list that users will be added to."
          name="resourceId"
          onChangeHandler={this.updateIntegration}
          value={resourceId}
          emptyOption="Select a list..."
          options={lists}
        />

        <button
          className="btn download icon"
          onClick={fetchMailingLists}
          disabled={isFetching}
        >
          {isFetching ? "Refreshing..." : "Refresh lists"}
        </button>

        <SelectProperty
          label="Target Email Field"
          instructions="The email field used to push to the mailing list."
          name="emailFieldHash"
          onChangeHandler={this.update}
          value={emailFieldHash}
          emptyOption="Select a field..."
          options={emailFields}
        />

        {mappingField}
      </div>
    );
  }

  updateIntegration(event) {
    const {updateField} = this.context;
    const resource      = event.target;

    const resourceId = resource.value;

    updateField({
      resourceId: resourceId ? resourceId : "",
      mapping: {},
    });

    this.update(event);
  }
}
