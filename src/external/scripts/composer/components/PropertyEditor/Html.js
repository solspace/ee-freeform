/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React from "react";
import PropTypes from 'prop-types';
import BasePropertyEditor from "./BasePropertyEditor";
import AceEditor from "react-ace";
import TextProperty from "./PropertyItems/TextProperty";
import "brace/mode/html";
import "brace/theme/chrome";

export default class Html extends BasePropertyEditor {
  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    hash: PropTypes.string.isRequired,
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
    const {hash, properties: {value}} = this.context;

    return (
      <div>
        <TextProperty
          label="Hash"
          instructions="Used to access this field on the frontend."
          name="handle"
          value={hash}
          className="code"
          readOnly={true}
        />

        <hr />

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
