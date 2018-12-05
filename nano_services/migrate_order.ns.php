<?php

/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 5 December 2018 at 01:48:40 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3

*/


class migrate_order
{
    public function __construct($db) {
        $this->db=$db;

        require_once 'utils/get_addressing.php';
        require_once 'utils/parse_natural_language.php';
        require_once 'utils/object_functions.php';
        include_once 'class.Billing_To.php';
        include_once 'class.Ship_To.php';
    }

    public function migrate($order_key) {

        $order = get_object('Order',$order_key);

        if($order->id){

            $store           = get_object('Store',$order->get('Store Key'));

            if($store->get('Store Version')!=1){
                return $order;
            }


            $data_to_update = array();


            if (!$order->get('Order Delivery Note Key')) {

                $sql = sprintf(
                    "select DN.`Delivery Note Key` from `Order Transaction Fact` OTF  left join `Delivery Note Dimension` DN on (DN.`Delivery Note Key`=OTF.`Delivery Note Key`) where `Delivery Note Type`='Order' and `Order Key`=%d and  OTF.`Delivery Note Key`>0", $order->id
                );


                if ($result2 = $this->db->query($sql)) {
                    if ($row2 = $result2->fetch()) {
                        $dn = get_object('DeliveryNote', $row2['Delivery Note Key']);
                        if ($dn->id) {
                            $data_to_update['Order Delivery Note Key'] = $dn->id;

                        }

                    }
                }
            }


            if (!$order->get('Order Invoice Key')) {
                $sql = sprintf("select DN.`Invoice Key` from `Order Transaction Fact` OTF  left join `Invoice Dimension` DN on (DN.`Invoice Key`=OTF.`Invoice Key`) where `Invoice Type`='Invoice' and `Order Key`=%d and  OTF.`Invoice Key`>0", $order->id);

                if ($result2 = $this->db->query($sql)) {
                    if ($row2 = $result2->fetch()) {
                        $invoice = get_object('Invoice', $row2['Invoice Key']);
                        if ($invoice->id) {
                            $data_to_update['Order Invoice Key'] = $invoice->id;

                        }

                    }
                }
            }


            $recipient    = $order->get('Order Customer Contact Name');
            $organization = $order->get('Order Customer Name');


            if ($organization == $recipient) {
                $organization = '';
            }


            $default_country = $store->get('Store Home Country Code 2 Alpha');

            $address_billing_fields_to_update = $this->parse_old_order_billing_address_fields($store, $order->get('Order Billing To Key To Bill'), $recipient, $organization, $default_country);


            $ship_to = new Ship_To($order->get('Order Ship To Key To Deliver'));


            if($ship_to->id){

                $recipient    = $ship_to->get('Ship To Contact Name');
                $organization = $ship_to->get('Ship To Company Name');


                if ($organization == $recipient) {
                    $organization = '';
                }

                $address_shipping_fields_to_update = $this->parse_old_order_shipping_address_fields($store, $ship_to, $recipient, $organization, $default_country);





                //print_r($data_to_update);
                //print_r($address_billing_fields_to_update);
                // print_r($address_shipping_fields_to_update);

                $order->fast_update($data_to_update);
                $order->fast_update($address_billing_fields_to_update);
                $order->fast_update($address_shipping_fields_to_update);
                $order->get_data('id',$order->id);
            }
        }




        return $order;


    }



