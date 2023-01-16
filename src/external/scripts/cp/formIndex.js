/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2023, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
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

  $(".duplicate").on({
    click: (e) => {
      const self = $(e.target);

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
