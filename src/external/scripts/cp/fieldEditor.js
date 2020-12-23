/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

$(() => {
  let wrappers = $('.option-editor-wrapper');

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
          if (event.which === 9 || event.keyCode === 9) {
            return false;
          }

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

    valueToggler
      .on({
        click: (event) => {
          const element = $(event.target);
          self.showValues(element.val() === "1");

          element.parent().addClass('chosen').siblings().removeClass('chosen');
        }
      }, 'input[type=radio]')
      .on({
        click: (event) => {
          const element = $(event.target),
                val = element.siblings('input[type=hidden]').val();

          self.showValues(val === "0");
        }
      }, 'button');

    itemList.sortable({
      handle: 'li[data-action=reorder] > a',
    });

    $('input:checked', valueToggler).trigger('click');
    self.showValues($('.value-toggler input:checked').val() === "1");
    self.showValues($('.value-toggler input:hidden').val() === "1");
    self.checkValueCount();
  });

  // $('select#dateTimeType')
  //   .on({
  //     change: function () {
  //       const val      = $(this).val(),
  //             showDate = val === 'both' || val === 'date',
  //             showTime = val === 'both' || val === 'time';
  //
  //       $('*[data-datetime-date-group]').each(function () {
  //         if (showDate) $(this).parents("fieldset:first").show();
  //         if (!showDate) $(this).parents("fieldset:first").hide();
  //       });
  //
  //       $('*[data-datetime-time-group]').each(function () {
  //         if (showTime) $(this).parents("fieldset:first").show();
  //         if (!showTime) $(this).parents("fieldset:first").hide();
  //       });
  //     }
  //   })
  //   .trigger('change');
  //
  // $("*[data-toggle]")
  //   .on({
  //     click: function () {
  //       $(this).trigger('change');
  //     },
  //     change: function () {
  //       const val       = $(this).val(),
  //             isReverse = $(this).data('toggle-reverse'),
  //             group     = $(this).data('toggle'),
  //             targets   = $('*[data-toggle-group="' + group + '"]');
  //
  //       if (val === (isReverse ? 'y' : 'n')) {
  //         targets.each(function () {
  //           $(this).parents("fieldset:first").show()
  //         });
  //       } else {
  //         targets.each(function () {
  //           $(this).parents("fieldset:first").hide()
  //         });
  //       }
  //     }
  //   });
  //
  // $("*[data-toggle]:checked").trigger('change');

  $('.color-picker').minicolors({

  });

});
