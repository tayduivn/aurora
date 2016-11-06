<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 18 October 2015 at 10:56:20 BST, Sheffield UK
 Copyright (c) 2015, Inikoo

 Version 3

*/


include_once 'utils/invalid_messages.php';
include_once 'conf/object_fields.php';


$node = $state['_object'];


$object_fields = get_object_fields(
    $node, $db, $user, $smarty, array('show_full_label' => false)
);


$smarty->assign('object', $state['_object']);
$smarty->assign('key', $state['key']);

$smarty->assign('object_fields', $object_fields);
$smarty->assign('state', $state);


$html = $smarty->fetch('edit_object.tpl');


?>
