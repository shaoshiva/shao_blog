<?php

echo $pagination->create_links(
    function($page) use ($type, $item) {

        if ($type == 'main') {
            $main_controller = \Nos\Nos::main_controller();
            $url = parse_url($main_controller->getContextUrl(), PHP_URL_PATH).$main_controller->getPageUrl();
            return $page == 1 ? $url : str_replace('.html', '/', $url).'page/'.$page.'.html';
        } else {
            return $item->url(array('page' => $page));
        }
    }
);
