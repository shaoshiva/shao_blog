<?php

namespace Shao\Blog;

class Model_Tag extends \Nos\Orm\Model
{
	protected static $_primary_key = array('tag_id');
	protected static $_table_name = 'shao_blog_tag';

	protected static $_title_property = 'tag_label';
	protected static $_properties = array(
		'tag_id' => array(
			'default' => null,
			'data_type' => 'int unsigned',
			'null' => false,
		),
		'tag_label' => array(
			'default' => null,
			'data_type' => 'varchar',
			'null' => false,
		),
	);

	protected static $_many_many = array(
		'posts' => array(
			'table_through' 	=> 'shao_blog_tag_post',
			'key_from' 			=> 'tag_id',
			'key_through_from' 	=> 'tapo_tag_id',
			'key_through_to'	=> 'tapo_post_id',
			'key_to' 			=> 'post_id',
			'cascade_save' 		=> true,
			'cascade_delete' 	=> false,
			'model_to'       	=> 'Shao\Blog\Model_Post',
		),
	);

	protected static $_behaviours = array(
		'Nos\Orm_Behaviour_Urlenhancer' => array(
			'enhancers' => array(),
		),
	);

	public static function _init()
	{
		\Nos\I18n::current_dictionary(array('shao_blog::common'));
		static::$_behaviours['Nos\Orm_Behaviour_Sharable'] = array(
			'data' => array(
				\Nos\DataCatcher::TYPE_TITLE => array(
					'value' => 'tag_label',
					'useTitle' => __('Use tag label'),
				),
				\Nos\DataCatcher::TYPE_URL => array(
					'value' => function($tag) {
						return $tag->url_canonical();
					},
					'options' => function($tag) {
						return $tag->urls();
					},
				),
			),
		);
	}
}
