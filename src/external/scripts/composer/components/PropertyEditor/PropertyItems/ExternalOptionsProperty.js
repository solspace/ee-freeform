import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { fetchGeneratedOptionsIfNeeded, invalidateGeneratedOptions } from "../../../actions/GeneratedOptionLists";
import * as ExternalOptions from "../../../constants/ExternalOptions";
import OptionTable from "../Components/OptionTable/OptionTable";
import PredefinedOptionTable from "../Components/OptionTable/PredefinedOptionTable";
import BasePropertyItem from "./BasePropertyItem";
import CustomProperty from "./CustomProperty";
import SelectProperty from "./SelectProperty";
import TextProperty from "./TextProperty";

const initialState = {
  emptyOption: "",
};

@connect(
  (state) => ({
    sourceTargets: state.sourceTargets,
    customFields: state.customFields,
    isFetchingOptions: state.generatedOptionLists.isFetching,
    generatedOptions: state.generatedOptionLists.cache,
    channelFields: state.channelFields,
    categoryFields: state.categoryFields,
    memberFields: state.memberFields,
  }),
  (dispatch) => ({
    fetchGeneratedOptions: (hash, source, target, configuration) => {
      dispatch(invalidateGeneratedOptions(hash));
      dispatch(fetchGeneratedOptionsIfNeeded(hash, source, target, configuration));
    },
  })
)
export default class ExternalOptionsProperty extends BasePropertyItem {
  static propTypes = {
    ...BasePropertyItem.propTypes,
    label: PropTypes.string,
    source: PropTypes.string,
    target: PropTypes.node,
    showEmptyOptionInput: PropTypes.bool,
    configuration: PropTypes.shape({
      labelField: PropTypes.string,
      valueField: PropTypes.string,
      start: PropTypes.number,
      end: PropTypes.number,
      listType: PropTypes.string,
      valueType: PropTypes.string,
      emptyOption: PropTypes.string,
    }),
    showCustomValues: PropTypes.bool,
    customOptions: PropTypes.arrayOf(
      PropTypes.shape({
        value: PropTypes.any.isRequired,
        label: PropTypes.any.isRequired,
      }),
    ),
    sourceTargets: PropTypes.object,
    customFields: PropTypes.array,
    fetchGeneratedOptions: PropTypes.func.isRequired,
    isFetchingOptions: PropTypes.bool.isRequired,
    generatedOptions: PropTypes.object,
    channelFields: PropTypes.array,
    categoryFields: PropTypes.array,
    memberFields: PropTypes.array,
  };

  static contextTypes = {
    updateField: PropTypes.func.isRequired,
    hash: PropTypes.string,
  };

  static sourceOptions = [
    { key: ExternalOptions.SOURCE_CUSTOM, value: "Custom Options" },
    { key: ExternalOptions.SOURCE_ENTRIES, value: "Entries" },
    { key: ExternalOptions.SOURCE_CATEGORIES, value: "Categories" },
    { key: ExternalOptions.SOURCE_MEMBERS, value: "Members" },
    { key: ExternalOptions.SOURCE_PREDEFINED, value: "Predefined Options" },
  ];

  lastOptions = null;
  updateEmptyOptionTrigger = null;

  constructor(props, context) {
    super(props, context);

    this.state = {
      ...initialState,
      emptyOption: this.getConfigProperty("emptyOption", ""),
    };

    this.getDisplayProperties = this.getDisplayProperties.bind(this);
    this.getConfigProperty = this.getConfigProperty.bind(this);
    this.getExternalSourceComponents = this.getExternalSourceComponents.bind(this);
    this.getCustomValuesComponent = this.getCustomValuesComponent.bind(this);
    this.getPredefinedValuesComponent = this.getPredefinedValuesComponent.bind(this);
    this.getGeneratedOptions = this.getGeneratedOptions.bind(this);
    this.onChangeSource = this.onChangeSource.bind(this);
    this.onChangePredefinedTarget = this.onChangePredefinedTarget.bind(this);
    this.onUpdateConfig = this.onUpdateConfig.bind(this);
    this.onUpdateEmptyOption = this.onUpdateEmptyOption.bind(this);
    this.persistEmptyOption = this.persistEmptyOption.bind(this);
    this.onChangeTarget = this.onChangeTarget.bind(this);
  }

