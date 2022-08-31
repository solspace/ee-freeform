<?php
/**
 * @var \Solspace\Addons\FreeformNext\Library\DataObjects\PluginUpdate[] $updates
 */
?>
<link rel="stylesheet" href="<?php echo URL_THIRD_THEMES ?>freeform_next/css/updates.css"/>


<div class="panel">
	<div class="panel-heading">
		<div class="title-bar title-bar--large">
			<h3 class="title-bar__title">
				Updates Available (<?php echo count($updates) ?>)
			</h3>

			<div class="title-bar__extra-tools">
				<a class="button button--primary" target="_blank" href="https://expressionengine.com/store/purchases">
					Get Latest Version
				</a>
			</div>
		</div>
	</div>

	<div class="panel-body">


		<?php foreach ($updates as $item) : ?>

			<section class="item-wrap" style="margin-bottom:25px;">
				<div class="item">
					<h3>
						Freeform <b><?= $item->getVersion() ?></b>
						<i style="font-size:75%;font-style:italic;font-weight:normal;margin-left:5px;">
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
