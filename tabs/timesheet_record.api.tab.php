<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 19 November 2015 at 13:14:53 GMT, Sheffield UK
 Copyright (c) 2015, Inikoo

 Version 3

*/



$smarty->assign('state', $state);
$smarty->assign('object_fields', $object_fields);

$html=$smarty->fetch('api.tpl');

?>
