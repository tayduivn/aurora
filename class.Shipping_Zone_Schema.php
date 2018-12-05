<?php
/*
 /*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 1 December 2018 at 13:23:50 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3

*/


include_once('class.DB_Table.php');

class Shipping_Zone_Schema extends DB_Table {


    function __construct($a1, $a2 = false) {

        global $db;
        $this->db = $db;

        $this->table_name    = 'Shipping Zone Schema';
        $this->ignore_fields = array('Shipping Zone Schema Key');

        if (is_numeric($a1) and !$a2) {
            $this->get_data('id', $a1);
        } else {
            if (preg_match('/find/i', $a1)) {
                $this->find($a2, $a1);
            } else {
                $this->get_data($a1, $a2);
            }
        }

    }

    function get_data($tipo, $tag) {


        $sql = sprintf(
            "SELECT * FROM `Shipping Zone Schema Dimension` WHERE `Shipping Zone Schema Key`=%d", $tag
        );


        if ($this->data = $this->db->query($sql)->fetch()) {
            $this->id = $this->data['Shipping Zone Schema Key'];


        }


    }

    function load_data() {

        $sql = sprintf("SELECT * FROM `Shipping Zone Schema Data`  WHERE `Shipping Zone Schema Key`=%d", $this->id);


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                foreach ($row as $key => $value) {
                    $this->data[$key] = $value;
                }
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }




    }

    function find($raw_data, $options) {

        if (isset($raw_data['editor']) and is_array($raw_data['editor'])) {
            foreach ($raw_data['editor'] as $key => $value) {

                if (array_key_exists($key, $this->editor)) {
                    $this->editor[$key] = $value;
                }

            }
        }

        $this->found     = false;
        $this->found_key = 0;


        if (preg_match('/create/i', $options)) {
            $create = 'create';
        } else {
            $create = '';
        }


        $data = $this->base_data();
        foreach ($raw_data as $key => $value) {

            if (array_key_exists($key, $data)) {
                $data[$key] = $value;
            }

        }


        $sql = sprintf(
            "SELECT `Shipping Zone Schema Key` FROM `Shipping Zone Schema Dimension` WHERE  `Shipping Zone Schema Store Key`=%d and `Shipping Zone Schema Label`=%s   ",
            $data['Shipping Zone Schema Store Key'],
            prepare_mysql($data['Shipping Zone Schema Label'])

        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->found     = true;
                $this->found_key = $row['Shipping Zone Schema Key'];
                $this->duplicated_field='Shipping Zone Schema Label';
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        if ($this->found) {
            $this->get_data('id', $this->found_key);
        }

        if ($create and !$this->found) {
            $this->create($data);

        }


    }


    function create($data) {


        $keys   = '(';
        $values = 'values(';
        foreach ($data as $key => $value) {
            $keys   .= "`$key`,";
            $values .= prepare_mysql($value).",";
        }
        $keys   = preg_replace('/,$/', ')', $keys);
        $values = preg_replace('/,$/', ')', $values);
        $sql    = sprintf(
            "INSERT INTO `Shipping Zone Schema Dimension` %s %s", $keys, $values
        );


        if ($this->db->exec($sql)) {
            $this->new=true;
            $this->id = $this->db->lastInsertId();
            $this->get_data('id', $this->id);

            $sql    = sprintf(
                "INSERT INTO `Shipping Zone Schema Data` (`Shipping Zone Schema Key`) values (%d)", $this->id
            );
            $this->db->exec($sql);

            $history_data = array(
                'History Abstract' => sprintf(_('%s shipping zone schema created'), $this->get('Label')),
                'History Details'  => '',
                'Action'           => 'created'
            );

            $this->add_subject_history(
                $history_data, true, 'No', 'Changes', $this->get_object_name(), $this->id
            );


        } else {
            print "Error can not create charge  $sql\n";
            exit;

        }
    }


    function get($key = '') {


        if (!$this->id) {
            return;
        }

        switch ($key) {
            case 'Amount':
                $store = get_object('Store', $this->data['Shipping Zone Schema Store Key']);

                return money($this->data['Shipping Zone Schema Total Acc '.$key], $store->get('Store Currency Code'));

                break;
            case 'Orders':
            case 'Customers':

                return number($this->data['Shipping Zone Schema Total Acc '.$key]);

                break;

            case 'Number History Records':

                return number($this->data['Shipping Zone Schema '.$key]);

                break;
            default:
                if (array_key_exists($key, $this->data)) {
                    return $this->data[$key];
                }

                if (array_key_exists('Shipping Zone Schema '.$key, $this->data)) {
                    return $this->data[$this->table_name.' '.$key];
                }


                return false;
        }


    }

    function create_shipping_zone($data) {



        include_once 'class.Shipping_Zone.php';

        if (!array_key_exists('Shipping Zone Code', $data) or $data['Shipping Zone Code'] == '') {
            $this->error = true;
            $this->msg   = 'error, no code';

            return;
        }

        if (!array_key_exists('Shipping Zone Name', $data) or $data['Shipping Zone Name'] == '') {
            $this->error = true;
            $this->msg   = 'error, no name';

            return;
        }


        $this->load_data();

        $data['Shipping Zone Store Key'] = $this->get('Shipping Zone Schema Store Key');
        $data['Shipping Zone Shipping Zone Schema Key'] = $this->id;

        $data['Shipping Zone Creation Date'] = gmdate('Y-m-d H:i:s');

        $data['Shipping Zone Position'] = $this->get('Shipping Zone Schema Number Zones')+1;


        $shipping_zone = new Shipping_Zone('find create', $data);


        if ($shipping_zone->id) {



            $this->new_object_msg = $shipping_zone->msg;

            if ($shipping_zone->new) {
                $this->new_object = true;
                $this->update_shipping_zones();
                $this->new=true;

            } else {
                $this->error = true;
                if ($shipping_zone->found) {

                    $this->error_code     = 'duplicated_field';
                    $this->error_metadata = json_encode(array($shipping_zone->duplicated_field));

                    if ($shipping_zone->duplicated_field == 'Shipping Zone Code') {
                        $this->msg = _('Duplicated code');
                    }if ($shipping_zone->duplicated_field == 'Shipping Zone Name') {
                        $this->msg = _('Duplicated name');
                    }


                } else {
                    $this->msg = $shipping_zone->msg;
                }
            }

            return $shipping_zone;
        } else {
            $this->error = true;
            $this->msg   = $shipping_zone->msg;
        }

    }


    function get_field_label($field) {


        switch ($field) {
            case 'Shipping Zone Schema Code':
                $label = _('code');
                break;
            case 'Shipping Zone Schema Name':
                $label = _('name');
                break;
            case 'Shipping Zone Schema Description':
                $label = _('description');
                break;


            default:


                $label = $field;

        }

        return $label;

    }

    function update_shipping_zones(){
        $zones = 0;

        $sql = sprintf(
            "SELECT count(*) as num FROM  `Shipping Zone Dimension`  WHERE `Shipping Zone Shipping Zone Schema Key`=%d  ",
            $this->id

        );




        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {



                $zones= $row['num'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $this->fast_update(
            array(

                'Shipping Zone Schema Number Zones'    => $zones,
            ),'Shipping Zone Schema Data'

        );

    }

    function update_usage() {

        $orders    = 0;
        $customers = 0;
        $amount    = 0;

        $sql = sprintf(
            "SELECT sum(`Transaction Net Amount`) as amount,count( DISTINCT O.`Order Key`) AS orders,count( DISTINCT `Order Customer Key`) AS customers FROM `Order No Product Transaction Fact` B LEFT  JOIN `Order Dimension` O ON (O.`Order Key`=B.`Order Key`) WHERE `Transaction Type Key`=%d AND `Transaction Type`='Shipping' AND `Order State` not in ('InBasket','Cancelled') ",
            $this->id

        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $orders    = $row['orders'];
                $customers = $row['customers'];
                $amount    = $row['amount'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $this->fast_update(
            array(
                'Shipping Zone Schema Total Acc Submitted Orders'    => $orders,
                'Shipping Zone Schema Total Acc Submitted Orders Customers' => $customers,
                'Shipping Zone Schema Total Acc Submitted Orders Amount'    => $amount,
            ),'Shipping Zone Schema Data'

        );

        $orders    = 0;
        $customers = 0;
        $amount    = 0;

        $sql = sprintf(
            "SELECT sum(`Transaction Net Amount`) as amount,count( DISTINCT O.`Order Key`) AS orders,count( DISTINCT `Order Customer Key`) AS customers FROM `Order No Product Transaction Fact` B LEFT   JOIN `Shipping Zone Dimension` SZ ON (SZ.`Shipping Zone Key`=B.`Transaction Type Key`)  JOIN `Order Dimension` O ON (O.`Order Key`=B.`Order Key`) WHERE `Shipping Zone Shipping Zone Schema Key`=%d AND `Transaction Type`='Shipping' AND `Order State`  in ('InBasket') ",
            $this->id

        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $orders    = $row['orders'];
                $customers = $row['customers'];
                $amount    = $row['amount'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $this->fast_update(
            array(
                'Shipping Zone Schema Total Acc Basket Orders'    => $orders,
                'Shipping Zone Schema Total Acc Basket Orders Customers' => $customers,
                'Shipping Zone Schema Total Acc Basket Orders Amount'    => $amount,
            ),'Shipping Zone Schema Data'

        );


    }


}