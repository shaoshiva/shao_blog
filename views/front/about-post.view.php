<?php if (\Arr::get($enhancer_args, 'show_author', true) || \Arr::get($enhancer_args, 'show_publication_date', true)) { ?>
<div class="about-post">
	<?php
	// Author
	if (\Arr::get($enhancer_args, 'show_author', true)) {
		$author = false;
		if (!empty($item->author)) {
			$author = '<a href="'.$item->author->url().'">'.e($item->author->fullname()).'</a>';
		} elseif (!empty($item->post_author_alias)) {
			$author = e($item->post_author_alias);
		}
		if (!empty($author)) {
			?>
            <span class="author">
					<?= strtr(__('Written by {{author}}'), array('{{author}}' => $author)); ?>
				</span>
			<?php
		}
	}

	// Publication date
	if (\Arr::get($enhancer_args, 'show_publication_date', true)) {
		?>
        <span class="date">
				<?= e(Date::forge(strtotime($item->post_created_at))->format(_('on %B %e, %Y'))); ?>
			</span>
		<?php
	}
	?>
</div>
<?php } ?>
