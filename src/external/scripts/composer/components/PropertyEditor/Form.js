/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import BasePropertyEditor from "./BasePropertyEditor";
import AddNewTemplate from "./Components/AddNewTemplate";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import TextProperty from "./PropertyItems/TextProperty";

@connect(
  (state) => ({
    solspaceTemplates: state.templates.solspaceTemplates,
    templates: state.templates.list,
    composerProperties: state.composer.properties,
    currentFormHandle: state.composer.properties.form.handle,
  }),
)
export default class Form extends BasePropertyEditor {
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

  constructor(props, context) {
      super(props, context);

      this.handleTitleUpdate = this.handleTitleUpdate.bind(this);
  }

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
    }

    optionGroups.push({
      label: "Custom Templates",
      options: templateList,
    });

    const statusOptions = [];
    formStatuses.map((status) => {
      statusOptions.push({
        key: status.id,
        value: status.name,
      });
    });

    // Updating the EE .main-nav__title h1 on load.
    document.getElementsByClassName("main-nav__title")[0].querySelector('h1').innerHTML = name;

    return (
      <div>
        <TextProperty
          label="Name"
          instructions="Name or title of the form."
          name="name"
          required={true}
          value={name}
          onChangeHandler={this.handleTitleUpdate}
        />

        <TextProperty
          label="Handle"
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

        <CheckboxProperty
          label="Store Submitted Data"
          bold={true}
          instructions="Should the submission data for this form be stored in the database?"
          name="storeData"
          checked={storeData}
          onChangeHandler={this.update}
        />

        <SelectProperty
          label="Formatting Template"
          instructions="The formatting template to assign to this form when using Render method (optional)."
          name="formTemplate"
          value={formTemplate}
          onChangeHandler={this.update}
          emptyOption="Select a template..."
          optionGroups={optionGroups}
        >
          {canManageSettings && <AddNewTemplate />}
        </SelectProperty>

        <SelectProperty
          label="Default Status"
          instructions="The default status to be assigned to new submissions."
          name="defaultStatus"
          required={true}
          value={defaultStatus}
          onChangeHandler={this.update}
          isNumeric={true}
          options={statusOptions}
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

  handleTitleUpdate(event) {
      const { value } = event.target;

      document.getElementsByClassName("main-nav__title")[0].querySelector('h1').innerHTML = value;

      document.title = value + " | ExpressionEngine";

      this.update(event);
  }
}
