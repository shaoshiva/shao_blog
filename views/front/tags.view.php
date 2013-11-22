<?php

\Nos\I18n::current_dictionary(array('shao_blog::common'));

$enhancer_args = (isset($enhancer_args) ? $enhancer_args : array());

if (!\Arr::get($enhancer_args, 'show_tags', true)) {
	return ;
}

if (empty($item)) {
	return ;
}

if (count($item->tags) > 0) {
	?>
	<div class="tags">
		<?php
		$tags = array();
		foreach ($item->tags as $tag) {
			$tags[$tag->url()] = $tag->tag_label;
		}
		$tags_str = implode(', ', array_map(function($href, $title) {
			return '<a href="'.$href.'">'.e($title).'</a>';
		}, array_keys($tags), array_values($tags)));
		echo strtr(__('Tags: {{tags}}'), array('{{tags}}' => $tags_str));
		?>
	</div>
	<?php
}