  render() {
    const { source = ExternalOptions.SOURCE_CUSTOM } = this.props;

    const displayProperties = this.getDisplayProperties();

    return (
      <div className="field">
        <SelectProperty
          label='Source'
          name='source'
          className='button--small button--block'
          value={source}
          options={ExternalOptionsProperty.sourceOptions}
          onChangeHandler={this.onChangeSource}
        />
        {displayProperties}
      </div>
    );
  }

  /**
   * Returns a renderable component based on SOURCE, TARGET and CONFIGURATION
   */
  getDisplayProperties() {
    const { source = ExternalOptions.SOURCE_CUSTOM } = this.props;

    switch (source) {
      case ExternalOptions.SOURCE_ENTRIES:
      case ExternalOptions.SOURCE_CATEGORIES:
      case ExternalOptions.SOURCE_MEMBERS:
        return this.getExternalSourceComponents();

      case ExternalOptions.SOURCE_CUSTOM:
        return this.getCustomValuesComponent();

      default:
        return this.getPredefinedValuesComponent();
    }
  }

  /**
   * @returns {*}
   */
  getExternalSourceComponents() {
    const { target = null, source, sourceTargets } = this.props;
    const { showEmptyOptionInput } = this.props;
    const { channelFields, categoryFields, memberFields } = this.props;
    const list = sourceTargets[source];

    let defaultLabelField, defaultValueField, fields = [];
    switch (source) {
      case ExternalOptions.SOURCE_MEMBERS:
        defaultLabelField = "username";
        defaultValueField = "member_id";
        fields = memberFields;
        break;

      case ExternalOptions.SOURCE_CATEGORIES:
        defaultLabelField = "cat_name";
        defaultValueField = "cat_id";
        fields = categoryFields;
        break;

      default:
        defaultLabelField = "title";
        defaultValueField = "entry_id";
        fields = channelFields;
        break;
    }

    let emptyOptionComponent = null;
    if (showEmptyOptionInput) {
      emptyOptionComponent = <TextProperty
        label="Empty Option Label (optional)"
        instructions="To show an empty option at the beginning of the Select field options, enter a value here. Leave blank if you don't want a first option."
        name="emptyOption"
        value={this.state.emptyOption}
        onChangeHandler={this.onUpdateEmptyOption}
      />;
    }

    return (
      <div>
        {emptyOptionComponent}

        <SelectProperty
          label='Target'
          name='target'
          className='button--small button--block'
          value={target}
          options={list}
          onChangeHandler={this.onChangeTarget}
        />

        <SelectProperty
          label='Option Label'
          name='labelField'
          className='button--small button--block'
          value={this.getConfigProperty("labelField", defaultLabelField)}
          options={fields}
          onChangeHandler={this.onUpdateConfig}
        />

        <SelectProperty
          label='Option Value'
          name='valueField'
          className='button--small button--block'
          value={this.getConfigProperty("valueField", defaultValueField)}
          options={fields}
          onChangeHandler={this.onUpdateConfig}
        />

        {this.getGeneratedOptions()}
      </div>
    );
  }

  /**
   * @returns {*}
   */
  getCustomValuesComponent() {
    const { values, value, customOptions, updateHandler, showCustomValues } = this.props;

    return (
      <CustomProperty
        label="Options"
        instructions="Options for this field"
        content={
          <OptionTable
            value={value}
            values={values}
            options={customOptions}
            triggerCustomValues={updateHandler}
            showCustomValues={showCustomValues}
          />
        }
      />
    );
  }

