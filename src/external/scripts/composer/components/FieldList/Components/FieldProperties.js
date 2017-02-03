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
import ReactDOM from "react-dom";
import {connect} from "react-redux";
import * as FieldTypes from "../../../constants/FieldTypes";
import {fetchFieldsIfNeeded, invalidateFields} from "../../../actions/Fields";
import fetch from "isomorphic-fetch";
import {getHandleValue} from "../../../helpers/Utilities";

@connect(
  null,
  (dispatch) => ({
    fetchFields: () => {
      dispatch(invalidateFields());
      dispatch(fetchFieldsIfNeeded());
    }
  })
)
export default class FieldProperties extends Component {
  static initialState = {
    label: "",
    handle: "",
    type: FieldTypes.TEXT,
    errors: [],
  };

  static propTypes = {
    toggleFieldForm: PropTypes.func.isRequired,
    fetchFields: PropTypes.func.isRequired,
  };

  static contextTypes = {
    csrf: PropTypes.shape({
      name: PropTypes.string.isRequired,
      token: PropTypes.string.isRequired,
    }).isRequired,
    notificator: PropTypes.func.isRequired,
    createFieldUrl: PropTypes.string.isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.state        = FieldProperties.initialState;
    this.updateLabel  = this.updateLabel.bind(this);
    this.updateHandle = this.updateHandle.bind(this);
    this.updateType   = this.updateType.bind(this);
    this.updateState  = this.updateState.bind(this);
    this.getHandle    = this.getHandle.bind(this);
    this.addField     = this.addField.bind(this);
    this.setErrors    = this.setErrors.bind(this);
    this.cleanErrors  = this.cleanErrors.bind(this);
  }

  componentDidMount() {
    ReactDOM.findDOMNode(this.refs.label).focus();
  }

  render() {
    const {label, handle, type, errors} = this.state;
    const {toggleFieldForm}             = this.props;

    return (
      <div className="composer-new-field-form">
        <div className="field">
          <div className="heading">
            <label>Type</label>
          </div>
          <div className="select">
            <select name="type"
                    value={type}
                    ref="type"
                    onChange={this.updateType}
                    className="fullwidth"
            >
              <option value={FieldTypes.TEXT}>Text</option>
              <option value={FieldTypes.TEXTAREA}>Textarea</option>
              <option value={FieldTypes.EMAIL}>Email</option>
              <option value={FieldTypes.HIDDEN}>Hidden</option>
              <option value={FieldTypes.SELECT}>Select</option>
              <option value={FieldTypes.CHECKBOX}>Checkbox</option>
              <option value={FieldTypes.CHECKBOX_GROUP}>Checkbox Group</option>
              <option value={FieldTypes.RADIO_GROUP}>Radio Group</option>
              <option value={FieldTypes.FILE}>File upload</option>
              <option value={FieldTypes.DYNAMIC_RECIPIENTS}>Dynamic Recipients</option>
            </select>
          </div>
        </div>
        <div className="field">
          <div className="heading">
            <label>Label</label>
          </div>
          <div className="input">
            <input type="text"
                   name="label"
                   ref="label"
                   className="text fullwidth"
                   value={label}
                   onChange={this.updateLabel}
                   onKeyUp={this.updateState}
            />
          </div>
        </div>
        <div className="field">
          <div className="heading">
            <label>Handle</label>
          </div>
          <div className="input">
            <input type="text"
                   name="handle"
                   ref="handle"
                   className="text fullwidth code"
                   value={handle}
                   onChange={this.updateHandle}
                   onKeyUp={this.updateState}
            />
          </div>
        </div>

        {errors.length > 0 &&
        <div className="errors">
          {errors.map((message, index) => (<div key={index}>{message}</div>))}
        </div>
        }

        <button className="btn cancel small" onClick={toggleFieldForm}>Cancel</button>
        <button className="btn submit small" onClick={this.addField}>Save</button>
      </div>
    );
  }

  updateLabel(event) {
    const {target: {value}} = event;
    this.setState({
      label: value,
      handle: this.getHandle(value),
    });
  }

  updateHandle(event) {
    this.setState({handle: this.getHandle(event.target.value)});
  }

  updateType(event) {
    this.setState({type: event.target.value});

    ReactDOM.findDOMNode(this.refs.label).focus();
  }

  /**
   * Checks for ESC or ENTER keypress and cancels, or tries to submit the form
   *
   * @param event
   */
  updateState(event) {
    switch (event.which) {
      case 13: // ENTER
        this.addField();
        break;

      case 27: // ESC
        this.props.toggleFieldForm();
        break;
    }
  }

  /**
   * Gets the camelized version of LABEL and sets first char as lowercase
   *
   * @param {string} value
   * @returns {string}
   */
  getHandle(value) {
    return getHandleValue(value);
  }

  /**
   * Adds the field via AJAX POST
   * Then triggers the fetching of fields
   *
   * @returns {boolean}
   */
  addField() {
    const {label, handle, type}               = this.refs;
    const {toggleFieldForm, fetchFields}      = this.props;
    const {csrf, notificator, createFieldUrl} = this.context;

    const labelValue  = ReactDOM.findDOMNode(label).value;
    const handleValue = ReactDOM.findDOMNode(handle).value;
    const typeValue   = ReactDOM.findDOMNode(type).value;

    const errors = [];

    if (!labelValue) {
      errors.push("Label must not be empty");
    }

    if (!handleValue) {
      errors.push("Handle must not be empty");
    }

    if (!typeValue) {
      errors.push("Field type must not be empty");
    }

    if (errors.length) {
      this.setErrors(errors);

      return false;
    }

    const formData = new FormData();
    formData.append(csrf.name, csrf.token);
    formData.append("label", labelValue);
    formData.append("handle", handleValue);
    formData.append("type", typeValue);

    fetch(createFieldUrl, {
      method: "post",
      credentials: 'same-origin',
      body: formData,
    })
      .then(response => response.json())
      .then(json => {
        if (json.success) {
          fetchFields();
          toggleFieldForm();

          notificator("notice", "Field added successfully");
        } else {
          this.setErrors(json.errors);
        }
      })
      .catch(exception => this.setErrors(exception));

    return true;
  }

  setErrors(errors) {
    this.setState({errors: errors});
  }

  cleanErrors() {
    this.setState({errors: []});
  }
}
