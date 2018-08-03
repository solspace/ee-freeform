/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

console.log('filters init');

var $context = $('#custom-filters');
var $valueLinks = $('a[data-value]', $context);
var $inputs = $('input[type=text]', $context);
var $dateRangeOccurrences = $('#date-range-inputs', $context);

var $dateChoice = $('input[name=search_date_range]', $context);

$valueLinks
  .unbind('click')
  .on({
    click: function () {

      console.log('filters click');
      var $self = $(this);
      var $menu = $self.parents('div.sub-menu');
      var target = $self.parents('ul:first[data-target]').data('target');
      var $input = $('input[name=' + target + ']', $context);

      var value = $self.data('value');

      $input.val(value);
      $menu.hide();

      $menu.parents('li:first').find('a.has-sub > span.faded').html('(' + $self.html().trim() + ')');

      if (target == 'search_date_range') {
        if (value == 'date_range') {
          $dateRangeOccurrences.show();
        } else {
          $dateRangeOccurrences
            .hide()
            .find('input')
            .val('');
        }
      }

      if (!$self.data('prevent-trigger')) {
        event.preventDefault();
        event.stopPropagation();
        postFilter();
      }
    }
  });

$inputs.on({
  keypress: function(event) {


    console.log('filters keypress');

    var $self = $(this);
    var which = event.which ? event.which : event.charCode;

    if (which == 13) {
      event.preventDefault();
      event.stopPropagation();
      postFilter();
    }
  }
});

function postFilter() {
  event.preventDefault();
  var form = $('#testid');
  var url = window.location.href.split('?')[0];
  var parameters = window.location.href.split('?')[1];
  var endpoint = parameters.split('&')[0];

  var fullurl =  url + '?' + endpoint + '&' + form.serialize();

  window.location.href = fullurl;
}

if ($dateChoice.val() == 'date_range') {
  $dateRangeOccurrences.show();
}

$('input', $context).each(function(){
  if ($(this).val()) {
    $('.filter-clear', $context).show();
  }
});

$(() => {
  $('#change-layout-trigger').featherlight('.choice-panel', {
    otherClose: 'button[data-featherlight-close]',
    afterContent: function(event) {
      $('ul.very-sortable', this.$content).sortable();
    },
  });

  $('button[data-layout-save]').on({
    click: () => {
      const $content = $('.featherlight-content');

      let formId = layoutEditorFormId;
      let pushData = [];

      $('ul.very-sortable > li', $content).each(function() {
        const id = $(this).data('id'),
          handle = $(this).data('handle'),
          label  = $(this).data('label');

        const checked = $('input[type=checkbox]', $(this)).is(':checked');

        pushData.push({
          id,
          handle,
          label,
          checked: checked ? 1 : 0,
        });
      });

      $.ajax({
        url: layoutEditorSaveUrl,
        type: 'post',
        dataType: 'json',
        data: {
          formId,
          data: pushData,
        },
        success: (response) => {
          if (response.success) {
            window.location.reload(false);
          }
        }
      });
    }
  });

  $('#export-csv-modal').on({
    submit: () => {
      const current = $.featherlight.current();
      current.close();
    }
  });

  $('#quick-export-trigger').featherlight('#quick-export-modal', {
    otherClose: '.btn.cancel',
    afterContent: function() {
      $('.checkbox-select', this.$content).sortable();
    }
  });
});
