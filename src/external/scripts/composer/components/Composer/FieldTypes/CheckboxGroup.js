/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import * as ExternalOptions from "../../../constants/ExternalOptions";
import { CHECKBOX_GROUP } from "../../../constants/FieldTypes";
import Checkbox from "./Components/Checkbox";
import HtmlInput from "./HtmlInput";

@connect(
  (state) => ({
    hash: state.context.hash,
    composerProperties: state.composer.properties,
    isFetchingOptions: state.generatedOptionLists.isFetching,
    generatedOptions: state.generatedOptionLists.cache,
  }),
)
export default class CheckboxGroup extends HtmlInput {
  static propTypes = {
    properties: PropTypes.shape({
      hash: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      required: PropTypes.bool.isRequired,
      options: PropTypes.array,
      values: PropTypes.array,
      source: PropTypes.string,
    }).isRequired,
    isFetchingOptions: PropTypes.bool.isRequired,
  };

  cachedOptions = null;

  getClassName() {
    return "CheckboxGroup";
  }

  getType() {
    return CHECKBOX_GROUP;
  }

  renderInput() {
    const { properties, generatedOptions, isFetchingOptions } = this.props;
    const { options, values, source, hash } = properties;

    if (isFetchingOptions && this.cachedOptions) {
      return this.cachedOptions;
    }

    let listOptions = [];
    if (!source || source === ExternalOptions.SOURCE_CUSTOM) {
      listOptions = options;
    } else if (generatedOptions && generatedOptions[hash]) {
      listOptions = generatedOptions[hash];
    }

    let checkboxes = [];
    if (listOptions) {
      for (let i = 0; i < listOptions.length; i++) {
        const { label, value } = listOptions[i];

        checkboxes.push(
          <Checkbox
            key={i}
            label={label}
            value={value}
            isChecked={values ? (values.indexOf(value) !== -1) : false}
            properties={properties} />,
        );
      }
    }

    this.cachedOptions = checkboxes;

    return checkboxes;
  }
}
