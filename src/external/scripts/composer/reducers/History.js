/*
 * Freeform for ExpressionEngine
 *
 * @package Solspace:Freeform
 * @license https://docs.solspace.com/license-agreement/
 */

import * as ActionTypes from "../constants/ActionTypes";

const LIMIT = 50;
const TYPING_WINDOW = 900;

const changes = new Set([
  ActionTypes.ADD_FIELD_TO_NEW_ROW,
  ActionTypes.ADD_COLUMN_TO_ROW,
  ActionTypes.ADD_COLUMN_TO_NEW_ROW,
  ActionTypes.REPOSITION_COLUMN,
  ActionTypes.REMOVE_COLUMN,
  ActionTypes.REMOVE_FIELD,
  ActionTypes.ADD_PAGE,
  ActionTypes.REMOVE_PAGE,
  ActionTypes.SWAP_PAGE,
  ActionTypes.UPDATE_PROPERTY,
  ActionTypes.REMOVE_PROPERTY,
  ActionTypes.RESET_PROPERTIES,
  ActionTypes.ADD_VALUE_SET,
  ActionTypes.CLEAN_UP_VALUES,
  ActionTypes.UPDATE_VALUE_SET,
  ActionTypes.UPDATE_IS_CHECKED,
  ActionTypes.INSERT_VALUE,
  ActionTypes.REMOVE_VALUE,
  ActionTypes.TOGGLE_CUSTOM_VALUES,
  ActionTypes.REORDER_VALUE_SET,
  ActionTypes.REMOVE_VALUE_SET,
  ActionTypes.MATRIX_ADD_ROW,
  ActionTypes.MATRIX_REMOVE_ROW,
  ActionTypes.MATRIX_SWAP_ROW,
  ActionTypes.MATRIX_UPDATE_COLUMN,
]);

const emptyHistory = { past: [], future: [], lastGroup: null, lastAt: 0 };

// Layout and option reducers mutate nested values. Snapshots must own their data.
const snapshot = state => JSON.parse(JSON.stringify({
  composer: state.composer,
  context: state.context,
}));

function typingGroup(action) {
  if (action.type === ActionTypes.UPDATE_PROPERTY) {
    const keys = Object.keys(action.keyValueObject || {}).sort();
    if (keys.length === 1 && typeof action.keyValueObject[keys[0]] === "string") {
      return `${action.type}:${action.hash}:${keys[0]}`;
    }
  }
  if (action.type === ActionTypes.UPDATE_VALUE_SET) {
    return `${action.type}:${action.hash}:${action.index}`;
  }
  if (action.type === ActionTypes.MATRIX_UPDATE_COLUMN && typeof action.value === "string") {
    return `${action.type}:${action.hash}:${action.attribute}:${action.rowIndex}:${action.name}`;
  }
  return null;
}

function restore(state, previous, history) {
  const composer = previous.composer;
  const maxPage = Math.max(0, composer.layout.length - 1);
  const page = Math.min(Math.max(0, previous.context.page || 0), maxPage);
  let hash = previous.context.hash;
  if (/^page\d+$/.test(hash)) {
    hash = `page${page}`;
  } else if (!composer.properties[hash] && !["form", "admin_notifications", "integration"].includes(hash)) {
    hash = "form";
  }

  // Handle warnings are derived from field properties and must follow restores.
  const handles = new Set();
  const duplicateHandles = [];
  Object.keys(composer.properties).forEach(key => {
    const property = composer.properties[key];
    if (Object.prototype.hasOwnProperty.call(property, "handle")) {
      if (handles.has(property.handle)) {
        duplicateHandles.push(property.handle);
      } else {
        handles.add(property.handle);
      }
    }
  });

  return {
    ...state,
    composer,
    context: { ...previous.context, page, hash },
    duplicateHandles,
    history,
  };
}

export default function withHistory(reducer) {
  return (state, action) => {
    const { history = emptyHistory, ...baseState } = state || {};

    if (action.type === ActionTypes.UNDO && history.past.length) {
      const previous = history.past[history.past.length - 1];
      return restore(baseState, previous, {
        past: history.past.slice(0, -1),
        future: [...history.future, snapshot(baseState)],
        lastGroup: null,
        lastAt: 0,
      });
    }
    if (action.type === ActionTypes.REDO && history.future.length) {
      const next = history.future[history.future.length - 1];
      return restore(baseState, next, {
        past: [...history.past, snapshot(baseState)].slice(-LIMIT),
        future: history.future.slice(0, -1),
        lastGroup: null,
        lastAt: 0,
      });
    }

    if (!changes.has(action.type) || action.meta && action.meta.skipHistory) {
      const next = reducer(baseState, action);
      const navigation = action.type === ActionTypes.SWITCH_HASH || action.type === ActionTypes.SWITCH_PAGE;
      return { ...next, history: navigation ? { ...history, lastGroup: null } : history };
    }

    const before = snapshot(baseState);
    const next = reducer(baseState, action);
    if (JSON.stringify(before.composer) === JSON.stringify(next.composer)) {
      return { ...next, history };
    }

    const group = typingGroup(action);
    const now = Date.now();
    const coalesce = group && group === history.lastGroup && now - history.lastAt < TYPING_WINDOW;
    return {
      ...next,
      history: {
        past: coalesce ? history.past : [...history.past, before].slice(-LIMIT),
        future: [],
        lastGroup: group,
        lastAt: now,
      },
    };
  };
}
