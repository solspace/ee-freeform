/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

import React     from "react";
import HtmlInput from "./HtmlInput";

export default class Password extends HtmlInput {
  getClassName() {
    return "Password";
  }

  getType() {
    return "password";
  }

  getInputClassNames() {
    return [
      "text",
      "fullwidth",
    ];
  }
}
