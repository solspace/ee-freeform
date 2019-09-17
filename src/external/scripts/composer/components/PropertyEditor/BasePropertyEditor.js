/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React, { Component } from "react";
import { getHandleValue } from "../../helpers/Utilities";

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

    this.update = this.update.bind(this);
    this.updateHandle = this.updateHandle.bind(this);
    this.updateKeyValue = this.updateKeyValue.bind(this);
  }

  /**
   * Updates a specific property
   *
   * @param event
   */
  update(event) {
    const { updateField } = this.context;
    const { name, value, type } = event.target;

    let postValue = value;
    switch (type) {
      case "checkbox":
        postValue = event.target.checked;
        break;
    }

    let isNumeric = false;
    if (event.target.dataset.isNumeric) {
      if (event.target.dataset.isNumeric !== "false") {
        isNumeric = true;
      }
    }

    if (isNumeric) {
      postValue = (postValue + "").replace(/[^0-9\.]/, "");
      postValue = postValue ? parseInt(postValue) : 0;
    }

    let couldBeNumeric = false;
    if (event.target.dataset.couldBeNumeric) {
      if (event.target.dataset.couldBeNumeric !== "false") {
        couldBeNumeric = true;
      }
    }

    if (couldBeNumeric) {
      if (/^[0-9]+$/.test(postValue)) {
        postValue = postValue ? parseInt(postValue) : 0;
      }
    }

    let isNullable = false;
    if (event.target.dataset.nullable) {
      if (event.target.dataset.nullable !== "false") {
        isNullable = true;
      }
    }

    if (isNullable) {
      postValue = postValue !== "" ? postValue : null;
    }

    updateField({ [name]: postValue });
  }

  /**
   * Updates a handle property, parsing out invalid characters
   *
   * @param event
   */
  updateHandle(event) {
    const { updateField } = this.context;
    const { name, value } = event.target;

    const handleValue = getHandleValue(value, false);

    updateField({ [name]: handleValue });
  }

  /**
   * Updates key and value manually
   *
   * @param key
   * @param value
   */
  updateKeyValue(key, value) {
    const { updateField } = this.context;

    updateField({ [key]: value });
  }
}