  getPredefinedValuesComponent() {
    const { target = null, showEmptyOptionInput = false } = this.props;

    let specificOptions = null;
    switch (target) {
      case ExternalOptions.PREDEFINED_STATES:
      case ExternalOptions.PREDEFINED_STATES_TERRITORIES:
      case ExternalOptions.PREDEFINED_PROVINCES:
      case ExternalOptions.PREDEFINED_COUNTRIES:
      case ExternalOptions.PREDEFINED_LANGUAGES:
        specificOptions = (
          <div>
            <SelectProperty
              label="Option Label"
              name="listType"
              className='button--small button--block'
              options={[
                { key: ExternalOptions.TYPE_ABBREVIATED, value: "Abbreviated" },
                { key: ExternalOptions.TYPE_FULL, value: "Full" },
              ]}
              value={this.getConfigProperty("listType", ExternalOptions.TYPE_FULL)}
              onChangeHandler={this.onUpdateConfig}
            />

            <SelectProperty
              label="Option Value"
              name="valueType"
              className='button--small button--block'
              options={[
                { key: ExternalOptions.TYPE_ABBREVIATED, value: "Abbreviated" },
                { key: ExternalOptions.TYPE_FULL, value: "Full" },
              ]}
              value={this.getConfigProperty("valueType", ExternalOptions.TYPE_ABBREVIATED)}
              onChangeHandler={this.onUpdateConfig}
            />
          </div>
        );

        break;

      case ExternalOptions.PREDEFINED_NUMBERS:
        specificOptions = (
          <div>
            <TextProperty
              label="Range Start"
              name="start"
              isNumeric={true}
              value={this.getConfigProperty("start", ExternalOptions.DEFAULT_NUMBERS_RANGE_START)}
              onChangeHandler={this.onUpdateConfig}
            />

            <TextProperty
              label="Range End"
              name="end"
              isNumeric={true}
              value={this.getConfigProperty("end", ExternalOptions.DEFAULT_NUMBERS_RANGE_END)}
              onChangeHandler={this.onUpdateConfig}
            />
          </div>
        );

        break;

      case ExternalOptions.PREDEFINED_YEARS:
        specificOptions = (
          <div>
            <TextProperty
              label="Range Start"
              name="start"
              isNumeric={true}
              value={this.getConfigProperty("start", ExternalOptions.DEFAULT_YEAR_RANGE_START)}
              onChangeHandler={this.onUpdateConfig}
            />

            <TextProperty
              label="Range End"
              name="end"
              isNumeric={true}
              value={this.getConfigProperty("end", ExternalOptions.DEFAULT_YEAR_RANGE_END)}
              onChangeHandler={this.onUpdateConfig}
            />

            <SelectProperty
              label="Sort Direction"
              name="sort"
              options={[
                { key: ExternalOptions.SORT_ASC, value: "Ascending" },
                { key: ExternalOptions.SORT_DESC, value: "Descending" },
              ]}
              value={this.getConfigProperty("sort", ExternalOptions.DEFAULT_YEAR_SORT)}
              onChangeHandler={this.onUpdateConfig}
            />
          </div>
        );

        break;

      case ExternalOptions.PREDEFINED_MONTHS:
        specificOptions = (
          <div>
            <SelectProperty
              label="Option Label"
              name="listType"
              className='button--small button--block'
              options={[
                { key: ExternalOptions.TYPE_FULL, value: "Full" },
                { key: ExternalOptions.TYPE_ABBREVIATED, value: "Abbreviated" },
                { key: ExternalOptions.TYPE_INT, value: "Single number" },
                { key: ExternalOptions.TYPE_INT_LEADING_ZERO, value: "2-digit number" },
              ]}
              value={this.getConfigProperty("listType", ExternalOptions.TYPE_FULL)}
              onChangeHandler={this.onUpdateConfig}
            />
            <SelectProperty
              label="Option Value"
              name="valueType"
              className='button--small button--block'
              options={[
                { key: ExternalOptions.TYPE_FULL, value: "Full" },
                { key: ExternalOptions.TYPE_ABBREVIATED, value: "Abbreviated" },
                { key: ExternalOptions.TYPE_INT, value: "Single number" },
                { key: ExternalOptions.TYPE_INT_LEADING_ZERO, value: "2-digit number" },
              ]}
              value={this.getConfigProperty("valueType", ExternalOptions.TYPE_FULL)}
              onChangeHandler={this.onUpdateConfig}
            />
          </div>
        );

        break;

      case ExternalOptions.PREDEFINED_DAYS:
        specificOptions = (
          <div>
            <SelectProperty
              label="Option Label"
              name="listType"
              className='button--small button--block'
              options={[
                { key: ExternalOptions.TYPE_INT, value: "Single number" },
                { key: ExternalOptions.TYPE_INT_LEADING_ZERO, value: "2-digit number" },
              ]}
              value={this.getConfigProperty("listType", ExternalOptions.TYPE_INT)}
              onChangeHandler={this.onUpdateConfig}
            />
            <SelectProperty
              label="Option Value"
              name="valueType"
              className='button--small button--block'
              options={[
                { key: ExternalOptions.TYPE_INT, value: "Single number" },
                { key: ExternalOptions.TYPE_INT_LEADING_ZERO, value: "2-digit number" },
              ]}
              value={this.getConfigProperty("valueType", ExternalOptions.TYPE_INT)}
              onChangeHandler={this.onUpdateConfig}
            />
          </div>
        );

        break;

      case ExternalOptions.PREDEFINED_DAYS_OF_WEEK:
        specificOptions = (
          <div>
            <SelectProperty
              label="Option Label"
              name="listType"
              className='button--small button--block'
              options={[
                { key: ExternalOptions.TYPE_FULL, value: "Full" },
                { key: ExternalOptions.TYPE_ABBREVIATED, value: "Abbreviated" },
                { key: ExternalOptions.TYPE_INT, value: "Single number" },
              ]}
              value={this.getConfigProperty("listType", ExternalOptions.TYPE_FULL)}
              onChangeHandler={this.onUpdateConfig}
            />
            <SelectProperty
              label="Option Value"
              name="valueType"
              className='button--small button--block'
              options={[
                { key: ExternalOptions.TYPE_FULL, value: "Full" },
                { key: ExternalOptions.TYPE_ABBREVIATED, value: "Abbreviated" },
                { key: ExternalOptions.TYPE_INT, value: "Single number" },
              ]}
              value={this.getConfigProperty("valueType", ExternalOptions.TYPE_FULL)}
              onChangeHandler={this.onUpdateConfig}
            />
          </div>
        );

        break;
    }

    let emptyOptionComponent = null;
    if (showEmptyOptionInput) {
      emptyOptionComponent = <TextProperty
        label="Empty Option Label (optional)"
        instructions="To show an empty option at the beginning of the Select field options, enter a value here. Leave blank if you don't want a first option."
        name="emptyOption"
        value={this.state.emptyOption}
        onChangeHandler={this.onUpdateEmptyOption}
      />;
    }

    return (
      <div>
        {emptyOptionComponent}

        <SelectProperty
          label='Target'
          name='target'
          className='button--small button--block'
          value={target}
          options={[
            { key: ExternalOptions.PREDEFINED_STATES, value: "States" },
            { key: ExternalOptions.PREDEFINED_STATES_TERRITORIES, value: "States & Territories" },
            { key: ExternalOptions.PREDEFINED_PROVINCES, value: "Provinces" },
            { key: ExternalOptions.PREDEFINED_COUNTRIES, value: "Countries" },
            { key: ExternalOptions.PREDEFINED_LANGUAGES, value: "Languages" },
            { key: ExternalOptions.PREDEFINED_NUMBERS, value: "Numbers" },
            { key: ExternalOptions.PREDEFINED_YEARS, value: "Years" },
            { key: ExternalOptions.PREDEFINED_MONTHS, value: "Months" },
            { key: ExternalOptions.PREDEFINED_DAYS, value: "Days" },
            { key: ExternalOptions.PREDEFINED_DAYS_OF_WEEK, value: "Days of Week" },
          ]}
          onChangeHandler={this.onChangePredefinedTarget}
        />

        {specificOptions}
        {this.getGeneratedOptions()}
      </div>
    );
  }

