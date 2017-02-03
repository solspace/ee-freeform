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
