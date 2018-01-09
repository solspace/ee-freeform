/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

const $prefix = $('input[name=prefix]');

$(() => {

  $prefix.on({
    keyup: function(e) {
      $('[data-prefix]').text($(e.target).val());
    }
  });

});
