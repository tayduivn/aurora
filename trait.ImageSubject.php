<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 17 February 2016 at 20:36:41 GMT+8, Kuala Lumpur, Malaysia

 Copyright (c) 2016, Inikoo

 Version 3.0

*/

include_once 'utils/natural_language.php';

trait ImageSubject {

    /**
     * @param        $raw_data
     * @param string $metadata
     *
     * @return bool|\Image
     */

    /**
     * @var \PDO
     */
    public $db;

    function add_image($raw_data, $metadata = '') {


        include_once 'utils/units_functions.php';
        include_once 'class.Image.php';


        $data = array(
            'Image Width'         => 0,
            'Image Height'        => 0,
            'Image File Size'     => 0,
            'Image File Checksum' => '',
            'Image Filename'      => $raw_data['Image Filename'],


            'upload_data' => $raw_data['Upload Data'],
            'editor'      => $this->editor
        );

        if ($this->fork) {
            $data['fork'] = true;
        }

        if (!empty($raw_data['Image Subject Object Image Scope'])) {
            $object_image_scope = $raw_data['Image Subject Object Image Scope'];
        } else {
            $object_image_scope = 'Default';
        }

        $image = new Image('find', $data, 'create');
        if ($image->id) {

            $this->link_image($image->id, $object_image_scope, $metadata);


            if ($this->table_name == 'Part') {


                if ($object_image_scope == 'Marketing') {


                    foreach ($this->get_products('objects') as $product) {

                        if (count($product->get_parts()) == 1) {
                            $product->editor = $this->editor;
                            $product->link_image($image->id, 'Marketing');
                        }

                    }
                }

            } elseif ($this->table_name == 'Category') {


                $account = new Account();
                if ($this->get('Category Scope') == 'Part' and $this->get('Category Root Key') == $account->get('Account Part Family Category Key') and $object_image_scope == 'Marketing') {

                    $sql = "ELECT `Category Key` FROM `Category Dimension` WHERE `Category Scope`='Product' AND `Category Code`=%s ";


                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(
                        array(
                            $this->get('Code')
                        )
                    );
                    while ($row = $stmt->fetch()) {
                        $category         = get_object('Category', $row['Category Key']);
                        $category->editor = $this->editor;
                        $category->link_image($image->id, 'Marketing');
                    }
                }

            }

            return $image;
        } else {
            $this->error = true;
            $this->msg   = "Can't create/found image (b), ".$image->msg;

            return false;
        }

    }


