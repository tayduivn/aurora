<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 6 October 2015 at 22:11:57 BST, Birmingham->Malaga (Plane)
 Copyright (c) 2015, Inikoo

 Version 3

*/

include_once('class.WebsiteNode.php');

$tab='website.root_nodes';
$ar_file='ar_websites_tables.php';
$tipo='root_nodes';

$default=$user->get_tab_defaults($tab);


$table_views=array();

$table_filters=array(
	'code'=>array('label'=>_('Code')),
	'title'=>array('label'=>_('Name')),

);

$parameters=array(
		'parent'=>$state['object'],
		'parent_key'=>$state['key'],
);

$parent_node=new WebsiteNode('website_code',$state['key'],'p.Home');

$smarty->assign('parent_node_key',$parent_node->id);

include('utils/get_table_html.php');


?>
