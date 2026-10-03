<div class="">
    <div class="panel">
        <?php if (isset($form_url)):?>
            <?=form_open($form_url, isset($form_attributes) ? $form_attributes : [] )?>
        <?php elseif (isset($footer) AND $footer['type'] == 'bulk_action_form'):?>
        <form><!-- currently EE's bulk action setup requires a form wrapper no matter what -->
            <?php endif;?>
			<div class="panel-heading">
				<div class="title-bar">
					<?php if (isset($cp_page_title)):?>
						<h3 class="title-bar__title"><?= htmlspecialchars((string) $cp_page_title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h3>
					<?php elseif (isset($wrapper_header)):?>
						<h3 class="title-bar__title"><?= htmlspecialchars((string) $wrapper_header, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h3>
					<?php endif;?>
					<?php if ( ! empty($form_right_links)):?>
						<fieldset class="tbl-search right title-bar__extra-tools">
							<?php foreach ($form_right_links as $link_data):?>
								<?php $attrs = $link_data['attrs'] ?? ''; ?>
								<a <?= $attrs ?> <?= str_contains($attrs, 'class=') ? '' : 'class="tn button button--primary"' ?> href="<?= htmlspecialchars((string) $link_data['link'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
									<?= htmlspecialchars((string) $link_data['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
								</a>
							<?php endforeach;?>
						</fieldset>
					<?php endif;?>
					<?php if ( ! empty($form_dropdown_links)):?>
						<fieldset class="tbl-search right title-bar__extra-tools">
							<?php foreach ($form_dropdown_links as $dropdownTitle => $links): ?>
								<div class="dropdown-field">
									<select name="form_handle" class="">
										<?php foreach ($links as $link_data):?>
										<option value="<?= htmlspecialchars((string) $link_data['link'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" <?= $link_data['attrs'] ?? '' ?>>
											<?= htmlspecialchars((string) $link_data['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
											</option>
										<?php endforeach;?>
									</select>
									<a class="btn action tn">
										<?php echo htmlspecialchars((string) $dropdownTitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
									</a>
								</div>
							<?php endforeach;?>
						</fieldset>
					<?php endif;?>

				</div>
			</div>

			<?php if(ee('CP/Alert')->getAllInlines() !== '') : ?>
			<div class="panel-body">
				<?=ee('CP/Alert')->getAllInlines()?>
			</div>
			<?php endif ?>

			<?=$child_view?>

			<?php if (isset($pagination)):?>
                <div class="ss_clearfix"><?=$pagination?></div>
            <?php endif;?>
            <?php if (isset($footer)):?>
                <?php if ($footer['type'] == 'form'):?>
                    <fieldset class="form-ctrls">
                        <?php if (isset($footer['submit_lang'])):?>
                            <input class="btn submit" type="submit" value="<?= htmlspecialchars((string) $footer['submit_lang'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" />
                        <?php endif;?>
                    </fieldset>
                <?php elseif ($footer['type'] == 'bulk_action_form'): ?>
                    <fieldset class="bulk-action-bar hidden">
						<select name="bulk_action" class="select-popup button--small">
                            <?php if (isset($footer['bulk_actions'])):?>
                                <?php foreach($footer['bulk_actions'] as $value => $label):?>
                                    <option value="<?= htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" data-confirm-trigger="selected" rel="modal-confirm-<?= htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                                        <?= htmlspecialchars((string) $label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                                    </option>
                                <?php endforeach;?>
                            <?php else: ?>
                                <option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove">
                                    <?=lang('remove')?>
                                </option>
                            <?php endif;?>
                        </select>
                        <button class="btn button--primary button--small submit" data-conditional-modal="confirm-trigger">
                            <?= htmlspecialchars((string) $footer['submit_lang'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                        </button>
                    </fieldset>
                <?php else:?>
                <?php endif;?>
            <?php endif;?>
            <?php if (isset($form_url) || (isset($footer) AND $footer['type'] == 'bulk_action_form')):?>
        </form> <!-- end of wrapper -->
    <?php endif;?>
		<?=isset($blocks['addonModals']) ? $blocks['addonModals'] : ''?>
    </div>
</div>
