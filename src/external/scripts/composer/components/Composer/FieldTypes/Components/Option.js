/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

import React from "react";
import PropTypes from 'prop-types';
import HtmlInput from "../HtmlInput";

export default class Radio extends HtmlInput {
    static propTypes = {
        label: PropTypes.string.isRequired,
        properties: PropTypes.object.isRequired,
    };

    getType() {
        return "option";
    }

    render() {
        const {label, value} = this.props;

        return (
            <option value={value}>{label}</option>
        );
    }
}
