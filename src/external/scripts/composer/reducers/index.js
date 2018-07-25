/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import { combineReducers } from "redux";
import { composer, context, formId } from "./Composer";
import { assetSources, fields, fileKinds, formStatuses, specialFields } from "./Fields";
import { templates } from "./FormTemplates";
import { generatedOptionLists } from "./GeneratedOptionLists";
import { integrations } from "./Integrations";
import { mailingLists } from "./MailingLists";
import { notifications } from "./Notifications";
import { placeholders } from "./Placeholders";
import { sourceTargets } from "./SourceTargets";

export default combineReducers({
  csrfToken: (state = {}) => state,
  formId,
  fields,
  specialFields,
  mailingLists,
  sourceTargets,
  formStatuses,
  generatedOptionLists,
  composer,
  context,
  notifications,
  assetSources,
  templates,
  placeholders,
  integrations,
  fileKinds,
  channelFields: (state = []) => state,
  categoryFields: (state = []) => state,
  memberFields: (state = []) => state,
});