    private function parse_old_order_billing_address_fields($store, $address_key, $recipient, $organization, $default_country) {


        $address = new Billing_To($address_key);
        if ($address->id > 0) {


            if($address->data['Billing To Country 2 Alpha Code'] == 'XX' or $address->data['Billing To Country 2 Alpha Code'] == '' ){
                $address->data['Billing To Country 2 Alpha Code'] =$default_country;
            }


            $address_format = get_address_format(
                (  ( $address->data['Billing To Country 2 Alpha Code'] == 'XX' or $address->data['Billing To Country 2 Alpha Code'] == '' )  ? $default_country : $address->data['Billing To Country 2 Alpha Code'])
            );


            $_tmp = preg_replace('/,/', '', $address_format->getFormat());

            $used_fields = preg_split('/\s+/', preg_replace('/%/', '', $_tmp));


            $address_fields = array(
                'Address Recipient'            => $recipient,
                'Address Organization'         => $organization,
                'Address Line 1'               => $address->get('Billing To Line 1'),
                'Address Line 2'               => $address->get('Billing To Line 2'),
                'Address Sorting Code'         => '',
                'Address Postal Code'          => $address->get('Billing To Postal Code'),
                'Address Dependent Locality'   => $address->get('Billing To Line 3'),
                'Address Locality'             => $address->get('Billing To Town'),
                'Address Administrative Area'  => $address->get('Billing To Line 4'),
                'Address Country 2 Alpha Code' => ($address->data['Billing To Country 2 Alpha Code'] == 'XX' ? $default_country : $address->data['Billing To Country 2 Alpha Code']),

            );
            //print_r($used_fields);

            //if (!in_array('recipient', $used_fields) or !in_array('organization', $used_fields) or !in_array('addressLine1', $used_fields)) {
            ////    print_r($used_fields);
            //    print_r($address->data);
            //    exit('no recipient or organization');
            // }

            if (!in_array('addressLine2', $used_fields)) {

                if ($address_fields['Address Line 2'] != '') {
                    $address_fields['Address Line 1'] .= ', '.$address_fields['Address Line 2'];
                }
                $address_fields['Address Line 2'] = '';
            }

            if (!in_array('dependentLocality', $used_fields)) {

                if ($address_fields['Address Line 2'] == '') {
                    $address_fields['Address Line 2'] = $address_fields['Address Dependent Locality'];
                } else {
                    $address_fields['Address Line 2'] .= ', '.$address_fields['Address Dependent Locality'];
                }

                $address_fields['Address Dependent Locality'] = '';
            }

            if (!in_array('administrativeArea', $used_fields) and $address->get('Billing To Line 4') != '') {
                $address_fields['Address Administrative Area'] = '';
                //print_r($address->data);
                //print_r($address_fields);

                //print $address->display();


                //exit;

                //print_r($used_fields);
                //print_r($address->data);
                //exit('administrativeArea problem');

            }

            if (!in_array('postalCode', $used_fields) and $address->display('Billing To Postal Code') != '') {

                if (in_array('sortingCode', $used_fields)) {
                    $address_fields['Address Sorting Code'] = $address_fields['Address Postal Code'];
                    $address_fields['Address Postal Code']  = '';

                } else {
                    if (in_array('addressLine2', $used_fields)) {
                        $address_fields['Address Line 2']      .= trim(
                            ' '.$address_fields['Address Postal Code']
                        );
                        $address_fields['Address Postal Code'] = '';
                    }


                    /*
                    print_r($used_fields);
                    print_r($address->data);
                    print_r($address_fields);

                    print $address->display();


                    exit("\nError2\n");
                    */
                }

            }

            if (!in_array('locality', $used_fields) and ($address->get(
                        'Billing To Town'
                    ) != '' or $address->get('Billing To Line 4') != '')) {


                //$address_fields['Address Locality']='';
                //$address_fields['Address Dependent Locality']='';

                if (in_array('addressLine2', $used_fields)) {

                    if ($address_fields['Address Line 1'] == '' and $address_fields['Address Line 2'] == '') {
                        $address_fields['Address Line 1'] .= $address_fields['Address Dependent Locality'];
                        $address_fields['Address Line 2'] .= $address_fields['Address Locality'];

                    } elseif ($address_fields['Address Line 1'] != '' and $address_fields['Address Line 2'] == '') {
                        $address_fields['Address Line 2'] = preg_replace(
                            '/^, /', '', $address_fields['Address Dependent Locality'].', '.$address_fields['Address Locality']
                        );

                    } else {
                        $address_fields['Address Line 2'] = preg_replace(
                            '/^, /', '', $address_fields['Address Dependent Locality'].', '.$address_fields['Address Locality']
                        );

                    }
                } else {

                    print_r($used_fields);
                    print_r($address->data);
                    print_r($address_fields);

                    print $address->display();


                    exit("Error3\n");

                }


            }


        } else {


            $address_format = get_address_format($default_country);


            $address_fields = array(
                'Address Recipient'            => $recipient,
                'Address Organization'         => $organization,
                'Address Line 1'               => '',
                'Address Line 2'               => '',
                'Address Sorting Code'         => '',
                'Address Postal Code'          => '',
                'Address Dependent Locality'   => '',
                'Address Locality'             => '',
                'Address Administrative Area'  => '',
                'Address Country 2 Alpha Code' => $default_country,

            );

        }

        array_walk($address_fields,array($this, 'trim_value'));


        if (preg_match('/gb|im|jy|gg/i', $address_fields['Address Country 2 Alpha Code'])) {
            include_once 'utils/geography_functions.php';
            $address_fields['Address Postal Code']=gbr_pretty_format_post_code($address_fields['Address Postal Code']);
        }


        $_address_fields = array();
        foreach ($address_fields as $key => $value) {
            $_address_fields['Order Invoice '.$key] = $value;
        }


        $new_checksum = md5(
            json_encode(
                array(
                    'Address Recipient'            => $_address_fields['Order Invoice Address Recipient'],
                    'Address Organization'         => $_address_fields['Order Invoice Address Organization'],
                    'Address Line 1'               => $_address_fields['Order Invoice Address Line 1'],
                    'Address Line 2'               => $_address_fields['Order Invoice Address Line 2'],
                    'Address Sorting Code'         => $_address_fields['Order Invoice Address Sorting Code'],
                    'Address Postal Code'          => $_address_fields['Order Invoice Address Postal Code'],
                    'Address Dependent Locality'   => $_address_fields['Order Invoice Address Dependent Locality'],
                    'Address Locality'             => $_address_fields['Order Invoice Address Locality'],
                    'Address Administrative Area'  => $_address_fields['Order Invoice Address Administrative Area'],
                    'Address Country 2 Alpha Code' => $_address_fields['Order Invoice Address Country 2 Alpha Code'],
                )
            )
        );

        $_address_fields['Order Invoice Address Checksum'] = $new_checksum;


        $account = get_object('Account', 1);
        $country = $account->get('Account Country 2 Alpha Code');
        $locale  = $store->get('Store Locale');


        list($address, $formatter, $postal_label_formatter) = get_address_formatter($country, $locale);


        $address = $address->withFamilyName($_address_fields['Order Invoice Address Recipient'])->withOrganization($_address_fields['Order Invoice Address Organization'])->withAddressLine1($_address_fields['Order Invoice Address Line 1'])->withAddressLine2(
            $_address_fields['Order Invoice Address Line 2']
        )->withSortingCode(
            $_address_fields['Order Invoice Address Sorting Code']
        )->withPostalCode($_address_fields['Order Invoice Address Postal Code'])->withDependentLocality(
            $_address_fields['Order Invoice Address Dependent Locality']
        )->withLocality($_address_fields['Order Invoice Address Locality'])->withAdministrativeArea(
            $_address_fields['Order Invoice Address Administrative Area']
        )->withCountryCode(
            $_address_fields['Order Invoice Address Country 2 Alpha Code']
        );


        $xhtml_address = $formatter->format($address);


        $xhtml_address = preg_replace('/class="address-line1"/', 'class="address-line1 street-address"', $xhtml_address);
        $xhtml_address = preg_replace('/class="address-line2"/', 'class="address-line2 extended-address"', $xhtml_address);
        $xhtml_address = preg_replace('/class="sort-code"/', 'class="sort-code postal-code"', $xhtml_address);
        $xhtml_address = preg_replace('/class="country"/', 'class="country country-name"', $xhtml_address);


        $xhtml_address = preg_replace('/(class="address-line1 street-address"><\/span>)<br>/', '$1', $xhtml_address);

        //  print $xhtml_address;

        $_address_fields['Order Invoice Address Formatted'] = $xhtml_address;
        /*
            $account=get_object('Account',1);
            $country = $account->get('Account Country 2 Alpha Code');
            $country = $store->get('Store Home Country Code 2 Alpha');

            $locale  = $store->get('Store Locale');



            list($address, $formatter, $postal_label_formatter) = get_address_formatter($country, $locale);
        */


        $_address_fields['Order Invoice Address Postal Label'] = $postal_label_formatter->format($address);


        //print "\n".$customer->id."\n";
        //print_r($address_fields);

        return $_address_fields;
    }




