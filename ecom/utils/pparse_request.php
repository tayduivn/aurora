<?php
/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 30 May 2016 at 18:48:34 CEST, Mijas Costa, Spain

 Copyright (c) 2016, Inikoo

 Version 3.0
*/


function parse_request($_data, $db, $website, $account, $user) {

    include_once 'class.WebsiteNode.php';
    include_once 'class.Webpage.php';


    $request = $_data['request'];
    $request = preg_replace('/\/+/', '/', $request);

    $request = preg_replace('/\?.*/', '', $request);


    $_request = $request;
    if (_PREVIEW) {

        $request = preg_replace('/\/ecom\//', '/', $request);
    }


    $original_request = preg_replace('/^\//', '', $request);
    $view_path        = preg_split('/\//', $original_request);

    $view_path = array_map('strtolower', $view_path);

    $count_view_path = count($view_path);
    $shorcut         = false;
    $is_main_section = false;


    if ($count_view_path == 1) {

        $code = array_shift($view_path);

        $webpage = $website->get_webpage($code);


        return array(
            $webpage,
            $_request
        );


    } elseif ($count_view_path == 2) {

        $webpage = $website->get_webpage(join($view_path, '.'));


        return array(
            $webpage,
            $_request
        );


    }


}


function get_webpage_to_delete($node, $request) {
    if ($node->id) {
        if ($webpage_key = $node->get_webpage_key()) {
            $webpage = new Webpage($webpage_key);
            if ($webpage->id) {
                return array(
                    $node,
                    $webpage,
                    $request
                );
            }
        } else {
            print $node->get_webpage_key();

            exit('shit B');
        }

    } else {
        exit('shit A');
    }
}


?>
