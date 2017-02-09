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
import * as FieldTypes from "../../constants/FieldTypes";
import Checkbox from "./FieldTypes/CheckboxField";
import CheckboxGroup from "./FieldTypes/CheckboxGroup";
import Text from "./FieldTypes/Text";
import Textarea from "./FieldTypes/Textarea";
import Email from "./FieldTypes/Email";
import Hidden from "./FieldTypes/Hidden";
import Html from "./FieldTypes/Html";
import Submit from "./FieldTypes/Submit";
import RadioGroup from "./FieldTypes/RadioGroup";
import Select from "./FieldTypes/Select";
import DynamicRecipients from "./FieldTypes/DynamicRecipients";
import MailingList from "./FieldTypes/MailingList";
import File from "./FieldTypes/File";

export default class Field extends Component {
    static propTypes = {
        type: PropTypes.string.isRequired,
        properties: PropTypes.shape({
            type: PropTypes.string.isRequired,
            id: PropTypes.number,
            placeholder: PropTypes.string
        }).isRequired,
        hash: PropTypes.string.isRequired,
        index: PropTypes.number.isRequired,
        rowIndex: PropTypes.number.isRequired,
    };

    static childContextTypes = {
        hash: PropTypes.string.isRequired,
        index: PropTypes.number.isRequired,
        rowIndex: PropTypes.number.isRequired,
    };

    getChildContext = () => ({
        hash: this.props.hash,
        index: this.props.index,
        rowIndex: this.props.rowIndex,
    });

    render() {
        const {type, properties} = this.props;

        switch (type) {
            case FieldTypes.TEXT:
                return <Text properties={properties} />;

            case FieldTypes.TEXTAREA:
                return <Textarea properties={properties} />;

            case FieldTypes.EMAIL:
                return <Email properties={properties} />;

            case FieldTypes.HIDDEN:
                return <Hidden properties={properties} />;

            case FieldTypes.CHECKBOX:
                return <Checkbox properties={properties} />;

            case FieldTypes.CHECKBOX_GROUP:
                return <CheckboxGroup properties={properties} />;

            case FieldTypes.RADIO:
                return <CheckboxGroup properties={properties} />;

            case FieldTypes.HTML:
                return <Html properties={properties} />;

            case FieldTypes.SUBMIT:
                return <Submit properties={properties} />;

            case FieldTypes.RADIO_GROUP:
                return <RadioGroup properties={properties} />;

            case FieldTypes.SELECT:
                return <Select properties={properties} />;

            case FieldTypes.DYNAMIC_RECIPIENTS:
                return <DynamicRecipients properties={properties} />;

            case FieldTypes.MAILING_LIST:
                return <MailingList properties={properties} />;

            case FieldTypes.FILE:
                return <File properties={properties} />;

            default:
                return <div>Field type "{type}" not found</div>;
        }
    }
}
