<?php
/*
  File: Company.php

  This file contains the Company Class

  About:
  Author: Raul Perusquia <rulovico@gmail.com>

  Copyright (c) 2009, Inikoo

  Version 2.0
*/


include_once 'class.DB_Table.php';

class Store extends DB_Table {


    function Store($a1, $a2 = false, $a3 = false, $_db = false) {

        if (!$_db) {
            global $db;
            $this->db = $db;
        } else {
            $this->db = $_db;
        }

        $this->table_name    = 'Store';
        $this->ignore_fields = array('Store Key');

        if (is_numeric($a1) and !$a2) {
            $this->get_data('id', $a1);
        } elseif ($a1 == 'find') {
            $this->find($a2, $a3);

        } else {
            $this->get_data($a1, $a2);
        }

    }


    function get_data($tipo, $tag) {

        if ($tipo == 'id') {
            $sql = sprintf(
                "SELECT * FROM `Store Dimension` WHERE `Store Key`=%d", $tag
            );
        } elseif ($tipo == 'code') {
            $sql = sprintf(
                "SELECT * FROM `Store Dimension` WHERE `Store Code`=%s", prepare_mysql($tag)
            );
        } else {
            return;
        }

        if ($this->data = $this->db->query($sql)->fetch()) {

            $this->id   = $this->data['Store Key'];
            $this->code = $this->data['Store Code'];
        }


    }

    function find($raw_data, $options) {

        if (isset($raw_data['editor'])) {
            foreach ($raw_data['editor'] as $key => $value) {
                if (array_key_exists($key, $this->editor)) {
                    $this->editor[$key] = $value;
                }
            }
        }

        $this->found     = false;
        $this->found_key = false;

        $create = '';

        if (preg_match('/create/i', $options)) {
            $create = 'create';
        }


        $data = $this->base_data();
        foreach ($raw_data as $key => $value) {
            if (array_key_exists($key, $data)) {
                $data[$key] = _trim($value);
            }
        }

        //    print_r($raw_data);

        if ($data['Store Code'] == '') {
            $this->error = true;
            $this->msg   = 'Store code empty';

            return;
        }

        if ($data['Store Name'] == '') {
            $data['Store Name'] = $data['Store Code'];
        }


        $sql = sprintf(
            "SELECT `Store Key` FROM `Store Dimension` WHERE `Store Code`=%s  ", prepare_mysql($data['Store Code'])
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {

                $this->found     = true;
                $this->found_key = $row['Store Key'];
                $this->get_data('id', $this->found_key);
                $this->duplicated_field = 'Store Code';

                return;
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }
        $sql = sprintf(
            "SELECT `Store Key` FROM `Store Dimension` WHERE `Store Name`=%s  ", prepare_mysql($data['Store Name'])
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {

                $this->found     = true;
                $this->found_key = $row['Store Key'];
                $this->get_data('id', $this->found_key);
                $this->duplicated_field = 'Store Name';

                return;
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        if ($create and !$this->found) {
            $this->create($data);

            return;
        }


    }

    function create($data) {


        $this->new = false;
        $base_data = $this->base_data();

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $base_data)) {
                $base_data[$key] = _trim($value);
            }
        }

        $keys   = '(';
        $values = 'values (';
        foreach ($base_data as $key => $value) {
            $keys .= "`$key`,";
            if (preg_match(
                '/Store Email|Store Telephone|Store Telephone|Slogan|URL|Fax|Sticky Note|Store VAT Number/i', $key
            )) {
                $values .= prepare_mysql($value, false).",";
            } else {
                $values .= prepare_mysql($value).",";
            }
        }
        $keys   = preg_replace('/,$/', ')', $keys);
        $values = preg_replace('/,$/', ')', $values);


        $sql = "insert into `Store Dimension` $keys  $values";

        if ($this->db->exec($sql)) {
            $this->id = $this->db->lastInsertId();


            $this->msg = _("Store Added");
            $this->get_data('id', $this->id);
            $this->new = true;

            if (is_numeric($this->editor['User Key']) and $this->editor['User Key'] > 1) {

                $sql = sprintf(
                    "INSERT INTO `User Right Scope Bridge` VALUES(%d,'Store',%d)", $this->editor['User Key'], $this->id
                );
                $this->db->exec($sql);

            }

            $sql = "INSERT INTO `Store Default Currency` (`Store Key`) VALUES(".$this->id.");";
            $this->db->exec($sql);

            $sql = "INSERT INTO `Store Data Currency` (`Store Key`) VALUES(".$this->id.");";
            $this->db->exec($sql);


            $sql = sprintf(
                "INSERT INTO `Store Data` (`Store Key`) VALUES (%d)", $this->id
            );

            $this->db->exec($sql);
            $sql = sprintf(
                "INSERT INTO `Store DC Data` (`Store Key`) VALUES (%d)", $this->id

            );
            $this->db->exec($sql);


            require_once 'conf/timeseries.php';

            $timeseries = get_time_series_config();


            $timeseries_data = $timeseries['Store'];

            include_once 'class.Timeserie.php';

            foreach ($timeseries_data as $time_series_data) {

                $time_series_data['editor'] = $this->editor;
                $this->create_timeseries($time_series_data);

            }


            $account         = new Account($this->db);
            $account->editor = $this->editor;


            $families_category_data = array(
                'Category Code'      => 'Fam.'.$this->get('Store Code'),
                'Category Label'     => 'Families',
                'Category Scope'     => 'Product',
                'Category Subject'   => 'Product',
                'Category Store Key' => $this->id


            );


            $families = $account->create_category($families_category_data);


            $departments_category_data = array(
                'Category Code'      => 'Dept.'.$this->get('Store Code'),
                'Category Label'     => 'Departments',
                'Category Scope'     => 'Product',
                'Category Subject'   => 'Category',
                'Category Store Key' => $this->id


            );


            $departments = $account->create_category($departments_category_data);

            $this->update(
                array(

                    'Store Family Category Key'     => $families->id,
                    'Store Department Category Key' => $departments->id,
                ), 'no_history'
            );


            $order_recursion_campaign_data = array(
                'Deal Campaign Name'       => 'Order recursion incentive',
                'Deal Campaign Valid From' => gmdate('Y-m-d'),
                'Deal Campaign Valid To'   => '',


            );

            $order_recursion_campaign = $this->create_campaign($order_recursion_campaign_data);

            $bulk_discounts_campaign_data = array(
                'Deal Campaign Name'       => 'Bulk discount',
                'Deal Campaign Valid From' => gmdate('Y-m-d'),
                'Deal Campaign Valid To'   => '',

            );

            $bulk_discounts_campaign = $this->create_campaign($bulk_discounts_campaign_data);


            $first_order_incentive_campaign_data = array(
                'Deal Campaign Name'       => 'First order incentive',
                'Deal Campaign Valid From' => gmdate('Y-m-d'),
                'Deal Campaign Valid To'   => '',


            );

            $first_order_incentive_campaign = $this->create_campaign($first_order_incentive_campaign_data);



            $this->update(
                array(

                    'Store Order Recursion Campaign Key' => $order_recursion_campaign->id,
                    'Store Bulk Discounts Campaign Key'  => $bulk_discounts_campaign->id,
                    'Store First Order Campaign Key'  => $first_order_incentive_campaign->id,

                ), 'no_history'
            );


            $history_data = array(
                'History Abstract' => sprintf(
                    _('Store %s (%s) created'), $this->data['Store Name'], $this->data['Store Code']
                ),
                'History Details'  => '',
                'Action'           => 'created'
            );

            $history_key = $this->add_subject_history(
                $history_data, true, 'No', 'Changes', $this->get_object_name(), $this->id
            );


            include_once 'class.Account.php';
            $account = new Account();
            $account->add_account_history($history_key);

            return;
        } else {
            print $sql;
            exit;
            $this->msg = _("Error can not create store");

        }

    }

    function create_timeseries($data, $fork_key = 0) {

        $data['Timeseries Parent']     = 'Store';
        $data['Timeseries Parent Key'] = $this->id;
        $data['editor']                = $this->editor;

        $timeseries = new Timeseries('find', $data, 'create');
        if ($timeseries->id) {
            require_once 'utils/date_functions.php';

            if ($this->data['Store Valid From'] != '') {
                $from = date('Y-m-d', strtotime($this->get('Valid From')));

            } else {
                $from = '';
            }

            if ($this->get('Store State') == 'Closed') {
                $to = $this->get('Valid To');
            } else {
                $to = date('Y-m-d');
            }


            $sql        = sprintf(
                'DELETE FROM `Timeseries Record Dimension` WHERE `Timeseries Record Timeseries Key`=%d AND `Timeseries Record Date`<%s ', $timeseries->id, prepare_mysql($from)
            );
            $update_sql = $this->db->prepare($sql);
            $update_sql->execute();
            if ($update_sql->rowCount()) {
                $timeseries->update(
                    array('Timeseries Updated' => gmdate('Y-m-d H:i:s')), 'no_history'
                );
            }

            $sql        = sprintf(
                'DELETE FROM `Timeseries Record Dimension` WHERE `Timeseries Record Timeseries Key`=%d AND `Timeseries Record Date`>%s ', $timeseries->id, prepare_mysql($to)
            );
            $update_sql = $this->db->prepare($sql);
            $update_sql->execute();
            if ($update_sql->rowCount()) {
                $timeseries->update(
                    array('Timeseries Updated' => gmdate('Y-m-d H:i:s')), 'no_history'
                );
            }

            if ($from and $to) {
                $this->update_timeseries_record($timeseries, $from, $to, $fork_key);
            }

            if ($timeseries->get('Timeseries Number Records') == 0) {
                $timeseries->update(
                    array('Timeseries Updated' => gmdate('Y-m-d H:i:s')), 'no_history'
                );
            }

        }

    }

