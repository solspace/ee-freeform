/*
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2016, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

$(function () {
  $("div#container").on({
    click: function () {
      var formId = $(this).data("submit-form");

      $("form#" + formId).submit();
    }
  }, "a[data-submit-form]");


  var $statusSelect = $('#status-menu-btn');
  new Garnish.MenuBtn($statusSelect, {
    onOptionSelect: function (data) {
      var id = $(data).data('id');
      var name = $(data).data('name');
      var color = $(data).data("color")
      $('#statusId').val(id);
      var html = "<span class='status " + color + "'></span>" + Craft.uppercaseFirst(name);
      $statusSelect.html(html);

      $("#status-menu-select li a.sel").removeClass("sel");
      $("#status-menu-select li a[data-id=" + id + "]").addClass("sel");
    }
  });

  var $assetDownloadForm = $("form#asset_download");

  $("a[data-asset-id]").on({
    click: function() {
      var assetId = $(this).data("asset-id");

      $("input[name=assetId]", $assetDownloadForm).val(assetId);
      $assetDownloadForm.submit();
    }
  });

  const $tabs = $("#tabs a.tab");

  $tabs.on({
    click: function () {
      var $self = $(this);

      $self.parent().siblings().find('.tab.sel').removeClass('sel');
      $self.addClass('sel');

      $('.tab-content').addClass('hidden');
      $('.tab-content[data-for-tab=' + $self.data('tab-id') + ']').removeClass('hidden');

      return false;
    }
  });

});
