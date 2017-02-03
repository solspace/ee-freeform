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

export default class Label extends Component {
    static propTypes = {
        fieldId: PropTypes.number.isRequired,
        label: PropTypes.string.isRequired
    };

    render () {
        const {fieldId, label} = this.props;

        return (
            <label for={"composer-input-" + fieldId}>
                {label}
            </label>
        );
    }
}
