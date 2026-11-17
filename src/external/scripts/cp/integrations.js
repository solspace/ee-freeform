/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

$(() => {
  // A provider change reveals all of that provider's settings. Reapply the
  // Salesforce method group so a newly selected provider shows only its active fields.
  const salesforceMethods = $("input[name$='-salesforce_client_credentials']:radio");
  // EE restores cached radio states when revealing a provider. Keep both
  // options in sync, since the browser only fires change on the selected radio.
  salesforceMethods.on("change", () => {
    salesforceMethods.each((index, input) => {
      $(input).data("el_checked", input.checked);
    });
  });
  function updateSalesforceFields() {
    const selected = salesforceMethods.filter(":checked");
    if (selected.length && selected.closest("fieldset").is(":visible")) {
      EE.cp.form_group_toggle(selected[0]);
    }
  }
  $("select[name='class']").on("change", updateSalesforceFields);
  updateSalesforceFields();

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