    private function parse_old_order_shipping_address_fields($store, $address, $recipient, $organization, $default_country) {



        if ($address->id > 0) {


            if($address->data['Ship To Country 2 Alpha Code'] == 'XX' or $address->data['Ship To Country 2 Alpha Code'] == '' ){
                $address->data['Ship To Country 2 Alpha Code'] =$default_country;
            }


            $address_format = get_address_format(
                ( ($address->data['Ship To Country 2 Alpha Code'] == 'XX' or  $address->data['Ship To Country 2 Alpha Code'] == '') ? $default_country : $address->data['Ship To Country 2 Alpha Code'])
            );

            $_tmp = preg_replace('/,/', '', $address_format->getFormat());

            $used_fields = preg_split('/\s+/', preg_replace('/%/', '', $_tmp));


            $address_fields = array(
                'Address Recipient'            => $recipient,
                'Address Organization'         => $organization,
                'Address Line 1'               => $address->get('Ship To Line 1'),
                'Address Line 2'               => $address->get('Ship To Line 2'),
                'Address Sorting Code'         => '',
                'Address Postal Code'          => $address->get('Ship To Postal Code'),
                'Address Dependent Locality'   => $address->get('Ship To Line 3'),
                'Address Locality'             => $address->get('Ship To Town'),
                'Address Administrative Area'  => $address->get('Ship To Line 4'),
                'Address Country 2 Alpha Code' => ($address->data['Ship To Country 2 Alpha Code'] == 'XX' ? $default_country : $address->data['Ship To Country 2 Alpha Code']),

            );
            //print_r($used_fields);

            //if (!in_array('recipient', $used_fields) or !in_array('organization', $used_fields) or !in_array('addressLine1', $used_fields)) {
            ////    print_r($used_fields);
            //    print_r($address->data);
            //    exit('no recipient or organization');
            // }

            if (!in_array('addressLine2', $used_fields)) {

                if ($address_fields['Address Line 2'] != '') {
                    $address_fields['Address Line 1'] .= ', '.$address_fields['Address Line 2'];
                }
                $address_fields['Address Line 2'] = '';
            }

            if (!in_array('dependentLocality', $used_fields)) {

                if ($address_fields['Address Line 2'] == '') {
                    $address_fields['Address Line 2'] = $address_fields['Address Dependent Locality'];
                } else {
                    $address_fields['Address Line 2'] .= ', '.$address_fields['Address Dependent Locality'];
                }

                $address_fields['Address Dependent Locality'] = '';
            }

            if (!in_array('administrativeArea', $used_fields) and $address->get(
                    'Ship To Line 4'
                ) != '') {
                $address_fields['Address Administrative Area'] = '';
                //print_r($address->data);
                //print_r($address_fields);

                //print $address->display();


                //exit;

                //print_r($used_fields);
                //print_r($address->data);
                //exit('administrativeArea problem');

            }

            if (!in_array('postalCode', $used_fields) and $address->get(
                    'Ship To Postal Code'
                ) != '') {

                if (in_array('sortingCode', $used_fields)) {
                    $address_fields['Address Sorting Code'] = $address_fields['Address Postal Code'];
                    $address_fields['Address Postal Code']  = '';

                } else {
                    if (in_array('addressLine2', $used_fields)) {
                        $address_fields['Address Line 2']      .= trim(
                            ' '.$address_fields['Address Postal Code']
                        );
                        $address_fields['Address Postal Code'] = '';
                    }


                    /*
                    print_r($used_fields);
                    print_r($address->data);
                    print_r($address_fields);

                    print $address->display();


                    exit("\nError2\n");
                    */
                }

            }

            if (!in_array('locality', $used_fields) and ($address->get(
                        'Ship To Town'
                    ) != '' or $address->get('Ship To Line 4') != '')) {


                //$address_fields['Address Locality']='';
                //$address_fields['Address Dependent Locality']='';

                if (in_array('addressLine2', $used_fields)) {

                    if ($address_fields['Address Line 1'] == '' and $address_fields['Address Line 2'] == '') {
                        $address_fields['Address Line 1'] .= $address_fields['Address Dependent Locality'];
                        $address_fields['Address Line 2'] .= $address_fields['Address Locality'];

                    } elseif ($address_fields['Address Line 1'] != '' and $address_fields['Address Line 2'] == '') {
                        $address_fields['Address Line 2'] = preg_replace(
                            '/^, /', '', $address_fields['Address Dependent Locality'].', '.$address_fields['Address Locality']
                        );

                    } else {
                        $address_fields['Address Line 2'] = preg_replace(
                            '/^, /', '', $address_fields['Address Dependent Locality'].', '.$address_fields['Address Locality']
                        );

                    }
                } else {

                    print_r($used_fields);
                    print_r($address->data);
                    print_r($address_fields);

                    print $address->display();


                    exit("Error3\n");

                }


            }


        } else {


            $address_format = get_address_format($default_country);


            $address_fields = array(
                'Address Recipient'            => $recipient,
                'Address Organization'         => $organization,
                'Address Line 1'               => '',
                'Address Line 2'               => '',
                'Address Sorting Code'         => '',
                'Address Postal Code'          => '',
                'Address Dependent Locality'   => '',
                'Address Locality'             => '',
                'Address Administrative Area'  => '',
                'Address Country 2 Alpha Code' => $default_country,

            );

        }

        array_walk($address_fields,array($this, 'trim_value'));


        if (preg_match('/gb|im|jy|gg/i', $address_fields['Address Country 2 Alpha Code'])) {
            include_once 'utils/geography_functions.php';
            $address_fields['Address Postal Code']=gbr_pretty_format_post_code($address_fields['Address Postal Code']);
        }

        $_address_fields = array();
        foreach ($address_fields as $key => $value) {
            $_address_fields['Order Delivery '.$key] = $value;
        }


        $new_checksum = md5(
            json_encode(
                array(
                    'Address Recipient'            => $_address_fields['Order Delivery Address Recipient'],
                    'Address Organization'         => $_address_fields['Order Delivery Address Organization'],
                    'Address Line 1'               => $_address_fields['Order Delivery Address Line 1'],
                    'Address Line 2'               => $_address_fields['Order Delivery Address Line 2'],
                    'Address Sorting Code'         => $_address_fields['Order Delivery Address Sorting Code'],
                    'Address Postal Code'          => $_address_fields['Order Delivery Address Postal Code'],
                    'Address Dependent Locality'   => $_address_fields['Order Delivery Address Dependent Locality'],
                    'Address Locality'             => $_address_fields['Order Delivery Address Locality'],
                    'Address Administrative Area'  => $_address_fields['Order Delivery Address Administrative Area'],
                    'Address Country 2 Alpha Code' => $_address_fields['Order Delivery Address Country 2 Alpha Code'],
                )
            )
        );

        $_address_fields['Order Delivery Address Checksum'] = $new_checksum;


        $account=get_object('Account',1);
        $country = $account->get('Account Country 2 Alpha Code');
        $locale  = $store->get('Store Locale');



        list($address, $formatter, $postal_label_formatter) = get_address_formatter($country, $locale);


        $address =
            $address->withFamilyName($_address_fields['Order Delivery Address Recipient'])->withOrganization($_address_fields['Order Delivery Address Organization'])->withAddressLine1($_address_fields['Order Delivery Address Line 1'])->withAddressLine2($_address_fields['Order Delivery Address Line 2'])->withSortingCode(
                $_address_fields['Order Delivery Address Sorting Code'])->withPostalCode($_address_fields['Order Delivery Address Postal Code'])->withDependentLocality(
                $_address_fields['Order Delivery Address Dependent Locality'])->withLocality($_address_fields['Order Delivery Address Locality'])->withAdministrativeArea(
                $_address_fields['Order Delivery Address Administrative Area'])->withCountryCode(
                $_address_fields['Order Delivery Address Country 2 Alpha Code']);


        $xhtml_address = $formatter->format($address);



        $xhtml_address = preg_replace('/class="address-line1"/', 'class="address-line1 street-address"', $xhtml_address);
        $xhtml_address = preg_replace('/class="address-line2"/', 'class="address-line2 extended-address"', $xhtml_address);
        $xhtml_address = preg_replace('/class="sort-code"/', 'class="sort-code postal-code"', $xhtml_address);
        $xhtml_address = preg_replace('/class="country"/', 'class="country country-name"', $xhtml_address);


        $xhtml_address = preg_replace('/(class="address-line1 street-address"><\/span>)<br>/', '$1', $xhtml_address);

        //  print $xhtml_address;

        $_address_fields['Order Delivery Address Postal Label'] =$postal_label_formatter->format($address); ;




        $_address_fields['Order Delivery Address Formatted'] = $xhtml_address;



        //print "\n".$customer->id."\n";
        //print_r($address_fields);

        return $_address_fields;
    }


    private function trim_value(&$value) {
        $value = trim(preg_replace('/\s+/', ' ', $value));
    }

}









?>
