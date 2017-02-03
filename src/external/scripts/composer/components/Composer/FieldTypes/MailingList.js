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
import {MAILING_LIST} from "../../../constants/FieldTypes";
import HtmlInput from "./HtmlInput";
import Checkbox from "./Components/Checkbox";
import Label from "./Components/Label";
import Instructions from "./Components/Instructions";
import Badge from "./Components/Badge";
import {connect} from "react-redux";

@connect(
  (state) => ({
    hash: state.context.hash,
    composerProperties: state.composer.properties,
    mailingListIntegrations: state.mailingLists.list,
  })
)
export default class MailingList extends HtmlInput {
  static propTypes = {
    mailingListIntegrations: PropTypes.array.isRequired,
    hash: PropTypes.string,
    properties: PropTypes.shape({
      integrationId: PropTypes.number.isRequired,
      resourceId: PropTypes.node,
      emailFieldHash: PropTypes.node,
      name: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
    }).isRequired,
  };

  getType() {
    return MAILING_LIST;
  }

  render() {
    const {properties, mailingListIntegrations} = this.props;

    const {name, label, value, integrationId, resourceId, emailFieldHash, instructions} = properties;

    let resourceName = "";
    if (resourceId) {
      for (let integration of mailingListIntegrations) {
        if (integrationId === integration.integrationId) {
          for (let list of integration.lists) {
            if (list.id.toString() === resourceId.toString()) {
              resourceName = list.name;
              break;
            }
          }
        }
      }
    }

    const badges = [];

    if (resourceName) {
      badges.push(<Badge key={resourceId} label={`"${resourceName}" list for ${name}`} />)
    } else {
      badges.push(<Badge key="no-resource-id" label={`No mailing list for ${name}`} type={Badge.WARNING} />)
    }

    if (!emailFieldHash) {
      badges.push(<Badge key="no-email-field-hash" label="No email field" type={Badge.WARNING} />)
    }

    return (
      <div>
        <Label>{badges}</Label>
        <Instructions instructions={instructions}/>
        <Checkbox
          label={label}
          value={1}
          isChecked={!!value}
          properties={properties}
        />
      </div>
    );
  }
}
