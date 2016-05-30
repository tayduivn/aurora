<?php
/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 30 May 2016 at 18:48:34 CEST, Mijas Costa, Spain

 Copyright (c) 2016, Inikoo

 Version 3.0
*/


function parse_request($_data, $db, $website,$account, $user) {

    include_once('class.WebsiteNode.php');
    include_once('class.Webpage.php');


	$request=$_data['request'];

	$request=preg_replace('/\/+/', '/', $request);

	$original_request=preg_replace('/^\//', '', $request);
	$view_path=preg_split('/\//', $original_request);



	$count_view_path=count($view_path);
	$shorcut=false;
	$is_main_section=false;


    if($count_view_path==1){
        
          $code=array_shift($view_path);
        
    
         $node=$website->get_node($code);
         if($node->id){
            if($webpage_key=$node->get_webpage_key()){
                $webpage = new Webpage($webpage_key);
                if($webpage->id){
                    return array($webpage,$request);
                }
            }else{
                print $node->get_webpage_key();
            
                exit('shit B');
            }
         
         }else{
            exit('shit A');
         }
         
    
    }


}



?>
