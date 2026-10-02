<?php /** @var \Solspace\Addons\FreeformNext\Model\ExportProfileModel $profile */ ?>
<ul id="field-settings" class="freeform-export-fields">
    <?php foreach ($profile->getFieldSettings() as $fieldId => $fieldData): ?>
        <?php $inputId = 'export-field-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $fieldId); ?>
        <li class="freeform-export-field">
            <span class="handle" title="Drag to reorder" aria-hidden="true">≡</span>
            <input type="hidden"
                   name="fieldSettings[<?= htmlspecialchars((string) $fieldId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>][checked]"
                   value="0" />
            <input type="checkbox"
                   id="<?= htmlspecialchars($inputId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                   name="fieldSettings[<?= htmlspecialchars((string) $fieldId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>][checked]"
                   value="1"
                   <?= $fieldData['checked'] ? 'checked' : '' ?> />
            <label for="<?= htmlspecialchars($inputId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                   title="<?= htmlspecialchars((string) $fieldData['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                <?= htmlspecialchars((string) $fieldData['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
            </label>
            <input type="hidden"
                   name="fieldSettings[<?= htmlspecialchars((string) $fieldId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>][label]"
                   value="<?= htmlspecialchars((string) $fieldData['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" />
        </li>
    <?php endforeach; ?>
</ul>