  /**
   * @returns {*}
   */
  getGeneratedOptions() {
    const { values, value, generatedOptions, isFetchingOptions } = this.props;
    const { hash } = this.context;

    if (isFetchingOptions && this.lastOptions) {
      return this.lastOptions;
    }

    const options = [];
    if (generatedOptions && generatedOptions[hash]) {
      for (const item of generatedOptions[hash]) {
        options.push({
          value: item.value,
          label: item.label,
        });
      }
    }

    const optionsProperty = (
      <CustomProperty
        label="Options"
        instructions="Options for this field"
        content={
          <PredefinedOptionTable
            value={value}
            values={values}
            options={options}
          />
        }
      />
    );

    this.lastOptions = optionsProperty;

    return optionsProperty;
  }

  /**
   * @param prop
   * @param defaultValue
   * @returns {*}
   */
  getConfigProperty(prop, defaultValue = null) {
    const { configuration = {} } = this.props;

    if (configuration && configuration.hasOwnProperty(prop)) {
      return configuration[prop];
    }

    return defaultValue;
  }

  /**
   * @param event
   */
  onChangeSource(event) {
    const { updateField, hash } = this.context;
    const { fetchGeneratedOptions } = this.props;
    const { value } = event.target;
    const { emptyOption } = this.state;

    let options = {};

    switch (value) {
      case ExternalOptions.SOURCE_ENTRIES:
      case ExternalOptions.SOURCE_CATEGORIES:
      case ExternalOptions.SOURCE_MEMBERS:
        options = {
          source: value,
          target: null,
          configuration: {
            emptyOption,
          },
        };

        break;

      case ExternalOptions.SOURCE_PREDEFINED:
        options = {
          source: value,
          target: ExternalOptions.PREDEFINED_STATES,
          configuration: {
            valueType: ExternalOptions.TYPE_ABBREVIATED,
            listType: ExternalOptions.TYPE_FULL,
            emptyOption,
          },
        };

        break;

      default:
        options = {
          source: ExternalOptions.SOURCE_CUSTOM,
          target: null,
          configuration: null,
        };

        break;
    }

    updateField({
      value: "",
      values: [],
      ...options,
    });
    if (value !== ExternalOptions.SOURCE_CUSTOM) {
      fetchGeneratedOptions(hash, options.source, options.target, options.configuration);
    }
  }

