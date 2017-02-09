/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

if (typeof Craft.Freeform === typeof undefined) {
  Craft.Freeform = {};
}

Craft.Freeform.SubmissionsIndex = Craft.BaseElementIndex.extend({
  getViewClass: function(mode) {
    switch (mode) {
      case 'table':
        return Craft.Freeform.SubmissionsTableView;
      default:
        return this.base(mode);
    }
  },
  getDefaultSort: function() {
    return ['dateCreated', 'desc'];
  }
});

// Register the Freeform SubmissionsIndex class
Craft.registerElementIndexClass('Freeform_Submission', Craft.Freeform.SubmissionsIndex);
