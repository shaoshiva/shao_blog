<?php

return array(
    'popup' => array(
        'layout' => array(
            'view' => 'shao_blog::admin/application/popup',
        ),
    ),
    'category_selector_options' => array(
        'width'                     => '260px',
        'height'                    => '200px',
        'input_name'                => 'cat_id',
        'treeOptions'               => array(
            'context'               => \Input::get('nosContext', false) ?: \Nos\Tools_Context::defaultContext(),
        ),
        'multiple'              => '0',
        'inspector'             => 'admin/shao_blog/inspector/category',
        'model'                 => 'Shao\Blog\Model_Category',
        'main_column'           => 'cat_title',
    )
);
