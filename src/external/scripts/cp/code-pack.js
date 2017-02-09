/*
 * Freeform Next for Expression Engine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform-next
 * @license       https://solspace.com/software/license-agreement
 */

var $prefix = $('#prefix');
var $components = $('#components-wrapper');
var firstFileLists = $('> div > ul.directory-structure', $components);

var prefixTimeout = null;

$(function(){
  $prefix.on({
    keyup: function(e) {
      clearTimeout(prefixTimeout);
      prefixTimeout = setTimeout(function(){
        updateFilePrefixes();
      }, 50);
    }
  });

  updateFilePrefixes();
});

function updateFilePrefixes()
{
  firstFileLists.each(function(){
    var $fileList = $(this);
    $('> li > span[data-name]', $fileList).each(function(){
      $(this).html($prefix.val() + $(this).data('name'));
    });
  });
}
