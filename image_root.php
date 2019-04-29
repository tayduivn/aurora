<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 11 January 2016 at 11:09:50 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/

include_once 'common.php';

if (empty($_REQUEST['id']) or  !is_numeric($_REQUEST['id'])   or  $_REQUEST['id']<=0 ) {
    header("HTTP/1.0 404 Not Found");
    echo "Image not found";
    exit();
} else {
    $image_key = $_REQUEST['id'];
}


if (isset($_REQUEST['size']) and preg_match('/^large|small|thumbnail|tiny$/', $_REQUEST['size'])) {
    $size = $_REQUEST['size'];
} else {
    $size = 'original';
}

if (isset($_REQUEST['r'])) {
    $size_r = $_REQUEST['r'];
} else {
    $size_r = '';
}

$redis = new Redis();
if ($redis->connect('127.0.0.1', 6379)) {
    $redis_on = true;
} else {
    $redis_on = false;
}

$cache_file = 'image_cache/'.$image_key.'_'.$size.($size_r!=''?'_'.$size_r:'');

$image_code = 'auAWi'.$image_key.'_'.$size.($size_r!=''?'_'.$size_r:'');



print $image_code;

exit;




if ($redis->exists($image_code) and false) {


    $image_filename = $redis->get($image_code);

    if (file_exists($image_filename)) {

        $imginfo = getimagesize($image_filename);
        header("Content-type: {$imginfo['mime']}");
        $seconds_to_cache = 3600 * 24 * 500;
        $ts               = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache)." GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");
        readfile($image_filename);



        exit();
    }
}

print_r($size);

print_r($size_r);



if ($size_r != '') {










    include_once 'class.Image.php';
    $image = new Image($image_key);


    list($w, $h) = preg_split('/x/', $size_r);

    $new_image = $image->fit_to_canvas($w, $h);
    header('Content-type: image/'.$image->get('Image File Format'));

    header('Content-Disposition: inline; filename='.$image->get('Image Filename'));
    $seconds_to_cache = 3600 * 24 * 500;
    $ts               = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache)." GMT";
    header("Expires: $ts");
    header("Pragma: cache");
    header("Cache-Control: max-age=$seconds_to_cache");


    ImagePNG($new_image);

    ImagePNG($new_image,$cache_file.'.'.$image->get('Image File Format'));
    $redis->set($image_code,$cache_file.'.'.$image->get('Image File Format'));
    imagedestroy($new_image);
    exit;
}


$sql = sprintf(
    "SELECT `Image Data`,`Image Thumbnail Data`,`Image Small Data`,`Image Large Data`,`Image File Format`,`Image Filename` FROM `Image Dimension` WHERE `Image Key`=%d", $image_key
);


if ($result = $db->query($sql)) {

    if ($row = $result->fetch()) {
        header('Content-type: image/'.$row['Image File Format']);
        header('Content-Disposition: inline; filename='.$row['Image Filename']);
        $seconds_to_cache = 3600 * 24 * 500;
        $ts               = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache)." GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");
        if ($size == 'original') {
            $_image= $row['Image Data'];
        } elseif ($size == 'large') {
            if (!$row['Image Large Data']) {
                $_image= $row['Image Data'];
            } else {
                $_image= $row['Image Large Data'];
            }
        } elseif ($size == 'small') {
            if (!$row['Image Small Data']) {
                $_image= $row['Image Data'];
            } else {
                $_image= $row['Image Small Data'];
            }
        } elseif ($size == 'thumbnail' or $size == 'tiny') {
            if ($row['Image Thumbnail Data']) {
                $_image= $row['Image Thumbnail Data'];
            } elseif ($row['Image Small Data']) {
                $_image= $row['Image Small Data'];
            } else {
                $_image= $row['Image Data'];
            }
        } else {
            $_image= $row['Image Data'];
        }

        file_put_contents($cache_file.'.'.$row['Image File Format'],$_image);
        $redis->set($image_code,$cache_file.'.'.$row['Image File Format']);
        echo $_image;


    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Image not found";

        exit;
    }


} else {
    print_r($error_info = $db->errorInfo());
    exit;

}

?>
