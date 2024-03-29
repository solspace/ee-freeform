var $form = $('#entry-filters');
var $context = $('#custom-filters');
var $targetLinks = $('a[data-target]', $context);
var $inputs = $('input[type=text]', $context);
var $dateRangeOccurrences = $('.date-range-inputs', $context);

var $dateChoice = $('input[name=search_date_range]', $context);

$targetLinks
  .unbind('click')
  .on({
    click: function () {

      var $self = $(this);
      var target = $self.data('target');
      var tagContent = $self.html();

      $('input[type=hidden][data-filter=' + target + ']').val(target);

      if (target == 'search_on_field')
      {
        $('input[type=hidden][data-filter=' + target + ']').val($self.data('value'));
        $self.parents('.dropdown').removeClass('dropdown--open');
        $('button.dropdown-open').removeClass('dropdown-open');
        $self.parents('.dropdown').prev('button').find('span.faded').html('(' + tagContent + ')');
      }

      if (target == 'date_range')
      {
        $dateRangeOccurrences.show();
        $self.parents('.dropdown').removeClass('dropdown--open');
        $('button.dropdown-open').removeClass('dropdown-open');
      }
      else
      {
        $dateRangeOccurrences
          .hide()
          .find('input')
          .val('');
      }

    }
  });

$inputs.on({
  keypress: function(event) {
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
  var form = $('#entry-filters');
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
