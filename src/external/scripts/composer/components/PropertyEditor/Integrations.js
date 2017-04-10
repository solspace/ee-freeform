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
import * as FieldTypes from "../../constants/FieldTypes";
import BasePropertyEditor from "./BasePropertyEditor";
import CustomProperty from "./PropertyItems/CustomProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import IntegrationMappingTable from "./Components/IntegrationMappingTable/IntegrationMappingTable";
import {connect} from "react-redux";
import {invalidateCrmIntegrations, fetchCrmIntegrationsIfNeeded} from "../../actions/Integrations";

@connect(
  (state) => ({
    properties: state.composer.properties,
    integrationProperties: state.composer.properties.integration,
    integrationList: state.integrations.list,
    isFetching: state.integrations.isFetching,
  }),
  (dispatch) => ({
    fetchCrmIntegrations: () => {
      dispatch(invalidateCrmIntegrations());
      dispatch(fetchCrmIntegrationsIfNeeded());
    }
  })
)
export default class Integrations extends BasePropertyEditor {
  static propTypes = {
    integrationList: PropTypes.array.isRequired,
    integrationProperties: PropTypes.object.isRequired,
    properties: PropTypes.object.isRequired,
    isFetching: PropTypes.bool.isRequired,
    fetchCrmIntegrations: PropTypes.func.isRequired,
  };

  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    hash: PropTypes.string.isRequired,
    properties: PropTypes.shape({
      type: PropTypes.string.isRequired,
      integrationId: PropTypes.node,
      mapping: PropTypes.any,
    })
  };

  constructor(props, context) {
    super(props, context);

    this.updateIntegration = this.updateIntegration.bind(this);
  }

  render() {
    const {integrationList, properties, integrationProperties: {integrationId, mapping}} = this.props;

    const {isFetching, fetchCrmIntegrations} = this.props;

    let fieldList = [];
    const integrationOptions = [];
    integrationList.map(item => {
      integrationOptions.push({
        key: item.id,
        value: item.name,
      });

      if (item.id == integrationId) {
        fieldList = item.fields;
      }
    });

    const formFields = [];
    for (let key in properties) {
      if (!properties.hasOwnProperty(key)) continue;

      const prop = properties[key];
      if (FieldTypes.INTEGRATION_SUPPORTED_TYPES.indexOf(prop.type) === -1) continue;

      formFields.push({
        handle: prop.handle,
        label: prop.label,
      })
    }

    let mappingField = "";
    if (integrationId) {
      mappingField = (
        <CustomProperty
          label="Field Mapping"
          instructions="Map CRM fields to your Freeform fields."
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
        <SelectProperty
          label="Integration"
          instructions="Choose an integration type"
          name="integrationId"
          ref="integration"
          value={integrationId ? integrationId : 0}
          isNumeric={true}
          emptyOption="Choose an integration..."
          options={integrationOptions}
          onChangeHandler={this.updateIntegration}
        />

        <button
          className="btn action refresh icon"
          onClick={fetchCrmIntegrations}
          disabled={isFetching}
        >
          {isFetching ? "Refreshing..." : "Refresh Integration"}
        </button>

        {mappingField}
      </div>
    );
  }

  updateIntegration(event) {
    const {updateField} = this.context;
    const integration   = event.target;

    const integrationId = parseInt(integration.value);

    updateField({
      integrationId: integrationId ? integrationId : 0,
      mapping: {},
    });
  }
}
