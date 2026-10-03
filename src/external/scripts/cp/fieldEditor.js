/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
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

    self.on('freeform:options-changed', () => self.checkValueCount());

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
          const element = $(event.target).parents('.value-toggler').children('button');
          const val = element.children('input[type=hidden]').val();

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

  // Confirm before carrying unsaved settings into the selected type.
  $('[data-compatible-field-types]').each((i, select) => {
    let previousType = $(select).val();
    const toggleGroups = () => EE.cp.form_group_toggle(select);
    $(select).on('change', () => {
      const nextType = $(select).val();
      if (previousType === nextType) return;
      const label = type => $(select).find('option').filter((index, option) => option.value === type).text();
      // Native EE's inline change handler ran first. Keep the current editor
      // intact until confirmation; Cancel and Escape leave it untouched.
      $(select).val(previousType);
      toggleGroups();
      const dialog = $(`<dialog class="freeform-field-type-dialog panel" role="alertdialog" aria-modal="true"
        aria-labelledby="freeform-type-title-${i}" aria-describedby="freeform-type-description-${i}">
        <div class="panel-heading"><h2 id="freeform-type-title-${i}">Change field type?</h2></div>
        <div class="panel-body" id="freeform-type-description-${i}">
          <p data-conversion></p>
          <p>This change applies to every form using this field when you save. Existing data could behave differently, and validation, custom templates, or integrations may need updates. Type-specific settings will be reset.</p>
          <p data-recipient-warning>Dynamic Recipients requires email addresses for its option values. Review these in each form and configure a notification template before enabling recipient emails. Options from a data source become a fixed list.</p>
        </div>
        <div class="panel-footer">
          <button type="button" class="button button--default" data-cancel>Cancel</button>
          <button type="button" class="button button--primary" data-confirm>Change Type</button>
        </div>
      </dialog>`).appendTo(document.body);
      $('[data-conversion]', dialog).text(`Change from ${label(previousType)} to ${label(nextType)}?`);
      $('[data-recipient-warning]', dialog).toggle(nextType === 'dynamic_recipients');
      const cancel = $('[data-cancel]', dialog)[0];
      const confirm = $('[data-confirm]', dialog)[0];
      const close = () => {
        dialog[0].close();
        dialog.remove();
        select.focus();
      };
      cancel.addEventListener('click', close);
      dialog[0].addEventListener('cancel', event => { event.preventDefault(); close(); });
      dialog[0].addEventListener('keydown', event => {
        if (event.key !== 'Tab') return;
        if (event.shiftKey && document.activeElement === cancel) {
          event.preventDefault(); confirm.focus();
        } else if (!event.shiftKey && document.activeElement === confirm) {
          event.preventDefault(); cancel.focus();
        }
      });
      confirm.addEventListener('click', () => {
        const previousPrefix = `types[${previousType}]`;
        const nextPrefix = `types[${nextType}]`;
        ['value', 'placeholder'].forEach(property => {
          const sourceProperty = property === 'value' && previousType === 'datetime' ? 'initialValue' : property;
          const targetProperty = property === 'value' && nextType === 'datetime' ? 'initialValue' : property;
          const source = $(`[name="${previousPrefix}[${sourceProperty}]"]`);
          const target = $(`[name="${nextPrefix}[${targetProperty}]"]`);
          if (source.length && target.length) target.val(source.val());
        });

        const source = wrappers.has(`[name^="${previousPrefix}"]`);
        const target = wrappers.has(`[name^="${nextPrefix}"]`);
        if (source.length && target.length) {
          const items = $('.option-editor .items', source).children().clone();
          items.find('input[name]').each((index, input) => {
            input.name = input.name.replace(previousPrefix, nextPrefix);
          });
          // An intermediate type can have multiple default choices before Save.
          if ($('.option-editor', target).is('[data-single-value]')) {
            items.find('input[type=checkbox]:checked').slice(1).each((index, input) => {
              $(input).prop('checked', false).prev().val(0);
            });
          }
          $('.option-editor .items', target).empty().append(items);
          const customValues = $('.value-toggler input', source).val();
          $('.value-toggler input', target).val(customValues);
          target.toggleClass('show-values', customValues === '1');
          $('.value-toggler button', target)
            .toggleClass('on', customValues === '1')
            .toggleClass('off', customValues !== '1')
            .attr('data-state', customValues === '1' ? 'on' : 'off')
            .attr('aria-checked', customValues === '1' ? 'true' : 'false');
          target.trigger('freeform:options-changed');
        }
        previousType = nextType;
        $(select).val(nextType);
        toggleGroups();
        close();
      });
      dialog[0].showModal();
      cancel.focus();
    });
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