    function get($key = '') {

        global $account;

        if (!$this->id) {
            return '';
        }




        switch ($key) {
            case $this->table_name.' Collect Address':

                $type = 'Collect';

                $address_fields = array(

                    'Address Recipient'            => $this->get($type.' Address Recipient'),
                    'Address Organization'         => $this->get($type.' Address Organization'),
                    'Address Line 1'               => $this->get($type.' Address Line 1'),
                    'Address Line 2'               => $this->get($type.' Address Line 2'),
                    'Address Sorting Code'         => $this->get($type.' Address Sorting Code'),
                    'Address Postal Code'          => $this->get($type.' Address Postal Code'),
                    'Address Dependent Locality'   => $this->get($type.' Address Dependent Locality'),
                    'Address Locality'             => $this->get($type.' Address Locality'),
                    'Address Administrative Area'  => $this->get($type.' Address Administrative Area'),
                    'Address Country 2 Alpha Code' => $this->get($type.' Address Country 2 Alpha Code'
                    ),


                );

                return  json_encode($address_fields);
                break;
            case 'Collect Address':


                return    $this->get($this->table_name.' '.$key.' Formatted');
                break;

            case('Google Map URL'):


                return '<iframe src="'.$this->data['Store Google Map URL'].'" width="1000" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>';


                $this->update_field('Store Google Map URL', $value);
                break;


            case 'State':
                switch ($this->data['Store State']) {
                    case 'Normal':
                        return _('Open');
                        break;
                    case 'Closed':
                        return _('Closed');
                        break;
                    default:
                        break;
                }
                break;
            case('Currency Code'):
                include_once 'utils/natural_language.php';

                return currency_label(
                    $this->data['Store Currency Code'], $this->db
                );
                break;

            case('Currency Symbol'):
                include_once 'utils/natural_language.php';

                return currency_symbol($this->data['Store Currency Code']);
                break;
            case('Valid From'):

                return strftime(
                    "%a %e %b %Y", strtotime($this->data['Store Valid From'].' +0:00')
                );
                break;
            case('Valid To'):
                return strftime(
                    "%a %e %b %Y", strtotime($this->data['Store Valid To'].' +0:00')
                );
                break;
            case("Sticky Note"):
                return nl2br($this->data['Store Sticky Note']);
                break;
            case('Contacts'):
            case('Active Contacts'):
            case('New Contacts'):
            case('Lost Contacts'):
            case('Losing Contacts'):
            case('Contacts With Orders'):
            case('Active Contacts With Orders'):
            case('New Contacts With Orders'):
            case('Lost Contacts With Orders'):
            case('Losing Contacts With Orders'):
            case('Active Web For Sale'):
            case('Active Web Out of Stock'):
            case('Active Web Offline'):
                return number($this->data['Store '.$key]);
            case 'Percentage Active Web Out of Stock':
                return percentage($this->data['Store Active Web Out of Stock'], $this->data['Store Active Products']);
            case 'Percentage Active Web Offline':
                return percentage($this->data['Store Active Web Offline'], $this->data['Store Active Products']);

            case('Potential Customers'):
                return number(
                    $this->data['Store Active Contacts'] - $this->data['Store Active Contacts With Orders']
                );
            case('Total Users'):
                return number($this->data['Store Total Users']);
            case('All To Pay Invoices'):
                return $this->data['Store Total Acc Invoices'] - $this->data['Store Paid Invoices'] - $this->data['Store Paid Refunds'];
            case('All Paid Invoices'):
                return $this->data['Store Paid Invoices'] - $this->data['Store Paid Refunds'];
            case('code'):
                return $this->data['Store Code'];
                break;
            case('type'):
                return $this->data['Store Type'];
                break;
            case('Total Products'):
                return $this->data['Store For Sale Products'] + $this->data['Store In Process Products'] + $this->data['Store Not For Sale Products'] + $this->data['Store Discontinued Products']
                    + $this->data['Store Unknown Sales State Products'];
                break;
            case('For Sale Products'):
                return number($this->data['Store For Sale Products']);
                break;
            case('For Public Sale Products'):
                return number($this->data['Store For Public Sale Products']);
                break;
            case('Families'):
                return number($this->data['Store Families']);
                break;
            case('Departments'):
                return number($this->data['Store Departments']);
                break;
            case('Percentage Active Contacts'):
                return percentage(
                    $this->data['Store Active Contacts'], $this->data['Store Contacts']
                );
            case('Percentage Total With Orders'):
                return percentage(
                    $this->data['Store Contacts With Orders'], $this->data['Store Contacts']
                );
            case 'Delta Today Start Orders In Warehouse Number':

                $start = $this->data['Store Today Start Orders In Warehouse Number'];
                $end   = $this->data['Store Orders In Warehouse Number'] + $this->data['Store Orders Packed Number'] + $this->data['Store Orders In Dispatch Area Number'];

                $diff = $end - $start;

                $delta = ($diff > 0 ? '+' : '').number($diff).delta_icon($end, $start, $inverse = true);


                return $delta;

            case 'Today Orders Dispatched':

                $number = 0;

                $sql = sprintf(
                    'SELECT count(*) AS num FROM `Order Dimension` WHERE `Order Store Key`=%d AND `Order State`="Dispatched" AND `Order Dispatched Date`>%s   AND  `Order Dispatched Date`<%s   ',
                    $this->id, prepare_mysql(date('Y-m-d 00:00:00')), prepare_mysql(date('Y-m-d 23:59:59'))
                );

                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {
                        $number = $row['num'];
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                return number($number);

            case 'Show in Warehouse Orders':
            case 'Store Show in Warehouse Orders':
                    return $this->data[$key];
                break;

        }


        if (preg_match('/^(DC Orders).*(Amount) Soft Minify$/', $key)) {

            $field = 'Store '.preg_replace('/ Soft Minify$/', '', $key);

            $suffix          = '';
            $fraction_digits = 'NO_FRACTION_DIGITS';
            $_amount         = $this->data[$field];


            $amount = money($_amount, $account->get('Account Currency'), $locale = false, $fraction_digits).$suffix;

            return $amount;
        }
        if (preg_match('/^(DC Orders).*(Amount|Profit)$/', $key)) {

            $field = 'Store '.$key;

            return money($this->data[$field], $account->get('Account Currency'));

            return $amount;
        }
        if (preg_match('/^(DC Orders).*(Amount|Profit) Minify$/', $key)) {

            $field = 'Store '.preg_replace('/ Minify$/', '', $key);

            $suffix          = '';
            $fraction_digits = 'NO_FRACTION_DIGITS';
            if ($this->data[$field] >= 1000000) {
                $suffix          = 'M';
                $fraction_digits = 'DOUBLE_FRACTION_DIGITS';
                $_amount         = $this->data[$field] / 1000000;
            } elseif ($this->data[$field] >= 10000) {
                $suffix  = 'K';
                $_amount = $this->data[$field] / 1000;
            } elseif ($this->data[$field] > 100) {
                $fraction_digits = 'SINGLE_FRACTION_DIGITS';
                $suffix          = 'K';
                $_amount         = $this->data[$field] / 1000;
            } else {
                $_amount = $this->data[$field];
            }

            $amount = money($_amount, $account->get('Account Currency'), $locale = false, $fraction_digits).$suffix;

            return $amount;


        }

        if (preg_match('/^(Orders|Last|Yesterday|Total|1|10|6|3|4|2|Year To|Quarter To|Month To|Today|Week To).*(Amount|Profit) Minify$/', $key)) {

            $field = 'Store '.preg_replace('/ Minify$/', '', $key);

            $suffix          = '';
            $fraction_digits = 'NO_FRACTION_DIGITS';
            if ($this->data[$field] >= 1000000) {
                $suffix          = 'M';
                $fraction_digits = 'DOUBLE_FRACTION_DIGITS';
                $_amount         = $this->data[$field] / 1000000;
            } elseif ($this->data[$field] >= 10000) {
                $suffix  = 'K';
                $_amount = $this->data[$field] / 1000;
            } elseif ($this->data[$field] > 100) {
                $fraction_digits = 'SINGLE_FRACTION_DIGITS';
                $suffix          = 'K';
                $_amount         = $this->data[$field] / 1000;
            } else {
                $_amount = $this->data[$field];
            }

            $amount = money($_amount, $this->get('Store Currency Code'), $locale = false, $fraction_digits).$suffix;

            return $amount;
        }

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        if (array_key_exists('Store '.$key, $this->data)) {
            return $this->data['Store '.$key];
        }

        if (preg_match('/^(Orders|Last|Yesterday|Total|1|10|6|3|4|2|Year To|Quarter To|Month To|Today|Week To).*(Amount|Profit) Soft Minify$/', $key)) {

            $field = 'Store '.preg_replace('/ Soft Minify$/', '', $key);

            $suffix          = '';
            $fraction_digits = 'NO_FRACTION_DIGITS';
            $_amount         = $this->data[$field];


            $amount = money($_amount, $this->get('Store Currency Code'), $locale = false, $fraction_digits).$suffix;

            return $amount;
        }
        if (preg_match('/^(Last|Yesterday|Total|1|10|6|3|2|4|5|Year To|Quarter To|Month To|Today|Week To).*(Quantity Invoiced|Invoices) Minify$/', $key)) {

            $field = 'Store '.preg_replace('/ Minify$/', '', $key);

            $suffix          = '';
            $fraction_digits = 0;
            if ($this->data[$field] >= 10000) {
                $suffix  = 'K';
                $_number = $this->data[$field] / 1000;
            } elseif ($this->data[$field] > 100) {
                $fraction_digits = 1;
                $suffix          = 'K';
                $_number         = $this->data[$field] / 1000;
            } else {
                $_number = $this->data[$field];
            }

            return number($_number, $fraction_digits).$suffix;
        }
        if (preg_match('/^(Last|Yesterday|Total|1|10|6|3|2|4|5|Year To|Quarter To|Month To|Today|Week To).*(Quantity Invoiced|Invoices) Soft Minify$/', $key)) {
            $field   = 'Store '.preg_replace('/ Soft Minify$/', '', $key);
            $_number = $this->data[$field];

            return number($_number, 0);
        }

        if (preg_match('/^(Orders|Total|1).*(Amount|Profit)$/', $key)) {

            $amount = 'Store '.$key;

            return money($this->data[$amount], $this->get('Store Currency Code'));
        }
        if (preg_match(
                '/^(Total|1).*(Quantity (Ordered|Invoiced|Delivered|)|Customers|Customers Contacts)$/', $key
            ) or preg_match('/^(Active Customers|Orders .* Number)$/', $key)
        ) {

            $amount = 'Store '.$key;

            return number($this->data[$amount]);
        }
        if (preg_match(
            '/^Delivery Notes For (Orders|Replacements|Shortages|Samples|Donations)$/', $key
        )) {

            $amount = 'Store '.$key;

            return number($this->data[$amount]);
        }





        if (preg_match('/(Orders|Delivery Notes|Invoices) Acc$/', $key)) {

            $amount = 'Store '.$key;

            return number($this->data[$amount]);
        } elseif (preg_match(
            '/(Orders|Delivery Notes|Invoices|Refunds|Orders In Process|(Active|New|Suspended|Discontinuing|Discontinued) Products)$/', $key
        )) {

            $amount = 'Store '.$key;

            return number($this->data[$amount]);
        }





    }

    function update_timeseries_record($timeseries, $from, $to, $fork_key = false) {

        if ($timeseries->get('Type') == 'StoreSales') {

            $dates = date_frequency_range(
                $this->db, $timeseries->get('Timeseries Frequency'), $from, $to
            );

            if ($fork_key) {

                $sql = sprintf(
                    "UPDATE `Fork Dimension` SET `Fork State`='In Process' ,`Fork Operations Total Operations`=%d,`Fork Start Date`=NOW(),`Fork Result`=%d  WHERE `Fork Key`=%d ", count($dates),
                    $timeseries->id, $fork_key
                );

                $this->db->exec($sql);
            }
            $index = 0;
            foreach ($dates as $date_frequency_period) {
                $index++;
                $sales_data = $this->get_sales_data($date_frequency_period['from'], $date_frequency_period['to']);
                $_date      = gmdate('Y-m-d', strtotime($date_frequency_period['from'].' +0:00'));


                if ($sales_data['invoices'] > 0 or $sales_data['refunds'] > 0 or $sales_data['customers'] > 0 or $sales_data['amount'] != 0 or $sales_data['dc_amount'] != 0 or $sales_data['profit']
                    != 0 or $sales_data['dc_profit'] != 0
                ) {

                    list($timeseries_record_key, $date) = $timeseries->create_record(array('Timeseries Record Date' => $_date));

                    $sql = sprintf(
                        'UPDATE `Timeseries Record Dimension` SET `Timeseries Record Integer A`=%d ,`Timeseries Record Integer B`=%d ,`Timeseries Record Integer C`=%d ,`Timeseries Record Float A`=%.2f ,  `Timeseries Record Float B`=%f ,`Timeseries Record Float C`=%f ,`Timeseries Record Float D`=%f ,`Timeseries Record Type`=%s WHERE `Timeseries Record Key`=%d',
                        $sales_data['invoices'], $sales_data['refunds'], $sales_data['customers'], $sales_data['amount'], $sales_data['dc_amount'], $sales_data['profit'], $sales_data['dc_profit'],
                        prepare_mysql('Data'), $timeseries_record_key

                    );


                    //  print "$sql\n";

                    $update_sql = $this->db->prepare($sql);
                    $update_sql->execute();
                    if ($update_sql->rowCount() or $date == date('Y-m-d')) {
                        $timeseries->update(
                            array(
                                'Timeseries Updated' => gmdate(
                                    'Y-m-d H:i:s'
                                )
                            ), 'no_history'
                        );
                    }


                } else {
                    $sql = sprintf(
                        'DELETE FROM `Timeseries Record Dimension` WHERE `Timeseries Record Timeseries Key`=%d AND `Timeseries Record Date`=%s ', $timeseries->id, prepare_mysql($_date)
                    );

                    $update_sql = $this->db->prepare($sql);
                    $update_sql->execute();
                    if ($update_sql->rowCount()) {
                        $timeseries->update(
                            array(
                                'Timeseries Updated' => gmdate(
                                    'Y-m-d H:i:s'
                                )
                            ), 'no_history'
                        );

                    }

                }
                if ($fork_key) {
                    $skip_every = 1;
                    if ($index % $skip_every == 0) {
                        $sql = sprintf(
                            "UPDATE `Fork Dimension` SET `Fork Operations Done`=%d  WHERE `Fork Key`=%d ", $index, $fork_key
                        );
                        $this->db->exec($sql);

                    }

                }
                $timeseries->update_stats();

            }

        }


        if ($fork_key) {

            $sql = sprintf(
                "UPDATE `Fork Dimension` SET `Fork State`='Finished' ,`Fork Finished Date`=NOW(),`Fork Operations Done`=%d,`Fork Result`=%d WHERE `Fork Key`=%d ", $index, $timeseries->id, $fork_key
            );

            $this->db->exec($sql);

        }

    }

    function get_sales_data($from_date, $to_date) {

        $sales_data = array(
            'discount_amount'    => 0,
            'amount'             => 0,
            'invoices'           => 0,
            'refunds'            => 0,
            'replacements'       => 0,
            'deliveries'         => 0,
            'profit'             => 0,
            'dc_amount'          => 0,
            'dc_discount_amount' => 0,
            'dc_profit'          => 0,
            'customers'          => 0,
            'repeat_customers'   => 0,

        );


        $sql = sprintf(
            "SELECT count(DISTINCT `Invoice Customer Key`)  AS customers,sum(if(`Invoice Type`='Invoice',1,0))  AS invoices, sum(if(`Invoice Type`='Refund',1,0))  AS refunds,sum(`Invoice Items Discount Amount`) AS discounts,sum(`Invoice Total Net Amount`) net  ,sum(`Invoice Total Profit`) AS profit ,sum(`Invoice Items Discount Amount`*`Invoice Currency Exchange`) AS dc_discounts,sum(`Invoice Total Net Amount`*`Invoice Currency Exchange`) dc_net  ,sum(`Invoice Total Profit`*`Invoice Currency Exchange`) AS dc_profit FROM `Invoice Dimension` WHERE `Invoice Store Key`=%d %s %s",
            $this->id, ($from_date ? sprintf('and `Invoice Date`>%s', prepare_mysql($from_date)) : ''), ($to_date ? sprintf('and `Invoice Date`<%s', prepare_mysql($to_date)) : '')

        );

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $sales_data['discount_amount']    = $row['discounts'];
                $sales_data['amount']             = $row['net'];
                $sales_data['profit']             = $row['profit'];
                $sales_data['invoices']           = $row['invoices'];
                $sales_data['refunds']            = $row['refunds'];
                $sales_data['dc_discount_amount'] = $row['dc_discounts'];
                $sales_data['dc_amount']          = $row['dc_net'];
                $sales_data['dc_profit']          = $row['dc_profit'];
                $sales_data['customers']          = $row['customers'];

            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $sql = sprintf(
            "SELECT count(*)  AS replacements FROM `Delivery Note Dimension` WHERE `Delivery Note Type` IN ('Replacement & Shortages','Replacement','Shortages') AND `Delivery Note Store Key`=%d %s %s",
            $this->id, ($from_date ? sprintf(
            'and `Delivery Note Date`>%s', prepare_mysql($from_date)
        ) : ''), ($to_date ? sprintf(
            'and `Delivery Note Date`<%s', prepare_mysql($to_date)
        ) : '')

        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $sales_data['replacements'] = $row['replacements'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $sql = sprintf(
            "SELECT count(*)  AS delivery_notes FROM `Delivery Note Dimension` WHERE `Delivery Note Type` IN ('Order') AND `Delivery Note Store Key`=%d %s %s", $this->id, ($from_date ? sprintf(
            'and `Delivery Note Date`>%s', prepare_mysql($from_date)
        ) : ''), ($to_date ? sprintf(
            'and `Delivery Note Date`<%s', prepare_mysql($to_date)
        ) : '')

        );

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $sales_data['deliveries'] = $row['delivery_notes'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $sql = sprintf(
            " SELECT COUNT(*) AS repeat_customers FROM( SELECT count(*) AS invoices ,`Invoice Customer Key` FROM `Invoice Dimension` WHERE `Invoice Store Key`=%d  %s %s GROUP BY `Invoice Customer Key` HAVING invoices>1) AS tmp",
            $this->id, ($from_date ? sprintf('and `Invoice Date`>%s', prepare_mysql($from_date)) : ''), ($to_date ? sprintf('and `Invoice Date`<%s', prepare_mysql($to_date)) : '')
        );

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $sales_data['repeat_customers'] = $row['repeat_customers'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        return $sales_data;


    }

    function create_campaign($data) {


        include_once 'class.DealCampaign.php';

        if (!array_key_exists('Deal Campaign Name', $data) or $data['Deal Campaign Name'] == '') {
            $this->error = true;
            $this->msg   = 'error, no campaign name';

            return;
        }

        if (!array_key_exists('Deal Campaign Valid From', $data)) {
            $this->error = true;
            $this->msg   = 'error, no campaign start date';

            return;
        }

        if ($data['Deal Campaign Valid From'] == '') {
            $data['Deal Campaign Valid From'] = gmdate('Y-m-d');
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['Deal Campaign Valid From'])) {
            $data['Deal Campaign Valid From'] .= ' 00:00:00';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['Deal Campaign Valid To'])) {
            $data['Deal Campaign Valid To'] .= ' 23:59:59';
        }

        $data['Deal Campaign Store Key'] = $this->id;


        $campaign = new DealCampaign('find create', $data);


        if ($campaign->id) {
            $this->new_object_msg = $campaign->msg;

            if ($campaign->new) {
                $this->new_object = true;
                $this->update_campaigns_data();


            } else {
                $this->error = true;
                if ($campaign->found) {

                    $this->error_code     = 'duplicated_field';
                    $this->error_metadata = json_encode(array($campaign->duplicated_field));

                    if ($campaign->duplicated_field == 'Deal Campaign Name') {
                        $this->msg = _('Duplicated campaign name');
                    }


                } else {
                    $this->msg = $campaign->msg;
                }
            }

            return $campaign;
        } else {
            $this->error = true;
            $this->msg   = $campaign->msg;
        }

    }

    function update_campaigns_data() {

        $campaigns = 0;

        $sql = sprintf(
            "SELECT count(*) AS num FROM `Deal Campaign Dimension` WHERE `Deal Campaign Store Key`=%d AND `Deal Campaign Status`='Active' ", $this->id
        );

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $campaigns = $row['num'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $this->update(array('Store Active Deal Campaigns' => $campaigns), 'no_history');

    }

    function load_acc_data() {

        $sql = sprintf("SELECT * FROM `Store Data`  WHERE `Store Key`=%d", $this->id);



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

        $sql = sprintf("SELECT * FROM `Store DC Data`  WHERE `Store Key`=%d", $this->id);

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

    function delete() {
        $this->deleted = false;
        $this->update_customers_data();

        if ($this->data['Store Contacts'] == 0) {

            $sql = sprintf("SELECT `Category Key` FROM `Category Dimension` WHERE `Category Store Key`=%d", $this->id);

            include_once 'class.Category.php';
            if ($result = $this->db->query($sql)) {
                foreach ($result as $row) {
                    $category = new Category($row['Category Key']);
                    $category->delete();
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }


            $sql = sprintf("DELETE FROM `Store Dimension` WHERE `Store Key`=%d", $this->id);
            $this->db->exec($sql);
            $this->deleted = true;
            $sql           = sprintf("DELETE FROM `User Right Scope Bridge` WHERE `Scope`='Store' AND `Scope Key`=%d ", $this->id);
            $this->db->exec($sql);

            $sql = sprintf("DELETE FROM `Store Data` WHERE `Store Key`=%d ", $this->id);
            $this->db->exec($sql);
            $sql = sprintf("DELETE FROM `Store DC Data` WHERE `Store Key`=%d ", $this->id);
            $this->db->exec($sql);
            $sql = sprintf("DELETE FROM `Invoice Category Dimension` WHERE `Invoice Category Store Key`=%d ", $this->id);
            $this->db->exec($sql);


            $sql = sprintf("SELECT `Timeseries Key` FROM `Timeseries Dimension` WHERE `Timeseries Parent`='Store' AND `Timeseries Parent Key`=%d ", $this->id);

            if ($result = $this->db->query($sql)) {
                foreach ($result as $row) {
                    $sql = sprintf("DELETE FROM FROM `Category Record Dimension` WHERE `Timeseries Record Timeseries Key`=%d", $row['Timeseries Key']);
                    $this->db->exec($sql);
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                print "$sql\n";
                exit;
            }
            $sql = sprintf("DELETE FROM `Timeseries Dimension` WHERE `Timeseries Parent`='Store' AND `Timeseries Parent Key`=%d ", $this->id);
            $this->db->exec($sql);


            $history_key = $this->add_history(
                array(
                    'Action'           => 'deleted',
                    'History Abstract' => sprintf(_('Store %d deleted'), $this->data['Store Name']),
                    'History Details'  => ''
                ), true
            );

            include_once 'class.Account.php';

            $hq = new Account();
            $hq->add_account_history($history_key);


            $this->deleted = true;
        } else {

            $this->update();

        }
    }

    function update_customers_data() {

        $this->data['Store Contacts']                    = 0;
        $this->data['Store New Contacts']                = 0;
        $this->data['Store Contacts With Orders']        = 0;
        $this->data['Store Active Contacts']             = 0;
        $this->data['Store Losing Contacts']             = 0;
        $this->data['Store Lost Contacts']               = 0;
        $this->data['Store New Contacts With Orders']    = 0;
        $this->data['Store Active Contacts With Orders'] = 0;
        $this->data['Store Losing Contacts With Orders'] = 0;
        $this->data['Store Lost Contacts With Orders']   = 0;
        $this->data['Store Contacts Who Visit Website']  = 0;


        $sql = sprintf(
            "SELECT count(*) AS num FROM  `Customer Dimension`    WHERE   `Customer Number Web Logins`>0  AND `Customer Store Key`=%d  ", $this->id
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->data['Store Contacts Who Visit Website'] = $row['num'];

            } else {
                $this->data['Store Contacts Who Visit Website'] = 0;

            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $sql = sprintf(
            "SELECT count(*) AS num ,sum(IF(`Customer New`='Yes',1,0)) AS new,  sum(IF(`Customer Type by Activity`='Active'   ,1,0)) AS active, sum(IF(`Customer Type by Activity`='Losing',1,0)) AS losing, sum(IF(`Customer Type by Activity`='Lost',1,0)) AS lost  FROM   `Customer Dimension` WHERE `Customer Store Key`=%d ",
            $this->id
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->data['Store Contacts']        = $row['num'];
                $this->data['Store New Contacts']    = $row['new'];
                $this->data['Store Active Contacts'] = $row['active'];
                $this->data['Store Losing Contacts'] = $row['losing'];
                $this->data['Store Lost Contacts']   = $row['lost'];

            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }

        $sql = sprintf(
            "SELECT count(*) AS num ,sum(IF(`Customer New`='Yes',1,0)) AS new,sum(IF(`Customer New`='Yes',1,0)) AS new,sum(IF(`Customer Type by Activity`='Active'   ,1,0)) AS active, sum(IF(`Customer Type by Activity`='Losing',1,0)) AS losing, sum(IF(`Customer Type by Activity`='Lost',1,0)) AS lost  FROM   `Customer Dimension` WHERE `Customer Store Key`=%d AND `Customer With Orders`='Yes'",
            $this->id
        );
        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->data['Store Contacts With Orders']        = $row['num'];
                $this->data['Store New Contacts With Orders']    = $row['new'];
                $this->data['Store Active Contacts With Orders'] = $row['active'];
                $this->data['Store Losing Contacts With Orders'] = $row['losing'];
                $this->data['Store Lost Contacts With Orders']   = $row['lost'];

            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $sql = sprintf(
            "UPDATE `Store Dimension` SET
                     `Store Contacts`=%d,
                     `Store New Contacts`=%d,
                     `Store Active Contacts`=%d ,
                     `Store Losing Contacts`=%d ,
                     `Store Lost Contacts`=%d ,

                     `Store Contacts With Orders`=%d,
                     `Store New Contacts With Orders`=%d,
                     `Store Active Contacts With Orders`=%d,
                     `Store Losing Contacts With Orders`=%d,
                     `Store Lost Contacts With Orders`=%d,
                     `Store Contacts Who Visit Website`=%d
                     WHERE `Store Key`=%d  ", $this->data['Store Contacts'], $this->data['Store New Contacts'], $this->data['Store Active Contacts'], $this->data['Store Losing Contacts'],
            $this->data['Store Lost Contacts'],

            $this->data['Store Contacts With Orders'], $this->data['Store New Contacts With Orders'], $this->data['Store Active Contacts With Orders'],
            $this->data['Store Losing Contacts With Orders'], $this->data['Store Lost Contacts With Orders'], $this->data['Store Contacts Who Visit Website'],

            $this->id
        );

        $this->db->exec($sql);

    }

    function update_children_data() {
        $this->update_product_data();

    }

    function update_product_data() {


        $active_products        = 0;
        $suspended_products     = 0;
        $discontinuing_products = 0;
        $discontinued_products  = 0;

        $elements_active_web_status_numbers = array(
            'For Sale'     => 0,
            'Out of Stock' => 0,
            'Offline'      => 0

        );


        //'InProcess','Active','Suspended','Discontinuing','Discontinued'

        $sql = sprintf(
            'SELECT count(*) AS num, `Product Status` FROM `Product Dimension` WHERE `Product Store Key`=%d GROUP BY `Product Status`',

            $this->id
        );

        //print $sql;

        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                if ($row['Product Status'] == 'Active') {
                    $active_products = $row['num'];
                } elseif ($row['Product Status'] == 'Discontinuing') {
                    $discontinuing_products = $row['num'];
                } elseif ($row['Product Status'] == 'Suspended') {
                    $suspended_products = $row['num'];
                } elseif ($row['Product Status'] == 'Discontinued') {
                    $discontinued_products = $row['num'];
                }
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $active_products = $active_products + $discontinuing_products;

        $sql = sprintf(
            "SELECT count(*) AS num ,`Product Web State` AS web_state FROM  `Product Dimension` P WHERE `Product Store Key`=%d AND `Product Status` IN ('Active','Discontinuing') GROUP BY  `Product Web State`   ",
            $this->id

        );

        // print "$sql\n";

        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                if ($row['web_state'] == 'Discontinued') {
                    $row['web_state'] = 'Offline';
                }
                $elements_active_web_status_numbers[$row['web_state']] += $row['num'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $this->update(
            array(
                'Store Active Products'         => $active_products,
                'Store Suspended Products'      => $suspended_products,
                'Store Discontinuing Products'  => $discontinuing_products,
                'Store Discontinued Products'   => $discontinued_products,
                'Store Active Web For Sale'     => $elements_active_web_status_numbers['For Sale'],
                'Store Active Web Out of Stock' => $elements_active_web_status_numbers['Out of Stock'],
                'Store Active Web Offline'      => $elements_active_web_status_numbers['Offline']

            ), 'no_history'
        );


    }

    function create_sr_category($parent_category_key, $suffix = '') {


        $parent_category = new Category($parent_category_key);
        if (!$parent_category->id) {
            return;
        }

        $data     = array(
            'Category Store Key' => $this->id,
            'Category Code'      => $this->data['Store Code'].($suffix != '' ? '.'.$suffix : ''),
            'Category Subject'   => 'Invoice',
            'Category Function'  => 'if(true)'
        );
        $category = $parent_category->create_children($data);
        if (!$category->new) {
            if ($suffix == '') {
                $this->sr_category_suffix = 2;
            } else {
                $this->sr_category_suffix++;
            }
            $this->create_sr_category(
                $parent_category_key, $this->sr_category_suffix
            );


        }


    }

    function update_orders() {

        $this->update_orders_in_basket_data();
        $this->update_orders_in_process_data();
        $this->update_orders_in_warehouse_data();
        $this->update_orders_packed_data();
        $this->update_orders_approved_data();
        $this->update_orders_dispatched();
        $this->update_orders_dispatched_today();

        $this->update_orders_cancelled();


        $this->data['Store Total Acc Orders']  = 0;
        $this->data['Store Dispatched Orders'] = 0;
        $this->data['Store Cancelled Orders']  = 0;
        $this->data['Store Orders In Process'] = 0;

        $this->data['Store Total Acc Invoices']      = 0;
        $this->data['Store Invoices']                = 0;
        $this->data['Store Refunds']                 = 0;
        $this->data['Store Paid Invoices']           = 0;
        $this->data['Store Paid Refunds']            = 0;
        $this->data['Store Partially Paid Invoices'] = 0;
        $this->data['Store Partially Paid Refunds']  = 0;

        $this->data['Store Total Acc Delivery Notes']         = 0;
        $this->data['Store Ready to Pick Delivery Notes']     = 0;
        $this->data['Store Picking Delivery Notes']           = 0;
        $this->data['Store Packing Delivery Notes']           = 0;
        $this->data['Store Ready to Dispatch Delivery Notes'] = 0;
        $this->data['Store Dispatched Delivery Notes']        = 0;
        $this->data['Store Cancelled Delivery Notes']         = 0;


        $this->data['Store Delivery Notes For Orders']       = 0;
        $this->data['Store Delivery Notes For Replacements'] = 0;
        $this->data['Store Delivery Notes For Samples']      = 0;
        $this->data['Store Delivery Notes For Donations']    = 0;
        $this->data['Store Delivery Notes For Shortages']    = 0;


        $sql =
            "SELECT count(*) AS `Store Total Acc Orders`,sum(IF(`Order Current Dispatch State`='Dispatched',1,0 )) AS `Store Dispatched Orders` ,sum(IF(`Order Current Dispatch State`='Cancelled',1,0 )) AS `Store Cancelled Orders` FROM `Order Dimension`   WHERE `Order Store Key`="
            .$this->id;

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->data['Store Total Acc Orders']  = $row['Store Total Acc Orders'];
                $this->data['Store Dispatched Orders'] = $row['Store Dispatched Orders'];
                $this->data['Store Cancelled Orders']  = $row['Store Cancelled Orders'];

                $this->data['Store Orders In Process'] =
                    $this->data['Store Total Acc Orders'] - $this->data['Store Dispatched Orders'] - $this->data['Store Cancelled Orders'] - $this->data['Store Unknown Orders']
                    - $this->data['Store Suspended Orders'];

            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }

        $sql =
            "SELECT count(*) AS `Store Total Invoices`,sum(IF(`Invoice Type`='Invoice',1,0 )) AS `Store Invoices`,sum(IF(`Invoice Type`!='Invoice',1,0 )) AS `Store Refunds` ,sum(IF(`Invoice Paid`='Yes' AND `Invoice Type`='Invoice',1,0 )) AS `Store Paid Invoices`,sum(IF(`Invoice Paid`='Partially' AND `Invoice Type`='Invoice',1,0 )) AS `Store Partially Paid Invoices`,sum(IF(`Invoice Paid`='Yes' AND `Invoice Type`!='Invoice',1,0 )) AS `Store Paid Refunds`,sum(IF(`Invoice Paid`='Partially' AND `Invoice Type`!='Invoice',1,0 )) AS `Store Partially Paid Refunds` FROM `Invoice Dimension`   WHERE `Invoice Store Key`="
            .$this->id;


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->data['Store Total Acc Invoices']      = $row['Store Total Invoices'];
                $this->data['Store Invoices']                = $row['Store Invoices'];
                $this->data['Store Paid Invoices']           = $row['Store Paid Invoices'];
                $this->data['Store Partially Paid Invoices'] = $row['Store Partially Paid Invoices'];
                $this->data['Store Refunds']                 = $row['Store Refunds'];
                $this->data['Store Paid Refunds']            = $row['Store Paid Refunds'];
                $this->data['Store Partially Paid Refunds']  = $row['Store Partially Paid Refunds'];

            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $sql = "SELECT count(*) AS `Store Total Delivery Notes`,
             sum(IF(`Delivery Note State`='Cancelled'  OR `Delivery Note State`='Cancelled to Restock' ,1,0 )) AS `Store Returned Delivery Notes`,
             sum(IF(`Delivery Note State`='Ready to be Picked' ,1,0 )) AS `Store Ready to Pick Delivery Notes`,
             sum(IF(`Delivery Note State`='Picking & Packing' OR `Delivery Note State`='Picking' OR `Delivery Note State`='Picker Assigned' OR `Delivery Note State`='' ,1,0 )) AS `Store Picking Delivery Notes`,
             sum(IF(`Delivery Note State`='Packing' OR `Delivery Note State`='Packer Assigned' OR `Delivery Note State`='Picked' ,1,0 )) AS `Store Packing Delivery Notes`,
             sum(IF(`Delivery Note State`='Approved' OR `Delivery Note State`='Packed' ,1,0 )) AS `Store Ready to Dispatch Delivery Notes`,
             sum(IF(`Delivery Note State`='Dispatched' ,1,0 )) AS `Store Dispatched Delivery Notes`,
             sum(IF(`Delivery Note Type`='Replacement & Shortages' OR `Delivery Note Type`='Replacement' ,1,0 )) AS `Store Delivery Notes For Replacements`,
             sum(IF(`Delivery Note Type`='Replacement & Shortages' OR `Delivery Note Type`='Shortages' ,1,0 )) AS `Store Delivery Notes For Shortages`,
             sum(IF(`Delivery Note Type`='Sample' ,1,0 )) AS `Store Delivery Notes For Samples`,
             sum(IF(`Delivery Note Type`='Donation' ,1,0 )) AS `Store Delivery Notes For Donations`,
             sum(IF(`Delivery Note Type`='Order' ,1,0 )) AS `Store Delivery Notes For Orders`
             FROM `Delivery Note Dimension`   WHERE `Delivery Note Store Key`=".$this->id;

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->data['Store Total Acc Delivery Notes']         = $row['Store Total Delivery Notes'];
                $this->data['Store Ready to Pick Delivery Notes']     = $row['Store Ready to Pick Delivery Notes'];
                $this->data['Store Picking Delivery Notes']           = $row['Store Picking Delivery Notes'];
                $this->data['Store Packing Delivery Notes']           = $row['Store Packing Delivery Notes'];
                $this->data['Store Ready to Dispatch Delivery Notes'] = $row['Store Ready to Dispatch Delivery Notes'];
                $this->data['Store Dispatched Delivery Notes']        = $row['Store Dispatched Delivery Notes'];
                $this->data['Store Returned Delivery Notes']          = $row['Store Returned Delivery Notes'];
                $this->data['Store Delivery Notes For Replacements']  = $row['Store Delivery Notes For Replacements'];
                $this->data['Store Delivery Notes For Shortages']     = $row['Store Delivery Notes For Shortages'];
                $this->data['Store Delivery Notes For Samples']       = $row['Store Delivery Notes For Samples'];
                $this->data['Store Delivery Notes For Donations']     = $row['Store Delivery Notes For Donations'];
                $this->data['Store Delivery Notes For Orders']        = $row['Store Delivery Notes For Orders'];

            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $sql = sprintf(
            "UPDATE `Store Dimension` SET `Store Suspended Orders`=%d,`Store Dispatched Orders`=%d,`Store Cancelled Orders`=%d,`Store Orders In Process`=%d,`Store Unknown Orders`=%d
                    ,`Store Invoices`=%d ,`Store Refunds`=%d ,`Store Paid Invoices`=%d ,`Store Paid Refunds`=%d ,`Store Partially Paid Invoices`=%d ,`Store Partially Paid Refunds`=%d
                     ,`Store Ready to Pick Delivery Notes`=%d,`Store Picking Delivery Notes`=%d,`Store Packing Delivery Notes`=%d,`Store Ready to Dispatch Delivery Notes`=%d,`Store Dispatched Delivery Notes`=%d,`Store Returned Delivery Notes`=%d
                     ,`Store Delivery Notes For Replacements`=%d,`Store Delivery Notes For Shortages`=%d,`Store Delivery Notes For Samples`=%d,`Store Delivery Notes For Donations`=%d,`Store Delivery Notes For Orders`=%d
                     WHERE `Store Key`=%d", $this->data['Store Suspended Orders'], $this->data['Store Dispatched Orders'], $this->data['Store Cancelled Orders'],
            $this->data['Store Orders In Process'], $this->data['Store Unknown Orders'], $this->data['Store Invoices'], $this->data['Store Refunds'], $this->data['Store Paid Invoices'],
            $this->data['Store Paid Refunds'], $this->data['Store Partially Paid Invoices'], $this->data['Store Partially Paid Refunds'], $this->data['Store Ready to Pick Delivery Notes'],
            $this->data['Store Picking Delivery Notes'], $this->data['Store Picking Delivery Notes'], $this->data['Store Ready to Dispatch Delivery Notes'],
            $this->data['Store Dispatched Delivery Notes'], $this->data['Store Returned Delivery Notes'], $this->data['Store Delivery Notes For Replacements'],
            $this->data['Store Delivery Notes For Shortages'], $this->data['Store Delivery Notes For Samples'], $this->data['Store Delivery Notes For Donations'],
            $this->data['Store Delivery Notes For Orders'], $this->id
        );
        $this->db->exec($sql);

        $sql = sprintf(
            "UPDATE `Store Data` SET `Store Total Acc Orders`=%d,`Store Total Acc Invoices`=%d ,`Store Total Acc Delivery Notes`=%d WHERE `Store Key`=%d", $this->data['Store Total Acc Orders'],
            $this->data['Store Total Acc Invoices'], $this->data['Store Total Acc Delivery Notes'], $this->id
        );
        $this->db->exec($sql);


    }

    function update_orders_in_basket_data() {

        $data = array(
            'in_basket' => array(
                'number'    => 0,
                'amount'    => 0,
                'dc_amount' => 0
            ),
        );

        $sql = sprintf(
            "SELECT count(*) AS num,ifnull(sum(`Order Total Net Amount`),0) AS amount,ifnull(sum(`Order Total Net Amount`*`Order Currency Exchange`),0) AS dc_amount FROM `Order Dimension` USE INDEX (StoreState)  WHERE `Order Store Key`=%d AND  `Order State`='InBasket'  ",
            $this->id
        );




        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {



                $data['in_basket']['number']    = $row['num'];
                $data['in_basket']['amount']    = $row['amount'];
                $data['in_basket']['dc_amount'] = $row['dc_amount'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $data_to_update = array(
            'Store Orders In Basket Number'    => $data['in_basket']['number'],
            'Store Orders In Basket Amount'    => $data['in_basket']['amount'],
            'Store DC Orders In Basket Amount' => $data['in_basket']['dc_amount'],


        );
        $this->update($data_to_update, 'no_history');
    }

    function update_orders_in_process_data() {

        $data = array(

            'in_process_paid'     => array(
                'number'    => 0,
                'amount'    => 0,
                'dc_amount' => 0
            ),
            'in_process_not_paid' => array(
                'number'    => 0,
                'amount'    => 0,
                'dc_amount' => 0
            )
        );
        $sql  = sprintf(
            'SELECT `Order Current Dispatch State`,count(*) AS num,ifnull(sum(`Order Total Net Amount`),0) AS amount,ifnull(sum(`Order Total Net Amount`*`Order Currency Exchange`),0) AS dc_amount FROM `Order Dimension` WHERE `Order Store Key`=%d  AND  `Order State` ="InProcess"  AND !`Order To Pay Amount`>0 ',
            $this->id
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {


                $data['in_process_paid']['number']    += $row['num'];
                $data['in_process_paid']['amount']    += $row['amount'];
                $data['in_process_paid']['dc_amount'] += $row['dc_amount'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $sql = sprintf(
            'SELECT `Order Current Dispatch State`,count(*) AS num,ifnull(sum(`Order Total Net Amount`) ,0)AS amount,ifnull(sum(`Order Total Net Amount`*`Order Currency Exchange`),0) AS dc_amount FROM `Order Dimension` WHERE `Order Store Key`=%d  AND `Order State`="InProcess"  AND `Order To Pay Amount`>0  ',
            $this->id
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {


                $data['in_process_not_paid']['number']    += $row['num'];
                $data['in_process_not_paid']['amount']    += $row['amount'];
                $data['in_process_not_paid']['dc_amount'] += $row['dc_amount'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $data_to_update = array(
            'Store Orders In Process Paid Number'        => $data['in_process_paid']['number'],
            'Store Orders In Process Paid Amount'        => $data['in_process_paid']['amount'],
            'Store DC Orders In Process Paid Amount'     => $data['in_process_paid']['dc_amount'],
            'Store Orders In Process Not Paid Number'    => $data['in_process_not_paid']['number'],
            'Store Orders In Process Not Paid Amount'    => $data['in_process_not_paid']['amount'],
            'Store DC Orders In Process Not Paid Amount' => $data['in_process_not_paid']['dc_amount'],


        );
        $this->update($data_to_update, 'no_history');
    }

    function update_orders_in_warehouse_data() {

        $data = array(
            'warehouse' => array(
                'number'    => 0,
                'amount'    => 0,
                'dc_amount' => 0
            ),
            'warehouse_no_alerts' => array(
                'number'    => 0,
                'amount'    => 0,
                'dc_amount' => 0
            ),
            'warehouse_with_alerts' => array(
                'number'    => 0,
                'amount'    => 0,
                'dc_amount' => 0
            ),
        );


        $sql = sprintf(
            "SELECT count(*) AS num,ifnull(sum(`Order Total Net Amount`),0) AS amount,ifnull(sum(`Order Total Net Amount`*`Order Currency Exchange`),0) AS dc_amount FROM `Order Dimension` WHERE `Order Store Key`=%d  AND  `Order State` ='InWarehouse' ",
            $this->id
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {


                $data['warehouse']['number']    = $row['num'];
                $data['warehouse']['amount']    = $row['amount'];
                $data['warehouse']['dc_amount'] = $row['dc_amount'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $sql = sprintf(
            "SELECT count(*) AS num,ifnull(sum(`Order Total Net Amount`),0) AS amount,ifnull(sum(`Order Total Net Amount`*`Order Currency Exchange`),0) AS dc_amount FROM `Order Dimension` WHERE   `Order State` ='InWarehouse' and `Order Delivery Note Alert`='Yes'  "
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {


                $data['warehouse_with_alerts']['number']    = $row['num'];
                $data['warehouse_with_alerts']['amount']    = $row['amount'];

                $data['warehouse_with_alerts']['dc_amount'] = $row['dc_amount'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }

        $data['warehouse_no_alerts']['number'] =$data['warehouse']['number']-$data['warehouse_with_alerts']['number'];
        $data['warehouse_no_alerts']['amount'] =$data['warehouse']['amount']-$data['warehouse_with_alerts']['amount'];

        $data['warehouse_no_alerts']['dc_amount'] =$data['warehouse']['dc_amount']-$data['warehouse_with_alerts']['dc_amount'];




        $data_to_update = array(
            'Store Orders In Warehouse Number'    => $data['warehouse']['number'],
            'Store Orders In Warehouse Amount'    => $data['warehouse']['amount'],
            'Store DC Orders In Warehouse Amount' => $data['warehouse']['dc_amount'],

            'Store Orders In Warehouse No Alerts Number' => $data['warehouse_no_alerts']['number'],
            'Store Orders In Warehouse No Alerts Amount' => $data['warehouse_no_alerts']['amount'],
            'Store DC Orders In Warehouse No Alerts Amount' => $data['warehouse_no_alerts']['dc_amount'],

            'Store Orders In Warehouse With Alerts Number' => $data['warehouse_with_alerts']['number'],
            'Store Orders In Warehouse With Alerts Amount' => $data['warehouse_with_alerts']['amount'],
            'Store DC Orders In Warehouse With Alerts Amount' => $data['warehouse_with_alerts']['dc_amount'],


        );
        $this->update($data_to_update, 'no_history');
    }

    function update_orders_packed_data() {

        $data = array(
            'packed' => array(
                'number'    => 0,
                'amount'    => 0,
                'dc_amount' => 0
            ),
        );


        $sql = sprintf(
            "SELECT count(*) AS num,ifnull(sum(`Order Total Net Amount`),0) AS amount,ifnull(sum(`Order Total Net Amount`*`Order Currency Exchange`),0) AS dc_amount FROM `Order Dimension` WHERE `Order Store Key`=%d  AND `Order State` ='PackedDone'  ",
            $this->id
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {


                $data['packed']['number']    = $row['num'];
                $data['packed']['amount']    = $row['amount'];
                $data['packed']['dc_amount'] = $row['dc_amount'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $data_to_update = array(
            'Store Orders Packed Number'    => $data['packed']['number'],
            'Store Orders Packed Amount'    => $data['packed']['amount'],
            'Store DC Orders Packed Amount' => $data['packed']['dc_amount'],


        );


        $this->update($data_to_update, 'no_history');
    }


    function update_orders_approved_data() {

        $data = array(
            'approved' => array(
                'number'    => 0,
                'amount'    => 0,
                'dc_amount' => 0
            ),
        );


        $sql = sprintf(
            "SELECT count(*) AS num,ifnull(sum(`Order Total Net Amount`),0) AS amount,ifnull(sum(`Order Total Net Amount`*`Order Currency Exchange`),0) AS dc_amount FROM `Order Dimension` WHERE  `Order Store Key`=%d  AND   `Order State` ='Approved' ",
            $this->id
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {


                $data['approved']['number']    = $row['num'];
                $data['approved']['amount'] = $row['amount'];
                $data['approved']['dc_amount'] = $row['dc_amount'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $data_to_update = array(
            'Store Orders Dispatch Approved Number' => $data['approved']['number'],
            'Store Orders Dispatch Approved Amount' => $data['approved']['amount'],
            'Store DC Orders Dispatch Approved Amount' => $data['approved']['dc_amount'],


        );
        $this->update($data_to_update, 'no_history');
    }

    function update_orders_dispatched() {

        $data = array(
            'dispatched' => array(
                'number'    => 0,
                'amount'    => 0,
                'dc_amount' => 0
            ),

        );


        $sql = sprintf(
            "SELECT count(*) AS num,ifnull(sum(`Order Total Net Amount`),0) AS amount,ifnull(sum(`Order Total Net Amount`*`Order Currency Exchange`),0) AS dc_amount FROM `Order Dimension` USE INDEX (StoreState)   WHERE `Order Store Key`=%d  and  `Order State` ='Dispatched' ",
            $this->id
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {


                $data['dispatched']['number']    = $row['num'];
                $data['dispatched']['amount'] = $row['amount'];
                $data['dispatched']['dc_amount'] = $row['dc_amount'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $data_to_update = array(
            'Store Orders Dispatched Number' => $data['dispatched']['number'],
            'Store Orders Dispatched Amount' => $data['dispatched']['amount'],
            'Store DC Orders Dispatched Amount' => $data['dispatched']['dc_amount'],


        );
        $this->update($data_to_update, 'no_history');
    }

    function update_orders_dispatched_today() {

        $data = array(
            'dispatched_today' => array(
                'number'    => 0,
                'amount'    => 0,
                'dc_amount' => 0
            ),


        );


        $sql = sprintf(
            "SELECT count(*) AS num,ifnull(sum(`Order Total Net Amount`),0) AS amount ,ifnull(sum(`Order Total Net Amount`*`Order Currency Exchange`),0) AS dc_amount FROM `Order Dimension` WHERE  `Order Store Key`=%d  AND   `Order State` ='Dispatched' and `Order Dispatched Date`>=%s ",
            $this->id,
            prepare_mysql(gmdate('Y-m-d 00:00:00'))

        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {


                $data['dispatched_today']['number']    = $row['num'];
                $data['dispatched_today']['amount'] = $row['amount'];
                $data['dispatched_today']['dc_amount'] = $row['dc_amount'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $data_to_update = array(
            'Store Orders Dispatched Today Number' => $data['dispatched_today']['number'],
            'Store Orders Dispatched Today Amount' => $data['dispatched_today']['amount'],
            'Store DC Orders Dispatched Today Amount' => $data['dispatched_today']['dc_amount'],


        );
        $this->update($data_to_update, 'no_history');
    }


    function update_orders_cancelled() {

        $data = array(

            'cancelled' => array(
                'number'    => 0,
                'amount'    => 0,
                'dc_amount' => 0
            ),
        );


        $sql = sprintf(
            "SELECT count(*) AS num,ifnull(sum(`Order Total Net Amount`),0) AS amount,ifnull(sum(`Order Total Net Amount`*`Order Currency Exchange`),0) AS dc_amount FROM `Order Dimension` USE INDEX (StoreState)  WHERE  `Order Store Key`=%d  AND   `Order State` ='Cancelled' ",
            $this->id
        );




        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {


                $data['cancelled']['number']    = $row['num'];
                $data['cancelled']['amount'] = $row['amount'];
                $data['cancelled']['dc_amount'] = $row['dc_amount'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $data_to_update = array(

            'Store Orders Cancelled Number' => $data['cancelled']['number'],
            'Store Orders Cancelled Amount' => $data['cancelled']['amount'],
            'Store DC Orders Cancelled Amount' => $data['cancelled']['dc_amount'],

        );
        $this->update($data_to_update, 'no_history');
    }


    function update_previous_years_data() {

        foreach (range(1, 5) as $i) {
            $data_iy_ago = $this->get_sales_data(
                date('Y-01-01 00:00:00', strtotime('-'.$i.' year')), date('Y-01-01 00:00:00', strtotime('-'.($i - 1).' year'))
            );


            $data_to_update = array(
                "Store $i Year Ago Invoiced Discount Amount"    => $data_iy_ago['discount_amount'],
                "Store $i Year Ago Invoiced Amount"             => $data_iy_ago['amount'],
                "Store $i Year Ago Invoices"                    => $data_iy_ago['invoices'],
                "Store $i Year Ago Refunds"                     => $data_iy_ago['refunds'],
                "Store $i Year Ago Replacements"                => $data_iy_ago['replacements'],
                "Store $i Year Ago Delivery Notes"              => $data_iy_ago['deliveries'],
                "Store $i Year Ago Profit"                      => $data_iy_ago['profit'],
                "Store DC $i Year Ago Invoiced Amount"          => $data_iy_ago['dc_amount'],
                "Store DC $i Year Ago Invoiced Discount Amount" => $data_iy_ago['dc_discount_amount'],
                "Store DC $i Year Ago Profit"                   => $data_iy_ago['dc_profit']
            );


            $this->update($data_to_update, 'no_history');
        }

        $this->update(['Store Acc Previous Intervals Updated' => gmdate('Y-m-d H:i:s')], 'no_history');


    }

    function update_previous_quarters_data() {


        include_once 'utils/date_functions.php';

        foreach (range(1, 4) as $i) {
            $dates     = get_previous_quarters_dates($i);
            $dates_1yb = get_previous_quarters_dates($i + 4);


            $sales_data     = $this->get_sales_data(
                $dates['start'], $dates['end']
            );
            $sales_data_1yb = $this->get_sales_data(
                $dates_1yb['start'], $dates_1yb['end']
            );

            $data_to_update = array(
                "Store $i Quarter Ago Invoiced Discount Amount"    => $sales_data['discount_amount'],
                "Store $i Quarter Ago Invoiced Amount"             => $sales_data['amount'],
                "Store $i Quarter Ago Invoices"                    => $sales_data['invoices'],
                "Store $i Quarter Ago Refunds"                     => $sales_data['refunds'],
                "Store $i Quarter Ago Replacements"                => $sales_data['replacements'],
                "Store $i Quarter Ago Delivery Notes"              => $sales_data['deliveries'],
                "Store $i Quarter Ago Profit"                      => $sales_data['profit'],
                "Store DC $i Quarter Ago Invoiced Amount"          => $sales_data['dc_amount'],
                "Store DC $i Quarter Ago Invoiced Discount Amount" => $sales_data['dc_discount_amount'],
                "Store DC $i Quarter Ago Profit"                   => $sales_data['dc_profit'],

                "Store $i Quarter Ago 1YB Invoiced Discount Amount"    => $sales_data_1yb['discount_amount'],
                "Store $i Quarter Ago 1YB Invoiced Amount"             => $sales_data_1yb['amount'],
                "Store $i Quarter Ago 1YB Invoices"                    => $sales_data_1yb['invoices'],
                "Store $i Quarter Ago 1YB Refunds"                     => $sales_data_1yb['refunds'],
                "Store $i Quarter Ago 1YB Replacements"                => $sales_data_1yb['replacements'],
                "Store $i Quarter Ago 1YB Delivery Notes"              => $sales_data_1yb['deliveries'],
                "Store $i Quarter Ago 1YB Profit"                      => $sales_data_1yb['profit'],
                "Store DC $i Quarter Ago 1YB Invoiced Amount"          => $sales_data_1yb['dc_amount'],
                "Store DC $i Quarter Ago 1YB Invoiced Discount Amount" => $sales_data_1yb['dc_discount_amount'],
                "Store DC $i Quarter Ago 1YB Profit"                   => $sales_data_1yb['dc_profit']
            );
            $this->update($data_to_update, 'no_history');
        }

        $this->update(['Store Acc Previous Intervals Updated' => gmdate('Y-m-d H:i:s')], 'no_history');


    }

    function get_from_date($period) {
        return $this->update_sales_from_invoices($period);

    }

    function update_sales_from_invoices($interval, $this_year = true, $last_year = true) {


        include_once 'utils/date_functions.php';
        list($db_interval, $from_date, $to_date, $from_date_1yb, $to_date_1yb) = calculate_interval_dates($this->db, $interval);

        if ($this_year) {

            $sales_data = $this->get_sales_data($from_date, $to_date);


            $data_to_update = array(
                "Store $db_interval Acc Invoiced Discount Amount" => $sales_data['discount_amount'],
                "Store $db_interval Acc Invoiced Amount"          => $sales_data['amount'],
                "Store $db_interval Acc Invoices"                 => $sales_data['invoices'],
                "Store $db_interval Acc Refunds"                  => $sales_data['refunds'],
                "Store $db_interval Acc Replacements"             => $sales_data['replacements'],
                "Store $db_interval Acc Delivery Notes"           => $sales_data['deliveries'],
                "Store $db_interval Acc Profit"                   => $sales_data['profit'],
                "Store $db_interval Acc Customers"                => $sales_data['customers'],
                "Store $db_interval Acc Repeat Customers"         => $sales_data['repeat_customers'],

                "Store DC $db_interval Acc Invoiced Amount"          => $sales_data['dc_amount'],
                "Store DC $db_interval Acc Invoiced Discount Amount" => $sales_data['dc_discount_amount'],
                "Store DC $db_interval Acc Profit"                   => $sales_data['dc_profit']
            );


            $this->update($data_to_update, 'no_history');
        }

        if ($from_date_1yb and $last_year) {


            $sales_data = $this->get_sales_data($from_date_1yb, $to_date_1yb);

            $data_to_update = array(
                "Store $db_interval Acc 1YB Invoiced Discount Amount"    => $sales_data['discount_amount'],
                "Store $db_interval Acc 1YB Invoiced Amount"             => $sales_data['amount'],
                "Store $db_interval Acc 1YB Invoices"                    => $sales_data['invoices'],
                "Store $db_interval Acc 1YB Refunds"                     => $sales_data['refunds'],
                "Store $db_interval Acc 1YB Replacements"                => $sales_data['replacements'],
                "Store $db_interval Acc 1YB Delivery Notes"              => $sales_data['deliveries'],
                "Store $db_interval Acc 1YB Profit"                      => $sales_data['profit'],
                "Store $db_interval Acc 1YB Customers"                   => $sales_data['customers'],
                "Store $db_interval Acc 1YB Repeat Customers"            => $sales_data['repeat_customers'],
                "Store DC $db_interval Acc 1YB Invoiced Amount"          => $sales_data['dc_amount'],
                "Store DC $db_interval Acc 1YB Invoiced Discount Amount" => $sales_data['dc_discount_amount'],
                "Store DC $db_interval Acc 1YB Profit"                   => $sales_data['dc_profit']
            );

            $this->update($data_to_update, 'no_history');


        }


        if (in_array(
            $db_interval, [
                            'Total',
                            'Year To Date',
                            'Quarter To Date',
                            'Week To Date',
                            'Month To Date',
                            'Today'
                        ]
        )) {

            $this->update(['Store Acc To Day Updated' => gmdate('Y-m-d H:i:s')], 'no_history');

        } elseif (in_array(
            $db_interval, [
                            '1 Year',
                            '1 Month',
                            '1 Week',
                            '1 Quarter'
                        ]
        )) {

            $this->update(['Store Acc Ongoing Intervals Updated' => gmdate('Y-m-d H:i:s')], 'no_history');
        } elseif (in_array(
            $db_interval, [
                            'Last Month',
                            'Last Week',
                            'Yesterday',
                            'Last Year'
                        ]
        )) {

            $this->update(['Store Acc Previous Intervals Updated' => gmdate('Y-m-d H:i:s')], 'no_history');
        }

    }

    function update_email_campaign_data() {

        $email_campaigns = 0;
        $sql             = sprintf("SELECT count(*) AS email_campaign FROM `Email Campaign Dimension` WHERE `Email Campaign Store Key`=%d  ", $this->id);

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $email_campaigns = $row['email_campaign'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $this->update(array('Store Email Campaigns' => $email_campaigns), 'no_history');


    }

    function update_newsletter_data() {

    }

    function update_email_reminder_data() {

    }

    function update_deals_data() {

        $deals = 0;

        $sql = sprintf("SELECT count(*) AS num FROM `Deal Dimension` WHERE `Deal Store Key`=%d AND `Deal Status`='Active' ", $this->id);

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $deals = $row['num'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $this->update(array('Store Active Deals' => $deals), 'no_history');


    }

    function get_formatted_email_credentials($type) {

        $credentials = $this->get_email_credentials_data($type);

        $formatted_credentials = '';
        foreach ($credentials as $credential) {
            $formatted_credentials .= ','.$credential['Email Address'];
        }

        $formatted_credentials = preg_replace(
            '/^,/', '', $formatted_credentials
        );

        return $formatted_credentials;


    }


    function post_add_history($history_key, $type = false) {

        if (!$type) {
            $type = 'Changes';
        }

        $sql = sprintf(
            "INSERT INTO  `Store History Bridge` (`Store Key`,`History Key`,`Type`) VALUES (%d,%d,%s)", $this->id, $history_key, prepare_mysql($type)
        );

        $this->db->exec($sql);


    }

    function get_tax_rate() {
        $rate = 0;
        $sql  = sprintf(
            "SELECT `Tax Category Rate` FROM kbase.`Tax Category Dimension` WHERE `Tax Category Code`=%s", prepare_mysql($this->data['Store Tax Category Code'])
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $rate = $row['Tax Category Rate'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        return $rate;
    }

    function get_payment_accounts($type='objects',$filter='') {


        if($filter=='Active'){
            $where=' and `Payment Account Store Status`="Active" ';
        }else{
            $where='';
        }

        $payment_accounts = array();
        $sql                 = sprintf(
            "SELECT PA.`Payment Account Key` FROM `Payment Account Dimension` PA LEFT JOIN `Payment Account Store Bridge` B ON (PA.`Payment Account Key`=B.`Payment Account Store Payment Account Key`)   where `Payment Account Store Store Key`=%d  %s ",
            $this->id,
            $where
        );



        if ($result=$this->db->query($sql)) {
        		foreach ($result as $row) {
                    if($type=='objects'){
                        $payment_accounts[] =get_object('Payment_Account',$row['Payment Account Key']);
                    }else{
                        $payment_accounts[] = $row['Payment Account Key'];
                    }
        		}
        }else {
        		print_r($error_info=$this->db->errorInfo());
        		print "$sql\n";
        		exit;
        }





        return $payment_accounts;

    }


    function cancel_old_orders_in_basket() {
        include_once 'common_natural_language.php';

        if (!$this->data['Cancel Orders In Basket Older Than']) {
            return;
        }

        $date = gmdate(
            'Y-m-d H:i:s', strtotime(
                             sprintf(
                                 "now -%d seconds +0:00", $this->data['Cancel Orders In Basket Older Than']
                             )
                         )
        );

        $sql = sprintf(
            "SELECT `Order Key` FROM `Order Dimension` WHERE  `Order State`='InBasket' AND `Order Store Key`=%d AND `Order Last Updated Date`<%s", $this->id,
            prepare_mysql($date)
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $order         = new Order($row['Order Key']);
                $order->editor = $this->editor;
                $note          = sprintf(
                    _('Order cancelled because has been untouched in the basket for more than %s'), seconds_to_string($this->data['Cancel Orders In Basket Older Than'])
                );


                $order->cancel($note, false, true);
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


    }

    function get_field_label($field) {

        switch ($field) {

            case 'Store Code':
                $label = _('code');
                break;
            case 'Store Name':
                $label = _('name');
                break;
            case 'Store Currency Code':
                $label = _('currency');
                break;
            case 'Store Locale':
                $label = _('language');
                break;
            case 'Store Timezone':
                $label = _('timezone');
                break;
            case 'Store Email':
                $label = _('email');
                break;
            case 'Store Telephone':
                $label = _('telephone');
                break;
            case 'Store Address':
                $label = _('address');
                break;
            case 'Store VAT Number':
                $label = _('VAT number');
                break;
            case 'Store Company Name':
                $label = _('company name');
                break;
            case 'Store Company Number':
                $label = _('company number');
                break;
            case 'Store URL':
                $label = _('website URL');
                break;

            case 'Store Email Template Signature':
                $label = _('Emails signature');
                break;
            case 'Store Invoice Message':
                $label =  _('Invoices signature');
                break;

            case 'Store Collect Address':
                $label = _("Collection address");
                break;
            case 'Store Order Public ID Format':
                $label = _("Order number format");
                break;



            default:
                $label = $field;

        }

        return $label;

    }

    function create_customer($data) {

        $this->new_customer = false;
        $this->new_website_user=false;

        $data['editor']             = $this->editor;
        $data['Customer Store Key'] = $this->id;





        $data['Customer Billing Address Link'] = 'Contact';
        $data['Customer Delivery Address Link'] = 'Billing';


        $address_fields = array(
            'Address Recipient'            => $data['Customer Main Contact Name'],
            'Address Organization'         => $data['Customer Company Name'],
            'Address Line 1'               => '',
            'Address Line 2'               => '',
            'Address Sorting Code'         => '',
            'Address Postal Code'          => '',
            'Address Dependent Locality'   => '',
            'Address Locality'             => '',
            'Address Administrative Area'  => '',
            'Address Country 2 Alpha Code' => $data['Customer Contact Address country'],

        );
        unset($data['Customer Contact Address country']);

        if (isset($data['Customer Contact Address addressLine1'])) {
            $address_fields['Address Line 1'] = $data['Customer Contact Address addressLine1'];
            unset($data['Customer Contact Address addressLine1']);
        }
        if (isset($data['Customer Contact Address addressLine2'])) {
            $address_fields['Address Line 2'] = $data['Customer Contact Address addressLine2'];
            unset($data['Customer Contact Address addressLine2']);
        }
        if (isset($data['Customer Contact Address sortingCode'])) {
            $address_fields['Address Sorting Code'] = $data['Customer Contact Address sortingCode'];
            unset($data['Customer Contact Address sortingCode']);
        }
        if (isset($data['Customer Contact Address postalCode'])) {
            $address_fields['Address Postal Code'] = $data['Customer Contact Address postalCode'];
            unset($data['Customer Contact Address postalCode']);
        }

        if (isset($data['Customer Contact Address dependentLocality'])) {
            $address_fields['Address Dependent Locality'] = $data['Customer Contact Address dependentLocality'];
            unset($data['Customer Contact Address dependentLocality']);
        }

        if (isset($data['Customer Contact Address locality'])) {
            $address_fields['Address Locality'] = $data['Customer Contact Address locality'];
            unset($data['Customer Contact Address locality']);
        }

        if (isset($data['Customer Contact Address administrativeArea'])) {
            $address_fields['Address Administrative Area'] = $data['Customer Contact Address administrativeArea'];
            unset($data['Customer Contact Address administrativeArea']);
        }

        //print_r($address_fields);
        // print_r($data);

        //exit;

        $customer = new Customer('new', $data, $address_fields);
        $website_user=false;
        $website_user_key=false;

        if ($customer->id) {
            $this->new_customer_msg = $customer->msg;

            if ($customer->new) {
                $this->new_customer = true;

                include_once 'utils/new_fork.php';
                global $account;

                if($customer->get('Customer Main Plain Email')!='') {


                    $website = get_object('website', $this->get('Store Website Key'));

                    $user_data['Website User Handle']       = $customer->get('Customer Main Plain Email');
                    $user_data['Website User Customer Key'] = $customer->id;
                    $website_user                           = $website->create_user($user_data);



                    $this->new_customer = true;

                    $this->new_website_user = $website_user->new;
                    $website_user_key=$website_user->id;

                }

                new_housekeeping_fork(
                    'au_housekeeping', array(
                    'type'     => 'customer_created',
                    'customer_key' => $customer->id,
                    'website_user_key' => $website_user_key
                ), $account->get('Account Code')
                );







            } else {
                $this->error = true;
                $this->msg   = $customer->msg;

            }

            return array($customer,$website_user);
        } else {
            $this->error = true;
            $this->msg   = $customer->msg;
        }



    }

    function create_product($data) {


        $this->new_product = false;

        $data['editor'] = $this->editor;


        //print_r($data);

        if (!isset($data['Product Code']) or $data['Product Code'] == '') {
            $this->error      = true;
            $this->msg        = _("Code missing");
            $this->error_code = 'product_code_missing';
            $this->metadata   = '';

            return;
        }

        $sql = sprintf(
            'SELECT count(*) AS num FROM `Product Dimension` WHERE `Product Code`=%s AND `Product Store Key`=%d AND `Product Status`!="Discontinued" ', prepare_mysql($data['Product Code']), $this->id

        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                if ($row['num'] > 0) {
                    $this->error      = true;
                    $this->msg        = sprintf(
                        _('Duplicated code (%s)'), $data['Product Code']
                    );
                    $this->error_code = 'duplicate_product_code_reference';
                    $this->metadata   = $data['Product Code'];

                    return;
                }
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        if (!isset($data['Product Unit Label']) or $data['Product Unit Label'] == '') {


            $this->error      = true;
            $this->msg        = _('Unit label missing');
            $this->error_code = 'product_unit_label_missing';

            return;
        }

        if (!isset($data['Product Name']) or $data['Product Name'] == '') {


            $this->error      = true;
            $this->msg        = _('Product name missing');
            $this->error_code = 'product_name_missing';

            return;
        }


        if (!isset($data['Product Units Per Case']) or $data['Product Units Per Case'] == '') {
            $this->error      = true;
            $this->msg        = _('Units per outer missing');
            $this->error_code = 'product_units_per_case_missing';

            return;
        }

        if (!is_numeric($data['Product Units Per Case']) or $data['Product Units Per Case'] < 0) {
            $this->error      = true;
            $this->msg        = sprintf(
                _('Invalid units per outer (%s)'), $data['Product Units Per Case']
            );
            $this->error_code = 'invalid_product_units_per_case';
            $this->metadata   = $data['Product Units Per Case'];

            return;
        }


        if (!isset($data['Product Price']) or $data['Product Price'] == '') {
            $this->error      = true;
            $this->msg        = _('Cost missing');
            $this->error_code = 'product_price_missing';

            return;
        }

        if (!is_numeric($data['Product Price']) or $data['Product Price'] < 0) {
            $this->error      = true;
            $this->msg        = sprintf(_('Invalid cost (%s)'), $data['Product Price']);
            $this->error_code = 'invalid_product_price';
            $this->metadata   = $data['Product Price'];

            return;
        }


        if (isset($data['Product Unit RRP']) and $data['Product Unit RRP'] != '') {
            if (!is_numeric($data['Product Unit RRP']) or $data['Product Unit RRP'] < 0) {
                $this->error      = true;
                $this->msg        = sprintf(
                    _('Invalid unit recommended RRP (%s)'), $data['Product Unit RRP']
                );
                $this->error_code = 'invalid_product_unit_rrp';
                $this->metadata   = $data['Product Unit RRP'];

                return;
            }
        }
        if ($data['Product Unit RRP'] != '') {
            $data['Product RRP'] = $data['Product Unit RRP'] * $data['Product Units Per Case'];
        } else {
            $data['Product RRP'] = '';
        }

        $data['Product Store Key'] = $this->id;


        $data['Product Currency'] = $this->data['Store Currency Code'];
        $data['Product Locale']   = $this->data['Store Locale'];


        if (array_key_exists('Family Category Code', $data)) {
            include_once 'class.Category.php';
            $root_category = new Category('id', $this->get('Store Family Category Key'), false, $this->db);
            if ($root_category->id) {
                $root_category->editor = $this->editor;
                $family                = $root_category->create_category(array('Category Code' => $data['Family Category Code']));
                if ($family->id) {
                    $data['Product Family Category Key'] = $family->id;

                }
            }
            unset($data['Family Category Code']);
        }

        if (isset($data['Product Family Category Key'])) {
            $family_key = $data['Product Family Category Key'];
            unset($data['Product Family Category Key']);
        } else {
            $family_key = false;
        }


        if (isset($data['Parts']) and $data['Parts'] != '') {

            include_once 'class.Part.php';
            $product_parts = array();
            foreach (preg_split('/\,/', $data['Parts']) as $part_data) {
                $part_data = _trim($part_data);
                if (preg_match('/(\d+)x\s+/', $part_data, $matches)) {

                    $ratio     = $matches[1];
                    $part_data = preg_replace('/(\d+)x\s+/', '', $part_data);
                } else {
                    $ratio = 1;
                }

                $part = new Part(
                    'reference', _trim(
                                   $part_data
                               )
                );

                $product_parts[] = array(
                    'Ratio'    => $ratio,
                    'Part SKU' => $part->id,
                    'Note'     => ''
                );

            }

            $data['Product Parts'] = json_encode($product_parts);
        }


        if (isset($data['Product Parts'])) {

            include_once 'class.Part.php';
            $product_parts = json_decode($data['Product Parts'], true);


            if ($product_parts and is_array($product_parts)) {

                foreach ($product_parts as $product_part) {


                    //   print_r($product_part);


                    if (!is_array($product_part)

                    ) {

                        $this->error      = true;
                        $this->msg        = "Can't parse product parts";
                        $this->error_code = 'can_not_parse_product_parts_no_array';
                        $this->metadata   = '';


                        return;
                    }

                    if (!isset($product_part['Part SKU'])


                    ) {

                        $this->error      = true;
                        $this->msg        = "Can't parse product parts, missing part sku";
                        $this->error_code = 'can_not_parse_product_parts_missing_part_sku';
                        $this->metadata   = '';


                        return;
                    }

                    if (!array_key_exists('Ratio', $product_part)

                    ) {

                        $this->error      = true;
                        $this->msg        = "Can't parse product parts, missing ratio";
                        $this->error_code = 'can_not_parse_product_parts_missing_ratio';
                        $this->metadata   = '';


                        return;
                    }

                    if (!array_key_exists('Note', $product_part)

                    ) {

                        $this->error      = true;
                        $this->msg        = "Can't parse product parts, missing note";
                        $this->error_code = 'can_not_parse_product_parts_missing_note';
                        $this->metadata   = '';


                        return;
                    }

                    if (is_null($product_part['Note'])) {
                        $product_part['Note'] = '';

                    }

                    if (

                    !is_numeric($product_part['Part SKU'])
                    ) {

                        $this->error      = true;
                        $this->msg        = "Can't parse product parts";
                        $this->error_code = 'can_not_parse_product_parts_wrong_part_sku';
                        $this->metadata   = '';


                        return;
                    }

                    if (

                    !is_string($product_part['Note'])
                    ) {

                        $this->error      = true;
                        $this->msg        = "Can't parse product parts";
                        $this->error_code = 'can_not_parse_product_parts_note_is_not_string';
                        $this->metadata   = '';


                        return;
                    }


                    $part = new Part($product_part['Part SKU']);

                    if (!$part->id) {

                        $this->error      = true;
                        $this->msg        = 'Part not found';
                        $this->error_code = 'part_not_found';
                        $this->metadata   = $product_part['Part SKU'];


                        return;

                    }


                    if (!is_numeric($product_part['Ratio']) or $product_part['Ratio'] < 0) {
                        $this->error      = true;
                        $this->msg        = sprintf(
                            _('Invalid parts per product (%s)'), $product_part['Ratio']
                        );
                        $this->error_code = 'invalid_parts_per_product';
                        $this->metadata   = array($product_part['Ratio']);

                        return;

                    }


                }


            } else {
                $this->error      = true;
                $this->msg        = "Can't parse product parts";
                $this->error_code = 'can_not_parse_product_parts';
                $this->metadata   = '';

                return;

            }


            $product_parts_data = $data['Product Parts'];
            unset($data['Product Parts']);

        } else {
            $product_parts_data = false;
        }


        $product = new Product('find', $data, 'create');


        if ($product->id) {


            $this->new_object_msg = $product->msg;

            if ($product->new) {
                $this->new_object  = true;
                $this->new_product = true;

                if ($product_parts_data) {

                    /*

                                        $tmp=json_decode($product_parts_data, true);
                                        print_r($tmp);

                                        foreach($tmp as $_key=>$_tmp_val){
                                            $tmp[$_key]['Key']=$product->id;
                                        }
                                        print_r($tmp);
                                        $product_parts_data=json_encode($tmp);

                    */

                    $product->update_part_list($product_parts_data, 'no_history');


                }


                if ($product->get('Product Number of Parts') == 1) {
                    foreach ($product->get_parts('objects') as $part) {

                        // print_r($part);
                        $part->updated_linked_products();
                    }
                }


                if ($family_key) {
                    $product->update(
                        array('Product Family Category Key' => $family_key), 'no_history'
                    );
                }


                foreach ($this->get_websites('objects') as $website) {
                    $website->create_product_webpage($product->id);
                }

                $this->update_product_data();
                $this->update_new_products();

            } else {

                $this->error = true;
                if ($product->found) {

                    $this->error_code     = 'duplicated_field';
                    $this->error_metadata = json_encode(
                        array($product->duplicated_field)
                    );

                    if ($product->duplicated_field == 'Product Code') {
                        $this->msg = _("Duplicated product code");
                    } else {
                        $this->msg = 'Duplicated '.$product->duplicated_field;
                    }


                } else {
                    $this->msg = $product->msg;
                }
            }

            return $product;
        } else {
            $this->error = true;

            $this->msg = 'Error '.$product->msg;


            $this->error      = true;
            $this->error_code = 'cant create product'.$product->msg;
            $this->metadata   = '';


        }

    }

    function get_websites($scope = 'keys') {


        if ($scope == 'objects') {
            include_once 'class.Website.php';
        }

        $sql = sprintf(
            "SELECT  `Website Key` FROM `Website Dimension` WHERE `Website Store Key`=%d ", $this->id
        );

        $websites = array();

        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {

                if ($scope == 'objects') {
                    $websites[$row['Website Key']] = new Website($row['Website Key']);
                } else {
                    $websites[$row['Website Key']] = $row['Website Key'];
                }


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        return $websites;
    }

    function update_new_products() {

        $new = 0;
        $sql = sprintf(
            'SELECT count(*) AS num FROM `Product Dimension` WHERE  `Product Status` IN ("Active","Discontinuing") AND  `Product Store Key` =%d AND `Product Valid From` >= CURDATE() - INTERVAL 14 DAY',
            $this->id

        );
        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $new = $row['num'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $this->update(array('Store New Products' => $new), 'no_history');

    }

    function create_category($raw_data) {

        if (!isset($raw_data['Category Label']) or $raw_data['Category Label'] == '') {
            $raw_data['Category Label'] = $raw_data['Category Code'];
        }

        $data = array(
            'Category Code'           => $raw_data['Category Code'],
            'Category Label'          => $raw_data['Category Label'],
            'Category Scope'          => 'Product',
            'Category Subject'        => $raw_data['Category Subject'],
            'Category Store Key'      => $this->id,
            'Category Can Have Other' => 'No',
            'Category Locked'         => 'Yes',
            'Category Branch Type'    => 'Root',
            'editor'                  => $this->editor

        );

        $category = new Category('find create', $data);


        if ($category->id) {
            $this->new_category_msg = $category->msg;

            if ($category->new) {
                $this->new_category = true;

            } else {
                $this->error = true;
                $this->msg   = $category->msg;

            }

            return $category;
        } else {
            $this->error = true;
            $this->msg   = $category->msg;
        }

    }

    function create_website($data) {

        include_once 'class.Account.php';
        $account = new Account($this->db);


        $this->new_object = false;

        $data['editor'] = $this->editor;


        $data['Website Store Key'] = $this->id;
        $data['Website Locale']    = $this->get('Store Locale');


        if(!isset($data['Website Theme'])){
            $data['Website Theme'] = 'theme_1';
        }


        $data['Website From'] = gmdate('Y-m-d H:i:s');


        switch ($this->get('Store Type')) {

            case 'B2B':
                $data['Website Type'] = 'EcomB2B';
                break;
            case 'Dropshipping':
                $data['Website Type'] = 'EcomDS';
                break;
            default:
                $data['Website Type'] = 'Ecom';


        }


        $website = new Website('find', $data, 'create');

        if ($website->id) {
            $this->new_object_msg = $website->msg;

            if ($website->new) {
                $this->new_object = true;


                $this->update_field_switcher('Store Website Key', $this->id, 'no_history');


                $this->update_websites_data();

                $account->update_stores_data();

            } else {
                $this->error = true;
                if ($website->found) {

                    $this->error_code     = 'duplicated_field';
                    $this->error_metadata = json_encode(
                        array($website->duplicated_field)
                    );

                    if ($website->duplicated_field == 'Website Code') {
                        $this->msg = _('Duplicated website code');
                    }
                    if ($website->duplicated_field == 'Website URL') {
                        $this->msg = _('Duplicated website URL');
                    } else {
                        $this->msg = _('Duplicated website name');
                    }


                } else {
                    $this->msg = $website->msg;
                }
            }

            return $website;
        } else {
            $this->error = true;
            $this->msg   = $website->msg;
        }
    }

    function update_field_switcher($field, $value, $options = '', $metadata = '') {


        switch ($field) {


            case 'Store Can Collect':

                $this->update_field($field, $value, $options);


                $this->other_fields_updated = array(
                    'Store_Collect_Address' => array(
                        'field'           => 'Store_Collect_Address',
                        'render'          => ($this->get('Store Can Collect') == 'Yes' ? true : false),



                    )
                );

            break;

            case 'Store Collect Address':


                $this->update_address('Collect', json_decode($value, true));

                $sql = sprintf('SELECT `Order Key` FROM `Order Dimension` WHERE  `Order Class`="InProcess"  and `Order For Collection`="Yes"  AND `Order Customer Key`=%d ', $this->id);
                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {
                        $order=get_object('Order',$row['Order Key']);
                        $order->update(array('Order Delivery Address' => $value), $options, array('no_propagate_customer' => true));
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                break;


            case('Store Google Map URL'):


                $doc = new DOMDocument();
                @$doc->loadHTML($value);

                $tags = $doc->getElementsByTagName('iframe');


                if ($tags->length== 1) {

                    foreach ($tags as $tag) {
                        $value = $tag->getAttribute('src');
                        break;
                    }


                }


                $this->update_field('Store Google Map URL', $value);
                break;

            case('Store Sticky Note'):
                $this->update_field_switcher('Sticky Note', $value);
                break;
            case('Sticky Note'):
                $this->update_field('Store '.$field, $value, 'no_null');
                $this->new_value = html_entity_decode($this->new_value);
                break;

            case('Store Code'):
            case('Store Name'):

                if ($value == '') {
                    $this->error = true;
                    $this->msg   = _("Value can't be empty");
                }
                $this->update_field($field, $value, $options);
                break;


            default:
                $base_data = $this->base_data();
                if (array_key_exists($field, $base_data)) {
                    if ($value != $this->data[$field]) {
                        $this->update_field($field, $value, $options);
                    }
                } elseif (array_key_exists($field, $this->base_data('Store Data'))) {
                    $this->update_table_field($field, $value, $options, 'Store', 'Store Data', $this->id);
                } elseif (array_key_exists(
                    $field, $this->base_data('Store DC Data')
                )) {
                    $this->update_table_field(
                        $field, $value, $options, 'Store', 'Store DC Data', $this->id
                    );
                }

        }


    }

    function update_websites_data() {


        $number_sites = 0;
        $sql          = sprintf(
            "SELECT count(*) AS number_sites FROM `Site Dimension` WHERE `Site Store Key`=%d ", $this->id
        );

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $number_sites = $row['number_sites'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }

        $this->update(array('Store Websites' => $number_sites), 'no_history');


    }

    function get_sales_timeseries_sql() {

        $table = '`Order Spanshot Fact` TR ';
        $where = sprintf(' where `Store Key`=%d', $this->id);

        $order  = '`Date`';
        $fields = "`Sales`,`Sales DC`,`Availability`,`Customers`,`Invoices`";

        $sql = "select $fields from $table $where  order by $order ";

        return $sql;

    }


    function update_address($type, $fields, $options = '') {


        $old_value = $this->get("$type Address");
        //$old_checksum = $this->get("$type Address Checksum");


        $updated_fields_number = 0;



        foreach ($fields as $field => $value) {

            $this->update_field(
                $this->table_name.' '.$type.' '.$field, $value, 'no_history'
            );
            if ($this->updated) {
                $updated_fields_number++;

            }
        }


        if ($updated_fields_number > 0) {
            $this->updated = true;
        }


        if ($this->updated) {

            $this->update_address_formatted_fields($type, $options);


            if (!preg_match('/no( |\_)history|nohistory/i', $options)) {

                $this->add_changelog_record(
                    $this->table_name." $type Address", $old_value, $this->get("$type Address"), '', $this->table_name, $this->id
                );

            }





        }

    }

    function update_address_formatted_fields($type, $options) {


        include_once 'utils/get_addressing.php';

        $new_checksum = md5(
            json_encode(
                array(
                    'Address Recipient'            => $this->get($type.' Address Recipient'),
                    'Address Organization'         => $this->get($type.' Address Organization'),
                    'Address Line 1'               => $this->get($type.' Address Line 1'),
                    'Address Line 2'               => $this->get($type.' Address Line 2'),
                    'Address Sorting Code'         => $this->get($type.' Address Sorting Code'),
                    'Address Postal Code'          => $this->get($type.' Address Postal Code'),
                    'Address Dependent Locality'   => $this->get($type.' Address Dependent Locality'),
                    'Address Locality'             => $this->get($type.' Address Locality'),
                    'Address Administrative Area'  => $this->get($type.' Address Administrative Area'),
                    'Address Country 2 Alpha Code' => $this->get($type.' Address Country 2 Alpha Code'),
                )
            )
        );


        $this->update_field(
            $this->table_name.' '.$type.' Address Checksum', $new_checksum, 'no_history'
        );




        $country = $this->get('Store Home Country Code 2 Alpha');
        $locale  = $this->get('Store Locale');

        list($address, $formatter, $postal_label_formatter) = get_address_formatter($country, $locale);


        $address = $address->withFamilyName($this->get($type.' Address Recipient'))->withOrganization($this->get($type.' Address Organization'))->withAddressLine1($this->get($type.' Address Line 1'))
            ->withAddressLine2($this->get($type.' Address Line 2'))->withSortingCode($this->get($type.' Address Sorting Code'))->withPostalCode($this->get($type.' Address Postal Code'))
            ->withDependentLocality($this->get($type.' Address Dependent Locality'))->withLocality($this->get($type.' Address Locality'))->withAdministrativeArea(
                $this->get($type.' Address Administrative Area')
            )->withCountryCode($this->get($type.' Address Country 2 Alpha Code'));


        $xhtml_address = $formatter->format($address);


        if ($this->get($type.' Address Recipient') == $this->get('Main Contact Name')) {
            $xhtml_address = preg_replace('/(class="recipient">.+<\/span>)<br>/', '$1', $xhtml_address);
        }

        if ($this->get($type.' Address Organization') == $this->get('Company Name')) {
            $xhtml_address = preg_replace('/(class="organization">.+<\/span>)<br>/', '$1', $xhtml_address);
        }

        $xhtml_address = preg_replace(
            '/class="recipient"/', 'class="recipient fn '.($this->get($type.' Address Recipient') == $this->get('Main Contact Name') ? 'hide' : '').'"', $xhtml_address
        );


        $xhtml_address = preg_replace('/class="organization"/', 'class="organization org '.($this->get($type.' Address Organization') == $this->get('Company Name') ? 'hide' : '').'"', $xhtml_address);
        $xhtml_address = preg_replace('/class="address-line1"/', 'class="address-line1 street-address"', $xhtml_address);
        $xhtml_address = preg_replace('/class="address-line2"/', 'class="address-line2 extended-address"', $xhtml_address);
        $xhtml_address = preg_replace('/class="sort-code"/', 'class="sort-code postal-code"', $xhtml_address);
        $xhtml_address = preg_replace('/class="country"/', 'class="country country-name"', $xhtml_address);


        $xhtml_address = preg_replace('/(class="address-line1 street-address"><\/span>)<br>/', '$1', $xhtml_address);


        //print $xhtml_address;
        $this->update_field($this->table_name.' '.$type.' Address Formatted', $xhtml_address, 'no_history');
        $this->update_field($this->table_name.' '.$type.' Address Postal Label', $postal_label_formatter->format($address), 'no_history');

    }



}


?>
