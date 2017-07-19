import React, {Component} from 'react';
import PropTypes from "prop-types";
import CustomProperty from './CustomProperty';

export default class RadioProperty extends Component {
  static propTypes = {
    label: PropTypes.string.isRequired,
    instructions: PropTypes.string,
    name: PropTypes.string.isRequired,
    value: PropTypes.any.isRequired,
    options: PropTypes.arrayOf(
      PropTypes.shape({
        key: PropTypes.string.isRequired,
        value: PropTypes.any.isRequired,
      })
    ).isRequired,
    onChangeHandler: PropTypes.func.isRequired,
  };

  render() {
    const {label, instructions, name, value, options, onChangeHandler} = this.props;

    return (
      <CustomProperty
        label={label}
        instructions={instructions}
        wrapperClassName="composer-submit-positioning"
      >
        <div>
          {options.map((option, i) => (
            <div key={i}>
              <label>
                <input
                  type="radio"
                  name={name}
                  value={option.key}
                  checked={value === option.key}
                  onChange={onChangeHandler}
                />
                {option.value}
              </label>
            </div>
          ))}
        </div>
      </CustomProperty>
    );
  }
}
