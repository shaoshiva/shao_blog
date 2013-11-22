<?php

namespace Shao\Blog;

use Nos\Controller_Front_Application;
use \Nos\Comments\Model_Comment;
use View;

class Controller_Front extends Controller_Front_Application
{
	/**
	 * @var \Nos\Pagination
	 */
	public $pagination		= false;
	public $current_page 	= 1;

	public $page_from 		= false;

	public $segments		= array();

	public $action;

	public static $tag_class;
	public static $post_class;
	public static $category_class;
	public static $author_class;

	public function after($response) {

		// Note to translator: The following texts are related to RSS feeds
		$this->main_controller->addMeta('<link rel="alternate" type="application/rss+xml" title="'.htmlspecialchars(static::_html_entity_decode(__('Posts list'))).'" href="'.$this->main_controller->getContextUrl().$this->main_controller->getEnhancedUrlPath().'rss/posts.html">');
		$this->main_controller->addMeta('<link rel="alternate" type="application/rss+xml" title="'.htmlspecialchars(static::_html_entity_decode(__('Comments list'))).'" href="'.$this->main_controller->getContextUrl().$this->main_controller->getEnhancedUrlPath().'rss/comments.html">');

		$this->main_controller->addCss('static/apps/shao_blog/css/blog.css');

		return parent::after($response);
	}

