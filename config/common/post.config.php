<?php

\Nos\I18n::current_dictionary(array('shao_blog::common'));

return array(
    'data_mapping' => array(
        'post_title' => array(
            'title'    => __('Title'),
        ),
        'context' => true,
        'author->user_name' => array(
            'title'         => __('Author'),
            'value' =>  function($item) {
                return !empty($item->author) ? $item->author->fullname() : $item->post_author_alias;
            },
        ),
        'publication_status' => array(
            'title' => __('Status'),
            'column' => 'published',
            'multiContextHide' => true,
        ),
        'post_created_at' => array(
            'title'    => __('Date'),
            'value' =>
                function ($item)
                {
                    if ($item->is_new()) {
                        return null;
                    }
                    return \Date::create_from_string($item->post_created_at, 'mysql')->format('%m/%d/%Y %H:%M:%S'); //%m/%d/%Y %H:%i:%s
                },
            'dataType' => 'datetime',
        ),
        'thumbnail' => array(
            'value' => function ($item) {
                foreach ($item->medias as $media) {
                    return $media->get_public_path_resized(64, 64);
                }
                return false;
            },
        ),
        'thumbnailAlternate' => array(
            'value' => function ($item) {
                return 'static/novius-os/admin/vendor/jquery/jquery-ui-input-file-thumb/css/images/apn.png';
            }
        ),
    ),
    'i18n' => array(
    ),
    'actions' => array(
        'order' => array(
            'Shao\Blog\Model_Post.edit',
            'Shao\Blog\Model_Post.visualise',
            'Shao\Blog\Model_Post.delete',
        ),
    ),
    'thumbnails' => true,
);
