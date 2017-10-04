(function () {
  const context = $("#export-csv-modal");

  if (!context.data('initiated')) {
    context.data('initated', true);

    $(".checkbox-select", context).each(function () {
      if (!$(this).data('dragger')) {
        $(this).data('dragger', true);

        // SORT
      }
    });

    const modal = $("#export-modal-wrapper");
    $(".btn.submit", modal).on({
      click: () => {
        modal.data('modal').hide();
      }
    });
    $(".btn.cancel", modal).on({
      click: () => {
        modal.data('modal').hide();
      }
    });

    const formSelector = $("select[name=form_id]", context);
    formSelector.on({
      change: function () {
        const val = $(this).val();

        $(".form-field-list").addClass('hidden');
        $(".form-field-list[data-id=" + val + "]").removeClass('hidden');
      }
    })
  }
})();
