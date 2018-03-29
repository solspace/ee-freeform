/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import PropTypes from 'prop-types';
import React from "react";
import { connect } from "react-redux";
import { MAILING_LIST } from "../../../constants/FieldTypes";
import Badge from "./Components/Badge";
import Checkbox from "./Components/Checkbox";
import HtmlInput from "./HtmlInput";

@connect(
  (state) => ({
    hash: state.context.hash,
    composerProperties: state.composer.properties,
    mailingListIntegrations: state.mailingLists.list,
  }),
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

  getClassName() {
    return 'MailingList';
  }

  getType() {
    return MAILING_LIST;
  }

  getBadges() {
    const badges                                              = super.getBadges();
    const { properties, mailingListIntegrations }             = this.props;
    const { name, integrationId, resourceId, emailFieldHash } = properties;

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

    if (resourceName) {
      badges.push(<Badge key={resourceId} label={`"${resourceName}" list for ${name}`}/>)
    } else {
      badges.push(<Badge key="no-resource-id" label={`No mailing list for ${name}`} type={Badge.WARNING}/>)
    }

    if (!emailFieldHash) {
      badges.push(<Badge key="no-email-field-hash" label="No email field" type={Badge.WARNING}/>)
    }

    return badges;
  }

  renderInput() {
    const { properties }   = this.props;
    const { label, value } = properties;

    return (
      <Checkbox
        label={label}
        value={1}
        isChecked={!!value}
        properties={properties}
      />
    );
  }
}
