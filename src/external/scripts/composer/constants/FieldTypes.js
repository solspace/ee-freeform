/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

export const HIDDEN             = "hidden";
export const TEXT               = "text";
export const TEXTAREA           = "textarea";
export const SELECT             = "select";
export const RADIO              = "radio";
export const RADIO_GROUP        = "radio_group";
export const CHECKBOX           = "checkbox";
export const CHECKBOX_GROUP     = "checkbox_group";
export const EMAIL              = "email";
export const DYNAMIC_RECIPIENTS = "dynamic_recipients";
export const FILE               = "file";

export const DATETIME     = "datetime";
export const NUMBER       = "number";
export const PHONE        = "phone";
export const WEBSITE      = "website";
export const RATING       = "rating";
export const REGEX        = "regex";
export const CONFIRMATION = "confirmation";

export const HTML         = "html";
export const MAILING_LIST = "mailing_list";
export const SUBMIT       = "submit";

export const FORM                = "form";
export const PAGE                = "page";
export const INTEGRATION         = "integration";
export const ADMIN_NOTIFICATIONS = "admin_notifications";

export const INTEGRATION_SUPPORTED_TYPES = [
  HIDDEN,
  TEXT,
  TEXTAREA,
  SELECT,
  RADIO_GROUP,
  CHECKBOX,
  CHECKBOX_GROUP,
  EMAIL,
  DYNAMIC_RECIPIENTS,
  FILE,
  DATETIME,
  NUMBER,
  PHONE,
  WEBSITE,
  RATING,
  REGEX,
  CONFIRMATION,
];

export const CONFIRMATION_SUPPORTED_TYPES = [
  TEXT,
  EMAIL,
  DATETIME,
  NUMBER,
  PHONE,
  WEBSITE,
  REGEX,
];

export const DATE_TIME_TYPE_BOTH = 'both';
export const DATE_TIME_TYPE_DATE = 'date';
export const DATE_TIME_TYPE_TIME = 'time';
