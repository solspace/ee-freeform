/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import { combineReducers } from "redux";
import { duplicateHandles } from "./DuplicateHandles";
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
  duplicateHandles,
  integrations,
  fileKinds,
  channelFields: (state = []) => state,
  categoryFields: (state = []) => state,
  memberFields: (state = []) => state,
});
