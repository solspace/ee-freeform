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
import {connect} from "react-redux";
import Field from "./Field";
import FieldHelper from "../../helpers/FieldHelper";

@connect(state => ({
    currentPage: state.context.page
}))
export default class SpecialFieldGroup extends Component {
    static propTypes = {
        fields: PropTypes.arrayOf(
            PropTypes.shape({
                type: PropTypes.string.isRequired,
                label: PropTypes.string.isRequired,
            }).isRequired
        ).isRequired,
        onFieldClick: PropTypes.func,
        currentPage: PropTypes.number.isRequired
    };

    render() {
        const {fields, currentPage, onFieldClick} = this.props;

        return (
            <div className="composer-special-fields">
                <h3>Special Fields</h3>
                <ul>
                    {fields.map((field, index) =>
                        <Field
                            key={index}
                            {...field}
                            isUsed={false}
                            onClick={() => onFieldClick(FieldHelper.hashField(field), field, currentPage)}
                        />
                    )}
                </ul>
            </div>
        )
    }
}
