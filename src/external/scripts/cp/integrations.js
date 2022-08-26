/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

$(() => {
  const csrfToken          = $("*[data-csrf-token]").data("csrf-token");
  const authChecker        = $("#auth-checker");

  const authorized = $(".authorized", authChecker);
  const notAuthorized = $(".not-authorized", authChecker);
  const pendingStatusCheck = $(".pending-status-check", authChecker);

  const integrationId      = pendingStatusCheck.data("id");
  const type               = pendingStatusCheck.data("type");

  if (integrationId) {
    checkAuth();

    $('a', notAuthorized).on({
      click: (e) => {
        checkAuth();

        return false;
      }
    })
  }

  function checkAuth() {
    pendingStatusCheck.show();
    authorized.hide();
    notAuthorized.hide();

    let data = {
      id: integrationId,
      csrf: csrfToken,
    };

    $.ajax({
      url: authChecker.data('url-stub'),
      data: data,
      type: "post",
      dataType: "json",
      success: function (json) {
        pendingStatusCheck.hide();

        if (json.success) {
          authorized.show();
        } else {
          notAuthorized.show();

          if (json.errors) {
            $(".errors", notAuthorized).empty().text(json.errors.join(". "));
          }
        }
      },
    });
  }
});
