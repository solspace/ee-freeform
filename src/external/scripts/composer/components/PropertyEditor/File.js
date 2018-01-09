/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
import BasePropertyEditor from "./BasePropertyEditor";
import TextProperty from "./PropertyItems/TextProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import CheckboxListProperty from "./PropertyItems/CheckboxListProperty";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import {connect} from "react-redux";

@connect(
  (state) => ({
    hash: state.context.hash,
    properties: state.composer.properties,
    assetSources: state.assetSources,
    allFileKinds: state.fileKinds,
  })
)
export default class File extends BasePropertyEditor {
  static propTypes = {
    assetSources: PropTypes.arrayOf(
      PropTypes.shape({
        id: PropTypes.number.isRequired,
        name: PropTypes.string.isRequired,
        type: PropTypes.string.isRequired,
      })
    ).isRequired,
    allFileKinds: PropTypes.object.isRequired,
  };

  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
      id: PropTypes.number.isRequired,
      type: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      handle: PropTypes.string.isRequired,
      assetSourceId: PropTypes.number.isRequired,
      required: PropTypes.bool,
      fileKinds: PropTypes.array,
      maxFileSizeKB: PropTypes.number,
    }).isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.getFileKindOptions = this.getFileKindOptions.bind(this);
  }

  render() {
    const {assetSources} = this.props;

    const {properties: {type, label, handle, required, assetSourceId, fileKinds, maxFileSizeKB, instructions}} = this.context;

    const assetSourceList = [];
    assetSources.map((source) => {
      assetSourceList.push({
        key: source.id,
        value: source.name,
      });
    });

    return (
      <div>
        <TextProperty
          label="Handle"
          instructions="How you’ll refer to this field in the templates."
          name="handle"
          value={handle}
          onChangeHandler={this.updateHandle}
        />

        <hr />

        <CheckboxProperty
          label="This field is required?"
          name="required"
          checked={required}
          onChangeHandler={this.update}
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

        <SelectProperty
          label="Upload Directory"
          instructions="Select an upload directory source to be able to store user uploaded files."
          name="assetSourceId"
          value={assetSourceId}
          onChangeHandler={this.update}
          isNumeric={true}
          emptyOption="Select an Upload Directory..."
          options={assetSourceList}
        />

        <TextProperty
          label="Maximum filesize"
          instructions="Specify the maximum filesize in KB"
          name="maxFileSizeKB"
          placeholder="2048"
          value={maxFileSizeKB}
          onChangeHandler={this.update}
          isNumeric={true}
        />

        <CheckboxListProperty
          label="Allowed File Kinds"
          instructions="Leave everything unchecked to allow all file kinds"
          name="fileKinds"
          values={fileKinds}
          onChangeHandler={this.update}
          updateField={this.context.updateField}
          options={this.getFileKindOptions()}
        />

      </div>
    );
  }

  getFileKindOptions() {
    const {allFileKinds} = this.props;

    const fileKindList = [];
    for (let key in allFileKinds) {
      if (!allFileKinds.hasOwnProperty(key)) continue;

      fileKindList.push({
        key: key,
        value: allFileKinds[key].label,
      });
    }

    return fileKindList;
  }
}
