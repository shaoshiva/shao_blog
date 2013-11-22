<?php
$thumbnail = (!empty($item->medias->thumbnail) ? \Arr::get($enhancer_args, 'thumbnail', 'top') : false);
?>
<div class="shao_blog shao_blog_post">

    <div class="title">
		<?php if (\Arr::get($enhancer_args, 'link_on_title')) { ?>
        <a href="<?= $item->url() ?>"><?= e($item->post_title) ?></a>
		<?php } else { ?>
		<?= e($item->post_title) ?>
		<?php } ?>
    </div>

	<?= \View::forge('shao_blog::front/about-post', array('item' => $item), false) ?>

	<?php if ($thumbnail == 'top') { ?>
    <div class="thumbnail thumbnail-top">
        <img src="<?= $item->medias->thumbnail->getToolkitImage()->crop_resize(800, 200)->url(true) ?>" title="<?= e($item->post_title) ?>" alt="" />
    </div>
	<?php } ?>

    <div class="content">
		<?php if (in_array($thumbnail, array('left', 'right'))) { ?>
		<img class="thumbnail thumbnail-<?= $thumbnail ?>" src="<?= $item->medias->thumbnail->get_public_path_resized(400) ?>" title="<?= e($item->post_title) ?>" alt="" width="0" height="0" />
		<?php } ?>
		<?= \Nos\Nos::parse_wysiwyg($item->wysiwygs->content) ?>
    </div>

	<?php if ($thumbnail == 'bottom') { ?>
    <div class="thumbnail thumbnail-bottom">
        <img src="<?= $item->medias->thumbnail->getToolkitImage()->crop_resize(800, 200)->url(true) ?>" title="<?= e($item->post_title) ?>" alt="" />
    </div>
	<?php } ?>

	<?= \View::forge('shao_blog::front/tags', array('item' => $item), false) ?>
	<?= \View::forge('shao_blog::front/categories', array('item' => $item), false) ?>

	<?php
	// Commentaires
	if (\Arr::get($enhancer_args, 'show_comments', true)) {
		echo \View::forge('shao_blog::front/comments', array(
			'item' => $item,
			'enhancer_args' => $enhancer_args,
			'add_comment_success' => $add_comment_success,
		), false);
	}
	?>
	<?php
	?>
</div>
