<?php

\Nos\I18n::current_dictionary(array('shao_blog::common'));

return array(
    'data_mapping' => array(
        'tag_label' => array(
            'title' => __('Tags'),
        ),
    ),
    'title_property' => 'tag_label',
    'i18n' => array(
        // Crud
        'notification item deleted' => __('The tag has been deleted.'),

        // General errors
        'notification item does not exist anymore' => __('This tag doesn’t exist any more. It has been deleted.'),
        'notification item not found' => __('We cannot find this tag.'),

        // Deletion popup
        'deleting item title' => __('Deleting the tag ‘{{title}}’'),

        # Delete action's labels
        'deleting button 1 item' => __('Yes, delete this tag'),
    ),
    'actions' => array(
        '{{namespace}}\Model_Tag.edit' => false,
        '{{namespace}}\Model_Tag.visualise' => false,
    ),
);