<?php
/**
 * @var \Solspace\Addons\FreeformNext\Model\FieldModel $model
 * @var string                                         $type
 */
?>

<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/fieldEditor.css"/>

<div class="option-editor-wrapper always-show-values">
    <div class="no-values">
        No <b>value/label pairs</b> found.
        <a href="javascript:;" data-add-row><?php echo lang('Add') ?></a>
    </div>

    <div class="option-editor" data-single-value>
        <ul class="headers">
            <li data-action></li>
            <li data-checked></li>
            <li data-label><?php echo lang('Label') ?></li>
            <li data-value><?php echo lang('Email') ?></li>
            <li data-action></li>
        </ul>
        <div class="items">
            <?php if ($model->options) : ?>
                <?php foreach ($model->options as $option): ?>
                    <?php
                        $checked = $option['value'] == $model->value;
                    ?>
                    <ul>
                        <li data-action="reorder">
                            <a href="javascript:;" title="reorder row"></a>
                        </li>
                        <li data-checked>
                            <input type="hidden"
                                   name="<?= htmlspecialchars(sprintf('types[%s][checked_by_default][]', $type), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                                   value="<?php echo $checked ? '1' : '0' ?>"
                            >
                            <input type="checkbox"
                                   class="toggle-checked"
                                   name=""
                                <?php echo $checked ? 'checked' : '' ?>
                            >
                        </li>
                        <li data-label>
                            <input type="text"
                                   name="<?= htmlspecialchars(sprintf('types[%s][labels][]', $type), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                                   value="<?= htmlspecialchars((string) $option['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                            >
                        </li>
                        <li data-value>
                            <input type="text"
                                   name="<?= htmlspecialchars(sprintf('types[%s][values][]', $type), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                                   value="<?= htmlspecialchars((string) $option['value'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                            >
                        </li>
                        <li data-action="remove">
                            <a href="javascript:;"></a>
                        </li>
                    </ul>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <template>
            <ul>
                <li data-action="reorder">
                    <a href="javascript:;" title="reorder row"></a>
                </li>
                <li data-checked>
                    <input type="hidden" name="<?= htmlspecialchars(sprintf('types[%s][checked_by_default][]', $type), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" value="0">
                    <input type="checkbox" name="" class="toggle-checked">
                </li>
                <li data-label>
                    <input type="text" name="<?= htmlspecialchars(sprintf('types[%s][labels][]', $type), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                </li>
                <li data-value>
                    <input type="text" name="<?= htmlspecialchars(sprintf('types[%s][values][]', $type), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                </li>
                <li data-action="remove">
                    <a href="javascript:;"></a>
                </li>
            </ul>
        </template>
    </div>

    <div class="button-row">
        <a href="javascript:;" data-add-row></a>
    </div>
</div>