	public function action_main($args = array()) {
		$this->page_from = $this->main_controller->getPage();
		$enhancer_url = $this->main_controller->getEnhancerUrl();

		$this->config['item_per_page'] = (int) isset($args['item_per_page']) ? $args['item_per_page'] : $this->config['item_per_page'];
		\View::set_global('enhancer_args', $args);

		$this->segments = $this->segments = explode('/', $enhancer_url);

		// Actions
		if (!empty($this->segments[1])) {
			switch ($this->segments[0]) {
				
				// Stats
				case 'stats':
					$post = $this->_get_post(array(
						'where' => array(
							array('post_id', $this->segments[1]),
						),
					));
					if (!empty($post)) {
						$stats = \Session::get('shao_blog_stats', array());
						if (!in_array($post->post_id, $stats)) {
							$post->post_read++;
							$post->save();
							$stats[] = $post->post_id;
							\Session::set('shao_blog_stats', $stats);
							\Session::write();
						}
					}
					\Nos\Tools_File::send(DOCROOT.'static/apps/shao_blog/img/transparent.gif');
					break;

				// Paginated list
				case 'page':
					$this->init_pagination(empty($this->segments[1]) ? 1 : $this->segments[1]);
					return $this->display_list($args);
					break;

				// Author
				case 'author':
					$this->init_pagination(!empty($this->segments[2]) ? $this->segments[2] : 1);
					return $this->display_list_by_author($args);
					break;

				// Tag
				case 'tag':
					$this->init_pagination(!empty($this->segments[2]) ? $this->segments[2] : 1);
					return $this->display_list_by_tag($args);
					break;

				// Category
				case 'category':
					$this->init_pagination(!empty($this->segments[2]) ? $this->segments[2] : 1);
					return $this->display_list_by_category($args);
					break;
				
				case 'rss':
					$rss = \Nos\Tools_RSS::forge(array(
						'link' => $this->main_controller->getUrl(),
						'language' => \Nos\Tools_Context::locale($this->page_from->page_context),
					));
					if ($this->segments[1] === 'posts') {
						if (empty($this->segments[2])) {
							$posts = $this->_get_posts();
							$rss->set(array(
								'title' => static::_html_entity_decode(__('Posts list')),
								'description' => static::_html_entity_decode(__('The full list of blog posts.')),
							));
						} elseif ($this->segments[2] === 'category' && !empty($this->segments[3])) {
							$category = $this->_get_category($this->segments[3]);
							$posts = $this->_get_posts(array('category' => $category));
							$rss->set(array(
								'title' => static::_html_entity_decode(strtr(__('{{category}}: Posts list'), array('{{category}}' => $category->cat_title))),
								'description' => static::_html_entity_decode(strtr(__('Blog posts listed under the ‘{{category}}’ category.'), array('{{category}}' => $category->cat_title))),
							));
						} elseif ($this->segments[2] === 'tag' && !empty($this->segments[3])) {
							$tag = $this->_get_tag($this->segments[3]);
							$posts = $this->_get_posts(array('tag' => $tag));
							$rss->set(array(
								'title' => static::_html_entity_decode(strtr(__('{{tag}}: Posts list'), array('{{tag}}' => $tag->tag_label))),
								'description' => static::_html_entity_decode(strtr(__('Blog posts listed under the ‘{{tag}}’ tag.'), array('{{tag}}' => $tag->tag_label))),
							));
						} elseif ($this->segments[2] === 'author' && !empty($this->segments[3])) {
							$author = $this->_get_author($this->segments[3]);
							$posts = $this->_get_posts(array('author' => $author));
							$rss->set(array(
								'title' => static::_html_entity_decode(strtr(__('{{author}}: Posts list'), array('{{author}}' => $author->fullname()))),
								'description' => static::_html_entity_decode(strtr(__('Blog posts written by {{author}}.'), array('{{author}}' => $author->fullname()))),
							));
						} else {
							throw new \Nos\NotFoundException();
						}
						$items = array();
						foreach ($posts as $post) {
							$items[] = static::_get_rss_post($post);
						}
						$rss->set_items($items);

					} elseif ($this->segments[1] === 'comments') {
						if (empty($this->segments[2])) {
							$rss->set(array(
								'title' => static::_html_entity_decode(__('Comments list')),
								'description' => static::_html_entity_decode(__('The full list of comments.')),
							));

							$comments = \Nos\Comments\Model_Comment::find('all', array(
								'order_by' => array('comm_created_at' => 'DESC'),
							));
						} else {
							$post = $this->_get_post(array(
								'where' => array(
									array('post_virtual_name', '=', $this->segments[2]),
									array('post_context', '=', $this->page_from->page_context),
								),
								'related' => 'comments',
								'order_by' => array('comments.comm_created_at' => 'DESC'),
							));
							if (empty($post)) {
								throw new \Nos\NotFoundException();
							}

							$rss->set(array(
								'title' => static::_html_entity_decode(strtr(__('{{post}}: Comments list'), array('{{post}}' => $post->post_title))),
								'description' => static::_html_entity_decode(strtr(__('Comments to the post ‘{{post}}’.'), array('{{post}}' => $post->post_title))),
							));

							$comments = $post->comments;
						}
						$items = array();
						foreach ($comments as $comment) {
							$item = static::_get_rss_comment($comment);
							if (!empty($item)) {
								$items[] = $item;
							}
						}
						$rss->set_items($items);

					}

					$this->main_controller->setHeader('Content-Type', 'application/xml');
					$this->main_controller->setCacheDuration($this->config['rss_cache_duration']);
					return $this->main_controller->sendContent($rss->build());
					break;

				// Nothing to do...
				default:
					throw new \Nos\NotFoundException();
			}
		}
		
		// Display post
		elseif (!empty($this->segments[0])) {
			$this->action = 'display_item';
			return $this->display_item();
		}
		
		// Display post list
		else {
			return $this->display_list($args);
		}
	}
	
	/**
	 * Display post list
	 * 
	 * @param $args
	 * @return \Fuel\Core\View
	 */
	public function display_list($args = array())
	{
		$this->init_pagination(1);

		$posts = $this->_get_posts($args);

		return \View::forge(
			$this->config['views']['list'],
			array(
				'posts' => $posts,
				'type' => 'main',
				'item' => 'main',
				'pagination' => $this->pagination,
			),
			false
		);
	}

	/**
	 * Display tag's posts
	 *
	 * @param array $args
	 * @return \Fuel\Core\View
	 */
	public function display_list_by_tag($args = array())
	{
		$tag = $this->segments[1];
		$tag = $this->_get_tag($tag);
		$posts = $this->_get_posts(array('tag' => $tag));

		$this->main_controller->addMeta('<link rel="alternate" type="application/rss+xml" title="'.htmlspecialchars(static::_html_entity_decode(strtr(__('{{tag}}: Posts list'), array('{{tag}}' => $tag->tag_label)))).'" href="'.$this->main_controller->getContextUrl().$this->main_controller->getEnhancedUrlPath().'rss/posts/tag/'.urlencode($tag->tag_label).'.html">');

		return View::forge('shao_blog::front/list', array(
			'posts'       => $posts,
			'type'        => 'tag',
			'item'        => $tag,
			'pagination' => $this->pagination,
		), false);
	}

