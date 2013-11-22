<?php
$thumbnail = (!empty($item->medias->thumbnail) ? \Arr::get($enhancer_args, 'thumbnail', 'left') : false);
?>
<article class="article">

    <div class="title">
		<?php if (\Arr::get($enhancer_args, 'link_on_title', true)) { ?>
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

    <div class="summary">
		<?php if (in_array($thumbnail, array('left', 'right'))) { ?>
		<img class="thumbnail thumbnail-<?= $thumbnail ?>" src="<?= $item->medias->thumbnail->get_public_path_resized(400) ?>" title="<?= e($item->post_title) ?>" alt="" />
		<?php } ?>
        <p>
			<?= \Shao\Template\Tools::summarize($item->wysiwygs->content, array(
			'limit'		=> \Arr::get($enhancer_args, 'summary_limit', 500),
			'read_more'	=> '... <a href="'.$item->url().'">'._('Read more').'</a>',
		)) ?>
        </p>
    </div>

	<?php if ($thumbnail == 'bottom') { ?>
    <div class="thumbnail thumbnail-bottom">
        <img src="<?= $item->medias->thumbnail->getToolkitImage()->crop_resize(800, 200)->url(true) ?>" title="<?= e($item->post_title) ?>" alt="" />
    </div>
	<?php } ?>

</article>
