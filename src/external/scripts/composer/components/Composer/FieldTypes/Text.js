/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
import HtmlInput from "./HtmlInput";

export default class Text extends HtmlInput {
  getClassName() {
    return 'Text';
  }

  getInputClassNames() {
    return [
      'text',
      'fullwidth',
    ]
  }
}
