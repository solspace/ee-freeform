<?php
/**
 * @var \Solspace\Addons\FreeformNext\Library\DataObjects\PluginUpdate[] $updates
 */
?>
<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/updates.css"/>


<div class="box">
    <div class="tbl-ctrls">

        <fieldset class="tbl-search right">
            <a class="btn tn action" target="_blank" href="https://solspace.com/account/software">
                Get Latest Version
            </a>
        </fieldset>

        <h1 style="margin-bottom: 10px;">
            Updates Available (<?php echo count($updates) ?>)
        </h1>

        <?php foreach ($updates as $item) : ?>

            <section class="item-wrap">
                <div class="item">
                    <h3>
                        Freeform Next <b><?= $item->getVersion() ?></b>
                        <i>
                            (released on
                            <?= ee()->localize->format_date($format, $item->getDate()->getTimestamp()) ?>)
                        </i>
                    </h3>
                    <div class="message">
                        <ul class="update-list">
                            <?php foreach ($item->getFeatures() as $note) : ?>
                                <li class="feature">
                                    <?php echo $note ?>
                                </li>
                            <?php endforeach; ?>
                            <?php foreach ($item->getNotes() as $note) : ?>
                                <li class="note">
                                    <?php echo $note ?>
                                </li>
                            <?php endforeach; ?>
                            <?php foreach ($item->getBugfixes() as $note) : ?>
                                <li class="bugfix">
                                    <?php echo $note ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </section>

        <?php endforeach; ?>
    </div>
</div>
