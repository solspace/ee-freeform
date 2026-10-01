import PropTypes from "prop-types";
import React from "react";
import * as FieldTypes from "../../../constants/FieldTypes";

export const FormSettings = ({ hash, integrationCount, editForm, editAdminNotifications, editIntegrations }) => (
  <div className="composer-form-settings">
    <button type="button" onClick={editForm} aria-pressed={hash === FieldTypes.FORM}
       className={"button button--secondary form-settings" + (hash === FieldTypes.FORM ? " active" : "")}
       data-icon="settings"
       title="Form Settings"
       aria-label="Form settings"
    >
        Settings
    </button>

    <button type="button" onClick={editAdminNotifications} aria-pressed={hash === FieldTypes.ADMIN_NOTIFICATIONS}
       className={"button button--secondary notification-settings" + (hash === FieldTypes.ADMIN_NOTIFICATIONS ? " active" : "")}
       data-icon="mail"
       title="Admin Notifications"
       aria-label="Admin notifications"
    >
        Notifications
    </button>

    {integrationCount ?
      (
        <button type="button" onClick={editIntegrations} aria-pressed={hash === FieldTypes.INTEGRATION}
           className={"button button--secondary crm-settings" + (hash === FieldTypes.INTEGRATION ? " active" : "")}
           data-icon="crm"
           title="CRM"
           aria-label="CRM integrations"
        >
            CRM
        </button>
      )
      : ""}
  </div>
);

FormSettings.propTypes = {
  editForm: PropTypes.func.isRequired,
  editIntegrations: PropTypes.func.isRequired,
  editAdminNotifications: PropTypes.func.isRequired,
  hash: PropTypes.string.isRequired,
  integrationCount: PropTypes.number.isRequired,
};

export default FormSettings;
