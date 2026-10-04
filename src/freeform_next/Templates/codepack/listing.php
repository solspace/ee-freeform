<?php
/** @var \Solspace\Addons\FreeformNext\Library\Codepack\Codepack $codepack */
$sampleTemplates = [
    'Tailwind 4 Light', 'Tailwind 4 Dark',
    'Bootstrap 5 Light', 'Bootstrap 5 Dark', 'Bootstrap 5 Floating Labels',
    'Flexbox', 'Grid',
    'Basic Light', 'Basic Dark', 'Basic Floating Labels',
];
?>
<div class="freeform-demo-install">
    <p>The demo creates a small ExpressionEngine template group with a form simulator, submissions area, and spam viewer. Switch between these ten included formatting templates without changing a form's saved settings.</p>
    <ul class="freeform-demo-samples">
        <?php foreach ($sampleTemplates as $name): ?>
            <li><?= htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
    <p class="freeform-demo-install-note">Submission data is visible to Super Admins only. Installing into an existing group updates demo templates with matching names.</p>
</div>
<style>
.freeform-demo-install { max-width: 760px; font-size: 13px; line-height: 1.5; }
.freeform-demo-install p { margin: 0 0 12px; }
.freeform-demo-samples { display: grid; grid-template-columns: repeat(auto-fit, minmax(175px, 1fr)); gap: 7px 16px; margin: 0 0 14px; padding: 0; list-style: none; }
.freeform-demo-samples li { padding: 7px 9px; border: 1px solid var(--ee-border, #dfe0ee); border-radius: 5px; background: var(--ee-input-bg, #fff); }
.freeform-demo-install-note { color: var(--ee-text-secondary, #606477); }
</style>
