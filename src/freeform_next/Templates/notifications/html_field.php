<?php /** @var \Solspace\Addons\FreeformNext\Model\NotificationModel $model */ ?>
<textarea name="bodyHtml" id="bodyHtml" hidden style="display: none !important;"><?php echo htmlspecialchars((string) $model->bodyHtml, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea>
<div class="freeform-html-editor-host" data-freeform-html-editor data-source="bodyHtml"></div>