	/**
	 * Display cateogyr's posts
	 *
	 * @param array $args
	 * @return \Fuel\Core\View
	 */
	public function display_list_by_category($args = array())
	{
		$category = $this->segments[1];
		$category = $this->_get_category($category);
		$posts = $this->_get_posts(array('category' => $category));

		$this->main_controller->addMeta('<link rel="alternate" type="application/rss+xml" title="'.htmlspecialchars(static::_html_entity_decode(strtr(__('{{category}}: Posts list'), array('{{category}}' => $category->cat_title)))).'" href="'.$this->main_controller->getContextUrl().$this->main_controller->getEnhancedUrlPath().'rss/posts/category/'.urlencode($category->cat_virtual_name).'.html">');

		return View::forge('shao_blog::front/list', array(
			'posts'       => $posts,
			'type'        => 'category',
			'item'        => $category,
			'pagination' => $this->pagination,
		), false);
	}

	/**
	 * Display author's posts
	 *
	 * @param array $args
	 * @return \Fuel\Core\View
	 */
	public function display_list_by_author($args = array())
	{
		// Get the author
		$parts_author = $this->segments[1];
		$array_author = explode('_', $parts_author);
		// The last part is the id
		$id_author = array_pop($array_author);
		$author = $this->_get_author($id_author);

		// Get posts from author
		$posts = $this->_get_posts(array('author' => $author));

		$this->main_controller->addMeta('<link rel="alternate" type="application/rss+xml" title="'.htmlspecialchars(static::_html_entity_decode(strtr(__('{{author}}: Posts list'), array('{{author}}' => $author->fullname())))).'" href="'.$this->main_controller->getContextUrl().$this->main_controller->getEnhancedUrlPath().'rss/posts/author/'.urlencode($id_author).'.html">');

		return View::forge('shao_blog::front/list', array(
			'posts'       => $posts,
			'type'        => 'author',
			'item'        => $author,
			'pagination' => $this->pagination,
		), false);
	}

	/**
	 * Display a single item (outside a list context)
	 *
	 * @param array $args
	 * @return \Fuel\Core\View
	 * @throws \Nos\NotFoundException
	 */
	public function display_item($args = array())
	{
		list($item_virtual_name) = $this->segments;
		$post = $this->_get_post(array(
			'where' => array(
				array('post_virtual_name', '=', $item_virtual_name),
				array('post_context', '=', $this->page_from->page_context),
			),
		));
		if (empty($post)) {
			throw new \Nos\NotFoundException();
		}

		$this->main_controller->addMeta('<link rel="alternate" type="application/rss+xml" title="'.htmlspecialchars(static::_html_entity_decode(strtr(__('{{post}}: Comments list'), array('{{post}}' => $post->post_title)))).'" href="'.$this->main_controller->getContextUrl().$this->main_controller->getEnhancedUrlPath().'rss/comments/'.urlencode($post->post_virtual_name).'.html">');

		$page = $this->main_controller->getPage();
		$this->main_controller->setTitle($page->page_title.' - '.$post->post_title);
		$page->page_title = $post->post_title;
		$add_comment_success = 'none';
		if (\Arr::get($this->app_config, 'comments.enabled') && \Arr::get($this->app_config, 'comments.can_post')) {
			if ($this->app_config['comments']['use_recaptcha']) {
				\Package::load('fuel-recatpcha', APPPATH.'packages/fuel-recaptcha/');
			}
			$add_comment_success = $this->_add_comment($post);
		}

		return \View::forge($this->config['views']['item'], array(
			'add_comment_success' => $add_comment_success,
			'item' => $post,
		), false);
	}

	/**
	 * Get a post
	 * 
	 * @param array $options
	 * @return mixed
	 */
	protected function _get_post($options = array()) {
		return Model_post::get_first($options, $this->main_controller->isPreview());
	}

