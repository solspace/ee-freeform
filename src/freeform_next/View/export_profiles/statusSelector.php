<?php
/** @var \Solspace\Addons\FreeformNext\Model\ExportProfileModel $profile */
/** @var \Solspace\Addons\FreeformNext\Model\StatusModel[] $statuses */
$selectedStatusIds = array_map('strval', is_array($profile->statuses) ? $profile->statuses : []);
$selectedCount = count($selectedStatusIds);
$selectedStatus = null;
if ($selectedCount === 1) {
    foreach ($statuses as $status) {
        if ((string) $status->id === $selectedStatusIds[0]) {
            $selectedStatus = $status;
            break;
        }
    }
}
?>
<details class="freeform-export-status-selector">
    <summary>
        <span class="freeform-export-status-selection">
            <?php if ($selectedStatus): ?>
                <span class="status-tag freeform-export-status-tag" style="--freeform-status-color: <?= preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', (string) $selectedStatus->color) ? $selectedStatus->color : 'var(--ee-text-secondary)' ?>">
                    <?= htmlspecialchars((string) $selectedStatus->name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                </span>
            <?php else: ?>
                <?= $selectedCount ? $selectedCount . ' selected' : 'All statuses' ?>
            <?php endif; ?>
        </span>
        <span class="freeform-export-status-chevron" aria-hidden="true"></span>
    </summary>
    <div class="freeform-export-status-options">
        <?php foreach ($statuses as $status): ?>
            <?php
            $color = (string) $status->color;
            $color = preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $color)
                ? $color
                : 'var(--ee-text-secondary)';
            ?>
            <label>
                <input type="checkbox" name="statuses[]" value="<?= (int) $status->id ?>"
                    <?= in_array((string) $status->id, $selectedStatusIds, true) ? 'checked' : '' ?> />
                <span class="status-tag freeform-export-status-tag" style="--freeform-status-color: <?= $color ?>">
                    <?= htmlspecialchars((string) $status->name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                </span>
            </label>
        <?php endforeach; ?>
    </div>
</details>
