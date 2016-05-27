<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 26 May 2016 at 14:37:00 CEST, Mijas Costa, Spain
 Copyright (c) 2016, Inikoo

 Version 3

*/

include_once 'utils/invalid_messages.php';


include_once 'conf/object_fields.php';
include_once 'class.SupplierPart.php';
include_once 'class.Part.php';




$options=array( 'new'=>true,'Category Scope'=>'');

if($state['module']=='products'){
    $options['Category Scope']='Product';
}

$object_fields=get_object_fields($state['_object'], $db, $user, $smarty, $options);



$smarty->assign('state', $state);
$smarty->assign('object', $state['_object']);


$smarty->assign('object_name', $state['_object']->get_object_name());


$smarty->assign('object_fields', $object_fields);




$html=$smarty->fetch('new_object.tpl');

?>