	/**
	 * Get a category
	 * 
	 * @param $category
	 * @return mixed
	 * @throws \Nos\NotFoundException
	 */
	protected function _get_category($category)
	{
		$category_class = 'Shao\Blog\Model_Category';

		$category = $category_class::find(
			'first',
			array(
				'where' => array(
					array('cat_virtual_name', 'LIKE', strtolower($category)),
					array('cat_context', '=', $this->page_from->page_context),
				)
			)
		);
		if (empty($category)) {
			throw new \Nos\NotFoundException();
		}

		return $category;
	}

	/**
	 * Get an author
	 * 
	 * @param $id
	 * @return mixed
	 * @throws \Nos\NotFoundException
	 */
	protected function _get_author($id)
	{
		$author_class = static::$author_class;

		$author = $author_class::find(
			'first', array(
				'where' => array(
					array('user_id', '=', $id),
				),
			)
		);
		if (empty($author)) {
			throw new \Nos\NotFoundException();
		}

		return $author;
	}

	/**
	 * Get a tag
	 * 
	 * @param $tag
	 * @return mixed
	 * @throws \Nos\NotFoundException
	 */
	protected function _get_tag($tag) {
		$tag = Model_Tag::find(
			'first', array(
				'where' => array(
					array('tag_label', 'LIKE', strtolower($tag)),
				),
			)
		);
		if (empty($tag)) {
			throw new \Nos\NotFoundException();
		}
		return $tag;
	}

	/**
	 * Get posts
	 * 
	 * @param array $params
	 * @return mixed
	 */
	protected function _get_posts($params = array()) {
		// Apply context
		if (isset($this->page_from->page_context)) {
			$params['context'] = $this->page_from->page_context;
		} else {
			$params['context'] = \Nos\Tools_Context::defaultContext();
		}

		// Apply pagination
		if (isset($this->pagination)) {
			$query_count = Model_Post::get_query($params);
			$this->applyQueryCallback($query_count, $params);
			$this->pagination->set_config(
				array(
					'total_items' => $query_count->count(),
					'per_page' => $this->config['item_per_page'],
					'current_page' => $this->current_page,
				)
			);
		}
		$params['offset'] = $this->pagination ? (int) $this->pagination->offset : 0;

		if ($this->config['item_per_page']) {
			$params['limit'] = $this->config['item_per_page'];
		}

		if (isset($params['cat_id'])) {
			if (!is_array($params['cat_id'])) {
				$params['cat_id'] = array($params['cat_id']);
			}
			$pk = Model_Category::primary_key();

			$params['categories'] = Model_Category::find('all', array('where' => array(array($pk[0], 'IN', $params['cat_id']))));
			if (!empty($params['category']) && !in_array($params['category']->cat_id, $params['cat_id'])) {
				$params['categories'][] = $params['category'];
				unset($params['category']);
			}
		}
		if (isset($this->config['order_by'])) {
			$params['order_by'] = $this->config['order_by'];
		}

		// Get objects
		$query = Model_Post::get_query($params);
		$this->applyQueryCallback($query, $params);

		$posts = Model_Post::get_all_from_query($query);

		// Re-fetch with a 2nd request to get all the relations (not only the filtered ones)
		if (!empty($posts) && (!empty($params['tag']) || !empty($params['category']) || !empty($params['categories']))) {
			$posts = Model_Post::fetch_relations($posts, $params['order_by']);
		}

		return $posts;
	}

	/**
	 * Apply callback on query
	 *
	 * @param $query
	 * @param $params
	 */
	protected function applyQueryCallback($query, $params) {
		if (isset($this->config['query_callback'])) {
			$this->config['query_callback']($query, $params, $this);
		}
	}

	/**
	 * Get post for rss
	 *
	 * @param $post
	 * @return array
	 */
	protected static function _get_rss_post($post) {
		$content = $post->get_default_nuggets();
		$item = array();
		$item['title'] = isset($content[\Nos\DataCatcher::TYPE_TITLE]) ? $content[\Nos\DataCatcher::TYPE_TITLE] : $post->post_title;
		$item['link'] = $post->url_canonical();
		if (isset($content[\Nos\DataCatcher::TYPE_IMAGE])) {
			$item['img'] = \Uri::base(false).$content[\Nos\DataCatcher::TYPE_IMAGE];
		}
		$item['description'] = isset($content[\Nos\DataCatcher::TYPE_TEXT]) ? $content[\Nos\DataCatcher::TYPE_TEXT] : $post->post_summary;
		$item['pubDate'] = $post->post_created_at;
		$item['author'] = !empty($post->author) ? $post->author->fullname() : $post->post_author_alias;

		return $item;
	}

