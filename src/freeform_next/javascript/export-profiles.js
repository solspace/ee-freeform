$(() => {
  $('#field-settings').sortable({ handle: '.handle', tolerance: 'pointer' });

  const statusSelector = $('.freeform-export-status-selector');
  statusSelector.on('change', 'input[type=checkbox]', function() {
    const checked = $('input[type=checkbox]:checked', statusSelector);
    const selection = $('.freeform-export-status-selection', statusSelector).empty();
    if (checked.length === 1) {
      selection.append(checked.closest('label').find('.status-tag').clone());
    } else {
      selection.text(checked.length ? `${checked.length} selected` : 'All statuses');
    }
  });

  $('.tbl-search .dropdown-field > a').on({
    click: function(e) {
      const parent = $(this).parents('.dropdown-field');

      window.location.href = $('select[name=form_handle]', parent).val();

      e.stopPropagation();
      e.preventDefault();
      return false;
    }
  });

  const addFilterButton = $('#add-filter');
  const filterTable = $('#filter-table');
  const template = $("template", filterTable);

  addFilterButton.on({
    click: () => {
      let clone = template.html();
      const lastIterator = $('tbody > tr[data-iterator]:last', filterTable).data('iterator');

      let currentIterator = 0;
      if (lastIterator !== undefined) {
        currentIterator = parseInt(lastIterator) + 1;
      }

      clone = clone.replace(/__iterator__/g, currentIterator);

      $('tbody', filterTable).append(clone);
    }
  });

  filterTable.on('click', '.freeform-filter-delete', function() {
    $(this).closest('tr').remove();
  });

});
