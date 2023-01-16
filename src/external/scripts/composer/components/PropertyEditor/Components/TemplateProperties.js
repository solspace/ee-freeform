/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import fetch from "isomorphic-fetch";
import PropTypes from "prop-types";
import React, { Component } from "react";
import ReactDOM from "react-dom";
import { connect } from "react-redux";
import { underscored } from "underscore.string";
import { fetchFormTemplatesIfNeeded, invalidateFormTemplates } from "../../../actions/FormTemplates";

@connect(
  null,
  (dispatch) => ({
    fetchTemplates: (hash, templateName) => {
      dispatch(invalidateFormTemplates());
      dispatch(fetchFormTemplatesIfNeeded(hash, templateName));
    },
  }),
)
export default class NotificationProperties extends Component {
  static initialState = {
    name: "",
    fileName: "",
    errors: [],
  };

  static propTypes = {
    toggleForm: PropTypes.func.isRequired,
    fetchTemplates: PropTypes.func.isRequired,
  };

  static contextTypes = {
    csrf: PropTypes.shape({
      name: PropTypes.string.isRequired,
      token: PropTypes.string.isRequired,
    }).isRequired,
    notificator: PropTypes.func.isRequired,
    createTemplateUrl: PropTypes.string.isRequired,
  };

  constructor(props, context) {
    super(props, context);

    this.state = NotificationProperties.initialState;
    this.updateName = this.updateName.bind(this);
    this.updateFileName = this.updateFileName.bind(this);
    this.updateState = this.updateState.bind(this);
    this.getFileName = this.getFileName.bind(this);
    this.addTemplate = this.addTemplate.bind(this);
    this.setErrors = this.setErrors.bind(this);
    this.cleanErrors = this.cleanErrors.bind(this);
  }

  componentDidMount() {
    ReactDOM.findDOMNode(this.refs.name).focus();
  }

  render() {
    const { name, fileName, errors } = this.state;
    const { toggleForm } = this.props;

    return (
      <div className="composer-new-field-form">
        <div className="field">
          <div className="heading">
            <label>Template Name</label>
          </div>
          <div className="input">
            <input type="text"
                   name="name"
                   ref="name"
                   className="text fullwidth input--small"
                   value={name}
                   onChange={this.updateName}
                   onKeyUp={this.updateState}
            />
          </div>
        </div>
        <div className="field">
          <div className="heading">
            <label>File Name</label>
          </div>
          <div className="input">
            <input type="text"
                   name="fileName"
                   ref="fileName"
                   className="text fullwidth code input--small"
                   readOnly={true}
                   disabled={true}
                   value={fileName}
            />
          </div>
        </div>

        {errors.length > 0 &&
        <div className="errors">
          {errors.map((message, index) => (<div key={index}>{message}</div>))}
        </div>
        }

        <button className="btn action cancel button--small" onClick={toggleForm}>Cancel</button>
        <button className="btn action submit button--small" onClick={this.addTemplate}>Save</button>
      </div>
    );
  }

  updateName(event) {
    const { target: { value } } = event;
    this.setState({
      name: value,
      fileName: this.getFileName(value),
    });
  }

  updateFileName(event) {
    this.setState({ handle: this.getFileName(event.target.value) });
  }

  /**
   * Checks for ESC or ENTER keypress and cancels, or tries to submit the form
   *
   * @param event
   */
  updateState(event) {
    switch (event.which) {
      case 13: // ENTER
        this.addTemplate();
        break;

      case 27: // ESC
        this.props.toggleForm();
        break;
    }
  }

  /**
   * Gets the camelized version of LABEL and sets first char as lowercase
   *
   * @param {string} value
   * @returns {string}
   */
  getFileName(value) {
    return underscored(value) + ".html";
  }

  /**
   * Adds the field via AJAX POST
   * Then triggers the fetching of fields
   *
   * @returns {boolean}
   */
  addTemplate() {
    const { name } = this.refs;
    const { toggleForm, fetchTemplates } = this.props;
    const { csrf, notificator, createTemplateUrl } = this.context;

    const nameValue = ReactDOM.findDOMNode(name).value;

    const errors = [];

    if (!nameValue) {
      errors.push("Name must not be empty");
    }

    if (errors.length) {
      this.setErrors(errors);

      return false;
    }

    const formData = new FormData();
    formData.append(csrf.name, csrf.token);
    formData.append("templateName", nameValue);

    fetch(createTemplateUrl, {
      method: "post",
      credentials: "same-origin",
      body: formData,
    })
      .then(response => response.json())
      .then(json => {
        if (json.templateName && json.success) {
          fetchTemplates("form", json.templateName);
          toggleForm();

          notificator("notice", "Template added successfully");
        } else {
          this.setErrors(json.errors);
        }
      })
      .catch(exception => this.setErrors(exception));

    return true;
  }

  setErrors(errors) {
    this.setState({ errors: errors });
  }

  cleanErrors() {
    this.setState({ errors: [] });
  }
}
