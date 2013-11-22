<?php

\Nos\I18n::current_dictionary(array('shao_blog::common'));

return array(
    'model' => 'Shao\Blog\Model_Tag',
    'query' => array(
        'order_by' => 'tag_label',
    ),
    'appdesk' => array(
        'label' => __('Tags'),
    ),
    'input' => array(
        'key'   => 'tags.tag_id',
    ),
);
