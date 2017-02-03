/*
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

import {hashFromTime, hashId, deHashId} from "./Utilities";

export default class FieldHelper {
  static hashField(field) {
    if (field.id) {
      return hashId(field.id);
    }

    return hashFromTime();
  }

  static deHashId(hash) {
    return deHashId(hash);
  }

  static getFieldPageIndex(hash, layout) {
    let pageIndex = 0;
    for (const rows of layout) {
      for (const columns of rows) {
        for (const fieldHash of columns.columns) {
          if (fieldHash === hash) {
            return pageIndex;
          }
        }
      }

      pageIndex++;
    }

    throw "Could not locate the page index this field is a part of";
  }

  static getTotalPages(layout) {
    return layout.length;
  }

  static isFieldOnLastPage(hash, layout) {
    return FieldHelper.getTotalPages(layout) === FieldHelper.getFieldPageIndex(hash, layout) + 1;
  }

  static isFieldOnFirstPage(hash, layout) {
    return this.getFieldPageIndex(hash, layout) === 0;
  }

  static isFieldOnMiddlePages(hash, layout) {
    return !FieldHelper.isFieldOnFirstPage(hash, layout) && !this.isFieldOnLastPage(hash, layout);
  }
}