	/**
	 * Get comment for rss
	 *
	 * @param $comment
	 * @return array|null
	 */
	protected static function _get_rss_comment($comment)
	{
		$post_class = 'Shao\Blog\Model_Post';
		$post = $post_class::find($comment->comm_foreign_id);
		if (empty($post)) {
			return null;
		}
		$item = array();
		$item['title'] = strtr(__('Comment to the post ‘{{post}}’.'), array('{{post}}' => $post->post_title));
		$item['link'] = $post->url_canonical().'#comment'.$comment->comm_id;
		$item['description'] = $comment->comm_content;
		$item['pubDate'] = $comment->comm_created_at;
		$item['author'] = $comment->comm_author;

		return $item;
	}

	/**
	 * Get post's stat url
	 *
	 * @param $item
	 * @return string
	 */
	protected function url_stats($item) {
		return $this->main_controller->getEnhancedUrlPath().'stats/'.urlencode($item->post_id).'.html';
	}

	public static function getUrlEnhanced($params = array())
	{
		$item = \Arr::get($params, 'item');
		if (empty($item)) {
			return ;
		}

		$model = get_class($item);
		$page = isset($params['page']) ? $params['page'] : 1;

		switch ($model) {
			case 'Shao\Blog\Model_Post':
				return urlencode($item->post_virtual_name).'.html';
				break;

			case 'Shao\Blog\Model_Tag':
				return 'tag/'.urlencode($item->tag_label).($page > 1 ? '/'.$page : '').'.html';
				break;

			case 'Shao\Blog\Model_Category':
				return 'category/'.urlencode($item->cat_virtual_name).($page > 1 ? '/'.$page : '').'.html';
				break;

			case static::$author_class:
				return 'author/'.urlencode($item->user_name.'_'.$item->user_firstname.'_'.$item->user_id).($page > 1 ? '/'.$page : '').'.html';
				break;
		}

		return false;
	}

	/**
	 * Post a comment
	 *
	 * @param $post
	 * @return bool|string
	 */
	protected function _add_comment($post)
	{
		if (\Input::post('todo') == 'add_comment' && \Input::post('ismm') == '327') {
			if (!$this->app_config['comments']['use_recaptcha'] || \ReCaptcha\ReCaptcha::instance()->check_answer(
				\Input::real_ip(),
				\Input::post('recaptcha_challenge_field'),
				\Input::post('recaptcha_response_field')
			)
			) {
				$comm = new Model_Comment();
				$comm->comm_from_table = Model_Post::get_table_name();
				$comm->comm_email = \Input::post('comm_email');
				$comm->comm_author = \Input::post('comm_author');
				$comm->comm_content = \Input::post('comm_content');
				$comm->comm_created_at = \Date::forge()->format('mysql');
				$comm->comm_foreign_id = $post->post_id;
				$comm->comm_state = $this->config['comment_default_state'];
				$comm->comm_ip = \Input::ip();

				\Event::trigger_function('shao_blog|front->_add_comment', array(&$comm, &$post));

				$comm->save();

				\Cookie::set('comm_email', \Input::post('comm_email'));
				\Cookie::set('comm_author', \Input::post('comm_author'));

				return true;
			} else {
				return false;
			}
		}

		return 'none'; // @todo: see if we can't return null
	}

	/**
	 * Initialize pagination
	 *
	 * @param $page
	 */
	protected function init_pagination($page) {
		if ($this->config['item_per_page']) {
			$this->current_page = $page;
			$this->pagination = new \Nos\Pagination();
		}
	}

	/**
	 * html_entity_decode shorthand
	 *
	 * @param $text
	 * @return string
	 */
	protected static function _html_entity_decode($text) {
		return html_entity_decode($text, ENT_COMPAT, 'UTF-8');
	}
}
