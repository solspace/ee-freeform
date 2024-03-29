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
import React from "react";
import { connect } from "react-redux";
import * as ExternalOptions from "../../../constants/ExternalOptions";
import { SELECT } from "../../../constants/FieldTypes";
import Option from "./Components/Option";
import HtmlInput from "./HtmlInput";

@connect(
  (state) => ({
    globalProps: state.composer.properties,
    isFetchingOptions: state.generatedOptionLists.isFetching,
    generatedOptions: state.generatedOptionLists.cache,
  }),
)
export default class Select extends HtmlInput {
  static propTypes = {
    properties: PropTypes.shape({
      hash: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      required: PropTypes.bool.isRequired,
      options: PropTypes.array,
      value: PropTypes.node,
    }).isRequired,
    isFetchingOptions: PropTypes.bool.isRequired,
  };

  cachedOptions = null;

  getClassName() {
    return "Select";
  }

  getType() {
    return SELECT;
  }

  renderInput() {
    const { properties, generatedOptions, isFetchingOptions } = this.props;
    const { options = [], source, hash } = properties;

    if (isFetchingOptions && this.cachedOptions) {
      return this.cachedOptions;
    }

    let listOptions = [];
    if (!source || source === ExternalOptions.SOURCE_CUSTOM) {
      listOptions = options;
    } else if (generatedOptions && generatedOptions[hash]) {
      listOptions = generatedOptions[hash];
    }

    if (!listOptions) {
      return (
        <div className="select">
          <select
            className={this.prepareInputClass()}
            readOnly={true}
            disabled={true}
          />
        </div>
      );
    }

    let selectOptions = [];
    for (let i = 0; i < listOptions.length; i++) {
      const { label, value } = listOptions[i];

      selectOptions.push(
        <Option
          key={i}
          label={label + ""}
          value={value + ""}
          properties={properties}
        />,
      );
    }

    const field = (
      <div className="select">
        <select
          className={this.prepareInputClass()}
          readOnly={true}
          disabled={true}
          value={this.props.properties.value}
        >
          {selectOptions}
        </select>
      </div>
    );

    this.cachedOptions = field;

    return field;
  }
}
