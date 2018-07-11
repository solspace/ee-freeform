import PropTypes       from "prop-types";
import React           from "react";
import * as FieldTypes from "../../../constants/FieldTypes";

export const FormSettings = ({ hash, integrationCount, editForm, editAdminNotifications, editIntegrations }) => (
  <div className="composer-form-settings">
    <a onClick={editForm}
       className={"btn action form-settings" + (hash === FieldTypes.FORM ? " active" : "")}
       data-icon="settings"
    >
      Form Settings
    </a>

    <a onClick={editAdminNotifications}
       className={"btn action notification-settings" + (hash === FieldTypes.ADMIN_NOTIFICATIONS ? " active" : "")}
       data-icon="mail"
    >
      Notify
    </a>

    {integrationCount ?
      (
        <a onClick={editIntegrations}
           className={"btn action crm-settings" + (hash === FieldTypes.INTEGRATION ? " active" : "")}
           data-icon="settings"
        >
          CRM
        </a>
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
