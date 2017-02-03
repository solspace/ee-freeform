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
  var $typeSelect = $("select#type");

  $typeSelect.on({
    change: function () {
      var type = $(this).val();

      $(".field-settings[data-type=" + type + "]")
        .show()
        .siblings()
        .hide();
    }
  });
  $typeSelect.trigger("change");


  var $table = $("table.value-group");
  $table.each(function () {
    var $sorter = new Craft.DataTableSorter($(this), {
      helperClass: 'editabletablesorthelper',
      copyDraggeeInputValuesToHelper: true
    });

    $(this).data("sorter", $sorter);
  });

  var $customValueSwitch = $("input[name$='[customValues]']").parents(".lightswitch");
  $customValueSwitch.on({
    change: function () {
      var isOn = $('input', this).val();
      if (isOn) {
        $table.removeClass("hide-custom-values");
      } else {
        $table.addClass("hide-custom-values");
      }
    }
  });
  $customValueSwitch.trigger("change");

  $(".value-group + .btn.add").on({
    click: function () {
      var $parentTable = $(this).prev("table.value-group");
      var type         = $parentTable.data("type");

      var $tr = $("<tr>")
        .append(
          $("<td>", {class: "textual field-label"})
            .append(
              $("<textarea>", {
                val: "",
                rows: 1,
                name: "types[" + type + "][labels][]",
              })
            )
        )
        .append(
          $("<td>", {class: "textual field-value"})
            .append(
              $("<textarea>", {
                val: "",
                rows: 1,
                class: "code",
                name: "types[" + type + "][values][]",
              })
            )
        )
        .append(
          $("<td>")
            .append(
              $("<input>", {
                type: "hidden",
                value: 0,
                class: "code",
                name: "types[" + type + "][checked][]",
              })
            )
            .append(
              $("<input>", {
                type: type == "checkbox_group" ? "checkbox" : "radio",
                name: type + "_is_checked",
                checked: false,
              })
            )
        )
        .append(
          $("<td>", {class: "thin action"})
            .append(
              $("<a>", {
                class: "move icon",
                title: Craft.t("Reorder"),
              })
            )
        )
        .append(
          $("<td>", {class: "thin action"})
            .append(
              $("<a>", {
                class: "delete icon",
                title: Craft.t("Delete"),
              })
            )
        );

      $("tbody", $parentTable).append($tr);
      $parentTable.find("tbody > tr:last > td:first textarea:first").focus();

      $parentTable.data("sorter").addItems($tr);
    }
  });

  $table
    .on({
      click: function () {
        $(this).parents("tr:first").remove();
      }
    }, "tr td.action .icon.delete")
    .on({
      keyup: function () {
        var $val = $(this).val();
        var $tr  = $(this).parents("tr:first");

        $("td.field-value > textarea", $tr).val($val);
      }
    }, "td.field-label > textarea")
    .on({
      click: function () {
        var $tbody    = $(this).parents("tbody:first");
        var isChecked = $(this).is(":checked");
        var isRadio   = $(this).is(":radio");

        if (isRadio && isChecked) {
          $("input:hidden", $tbody).val(0);
        }

        $(this).siblings("input:hidden").val(isChecked ? 1 : 0);
      }
    }, "input:checkbox, input:radio");
});
