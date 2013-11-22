<div class="shao_blog shao_blog_posts">
	<?

	$title = false;
	if ($type == 'tag') {
		$title = strtr(__('Tag: {{tag}}'), array('{{tag}}' => $item->tag_label));
		$link  = $item->url();
	}
	if ($type == 'category') {
		$title = strtr(__('Category: {{category}}'), array('{{category}}' => $item->cat_title));
		$link  = $item->url();
	}
	if ($title) {
		?>
		<div class="main-title"><a href="<?= $link ?>"><?= e($title) ?></a></div>
		<?
	}
	?>

    <div class="list">
		<?php
		foreach ($posts as $post) {
			echo \View::forge('shao_blog::front/item', array('item' => $post), false);
		}
		?>
    </div>

	<?php
	if (!empty($pagination)) {
		echo \View::forge('shao_blog::front/pagination', array(
			'type' => $type,
			'item' => $item,
			'pagination' => $pagination
		), false);
	}
	?>
</div>
