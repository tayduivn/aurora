<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 12 August 2016 at 22:03:03 GMT+8, Kuala Lumpur, Malaysia

 Copyright (c) 2016, Inikoo

 Version 3.0
*/

require_once 'utils/date_functions.php';


trait PartCategory {


    function create_part_timeseries($data, $fork_key = 0) {


        if ($this->get('Category Branch Type') == 'Root') {


            if ($fork_key) {


                $sql = sprintf(
                    "UPDATE `Fork Dimension` SET `Fork State`='Finished' ,`Fork Finished Date`=NOW(),`Fork Operations Done`=%d,`Fork Result`=%s WHERE `Fork Key`=%d ", 0, prepare_mysql('0'), $fork_key
                );

                $this->db->exec($sql);

            }

            return;
        }

        $data['editor']     = $this->editor;
        $data['Timeseries Parent']     = 'Category';
        $data['Timeseries Parent Key'] = $this->id;

        $timeseries = new Timeseries('find', $data, 'create');
        if ($timeseries->id) {


            if ($this->data['Part Category Valid From'] != '') {
                $from = date('Y-m-d', strtotime($this->get('Part Category Valid From')));

            } else {
                $from = '';
            }

            if ($this->get('Part Category Status') == 'NotInUse') {
                $to = date('Y-m-d', strtotime($this->get('Part Category Valid To')));
            } else {
                $to = date('Y-m-d');
            }


            $sql = sprintf(
                'DELETE FROM `Timeseries Record Dimension` WHERE `Timeseries Record Timeseries Key`=%d AND `Timeseries Record Date`<%s ', $timeseries->id, prepare_mysql($from)
            );

            $update_sql = $this->db->prepare($sql);
            $update_sql->execute();
            if ($update_sql->rowCount()) {
                $timeseries->fast_update(
                    array('Timeseries Updated' => gmdate('Y-m-d H:i:s'))
                );

            }

            $sql = sprintf(
                'DELETE FROM `Timeseries Record Dimension` WHERE `Timeseries Record Timeseries Key`=%d AND `Timeseries Record Date`>%s ', $timeseries->id, prepare_mysql($to)
            );

            $update_sql = $this->db->prepare($sql);
            $update_sql->execute();
            if ($update_sql->rowCount()) {
                $timeseries->fast_update(
                    array('Timeseries Updated' => gmdate('Y-m-d H:i:s'))
                );

            }


            if ($from and $to) {


                $this->update_part_timeseries_record($timeseries,$from, $to, $fork_key);


            }

            if ($timeseries->get('Timeseries Number Records') == 0) {
                $timeseries->fast_update(
                    array('Timeseries Updated' => gmdate('Y-m-d H:i:s'))
                );
            }

        }

    }


