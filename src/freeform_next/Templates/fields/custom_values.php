<?php
/**
 * @var \Solspace\Addons\FreeformNext\Model\FieldModel $model
 * @var string                                         $type
 */
?>

<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/fieldEditor.css"/>

<div class="option-editor-wrapper<?php echo $model->hasCustomOptionValues() ? ' show-values' : '' ?>">
    <div class="value-toggler">
        <?php if (version_compare(APP_VER, '4.0.0', '<')) : ?>

            <label class="choice mr yes <?php echo $model->hasCustomOptionValues() ? 'chosen' : '' ?>">
                <input type="radio"
                       name="<?php echo sprintf('types[%s][custom_values]', $type) ?>"
                       value="1"
                    <?php echo $model->hasCustomOptionValues() ? 'checked' : '' ?>
                >
                <?php echo lang('Yes') ?>
            </label>
            <label class="choice no <?php echo $model->hasCustomOptionValues() ? '' : 'chosen' ?>">
                <input type="radio"
                       name="<?php echo sprintf('types[%s][custom_values]', $type) ?>"
                       value="0"
                    <?php echo $model->hasCustomOptionValues() ? '' : 'checked' ?>
                >
                <?php echo lang('No') ?>
            </label>

        <?php else: ?>

            <button type="button" class="toggle-btn <?= $model->hasCustomOptionValues() ? 'on' : 'off' ?>" data-state="<?= $model->hasCustomOptionValues() ? 'on' : 'off' ?>" role="switch" aria-checked="<?= $model->hasCustomOptionValues() ? 'true' : 'false' ?>" alt="<?= $model->hasCustomOptionValues() ? 'on' : 'off' ?>">
                <input type="hidden" name="<?php echo sprintf('types[%s][custom_values]', $type) ?>" value="<?= $model->hasCustomOptionValues() ? 1 : 0?>">
                <span class="slider"></span>
                <span class="option"></span>
            </button>

        <?php endif; ?>
    </div>





    <div class="no-values">
        No <b>value/label pairs</b> found.
        <a href="javascript:;" data-add-row><?php echo lang('Add') ?></a>
    </div>

    <div class="option-editor" <?php echo $singleValue ? 'data-single-value' : '' ?>>
        <ul class="headers">
            <li data-action></li>
            <li data-checked></li>
            <li data-label><?php echo lang('Label') ?></li>
            <li data-value><?php echo lang('Value') ?></li>
            <li data-action></li>
        </ul>
        <div class="items">
            <?php if ($model->options) : ?>
                <?php foreach ($model->options as $option): ?>
                    <?php
                    if ($type === \Solspace\Addons\FreeformNext\Library\Composer\Components\FieldInterface::TYPE_CHECKBOX_GROUP) {
                        $checked = $model->values && in_array($option['value'], $model->values);
                    } else {
                        $checked = $option['value'] == $model->value;
                    }
                    ?>
                    <ul>
                        <li data-action="reorder">
                            <a href="javascript:;" title="reorder row"></a>
                        </li>
                        <li data-checked>
                            <input type="hidden"
                                   name="<?php echo sprintf('types[%s][checked_by_default][]', $type) ?>"
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
                                   name="<?php echo sprintf('types[%s][labels][]', $type) ?>"
                                   value="<?php echo $option['label'] ?>"
                            >
                        </li>
                        <li data-value>
                            <input type="text"
                                   name="<?php echo sprintf('types[%s][values][]', $type) ?>"
                                   value="<?php echo $option['value'] ?>"
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
                    <input type="hidden" name="<?php echo sprintf('types[%s][checked_by_default][]', $type) ?>" value="0">
                    <input type="checkbox" name="" class="toggle-checked">
                </li>
                <li data-label>
                    <input type="text" name="<?php echo sprintf('types[%s][labels][]', $type) ?>">
                </li>
                <li data-value>
                    <input type="text" name="<?php echo sprintf('types[%s][values][]', $type) ?>">
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
