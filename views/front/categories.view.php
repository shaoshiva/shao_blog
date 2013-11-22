<?php
\Nos\I18n::current_dictionary(array('shao_blog::common'));

$enhancer_args = (isset($enhancer_args) ? $enhancer_args : array());

if (!\Arr::get($enhancer_args, 'show_categories', true)) {
	return ;
}

if (empty($item)) {
	return ;
}

if (count($item->categories) > 0) {
	?>
	<div class="categories">
		<?php
		$categories = array();
		foreach ($item->categories as $category) {
			$categories[$category->url()] = $category->cat_title;
		}
		$categories_str = implode(', ', array_map(function($href, $title) {
			return '<a href="'.$href.'">'.e($title).'</a>';
		}, array_keys($categories), array_values($categories)));
		echo strtr(__('Categories: {{categories}}'), array('{{categories}}' => $categories_str));
		?>
	</div>
	<?php
}
