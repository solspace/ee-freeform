/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

$(() => {
  let wrappers = $('.option-editor-wrapper');

  let label  = $('input:text[name=label]');
  let handle = $('input:text[name=handle]');

  const camelize = (str) => {
    return str
        .replace(/(?:^\w|[A-Z]|\b\w)/g, (letter, index) => (
        index === 0 ? letter.toLowerCase() : letter.toUpperCase()
      ))
      .replace(/\s+/g, '')
      .replace(/[^a-zA-Z0-9_]/g, '')
  };

  label.on({
    keyup: () => {
      let val = label.val();

      val = camelize(val);
      handle.val(val);
    },
    change: (event) => $(event.target).trigger('keypress'),
  });

  wrappers.each((i, wrapper) => {
    const self = $(wrapper);

    const editor        = $('.option-editor', self);
    const itemList      = $('.items', editor);
    const valueToggler  = $('.value-toggler', self);
    const buttonRow     = $('.button-row', self);
    const noValuesBlock = $('.no-values', self);

    /**
     * Shows or hides VALUE column based on showValues variable
     */
    self.showValues = (showValues) => {
      showValues ? self.addClass('show-values') : self.removeClass('show-values');
    };

    self.checkValueCount = () => {
      const valueCount = $("> ul", itemList).length;

      if (valueCount) {
        noValuesBlock.hide();
        editor.show();
        buttonRow.show();
      } else {
        noValuesBlock.show();
        editor.hide();
        buttonRow.hide();
      }
    };

    editor
    // REMOVE button click handler
      .on({
        click: (event) => {
          $(event.target).parents('ul:first').remove();
          self.checkValueCount();
        }
      }, '[data-action=remove] a')
      // IS CHECKED BY DEFAULT click handler
      .on({
        click: (event) => {
          $(event.target).prev().val($(event.target).is(':checked') ? 1 : 0);

          if (typeof editor.data('single-value') !== 'undefined') {
            const siblings = $(event.target).parents('ul:first').siblings();
            $('input[type=hidden]', siblings).val(0);
            $('input[type=checkbox]', siblings).prop('checked', false);
          }

        }
      }, '[data-checked] input[type=checkbox]')
      .on({
        keyup: (event) => {
          const labelInput = $(event.target);
          const valueInput = labelInput.parent().siblings('[data-value]').find('input:text');

          valueInput.val(labelInput.val());
        },
        change: (event) => {
          $(event.target).trigger('keypress');
        }
      }, '[data-label] input:text')
    ;

    // ADD ROW button click handler
    self.on({
      click: () => {
        const templateContents = $("template", editor).html().trim();

        itemList.append(templateContents);
        self.checkValueCount();
      }
    }, 'a[data-add-row]');

    valueToggler.on({
      click: (event) => {
        const element = $(event.target);
        self.showValues(element.val() === "1");

        element.parent().addClass('chosen').siblings().removeClass('chosen');
      }
    }, 'input[type=radio]');

    itemList.sortable({
      handle: 'li[data-action=reorder] > a',
    });

    $('input:checked', valueToggler).trigger('click');
    self.showValues($('.value-toggler input:checked').val() === "1");
    self.checkValueCount();
  });


});
