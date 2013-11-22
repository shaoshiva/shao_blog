<?php

\Nos\I18n::current_dictionary(array('shao_blog::common'));

return array(
    'model' => '\Nos\User\Model_User',
    'query' => array(
        'order_by' => \DB::expr('CONCAT(COALESCE(user_firstname, ""), user_name)'),
    ),
    'input' => array(
        'key'   => 'post_author_id',
    ),
    'appdesk' => array(
        'label' => __('Authors'),
    ),
    'data_mapping' => array(
        'fullname'=> array(
            'title' => __('Authors')
        )
    )
);
