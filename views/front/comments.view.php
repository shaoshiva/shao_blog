<?php
\Nos\I18n::current_dictionary(array('noviusos_comments::common', 'shao_blog::common'));

$enhancer_args = (isset($enhancer_args) ? $enhancer_args : array());

// Comments not shown ?
if (!\Arr::get($enhancer_args, 'show_comments', true)) {
	return ;
}

if (empty($item)) {
	return ;
}
?>
<div class="comments" id="comments">
	<?php
	// Comment counter
	if ($item->count_comments() > 1) {
		$class = 'nb-many';
	} elseif ($item->count_comments() > 0) {
		$class = 'nb-one';
	} else {
		$class = 'nb-zero';
	}
	?>
    <div class="shao_blog_nb_comments <?= $class ?>">
		<?php
		if ($item->count_comments() > 0) {
			if ($item->count_comments() > 1) {
				echo e(strtr(__('{{nb}} comments'), array('{{nb}}' => $item->count_comments())));
			} else {
				echo e(__('1 comment'));
			}
		} else {
			echo e(__('No comments'));
		}
		?>
    </div>
	<?php

	// Comment list
	echo \View::forge('noviusos_comments::front/list', array(
		'from_item' => $item,
		'comments' => $item->comments
	), true);

	// Comment form
	echo \View::forge('noviusos_comments::front/form', array(
		'add_comment_success' => $add_comment_success,
		'use_recaptcha' => \Arr::get($enhancer_args, 'comments_use_recaptcha')
	), true);
	?>
</div>
