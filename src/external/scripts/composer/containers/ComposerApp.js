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
import FieldList from "../containers/FieldList";
import Composer from "../containers/Composer";
import PropertyEditor from "../containers/PropertyEditor";
import {DragDropContext} from "react-dnd";
import HTML5Backend from "react-dnd-html5-backend";
import SaveButton from "../components/SaveButton";
import Tutorials from "../components/Tutorials";

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
    canManageFields: PropTypes.bool.isRequired,
    canManageNotifications: PropTypes.bool.isRequired,
    canManageSettings: PropTypes.bool.isRequired,
    isDbEmailTemplateStorage: PropTypes.bool.isRequired,
    isWidgetsInstalled: PropTypes.bool.isRequired,
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
  });

  render() {
    const {saveUrl, formUrl, showTutorial, finishTutorialUrl} = this.props;

    return (
      <div className="builder-interface">
        <Tutorials showTutorial={showTutorial} finishTutorialUrl={finishTutorialUrl} />
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
