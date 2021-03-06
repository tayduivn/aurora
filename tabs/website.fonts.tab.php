<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 16 February 2018 at 17:32:37 GMT+8, Cyberjaya , Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/


include_once 'utils/invalid_messages.php';
include_once 'conf/object_fields.php';


$website = $state['_object'];


$object_fields = get_object_fields(
    $website, $db, $user, $smarty, array('fonts' => true)
);


$smarty->assign('object', $state['_object']);
$smarty->assign('key', $state['key']);

$smarty->assign('object_fields', $object_fields);
$smarty->assign('state', $state);


$html = $smarty->fetch('edit_object.tpl');

?>
