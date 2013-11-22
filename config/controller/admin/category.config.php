<?php

\Nos\I18n::current_dictionary('shao_blog::common');

return array (
    'controller_url'  => 'admin/shao_blog/category',
    'model' => '\Shao\Blog\Model_Category',
    'i18n_file' => 'shao_blog::category',
    'tab' => array(
        'labels' => array(
            'insert' => __('Add a category'),
            'blankSlate' => __('Translate a category'),
        ),
    ),
    'layout' => array(
        'title' => 'cat_title',
        'large' => true,
        'content' => array(
            'expander' => array(
                'view' => 'nos::form/expander',
                'params' => array(
                    'title'   => __('Properties'),
                    'nomargin' => true,
                    'options' => array(
                        'allowExpand' => false,
                    ),
                    'content' => array(
                        'view' => 'nos::form/fields',
                        'params' => array(
                            'begin' => '<table class="fieldset">',
                            'fields' => array(
                                'cat_virtual_name',
                                'cat_parent_id'
                            ),
                            'end' => '</table>',
                        ),
                    ),
                ),
            ),
        ),
        'save' => 'save',
    ),
    'fields' => array(
        'cat_title' => array (
            'label' => __('Title'),
            'form' => array(
                'type' => 'text',
            ),
            'validation' => array(
                'required',
                'min_length' => array(2),
            ),
        ),
        'cat_virtual_name' => array(
            'label' => __('URL:'),
            'renderer' => 'Nos\Renderer_Virtualname',
            'validation' => array(
                'required',
                'min_length' => array(2),
            ),
            'template' => "\t\t<tr><th class=\"{error_class}\">{label}{required}</th><td class=\"{error_class}\">{field} {use_title_checkbox} {error_msg}</td></tr>\n"
        ),
        'cat_parent_id' => array(
            'label' => __('Location:'),
            'renderer' => 'Nos\BlogNews\Renderer_Selector',
            'renderer_options' => array(
                'width'                 => '100%',
                'height'                => '350px',
                'inspector'             => 'admin/shao_blog/inspector/category',
                'model'                 => '\Shao\Blog\Model_Category',
                'sortable'              => true,
                'main_column'           => 'cat_title',
            ),
        ),
        'save' => array(
            'label' => '',
            'form' => array(
                'type' => 'submit',
                'tag' => 'button',
                // Note to translator: This is a submit button
                'value' => __('Save'),
                'class' => 'primary',
                'data-icon' => 'check',
            ),
        ),
    ),
);
