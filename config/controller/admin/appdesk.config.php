<?php

return array(
	'model' => 'Shao\Blog\Model_Post',
	'toolbar' => array(
		'models' => array(
			'Shao\Blog\Model_Post',
			'Shao\Blog\Model_Category',
		)
	),
	'query' => array(
		'model' => 'Shao\Blog\Model_Post',
		'related' => array('linked_medias'),
		'order_by' => array('post_created_at' => 'DESC'),
		'limit' => 20,
	),
	'search_text' => 'post_title',
	'thumbnails' => true,
	'inspectors' => array(
		'author',
		'tag',
		'category',
		'date',
	)
);