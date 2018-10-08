/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
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
