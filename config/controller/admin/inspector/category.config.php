<?php

\Nos\I18n::current_dictionary('shao_blog::common');

return array(
    'model' => 'Shao\Blog\Model_Category',
//	'query' => array(
//		'order_by' => 'cat_title',
//	),
    'input' => array(
        'key' => 'categories.cat_id'
    ),
    'root_node' => array(
        'cat_title' => __('Root'),
    ),
);
