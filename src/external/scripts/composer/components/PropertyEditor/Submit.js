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
import BasePropertyEditor from "./BasePropertyEditor";
import TextProperty from "./PropertyItems/TextProperty";
import CheckboxProperty from "./PropertyItems/CheckboxProperty";
import PositionProperty from "./Components/Submit/PositionProperty";
import DualPositionProperty from "./Components/Submit/DualPositionProperty";
import FieldHelper from "../../helpers/FieldHelper";
import {connect} from "react-redux";

@connect(
  state => ({
    layout: state.composer.layout,
    properties: state.composer.properties,
  })
)
export default class Submit extends BasePropertyEditor {
  static propTypes = {
    layout: PropTypes.array.isRequired,
  };

  static contextTypes = {
    ...BasePropertyEditor.contextTypes,
    hash: PropTypes.string.isRequired,
    properties: PropTypes.shape({
      type: PropTypes.string.isRequired,
      label: PropTypes.string.isRequired,
      labelNext: PropTypes.string.isRequired,
      labelPrev: PropTypes.string.isRequired,
      disablePrev: PropTypes.bool.isRequired,
      position: PropTypes.string.isRequired,
    }).isRequired,
  };

  constructor(props, context) {
    super(props, context);
  }

  render() {
    const {hash, properties: {labelNext, labelPrev, disablePrev, position}} = this.context;

    const {layout} = this.props;

    const isFirstPage = FieldHelper.isFieldOnFirstPage(hash, layout);
    const showPrev    = !disablePrev && !isFirstPage;

    return (
      <div>
        <TextProperty
          label="Submit button Label"
          instructions="The label of the submit button"
          name="labelNext"
          value={labelNext}
          onChangeHandler={this.update}
        />

        {!isFirstPage &&
        <CheckboxProperty
          label="Disable the Previous button"
          name="disablePrev"
          checked={disablePrev}
          onChangeHandler={this.update}
        />
        }

        {showPrev &&
        <TextProperty
          label="Previous button Label"
          instructions="The label of the previous button"
          name="labelPrev"
          value={labelPrev}
          onChangeHandler={this.update}
        />
        }

        {!showPrev && <PositionProperty position={position} onChangeHandler={this.update} />}
        {showPrev && <DualPositionProperty position={position} onChangeHandler={this.update} />}
      </div>
    );
  }
}
