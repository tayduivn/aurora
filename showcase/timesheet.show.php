<?php
/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 22 November 2015 at 23:17:17 GMT Sheffiled UK

 Copyright (c) 2015, Inikoo

 Version 3.0
*/

function get_timesheet_showcase($data) {

    global $smarty;
     
    $smarty->assign('timesheet',$data['_object']);

    return $smarty->fetch('showcase/timesheet.tpl');
    


}


?>