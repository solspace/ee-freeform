/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

$(function () {
  var $classSelector = $("select#class");
  $classSelector.on({
    change: function () {
      var val = $(this).val().split("\\").join("");

      $("div#settings-" + val)
        .show()
        .siblings()
        .hide();
    }
  });

  $classSelector.trigger("change");

  if (!$("#name").val().length) {
    $("#name").on({
      keyup: function () {
        $("#handle").val(generateHandle($(this).val())).trigger("change");
      }
    })
  }

  var $returnUri = $("input.setting-return_uri");
  var urlType = $("#integration-type").data("type");

  $("#handle").on({
    change: function () {
      var val        = $(this).val();
      var updatedUrl = Craft.getCpUrl("freeform/settings/" + urlType + "/" + val);

      $returnUri.val(updatedUrl);
    },
    keyup: function () {
      $(this).trigger("change");
    }
  });

  var $authChecker       = $("#auth-checker");
  var pendingStatusCheck = $(".pending-status-check", $authChecker);
  var integrationId      = pendingStatusCheck.data("id");
  var type               = pendingStatusCheck.data("type");

  if (integrationId) {
    var data = {
      id: integrationId,
    };

    data[Craft.csrfTokenName] = Craft.csrfTokenValue;

    $.ajax({
      url: Craft.getCpUrl("freeform/" + type + "/check"),
      data: data,
      type: "post",
      dataType: "json",
      success: function (json) {
        pendingStatusCheck.hide();

        if (json.success) {
          $(".authorized", $authChecker).show();
        } else {
          $(".not-authorized", $authChecker).show();

          if (json.errors) {
            $(".not-authorized .errors", $authChecker).empty().text(json.errors.join(". "));
          }
        }
      },
    });
  }
});


function generateHandle(value) {
  // Remove HTML tags
  var handle = value.replace("/<(.*?)>/g", '');

  // Remove inner-word punctuation
  handle = handle.replace(/['"‘’“”\[\]\(\)\{\}:]/g, '');

  // Make it lowercase
  handle = handle.toLowerCase();

  // Convert extended ASCII characters to basic ASCII
  handle = Craft.asciiString(handle);

  // Handle must start with a letter
  handle = handle.replace(/^[^a-z]+/, '');

  // Get the "words"
  var words = Craft.filterArray(handle.split(/[^a-z0-9]+/));

  handle = '';

  // Make it camelCase
  for (var i = 0; i < words.length; i++) {
    if (i == 0) {
      handle += words[i];
    }
    else {
      handle += words[i].charAt(0).toUpperCase() + words[i].substr(1);
    }
  }

  return handle;
}
