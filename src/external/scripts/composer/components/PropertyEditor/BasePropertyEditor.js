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
import {getHandleValue} from "../../helpers/Utilities";
import {camelize} from "underscore.string";

export default class BasePropertyEditor extends Component {
  static contextTypes = {
    properties: PropTypes.shape({
      label: PropTypes.string.isRequired,
    }).isRequired,
    updateField: PropTypes.func.isRequired,
  };

  /**
   * @param props
   * @param context
   */
  constructor(props, context) {
    super(props, context);

    this.update       = this.update.bind(this);
    this.updateHandle = this.updateHandle.bind(this);
  }

  /**
   * Updates a specific property
   *
   * @param event
   */
  update(event) {
    const {updateField}       = this.context;
    const {name, value, type} = event.target;

    let postValue = value;
    switch (type) {
      case "checkbox":
        postValue = event.target.checked;
        break;
    }

    let isNumeric = false;
    if (event.target.dataset.isNumeric) {
      if (event.target.dataset.isNumeric != "false") {
        isNumeric = true;
      }
    }

    if (isNumeric) {
      postValue = (postValue+"").replace(/[^0-9\.]/, "");
      postValue = postValue ? parseInt(postValue) : 0;
    }

    updateField({
      [name]: postValue,
    });
  }

  /**
   * Updates a handle property, parsing out invalid characters
   *
   * @param event
   */
  updateHandle(event) {
    const {updateField} = this.context;
    const {name, value} = event.target;

    const handleValue = getHandleValue(value);

    updateField({[name]: handleValue});
  }
}
