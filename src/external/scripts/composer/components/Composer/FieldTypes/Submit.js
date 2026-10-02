/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import * as SubmitPositions from "../../../constants/SubmitPositions";
import FieldHelper from "../../../helpers/FieldHelper";
import HtmlInput from "./HtmlInput";

class Submit extends HtmlInput {
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
    return "Submit";
  }

  render() {
    const { layout, properties: { labelNext, labelPrev, disablePrev } } = this.props;

    let { properties: { position } } = this.props;
    const { hash } = this.context;

    if (disablePrev) {
      const allowedPositions = [SubmitPositions.LEFT, SubmitPositions.RIGHT, SubmitPositions.CENTER];

      if (!allowedPositions.find(x => x == position)) {
        position = SubmitPositions.LEFT;
      }
    }

    const isFirstPage = FieldHelper.isFieldOnFirstPage(hash, layout);
    const showPrev = !disablePrev && !isFirstPage;

    const wrapperClass = ["composer-submit-position-wrapper", "composer-submit-position-" + position];

    return (
      <div className={wrapperClass.join(" ")}>
        {showPrev &&
        <button type="button" className="button button--secondary">{labelPrev}</button>
        }

        <button type="submit" className="button button--secondary">{labelNext}</button>
      </div>
    );
  }

  getWrapperClassNames() {
    let { properties: { position, disablePrev } } = this.props;

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

export default connect(
  state => ({
    layout: state.composer.layout,
  }),
)(Submit);
