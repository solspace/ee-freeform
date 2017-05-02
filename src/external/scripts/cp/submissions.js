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
  })
});
