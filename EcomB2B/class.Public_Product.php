<?php

/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Based in 2009 class.Product.php
 Created: 28 November 2016 at 10:01:35 GMT+8, Yiwu, China
 Copyright (c) 2016, Inikoo

 Version 3

*/


class Public_Product {

    public $table_name = 'Product';

    /**
     * @var string|bool
     */
    public $id = false;
    /**
     * @var \Public_Webpage|bool
     */
    public $webpage = false;
    /**
     * @var array
     */
    public $data;
    /**
     * @var array
     */
    public $properties;
    /**
     * @var array
     */
    public $settings;
    /**
     * @var bool
     */
    public $error;
    /**
     * @var string
     */
    public $msg;
    /**
     * @var bool|\PDO
     */
    private $db;

    /**
     * @var integer
     */
    public $historic_id;

    function __construct($arg1 = false, $arg2 = false, $arg3 = false) {

        global $db;
        $this->db      = $db;
        $this->id      = false;
        $this->webpage = false;


        if (is_numeric($arg1)) {
            $this->get_data('id', $arg1);

            return;
        }

        $this->get_data($arg1, $arg2, $arg3);


    }


    function get_data($key, $id, $aux_id = false) {


        if ($key == 'id') {
            $sql = sprintf(
                "SELECT * FROM `Product Dimension` WHERE `Product ID`=%d", $id
            );


            if ($this->data = $this->db->query($sql)->fetch()) {
                $this->id          = $this->data['Product ID'];
                $this->historic_id = $this->data['Product Current Key'];
            }
        } elseif ($key == 'store_code') {
            $sql = sprintf(
                "SELECT * FROM `Product Dimension` WHERE `Product Store Key`=%s  AND `Product Code`=%s", $id, prepare_mysql($aux_id)
            );
            if ($this->data = $this->db->query($sql)->fetch()) {
                $this->id          = $this->data['Product ID'];
                $this->historic_id = $this->data['Product Current Key'];
            }
        } elseif ($key == 'historic_key') {
            $sql = sprintf(
                "SELECT * FROM `Product History Dimension` WHERE `Product Key`=%d", $id
            );


            if ($this->data = $this->db->query($sql)->fetch()) {
                $this->historic_id = $this->data['Product Key'];
                $this->id          = $this->data['Product ID'];


                $sql = sprintf("SELECT * FROM `Product Dimension` WHERE `Product ID`=%d", $this->data['Product ID']);
                if ($row = $this->db->query($sql)->fetch()) {

                    foreach ($row as $key => $value) {
                        $this->data[$key] = $value;
                    }
                }


            }
        } else {


            return;
        }

        $sql = sprintf(
            "SELECT * FROM `Product Data` WHERE `Product ID`=%d", $this->id
        );


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

        $sql = sprintf(
            'SELECT * FROM `Store Dimension` WHERE `Store Key`=%d ', $this->data['Product Store Key']
        );
        if ($row = $this->db->query($sql)->fetch()) {

            foreach ($row as $key => $value) {
                $this->data[$key] = $value;
            }
        }

    }

