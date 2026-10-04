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
import CheckboxListProperty from "./PropertyItems/CheckboxListProperty";
import SelectProperty from "./PropertyItems/SelectProperty";
import TextareaProperty from "./PropertyItems/TextareaProperty";
import TextProperty from "./PropertyItems/TextProperty";
import LightSwitchProperty from "./PropertyItems/LightSwitchProperty";

class File extends BasePropertyEditor {
  static propTypes = {
    assetSources: PropTypes.arrayOf(
      PropTypes.shape({
        id: PropTypes.number.isRequired,
        name: PropTypes.string.isRequired,
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
      fileCount: PropTypes.number,
    }).isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.getFileKindOptions = this.getFileKindOptions.bind(this);
  }

  render() {
    const { assetSources } = this.props;

    const { properties: { type, handle, required, assetSourceId, fileKinds, maxFileSizeKB, fileCount, instructions } } = this.context;

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

        <LightSwitchProperty
          label="This field is required"
          name="required"
          checked={required}
          onChangeHandler={this.update}
        />

        <hr />

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
          emptyOption="--"
          options={assetSourceList}
        />

        <TextProperty
          label="File Count"
          instructions="Specify the maximum uploadable file count"
          name="fileCount"
          placeholder="1"
          value={fileCount}
          onChangeHandler={this.update}
          isNumeric={true}
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
    const { allFileKinds } = this.props;

    const fileKindList = [];
    for (let key in allFileKinds) {
      if (!allFileKinds.hasOwnProperty(key)) {
        continue;
      }

      fileKindList.push({
        key: key,
        value: allFileKinds[key].label,
      });
    }

    return fileKindList;
  }
}

export default connect(
  (state) => ({
    hash: state.context.hash,
    properties: state.composer.properties,
    assetSources: state.assetSources,
    allFileKinds: state.fileKinds,
  }),
)(File);
