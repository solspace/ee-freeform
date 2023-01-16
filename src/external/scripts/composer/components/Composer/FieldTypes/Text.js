/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import React from "react";
import HtmlInput from "./HtmlInput";

export default class Text extends HtmlInput {
  getClassName() {
    return "Text";
  }

  getInputClassNames() {
    return [
      "text",
      "fullwidth",
    ];
  }
}
