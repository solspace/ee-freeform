/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
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
