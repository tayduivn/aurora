<?php

/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 28 November 2016 at 17:49:06 GMT+8, Plane (Hangzhou - Kuala Lumpur)
 Copyright (c) 2016, Inikoo

 Version 3

*/


class Public_Category {

    function __construct($arg1 = false, $arg2 = false, $arg3 = false) {

        global $db;
        $this->db = $db;
        $this->id = false;

        $this->webpage = false;


        $this->table_name = 'Category';

        if (is_numeric($arg1)) {
            $this->get_data('id', $arg1);

            return;
        }
        $this->get_data($arg1, $arg2, $arg3);


    }


    function get_data($tipo, $tag, $tag2 = false) {


        switch ($tipo) {
            case 'root_key_code':
                $sql = sprintf(
                    "SELECT * FROM `Category Dimension` WHERE `Category Root Key`=%d AND `Category Code`=%s ", $tag, prepare_mysql($tag2)
                );
                break;
            case 'subject_code':
                $sql = sprintf(
                    "SELECT * FROM `Category Dimension` WHERE `Category Subject`=%s AND `Category Code`=%s ", prepare_mysql($tag), prepare_mysql($tag2)
                );
                break;
            default:
                $sql = sprintf(
                    "SELECT * FROM `Category Dimension` WHERE `Category Key`=%d", $tag
                );

                break;
        }

        if ($this->data = $this->db->query($sql)->fetch()) {
            $this->id = $this->data['Category Key'];


            $sql = sprintf("SELECT * FROM `Product Category Dimension` WHERE `Product Category Key`=%d", $this->id);

            if ($result2 = $this->db->query($sql)) {
                if ($row = $result2->fetch()) {
                    $this->data = array_merge($this->data, $row);
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }


        }


    }

    function load_webpage() {

        include_once 'class.Public_Webpage.php';


        $this->webpage = new Public_Webpage('scope', ($this->get('Category Subject') == 'Category' ? 'Category Categories' : 'Category Products'), $this->id);
    }


    function get($key) {

        switch ($key) {

            case 'Product Category Status':
            case 'Category Subject':
                return $this->data[$key];
                break;
            case 'Subject':
            case 'Code':
            case 'Scope':
            case 'Label':
                return $this->data['Category '.$key];
                break;
            case 'Description':
                return $this->data['Product Category '.$key];
                break;
            case 'Image':


                $image_key = $this->data['Category Main Image Key'];

                if ($image_key) {
                    $img = '/image_root.php?size=small&id='.$image_key;
                } else {
                    $img = '/art/nopic.png';

                }

                return $img;


        }

    }


    function get_object_name() {
        return $this->table_name;

    }


    function get_parent_categories($scope = 'keys') {


        $type              = 'Category';
        $parent_categories = array();

        $sql = sprintf(
            "SELECT `Webpage Code`,B.`Category Key`,`Category Root Key`,`Other Note`,`Category Label`,`Category Code`,`Is Category Field Other` 
        FROM `Category Bridge` B 
        LEFT JOIN `Category Dimension` C ON (C.`Category Key`=B.`Category Key`) 
        LEFT JOIN `Page Store Dimension` W ON (W.`Webpage Scope Key`=B.`Category Key` AND `Webpage Scope`=%s) 

          WHERE  `Category Branch Type`='Head'  AND B.`Subject Key`=%d AND B.`Subject`=%s",

            prepare_mysql('Category Categories'),

            $this->id, prepare_mysql($type)
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {

                if ($scope == 'keys') {
                    $parent_categories[$row['Category Key']] = $row['Category Key'];
                } elseif ($scope == 'objects') {
                    $parent_categories[$row['Category Key']] = get_object('Category', $row['Category Key']);
                } elseif ($scope == 'data') {


                    $value = $row['Category Label'];

                    $parent_categories[] = array(

                        'label'        => $row['Category Label'],
                        'code'         => $row['Category Code'],
                        'value'        => $value,
                        'category_key' => $row['Category Key'],
                        'webpage_code' => strtolower($row['Webpage Code'])
                    );
                }

            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        return $parent_categories;
    }


    function get_deal_components($scope = 'keys', $options = 'Active') {

        switch ($options) {
            case 'Active':
                $where = 'AND `Deal Component Status`=\'Active\'';
                break;
            default:
                $where = '';
                break;
        }


        $deal_components = array();


        $sql = sprintf(
            "SELECT `Deal Component Key` FROM `Deal Component Dimension` WHERE `Deal Component Allowance Target`='Category' AND `Deal Component Allowance Target Key`=%d $where", $this->id
        );

        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {

                if ($scope == 'objects') {
                    $deal_components[$row['Deal Component Key']] = get_object('DealComponent', $row['Deal Component Key']);
                } else {
                    $deal_components[$row['Deal Component Key']] = $row['Deal Component Key'];
                }


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        return $deal_components;


    }


}


?>