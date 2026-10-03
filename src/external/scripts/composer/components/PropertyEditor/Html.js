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
import { mountHtmlEditor } from "../../../shared/htmlEditor";
import BasePropertyEditor from "./BasePropertyEditor";
import TextProperty from "./PropertyItems/TextProperty";

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

  componentDidMount() {
    this.editor = mountHtmlEditor(this.editorHost, {
      value: this.context.properties.value || "",
      onChange: this.updateHtmlValue,
      compact: true,
      label: "HTML field content",
    });
  }

  componentDidUpdate() {
    this.editor.setValue(this.context.properties.value || "");
  }

  componentWillUnmount() {
    this.editor.destroy();
  }

  render() {
    const { hash } = this.context;

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

        <div className="freeform-html-editor-host" ref={(node) => { this.editorHost = node; }} />
      </div>
    );
  }

  /**
   * Sync HTML source edits with the builder field properties.
   *
   * @param value
   */
  updateHtmlValue(value) {
    const { updateField } = this.context;

    updateField({
      value: value,
    });
  }
}