    function get($key, $arg1 = '') {

        switch ($key) {


            case 'Favourite Key':

                $sql = sprintf(
                    'SELECT `Customer Favourite Product Key`  FROM `Customer Favourite Product Fact` WHERE `Customer Favourite Product Product ID`=%d AND `Customer Favourite Product Customer Key`=%d ', $this->id, $arg1
                );

                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {
                        return $row['Customer Favourite Product Key'];
                    } else {
                        return 0;
                    }
                }

                break;
            case 'Unit Smart Weight':
                include_once 'utils/natural_language.php';
                $key = preg_replace('/Smart /', '', $key);

                $weight = $this->data['Product '.$key];


                if ($weight < 1) {
                    return weight($weight * 1000, 'g');
                } else {
                    return weight($weight);
                }


                break;

            case 'Status':
            case 'Barcode Number':
            case 'CPNP Number':
            case 'Code':
            case 'Web State':
            case 'Description':
            case 'Current Key':
            case 'Code File As':
            case 'Store Key':

                return $this->data['Product '.$key];
                break;


            case 'Product Current Key':
                return $this->data[$key];
                break;


            case 'Ordered Quantity':


                $sql = sprintf(
                    "SELECT `Order Quantity` FROM `Order Transaction Fact` WHERE `Order Key`=%d AND `Product ID`=%d", $arg1, $this->id
                );

                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {
                        $ordered_quantity = $row['Order Quantity'];
                        if ($ordered_quantity == 0) {
                            $ordered_quantity = '';
                        }
                    } else {
                        $ordered_quantity = '';

                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }

                return $ordered_quantity;
                break;



            case 'Product Name':
            case 'Product Price':
            case 'Product Unit Weight':
            case 'Product Barcode Number':
            case 'Product Code':
            case 'Product Family Category Key':
            case 'Product Department Category Key':
                return $this->data[$key];


                break;

            case 'Name':

                if ($this->data['Product Units Per Case'] > 1) {
                    return $this->data['Product Units Per Case'].'x '.$this->data['Product Name'];
                } else {
                    return $this->data['Product Name'];
                }


                break;

            case 'Origin':
                if ($this->data['Product Origin Country Code']) {
                    include_once 'class.Country.php';
                    $country = new Country('code', $this->data['Product Origin Country Code']);

                    return '<img alt="" src="/art/flags/'.strtolower($country->get('Country 2 Alpha Code')).'.gif" title="'.$country->get('Country Code').'"> '._($country->get('Country Name'));
                } else {
                    return '';
                }

                break;

            case 'Image Data':

                include_once 'utils/image_functions.php';

                $sql = sprintf(
                    "SELECT `Image Subject Is Principal`,`Image Key`,`Image Subject Image Caption`,`Image Filename`,`Image File Size`,`Image File Checksum`,`Image Width`,`Image Height`,`Image File Format` 
                    FROM `Image Dimension`    LEFT JOIN `Image Subject Bridge`   ON (`Image Subject Image Key`=`Image Key`)  WHERE `Image key`=%d ", $this->data['Product Main Image Key']
                );


                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {


                        $image_website = 'wi.php?id='.$row['Image Key'].'&s='.get_image_size($row['Image Key'], 330, 330, 'fit_highest');


                        $image_data = array(
                            'key'           => $row['Image Key'],
                            'src'           => $img = '/wi.php?&id='.$row['Image Key'],
                            'caption'       => $row['Image Subject Image Caption'],
                            'width'         => $row['Image Width'],
                            'height'        => $row['Image Height'],
                            'image_website' => $image_website
                        );
                    } else {
                        $image_data = array(
                            'key'           => 0,
                            'src'           => '/art/nopic.png',
                            'caption'       => '',
                            'width'         => 190,
                            'height'        => 130,
                            'image_website' => '/art/nopic.png'
                        );
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                return $image_data;

                break;

            case 'Image':


                $image_key = $this->data['Product Main Image Key'];

                if ($image_key) {
                    $img = '/wi.php?s=320x280&id='.$image_key;


                } else {
                    $img = '/art/nopic.png';

                }

                return $img;

                break;

            case 'Image Mobile In Family Webpage':

                $image_key = $this->data['Product Main Image Key'];

                if ($image_key) {
                    return '/wi.php?id='.$image_key.'&s=340x214';
                } else {
                    return '/art/nopic.png';
                }

                break;


            case 'Webpage Key':
                if (!is_object($this->webpage)) {
                    $this->load_webpage();
                }

                return $this->webpage->id;

                break;


            case 'Webpage Name':
                if (!is_object($this->webpage)) {
                    $this->load_webpage();
                }

                return $this->webpage->get('Webpage Name');

                break;


            case 'Price':

                $price = money($this->data['Product Price'], $this->data['Store Currency Code']);


                return $price;
                break;

            case 'Price Per Unit':


                if ($this->data['Product Units Per Case'] != 1) {

                    $price = '('.preg_replace('/PLN/', 'zł ', money($this->data['Product Price'] / $this->data['Product Units Per Case'], $this->data['Store Currency Code'])).'/'.$this->data['Product Unit Label'].')';


                } else {
                    $price = '';
                }


                return $price;
                break;

            case 'Webpage RRP':
            case 'RRP':

                if ($this->data['Product RRP'] == '') {
                    return '';
                }

                $rrp = preg_replace('/PLN/', 'zł ', money($this->data['Product RRP'] / $this->data['Product Units Per Case'], $this->data['Store Currency Code']));
                if ($this->get('Product Units Per Case') != 1) {
                    $rrp .= '/'.$this->data['Product Unit Label'];
                }


                return $rrp;
                break;

            case 'Out of Stock Label':


                if ($this->data['Product Next Supplier Shipment'] != '') {
                    $title = _('Expected').': '.strftime("%a %e %b %Y", strtotime($this->data['Product Next Supplier Shipment'].' +0:00'));


                    $label = _('Out of stock').' <span style="font-size:80%" title="'.$title.'">('.$this->get('Next Supplier Shipment').')</span>';
                } else {
                    $label = _('Out of stock');
                }

                return $label;


                break;

            case 'Out of Stock Class':

                return 'out_of_stock';
            //return 'launching_soon';


            case 'Unit Type':
                if ($this->data['Product Unit Type'] == '') {
                    return '';
                }

                return _($this->data['Product Unit Type']);


                break;


            case 'Availability':

                if ($this->data['Product Availability State'] == 'OnDemand') {
                    return _('On demand');
                } else {
                    return number($this->data['Product Availability']);
                }
                break;

            case 'Product Next Supplier Shipment':

                return ($this->data['Product Availability State'] == '0000-00-00 00:00:00' ? '' : $this->data['Product Availability State']);

                break;
            case 'Next Supplier Shipment':

                if ($this->data['Product Next Supplier Shipment'] != '' and $this->data['Product Next Supplier Shipment'] != '0000-00-00 00:00:00') {
                    return strftime("%e %b %y", strtotime($this->data['Product Next Supplier Shipment'].' +0:00'));

                } else {
                    return '';
                }

                break;

            case 'Next Supplier Shipment Timestamp':

                if ($this->data['Product Next Supplier Shipment'] != '' and $this->data['Product Next Supplier Shipment'] != '0000-00-00 00:00:00') {
                    return strtotime($this->data['Product Next Supplier Shipment'].' +0:00');

                } else {
                    return '';
                }

                break;
            case 'Unit Weight':
                include_once 'utils/natural_language.php';


                return weight($this->data['Product Unit Weight']);
                break;

            case 'Unit Dimensions':

                include_once 'utils/natural_language.php';


                $dimensions = '';


                $tag = preg_replace('/ Dimensions$/', '', $key);

                if ($this->data[$this->table_name.' '.$key] != '') {
                    $data = json_decode(
                        $this->data[$this->table_name.' '.$key], true
                    );
                    include_once 'utils/units_functions.php';


                    switch ($data['type']) {
                        case 'Rectangular':

                            $dimensions = number(
                                    convert_units(
                                        $data['l'], 'm', $data['units']
                                    )
                                ).'x'.number(
                                    convert_units(
                                        $data['w'], 'm', $data['units']
                                    )
                                ).'x'.number(
                                    convert_units(
                                        $data['h'], 'm', $data['units']
                                    )
                                ).' ('.$data['units'].')';
                            $dimensions .= '<span class="discreet volume">, '.volume($data['vol']).'</span>';
                            if ($this->data[$this->table_name." $tag Weight"] > 0 and $data['vol'] > 0) {

                                $dimensions .= '<span class="discreet density">, '.number(
                                        $this->data[$this->table_name." $tag Weight"] / $data['vol'], 3
                                    ).'Kg/L</span>';
                            }

                            break;
                        case 'Sheet':
                            $dimensions = number(
                                    convert_units(
                                        $data['l'], 'm', $data['units']
                                    )
                                ).'x'.number(
                                    convert_units(
                                        $data['w'], 'm', $data['units']
                                    )
                                ).' ('.$data['units'].')';

                            break;

                        case 'Cilinder':
                            $dimensions = number(
                                    convert_units(
                                        $data['h'], 'm', $data['units']
                                    )
                                ).'x'.number(
                                    convert_units(
                                        $data['w'], 'm', $data['units']
                                    )
                                ).' ('.$data['units'].')';
                            $dimensions .= '<span class="discreet volume">, '.volume($data['vol']).'</span>';
                            if ($this->data[$this->table_name." $tag Weight"] > 0 and $data['vol']) {
                                $dimensions .= '<span class="discreet density">, '.number(
                                        $this->data[$this->table_name." $tag Weight"] / $data['vol']
                                    ).'Kg/L</span>';
                            }


                            break;
                        case 'Sphere':


                            $dimensions = _('Diameter').' '.number(
                                    convert_units(
                                        $data['l'], 'm', $data['units']
                                    )
                                ).$data['units'];
                            $dimensions .= ', <span class="discreet">'.volume(
                                    $data['vol']
                                ).'</span>';
                            if ($this->data[$this->table_name." $tag Weight"] > 0 and $data['vol'] > 0) {
                                $dimensions .= '<span class="discreet">, '.number(
                                        $this->data[$this->table_name." $tag Weight"] / $data['vol']
                                    ).'Kg/L</span>';
                            }

                            break;

                        case 'String':
                            $dimensions = number(
                                    convert_units(
                                        $data['l'], 'm', $data['units']
                                    )
                                ).$data['units'];
                            break;


                        default:
                            $dimensions = '';
                    }

                }


                return $dimensions;

                break;

            case 'Unit Dimensions Short':


                $key = 'Unit Dimensions';
                include_once 'utils/natural_language.php';


                $dimensions = '';


                //$tag = preg_replace('/ Dimensions$/', '', $key);

                if ($this->data[$this->table_name.' '.$key] != '') {
                    $data = json_decode(
                        $this->data[$this->table_name.' '.$key], true
                    );
                    include_once 'utils/units_functions.php';


                    switch ($data['type']) {
                        case 'Rectangular':

                            $dimensions = number(
                                    convert_units(
                                        $data['l'], 'm', $data['units']
                                    )
                                ).'x'.number(
                                    convert_units(
                                        $data['w'], 'm', $data['units']
                                    )
                                ).'x'.number(
                                    convert_units(
                                        $data['h'], 'm', $data['units']
                                    )
                                ).' ('.$data['units'].')';


                            break;
                        case 'Sheet':
                            $dimensions = number(
                                    convert_units(
                                        $data['l'], 'm', $data['units']
                                    )
                                ).'x'.number(
                                    convert_units(
                                        $data['w'], 'm', $data['units']
                                    )
                                ).' ('.$data['units'].')';

                            break;

                        case 'Cilinder':
                            $dimensions = number(
                                    convert_units(
                                        $data['h'], 'm', $data['units']
                                    )
                                ).'x'.number(
                                    convert_units(
                                        $data['w'], 'm', $data['units']
                                    )
                                ).' ('.$data['units'].')';


                            break;

                        case 'Sphere':


                            $dimensions = _('Diameter').' '.number(convert_units($data['l'], 'm', $data['units'])).$data['units'];

                            break;
                        case 'String':
                            $dimensions = number(
                                    convert_units(
                                        $data['l'], 'm', $data['units']
                                    )
                                ).$data['units'];
                            break;



                        default:
                            $dimensions = '';
                    }

                }


                return $dimensions;

                break;

            case 'Materials':


                if ($this->data[$this->table_name.' Materials'] != '') {
                    $materials_data  = json_decode(
                        $this->data[$this->table_name.' Materials'], true
                    );
                    $xhtml_materials = '';


                    foreach ($materials_data as $material_data) {
                        if (!array_key_exists('id', $material_data)) {
                            continue;
                        }

                        if ($material_data['may_contain'] == 'Yes') {
                            $may_contain_tag = '±';
                        } else {
                            $may_contain_tag = '';
                        }

                        if ($material_data['id'] > 0) {
                            $xhtml_materials .= sprintf(
                                ', %s<span >%s</span>', $may_contain_tag, $material_data['name']
                            );
                        } else {
                            $xhtml_materials .= sprintf(
                                ', %s%s', $may_contain_tag, $material_data['name']
                            );

                        }


                        if ($material_data['ratio'] > 0) {
                            $xhtml_materials .= sprintf(
                                ' (%s)', percentage($material_data['ratio'], 1)
                            );
                        }
                    }

                    $xhtml_materials = ucfirst(
                        preg_replace('/^, /', '', $xhtml_materials)
                    );


                    return $xhtml_materials;


                } else {
                    return '';
                }
                break;

            case 'Family Code':

                $family_code = '';
                $sql         = 'select `Category Code` from `Category Dimension` where `Category Key`=?';

                $stmt = $this->db->prepare($sql);
                if ($stmt->execute(
                    array(
                        $this->data['Product Family Category Key']
                    )
                )) {
                    if ($row = $stmt->fetch()) {
                        $family_code = $row['Category Code'];
                    }
                }

                return $family_code;

                break;

            default:

            return '';
        }
        return '';
    }

