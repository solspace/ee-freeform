/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React, {Component} from "react";
import PropTypes from "prop-types";
import HtmlInput from "./HtmlInput";
import FieldHelper from "../../../helpers/FieldHelper";
import * as SubmitPositions  from "../../../constants/SubmitPositions";
import {connect} from "react-redux";

@connect(
  state => ({
    layout: state.composer.layout,
  })
)
export default class Submit extends HtmlInput {
  static propTypes = {
    ...HtmlInput.propTypes,
    properties: PropTypes.shape({
      labelNext: PropTypes.string.isRequired,
      labelPrev: PropTypes.string.isRequired,
      disablePrev: PropTypes.bool.isRequired,
      position: PropTypes.string.isRequired,
    }),
    layout: PropTypes.array.isRequired,
  };

  static contextTypes = {
    hash: PropTypes.string.isRequired,
  };

  getClassName() {
    return 'Submit';
  }

  render() {
    const {layout, properties: {labelNext, labelPrev, disablePrev}} = this.props;

    let {properties: {position}} = this.props;
    const {hash} = this.context;

    if (disablePrev) {
      const allowedPositions = [SubmitPositions.LEFT, SubmitPositions.RIGHT, SubmitPositions.CENTER];

      if (!allowedPositions.find(x => x == position)) {
        position = SubmitPositions.LEFT;
      }
    }

    const isFirstPage = FieldHelper.isFieldOnFirstPage(hash, layout);
    const showPrev    = !disablePrev && !isFirstPage;

    const wrapperClass = ["composer-submit-position-wrapper", "composer-submit-position-" + position];

    return (
      <div className={wrapperClass.join(" ")}>
        {showPrev &&
        <input type="button" className="btn submit" value={labelPrev} />
        }

        <input type="submit" className="btn submit" value={labelNext} />
      </div>
    )
  }

  getWrapperClassNames() {
    let {properties: {position, disablePrev}} = this.props;

    if (disablePrev) {
      const allowedPositions = [SubmitPositions.LEFT, SubmitPositions.RIGHT, SubmitPositions.CENTER];

      if (!allowedPositions.find(x => x === position)) {
        position = SubmitPositions.LEFT;
      }
    }

    return [
      "composer-submit-position-wrapper",
      "composer-submit-position-" + position,
    ];
  }
}
