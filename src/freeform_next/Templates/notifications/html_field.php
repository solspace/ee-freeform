<?php
/**
 * @var \Solspace\Addons\FreeformNext\Model\NotificationModel $model
 */
?>

<script src="<?php echo URL_THIRD_THEMES ?>freeform_next/lib/ace/ace.js"></script>
<script src="<?php echo URL_THIRD_THEMES ?>freeform_next/lib/ace/mode-html.js"></script>
<script src="<?php echo URL_THIRD_THEMES ?>freeform_next/lib/ace/theme-github.js"></script>

<style>
    #ace-editor {
        width: 100%;
        height: 400px;
        background-color: #fff;
        border: 1px solid;
        border-color: #b3b3b3 #cdcdcd #cdcdcd #b3b3b3;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
    }
</style>

<textarea name="bodyHtml" id="bodyHtml" style="display: none;"><?php echo $model->bodyHtml ?></textarea>
<div id="ace-editor"></div>

<script>
  var editor = ace.edit("ace-editor");
  editor.setTheme("ace/theme/github");
  editor.getSession().setMode("ace/mode/html");

  var textarea = document.getElementById("bodyHtml");
  editor.getSession().setValue(textarea.value);
  editor.getSession().on('change', function(){
    textarea.value = editor.getSession().getValue();
  });
</script>
