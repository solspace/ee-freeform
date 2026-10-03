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
import React from "react";
import { connect } from "react-redux";
import BasePropertyEditor from "./BasePropertyEditor";
import AddNewTemplate from "./Components/AddNewTemplate";
import LightSwitchProperty from "./PropertyItems/LightSwitchProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import StatusProperty from "./PropertyItems/StatusProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import TextProperty from "./PropertyItems/TextProperty";

class Form extends BasePropertyEditor {
  static propTypes = {
    formStatuses: PropTypes.array.isRequired,
    solspaceTemplates: PropTypes.array.isRequired,
    templates: PropTypes.array.isRequired,
  };

  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
      name: PropTypes.string.isRequired,
      handle: PropTypes.string.isRequired,
      submissionTitleFormat: PropTypes.string.isRequired,
      description: PropTypes.string.isRequired,
      storeData: PropTypes.bool,
      defaultStatus: PropTypes.number.isRequired,
      returnUrl: PropTypes.string.isRequired,
      formTemplate: PropTypes.string,
    }).isRequired,
    canManageSettings: PropTypes.bool.isRequired,
    isDefaultTemplates: PropTypes.bool.isRequired,
  };

  render() {
    const { isDefaultTemplates } = this.context;
    const { properties: { name, handle, submissionTitleFormat, defaultStatus, returnUrl, description, formTemplate } } = this.context;

    let storeData = this.context.properties.storeData;
    if (storeData === undefined) {
      storeData = true;
    }

    const { formStatuses, solspaceTemplates, templates } = this.props;
    const { canManageSettings } = this.context;

    const solspaceTemplateList = [];
    solspaceTemplates.map((item, i) => {
      solspaceTemplateList.push({
        key: item.fileName,
        value: item.name,
      });
    });

    // Keep the selected value visible for forms using an older bundled sample.
    const legacyTemplates = {
      "bootstrap.html": "Bootstrap (Legacy)",
      "foundation.html": "Foundation (Legacy)",
      "materialize.html": "Materialize (Legacy)",
    };
    if (Object.prototype.hasOwnProperty.call(legacyTemplates, formTemplate)) {
      solspaceTemplateList.push({
        key: formTemplate,
        value: legacyTemplates[formTemplate],
      });
    }

    const templateList = [];
    templates.map((item) => {
      templateList.push({
        key: item.fileName,
        value: item.name,
      });
    });

    let optionGroups = [];
    if (isDefaultTemplates) {
      optionGroups.push({
        label: "Solspace Templates",
        options: solspaceTemplateList,
      });
    } else if (Object.prototype.hasOwnProperty.call(legacyTemplates, formTemplate)) {
      optionGroups.push({
        label: "Legacy Template",
        options: [{ key: formTemplate, value: legacyTemplates[formTemplate] }],
      });
    }

    optionGroups.push({
      label: "Custom Templates",
      options: templateList,
    });

    return (
      <div>
        <TextProperty
          label="Form Name"
          instructions="Enter a name or title for the form."
          name="name"
          required={true}
          value={name}
          onChangeHandler={this.update}
        />

        <TextProperty
          label="Form Handle"
          instructions="How you’ll refer to this form in the templates."
          name="handle"
          required={true}
          value={handle}
          onChangeHandler={this.updateHandle}
        />

        <TextProperty
          label="Submission Title"
          instructions="What the auto-generated submission titles should look like."
          name="submissionTitleFormat"
          required={true}
          value={submissionTitleFormat}
          onChangeHandler={this.update}
        />

        <LightSwitchProperty
          label="Store Submitted Data"
          instructions="Store submission data for this form in the database."
          name="storeData"
          checked={storeData}
          onChangeHandler={this.update}
        />

        <SelectProperty
          label="Formatting Template"
          instructions="The template used when rendering the form (optional)."
          name="formTemplate"
          value={formTemplate}
          onChangeHandler={this.update}
          emptyOption="--"
          optionGroups={optionGroups}
          inlineAction={canManageSettings}
        >
          {canManageSettings && <AddNewTemplate buttonLabel="New" />}
        </SelectProperty>

        <StatusProperty
          label="Default Status"
          instructions="The default status to be assigned to new submissions."
          name="defaultStatus"
          required={true}
          value={defaultStatus}
          onChangeHandler={value => this.updateKeyValue("defaultStatus", value)}
          statuses={formStatuses}
        />

        <TextProperty
          label="Return URL"
          instructions="The URL the form will redirect to after successful submit."
          name="returnUrl"
          value={returnUrl}
          onChangeHandler={this.update}
        />

        <TextareaProperty
          label="Description"
          instructions="Description of this form."
          name="description"
          value={description}
          onChangeHandler={this.update}
        />
      </div>
    );
  }

}

export default connect(
  (state) => ({
    solspaceTemplates: state.templates.solspaceTemplates,
    templates: state.templates.list,
    composerProperties: state.composer.properties,
    currentFormHandle: state.composer.properties.form.handle,
  }),
)(Form);
