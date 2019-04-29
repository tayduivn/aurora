<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 11 January 2016 at 11:09:50 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/

include_once 'keyring/dns.php';

use \Gumlet\ImageResize;
use Spatie\ImageOptimizer\OptimizerChainFactory;

if (empty($_REQUEST['id']) or !is_numeric($_REQUEST['id']) or $_REQUEST['id'] <= 0) {
    header("HTTP/1.0 404 Not Found");
    echo "Image not found";
    exit();
} else {
    $image_key = $_REQUEST['id'];
}


if (!empty($_REQUEST['s'])) {
    $size_r = $_REQUEST['s'];

    if (!preg_match('/^\d+x\d+$/', $size_r)) {
        $size_r = '';
    }


} else {
    $size_r = '';
}

$redis = new Redis();
if ($redis->connect('127.0.0.1', 6379)) {
    $redis_on = true;
} else {
    $redis_on = false;
}

//$cache_file = 'image_cache/'.$image_key.'_'.$size.($size_r!=''?'_'.$size_r:'');

$image_code = 'i.'.DNS_ACCOUNT_CODE.'.'.$image_key.'_'.$size_r;


if ($redis->exists($image_code)  ) {


    list($image_filename, $mime_type) = json_decode($redis->get($image_code), true);


    if (file_exists($image_filename)) {

        header("Content-type: $mime_type");
        $seconds_to_cache = 43200000;
        $ts               = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache)." GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");
        header('Content-Length: ' . filesize($image_filename));


        readfile($image_filename);




        exit();
    }

}



include_once 'common.php';


$sql  = sprintf('select `Image Path`,`Image MIME Type` from `Image Dimension` where `Image Key`=? ');
$stmt = $db->prepare($sql);

$stmt->execute([$image_key]);

if ($row = $stmt->fetch()) {
    $image_path = $row['Image Path'];
    $image_mime = $row['Image MIME Type'];

    $cached_image_path = preg_replace('/^img\/db/', 'img/cache', $image_path);
    $cached_image_path = preg_replace('/\./', '_'.$size_r.'.', $cached_image_path);

    if($size_r!=''){
        list($w, $h) = preg_split('/x/', $size_r);
        $image              = new ImageResize($image_path);
        $image->quality_jpg = 100;
        $image->quality_png = 9;

        $image->resizeToBestFit($w, $h);
        $image->save($cached_image_path);

        if (file_exists($cached_image_path)) {
            usleep(  1000 );
        }
        if (file_exists($cached_image_path)) {
            usleep(  2000 );
        }
        if (file_exists($cached_image_path)) {
            usleep(  3000 );
        }
        if (file_exists($cached_image_path)) {
            usleep(  100000 );
        }
    }else{
        copy($image_path,$cached_image_path);
    }




    $optimizerChain = OptimizerChainFactory::create();
    $optimizerChain->optimize($cached_image_path);

    if (file_exists($cached_image_path)) {
        usleep(  1000 );
    }
    if (file_exists($cached_image_path)) {
        usleep(  2000 );
    }
    if (file_exists($cached_image_path)) {
        usleep(  3000 );
    }
    if (file_exists($cached_image_path)) {
        usleep(  100000 );
    }


    $redis->set(
        $image_code, json_encode(
        array(
            $cached_image_path,
            $image_mime
        )
    )
    );


    if (file_exists($cached_image_path)) {

        header("Content-type: ".$image_mime);
        $seconds_to_cache = 43200000;
        $ts               = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache)." GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");
        header('Content-Length: '.filesize($cached_image_path));
        header('X-caca: caca');

        readfile($cached_image_path);


    }else{
        header("HTTP/1.0 404 Not Found");
        echo "Image not found";
        exit();
    }

    //print $cached_image_path;
   // exit;


} else {
    header("HTTP/1.0 404 Not Found");
    echo "Image not found";
    exit();
}




