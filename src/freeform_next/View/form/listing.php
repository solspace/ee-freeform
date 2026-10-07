<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

$this->extend('_layouts/table_form_wrapper')?>

<?php
$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<div class="freeform-forms-list" data-forms-panel="list" hidden>
    <?php $this->embed('ee:_shared/table', $table); ?>
</div>

<div class="freeform-forms-cards" data-forms-panel="cards">
    <?php if (empty($form_cards)): ?>
        <div class="freeform-forms-empty">No forms yet.</div>
    <?php else: ?>
        <div class="freeform-forms-grid">
            <?php foreach ($form_cards as $card): ?>
                <article class="freeform-form-card" style="--freeform-card-color: <?= $escape($card['color']) ?>">
                    <div class="freeform-form-card__body">
                        <div class="freeform-form-card__heading">
                            <?php if ($card['editUrl']): ?>
                                <a class="freeform-form-card__title" href="<?= $escape($card['editUrl']) ?>"><?= $escape($card['name']) ?></a>
                            <?php else: ?>
                                <span class="freeform-form-card__title"><?= $escape($card['name']) ?></span>
                            <?php endif; ?>
                            <?php if ($can_manage_forms): ?>
                                <button type="button" class="freeform-form-card__duplicate duplicate" data-form-id="<?= (int) $card['id'] ?>" data-url="<?= $escape($duplicate_url) ?>" data-csrf="<?= $escape(CSRF_TOKEN) ?>" title="Duplicate <?= $escape($card['name']) ?>" aria-label="Duplicate <?= $escape($card['name']) ?>">
                                    <svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M7 3h10v11H7zM4 6H3v11h10v-1H4z"/></svg>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="freeform-form-card__handle"><?= $escape($card['handle']) ?></div>
                        <?php if (trim((string) $card['description']) !== ''): ?>
                            <p class="freeform-form-card__description"><?= $escape($card['description']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="freeform-form-card__stats">
                        <?php foreach (['submissions' => 'Submissions', 'spam' => 'Spam'] as $key => $label): ?>
                            <?php if ($card[$key . 'Url']): ?>
                                <a href="<?= $escape($card[$key . 'Url']) ?>"><strong><?= (int) $card[$key] ?></strong> <?= $escape(lang($label)) ?></a>
                            <?php else: ?>
                                <span><strong><?= (int) $card[$key] ?></strong> <?= $escape(lang($label)) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