  onChangeTarget(event) {
    const { hash } = this.context;
    const { fetchGeneratedOptions, configuration, source, onChangeHandler } = this.props;
    const { value } = event.target;

    onChangeHandler(event);
    fetchGeneratedOptions(hash, source, value, configuration);
  }

  onChangePredefinedTarget(event) {
    const { updateField, hash } = this.context;
    const { source, fetchGeneratedOptions } = this.props;
    const { value } = event.target;

    let updatedConfiguration = {};
    switch (value) {
      case ExternalOptions.PREDEFINED_STATES:
      case ExternalOptions.PREDEFINED_STATES_TERRITORIES:
      case ExternalOptions.PREDEFINED_PROVINCES:
      case ExternalOptions.PREDEFINED_COUNTRIES:
      case ExternalOptions.PREDEFINED_LANGUAGES:
        updatedConfiguration = {
          valueType: ExternalOptions.TYPE_ABBREVIATED,
          listType: ExternalOptions.TYPE_FULL,
        };

        break;

      case ExternalOptions.PREDEFINED_NUMBERS:
        updatedConfiguration = {
          start: 0,
          end: 10,
        };

        break;

      case ExternalOptions.PREDEFINED_YEARS:
        updatedConfiguration = {
          sort: ExternalOptions.SORT_DESC,
          start: 100,
          end: 0,
        };

        break;

      case ExternalOptions.PREDEFINED_MONTHS:
        updatedConfiguration = {
          valueType: ExternalOptions.TYPE_FULL,
          listType: ExternalOptions.TYPE_FULL,
        };

        break;

      case ExternalOptions.PREDEFINED_DAYS:
        updatedConfiguration = {
          valueType: ExternalOptions.TYPE_INT,
          listType: ExternalOptions.TYPE_INT,
        };

        break;

      case ExternalOptions.PREDEFINED_DAYS_OF_WEEK:
        updatedConfiguration = {
          valueType: ExternalOptions.TYPE_FULL,
          listType: ExternalOptions.TYPE_FULL,
        };

        break;
    }

    updateField({
      value: "",
      values: [],
      source,
      target: value,
      configuration: updatedConfiguration
    });
    fetchGeneratedOptions(hash, source, value, updatedConfiguration);
  }

  /**
   * @param event
   */
  onUpdateConfig(event) {
    const { updateField, hash } = this.context;
    const { configuration, fetchGeneratedOptions, source, target } = this.props;
    const { name, value } = event.target;

    let isNumeric = false;
    if (event.target.dataset.isNumeric) {
      if (event.target.dataset.isNumeric !== "false") {
        isNumeric = true;
      }
    }

    let parsedValue = value;
    if (isNumeric) {
      const isNegative = /^-/.test(parsedValue);

      parsedValue = (parsedValue + "").replace(/[^0-9\.]/, "");
      parsedValue = parsedValue ? parseInt(parsedValue) : 0;

      if (isNegative && parsedValue >= 0) {
        parsedValue *= -1;
      }
    }

    let updatedConfiguration = configuration ? { ...configuration } : {};
    updatedConfiguration[name] = parsedValue;

    updateField({
      value: "",
      values: [],
      configuration: updatedConfiguration
    });
    fetchGeneratedOptions(hash, source, target, updatedConfiguration);
  }

  onUpdateEmptyOption(event) {
    const { value } = event.target;

    this.setState({ emptyOption: value });
    if (this.updateEmptyOptionTrigger) {
      clearTimeout(this.updateEmptyOptionTrigger);
    }

    this.updateEmptyOptionTrigger = setTimeout(this.persistEmptyOption, 500);
  }

  persistEmptyOption() {
    const { hash, updateField } = this.context;
    const { configuration, fetchGeneratedOptions, source, target } = this.props;
    const { emptyOption } = this.state;

    const updatedConfiguration = {
      ...configuration,
      emptyOption: emptyOption,
    };

    updateField({ configuration: updatedConfiguration });
    fetchGeneratedOptions(hash, source, target, updatedConfiguration);
  }
}
