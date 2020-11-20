/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import * as ExternalOptions from "../../../constants/ExternalOptions";
import { RADIO_GROUP } from "../../../constants/FieldTypes";
import Radio from "./Components/Radio";
import HtmlInput from "./HtmlInput";

@connect(
  (state) => ({
    hash: state.context.hash,
    composerProperties: state.composer.properties,
    isFetchingOptions: state.generatedOptionLists.isFetching,
    generatedOptions: state.generatedOptionLists.cache,
  }),
)
export default class RadioGroup extends HtmlInput {
  static propTypes = {
    properties: PropTypes.shape({
      hash: PropTypes.string.isRequired,
      label: PropTypes.node.isRequired,
      required: PropTypes.bool.isRequired,
      options: PropTypes.array,
      value: PropTypes.node,
    }).isRequired,
    isFetchingOptions: PropTypes.bool.isRequired,
  };

  cachedOptions = null;

  getClassName() {
    return "RadioGroup";
  }

  getType() {
    return RADIO_GROUP;
  }

  renderInput() {
    const { properties, generatedOptions, isFetchingOptions } = this.props;
    const { options, source, hash } = properties;

    if (isFetchingOptions && this.cachedOptions) {
      return this.cachedOptions;
    }

    let listOptions = [];
    if (!source || source === ExternalOptions.SOURCE_CUSTOM) {
      listOptions = options;
    } else if (generatedOptions && generatedOptions[hash]) {
      listOptions = generatedOptions[hash];
    }


    let radios = [];
    if (listOptions) {
      for (let i = 0; i < listOptions.length; i++) {
        const { label, value } = listOptions[i];

        radios.push(
          <Radio
            key={i}
            label={label + ""}
            value={value + ""}
            isChecked={(value + "") === (this.props.properties.value + "")}
            properties={properties} />,
        );
      }
    }

    this.cachedOptions = radios;

    return radios;
  }
}
