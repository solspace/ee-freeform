/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
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
