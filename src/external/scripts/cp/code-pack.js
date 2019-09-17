/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

const $prefix = $('input[name=prefix]');

$(() => {

  $prefix.on({
    keyup: function(e) {
      $('[data-prefix]').text($(e.target).val());
    }
  });

});
