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
import AceEditor from "react-ace";
import "brace/mode/html";
import "brace/theme/chrome";

export default class Html extends BasePropertyEditor {
  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    properties: PropTypes.shape({
      type: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      value: PropTypes.string.isRequired,
    }).isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.updateHtmlValue = this.updateHtmlValue.bind(this);
  }

  render() {
    const {properties: {value}} = this.context;

    return (
      <div>
        <AceEditor
          mode="html"
          theme="chrome"
          value={value}
          onChange={this.updateHtmlValue}
          enableLiveAutocompletion={true}
          enableBasicAutocompletion={true}
          highlightActiveLine={true}
          showGutter={false}
          fontSize={12}
          width="250px"
          editorProps={{$blockScrolling: true}}
        />
      </div>
    );
  }

  /**
   * Custom value update handler for ACE editor
   *
   * @param value
   */
  updateHtmlValue(value) {
    const {updateField}       = this.context;

    updateField({
      value: value,
    });
  }
}
