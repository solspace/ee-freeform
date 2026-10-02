(function ($) {
    var $form = $('#entry-filters');
    var $context = $('#custom-filters');
    var $targetLinks = $('a[data-target]', $context);
    var $inputs = $('input[type=text]', $context);
    var $dateRangeOccurrences = $('.date-range-inputs', $context);
    var $dateChoice = $('input[name=search_date_range]', $context);

    $targetLinks.off('click').on('click', function () {
        var $self = $(this);
        var target = $self.data('target');
        var tagContent = $self.html();

        $('input[type=hidden][data-filter=' + target + ']').val(target);

        if (target === 'search_on_field') {
            $('input[type=hidden][data-filter=' + target + ']').val($self.data('value'));
            $self.parents('.dropdown').removeClass('dropdown--open');
            $('button.dropdown-open').removeClass('dropdown-open');
            $self.parents('.dropdown').prev('button').find('span.faded').html('(' + tagContent + ')');
        }

        if (target === 'date_range') {
            $dateRangeOccurrences.show();
            $self.parents('.dropdown').removeClass('dropdown--open');
            $('button.dropdown-open').removeClass('dropdown-open');
        } else {
            $dateRangeOccurrences.hide().find('input').val('');
        }
    });

    $inputs.on('keypress', function (event) {
        var which = event.which ? event.which : event.charCode;
        if (which == 13) {
            event.preventDefault();
            event.stopPropagation();
            postFilter(event);
        }
    });

    function postFilter(event) {
        if (event) event.preventDefault();
        var $form = $('#entry-filters');
        var url = window.location.href.split('?')[0];
        var parameters = window.location.href.split('?')[1] || '';
        var endpoint = parameters.split('&')[0] || '';
        var fullurl = url + (endpoint ? '?' + endpoint + '&' : '?') + $form.serialize();
        window.location.href = fullurl;
    }

    if ($dateChoice.val() === 'date_range') {
        $dateRangeOccurrences.show();
    }

    $('input', $context).each(function () {
        if ($(this).val()) {
            $('.filter-clear', $context).show();
        }
    });

    $(function () {
        var $columnsMenu = $('.freeform-columns-dropdown');
        $('ul.very-sortable', $columnsMenu).sortable({ cancel: 'input, button', tolerance: 'pointer' });
        $columnsMenu.on('click', function (event) {
            event.stopPropagation();
        });

        $('button[data-layout-save]').on('click', function () {
            var $content = $(this).closest('.freeform-columns-dropdown');
            var $button = $(this);
            var formId = layoutEditorFormId;
            var pushData = [];

            $('ul.very-sortable > li', $content).each(function () {
                var id = $(this).data('id');
                var handle = $(this).data('handle');
                var label = $(this).data('label');
                var checked = $('input[type=checkbox]', $(this)).is(':checked');

                pushData.push({ id: id, handle: handle, label: label, checked: checked ? 1 : 0 });
            });

            $button.prop('disabled', true);
            $.ajax({
                url: layoutEditorSaveUrl,
                type: 'post',
                dataType: 'json',
                data: { formId: formId, data: pushData },
                success: function (response) {
                    if (response && response.success) window.location.reload(false);
                },
                complete: function () {
                    $button.prop('disabled', false);
                }
            });
        });

        $('#export-csv-modal').on('submit', function () {
            var current = $.featherlight.current();
            if (current) current.close();
        });

        $('#quick-export-trigger').featherlight('#quick-export-modal', {
            otherClose: '.btn.cancel',
            afterContent: function () {
                $('.checkbox-select', this.$content).sortable();
            }
        });
    });
})(jQuery);
