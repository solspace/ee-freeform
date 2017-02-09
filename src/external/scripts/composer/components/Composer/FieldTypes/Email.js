/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component, PropTypes} from "react";
import HtmlInput from "./HtmlInput";
import * as FieldTypes from "../../../constants/FieldTypes";
import Label from "./Components/Label";
import Instructions from "./Components/Instructions";
import Badge from "./Components/Badge";

export default class Email extends HtmlInput {
  static propTypes = {
    ...HtmlInput.propTypes,
    notificationId: PropTypes.number,
  };

  getType() {
    return FieldTypes.EMAIL;
  }

  render() {
    const {properties: {label, type, required, notificationId, instructions}} = this.props;

    let badges;
    if (!notificationId) {
      badges = <Badge label="No Template" />;
    }

    return (
      <div>
        <Label label={label} type={type} isRequired={required}>{badges}</Label>
        <Instructions instructions={instructions}/>
        <input
          readOnly={true}
          className="composer-ft-text text fullwidth"
          type={this.getType()}
          {...this.getCleanProperties()}
        />
      </div>
    );
  }
}
