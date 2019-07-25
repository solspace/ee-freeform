/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

$(function () {
  $(".reset-spam-count").on({
    click: (e) => {
      const self = $(e.target);
      const msg  = self.data("confirm-message");

      if (!confirm(msg)) {
        return false;
      }

      const formId = self.data("form-id");

      $.ajax({
        url: self.data('url'),
        type: "post",
        data: {
          formId: formId,
          csrf: self.data("csrf"),
        },
        dataType: "json",
        success: (response) => {
          if (response.error) {
            alert(response.error);
          } else if (response.success) {
            window.location.reload(false);
          }
        }
      })
    }
  });
});
