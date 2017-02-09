/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import {combineReducers} from "redux";
import {composer, context, formId} from "./Composer";
import {fields, specialFields, formStatuses, assetSources, fileKinds} from "./Fields";
import {notifications} from "./Notifications";
import {mailingLists} from "./MailingLists";
import {templates} from "./FormTemplates";
import {placeholders} from "./Placeholders";
import {integrations} from "./Integrations";

export default combineReducers({
  formId,
  fields,
  specialFields,
  mailingLists,
  formStatuses,
  composer,
  context,
  notifications,
  assetSources,
  templates,
  placeholders,
  integrations,
  fileKinds,
});
