/*
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

$(function(){
  $("button.reset-spam-count").on({
    click: function(event) {
      var msg = $(this).data("confirm-message");

      if (!confirm(msg)) {
        return false;
      }

      var formId = $(this).data("form-id");
      var data = {
        formId: formId,
      };

      data[Craft.csrfTokenName] = Craft.csrfTokenValue;

      $.ajax({
        url: Craft.getActionUrl("freeform/forms/resetSpamCounter"),
        type: "post",
        data: data,
        dataType: "json",
        success: function(response) {
          if (response.error) {
            Craft.cp.displayNotification("error", response.error);
          } else if (response.success) {
            $("td.spam-count[data-form-id=" + formId + "]").html(0);
          }
        }
      })
    }
  });
});
