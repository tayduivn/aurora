<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 13 September 2017 at 15:34:11 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/


require_once 'common.php';

if (!isset($_REQUEST['id'])) {
    $attachment_key = -1;
} else {
    $attachment_key = $_REQUEST['id'];
}


$sql = sprintf(
    "SELECT `Attachment Public`,`Subject`,`Subject Key`,`Attachment MIME Type`,`Attachment File Original Name`,`Attachment Data` FROM `Attachment Bridge` B LEFT JOIN  `Attachment Dimension` A ON (A.`Attachment Key`= B.`Attachment Key`) 
    WHERE `Attachment Bridge Key`=%d  and `Attachment Public`='Yes' and `Subject` in ('Part','Product') ",
    $attachment_key
);


if ($result = $db->query($sql)) {

    if ($row = $result->fetch()) {


     
            header('Content-Type: '.$row['Attachment MIME Type']);

            header(
                'Content-Disposition: inline; filename='.$row['Attachment File Original Name']
            );
            echo $row['Attachment Data'];
      
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Attachment not found";

        exit;
    }


} else {
    print_r($error_info = $db->errorInfo());
    exit;

}


?>