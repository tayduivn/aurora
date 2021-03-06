<?php
/*
 About:
 Author: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2009, Inikoo

 Version 2.0
*/
include_once 'class.DB_Table.php';


class TaxCategory extends DB_Table {


    function __construct($a1, $a2 = false) {

        global $db;
        $this->db = $db;

        $this->table_name    = 'Tax Category';
        $this->ignore_fields = array();

        if ($a1 and !$a2) {
            $this->get_data('code', $a1);
        } else {
            $this->get_data($a1, $a2);
        }
    }


    function get_data($key, $tag) {

        if ($key == 'code') {


            $account=get_object('Account',1);




            $sql = sprintf(
                "SELECT *   FROM kbase.`Tax Category Dimension` WHERE `Tax Category Code`=%s  and `Tax Category Country Code`=%s ", prepare_mysql($tag), prepare_mysql($account->get('Account Country Code'))
            );
        } elseif ($key == 'key' or $key == 'id') {
            $sql = sprintf(
                "SELECT *   FROM kbase.`Tax Category Dimension` WHERE `Tax Category Key`=%d ", $tag
            );
        } else {
            return;
        }



        if ($this->data = $this->db->query($sql)->fetch()) {
            $this->id   = $this->data['Tax Category Key'];
            $this->code = $this->data['Tax Category Code'];
        }



    }


    function get($key, $data = false) {
        switch ($key) {

            default:
                if (isset($this->data[$key])) {
                    return $this->data[$key];
                } else {
                    return '';
                }
        }

        return '';
    }


}