    function update_part_timeseries_record($timeseries,  $from,$to, $fork_key = false) {

        if ($this->get('Category Branch Type') == 'Root') {


            if ($fork_key) {


                $sql = sprintf(
                    "UPDATE `Fork Dimension` SET `Fork State`='Finished' ,`Fork Finished Date`=NOW(),`Fork Operations Done`=%d,`Fork Result`=%d WHERE `Fork Key`=%d ", 0, $timeseries->id, $fork_key
                );

                $this->db->exec($sql);

            }

            return;
        }

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



        $timeseries->fast_update(
            array('Timeseries Updated' => gmdate('Y-m-d H:i:s'))
        );




        $index = 0;
        foreach ($dates as $date_frequency_period) {
            $index++;
            list($sold_amount, $deliveries, $skos) = $this->get_part_timeseries_record_data($timeseries, $date_frequency_period);

            //print_r($date_frequency_period);
            //print "$sold_amount, $deliveries, $skos\n";
            $_date = gmdate(
                'Y-m-d', strtotime($date_frequency_period['from'].' +0:00')
            );

            if ($skos != 0 or $deliveries != 0 or $sold_amount != 0) {
                list($timeseries_record_key, $date) = $timeseries->create_record(
                    array('Timeseries Record Date' => $_date)
                );
                $sql = sprintf(
                    'UPDATE `Timeseries Record Dimension` SET `Timeseries Record Integer A`=%d , `Timeseries Record Integer B`=%d , `Timeseries Record Float A`=%.2f , `Timeseries Record Type`=%s  WHERE `Timeseries Record Key`=%d',
                    $deliveries, $skos, $sold_amount, prepare_mysql('Data'), $timeseries_record_key

                );
                
                //print $sql."\n";

                $update_sql = $this->db->prepare($sql);
                $update_sql->execute();

                if ($update_sql->rowCount() or $date == date('Y-m-d')) {
                    $timeseries->fast_update(
                        array('Timeseries Updated' => gmdate('Y-m-d H:i:s'))
                    );
                }

            } else {
                $sql = sprintf(
                    'DELETE FROM `Timeseries Record Dimension` WHERE `Timeseries Record Timeseries Key`=%d AND `Timeseries Record Date`=%s ', $timeseries->id, prepare_mysql($_date)
                );

                $update_sql = $this->db->prepare($sql);
                $update_sql->execute();
                if ($update_sql->rowCount()) {
                    $timeseries->fast_update(
                        array('Timeseries Updated' => gmdate('Y-m-d H:i:s'))
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



            $date = gmdate('Y-m-d H:i:s');
            $sql = 'insert into `Stack Dimension` (`Stack Creation Date`,`Stack Last Update Date`,`Stack Operation`,`Stack Object Key`) values (?,?,?,?) ON DUPLICATE KEY UPDATE `Stack Last Update Date`=? ,`Stack Counter`=`Stack Counter`+1 ';
            $this->db->prepare($sql)->execute(
                [
                    $date,
                    $date,
                    'timeseries_stats',
                    $timeseries->id,
                    $date,

                ]
            );



        }




        if ($fork_key) {

            $sql = sprintf(
                "UPDATE `Fork Dimension` SET `Fork State`='Finished' ,`Fork Finished Date`=NOW(),`Fork Operations Done`=%d,`Fork Result`=%d WHERE `Fork Key`=%d ", $index, $timeseries->id, $fork_key
            );

            $this->db->exec($sql);

        }

    }

    function get_part_timeseries_record_data($timeseries, $date_frequency_period) {


        /*

        $part_skus = $this->get_part_skus();

        $subject_type = $this->get('Category Subject');

        if ($subject_type == 'Category') {
            $category_ids = $part_skus;
            $part_skus    = '';
            $sql          = sprintf(
                'SELECT `Subject Key` ,`Subject` FROM `Category Bridge` WHERE `Category Key` IN (%s) AND `Subject Key`>0 ', $category_ids
            );
            if ($result = $this->db->query($sql)) {
                foreach ($result as $row) {
                    $part_skus .= $row['Subject Key'].',';
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }
            $part_skus = preg_replace('/\,$/', '', $part_skus);

        }


        if ($part_skus == '') {
            return array(
                0,
                0,
                0
            );
        }

        */

        if ($timeseries->get('Timeseries Scope') == 'Sales') {


            $sales_data= $this->get_part_category_sales_data($date_frequency_period['from'],$date_frequency_period['to']);

            return array(
                $sales_data['invoiced_amount'],
                $sales_data['deliveries'],
                $sales_data['dispatched'],
            );


            /*

            $sql = sprintf(
                "SELECT count(DISTINCT `Delivery Note Key`)  AS deliveries,sum(`Amount In`) net,sum(`Inventory Transaction Quantity`) skos FROM `Inventory Transaction Fact` WHERE `Part SKU` IN (%s) AND `Inventory Transaction Type`='Sale' AND  `Date`>=%s  AND   `Date`<=%s  ",
                $part_skus, prepare_mysql($date_frequency_period['from']), prepare_mysql($date_frequency_period['to'])
            );

            print "$sql\n";

            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {


                    $deliveries = $row['deliveries'];
                    $skos       = round(-1 * $row['skos']);
                    $net        = $row['net'];


                    print_r($row);


                } else {
                    $deliveries = 0;
                    $skos       = 0;
                    $net        = 0;
                }

                return array(
                    $net,
                    $deliveries,
                    $skos
                );

            } else {
                print_r($error_info = $this->db->errorInfo());
                print "$sql\n";
                exit;
            }




            */

        }


    }

    function get_part_skus() {

        $part_skus    = '';
        $sql          = sprintf(
            'SELECT `Subject Key`,`Subject` FROM `Category Bridge` WHERE `Category Key`=%d AND `Subject Key`>0 ', $this->id
        );
        $part_skus    = '';
        $subject_type = '';
        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $part_skus .= $row['Subject Key'].',';
                $subject_type = $row['Subject'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }
        $part_skus = preg_replace('/\,$/', '', $part_skus);

        return $part_skus;

    }


    function update_part_category_sales($interval, $this_year = true, $last_year = true) {


        include_once 'utils/date_functions.php';

        list($db_interval, $from_date, $to_date, $from_date_1yb, $to_1yb) = calculate_interval_dates($this->db, $interval);

        if ($this_year) {

            $sales_data = $this->get_part_category_sales_data(
                $from_date, $to_date
            );

            $data_to_update = array(
                "Part Category $db_interval Acc Customers"        => $sales_data['customers'],
                "Part Category $db_interval Acc Repeat Customers" => $sales_data['repeat_customers'],
                "Part Category $db_interval Acc Deliveries"       => $sales_data['deliveries'],
                "Part Category $db_interval Acc Profit"           => $sales_data['profit'],
                "Part Category $db_interval Acc Invoiced Amount"  => $sales_data['invoiced_amount'],
                "Part Category $db_interval Acc Required"         => $sales_data['required'],
                "Part Category $db_interval Acc Dispatched"       => $sales_data['dispatched'],
                "Part Category $db_interval Acc Keeping Days"     => $sales_data['keep_days'],
                "Part Category $db_interval Acc With Stock Days"  => $sales_data['with_stock_days'],
            );


            $this->fast_update($data_to_update, 'Part Category Data');
        }
        if ($from_date_1yb and $last_year) {


            $sales_data = $this->get_part_category_sales_data(
                $from_date_1yb, $to_1yb
            );


            $data_to_update = array(

                "Part Category $db_interval Acc 1YB Customers"        => $sales_data['customers'],
                "Part Category $db_interval Acc 1YB Repeat Customers" => $sales_data['repeat_customers'],
                "Part Category $db_interval Acc 1YB Deliveries"       => $sales_data['deliveries'],
                "Part Category $db_interval Acc 1YB Profit"           => $sales_data['profit'],
                "Part Category $db_interval Acc 1YB Invoiced Amount"  => $sales_data['invoiced_amount'],
                "Part Category $db_interval Acc 1YB Required"         => $sales_data['required'],
                "Part Category $db_interval Acc 1YB Dispatched"       => $sales_data['dispatched'],
                "Part Category $db_interval Acc 1YB Keeping Days"     => $sales_data['keep_days'],
                "Part Category $db_interval Acc 1YB With Stock Days"  => $sales_data['with_stock_days'],

            );
            $this->fast_update($data_to_update, 'Part Category Data');


        }

        //  print "$db_interval";

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

            $this->fast_update(['Part Category Acc To Day Updated' => gmdate('Y-m-d H:i:s')], 'Part Category Dimension');

        } elseif (in_array(
            $db_interval, [
                            '1 Year',
                            '1 Month',
                            '1 Week',
                            '1 Quarter'
                        ]
        )) {

            $this->fast_update(['Part Category Acc Ongoing Intervals Updated' => gmdate('Y-m-d H:i:s')], 'Part Category Dimension');
        } elseif (in_array(
            $db_interval, [
                            'Last Month',
                            'Last Week',
                            'Yesterday',
                            'Last Year'
                        ]
        )) {

            $this->fast_update(['Part Category Acc Previous Intervals Updated' => gmdate('Y-m-d H:i:s')], 'Part Category Dimension');
        }


    }

    function get_part_category_sales_data($from_date, $to_date) {

        $sales_data = array(
            'invoiced_amount'  => 0,
            'profit'           => 0,
            'required'         => 0,
            'dispatched'       => 0,
            'deliveries'       => 0,
            'customers'        => 0,
            'repeat_customers' => 0,
            'keep_days'        => 0,
            'with_stock_days'  => 0,

        );

        $part_skus = $this->get_part_skus();

        if ($part_skus != '' and $this->get('Category Branch Type') != 'Root') {


            if ($from_date == '' and $to_date == '') {
                //$sales_data['repeat_customers']=$this->get_part_category_customers_total_data($part_skus);
            }

            $sql = sprintf(
                "SELECT count( DISTINCT DN.`Delivery Note Customer Key`) AS customers, count( DISTINCT ITF.`Delivery Note Key`) AS deliveries, round(ifnull(sum(`Amount In`),0),2) AS invoiced_amount,round(ifnull(sum(`Amount In`+`Inventory Transaction Amount`),0),2) AS profit,round(ifnull(sum(`Inventory Transaction Quantity`),0),1) AS dispatched,round(ifnull(sum(`Required`),0),1) AS required FROM `Inventory Transaction Fact` ITF LEFT JOIN `Delivery Note Dimension` DN ON (ITF.`Delivery Note Key`=DN.`Delivery Note Key`) WHERE `Inventory Transaction Type` LIKE 'Sale' AND `Part SKU` IN (%s) %s %s",
                $part_skus,
                    ($from_date ? sprintf('and  `Date`>=%s', prepare_mysql($from_date)) : ''),
                    ($to_date ? sprintf('and `Date`<%s', prepare_mysql($to_date)) : '')
            );

          //  print "$sql\n";

            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $sales_data['invoiced_amount'] = $row['invoiced_amount'];
                    $sales_data['profit']          = $row['profit'];
                    $sales_data['dispatched']      = -1.0 * $row['dispatched'];
                    $sales_data['required']        = $row['required'];
                    $sales_data['deliveries']      = $row['deliveries'];
                    $sales_data['customers']       = $row['customers'];
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }
        }



        return $sales_data;


    }


    function get_subcategories_status_numbers($options = '') {

        $elements_numbers = array(
            'InUse'    => 0,
            'NotInUse' => 0
        );

        $sql = sprintf(
            "SELECT count(*) AS num ,`Part Category Status` FROM  `Part Category Dimension` P LEFT JOIN `Category Dimension` C ON (C.`Category Key`=P.`Part Category Key`)  WHERE `Category Parent Key`=%d  GROUP BY  `Part Category Status`   ",
            $this->id
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                if ($options == 'Formatted') {
                    $elements_numbers[$row['Part Category Status']] = number(
                        $row['num']
                    );

                } else {
                    $elements_numbers[$row['Part Category Status']] = $row['num'];
                }
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }

        return $elements_numbers;

    }


    function update_part_category_status() {

        $current_status = $this->data['Part Category Status'];

        $total            = 0;
        $elements_numbers = array(
            'In Process'    => 0,
            'Not In Use'    => 0,
            'In Use'        => 0,
            'Discontinuing' => 0
        );

        $sql = sprintf(
            "SELECT count(*) AS num ,`Part Status` FROM  `Part Dimension` P LEFT JOIN `Category Bridge` B ON (P.`Part SKU`=B.`Subject Key`)  WHERE B.`Category Key`=%d AND `Subject`='Part' GROUP BY  `Part Status`   ",
            $this->id
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $elements_numbers[$row['Part Status']] = $row['num'];
                $total += $row['num'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        if ($total == 0) {
            $status = 'InProcess';
        } elseif ($elements_numbers['In Use'] > 0) {
            $status = 'InUse';
        } else {

            if ($elements_numbers['Not In Use'] == $total) {
                $status = 'NotInUse';


            } elseif ($elements_numbers['Discontinuing'] == $total) {
                $status = 'Discontinuing';
            } elseif ($elements_numbers['In Process'] > 0) {
                $status = 'InProcess';
            } else {
                $status = 'Discontinuing';
            }

        }


        $this->fast_update(
            array(
                'Part Category In Process'    => $elements_numbers['In Process'],
                'Part Category Active'        => $elements_numbers['In Use'],
                'Part Category Discontinuing' => $elements_numbers['Discontinuing'],
                'Part Category Discontinued'  => $elements_numbers['Not In Use'],
                'Part Category Status'        => $status
            ), 'Part Category Dimension'
        );


        if ($status == 'NotInUse') {

            if ($this->data['Part Category Valid To'] == '' or $current_status != $status) {
                $this->fast_update(
                    array(
                        'Part Category Valid To' => gmdate('Y-m-d H:i:s')
                    ), 'Part Category Dimension'
                );

            }

        } else {
            $this->fast_update(
                array(
                    'Part Category Valid To' => ''
                ), 'Part Category Dimension'
            );
        }
        $this->fork_index_elastic_search();


    }

    function update_part_stock_status() {


        $elements_numbers = array(
            'Surplus'      => 0,
            'Optimal'      => 0,
            'Low'          => 0,
            'Critical'     => 0,
            'Out_Of_Stock' => 0,
            'Error'        => 0
        );

        $sql = sprintf(
            "SELECT count(*) AS num ,`Part Stock Status` FROM  `Part Dimension` P LEFT JOIN `Category Bridge` B ON (P.`Part SKU`=B.`Subject Key`)  WHERE B.`Category Key`=%d AND `Subject`='Part' GROUP BY  `Part Stock Status`   ",
            $this->id
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $elements_numbers[$row['Part Stock Status']] = number(
                    $row['num']
                );
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $sql = sprintf(
            "UPDATE `Part Category Dimension` SET `Part Category Number Surplus Parts`=%d ,`Part Category Number Optimal Parts`=%d ,`Part Category Number Low Parts`=%d ,`Part Category Number Critical Parts`=%d ,`Part Category Number Out Of Stock Parts`=%d ,`Part Category Number Error Parts`=%d  WHERE `Part Category Key`=%d",
            $elements_numbers['Surplus'], $elements_numbers['Optimal'], $elements_numbers['Low'], $elements_numbers['Critical'], $elements_numbers['Out_Of_Stock'], $elements_numbers['Error'],
            $this->id
        );
        $this->db->exec($sql);


    }

    function get_part_category_customers_total_data($part_skus) {

        $repeat_customers = 0;


        $sql = sprintf(
            'SELECT count(`Customer Part Customer Key`) AS num  FROM `Customer Part Bridge` WHERE `Customer Part Delivery Notes`>1 AND `Customer Part Part SKU` IN (%s)    ', $part_skus
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $repeat_customers = $row['num'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        return $repeat_customers;

    }

    function update_part_category_previous_years_data() {


        foreach (range(1, 5) as $i) {
            $data_iy_ago = $this->get_part_category_sales_data(
                date('Y-01-01 00:00:00', strtotime('-'.$i.' year')), date('Y-01-01 00:00:00', strtotime('-'.($i - 1).' year'))
            );


            $data_to_update = array(
                "Part Category $i Year Ago Customers"        => $data_iy_ago['customers'],
                "Part Category $i Year Ago Repeat Customers" => $data_iy_ago['repeat_customers'],
                "Part Category $i Year Ago Deliveries"       => $data_iy_ago['deliveries'],
                "Part Category $i Year Ago Profit"           => $data_iy_ago['profit'],
                "Part Category $i Year Ago Invoiced Amount"  => $data_iy_ago['invoiced_amount'],
                "Part Category $i Year Ago Required"         => $data_iy_ago['required'],
                "Part Category $i Year Ago Dispatched"       => $data_iy_ago['dispatched'],
                "Part Category $i Year Ago Keeping Days"      => $data_iy_ago['keep_days'],
                "Part Category $i Year Ago With Stock Days"  => $data_iy_ago['with_stock_days'],
            );


            $this->fast_update($data_to_update, 'Part Category Data');
        }



        $this->fast_update(['Part Category Acc Previous Intervals Updated' => gmdate('Y-m-d H:i:s')], 'Part Category Dimension');


    }


    function update_part_category_previous_quarters_data() {


        include_once 'utils/date_functions.php';


        foreach (range(1, 4) as $i) {
            $dates     = get_previous_quarters_dates($i);
            $dates_1yb = get_previous_quarters_dates($i + 4);


            $sales_data     = $this->get_part_category_sales_data(
                $dates['start'], $dates['end']
            );
            $sales_data_1yb = $this->get_part_category_sales_data(
                $dates_1yb['start'], $dates_1yb['end']
            );

            $data_to_update = array(
                "Part Category $i Quarter Ago Customers"        => $sales_data['customers'],
                "Part Category $i Quarter Ago Repeat Customers" => $sales_data['repeat_customers'],
                "Part Category $i Quarter Ago Deliveries"       => $sales_data['deliveries'],
                "Part Category $i Quarter Ago Profit"           => $sales_data['profit'],
                "Part Category $i Quarter Ago Invoiced Amount"  => $sales_data['invoiced_amount'],
                "Part Category $i Quarter Ago Required"         => $sales_data['required'],
                "Part Category $i Quarter Ago Dispatched"       => $sales_data['dispatched'],
                "Part Category $i Quarter Ago Keeping Days"      => $sales_data['keep_days'],
                "Part Category $i Quarter Ago With Stock Days"  => $sales_data['with_stock_days'],

                "Part Category $i Quarter Ago 1YB Customers"        => $sales_data_1yb['customers'],
                "Part Category $i Quarter Ago 1YB Repeat Customers" => $sales_data_1yb['repeat_customers'],
                "Part Category $i Quarter Ago 1YB Deliveries"       => $sales_data_1yb['deliveries'],
                "Part Category $i Quarter Ago 1YB Profit"           => $sales_data_1yb['profit'],
                "Part Category $i Quarter Ago 1YB Invoiced Amount"  => $sales_data_1yb['invoiced_amount'],
                "Part Category $i Quarter Ago 1YB Required"         => $sales_data_1yb['required'],
                "Part Category $i Quarter Ago 1YB Dispatched"       => $sales_data_1yb['dispatched'],
                "Part Category $i Quarter Ago 1YB Keeping Days"      => $sales_data_1yb['keep_days'],
                "Part Category $i Quarter Ago 1YB With Stock Days"  => $sales_data_1yb['with_stock_days'],
            );
            $this->fast_update($data_to_update, 'Part Category Data');
        }

        $this->fast_update(['Part Category Acc Previous Intervals Updated' => gmdate('Y-m-d H:i:s')], 'Part Category Dimension');


    }


}


