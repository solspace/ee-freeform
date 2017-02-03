/*
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
import BasePropertyEditor from "./BasePropertyEditor";
import {camelize} from "underscore.string";
import {connect} from "react-redux";
import AddNewTemplate from "./Components/AddNewTemplate";
import TextProperty from "./PropertyItems/TextProperty";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";

@connect(
  (state) => ({
    solspaceTemplates: state.templates.solspaceTemplates,
    templates: state.templates.list,
    composerProperties: state.composer.properties,
    currentFormHandle: state.composer.properties.form.handle,
  })
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
  };

  render() {
    const {properties: {name, handle, submissionTitleFormat, defaultStatus, returnUrl, description, formTemplate}} = this.context;

    let storeData = this.context.properties.storeData;
    if (storeData === undefined) {
      storeData = true;
    }

    const {formStatuses, solspaceTemplates, templates} = this.props;
    const {canManageSettings}                          = this.context;

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
      })
    });

    const optionGroups = [
      {
        label: "Solspace Templates",
        options: solspaceTemplateList,
      },
      {
        label: "Custom Templates",
        options: templateList,
      }
    ];

    const statusOptions = [];
    formStatuses.map((status) => {
      statusOptions.push({
        key: status.id,
        value: status.name,
      });
    });

    return (
      <div>
        <TextProperty
          label="Name"
          instructions="Name or title of the form."
          name="name"
          required={true}
          value={name}
          onChangeHandler={this.update}
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
}
