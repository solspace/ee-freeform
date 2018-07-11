/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import PropTypes from "prop-types";
import React from "react";
import BasePropertyEditor from "./BasePropertyEditor";
import TextProperty from "./PropertyItems/TextProperty";

export default class Recaptcha extends BasePropertyEditor {
  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    hash: PropTypes.string.isRequired,
  };

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
      </div>
    );
  }
}
