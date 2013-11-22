<?php
\Nos\I18n::current_dictionary(array('shao_blog::common'));
?>

<div>
    <p><label for="item_per_page"><?= __('Posts per page:') ?></label> <input type="text" name="item_per_page" id="item_per_page" value="<?= \Arr::get($enhancer_args, 'item_per_page', 10) ?>" /></p>
	<?php
	if (isset($params['renderer'])) {
		?>
        <p style="margin-top: 10px;">
            <label><?= __('Category:') ?></label>
			<?= $params['renderer'] ?>
        </p>
        <p style="margin-top: 5px;">
			<?= __('Leave blank to select all categories') ?>
        </p>
		<?php
	}
	?>
    <p style="margin-top: 15px;"><input type="checkbox" name="link_on_title" id="link_on_title" value="1" <?= \Arr::get($enhancer_args, 'link_on_title', 0) ? 'checked' : '' ?> /> <label for="link_on_title"><?= __('Clickable post titles') ?></label></p>
</div>