    function load_webpage() {

        $this->webpage = get_object('public_webpage-scope_product', $this->id);
    }

    function get_attachments() {

        $attachments = array();


        $sql = sprintf(
            "SELECT `Attachment Subject Type`, `Attachment Bridge Key`,`Attachment Caption`  FROM `Product Part Bridge`  LEFT JOIN `Attachment Bridge` AB  ON (AB.`Subject Key`=`Product Part Part SKU`)    WHERE AB.`Subject`='Part' AND  `Product Part Product ID`=%d  AND `Attachment Public`='Yes' ",
            $this->id
        );


        if ($result2 = $this->db->query($sql)) {
            foreach ($result2 as $row2) {

                if ($row2['Attachment Subject Type'] == 'MSDS') {
                    $label = '<span title="'._('Material safety data sheet').'">MSDS</span>';
                } else {
                    $label = _('Attachment');
                }


                $attachments[] = array(
                    'id'    => $row2['Attachment Bridge Key'],
                    'label' => $label,
                    'name'  => $row2['Attachment Caption']
                );
            }
        }


        return $attachments;


    }




    function get_image_gallery() {

        include_once 'utils/image_functions.php';


        $sql = sprintf(
            "SELECT `Image Subject Is Principal`,`Image Key`,`Image Subject Image Caption`,`Image Filename`,`Image File Size`,`Image File Checksum`,`Image Width`,`Image Height`,`Image File Format` FROM `Image Subject Bridge` B LEFT JOIN `Image Dimension` I ON (`Image Subject Image Key`=`Image Key`) WHERE `Image Subject Object`=%s AND   `Image Subject Object Key`=%d ORDER BY `Image Subject Is Principal`,`Image Subject Date`,`Image Subject Key`",
            prepare_mysql('Product'), $this->id
        );


        $gallery = array();
        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {

                if ($row['Image Key']) {


                    $image_website = 'wi.php?id='.$row['Image Key'].'&s='.get_image_size($row['Image Key'], '', 50, 'height');


                    $gallery[] = array(
                        'src'           => 'wi.php?id='.$row['Image Key'],
                        'caption'       => $row['Image Subject Image Caption'],
                        'key'           => $row['Image Key'],
                        'width'         => $row['Image Width'],
                        'height'        => $row['Image Height'],
                        'image_website' => $image_website

                    );
                }

            }
        }
        return $gallery;

    }

    function get_number_images() {

        $subject = $this->table_name;

        $number_of_images = 0;
        $sql              = sprintf(
            "SELECT count(*) AS num FROM `Image Subject Bridge` WHERE `Image Subject Object`=%s AND `Image Subject Object Key`=%d ", prepare_mysql($subject), $this->id
        );
        //print $sql;


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $number_of_images = $row['num'];
            }
        }


        return $number_of_images;
    }

    function get_object_name() {
        return $this->table_name;

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


        $parent_categories = $this->get_parent_categories();

        if (count($parent_categories) > 0) {

            $sql = sprintf(
                "SELECT `Deal Component Key` FROM `Deal Component Dimension` WHERE `Deal Component Allowance Target`='Category' AND `Deal Component Allowance Target Key` in (%s) $where", join(',', $parent_categories)

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

        }


        return $deal_components;


    }

    function get_parent_categories($scope = 'keys') {


        $type              = 'Product';
        $parent_categories = array();


        $sql = sprintf(
            "SELECT `Webpage Code`,B.`Category Key`,`Category Root Key`,`Other Note`,`Category Label`,`Category Code`,`Is Category Field Other` 
        FROM `Category Bridge` B 
        LEFT JOIN `Category Dimension` C ON (C.`Category Key`=B.`Category Key`) 
        LEFT JOIN `Page Store Dimension` W ON (W.`Webpage Scope Key`=B.`Category Key` AND `Webpage Scope`=%s) 

          WHERE  `Category Branch Type`='Head'  AND B.`Subject Key`=%d AND B.`Subject`=%s",

            prepare_mysql('Category Products'),

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



}

