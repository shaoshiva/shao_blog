<?php

namespace Shao\Blog;

class Controller_Admin_Post extends \Nos\Controller_Admin_Crud
{
    protected function init_item()
    {
        parent::init_item();

		$title = \Input::get('title', null);
        if (!empty($title)) {
            $this->item->post_title = $title;
        }

		$summary = \Input::get('summary', null);
        if (!empty($summary)) {
            $this->item->post_summary = $summary;
        }

		$thumbnail = \Input::get('thumbnail', null);
        if (!empty($thumbnail)) {
            $this->item->{'medias->thumbnail->medil_media_id'} = $thumbnail;
        }

		if (!$this->item->author) {
			$this->item->author = \Session::user();
		}

		if ($this->item_from) {
			$this->item->tags = $this->item_from->tags;

			foreach ($this->item_from->categories as $category_from) {
				$category = $category_from->find_context($this->item->post_context);
				if (!empty($category)) {
					$this->item->categories[$category->cat_id] = $category;
				}
			}
		}
    }

    protected function get_tab_params()
    {
        $tabInfos = parent::get_tab_params();

        if ($this->is_new) {
            $params = array();
            foreach (array('title', 'summary', 'thumbnail') as $key) {
                $value = \Input::get($key, false);
                if ($value !== false) {
                    $params[$key] = $value;
                }
            }
            if (count($params)) {
                $tabInfos['url'] = $tabInfos['url'].'&'.http_build_query($params);
            }
        }

        return $tabInfos;
    }

	protected function fields($fields)
	{
		$fields = parent::fields($fields);
		\Arr::set($fields, 'author->user_fullname.form.value', !empty($this->item->author) ? $this->item->author->fullname() : '');

		return $fields;
	}

	// Added this small hack in order to save the input date when adding an element.
	// @todo: should be removed when switching to novius-os 0.3
	public function save($item, $data)
	{
		if ($this->is_new) {
			$item->post_created_at = $data['post_created_at'];
			$item->save();
		}

		return parent::save($item, $data);
	}
}
