<?php

namespace Shao\Blog;

class Model_Post extends \Nos\Orm\Model {

	protected static $_primary_key = array('post_id');
	protected static $_table_name = 'shao_blog_post';

	protected static $_title_property = 'post_title';
	protected static $_properties = array(
		'post_id' => array(
			'default' => null,
			'data_type' => 'int unsigned',
			'null' => false,
		),
		'post_title' => array(
			'default' => null,
			'data_type' => 'varchar',
			'null' => false,
		),
		'post_summary' => array(
			'default' => null,
			'data_type' => 'text',
			'null' => false,
		),
		'post_author_alias' => array(
			'default' => null,
			'data_type' => 'varchar',
			'null' => true,
			'convert_empty_to_null' => true,
		),
		'post_author_id' => array(
			'default' => null,
			'data_type' => 'int unsigned',
			'null' => true,
			'convert_empty_to_null' => true,
		),
		'post_created_at' => array(
			'data_type' => 'timestamp',
			'null' => false,
		),
		'post_updated_at' => array(
			'data_type' => 'timestamp',
			'null' => false,
		),
		'post_context' => array(
			'default' => null,
			'data_type' => 'varchar',
			'null' => false,
		),
		'post_context_common_id' => array(
			'default' => null,
			'data_type' => 'int',
			'null' => false,
		),
		'post_context_is_main' => array(
			'default' => 0,
			'data_type' => 'tinyint',
			'null' => false,
		),
		'post_published' => array(
			'default' => null,
			'data_type' => 'tinyint',
			'null' => false,
		),
		'post_publication_start' => array(
			'default' => null,
			'data_type' => 'datetime',
			'null' => true,
			'convert_empty_to_null' => true,
		),
		'post_publication_end' => array(
			'default' => null,
			'data_type' => 'datetime',
			'null' => true,
			'convert_empty_to_null' => true,
		),
		'post_read' => array(
			'default' => null,
			'data_type' => 'int unsigned',
			'null' => false,
		),
		'post_virtual_name' => array(
			'default' => null,
			'data_type' => 'varchar',
			'null' => false,
		),
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
			'property'=>'post_created_at'
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
			'property'=>'post_updated_at'
		)
	);

	protected static $_behaviours = array(
		'Nos\Orm_Behaviour_Publishable' => array(
			'publication_state_property' => 'post_published',
			'publication_start_property' => 'post_publication_start',
			'publication_end_property' => 'post_publication_end',
		),
		'Nos\Orm_Behaviour_Urlenhancer' => array(
			'enhancers' => array('shao_blog'),
		),
		'Nos\Orm_Behaviour_Virtualname' => array(
			'events' => array('before_save', 'after_save'),
			'virtual_name_property' => 'post_virtual_name',
		),
		'Nos\Orm_Behaviour_Twinnable' => array(
			'events' => array('before_insert', 'after_insert', 'before_save', 'after_delete', 'change_parent'),
			'context_property'      => 'post_context',
			'common_id_property' => 'post_context_common_id',
			'is_main_property' => 'post_context_is_main',
			'invariant_fields'   => array(),
		),
	);

	protected static $_belongs_to  = array(
		'author' => array(
			'key_from' => 'post_author_id',
			'model_to' => 'Nos\User\Model_User',
			'key_to' => 'user_id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	protected static $_has_many  = array();

	protected static $_many_many = array(
		'categories' => array(
			'table_through' 	=> 'shao_blog_category_post',
			'key_from' 			=> 'post_id',
			'key_through_from' 	=> 'capo_post_id',
			'key_through_to' 	=> 'capo_cat_id',
			'key_to' 			=> 'cat_id',
			'cascade_save' 		=> true,
			'cascade_delete' 	=> false,
			'model_to'       	=> 'Shao\Blog\Model_Category',
		),
		'tags' => array(
			'table_through' 	=> 'shao_blog_tag_post',
			'key_from' 			=> 'post_id',
			'key_through_from' 	=> 'tapo_post_id',
			'key_through_to'	=> 'tapo_tag_id',
			'key_to' 			=> 'tag_id',
			'cascade_save' 		=> true,
			'cascade_delete' 	=> false,
			'model_to'       	=> 'Shao\Blog\Model_Tag',
		),
	);

	public static function _init()
	{
		\Nos\I18n::current_dictionary(array('shao_blog::common'));
		static::$_behaviours['Nos\Orm_Behaviour_Sharable'] = array(
			'data' => array(
				\Nos\DataCatcher::TYPE_TITLE => array(
					'value' => 'post_title',
					'useTitle' => __('Use post title'),
				),
				\Nos\DataCatcher::TYPE_URL => array(
					'value' =>
					function($post)
					{
						$urls = $post->urls();
						if (empty($urls)) {
							return null;
						}
						reset($urls);

						return key($urls);
					},
					'options' =>
					function($post)
					{
						return $post->urls();
					},
				),
				\Nos\DataCatcher::TYPE_IMAGE => array(
					'value' =>
					function($post) {
						$possible = $post->possible_medias();

						return \Arr::get(array_keys($possible), 0, null);
					},
					'options' =>
					function($post) {
						return $post->possible_medias();
					},
				),
				\Nos\DataCatcher::TYPE_TEXT => array(
					'value' => 'post_summary',
					'useTitle' => __('Use post summary'),
				),
			),
		);
	}

	public static function relations($specific = false)
	{
		list($app) = \Config::configFile(get_called_class());
		\Config::load($app.'::config', true);
		$with_comments = \Config::get($app.'::config.comments.enabled', true);
		if ($with_comments) {
			static::$_has_many['comments'] = array(
				'key_from' => 'post_id',
				'model_to' => '\Nos\Comments\Model_Comment',
				'key_to' => 'comm_foreign_id',
				'cascade_save' => false,
				'cascade_delete' => true,
				'conditions' => array(
					'where' => array(
						array(
							'comm_from_table', '=', static::$_table_name
						),
					),
					'order_by' => array(
						'comm_created_at' => 'ASC'
					),
				),
			);
		}

		return parent::relations($specific);
	}

	public static function get_primary_key()
	{
		return static::$_primary_key;
	}

	public static function get_table_name()
	{
		return static::$_table_name;
	}

	public static function get_first($options, $preview = false)
	{
		// First argument is a string => it's the virtual name
		if (!is_array($options)) {
			$options = array(
				'where' => array(
					array('post_virtual_name', '=', $options),
				),
			);
		}

		if (!$preview) {
			$options['where'][] = array('published', true);
		}

		return static::find('first', $options);
	}

	public static function get_query($params)
	{
		$query = static::query(array(
			'where' => array(
				array('published', true),
			),
		))->related(array('author'));

		$query->where(array('post_context', $params['context']));

		if (!empty($params['author'])) {
			$query->where(array('post_author_id', $params['author']->user_id));
		}
		if (!empty($params['tag'])) {
			$query->related(array('tags'));
			$query->where(array('tags.tag_label', $params['tag']->tag_label));
		}
		if (!empty($params['category'])) {
			$query->related(array('categories'));
			$query->where(array('categories.cat_id', $params['category']->cat_id));
		}
		if (!empty($params['categories'])) {
			$query->related(array('categories'));
			$cat_ids = array();
			foreach ($params['categories'] as $category) {
				$cat_ids[] = $category->cat_id;
			}
			$query->where(array('categories.cat_id', 'IN', $cat_ids));
		}
		if (!empty($params['order_by'])) {
			$query->order_by($params['order_by']);
		}
		if (!empty($params['offset'])) {
			$query->rows_offset($params['offset']);
		}
		if (!empty($params['limit'])) {
			$query->rows_limit($params['limit']);
		}

		return $query;
	}

	public static function get_all($params)
	{
		$query = static::get_query($params);

		$posts = static::get_all_from_query($query);

		// Re-fetch with a 2nd request to get all the relations (not only the filtered ones)
		// @todo : to take a look later, see if the orm can't be fixed
		if (!empty($posts) && (!empty($params['tag']) || !empty($params['category']) || !empty($params['categories']))) {
			$posts = static::fetch_relations($posts, $params['order_by']);
		}

		return $posts;
	}

	public static function get_all_from_query($query)
	{
		$posts = $query->get();
		static::count_multiple_comments($posts);
		return $posts;
	}

	public static function fetch_relations($posts, $order_by)
	{
		$keys = array_keys((array) $posts);
		$posts = static::query(array(
			'where' => array(
				array('post_id', 'IN', $keys),
			),
			'order_by' => $order_by,
			'related' => array('author', 'tags', 'categories'),
		))->get();
		return $posts;
	}

	public static function count_all($params)
	{
		$query = static::get_query($params);

		return $query->count();
	}

	public static function count_multiple_comments($items)
	{
		$class = get_called_class();
		list($app) = \Config::configFile($class);
		\Config::load($app.'::config', true);
		$with_comment = \Config::get($app.'::config.comments.enabled');
		if (!$with_comment || count($items) == 0) {
			return $items;
		}
		$ids = array();

		foreach ($items as $post) {
			$ids[] = $post->id;
		}

		$comments_count = \Db::select(\Db::expr('COUNT(comm_id) AS count_result'), 'comm_foreign_id')
			->from(\Nos\Comments\Model_Comment::table())
			->where('comm_foreign_id', 'in', $ids)
			->and_where('comm_from_table', '=', static::$_table_name)
			->group_by('comm_foreign_id')
			->execute()->as_array();

		$comments_count = \Arr::assoc_to_keyval($comments_count, 'comm_foreign_id', 'count_result');

		foreach ($items as $key => $item) {
			if (isset($comments_count[$items[$key]->id])) {
				$items[$key]->nb_comments = $comments_count[$items[$key]->id];
			}
		}

		return $items;
	}

	protected $nb_comments = null;
	public function count_comments()
	{
		if ($this->nb_comments === null) {
			$this->nb_comments = \Nos\Comments\Model_Comment::count(array(
				'where' => array(
					array('comm_foreign_id' => $this->id),
					array('comm_from_table' => static::$_table_name)
				)
			));
		}

		return $this->nb_comments;
	}
}
