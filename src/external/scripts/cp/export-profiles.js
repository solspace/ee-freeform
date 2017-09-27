$(() => {
  const table = $('#field-settings');

  $('tbody', table).sortable();

  $('.tbl-search .dropdown > a').on({
    click: function(e) {
      const parent = $(this).parents('.dropdown');

      window.location.href = $('select[name=form_handle]', parent).val();

      e.stopPropagation();
      e.preventDefault();
      return false;
    }
  });
});
