/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React, { Component } from "react";
import { DragDropContext } from "react-dnd";
import HTML5Backend from "react-dnd-html5-backend";
import SaveButton from "../components/SaveButton";
import Composer from "../containers/Composer";
import FieldList from "../containers/FieldList";
import PropertyEditor from "../containers/PropertyEditor";

@DragDropContext(HTML5Backend)
export default class ComposerApp extends Component {
  static propTypes = {
    saveUrl: PropTypes.string.isRequired,
    formUrl: PropTypes.string.isRequired,
    csrf: PropTypes.shape({
      name: PropTypes.string.isRequired,
      token: PropTypes.string.isRequired,
    }).isRequired,
    notificator: PropTypes.func.isRequired,
    createFieldUrl: PropTypes.string.isRequired,
    createNotificationUrl: PropTypes.string.isRequired,
    createTemplateUrl: PropTypes.string.isRequired,
    finishTutorialUrl: PropTypes.string.isRequired,
    showTutorial: PropTypes.bool.isRequired,
    defaultTemplates: PropTypes.bool.isRequired,
    canManageFields: PropTypes.bool.isRequired,
    canManageNotifications: PropTypes.bool.isRequired,
    canManageSettings: PropTypes.bool.isRequired,
    isDbEmailTemplateStorage: PropTypes.bool.isRequired,
    isWidgetsInstalled: PropTypes.bool.isRequired,
    formPropCleanup: PropTypes.bool.isRequired,
  };

  static childContextTypes = {
    csrf: PropTypes.shape({
      name: PropTypes.string.isRequired,
      token: PropTypes.string.isRequired,
    }).isRequired,
    notificator: PropTypes.func.isRequired,
    createFieldUrl: PropTypes.string.isRequired,
    createNotificationUrl: PropTypes.string.isRequired,
    createTemplateUrl: PropTypes.string.isRequired,
    canManageFields: PropTypes.bool.isRequired,
    canManageNotifications: PropTypes.bool.isRequired,
    canManageSettings: PropTypes.bool.isRequired,
    isDbEmailTemplateStorage: PropTypes.bool.isRequired,
    isWidgetsInstalled: PropTypes.bool.isRequired,
    isDefaultTemplates: PropTypes.bool.isRequired,
    formPropCleanup: PropTypes.bool.isRequired,
  };

  getChildContext = () => ({
    csrf: this.props.csrf,
    notificator: this.props.notificator,
    createFieldUrl: this.props.createFieldUrl,
    createNotificationUrl: this.props.createNotificationUrl,
    createTemplateUrl: this.props.createTemplateUrl,
    canManageFields: this.props.canManageFields,
    canManageNotifications: this.props.canManageNotifications,
    canManageSettings: this.props.canManageSettings,
    isDbEmailTemplateStorage: this.props.isDbEmailTemplateStorage,
    isWidgetsInstalled: this.props.isWidgetsInstalled,
    isDefaultTemplates: this.props.defaultTemplates,
    formPropCleanup: this.props.formPropCleanup,
  });

  render() {
    const { saveUrl, formUrl, showTutorial, finishTutorialUrl } = this.props;

    // <Tutorials showTutorial={showTutorial} finishTutorialUrl={finishTutorialUrl} />

    return (
      <div className="builder-interface">
        <SaveButton saveUrl={saveUrl} formUrl={formUrl} />

        <div className="builder-blocks">
          <div className="field-list">
            <FieldList />
          </div>
          <div className="builder">
            <Composer />
          </div>
          <div className="property-editor">
            <PropertyEditor />
          </div>
        </div>
      </div>
    );
  }
}
