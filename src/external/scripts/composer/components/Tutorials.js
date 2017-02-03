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
import Joyride from "react-joyride";
import {notificator} from "../app";

export default class Tutorials extends Component {
  static propTypes = {
    showTutorial: PropTypes.bool.isRequired,
    finishTutorialUrl: PropTypes.string.isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.handleCallbacks = this.handleCallbacks.bind(this);
  }

  componentDidMount() {
    const {showTutorial} = this.props;

    if (showTutorial) {
      this.refs.joyride.start();
    }
  }

  render() {
    const {showTutorial} = this.props;

    if (!showTutorial) {
      return null;
    }

    return (
      <div>
        <Joyride
          ref="joyride"
          steps={steps}
          debug={false}
          type="continuous"
          showSkipButton={true}
          callback={this.handleCallbacks}
        />
      </div>
    );
  }

  handleCallbacks({action, type, steps, skipped}) {
    switch (type) {
      case "finished":
        const {finishTutorialUrl} = this.props;

        fetch(finishTutorialUrl, {credentials: 'same-origin'})
          .then(response => response.json())
          .then(json => {
            if (!json.success) {
              notificator("error", "Could not finish the tutorial");
            }
          })
          .catch(exception => notificator("error", "Could not finish the tutorial"));

        break;

      default:
        return;
    }
  }
}

const steps = [
  {
    title: 'Form Settings',
    text: `Adjust all settings including return URL and formatting template for your form here. To get back here at a later time, just click the 'Form Settings' button.`,
    selector: '.form-settings',
    position: 'left',
    type: 'hover',
  },
  {
    title: 'Admin Email Notifications',
    text: `If you wish to send an email notification to admin(s) upon users successfully submitting this form, set that up here.`,
    selector: '.notification-settings',
    position: 'left',
    type: 'hover',
  },
  {
    title: 'Available Fields',
    text: `Fields are global throughout all forms, but are customizable for each form. Drag and drop any of these fields into position on the blank layout area in the center column of this page.`,
    selector: '.composer-fields',
    position: 'right',
    type: 'hover',
  },
  {
    title: 'Add New Field',
    text: `Quickly create new fields as you need them. Then adjust their properties and options in the Property Editor in the column on the right. Note: fields created here will be available for all other forms as well.`,
    selector: '.composer-add-new-field-wrapper > button',
    position: 'right',
    type: 'hover',
  },
  {
    title: 'Special Fields',
    text: `Drag and drop these when you need them. You can have as many HTML fields as you need, but should only have 1 submit button per page.`,
    selector: '.composer-special-fields',
    position: 'right',
    type: 'hover',
  },
  {
    title: 'Form Layout',
    text: 'This is a live preview of what your form will look like. Drag and drop and fields from the left column into position here. New rows and columns will automatically be created as you position the fields.',
    selector: '.builder',
    position: 'top',
    type: 'hover',
  },
  {
    title: 'Editing Fields',
    text: 'Fields can easily be moved around whenever you need. Clicking on any field will open up its properties in the Property Editor in the right column.',
    selector: '.layout',
    position: 'top',
    type: 'hover',
  },
  {
    title: 'Multi-page Forms',
    text: 'To create multi-page forms, click the + button to add more pages. You can edit the names of the pages in the Property Editor in the right column.',
    selector: '.tab-list-wrapper',
    position: 'bottom',
    type: 'hover',
  },
  {
    title: 'Property Editor',
    text: 'This is where all your configuration will happen. Clicking on any field, page tab, etc in Composer layout area will load its configuration options here.',
    selector: '.property-editor > div > hr + div',
    position: 'left',
    type: 'hover',
  },
];