    function link_image($image_key, $object_image_scope = 'Default', $metadata = '') {


        $image = get_object('Image', $image_key);

        if ($image->id) {
            $subject_key = $this->id;
            $subject     = $this->table_name;

            $image->fork = $this->fork;


            if ($this->table_name == 'Page') {
                $subject = 'Webpage';
            }


            // todo, very dangerous hack to remove Default image bridges,Remove this when all Defaults are gone
            if ($object_image_scope != 'Default') {
                $sql  = "DELETE FROM `Image Subject Bridge`  WHERE `Image Subject Object`=? AND `Image Subject Object Key`=?  AND `Image Subject Image Key`=?  AND `Image Subject Object Image Scope`='Default'";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(
                    array(
                        $subject,
                        $subject_key,
                        $image->id,
                    )
                );
            }

            $sql = "SELECT `Image Subject Image Key`,`Image Subject Is Principal` FROM `Image Subject Bridge` WHERE `Image Subject Object`=? AND `Image Subject Object Key`=?  AND `Image Subject Image Key`=?  AND `Image Subject Object Image Scope`=?";


            $stmt = $this->db->prepare($sql);
            $stmt->execute(
                array(
                    $subject,
                    $subject_key,
                    $image->id,
                    $object_image_scope
                )
            );
            if ($row = $stmt->fetch()) {


                $this->nochange = true;
                $this->msg      = _('Image already uploaded');

                return;
            }


            $number_images = $this->get_number_images();
            if ($number_images == 0) {
                $principal = 'Yes';
            } else {
                $principal = 'No';
            }


            $is_public = 'No';


            switch ($subject) {
                case 'Part':
                case 'Category':
                case 'Product':
                    if ($object_image_scope == 'Marketing') {
                        $is_public = 'Yes';
                    }

                    break;
                case 'Website':
                case 'Webpage':
                    $is_public = 'Yes';
                    break;
            }


            $sql =
                "INSERT INTO `Image Subject Bridge` (`Image Subject Object Image Scope`,`Image Subject Object`,`Image Subject Object Key`,`Image Subject Image Key`,`Image Subject Image File Format`,`Image Subject Is Principal`,`Image Subject Image Caption`,`Image Subject Date`,`Image Subject Order`,`Image Subject Is Public`, `Image Subject Metadata`) VALUES (?,?,?,?,?,?,'',?,?,?,?)";


            $this->db->prepare($sql)->execute(
                array(
                    $object_image_scope,
                    $subject,
                    $subject_key,
                    $image->id,
                    $image->get('Image File Format'),
                    $principal,
                    gmdate('Y-m-d H:i:s'),
                    ($number_images + 1),
                    $is_public,
                    ($metadata == '' ? '{}' : json_encode($metadata))
                )
            );


            $image_subject_key = $this->db->lastInsertId();


            $image->update_public_db();


            $this->update_images_data();


            //print $sql;

            $this->reindex_order();

            $sql = sprintf(
                "SELECT `Image Subject Key`,`Image Subject Is Principal`,`Image Key`,`Image Subject Image Caption`,`Image Filename`,`Image File Size`,`Image File Checksum`,`Image Width`,`Image Height`,`Image File Format` FROM `Image Subject Bridge` B LEFT JOIN `Image Dimension` ID ON (`Image Key`=`Image Subject Image Key`) WHERE `Image Subject Object`=%s AND `Image Subject Object Key`=%d AND  `Image Key`=%d",
                prepare_mysql($subject), $subject_key, $image->id
            );


            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {

                    if ($row['Image Height'] != 0) {
                        $ratio = $row['Image Width'] / $row['Image Height'];
                    } else {
                        $ratio = 1;
                    }
                    include_once 'utils/units_functions.php';

                    $this->new_value = array(
                        'name'              => $row['Image Filename'],
                        'small_url'         => 'image.php?id='.$row['Image Key'].'&size=small',
                        'thumbnail_url'     => 'image.php?id='.$row['Image Key'].'&size=thumbnail',
                        'filename'          => $row['Image Filename'],
                        'ratio'             => $ratio,
                        'caption'           => $row['Image Subject Image Caption'],
                        'is_principal'      => $row['Image Subject Is Principal'],
                        'id'                => $row['Image Key'],
                        'size'              => file_size($row['Image File Size']),
                        'width'             => $row['Image Width'],
                        'height'            => $row['Image Height'],
                        'image_subject_key' => $image_subject_key

                    );


                }
            }


            $this->updated = true;
            $this->msg     = _("Image added");

            if ($this->table_name == 'Product') {
                $this->update_updated_markers('Images');

            }

            return $image;
        } else {


            $this->error = true;
            $this->msg   = "Can't create/found image (a), ".$image->msg;

            return false;
        }

    }

    function get_number_images() {

        $subject = $this->table_name;

        $number_of_images = 0;
        $sql              = sprintf(
            "SELECT count(*) AS num FROM `Image Subject Bridge` WHERE `Image Subject Object`=%s AND `Image Subject Object Key`=%d ", prepare_mysql($subject), $this->id
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $number_of_images = $row['num'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql";
            exit;
        }


        return $number_of_images;
    }


    function update_images_data() {


        $subject = $this->table_name;

        $sql = "SELECT count(*) AS num FROM `Image Subject Bridge` WHERE `Image Subject Object`=? AND `Image Subject Object Key`=? ";


        $stmt = $this->db->prepare($sql);
        if ($stmt->execute(
            array(
                $subject,
                $this->id
            )
        )) {
            if ($row = $stmt->fetch()) {
                $number_images = $row['num'];
            } else {
                $number_images = 0;
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit();
        }


        if ($this->get_object_name() == 'Category') {
            $this->fast_update(
                array($this->subject_table_name.' Number Images' => $number_images), $this->subject_table_name.' Dimension'
            );
        } else {
            $this->fast_update(
                array($this->get_object_name().' Number Images' => $number_images)
            );
        }


    }


    function reindex_order() {

        $order_index = array();

        $subject = $this->table_name;
        $sql     = sprintf(
            "SELECT `Image Subject Key` FROM `Image Subject Bridge` WHERE `Image Subject Object`=%s AND   `Image Subject Object Key`=%d ORDER BY `Image Subject Order`,`Image Subject Date`,`Image Subject Key`", prepare_mysql($subject), $this->id
        );
        //print $sql;
        $order = 1;
        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {

                $sql           = sprintf(
                    "UPDATE `Image Subject Bridge` SET `Image Subject Order`=%d WHERE `Image Subject Key`=%d ", $order, $row['Image Subject Key']
                );
                $order_index[] = $row['Image Subject Key'];
                $this->db->exec($sql);
                $order++;
            }
        }

        $this->update_main_image();


        return $order_index;
    }

    function update_main_image() {


        $image_key = $this->get_main_image_key();


        if ($image_key) {


            $main_image_src = 'image.php?id='.$image_key.'&s=320x280';
            $main_image_key = $image_key;

        } else {
            $main_image_src = '/art/nopic.png';
            $main_image_key = 0;
        }

        $this->fast_update(
            array(
                $this->table_name.' Main Image'     => $main_image_src,
                $this->table_name.' Main Image Key' => $main_image_key

            )
        );


        if ($this->table_name == 'Category') {
            $this->update_webpages('main_image');
        } elseif ($this->table_name == 'Part') {
            $this->activate();
        } elseif ($this->table_name == 'Product') {
            $this->update_webpages('main_image');
        }

        $this->updated = true;

    }

    function get_main_image_key() {

        $image_key = false;

        $subject = $this->table_name;

        $sql = sprintf(
            "SELECT `Image Subject Image Key` FROM `Image Subject Bridge` WHERE `Image Subject Object`=%s AND `Image Subject Object Key`=%d ORDER BY `Image Subject Order` LIMIT 1", prepare_mysql($subject), $this->id

        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $image_key = $row['Image Subject Image Key'];
            }
        }


        return $image_key;

    }

    function get_images_slideshow() {

        include_once 'utils/natural_language.php';


        $image_subject_type = $this->table_name;

        $images_slideshow = array();

        $sql =
            "SELECT `Image Subject Key`,`Image Subject Is Principal`,`Image Key`,`Image Subject Image Caption`,`Image Filename`,`Image File Size`,`Image File Checksum`,`Image Width`,`Image Height`,`Image File Format` FROM `Image Subject Bridge` B LEFT JOIN `Image Dimension` I ON (`Image Subject Image Key`=`Image Key`) WHERE `Image Subject Object`=? AND   `Image Subject Object Key`=? ORDER BY `Image Subject Order`,`Image Subject Is Principal`,`Image Subject Date`,`Image Subject Key`";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(
            array(
                $image_subject_type,
                $this->id
            )
        );
        while ($row = $stmt->fetch()) {
            if ($row['Image Height'] != 0) {
                $ratio           = sprintf('%.5f', $row['Image Width'] / $row['Image Height']);
                $formatted_ratio = sprintf('%.2f', $row['Image Width'] / $row['Image Height']);
            } else {
                $ratio           = 1;
                $formatted_ratio = '-';
            }
            $images_slideshow[] = array(
                'name'              => $row['Image Filename'],
                'small_url'         => 'image.php?id='.$row['Image Key'].'&s=320x280',
                'thumbnail_url'     => 'image.php?id='.$row['Image Key'].'&s=25x20',
                'normal_url'        => 'image.php?id='.$row['Image Key'],
                'filename'          => $row['Image Filename'],
                'ratio'             => $ratio,
                'formatted_ratio'   => $formatted_ratio,
                'caption'           => $row['Image Subject Image Caption'],
                'is_principal'      => $row['Image Subject Is Principal'],
                'id'                => $row['Image Key'],
                'size'              => file_size($row['Image File Size']),
                'width'             => $row['Image Width'],
                'height'            => $row['Image Height'],
                'image_subject_key' => $row['Image Subject Key']

            );
        }


        return $images_slideshow;
    }

    function delete_image($image_bridge_key) {

        $sql = sprintf(
            'SELECT `Image Subject Key`,`Image Subject Image Key` FROM `Image Subject Bridge` WHERE `Image Subject Key`=%d ', $image_bridge_key
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {


                $sql = sprintf('DELETE FROM `Image Subject Bridge` WHERE `Image Subject Key`=%d ', $image_bridge_key);
                $this->db->exec($sql);

                $this->update_images_data();


                $image         = get_object('Image', $row['Image Subject Image Key']);
                $image->editor = $this->editor;

                $image->delete();


                $this->reindex_order();

                if ($this->table_name == 'Product') {
                    $this->update_updated_markers('Images');
                }

            } else {
                $this->error;
                $this->msg = _('Image not found');
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql";
            exit;
        }


    }

    function set_as_principal($key, $look_up = 'image_bridge_key') {


        if ($look_up == 'image_key') {
            $sql = sprintf(
                'UPDATE  `Image Subject Bridge` SET `Image Subject Order`=0  WHERE   `Image Subject Object`=%s AND `Image Subject Object Key`=%d AND  `Image Subject Image Key`=%d ', prepare_mysql($this->table_name), $this->id, $key
            );
        } else {
            $sql = sprintf(
                'UPDATE  `Image Subject Bridge` SET `Image Subject Order`=0  WHERE `Image Subject Key`=%d ', $key
            );
        }

        $this->db->exec($sql);

        $this->reindex_order();

    }


}


