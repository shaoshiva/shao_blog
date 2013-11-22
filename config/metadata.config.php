<?php

return array(
    'name'    => 'Shao &#187; Blog',
    'version' => '0.1',
    'provider' => array(
        'name' => 'Shao',
    ),
    'namespace' => 'Shao\Blog',
    'i18n_file' => 'shao_blog::common',
    'launchers' => array(
		'shao_blog' => array(
			'name'    => 'Shao Blog',
			'action' => array(
				'action' => 'nosTabs',
				'tab' => array(
					'url' => 'admin/shao_blog/appdesk',
				),
			),
		),
    ),
	'icons' => array( //@todo: to be defined
		64 => '/static/apps/shao_blog/img/64/post.png',
		32 => '/static/apps/shao_blog/img/32/post.png',
		16 => '/static/apps/shao_blog/img/16/post.png',
	),
    'enhancers' => array(
		'shao_blog' => array(
			'title' => 'Blog',
			'desc'  => '',
			'urlEnhancer' => 'shao_blog/front/main',
			'dialog' => array(
				'contentUrl' => 'admin/shao_blog/application/popup',
				'width' => 370,
				'height' => 400,
				'ajax' => true,
			),
		),
    ),
	'data_catchers' => array(
		'shao_blog' => array(
			'title' => 'Blog',
			'description'  => '',
			'action' => array(
				'action' => 'nosTabs',
				'tab' => array(
					'url' => 'admin/shao_blog/post/insert_update/?context={{context}}&title={{urlencode:'.\Nos\DataCatcher::TYPE_TITLE.'}}&summary={{urlencode:'.\Nos\DataCatcher::TYPE_TEXT.'}}&thumbnail={{urlencode:'.\Nos\DataCatcher::TYPE_IMAGE.'}}',
					'label' => __('Add a post'),
				),
			),
			'onDemand' => true,
			'specified_models' => false,
			'required_data' => array(
				\Nos\DataCatcher::TYPE_TITLE,
			),
			'optional_data' => array(
				\Nos\DataCatcher::TYPE_TEXT,
				\Nos\DataCatcher::TYPE_IMAGE,
			),
		),
	),
);
