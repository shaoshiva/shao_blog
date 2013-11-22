<?php

namespace Shao\Blog;

class Model_Category extends \Nos\Orm\Model {

	protected static $_primary_key = array('cat_id');
	protected static $_table_name = 'shao_blog_category';

	protected static $_title_property = 'cat_title';
	protected static $_properties = array(
		'cat_id' => array(
			'default' => null,
			'data_type' => 'int',
			'null' => false,
		),
		'cat_title' => array(
			'default' => null,
			'data_type' => 'varchar',
			'null' => false,
		),
		'cat_virtual_name' => array(
			'default' => null,
			'data_type' => 'varchar',
			'null' => false,
		),
		'cat_context' => array(
			'default' => null,
			'data_type' => 'varchar',
			'null' => false,
		),
		'cat_context_common_id' => array(
			'default' => null,
			'data_type' => 'int',
			'null' => false,
		),
		'cat_context_is_main' => array(
			'default' => 0,
			'data_type' => 'tinyint',
			'null' => false,
		),
		'cat_parent_id' => array(
			'default' => null,
			'data_type' => 'int',
			'null' => true,
			'convert_empty_to_null' => true,
		),
		'cat_sort' => array(
			'default' => null,
			'data_type' => 'float',
			'null' => true,
			'convert_empty_to_null' => true,
		),
		'cat_created_at' => array(
			'data_type' => 'timestamp',
			'null' => false,
		),
		'cat_updated_at' => array(
			'data_type' => 'timestamp',
			'null' => false,
		),
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
			'property'=>'cat_created_at'
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
			'property'=>'cat_updated_at'
		)
	);

	protected static $_behaviours = array(
		'Nos\Orm_Behaviour_Tree' => array(
			'events' => array('before_query', 'before_delete'),
			'parent_relation' => 'parent',
			'children_relation' => 'children',
		),
		'Nos\Orm_Behaviour_Sortable' => array(
			'events' => array('before_insert', 'before_save', 'after_save'),
			'sort_property' => 'cat_sort',
		),
		'Nos\Orm_Behaviour_Urlenhancer' => array(
			'enhancers' => array(),
		),
		'Nos\Orm_Behaviour_Virtualname' => array(
			'events' => array('before_save', 'after_save'),
			'virtual_name_property' => 'cat_virtual_name',
		),
		'Nos\Orm_Behaviour_Twinnable' => array(
			'events' => array('before_insert', 'after_insert', 'before_save', 'after_delete', 'change_parent'),
			'context_property'      => 'cat_context',
			'common_id_property' => 'cat_context_common_id',
			'is_main_property' => 'cat_context_is_main',
			'invariant_fields'   => array(),
		),
	);

	protected static $_has_many  = array(
		'children' => array(
			'key_from'       => 'cat_id', //cat_id
			'model_to'       => 'Shao\Blog\Model_Category',
			'key_to'         => 'cat_parent_id', //cat_parent_id
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_belongs_to = array(
		'parent' => array(
			'key_from'       => 'cat_parent_id', //cat_parent_id
			'model_to'       => 'Shao\Blog\Model_Category',
			'key_to'         => 'cat_id', //cat_id
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_many_many = array(
		'posts' => array(
			'table_through' => 'shao_blog_category_post',
			'key_from' => 'cat_id',
			'key_through_from' => 'cat_id',
			'key_through_to' => 'post_id',
			'key_to' => 'post_id',
			'cascade_save' => true,
			'cascade_delete' => false,
			'model_to'       => 'Shao\Blog\Model_post',
		),
	);

	public static function _init() {
		\Nos\I18n::current_dictionary(array('shao_blog::common'));
		static::$_behaviours['Nos\Orm_Behaviour_Sharable'] = array(
			'data' => array(
				\Nos\DataCatcher::TYPE_TITLE => array(
					'value' => 'cat_title',
					'useTitle' => __('Use category title'),
				),
				\Nos\DataCatcher::TYPE_URL => array(
					'value' => function($category) {
						return $category->url_canonical();
					},
					'options' => function($category) {
						return $category->urls();
					},
				),
			),
		);
	}

	public static function get_primary_key() {
		return static::$_primary_key;
	}

	public static function get_table_name() {
		return static::$_table_name;
	}
}
