<?php
/*
 File: Page.php

 This file contains the Page Class

 About:
 Author: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2009, Inikoo

 Version 2.0
*/
include_once 'class.DB_Table.php';

include_once 'class.Image.php';
include_once 'trait.ImageSubject.php';
include_once 'trait.NotesSubject.php';

include_once 'utils/website_functions.php';

class Page extends DB_Table {
    use ImageSubject, NotesSubject;

    var $new = false;
    var $logged = false;
    var $snapshots_taken = 0;
    var $set_title = false;
    var $set_currency = 'GBP';
    var $set_currency_exchange = 1;
    var $deleted = false;

    function Page($arg1 = false, $arg2 = false, $arg3 = false, $_db = false) {

        if (!$_db) {
            global $db;
            $this->db = $db;
        } else {
            $this->db = $_db;
        }

        $this->table_name    = 'Page';
        $this->ignore_fields = array('Page Key');
        $this->scope         = false;
        $this->scope_load    = false;

        $this->scope_found = '';


        if (!$arg1 and !$arg2) {
            $this->error = true;
            $this->msg   = 'No arguments';
        }
        if (is_numeric($arg1)) {
            $this->get_data('id', $arg1);

            return;
        }
        if (is_string($arg1) and !$arg2) {
            $this->get_data('url', $arg1);

            return;
        }


        if (is_array($arg2) and preg_match('/create|new/i', $arg1)) {
            $this->find($arg2, $arg3.' create');

            return;
        }
        if (preg_match('/find/i', $arg1)) {
            $this->find($arg2, $arg3);

            return;
        }

        $this->get_data($arg1, $arg2, $arg3);

    }


    function get_data($tipo, $tag, $tag2 = false) {

        if (preg_match('/url|address|www/i', $tipo)) {
            $sql = sprintf(
                "SELECT * FROM `Page Dimension` WHERE  `Page URL`=%s", prepare_mysql($tag)
            );
        } elseif ($tipo == 'store_page_code') {
            $sql = sprintf(
                "SELECT * FROM `Page Store Dimension` PS LEFT JOIN `Page Dimension` P  ON (P.`Page Key`=PS.`Page Key`) WHERE `Page Code`=%s AND `Page Store Key`=%d ", prepare_mysql($tag2), $tag
            );
        } elseif ($tipo == 'site_code') {
            $sql = sprintf(
                "SELECT * FROM `Page Store Dimension` PS LEFT JOIN `Page Dimension` P  ON (P.`Page Key`=PS.`Page Key`) WHERE `Page Code`=%s AND PS.`Page Site Key`=%d ", prepare_mysql($tag2), $tag
            );

        } elseif ($tipo == 'website_code') {
            $sql = sprintf(
                "SELECT * FROM `Page Store Dimension` PS LEFT JOIN `Page Dimension` P  ON (P.`Page Key`=PS.`Page Key`) WHERE `Website Code`=%s AND PS.`Webpage Website Key`=%d ", prepare_mysql($tag2), $tag
            );

        } elseif ($tipo == 'scope') {
            $sql = sprintf(
                "SELECT * FROM `Page Store Dimension` PS LEFT JOIN `Page Dimension` P  ON (P.`Page Key`=PS.`Page Key`) WHERE `Webpage Scope`=%s AND `Webpage Scope Key`=%d ", prepare_mysql($tag), $tag2

            );

        } elseif ($tipo == 'deleted') {
            $this->get_deleted_data($tag);

            return;
        } else {
            $sql = sprintf(
                "SELECT * FROM `Page Dimension` WHERE  `Page Key`=%d", $tag
            );
        }


        if ($this->data = $this->db->query($sql)->fetch()) {

            $this->id = $this->data['Page Key'];


            $this->type = $this->data['Page Type'];

            if ($this->type == 'Store') {
                $sql = sprintf("SELECT * FROM `Page Store Dimension` WHERE  `Page Key`=%d", $this->id);


                if ($result2 = $this->db->query($sql)) {
                    if ($row2 = $result2->fetch()) {
                        foreach ($row2 as $key => $value) {
                            $this->data[$key] = $value;
                        }
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


            } elseif ($this->type == 'Internal') {
                $sql = sprintf("SELECT * FROM `Page Internal Dimension` WHERE  `Page Key`=%d", $this->id);


                if ($result2 = $this->db->query($sql)) {
                    if ($row2 = $result2->fetch()) {
                        foreach ($row2 as $key => $value) {
                            $this->data[$key] = $value;
                        }
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


            }
        }


    }

    function get_deleted_data($tag) {

        $this->deleted = true;
        $sql           = sprintf(
            "SELECT * FROM `Page Store Deleted Dimension` WHERE `Page Key`=%d", $tag
        );

        // print $sql;


        if ($this->data = $this->db->query($sql)->fetch()) {
            $this->id = $this->data['Page Store Deleted Key'];


            if ($this->data['Page Store Deleted Metadata'] != '') {
                $deleted_data = json_decode(gzuncompress($this->data['Page Store Deleted Metadata']), true);
                foreach ($deleted_data['data'] as $key => $value) {
                    $this->data[$key] = $value;
                }
            }
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

        $create = '';

        if (preg_match('/create/i', $options)) {
            $create = 'create';
        }


        $sql = sprintf(
            "SELECT P.`Page Key` FROM `Page Store Dimension` P  WHERE `Webpage Code`=%s AND `Webpage Website Key`=%d ", prepare_mysql($raw_data['Webpage Code']), $raw_data['Webpage Website Key']
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->found     = true;
                $this->found_key = $row['Page Key'];
                $this->get_data('id', $this->found_key);
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        if (!$this->found and $create) {
            $this->create($raw_data);

        }


    }

    function create($raw_data, $migration_hack = false) {


        $this->new = false;
        if (!isset($raw_data['Page Code']) or $raw_data['Page Code'] == '') {
            $this->error = true;
            $this->msg   = 'No page code';

        }

        if (!isset($raw_data['Page URL']) or $raw_data['Page URL'] == '') {

            $raw_data['Page URL'] = "info.php?page=".$raw_data['Page Code'];
        }

        if (!isset($raw_data['Page Short Title']) or $raw_data['Page Short Title'] == '') {

            $raw_data['Page Short Title'] = $raw_data['Page Title'];
        }


        $data = $this->base_data();
        foreach ($raw_data as $key => $value) {
            if (array_key_exists($key, $data)) {
                $data[$key] = _trim($value);
            }


        }


        $keys   = '(';
        $values = 'values(';
        foreach ($data as $key => $value) {
            $keys .= "`$key`,";
            if (preg_match('/Page Title|Page Description|Javascript|CSS|Page Keywords/i', $key)) {
                $values .= "'".addslashes($value)."',";
            } else {
                $values .= prepare_mysql($value).",";
            }
        }
        $keys   = preg_replace('/,$/', ')', $keys);
        $values = preg_replace('/,$/', ')', $values);
        $sql    = sprintf("INSERT INTO `Page Dimension` %s %s", $keys, $values);


        if ($this->db->exec($sql)) {
            $this->id = $this->db->lastInsertId();
            $this->get_data('id', $this->id);


            if ($this->data['Page Type'] == 'Store') {
                $this->create_store_page($raw_data);

            }

            $sql = sprintf(
                "INSERT INTO `Page State Timeline`  (`Page Key`,`Site Key`,`Store Key`,`Date`,`State`,`Operation`) VALUES (%d,%d,%d,%s,%s,'Created') ", $this->id, $this->data['Page Site Key'], $this->data['Page Site Key'], prepare_mysql(gmdate('Y-m-d H:i:s')),
                prepare_mysql($this->data['Page State'])

            );
            $this->db->exec($sql);


        } else {
            $this->error = true;
            $this->msg   = 'Can not insert Page Dimension';
            exit("$sql\n");
        }


    }


    function create_store_page($raw_data) {

        $data = $this->store_base_data();
        foreach ($raw_data as $key => $value) {
            if (array_key_exists($key, $data)) {
                $data[$key] = $value;
                if (is_string($value)) {
                    $data[$key] = _trim($value);
                } elseif (is_array($value)) {
                    $data[$key] = serialize($value);
                }
            }
        }


        $data['Page Key'] = $this->id;
        $keys             = '(';

        $values = 'values(';
        foreach ($data as $key => $value) {
            $keys .= "`$key`,";
            if ($key == 'Page Source Template') {
                $values .= prepare_mysql($value, false).",";
            } else {
                if (preg_match('/Title|Description/i', $key)) {
                    $values .= "'".addslashes($value)."',";
                } else {
                    $values .= prepare_mysql($value).",";
                }
            }
        }


        $keys   = preg_replace('/,$/', ')', $keys);
        $values = preg_replace('/,$/', ')', $values);
        $sql    = sprintf("INSERT INTO `Page Store Dimension` %s %s", $keys, $values);


        if ($this->db->exec($sql)) {

            $this->get_data('id', $this->id);
            $this->new = true;


            $sql = sprintf(
                "INSERT INTO `Page Store Data Dimension` (`Page Key`) VALUES (%d)", $this->id
            );
            $this->db->exec($sql);


            $content = $this->get_plain_content();


            $sql = sprintf(
                "INSERT INTO `Page Store Search Dimension` VALUES (%d,%d,%s,%s,%s,%s)", $this->id, $this->data['Webpage Website Key'], prepare_mysql($this->data['Webpage URL']), prepare_mysql($this->data['Webpage Name'], false),
                prepare_mysql($this->data['Webpage Meta Description'], false), prepare_mysql($content, false)
            );
            $this->db->exec($sql);

            $this->update_url();

            $this->update_see_also();

            $this->update_image_key();
            $this->refresh_cache();

            return $this;


        } else {
            $this->error = true;
            $this->msg   = 'Can not insert Page Store Dimension';
            print "$sql\n";
            exit;
        }

    }

    function store_base_data() {
        $data = array();


        $sql = 'show columns from `Page Store Dimension`';
        foreach ($this->db->query($sql) as $row) {
            if (!in_array($row['Field'], $this->ignore_fields)) {
                $data[$row['Field']] = $row['Default'];
            }
        }


        return $data;
    }

    function get_plain_content() {
        $content = $this->get_xhtml_content();
        $content = preg_replace('/\<br\/?\>/', ' ', $content);
        $content = preg_replace('/:/', ' ', $content);

        $content = strip_tags($content);
        $content = preg_replace('/\s+/', ' ', $content);

        $content = html_entity_decode($content, ENT_QUOTES, "utf-8");

        $content = preg_replace('/\&amp\;/', '', $content);
        $content = preg_replace('/\&nbsp\;/', '', $content);
        $content = preg_replace('/\{.+\}/', '', $content);
        $content = preg_replace('/(\"|\“|\”)/', '', $content);


        return $content;
    }

    function get_xhtml_content() {

        // todo

        return '';


    }

    function update_url() {

        $website = get_object('website', $this->get('Webpage Website Key'));


        $this->update(array('Webpage URL' => 'https://'.$website->get('Website URL').'/'.strtolower($this->get('Code'))), 'no_history');


    }

    function get($key) {
        switch ($key) {


            case 'Website Registration Type':
            case 'Registration Type':

                $website = get_object('website', $this->get('Webpage Website Key'));

                return $website->get($key);


                break;
            case 'See Also':

                $see_also_data = $this->get_see_also_data();
                $see_also      = '';
                if ($see_also_data['type'] == 'Auto') {
                    $see_also = _('Automatic').': ';
                }

                if (count($see_also_data['links']) == 0) {
                    $see_also .= ', '._('none');
                } else {
                    foreach ($see_also_data['links'] as $link) {
                        $see_also .= $link['code'].', ';
                    }
                }
                $see_also = preg_replace('/, $/', '', $see_also);

                return $see_also;


                break;


            case 'Browser Title':

                return $this->data['Webpage '.$key];
                break;
            case 'State Icon':

                switch ($this->data['Webpage State']) {
                    case 'InProcess':
                        return '<i class="fa fa-fw fa-child" aria-hidden="true"></i>';
                    case 'Ready':
                        return '<i class="fa fa-fw  fa-check-circle" aria-hidden="true"></i>';
                    case 'Online':
                        return '<i class="fa fa-fw fa-rocket" aria-hidden="true"></i>';
                    case 'Offline':
                        return '<i class="fa fa-fw fa-rocket discreet fa-flip-vertical" aria-hidden="true"></i>';

                    default:
                        return $this->data['Webpage State'];
                }

                break;


            case 'State':

                switch ($this->data['Webpage State']) {
                    case 'InProcess':
                        return $this->get('State Icon').' '._('In process');
                    case 'Ready':
                        return $this->get('State Icon').' '._('Ready');
                    case 'Online':
                        return $this->get('State Icon').' '._('Online');
                    case 'Offline':
                        return '<span class="very_discreet">'.$this->get('State Icon').' '._('Offline').'</span>';

                    default:
                        return $this->data['Webpage State'];
                }

                break;

            case 'Send Email Address':
                include_once 'class.Store.php';
                $store = new Store($this->data['Webpage Store Key']);

                return $store->get('Store Email');

                break;
            case 'Send Email Signature':
                include_once 'class.Store.php';
                $store = new Store($this->data['Webpage Store Key']);

                return $store->get('Store Email Template Signature');

                break;

            case 'Email':
            case 'Company Name':
            case 'VAT Number':
            case 'Company Number':
            case 'Telephone':
            case 'Address':
            case 'Google Map URL':
                include_once('class.Store.php');
                $store = new Store($this->data['Webpage Store Key']);

                return $store->get($key);

                break;
            case 'Store Email':
            case 'Store Company Name':
            case 'Store VAT Number':
            case 'Store Company Number':
            case 'Store Telephone':
            case 'Store Address':
            case 'Store Google Map URL':
                include_once('class.Store.php');
                $store = new Store($this->data['Webpage Store Key']);

                return $store->get($key);

                break;

            case 'Template Filename':

                switch ($this->data['Webpage Template Filename']) {
                    case 'blank':
                        $template_label = _('Old template').' '._('unsupported');
                        break;
                    case 'categories_classic_showcase':
                        $template_label = _('Classic grid');
                        break;
                    case 'categories_showcase':
                        $template_label = _('Rigid grid');
                        break;
                    default:
                        $template_label = $this->data['Webpage Template Filename'];
                }

                return $template_label;
                break;
            case 'Publish':


                if ($this->data['Page Store Content Data'] != $this->data['Page Store Content Published Data']) {


                    return true;
                }

                if ($this->data['Page Store CSS'] != $this->data['Page Store Published CSS']) {


                    return true;
                }

                $this->load_scope();

                if ($this->scope_found == 'Category') {

                    $sql = sprintf(
                        'SELECT `Product Category Index Stack`,`Product Category Index Published Stack`,`Product Category Index Content Data`,`Product Category Index Content Published Data`  FROM  `Product Category Index`  WHERE `Product Category Index Category Key`=%d  ',
                        $this->scope->id
                    );

                    if ($result = $this->db->query($sql)) {
                        foreach ($result as $row) {
                            if ($row['Product Category Index Stack'] != $row['Product Category Index Published Stack']) {
                                return true;
                            }
                            if ($row['Product Category Index Content Data'] != $row['Product Category Index Content Published Data']) {
                                return true;
                            }
                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        print "$sql\n";
                        exit;
                    }


                }


                $sql = sprintf(
                    'SELECT `Webpage Related Product Order`,`Webpage Related Product Published Order`,`Webpage Related Product Content Data`,`Webpage Related Product Content Published Data`  FROM  `Webpage Related Product Bridge`  WHERE `Webpage Related Product Page Key`=%d  ',
                    $this->id
                );

                // print $sql;

                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {
                        if ($row['Webpage Related Product Order'] != $row['Webpage Related Product Published Order']) {
                            return true;
                        }
                        if ($row['Webpage Related Product Content Data'] != $row['Webpage Related Product Content Published Data']) {
                            return true;
                        }
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                return false;

                break;

            case 'Content Data':
                if ($this->data['Page Store '.$key] == '') {
                    $content_data = false;
                } else {
                    $content_data = json_decode($this->data['Page Store '.$key], true);
                }

                return $content_data;
                break;


            case 'Scope Metadata':

                if ($this->data['Webpage '.$key] == '') {
                    $content_data = false;
                } else {
                    $content_data = json_decode($this->data['Webpage '.$key], true);
                }

                return $content_data;
                break;

            case 'Webpage Launching Date':
                $content_data = $this->get('Content Data');
                if (isset($content_data['_launch_date'])) {
                    return $content_data['_launch_date'];
                } else {
                    return '';
                }
            case 'Launching Date':
                $content_data = $this->get('Content Data');
                if (isset($content_data['_launch_date']) and $content_data['_launch_date'] != '') {
                    return strftime("%A, %x", strtotime($content_data['_launch_date'].' +0:00'));
                } else {
                    return '';
                }

            case 'Webpage Website Key':
                return $this->get('Page Site Key');
                break;
            case 'Code':
                return $this->data['Webpage Code'];
                break;

            case  'Page Found In Page Key':

                $found_in_page_key = '';

                $sql = sprintf(
                    "SELECT `Page Store Found In Key` FROM  `Page Store Found In Bridge` WHERE `Page Store Key`=%d", $this->id
                );

                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {
                        $found_in_page_key = $row['Page Store Found In Key'];
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    exit;
                }

                return $found_in_page_key;
                break;
            case  'Found In Page Key':

                $found_in_page = '';

                $sql = sprintf(
                    "SELECT `Page Code` FROM  `Page Store Found In Bridge` B  LEFT JOIN `Page Store Dimension` ON (`Page Key`=`Page Store Found In Key`)  WHERE B.`Page Store Key`=%d", $this->id
                );

                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {
                        $found_in_page = $row['Page Code'];
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    exit;
                }

                return $found_in_page;
                break;

            case('link'):
                return $this->display();
                break;

            default:
                if (isset($this->data[$key])) {
                    return $this->data[$key];
                }

                if (isset($this->data['Webpage '.$key])) {
                    return $this->data['Webpage '.$key];
                }
        }

        if (preg_match('/ Acc /', $key)) {

            $amount = 'Page Store '.$key;

            return number($this->data[$amount]);
        }

        return false;
    }

    function get_see_also_data() {

        $see_also = array();
        $sql      = sprintf(
            "SELECT `Page Store See Also Key`,`Correlation Type`,`Correlation Value` FROM  `Page Store See Also Bridge` WHERE `Page Store Key`=%d ORDER BY `Webpage See Also Order` ", $this->id
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {

                $see_also_page = new Page($row['Page Store See Also Key']);
                if ($see_also_page->id) {

                    if ($this->data['Page Store See Also Type'] == 'Manual') {
                        $formatted_correlation_type  = _('Manual');
                        $formatted_correlation_value = '';
                    } else {

                        switch ($row['Correlation Type']) {
                            case 'Manual':
                                $formatted_correlation_type  = _('Manual');
                                $formatted_correlation_value = '';
                                break;
                            case 'Sales':
                                $formatted_correlation_type  = _('Sales');
                                $formatted_correlation_value = percentage(
                                    $row['Correlation Value'], 1
                                );
                                break;
                            case 'Semantic':
                                $formatted_correlation_type  = _('Semantic');
                                $formatted_correlation_value = number(
                                    $row['Correlation Value']
                                );
                                break;
                            case 'New':
                                $formatted_correlation_type  = _('New');
                                $formatted_correlation_value = number(
                                    $row['Correlation Value']
                                );
                                break;
                            default:
                                $formatted_correlation_type  = $row['Correlation Type'];
                                $formatted_correlation_value = number(
                                    $row['Correlation Value']
                                );
                                break;
                        }
                    }
                    //if ($site_url)
                    //$link='<a href="http://'.$site_url.'/'.$see_also_page->data['Page URL'].'">'.$see_also_page->data['Page Short Title'].'</a>';

                    //else
                    $link = '<a href="http://'.$see_also_page->data['Page URL'].'">'.$see_also_page->data['Page Short Title'].'</a>';

                    $see_also[] = array(
                        'link'                        => $link,
                        'label'                       => $see_also_page->data['Page Short Title'],
                        'url'                         => $see_also_page->data['Page URL'],
                        'key'                         => $see_also_page->id,
                        'code'                        => $see_also_page->data['Page Code'],
                        'correlation_type'            => $row['Correlation Type'],
                        'correlation_formatted'       => $formatted_correlation_type,
                        'correlation_value'           => $row['Correlation Value'],
                        'correlation_formatted_value' => $formatted_correlation_value,
                        'image_key'                   => $see_also_page->data['Page Store Image Key']

                    );
                }


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        if ($this->data['Page See Also Last Updated'] == '') {
            $last_updated = '';
        } else {
            $last_updated = strftime(
                "%a %e %b %Y %H:%M:%S %Z", strtotime($this->data['Page See Also Last Updated'].' +0:00')
            );
        }


        $data = array(
            'website_key'  => $this->get('Page Site Key'),
            'webpage_key'  => $this->id,
            'type'         => $this->get('Page Store See Also Type'),
            'number_links' => $this->get('Number See Also Links'),
            'last_updated' => $last_updated,
            'links'        => $see_also
        );

        return $data;
    }

    function load_scope() {

        $this->scope_load = true;


        if ($this->data['Webpage Scope'] == 'Product') {
            include_once('class.Public_Product.php');
            $this->scope       = new Public_Product($this->data['Webpage Scope Key']);
            $this->scope_found = 'Product';

        } elseif ($this->data['Webpage Scope'] == 'Category Categories' or $this->data['Webpage Scope'] == 'Category Products') {
            include_once('class.Public_Category.php');

            $this->scope       = new Public_Category($this->data['Webpage Scope Key']);
            $this->scope_found = 'Category';

        }


    }

    function display($tipo = 'link') {

        switch ($tipo) {
            case('html'):
            case('xhtml'):
            case('link'):
            default:
                return '<a href="'.$this->data['Webpage URL'].'">'.$this->data['Page Title'].'</a>';

        }


    }

    function update_see_also() {


        if ($this->data['Page Type'] != 'Store' or $this->data['Page Store See Also Type'] == 'Manual') {
            return;
        }


        if (!isset($this->data['Number See Also Links'])) {
            //print_r($this);
            exit('error in update see also');

        }

        $max_links = $this->data['Number See Also Links'] * 2;


        $max_sales_links = ceil($max_links * .6);


        $min_sales_correlation_samples = 5;
        $correlation_upper_limit       = .5 / ($min_sales_correlation_samples);
        $see_also                      = array();
        $number_links                  = 0;


        switch ($this->data['Webpage Scope']) {
            case 'Department Catalogue':
                break;

            case 'Category Products':


                $sql = sprintf(
                    "SELECT * FROM `Product Family Sales Correlation` WHERE `Family A Key`=%d ORDER BY `Correlation` DESC ", $this->data['Webpage Scope Key']
                );


                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {
                        $_family  = get_object('Category', $row['Family B Key']);
                        $_webpage = $_family->get_webpage();
                        // and $_webpage->data['Page Stealth Mode'] == 'No'
                        if ($_webpage->id and $_webpage->data['Page State'] == 'Online') {
                            $see_also[$_webpage->id] = array(
                                'type'     => 'Sales',
                                'value'    => $row['Correlation'],
                                'page_key' => $_webpage->id
                            );
                            $number_links            = count($see_also);
                            if ($number_links >= $max_sales_links) {
                                break;
                            }
                        }
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                if ($number_links < $max_links) {
                    $sql = sprintf(
                        "SELECT * FROM `Product Family Semantic Correlation` WHERE `Family A Key`=%d ORDER BY `Weight` DESC LIMIT %d", $this->data['Webpage Scope Key'], ($max_links - $number_links) * 2
                    );


                    if ($result = $this->db->query($sql)) {
                        foreach ($result as $row) {

                            if (!array_key_exists($row['Family B Key'], $see_also)) {


                                $_family  = get_object('Category', $row['Family B Key']);
                                $_webpage = $_family->get_webpage();
                                // and $_webpage->data['Page Stealth Mode'] == 'No'
                                if ($_webpage->id and $_webpage->data['Page State'] == 'Online') {
                                    $see_also[$_webpage->id] = array(
                                        'type'     => 'Semantic',
                                        'value'    => $row['Weight'],
                                        'page_key' => $_webpage->id
                                    );
                                    $number_links            = count($see_also);
                                    if ($number_links >= $max_links) {
                                        break;
                                    }
                                }


                            }

                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        exit;
                    }


                }


                if ($number_links < $max_links) {

                    $category = get_object('Category', $this->data['Webpage Scope Key']);

                    $sql = sprintf(
                        "SELECT `Category Key` FROM `Category Dimension` LEFT JOIN   `Product Category Dimension` ON (`Category Key`=`Product Category Key`)   LEFT JOIN `Page Store Dimension` ON (`Product Category Webpage Key`=`Page Key`) WHERE `Category Parent Key`=%d  AND `Webpage State`='Online'  AND `Category Key`!=%d  ORDER BY RAND()  LIMIT %d",
                        $category->get('Category Parent Key'), $category->id, ($max_links - $number_links) * 2
                    );


                    if ($result = $this->db->query($sql)) {
                        foreach ($result as $row) {

                            if (!array_key_exists($row['Category Key'], $see_also)) {


                                $_family  = get_object('Category', $row['Category Key']);
                                $_webpage = $_family->get_webpage();
                                // and $_webpage->data['Page Stealth Mode'] == 'No'
                                if ($_webpage->id and $_webpage->data['Page State'] == 'Online') {
                                    $see_also[$_webpage->id] = array(
                                        'type'     => 'SameParent',
                                        'value'    => .2,
                                        'page_key' => $_webpage->id
                                    );
                                    $number_links            = count($see_also);
                                    if ($number_links >= $max_links) {
                                        break;
                                    }
                                }


                            }

                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        exit;
                    }


                }


                if ($number_links < $max_links) {


                    $sql = sprintf(
                        "SELECT `Category Key` FROM `Category Dimension` LEFT JOIN   `Product Category Dimension` ON (`Category Key`=`Product Category Key`)   LEFT JOIN `Page Store Dimension` ON (`Product Category Webpage Key`=`Page Key`) WHERE  `Webpage State`='Online'  AND `Category Key`!=%d  AND `Category Store Key`=%d ORDER BY RAND()  LIMIT %d",
                        $this->data['Webpage Scope Key'], $category->get('Category Store Key'), ($max_links - $number_links) * 2
                    );


                    if ($result = $this->db->query($sql)) {
                        foreach ($result as $row) {

                            if (!array_key_exists($row['Category Key'], $see_also)) {


                                $_family  = get_object('Category', $row['Category Key']);
                                $_webpage = $_family->get_webpage();
                                // and $_webpage->data['Page Stealth Mode'] == 'No'
                                if ($_webpage->id and $_webpage->data['Page State'] == 'Online') {
                                    $see_also[$_webpage->id] = array(
                                        'type'     => 'Other',
                                        'value'    => .1,
                                        'page_key' => $_webpage->id
                                    );
                                    $number_links            = count($see_also);
                                    if ($number_links >= $max_links) {
                                        break;
                                    }
                                }


                            }

                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        exit;
                    }


                }


                break;


            case 'Product':

                $product = get_object('Product', $this->data['Webpage Scope Key']);
                $sql     = sprintf(
                    "SELECT `Product Webpage Key`,`Product B ID`,`Correlation` FROM `Product Sales Correlation`  LEFT JOIN `Product Dimension` ON (`Product ID`=`Product B ID`)    LEFT JOIN `Page Store Dimension` ON (`Page Key`=`Product Webpage Key`)  WHERE `Product A ID`=%d AND `Webpage State`='Online' AND `Product Web State`='For Sale'  ORDER BY `Correlation` DESC",
                    $product->id
                );
                //  $see_also_page->data['Page Stealth Mode'] == 'No')

                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {
                        if (!array_key_exists($row['Product B ID'], $see_also) and $row['Product Webpage Key']) {

                            $see_also[$row['Product Webpage Key']] = array(
                                'type'     => 'Sales',
                                'value'    => $row['Correlation'],
                                'page_key' => $row['Product Webpage Key']
                            );
                            $number_links                          = count($see_also);
                            if ($number_links >= $max_links) {
                                break;
                            }

                        }
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                if ($number_links >= $max_links) {
                    break;
                }


                $max_customers = 0;

                $sql = sprintf(
                    "SELECT P.`Product ID`,P.`Product Code`,`Product Web State`,`Product Webpage Key`,`Product Total Acc Customers` FROM `Product Dimension` P LEFT JOIN `Product Data Dimension` D ON (P.`Product ID`=D.`Product ID`)    LEFT JOIN `Page Store Dimension` ON (`Page Key`=`Product Webpage Key`)  WHERE  `Product Web State`='For Sale' AND `Webpage State`='Online' AND P.`Product ID`!=%d  AND `Product Family Category Key`=%d ORDER BY `Product Total Acc Customers` DESC  ",
                    $product->id, $product->get('Product Family Category Key')

                );

                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {


                        if (!array_key_exists($row['Product ID'], $see_also) and $row['Product Webpage Key']) {


                            if ($max_customers == 0) {
                                $max_customers = $row['Product Total Acc Customers'];
                            }


                            $rnd = mt_rand() / mt_getrandmax();

                            $see_also[$row['Product Webpage Key']] = array(
                                'type'     => 'Same Family',
                                'value'    => .25 * $rnd * ($row['Product Total Acc Customers'] == 0 ? 1 : $row['Product Total Acc Customers']) / ($max_customers == 0 ? 1 : $max_customers),
                                'page_key' => $row['Product Webpage Key']
                            );
                            $number_links                          = count($see_also);
                            if ($number_links >= $max_links) {
                                break;
                            }
                        }

                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                if ($number_links >= $max_links) {
                    break;
                }
                $max_customers = 0;
                $sql           = sprintf(
                    "SELECT P.`Product ID`,P.`Product Code`,`Product Web State`,`Product Webpage Key`,`Product Total Acc Customers` FROM `Product Dimension` P LEFT JOIN `Product Data Dimension` D ON (P.`Product ID`=D.`Product ID`)    LEFT JOIN `Page Store Dimension` ON (`Page Key`=`Product Webpage Key`)  WHERE  `Product Web State`='For Sale' AND `Webpage State`='Online' AND P.`Product ID`!=%d  AND `Product Store Key`=%d ORDER BY `Product Total Acc Customers` DESC  ",
                    $product->id, $product->get('Product Store Key')

                );

                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {


                        if (!array_key_exists($row['Product ID'], $see_also) and $row['Product Webpage Key']) {

                            if ($max_customers == 0) {
                                $max_customers = $row['Product Total Acc Customers'];
                            }


                            $rnd = mt_rand() / mt_getrandmax();

                            $see_also[$row['Product Webpage Key']] = array(
                                'type'     => 'Other',
                                'value'    => .1 * $rnd * ($row['Product Total Acc Customers'] == 0 ? 1 : $row['Product Total Acc Customers']) / ($max_customers == 0 ? 1 : $max_customers),
                                'page_key' => $row['Product Webpage Key']
                            );
                            $number_links                          = count($see_also);
                            if ($number_links >= $max_links) {
                                break;
                            }
                        }

                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                break;
            default:

                break;
        }


        $sql = sprintf(
            "DELETE FROM `Page Store See Also Bridge`WHERE `Page Store Key`=%d ", $this->id
        );
        $this->db->exec($sql);


        $count = 0;

        $order_value = 1;


        if (count($see_also) > 0) {


            foreach ($see_also as $key => $row) {
                $correlation[$key] = $row['value'];
            }

            //print_r($correlation);

            array_multisort($correlation, SORT_DESC, $see_also);
            // print_r($see_also);


            foreach ($see_also as $see_also_page_key => $see_also_data) {

                if ($count >= $this->data['Number See Also Links']) {
                    break;
                }

                $sql = sprintf(
                    "INSERT  INTO `Page Store See Also Bridge` (`Page Store Key`,`Page Store See Also Key`,`Correlation Type`,`Correlation Value`,`Webpage See Also Order`)  VALUES (%d,%d,%s,%f,%d) ", $this->id, $see_also_data['page_key'],
                    prepare_mysql($see_also_data['type']), $see_also_data['value'], $order_value
                );
                $this->db->exec($sql);
                $count++;
                $order_value++;
                //print "$sql\n";
            }

        }
        $this->update(
            array('Page See Also Last Updated' => gmdate('Y-m-d H:i:s')), 'no_history'
        );

    }

    function update_image_key() {


        if ($this->data['Page Type'] != 'Store') {
            return;
        }


        $page_image_source = 'art/nopic.png';
        $image_key         = '';


        switch ($this->data['Webpage Scope']) {
            case 'Category Categories':
            case 'Category Products':
                include_once 'class.Category.php';
                $category = new Category('id', $this->data['Page Parent Key']);
                if ($category->id and $category->get('Category Main Image Key')) {
                    $_page_image = new Image($category->get('Category Main Image Key'));
                    if ($_page_image->id) {
                        $page_image_source = sprintf("images/%07d.%s", $_page_image->data['Image Key'], $_page_image->data['Image File Format']);
                        $image_key         = $_page_image->id;
                    }
                }
            case 'Product':
                include_once 'class.Product.php';
                $product = new Product('id', $this->data['Page Parent Key']);
                if ($product->id and $product->get('Product Main Image Key')) {
                    $_page_image = new Image($product->get('Product Main Image Key'));
                    if ($_page_image->id) {
                        $page_image_source = sprintf("images/%07d.%s", $_page_image->data['Image Key'], $_page_image->data['Image File Format']);
                        $image_key         = $_page_image->id;
                    }
                }

            default:

                break;
        }


        $sql = sprintf(
            "UPDATE `Page Store Dimension` SET `Page Store Image Key`=%s ,`Page Store Image URL`=%s   WHERE `Page Key`=%d ", prepare_mysql($image_key), prepare_mysql($page_image_source), $this->id
        );
        $this->db->exec($sql);

        $this->data['Page Store Image Key'] = $image_key;
        $this->data['Page Store Image URL'] = $page_image_source;


    }

    function refresh_cache() {
        global $memcache_ip;


        $account      = new Account($this->db);
        $account_code = $account->get('Account Code');


        $template_response = '';

        // Tdo manage smarty cache
        /*

                if ($site->data['Site SSL'] == 'Yes') {
                    $site_protocol = 'https';
                } else {
                    $site_protocol = 'http';
                }
                $template_response = file_get_contents(
                    $site_protocol.'://'.$site->data['Site URL']."/maintenance/write_templates.php?parent=page_clean_cache&parent_key=".$this->id."&sk=x"
                );

                */

        $mem = new Memcached();
        $mem->addServer($memcache_ip, 11211);

        $mem->set('ECOMP'.md5($account_code.$this->get('Webpage Website Key').'/'.$this->get('Page Code')), $this->id, 172800);
        $mem->set(
            'ECOMP'.md5($account_code.$this->get('Webpage Website Key').'/'.strtolower($this->get('Page Code'))), $this->id, 172800
        );

        return $template_response;

    }

    function update_site_flag_key($value) {


        $sql = sprintf(
            "SELECT `Site Key`,`Site Flag Color` FROM  `Site Flag Dimension` WHERE `Site Flag Key`=%d", $value
        );

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                if ($row['Site Key'] != $this->data['Page Site Key']) {
                    $this->error = true;
                    $this->msg   = 'flag key not in this site';

                    return;
                }

                $old_key = $this->data['Site Flag Key'];

                $sql = sprintf(
                    "UPDATE `Page Store Dimension` SET `Site Flag Key`=%d ,`Site Flag`=%s WHERE `Page Key`=%d", $value, prepare_mysql($row['Site Flag Color']), $this->id
                );

                $this->db->exec($sql);
                $this->data['Site Flag Key'] = $value;
                $this->new_value             = $this->data['Site Flag Key'];
                $this->msg                   = _('Site flag changed');
                $this->updated               = true;

                /*
                $site = new Site($this->data['Page Site Key']);
                $site->update_page_flag_number($this->data['Site Flag Key']);
                if ($old_key) {
                    $site->update_page_flag_number($old_key);

                }
                */
            } else {
                $this->error = true;
                $this->msg   = 'flag key not found';
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


    }

    function load_data() {
        $sql = sprintf(
            "SELECT * FROM `Page Store Data Dimension` WHERE `Page Key`=%d", $this->id
        );

        $res = mysql_query($sql);
        if ($row = mysql_fetch_assoc($res)) {
            foreach ($row as $key => $value) {
                $this->data[$key] = $value;
            }

        }
    }

    function get_options() {

        if (array_key_exists('Page Options', $this->data)) {

            return unserialize($this->data['Page Options']);
        } else {
            return false;
        }

    }

    function update_thumbnail_key($image_key) {

        $old_value = $this->data['Page Snapshot Image Key'];
        if ($old_value != $image_key) {
            $this->updated;
            $this->data['Page Snapshot Image Key'] = $image_key;

            $sql = sprintf(
                "UPDATE `Page Dimension` SET `Page Snapshot Image Key`=%d ,`Page Snapshot Last Update`=NOW() WHERE `Page Key`=%d ", $this->data['Page Snapshot Image Key'], $this->id
            );
            mysql_query($sql);

            $sql = sprintf(
                "DELETE FROM  `Image Bridge` WHERE `Subject Type`='Website' AND `Subject Key`=%d ", $this->id

            );
            mysql_query($sql);

            if ($this->data['Page Snapshot Image Key']) {
                $sql = sprintf(
                    "INSERT INTO `Image Bridge` (`Subject Type`,`Subject Key`,`Image Key`) VALUES('Website',%d,%d)", $this->id, $image_key
                );
                print $sql;
                mysql_query($sql);
            }

        }

    }

    function reindex_items() {

        $this->updated = false;

        $website = get_object('Website', $this->get('Webpage Website Key'));

        if ($website->get('Website Theme') == 'theme_1') {

            $content_data = $this->get('Content Data');
            if (isset($content_data['blocks'])) {
                foreach ($content_data['blocks'] as $block_key => $block) {
                    switch ($block['type']) {
                        case 'category_products':
                            $this->reindex_category_products();
                            break;
                        case 'category_categories':
                            $this->reindex_category_products();
                            break;
                        case 'products':
                            $this->reindex_products();
                            break;
                        case 'see_also':
                            $this->reindex_see_also();
                            break;
                    }


                }
            }
            $this->updated = true;
        } else {
            if ($this->get('Webpage Scope') == 'Category Categories') {

                if ($this->get('Webpage Version') == 2) {


                    $this->updated = true;


                    $subjects = array();
                    $sql      = sprintf(
                        'SELECT `Webpage Scope Key` FROM `Category Bridge` LEFT JOIN `Page Store Dimension` ON (`Webpage Scope Key`=`Subject Key`   )  WHERE  ( `Webpage Scope`="Category Categories" OR  `Webpage Scope`="Category Products" ) AND   `Subject`="Category" AND `Category Key`=%d  ORDER BY `Webpage Scope Key` ',
                        $this->get('Webpage Scope Key')
                    );
                    if ($result = $this->db->query($sql)) {
                        foreach ($result as $row) {
                            if ($row['Webpage Scope Key']) {
                                $subjects[] = $row['Webpage Scope Key'];
                            }
                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        print "$sql\n";
                        exit;
                    }


                    foreach ($subjects as $item_key) {
                        $sql = sprintf(
                            'UPDATE `Category Webpage Index` SET `Category Webpage Index Subject Type`="Subject" WHERE `Category Webpage Index Webpage Key`=%d  AND `Category Webpage Index Category Key`=%d   ', $this->id, $item_key
                        );
                        $this->db->exec($sql);

                    }

                    // print_r($subjects);


                    $content_data = $this->get('Content Data');


                    //     print count($subjects)."sss\n";


                    if ($content_data != '') {


                        foreach ($content_data['sections'] as $section_stack_index => $section_data) {


                            $content_data['sections'][$section_stack_index]['items'] = get_website_section_items($this->db, $section_data);


                        }
                    }
                    $this->update(array('Page Store Content Data' => json_encode($content_data)), 'no_history');

                    $_subjects_in_webpage = array();

                    $sql = sprintf(
                        "SELECT `Category Webpage Index Category Key`  ,`Category Webpage Index Section Key`          FROM `Category Webpage Index` CWI  WHERE  `Category Webpage Index Webpage Key`=%d AND `Category Webpage Index Subject Type`='Subject'  ORDER BY `Category Webpage Index Category Key` ",
                        $this->id


                    );


                    if ($result = $this->db->query($sql)) {
                        foreach ($result as $row) {


                            $_subjects_in_webpage[] = $row['Category Webpage Index Category Key'];

                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        print "$sql\n";
                        exit;
                    }
                    //print_r($subjects);
                    //print_r($_subjects_in_webpage);

                    //print count($_subjects_in_webpage)."\n";


                    $to_add    = array_diff($subjects, $_subjects_in_webpage);
                    $to_remove = array_diff($_subjects_in_webpage, $subjects);


                    //print_r($to_add);
                    //print_r($to_remove);


                    foreach ($to_add as $item_key) {
                        $this->add_section_item($item_key);


                    }


                    // print_r($_to_remove);

                    foreach ($to_remove as $item_key) {
                        $this->remove_section_item($item_key);

                    }


                }
            }
        }


        if ($this->get('Webpage Template Filename') == 'category_categories') {

            $this->reindex_category_categories();
            $this->updated = true;

            return;
        }
        if ($this->get('Webpage Template Filename') == 'category_products') {

            $this->reindex_category_products();
            $this->updated = true;

            return;
        }


    }

    function reindex_category_products() {
        $content_data = $this->get('Content Data');

        $block_key = false;
        foreach ($content_data['blocks'] as $_block_key => $_block) {
            if ($_block['type'] == 'category_products') {
                $block     = $_block;
                $block_key = $_block_key;
                break;
            }
        }

        if (!$block_key) {
            return;
        }

        $sql = sprintf(
            "SELECT P.`Product ID` 
                  FROM `Category Bridge` B  LEFT JOIN `Product Dimension` P ON (`Subject Key`=P.`Product ID`)  
                WHERE  `Category Key`=%d  AND `Product Web State` IN  ('For Sale','Out of Stock')   ORDER BY `Product Web State`", $this->data['Webpage Scope Key']
        );

        $items                  = array();
        $items_product_id_index = array();

        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $items[$row['Product ID']]                  = $row;
                $items_product_id_index[$row['Product ID']] = $row['Product ID'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $offline_items_product_id_index[$row['Product ID']] = $row['Product ID'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }

        foreach ($block['items'] as $item_key => $item) {
            if ($item['type'] == 'product') {
                if (in_array($item['product_id'], $items_product_id_index)) {


                    $product = get_object('Public_Product', $item['product_id']);
                    $product->load_webpage();


                    $content_data['blocks'][$block_key]['items'][$item_key]['web_state']    = $product->get('Web State');
                    $content_data['blocks'][$block_key]['items'][$item_key]['price']        = $product->get('Price');
                    $content_data['blocks'][$block_key]['items'][$item_key]['rrp']          = $product->get('RRP');
                    $content_data['blocks'][$block_key]['items'][$item_key]['code']         = $product->get('Code');
                    $content_data['blocks'][$block_key]['items'][$item_key]['name']         = $product->get('Name');
                    $content_data['blocks'][$block_key]['items'][$item_key]['link']         = $product->webpage->get('URL');
                    $content_data['blocks'][$block_key]['items'][$item_key]['webpage_code'] = $product->webpage->get('Webpage Code');
                    $content_data['blocks'][$block_key]['items'][$item_key]['webpage_key']  = $product->webpage->id;


                    $content_data['blocks'][$block_key]['items'][$item_key]['out_of_stock_class'] = $product->get('Out of Stock Class');
                    $content_data['blocks'][$block_key]['items'][$item_key]['out_of_stock_label'] = $product->get('Out of Stock Label');
                    $content_data['blocks'][$block_key]['items'][$item_key]['sort_code']          = $product->get('Code File As');
                    $content_data['blocks'][$block_key]['items'][$item_key]['sort_name']          = mb_strtolower($product->get('Product Name'));


                    unset($items_product_id_index[$item['product_id']]);
                } else {
                    unset($content_data['blocks'][$block_key]['items'][$item_key]);

                }

            }
        }

        foreach ($items_product_id_index as $product_id) {

            $product = get_object('Public_Product', $product_id);
            $product->load_webpage();


            $item = array(
                'type'                 => 'product',
                'product_id'           => $product_id,
                'web_state'            => $product->get('Web State'),
                'price'                => $product->get('Price'),
                'rrp'                  => $product->get('RRP'),
                'header_text'          => '',
                'code'                 => $product->get('Code'),
                'name'                 => $product->get('Name'),
                'link'                 => $product->webpage->get('URL'),
                'webpage_code'         => $product->webpage->get('Webpage Code'),
                'webpage_key'          => $product->webpage->id,
                'image_src'            => $product->get('Image'),
                'image_mobile_website' => '',
                'image_website'        => '',
                'out_of_stock_class'   => $product->get('Out of Stock Class'),
                'out_of_stock_label'   => $product->get('Out of Stock Label'),
                'sort_code'            => $product->get('Code File As'),
                'sort_name'            => mb_strtolower($product->get('Product Name')),


            );


            array_unshift($content_data['blocks'][$block_key]['items'], $item);
        }


        $this->update_field_switcher('Page Store Content Data', json_encode($content_data), 'no_history');

        $sql = sprintf('DELETE FROM `Website Webpage Scope Map` WHERE `Website Webpage Scope Webpage Key`=%d AND `Website Webpage Scope Type`="Category_Products_Item" ', $this->id);
        $this->db->exec($sql);

        $index = 0;
        foreach ($content_data['blocks'][$block_key]['items'] as $item) {
            if ($item['type'] == 'product') {
                $sql = sprintf(
                    'INSERT INTO `Website Webpage Scope Map` (`Website Webpage Scope Website Key`,`Website Webpage Scope Webpage Key`,`Website Webpage Scope Scope`,`Website Webpage Scope Scope Key`,`Website Webpage Scope Type`,`Website Webpage Scope Index`) VALUES (%d,%d,%s,%d,%s,%d) ',
                    $this->get('Webpage Website Key'), $this->id, prepare_mysql('Product'), $item['product_id'], prepare_mysql('Category_Products_Item'), $index

                );


                $this->db->exec($sql);
                $index++;

            }


        }


    }

    function update_field_switcher($field, $value, $options = '', $metadata = '') {


        switch ($field) {

            case 'Webpage See Also':

                $this->update(
                    array(
                        'See Also' => $value
                    ), $options
                );


                break;

            case('Webpage Code'):
                $sql = sprintf('UPDATE `Page Dimension` SET `Page Code`=%s WHERE `Page Key`=%d ', prepare_mysql($value), $this->id);
                $this->db->exec($sql);
                $this->update_field($field, $value, $options);
                $this->update_url();


                $this->update_metadata = array(
                    'class_html' => array(
                        'Webpage_URL' => $this->get('Webpage URL'),

                    ),

                );


                break;


            case 'Webpage Browser Title':

                $sql = sprintf('UPDATE `Page Dimension` SET `Page Title`=%s WHERE `Page Key`=%d ', prepare_mysql($value), $this->id);
                $this->db->exec($sql);
                $this->update_field($field, $value, $options);
                break;
            case 'Webpage Name':


                $sql = sprintf('UPDATE `Page Dimension` SET `Page Short Title`=%s WHERE `Page Key`=%d ', prepare_mysql($value), $this->id);
                $this->db->exec($sql);
                $sql = sprintf('UPDATE `Page Store Dimension` SET `Page Store Title`=%s WHERE `Page Key`=%d ', prepare_mysql($value), $this->id);
                $this->db->exec($sql);
                $this->update_field($field, $value, $options);
                break;

            case 'Webpage Meta Description':


                $sql = sprintf('UPDATE `Page Store Dimension` SET `Page Store Description`=%s WHERE `Page Key`=%d ', prepare_mysql($value), $this->id);
                $this->db->exec($sql);
                $this->update_field($field, $value, $options);
                break;


            case 'Store Email':
            case 'Store Company Name':
            case 'Store VAT Number':
            case 'Store Company Number':
            case 'Store Telephone':
            case 'Store Address':
            case 'Store Google Map URL':
                include_once('class.Store.php');
                $store         = new Store($this->data['Webpage Store Key']);
                $store->editor = $this->editor;

                $store->update_field_switcher($field, $value, $options);
                $this->updated = $store->updated;
                $this->error   = $store->error;
                $this->msg     = $store->msg;

                break;


            case 'History Note':
                $this->add_note($value, '', '', $metadata['deletable'], 'Notes', false, false, false, 'Webpage', false, 'Webpage Publishing', false);

                break;


            case 'Scope Metadata':

                $this->update_field('Webpage '.$field, $value, $options);
                break;

            case('Webpage Scope'):
            case('Webpage Scope Key'):
            case('Webpage Scope Metadata'):
            case('Webpage Website Key'):
            case('Webpage Store Key'):
            case ('Webpage Redirection Code'):

            case('Webpage Type Key'):
            case 'Webpage Version':
            case 'Webpage Launch Date':
            case 'Webpage Name':
            case 'Webpage Browser Title':
            case 'Webpage Meta Description':
            case 'Webpage URL':

                $this->update_field($field, $value, $options);
                break;


            case 'Webpage Template Filename':


                if ($value == 'blank') {


                    $sql = sprintf('UPDATE `Page Store Dimension` SET `Page Store Content Display Type`="Source" WHERE `Page Key`=%d ', $this->id);
                    $this->db->exec($sql);

                } else {


                    $sql = sprintf('UPDATE `Page Store Dimension` SET `Page Store Content Display Type`="Template" WHERE `Page Key`=%d ', $this->id);
                    $this->db->exec($sql);

                    $sql = sprintf('UPDATE `Page Store Dimension` SET `Page Store Content Template Filename`=%s WHERE `Page Key`=%d ', prepare_mysql($value), $this->id);
                    $this->db->exec($sql);


                }


                $this->update_field($field, $value, $options);

                $this->update_version();
                $this->publish();

                break;


            case('Webpage Launching Date'):


                if ($value == '00:00:00') {
                    $value = '';
                }

                $this->update_content_data('_launch_date', $value, $options);

                if ($value == '') {
                    $this->update_content_data('show_countdown', false, 'no_history');

                } else {
                    $this->update_content_data('show_countdown', true, 'no_history');

                }


                break;


            case 'Related Products':

                $value = json_decode($value, true);
                //print_r($value);

                $product_page_keys = array();
                foreach ($value as $product_id) {


                    $sql = sprintf(
                        'SELECT `Page Key` FROM `Page Store Dimension` WHERE `Page Store Section Type`="Product"  AND  `Page Parent Key`=%d ', $product_id
                    );

                    if ($result = $this->db->query($sql)) {
                        if ($row = $result->fetch()) {
                            $product_page_keys[$product_id] = $row['Page Key'];
                        } else {
                            $this->error = true;
                            $this->msg   = 'Product and/or page no found';
                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        exit;
                    }


                }


                $sql = sprintf(
                    'DELETE FROM `Webpage Related Product Bridge` WHERE `Webpage Related Product Page Key`=%d %s', $this->id, (count($value) > 0 ? sprintf(
                    'and `Webpage Related Product Product ID` not in (%s)', join(',', $value)
                ) : '')
                );
                //print $sql;
                $this->db->exec($sql);

                $order_value = 1;
                foreach ($value as $product_id) {

                    if (isset($product_page_keys[$product_id])) {


                        $sql = sprintf(
                            'INSERT INTO  `Webpage Related Product Bridge` (`Webpage Related Product Page Key`,`Webpage Related Product Product ID`,`Webpage Related Product Product Page Key`,`Webpage Related Product Published Order`,`Webpage Related Product Order`) VALUES (%d,%d,%d,%d,%d)  ON DUPLICATE KEY UPDATE `Webpage Related Product Order`=%d,`Webpage Related Product Published Order`=%d ',
                            $this->id, $product_id, $product_page_keys[$product_id], $order_value, $order_value, $order_value, $order_value


                        );


                        $this->db->exec($sql);
                        $order_value++;

                    }
                }


                $this->refresh_cache();


                break;

            case 'See Also':

                $value = json_decode($value, true);
                // print_r($value);

                $this->update_field(
                    'Page Store See Also Type', $value['type'], $options
                );
                $updated = $this->updated;
                if ($value['type'] == 'Auto') {
                    $this->update_field(
                        'Number See Also Links', $value['number_links'], $options
                    );
                    if ($this->updated) {
                        $updated = $this->updated;
                    }

                    //if ($updated) {
                    $this->update_see_also();
                    //}

                    $this->updated = $updated;
                } else {

                    //print_r($value);


                    $sql = sprintf(
                        'DELETE FROM `Page Store See Also Bridge` WHERE `Page Store Key`=%d %s', $this->id, (count($value['manual_links']) > 0 ? sprintf(
                        'and `Page Store See Also Key` not in (%s)', join(',', $value['manual_links'])
                    ) : '')
                    );

                    $this->db->exec($sql);

                    $order_value = 1;
                    foreach ($value['manual_links'] as $link_key) {
                        $sql = sprintf(
                            'INSERT INTO  `Page Store See Also Bridge` (`Page Store Key`,`Page Store See Also Key`,`Correlation Type`,`Correlation Value`,`Webpage See Also Order`) VALUES (%d,%d,"Manual",NULL,%d)  ON DUPLICATE KEY UPDATE `Correlation Type`="Manual",`Webpage See Also Order`=%d ',
                            $this->id, $link_key, $order_value, $order_value


                        );
                        $this->db->exec($sql);
                        //print "$sql\n";
                        $order_value++;
                    }


                    $this->update_field(
                        'Number See Also Links', count($value['manual_links']), $options
                    );


                }
                $this->refresh_cache();
                break;

            case 'Found In':

                $this->update_found_in($value);
                $this->refresh_cache();
                break;
            case('Page Store See Also Type'):
                $this->update_field(
                    'Page Store See Also Type', $value, $options
                );
                if ($value == 'Auto') {
                    $this->update_see_also();
                }
                break;


            case('Page See Also Last Updated'):

                $this->update_field($field, $value, $options);


                break;


            case('Webpage State'):


                $this->update_state($value, $options);
                // $this->refresh_cache();
                break;

            case('Page Store CSS'):
            case('Number See Also Links'):
            case('Number Found In Links'):
            case('Page Footer Height'):
                $this->update_field($field, $value, $options);
                $this->update_store_search();
                break;

            case 'Page Store Content Data':

                // post edit content data
                include_once('utils/image_functions.php');

                $content_data = json_decode($value, true);


                if (isset($content_data['blocks'])) {
                    foreach ($content_data['blocks'] as $block_key => $block) {

                        if ($block['type'] == 'blackboard') {

                            $items = array();
                            $index = 0;

                            $max_images = count($block['texts']);
                            if ($max_images == 0) {
                                $max_images = 1;
                            }

                            $counter = 0;
                            foreach ($block['images'] as $key_item => $item) {
                                $index        = $index + 10;
                                $item['type'] = 'image';


                                if (empty($item['image_website'])) {
                                    $image_website = $item['src'];
                                    if (preg_match('/id=(\d+)/', $item['src'], $matches)) {
                                        $image_key = $matches[1];

                                        $width  = $item['width'] * 2;
                                        $height = $item['height'] * 2;


                                        $image_website = create_cached_image($image_key, $width, $height, 'do_not_enlarge');

                                    }


                                    $content_data['blocks'][$block_key]['images'][$key_item]['image_website'] = $image_website;
                                    $item['image_website']                                                    = $image_website;

                                }

                                $items[$index] = $item;
                                $counter++;

                            }
                            $index = 5;
                            foreach ($block['texts'] as $item) {
                                $index         = $index + 10;
                                $item['type']  = 'text';
                                $items[$index] = $item;
                            }

                            ksort($items);


                            $image_counter = 0;
                            $mobile_html   = '';
                            $tablet_html   = '';


                            foreach ($items as $item) {
                                if ($item['type'] == 'text') {
                                    $tablet_html .= '<p>'.$item['text'].'</p>';
                                }
                                if ($item['type'] == 'image') {

                                    if ($image_counter >= $max_images) {
                                        break;
                                    }

                                    if ($image_counter % 2 == 0) {
                                        $tablet_html .= '<img src="'.$item['image_website'].'" style="width:45%;float:left;margin-right:20px;" alt="'.$item['title'].'">';

                                    } else {
                                        $tablet_html .= '<img src="'.$item['image_website'].'" style="width:40%;float:right;margin-left:20px;" alt="'.$item['title'].'">';

                                    }


                                    $image_counter++;

                                }

                            }
                            $image_counter = 0;

                            foreach ($items as $key_item => $item) {
                                if ($item['type'] == 'image') {
                                    $ratio = $item['width'] / $item['height'];
                                    //print "$ratio\n";

                                    if ($ratio > 7.5) {
                                        $mobile_html .= '<img src="'.$item['image_website'].'" style="width:100%;" alt="'.$item['title'].'">';
                                        unset($items[$key_item]);
                                        break;
                                    }


                                }

                            }


                            foreach ($items as $item) {
                                if ($item['type'] == 'text') {
                                    $mobile_html .= '<p>'.$item['text'].'</p>';
                                }
                                if ($item['type'] == 'image') {


                                    if ($image_counter % 2 == 0) {
                                        $mobile_html .= '<img src="'.$item['image_website'].'" style="width:40%;padding-top:15px;float:left;margin-right:15px;" alt="'.$item['title'].'">';

                                    } else {
                                        $mobile_html .= '<img src="'.$item['image_website'].'" style="width:40%;padding-top:15px;float:right;margin-left:15px;" alt="'.$item['title'].'">';

                                    }


                                    $image_counter++;

                                }

                            }


                            $mobile_html = preg_replace('/\<p\>\<br\>\<\/p\>/', '', $mobile_html);
                            $mobile_html = preg_replace('/\<p style\=\"text-align: left;\"\><br\>\<\/p\>/', '', $mobile_html);
                            $tablet_html = preg_replace('/\<p\>\<br\>\<\/p\>/', '', $tablet_html);
                            $tablet_html = preg_replace('/\<p style\=\"text-align: left;\"\><br\>\<\/p\>/', '', $tablet_html);
                            // print_r($mobile_html);
                            $content_data['blocks'][$block_key]['mobile_html'] = $mobile_html;
                            $content_data['blocks'][$block_key]['tablet_html'] = $tablet_html;

                        } elseif ($block['type'] == 'category_products') {
                            foreach ($block['items'] as $item_key => $item) {
                                if ($item['type'] == 'product') {
                                    if (empty($item['image_mobile_website'])) {
                                        $image_mobile_website = $item['image_src'];
                                        if (preg_match('/id=(\d+)/', $item['image_src'], $matches)) {
                                            $image_key = $matches[1];

                                            $image_mobile_website = create_cached_image($image_key, 340, 214);

                                        }


                                        $content_data['blocks'][$block_key]['items'][$item_key]['image_mobile_website'] = $image_mobile_website;


                                    }

                                    if (empty($item['image_website'])) {
                                        $image_website = $item['image_src'];
                                        if (preg_match('/id=(\d+)/', $item['image_src'], $matches)) {
                                            $image_key     = $matches[1];
                                            $image_website = create_cached_image($image_key, 432, 330, 'fit_highest');
                                        }


                                        $content_data['blocks'][$block_key]['items'][$item_key]['image_website'] = $image_website;


                                    }
                                } elseif ($item['type'] == 'image') {

                                    if (empty($item['image_website'])) {
                                        $image_website = $item['image_src'];
                                        if (preg_match('/id=(\d+)/', $item['image_src'], $matches)) {
                                            $image_key = $matches[1];

                                            if ($content_data['blocks'][$block_key]['item_headers']) {
                                                $height = 330;
                                            } else {
                                                $height = 290;
                                            }


                                            switch ($item['size_class']) {
                                                case 'panel_1':
                                                    $width = 226;

                                                    break;
                                                case 'panel_2':
                                                    $width = 470;
                                                    break;
                                                case 'panel_3':
                                                    $width = 714;
                                                    break;
                                                case 'panel_4':
                                                    $width = 958;
                                                    break;
                                                case 'panel_5':
                                                    $width = 1202;
                                                    break;

                                            }

                                            $image_website = create_cached_image($image_key, $width, $height);
                                        }


                                        $content_data['blocks'][$block_key]['items'][$item_key]['image_website'] = $image_website;


                                    }


                                }

                            }

                        } elseif ($block['type'] == 'products') {
                            foreach ($block['items'] as $item_key => $item) {

                                if (empty($item['image_mobile_website'])) {
                                    $image_mobile_website = $item['image_src'];
                                    if (preg_match('/id=(\d+)/', $item['image_src'], $matches)) {
                                        $image_key = $matches[1];

                                        $image_mobile_website = create_cached_image($image_key, 340, 214);

                                    }


                                    $content_data['blocks'][$block_key]['items'][$item_key]['image_mobile_website'] = $image_mobile_website;


                                }

                                if (empty($item['image_website'])) {
                                    $image_website = $item['image_src'];
                                    if (preg_match('/id=(\d+)/', $item['image_src'], $matches)) {
                                        $image_key     = $matches[1];
                                        $image_website = create_cached_image($image_key, 432, 330, 'fit_highest');
                                    }


                                    $content_data['blocks'][$block_key]['items'][$item_key]['image_website'] = $image_website;


                                }
                            }

                        } elseif ($block['type'] == 'see_also') {
                            foreach ($block['items'] as $item_key => $item) {

                                if (empty($item['image_mobile_website'])) {
                                    $image_mobile_website = $item['image_src'];
                                    if (preg_match('/id=(\d+)/', $item['image_src'], $matches)) {
                                        $image_key = $matches[1];

                                        $image_mobile_website = create_cached_image($image_key, 320, 200);

                                    }


                                    $content_data['blocks'][$block_key]['items'][$item_key]['image_mobile_website'] = $image_mobile_website;


                                }

                                if (empty($item['image_website'])) {
                                    $image_website = $item['image_src'];
                                    if (preg_match('/id=(\d+)/', $item['image_src'], $matches)) {
                                        $image_key     = $matches[1];
                                        $image_website = create_cached_image($image_key, 432, 330, 'fit_highest');
                                    }


                                    $content_data['blocks'][$block_key]['items'][$item_key]['image_website'] = $image_website;


                                }
                            }

                        } elseif ($block['type'] == 'category_categories') {


                            foreach ($block['sections'] as $section_key => $section) {
                                foreach ($section['items'] as $item_key => $item) {

                                    if ($item['type'] == 'category') {
                                        if (empty($item['image_mobile_website'])) {
                                            $image_mobile_website = $item['image_src'];
                                            if (preg_match('/id=(\d+)/', $item['image_src'], $matches)) {
                                                $image_key = $matches[1];

                                                $image_mobile_website = create_cached_image($image_key, 320, 200);

                                            }


                                            $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['image_mobile_website'] = $image_mobile_website;


                                        }

                                        if (empty($item['image_website'])) {
                                            $image_website = $item['image_src'];
                                            if (preg_match('/id=(\d+)/', $item['image_src'], $matches)) {
                                                $image_key     = $matches[1];
                                                $image_website = create_cached_image($image_key, 432, 330, 'fit_highest');
                                            }


                                            $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['image_website'] = $image_website;


                                        }
                                    } elseif ($item['type'] == 'image') {

                                        if (empty($item['image_website'])) {
                                            $image_website = $item['image_src'];
                                            if (preg_match('/id=(\d+)/', $item['image_src'], $matches)) {
                                                $image_key = $matches[1];
                                                $height    = 220;
                                                switch ($item['size_class']) {
                                                    case 'panel_1':
                                                        $width = 226;

                                                        break;
                                                    case 'panel_2':
                                                        $width = 470;
                                                        break;
                                                    case 'panel_3':
                                                        $width = 714;
                                                        break;
                                                    case 'panel_4':
                                                        $width = 958;
                                                        break;
                                                    case 'panel_5':
                                                        $width = 1202;
                                                        break;

                                                }

                                                $image_website = create_cached_image($image_key, $width, $height);
                                            }


                                            $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['image_website'] = $image_website;


                                        }


                                    }
                                }


                            }
                        }


                    }

                }


                $value = json_encode($content_data);

                $this->update_field('Page Store Content Data', $value, $options);
                $this->update_store_search();

                // Todo remove after migration
                if ($this->get('Webpage Scope') == 'Category Categories' and $this->get('Webpage Template Filename') != 'category_categories') {

                    include_once 'class.Website.php';
                    $website = new Website($this->get('Webpage Website Key'));

                    if ($website->get('Website Theme') == 'theme_1') {
                        $this->update_category_webpage_index();
                    }
                }


                break;

            case 'Website Registration Type':


                $old_content_data = $this->get('Content Data');
                if (empty($old_content_data['backup'])) {
                    $backup = array(
                        'Open'         => '',
                        'Closed'       => '',
                        'ApprovedOnly' => ''
                    );
                } else {
                    $backup = $old_content_data['backup'];
                }
                unset($old_content_data['backup']);


                $website = get_object('website', $this->get('Webpage Website Key'));

                $old_type = $website->get('Website Registration Type');

                $website->editor = $this->editor;
                $website->update_field($field, $value, $options);
                if ($website->updated) {
                    $this->updated;


                    //print_r($backup);
                    //print_r($old_type);

                    $backup[$old_type] = $old_content_data;

                    if (isset($backup[$value])) {
                        $this->update(array('Page Store Content Data' => json_encode($backup[$value])), 'no_history');
                    } else {
                        $this->reset_object();
                    }
                    $this->update_content_data('backup', $backup);


                }


                break;


            default:

                $base_data = $this->base_data();
                if (array_key_exists($field, $base_data)) {

                    if ($value != $this->data[$field]) {


                        $this->update_field($field, $value, $options);
                    }
                } else {
                    $this->error = true;
                    $this->msg   = "field not found ($field)";

                }

        }


    }

    function update_version() {

        if (in_array(
                $this->get('Page Store Content Template Filename'), array(
                                                                      'products_showcase',
                                                                      'categories_showcase'
                                                                  )
            ) and $this->get('Page Store Content Display Type') == 'Template') {
            $version = 2;
        } elseif ($this->get('Webpage Scope') == 'Product') {
            $version = 2;

        } else {
            $version = 1;

        }


        $this->update(array('Webpage Version' => $version), 'no_history');

    }

    function publish($note = '') {

        $website = get_object('Website', $this->get('Webpage Website Key'));


        if ($website->get('Website Status') != 'Active') {
            $this->error = true;
            $this->msg   = 'Website not active';

            return;
        }


        if ($this->get('Webpage State') == 'Offline' or $this->get('Webpage State') == 'InProcess' or $this->get('Webpage State') == 'Ready') {


            $this->update_state('Online');

        }
        if ($this->get('Webpage Launch Date') == '') {
            $this->update(array('Webpage Launch Date' => gmdate('Y-m-d H:i:s')), 'no_history');
            $msg = _('Webpage launched');
        } else {
            $msg = _('Webpage published');
        }


        $content_data = $this->get('Content Data');


        $sql = sprintf(
            'UPDATE `Page Store Dimension` SET  `Page Store Content Published Data`=`Page Store Content Data`,`Page Store Published CSS`=`Page Store CSS` WHERE `Page Key`=%d ', $this->id
        );

        $this->db->exec($sql);


        $history_data = array(
            'Date'              => gmdate('Y-m-d H:i:s'),
            'Direct Object'     => 'Webpage',
            'Direct Object Key' => $this->id,
            'History Details'   => '',
            'History Abstract'  => $msg.($note != '' ? ', '.$note : ''),
        );

        $history_key = $this->add_history($history_data, $force_save = true);
        $sql         = sprintf(
            "INSERT INTO `Webpage Publishing History Bridge` VALUES (%d,%d,'No','No','Deployment')", $this->id, $history_key
        );


        $this->db->exec($sql);


        if ($this->get('Webpage Scope') == 'Category Products') {


            include_once 'class.Page.php';

            $sql = sprintf(
                'UPDATE  `Product Category Index` SET  `Product Category Index Published Stack`=`Product Category Index Stack`,`Product Category Index Content Published Data`=`Product Category Index Content Data` WHERE `Product Category Index Category Key`=%d ',
                $this->get('Webpage Scope Key')
            );
            $this->db->exec($sql);


            $sql = sprintf('SELECT `Product Category Index Product ID` FROM `Product Category Index`    WHERE `Product Category Index Website Key`=%d', $this->id);


            //print "$sql\n";

            if ($result = $this->db->query($sql)) {
                foreach ($result as $row) {

                    $webpage = new Page('scope', 'Product', $row['Product Category Index Product ID']);

                    // print_r($webpage);
                    //exit;

                    if ($webpage->id) {


                        // if ($webpage->get('Webpage Launch Date') == '') {


                        //  print $webpage->get('Webpage Code')."\n";

                        $webpage->publish();
                        //  }
                    }

                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                print "$sql\n";
                exit;
            }


        } elseif ($this->get('Webpage Scope') == 'Product') {


            if (isset($content_data['description_block']['content'])) {
                $web_text = $content_data['description_block']['content'];
            } else {
                $web_text = '';
            }


            $product = get_object('Product', $this->get('Webpage Scope Key'));
            $product->fast_update(array('Product Published Webpage Description' => $web_text));

        }

        $sql = sprintf(
            'UPDATE  `Webpage Related Product Bridge` SET  `Webpage Related Product Content Published Data`=`Webpage Related Product Content Data`,`Webpage Related Product Published Order`=`Webpage Related Product Order` WHERE `Webpage Related Product Page Key`=%d ',
            $this->id
        );
        $this->db->exec($sql);

        $this->get_data('id', $this->id);


        if (isset($content_data['sections'])) {
            $sections = array();


            foreach ($content_data['sections'] as $section_stack_index => $section_data) {

                $categories                     = get_website_section_items($this->db, $section_data);
                $sections[$section_data['key']] = array(
                    'data'       => $section_data,
                    'categories' => $categories
                );
            }

        }


        $this->update_metadata = array(
            'class_html'    => array(
                'Webpage_State_Icon'    => $this->get('State Icon'),
                'Webpage_State'         => $this->get('State'),
                'preview_publish_label' => _('Publish')

            ),
            'hide_by_id'    => array(
                'republish_webpage_field',
                'launch_webpage_field'
            ),
            'show_by_id'    => array('unpublish_webpage_field'),
            'visible_by_id' => array('link_to_live_webpage'),
        );


    }

    function update_state($value, $options = '') {

        $old_state = $this->data['Webpage State'];


        $this->update_field('Page State', $value, 'no_history');
        $this->update_field('Webpage State', $value, $options);


        if ($old_state != $this->data['Webpage State']) {


            if ($this->data['Webpage State'] == 'Offline') {

                $this->update_field('Webpage Take Down Date', gmdate('Y-m-d H:i:s'), 'no_history');

            }

            $sql = sprintf(
                "INSERT INTO `Page State Timeline`  (`Page Key`,`Site Key`,`Store Key`,`Date`,`State`,`Operation`) VALUES (%d,%d,%d,%s,%s,'Change') ", $this->id, $this->data['Webpage Website Key'], $this->data['Webpage Store Key'], prepare_mysql(gmdate('Y-m-d H:i:s')),
                prepare_mysql($this->data['Webpage State'])

            );

            $this->db->exec($sql);


            $sql = sprintf(
                "UPDATE `Page Product Dimension` SET `State`=%s WHERE `Page Key`=%d", prepare_mysql($this->data['Page State']), $this->id
            );
            $this->db->exec($sql);


            $sql = sprintf(
                "SELECT `Page Store Key`  FROM  `Page Store See Also Bridge` WHERE `Page Store See Also Key`=%d ", $this->id
            );


            if ($result = $this->db->query($sql)) {
                foreach ($result as $row) {
                    $_page = new Page ($row['Page Store Key']);
                    $_page->update_see_also();
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                print "$sql\n";
                exit;
            }


            $this->reindex_items();
            if ($this->updated) {
                $this->publish();
            }

            $sql = sprintf(
                'SELECT `Category Webpage Index Webpage Key` FROM `Category Webpage Index` WHERE `Category Webpage Index Category Webpage Key`=%d  GROUP BY `Category Webpage Index Webpage Key` ', $this->id
            );


            if ($result = $this->db->query($sql)) {
                foreach ($result as $row) {
                    $webpage = new Page($row['Category Webpage Index Webpage Key']);
                    $webpage->reindex_items();
                    if ($webpage->updated) {
                        $webpage->publish();
                    }
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                print "$sql\n";
                exit;
            }


            $this->updated = true;

        }


        $show = array();
        $hide = array();


        if ($this->get('Webpage State') == 'Ready') {

            $show = array('set_as_not_ready_webpage_field');
            $hide = array('set_as_ready_webpage_field');
        } elseif ($this->get('Webpage State') == 'InProcess') {


            $show = array('set_as_ready_webpage_field');
            $hide = array('set_as_not_ready_webpage_field');
        }


        $this->update_metadata = array(
            'class_html' => array(
                'Webpage_State_Icon' => $this->get('State Icon'),
                'Webpage_State'      => $this->get('State'),


            ),
            'hide_by_id' => $hide,
            'show_by_id' => $show


        );


    }

    function update_content_data($field, $value, $options = '') {

        $content_data = $this->get('Content Data');

        $content_data[$field] = $value;

        $this->update_field('Page Store Content Data', json_encode($content_data), $this->no_history);


    }

    function update_found_in($parent_keys) {


        $parent_keys = array_unique($parent_keys);

        $sql = sprintf(
            "SELECT `Page Store Found In Key` FROM  `Page Store Found In Bridge` WHERE `Page Store Key`=%d", $this->id
        );

        $keys_to_delete = array();
        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {

                if (!in_array($row['Page Store Found In Key'], $parent_keys)) {
                    $sql = sprintf(
                        "DELETE FROM  `Page Store Found In Bridge` WHERE `Page Store Key`=%d AND `Page Store Found In Key`=%d   ", $this->id, $row['Page Store Found In Key']
                    );

                    $this->db->exec($sql);
                }

            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        foreach ($parent_keys as $parent_key) {

            if ($this->id != $parent_key and is_numeric($parent_key) and $parent_key > 0) {

                $sql = sprintf(
                    "INSERT INTO `Page Store Found In Bridge`  (`Page Store Key`,`Page Store Found In Key`)  VALUES (%d,%d)  ", $this->id, $parent_key
                );
                $this->db->exec($sql);


            }

        }


        $number_found_in_links = 0;

        $sql = sprintf(
            "SELECT count(*) AS num FROM  `Page Store Found In Bridge` WHERE `Page Store Key`=%d", $this->id
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $number_found_in_links = $row['num'];

            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $this->update(
            array('Number Found In Links' => $number_found_in_links), 'no_history'
        );


    }

    function update_store_search() {


        //todo redo this

        if ($this->data['Page Type'] == 'Store') {


            $sql = sprintf(
                "INSERT INTO `Page Store Search Dimension` VALUES (%d,%d,%s,%s,%s,%s)  ON DUPLICATE KEY UPDATE `Page Store Title`=%s ,`Page Store Resume`=%s ,`Page Store Content`=%s  ", $this->id, $this->data['Page Site Key'], prepare_mysql($this->data['Page URL']),
                prepare_mysql($this->data['Page Title'], false), prepare_mysql($this->data['Page Store Description'], false), prepare_mysql($this->get_plain_content(), false), prepare_mysql($this->data['Page Title'], false),
                prepare_mysql($this->data['Page Store Description'], false), prepare_mysql($this->get_plain_content(), false)
            );
            $this->db->exec($sql);


        }

    }

    function update_category_webpage_index() {


        if ($this->get('Webpage Scope') == 'Category Categories') {

            include_once 'class.Website.php';
            $website = new Website($this->get('Webpage Website Key'));

            if ($website->get('Website Theme') == 'theme_1' and false) {


                $sql = sprintf('DELETE FROM  `Category Webpage Index` WHERE `Category Webpage Index Webpage Key`=%d  ', $this->id);
                $this->db->exec($sql);


                $content_data = $this->get('Content Data');

                $stack              = 0;
                $anchor_section_key = 0;

                foreach ($content_data['catalogue']['items'] as $item) {


                    $sql = sprintf(
                        'INSERT INTO `Category Webpage Index` (`Category Webpage Index Section Key`,`Category Webpage Index Content Data`,
                          `Category Webpage Index Parent Category Key`,`Category Webpage Index Category Key`,`Category Webpage Index Webpage Key`,`Category Webpage Index Category Webpage Key`,`Category Webpage Index Stack`) VALUES (%d,%s,%d,%d,%d,%d,%d) ',
                        $anchor_section_key, prepare_mysql(json_encode($item)), $this->get('Webpage Scope Key'), $item['category_key'], $this->id, $item['webpage_key'], $stack
                    );


                    $this->db->exec($sql);
                    $stack++;


                }


            }


        }


    }

    function reset_object() {


        $website = get_object('Website', $this->get('Webpage Website Key'));

        if ($this->get('Webpage Scope') == 'Category Products') {

            include_once 'class.Category.php';

            $category = new Category($this->get('Webpage Scope Key'));

            if ($website->get('Website Theme') == 'theme_1x') {


                $category->create_category_webpage_index();


                $content_data = array(


                    'blocks' => array(
                        'intro'    => 1,
                        'products' => 1

                    ),

                    'intro' => array(
                        'type'            => '50_50',
                        'image'           => '',
                        'image_key'       => '',
                        'title'           => $this->get('Webpage Name'),
                        'sub_title'       => 'Will cover many web sites still in their infancy various versions have evolved packages over the years.',
                        'text'            => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet anything embarrassing hidden in the middle many web sites.',
                        'class_title'     => '',
                        'class_sub_title' => '',
                        'class_text'      => '',


                    ),

                    'products' => array(
                        'items'   => $this->get_items(),
                        'filters' => array()
                    )


                );

                //print_r($content_data);


                $this->update(array('Page Store Content Data' => json_encode($content_data)), 'no_history');


            } else {
                include_once 'class.Public_Product.php';


                $title = $category->get('Label');
                if ($title == '') {
                    $title = $category->get('Code');
                }
                if ($title == '') {
                    $title = _('Title');
                }

                $description = $category->get('Product Category Description');
                if ($description == '') {
                    $description = $category->get('Label');
                }
                if ($description == '') {
                    $description = $category->get('Code');
                }
                if ($description == '') {
                    $description = _('Description');
                }


                $image_src = $category->get('Image');

                $content_data = array(
                    'description_block' => array(
                        'class' => '',

                        'blocks' => array(

                            'webpage_content_header_image' => array(
                                'type'      => 'image',
                                'image_src' => $image_src,
                                'caption'   => '',
                                'class'     => ''

                            ),

                            'webpage_content_header_text' => array(
                                'class'   => '',
                                'type'    => 'text',
                                'content' => sprintf('<h1 class="description_title">%s</h1><div class="description">%s</div>', $title, $description)

                            )

                        )
                    )

                );

                //print_r($content_data);
                $this->update(array('Page Store Content Data' => json_encode($content_data)), 'no_history');

                $content_data = $this->get('Content Data');


            }


        } elseif ($this->get('Webpage Scope') == 'Category Categories') {


            //  $category=get_object('Category',$this->get('Webpage Scope Key'));

            if ($website->get('Website Theme') == 'theme_1') {


                $items = array();

                $sql = sprintf(
                    "SELECT  `Webpage URL`,`Category Main Image Key`,`Category Main Image`,`Category Label`,`Category Main Image Key`,`Webpage State`,`Product Category Public`,`Webpage State`,`Page Key`,`Webpage Code`,`Product Category Active Products`,`Category Code`,B.`Category Key` FROM    `Category Bridge` B  LEFT JOIN     `Product Category Dimension` P   ON (`Subject Key`=`Product Category Key` AND `Subject`='Category' )    LEFT JOIN `Category Dimension` Cat ON (Cat.`Category Key`=P.`Product Category Key`) LEFT JOIN `Page Store Dimension` CatWeb ON (CatWeb.`Page Key`=`Product Category Webpage Key`)  WHERE  B.`Category Key`=%d  AND `Product Category Public`='Yes'  AND `Webpage State` IN ('Online','Ready')    ORDER BY  `Category Label`  ",
                    $this->get('Webpage Scope Key')


                );

                //   print $sql;

                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {
                        $items[] = array(
                            'type'                 => 'category',
                            'category_key'         => $row['Category Key'],
                            'header_text'          => trim(strip_tags($row['Category Label'])),
                            'image_src'            => ($row['Category Main Image Key'] ? 'image_root.php?id='.$row['Category Main Image Key'] : '/art/nopic.png'),
                            'image_mobile_website' => '',
                            'image_website'        => '',
                            'webpage_key'          => $row['Page Key'],
                            'webpage_code'         => strtolower($row['Webpage Code']),
                            'item_type'            => 'Subject',
                            'category_code'        => $row['Category Code'],
                            'number_products'      => $row['Product Category Active Products'],
                            'link'                 => $row['Webpage URL'],
                        );
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                $sections = array(

                    array(
                        'type'     => 'anchor',
                        'title'    => '',
                        'subtitle' => '',
                        'items'    => $items
                    )

                );


                $content_data = array(
                    'blocks' => array(
                        array(
                            'type'          => 'category_categories',
                            'label'         => _('Categories').' ('._('sections').')',
                            'icon'          => 'fa-th',
                            'show'          => 1,
                            'top_margin'    => 0,
                            'bottom_margin' => 30,
                            'sections'      => $sections
                        )
                    )

                );


                $this->update(array('Page Store Content Data' => json_encode($content_data)), 'no_history');


            } else {


                include_once 'class.Category.php';

                $category = new Category($this->get('Webpage Scope Key'));

                $sql = sprintf(
                    'DELETE FROM  `Webpage Section Dimension` WHERE `Webpage Section Webpage Key`=%d  ', $this->id

                );

                $this->db->exec($sql);

                $sql = sprintf(
                    'DELETE FROM  `Category Webpage Index` WHERE `Category Webpage Index Webpage Key`=%d  ', $this->id

                );

                $this->db->exec($sql);
                $sql = sprintf(
                    'DELETE FROM  `Category Webpage Index` WHERE `Category Webpage Index Parent Category Key`=%d  ', $this->get('Webpage Scope Key')

                );


                //  print "$sql\n";

                $this->db->exec($sql);


                $title = $category->get('Label');
                if ($title == '') {
                    $title = $category->get('Code');
                }
                if ($title == '') {
                    $title = _('Title');
                }

                $description = $category->get('Product Category Description');
                if ($description == '') {
                    $description = $category->get('Label');
                }
                if ($description == '') {
                    $description = $category->get('Code');
                }
                if ($description == '') {
                    $description = _('Description');
                }


                $image_src = $category->get('Image');

                $content_data = array(
                    'description_block' => array(
                        'class' => '',

                        'blocks' => array(

                            'webpage_content_header_image' => array(
                                'type'      => 'image',
                                'image_src' => $image_src,
                                'caption'   => '',
                                'class'     => ''

                            ),

                            'webpage_content_header_text' => array(
                                'class'   => '',
                                'type'    => 'text',
                                'content' => sprintf('<h1 class="description_title">%s</h1><div class="description">%s</div>', $title, $description)

                            )

                        )
                    ),
                    'sections'          => array()

                );

                $section = array(
                    'type'     => 'anchor',
                    'title'    => '',
                    'subtitle' => '',
                    'panels'   => array()
                );


                $sql = sprintf(
                    'INSERT INTO `Webpage Section Dimension` (`Webpage Section Webpage Key`,`Webpage Section Webpage Stack Index`,`Webpage Section Data`) VALUES (%d,%d,%s) ', $this->id, 0, prepare_mysql(json_encode($section))

                );

                //  print $sql;

                $this->db->exec($sql);

                $section['key'] = $this->db->lastInsertId();

                $content_data['sections'][] = $section;
                $this->update(array('Page Store Content Data' => json_encode($content_data)), 'no_history');

                $category->create_stack_index(true);

                // new list of

            }


        } else {

            include_once 'class.Website.php';

            include_once 'conf/website_system_webpages.php';

            $website = new Website($this->get('Webpage Website Key'));

            $website_system_webpages = website_system_webpages_config($website->get('Website Type'));


            if (isset($website_system_webpages[$this->get('Webpage Code')]['Page Store Content Data'])) {

                $this->update(array('Page Store Content Data' => $website_system_webpages[$this->get('Webpage Code')]['Page Store Content Data']), 'no_history');
            }


        }


    }

    function get_items() {


        $items = array();


        if ($this->get('Webpage Scope') == 'Category Products') {

            $sql = sprintf(
                "SELECT `Product Category Index Content Data` FROM `Product Category Index` 
                 WHERE  `Product Category Index Website Key`=%d   ORDER BY  ifnull(`Product Category Index Stack`,99999999)", $this->id


            );


            if ($result = $this->db->query($sql)) {
                foreach ($result as $row) {
                    $items[] = json_decode($row['Product Category Index Content Data'], true);
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                print "$sql\n";
                exit;
            }


        } else {
            if ($this->get('Webpage Scope') == 'Category Categories') {

                $sql = sprintf(
                    "SELECT `Category Webpage Index Content Data` FROM `Category Webpage Index` CWI
                 WHERE  `Category Webpage Index Webpage Key`=%d   ORDER BY  ifnull(`Category Webpage Index Stack`,99999999)", $this->id


                );


                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {
                        $items[] = json_decode($row['Category Webpage Index Content Data'], true);
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }

            }
        }


        return $items;

    }

    function reindex_products() {
        $content_data = $this->get('Content Data');
        $block_key    = false;
        foreach ($content_data['blocks'] as $_block_key => $_block) {
            if ($_block['type'] == 'products') {
                $block     = $_block;
                $block_key = $_block_key;
                break;
            }
        }

        if (!$block_key) {
            return;
        }

        foreach ($block['items'] as $item_key => $item) {

            $sql = sprintf('SELECT `Product Web State` FROM `Product Dimension` WHERE `Product ID`=%d', $item['product_id']);
            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    if ($row['Product Web State'] == 'For Sale' or $row['Product Web State'] == 'Out of Stock') {

                        $product = get_object('Public_Product', $item['product_id']);
                        $product->load_webpage();


                        $content_data['blocks'][$block_key]['items'][$item_key]['web_state']    = $product->get('Web State');
                        $content_data['blocks'][$block_key]['items'][$item_key]['price']        = $product->get('Price');
                        $content_data['blocks'][$block_key]['items'][$item_key]['rrp']          = $product->get('RRP');
                        $content_data['blocks'][$block_key]['items'][$item_key]['code']         = $product->get('Code');
                        $content_data['blocks'][$block_key]['items'][$item_key]['name']         = $product->get('Name');
                        $content_data['blocks'][$block_key]['items'][$item_key]['link']         = $product->webpage->get('URL');
                        $content_data['blocks'][$block_key]['items'][$item_key]['webpage_code'] = $product->webpage->get('Webpage Code');
                        $content_data['blocks'][$block_key]['items'][$item_key]['webpage_key']  = $product->webpage->id;


                        $content_data['blocks'][$block_key]['items'][$item_key]['out_of_stock_class'] = $product->get('Out of Stock Class');
                        $content_data['blocks'][$block_key]['items'][$item_key]['out_of_stock_label'] = $product->get('Out of Stock Label');
                        $content_data['blocks'][$block_key]['items'][$item_key]['sort_code']          = $product->get('Code File As');
                        $content_data['blocks'][$block_key]['items'][$item_key]['sort_name']          = mb_strtolower($product->get('Product Name'));

                    } else {
                        unset($content_data['blocks'][$block_key]['items'][$item_key]);

                    }

                } else {
                    unset($content_data['blocks'][$block_key]['items'][$item_key]);
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                print "$sql\n";
                exit;
            }


        }

        $this->update_field_switcher('Page Store Content Data', json_encode($content_data), 'no_history');
        $sql = sprintf('DELETE FROM `Website Webpage Scope Map` WHERE `Website Webpage Scope Webpage Key`=%d  AND `Website Webpage Scope Type`="Products_Item" ', $this->id);
        $this->db->exec($sql);

        $index = 0;
        foreach ($content_data['blocks'][$block_key]['items'] as $item) {

            $sql = sprintf(
                'INSERT INTO `Website Webpage Scope Map` (`Website Webpage Scope Website Key`,`Website Webpage Scope Webpage Key`,`Website Webpage Scope Scope`,`Website Webpage Scope Scope Key`,`Website Webpage Scope Type`,`Website Webpage Scope Index`) VALUES (%d,%d,%s,%d,%s,%d) ',
                $this->get('Webpage Website Key'), $this->id, prepare_mysql('Product'), $item['product_id'], prepare_mysql('Products_Item'), $index

            );
            //print "$sql\n";

            $this->db->exec($sql);
            $index++;


        }


    }

    function reindex_see_also() {

        $content_data = $this->get('Content Data');
        $block_key    = false;
        foreach ($content_data['blocks'] as $_block_key => $_block) {
            if ($_block['type'] == 'see_also') {
                $block     = $_block;
                $block_key = $_block_key;
                break;
            }
        }

        if (!$block_key) {
            return;
        }


    //    print_r($block['items']);

        foreach ($block['items'] as $item_key => $item) {


            if ($item['type'] == 'category') {

                $sql = sprintf(
                    "SELECT `Category Label`,`Webpage URL`,`Webpage Code`,`Page Key`,`Category Code`,`Product Category Active Products`,`Category Main Image Key`
                   `Product Category Dimension` P      LEFT JOIN 
                   `Category Dimension` Cat ON (Cat.`Category Key`=P.`Product Category Key`) 
                   LEFT JOIN `Page Store Dimension` CatWeb ON (CatWeb.`Page Key`=`Product Category Webpage Key`)  
                WHERE  `Product Category Key`=%d  AND `Product Category Public`='Yes'  AND `Webpage State` IN ('Online','Ready')  ", $item['category_key']


                );


                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {


                        if ($block['auto'] == true) {
                            $content_data['blocks'][$block_key]['items'][$item_key]['header_text'] = $row['Category Label'];


                            $image_key = $this->data['Category Main Image Key'];


                            if ($image_key) {
                                $image_src = '/image_root.php?id='.$image_key;
                            } else {
                                $image_src = '/art/nopic.png';

                            }

                            if ($image_src != $content_data['blocks'][$block_key]['items'][$item_key]['image_src']) {

                                $content_data['blocks'][$block_key]['items'][$item_key]['image_src'] = $image_src;

                                $content_data['blocks'][$block_key]['items'][$item_key]['image_mobile_website'] = '';
                                $content_data['blocks'][$block_key]['items'][$item_key]['image_website']        = '';
                            }


                        }


                        $content_data['blocks'][$block_key]['items'][$item_key]['category_code']   = $row['Webpage URL'];
                        $content_data['blocks'][$block_key]['items'][$item_key]['number_products'] = $row['Product Category Active Products'];


                        $content_data['blocks'][$block_key]['items'][$item_key]['link']         = $row['Webpage URL'];
                        $content_data['blocks'][$block_key]['items'][$item_key]['webpage_code'] = mb_strtolower($row['Webpage Code']);
                        $content_data['blocks'][$block_key]['items'][$item_key]['webpage_key']  = $row['`Page Key'];


                    } else {
                        unset($content_data['blocks'][$block_key]['items'][$item_key]);
                    }
                }


            } elseif ($item['type'] == 'product') {


                $sql = sprintf('SELECT `Product Web State` FROM `Product Dimension` WHERE `Product ID`=%d', $item['product_id']);
                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {
                        if ($row['Product Web State'] == 'For Sale' or $row['Product Web State'] == 'Out of Stock') {

                            $product = get_object('Public_Product', $item['product_id']);
                            $product->load_webpage();


                            if ($block['auto'] == true) {
                                $content_data['blocks'][$block_key]['items'][$item_key]['header_text'] = $product->get('Name');

                                $image_src = $product->get('Image');
                                if ($image_src != $content_data['blocks'][$block_key]['items'][$item_key]['image_src']) {

                                    $content_data['blocks'][$block_key]['items'][$item_key]['image_src'] = $image_src;

                                    $content_data['blocks'][$block_key]['items'][$item_key]['image_mobile_website'] = '';
                                    $content_data['blocks'][$block_key]['items'][$item_key]['image_website']        = '';
                                }

                            }


                            $content_data['blocks'][$block_key]['items'][$item_key]['link']         = $product->webpage->get('URL');
                            $content_data['blocks'][$block_key]['items'][$item_key]['webpage_code'] = $product->webpage->get('Webpage Code');
                            $content_data['blocks'][$block_key]['items'][$item_key]['webpage_key']  = $product->webpage->id;


                        } else {
                            unset($content_data['blocks'][$block_key]['items'][$item_key]);

                        }

                    } else {
                        unset($content_data['blocks'][$block_key]['items'][$item_key]);
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }

            }


        }


        if ($block['auto'] and count($block['items']) < $block['auto_items']) {
            foreach ($this->get_related_webpages_key($block['auto_items']) as $webpage_key) {
                $found = false;
                foreach ($block['items'] as $item_key => $item) {
                    if ($webpage_key == $item['webpage_key']) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $see_also_page = get_object('Webpage', $webpage_key);

                    if ($see_also_page->get('Webpage Scope') == 'Category Products' or $see_also_page->get('Webpage Scope') == 'Category Categories') {
                        $category = get_object('Category', $see_also_page->get('Webpage Scope Key'));


                        $content_data['blocks'][$block_key]['items'][] = array(
                            'type' => 'category',

                            'header_text'          => $category->get('Category Label'),
                            'image_src'            => $category->get('Image'),
                            'image_mobile_website' => '',
                            'image_website'        => '',

                            'webpage_key'  => $see_also_page->id,
                            'webpage_code' => mb_strtolower($see_also_page->get('Webpage Code')),

                            'category_key'    => $category->id,
                            'category_code'   => $category->get('Category Code'),
                            'number_products' => $category->get('Product Category Active Products'),
                            'link'            => $see_also_page->get('Webpage URL'),


                        );
                    } elseif ($see_also_page->get('Webpage Scope') == 'Product') {

                        $product = get_object('Public_Product', $see_also_page->get('Webpage Scope Key'));


                        $content_data['blocks'][$block_key]['items'][] = array(
                            'type' => 'category',

                            'header_text'          => $product->get('Name'),
                            'image_src'            => $product->get('Image'),
                            'image_mobile_website' => '',
                            'image_website'        => '',

                            'webpage_key'  => $see_also_page->id,
                            'webpage_code' => mb_strtolower($see_also_page->get('Webpage Code')),

                            'product_id'        => $product->id,
                            'product_code'      => $product->get('Code'),
                            'product_web_state' => $product->get('Web State'),
                            'link'              => $see_also_page->get('Webpage URL'),


                        );
                    }


                }


            }

        }


        $this->update_field_switcher('Page Store Content Data', json_encode($content_data), 'no_history');
        $sql = sprintf('DELETE FROM `Website Webpage Scope Map` WHERE `Website Webpage Scope Webpage Key`=%d  AND `Website Webpage Scope Type` in  ("See_Also_Category_Manual","See_Also_Category_Auto","See_Also_Product_Manual","See_Also_Product_Auto") ', $this->id);
        $this->db->exec($sql);

        $index = 0;
        foreach ($content_data['blocks'][$block_key]['items'] as $item) {

            $sql = sprintf(
                'INSERT INTO `Website Webpage Scope Map` (`Website Webpage Scope Website Key`,`Website Webpage Scope Webpage Key`,`Website Webpage Scope Scope`,`Website Webpage Scope Scope Key`,`Website Webpage Scope Type`,`Website Webpage Scope Index`) VALUES (%d,%d,%s,%d,%s,%d) ',
                $this->get('Webpage Website Key'), $this->id, prepare_mysql(capitalize($item['type'])),($item['type']=='category'?$item['category_key']:$item['product_id']),
                prepare_mysql('See_Also_'.capitalize($item['type']).'_'.($block['auto']?'Auto':'Manual')),
                $index

            );
            //print "$sql\n";

            $this->db->exec($sql);
            $index++;


        }



    }

    function get_related_webpages_key($number_items) {

        $max_links = $number_items * 2;


        $max_sales_links = ceil($max_links * .6);


        $min_sales_correlation_samples = 5;
        // $correlation_upper_limit       = .5 / ($min_sales_correlation_samples);
        $see_also     = array();
        $number_links = 0;
        $items        = array();

        switch ($this->data['Webpage Scope']) {


            case 'Category Products':


                $sql = sprintf(
                    "SELECT * FROM `Product Family Sales Correlation` WHERE `Family A Key`=%d ORDER BY `Correlation` DESC ", $this->data['Webpage Scope Key']
                );


                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {
                        $_family  = get_object('Category', $row['Family B Key']);
                        $_webpage = $_family->get_webpage();
                        // and $_webpage->data['Page Stealth Mode'] == 'No'
                        if ($_webpage->id and $_webpage->data['Page State'] == 'Online') {
                            $see_also[$_webpage->id] = array(
                                'type'     => 'Sales',
                                'value'    => $row['Correlation'],
                                'page_key' => $_webpage->id
                            );
                            $number_links            = count($see_also);
                            if ($number_links >= $max_sales_links) {
                                break;
                            }
                        }
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                if ($number_links < $max_links) {
                    $sql = sprintf(
                        "SELECT * FROM `Product Family Semantic Correlation` WHERE `Family A Key`=%d ORDER BY `Weight` DESC LIMIT %d", $this->data['Webpage Scope Key'], ($max_links - $number_links) * 2
                    );


                    if ($result = $this->db->query($sql)) {
                        foreach ($result as $row) {

                            if (!array_key_exists($row['Family B Key'], $see_also)) {


                                $_family  = get_object('Category', $row['Family B Key']);
                                $_webpage = $_family->get_webpage();
                                // and $_webpage->data['Page Stealth Mode'] == 'No'
                                if ($_webpage->id and $_webpage->data['Page State'] == 'Online') {
                                    $see_also[$_webpage->id] = array(
                                        'type'     => 'Semantic',
                                        'value'    => $row['Weight'],
                                        'page_key' => $_webpage->id
                                    );
                                    $number_links            = count($see_also);
                                    if ($number_links >= $max_links) {
                                        break;
                                    }
                                }


                            }

                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        exit;
                    }


                }


                if ($number_links < $max_links) {

                    $category = get_object('Category', $this->data['Webpage Scope Key']);

                    $sql = sprintf(
                        "SELECT `Category Key` FROM `Category Dimension` LEFT JOIN   `Product Category Dimension` ON (`Category Key`=`Product Category Key`)   LEFT JOIN `Page Store Dimension` ON (`Product Category Webpage Key`=`Page Key`) WHERE `Category Parent Key`=%d  AND `Webpage State`='Online'  AND `Category Key`!=%d  ORDER BY RAND()  LIMIT %d",
                        $category->get('Category Parent Key'), $category->id, ($max_links - $number_links) * 2
                    );


                    if ($result = $this->db->query($sql)) {
                        foreach ($result as $row) {

                            if (!array_key_exists($row['Category Key'], $see_also)) {


                                $_family  = get_object('Category', $row['Category Key']);
                                $_webpage = $_family->get_webpage();
                                // and $_webpage->data['Page Stealth Mode'] == 'No'
                                if ($_webpage->id and $_webpage->data['Page State'] == 'Online') {
                                    $see_also[$_webpage->id] = array(
                                        'type'     => 'SameParent',
                                        'value'    => .2,
                                        'page_key' => $_webpage->id
                                    );
                                    $number_links            = count($see_also);
                                    if ($number_links >= $max_links) {
                                        break;
                                    }
                                }


                            }

                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        exit;
                    }


                }


                if ($number_links < $max_links) {


                    $sql = sprintf(
                        "SELECT `Category Key` FROM `Category Dimension` LEFT JOIN   `Product Category Dimension` ON (`Category Key`=`Product Category Key`)   LEFT JOIN `Page Store Dimension` ON (`Product Category Webpage Key`=`Page Key`) WHERE  `Webpage State`='Online'  AND `Category Key`!=%d  AND `Category Store Key`=%d ORDER BY RAND()  LIMIT %d",
                        $this->data['Webpage Scope Key'], $category->get('Category Store Key'), ($max_links - $number_links) * 2
                    );


                    if ($result = $this->db->query($sql)) {
                        foreach ($result as $row) {

                            if (!array_key_exists($row['Category Key'], $see_also)) {


                                $_family  = get_object('Category', $row['Category Key']);
                                $_webpage = $_family->get_webpage();
                                // and $_webpage->data['Page Stealth Mode'] == 'No'
                                if ($_webpage->id and $_webpage->data['Page State'] == 'Online') {
                                    $see_also[$_webpage->id] = array(
                                        'type'     => 'Other',
                                        'value'    => .1,
                                        'page_key' => $_webpage->id
                                    );
                                    $number_links            = count($see_also);
                                    if ($number_links >= $max_links) {
                                        break;
                                    }
                                }


                            }

                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        exit;
                    }


                }

                $count = 0;

                $order_value = 1;


                if (count($see_also) > 0) {


                    foreach ($see_also as $key => $row) {
                        $correlation[$key] = $row['value'];
                    }

                    //print_r($correlation);

                    array_multisort($correlation, SORT_DESC, $see_also);
                    // print_r($see_also);


                    foreach ($see_also as $see_also_page_key => $see_also_data) {

                        if ($count >= $number_items) {
                            break;
                        }
                        $items[] = $see_also_data['page_key'];

                        $count++;
                        $order_value++;
                        //print "$sql\n";
                    }

                }


                break;


            case 'Product':

                $product = get_object('Product', $this->data['Webpage Scope Key']);
                $sql     = sprintf(
                    "SELECT `Product Webpage Key`,`Product B ID`,`Correlation` FROM `Product Sales Correlation`  LEFT JOIN `Product Dimension` ON (`Product ID`=`Product B ID`)    LEFT JOIN `Page Store Dimension` ON (`Page Key`=`Product Webpage Key`)  WHERE `Product A ID`=%d AND `Webpage State`='Online' AND `Product Web State`='For Sale'  ORDER BY `Correlation` DESC",
                    $product->id
                );
                //  $see_also_page->data['Page Stealth Mode'] == 'No')

                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {
                        if (!array_key_exists($row['Product B ID'], $see_also) and $row['Product Webpage Key']) {

                            $see_also[$row['Product Webpage Key']] = array(
                                'type'     => 'Sales',
                                'value'    => $row['Correlation'],
                                'page_key' => $row['Product Webpage Key']
                            );
                            $number_links                          = count($see_also);
                            if ($number_links >= $max_links) {
                                break;
                            }

                        }
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                if ($number_links >= $max_links) {
                    break;
                }


                $max_customers = 0;

                $sql = sprintf(
                    "SELECT P.`Product ID`,P.`Product Code`,`Product Web State`,`Product Webpage Key`,`Product Total Acc Customers` FROM `Product Dimension` P LEFT JOIN `Product Data Dimension` D ON (P.`Product ID`=D.`Product ID`)    LEFT JOIN `Page Store Dimension` ON (`Page Key`=`Product Webpage Key`)  WHERE  `Product Web State`='For Sale' AND `Webpage State`='Online' AND P.`Product ID`!=%d  AND `Product Family Category Key`=%d ORDER BY `Product Total Acc Customers` DESC  ",
                    $product->id, $product->get('Product Family Category Key')

                );

                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {


                        if (!array_key_exists($row['Product ID'], $see_also) and $row['Product Webpage Key']) {


                            if ($max_customers == 0) {
                                $max_customers = $row['Product Total Acc Customers'];
                            }


                            $rnd = mt_rand() / mt_getrandmax();

                            $see_also[$row['Product Webpage Key']] = array(
                                'type'     => 'Same Family',
                                'value'    => .25 * $rnd * ($row['Product Total Acc Customers'] == 0 ? 1 : $row['Product Total Acc Customers']) / ($max_customers == 0 ? 1 : $max_customers),
                                'page_key' => $row['Product Webpage Key']
                            );
                            $number_links                          = count($see_also);
                            if ($number_links >= $max_links) {
                                break;
                            }
                        }

                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                if ($number_links >= $max_links) {
                    break;
                }
                $max_customers = 0;
                $sql           = sprintf(
                    "SELECT P.`Product ID`,P.`Product Code`,`Product Web State`,`Product Webpage Key`,`Product Total Acc Customers` FROM `Product Dimension` P LEFT JOIN `Product Data Dimension` D ON (P.`Product ID`=D.`Product ID`)    LEFT JOIN `Page Store Dimension` ON (`Page Key`=`Product Webpage Key`)  WHERE  `Product Web State`='For Sale' AND `Webpage State`='Online' AND P.`Product ID`!=%d  AND `Product Store Key`=%d ORDER BY `Product Total Acc Customers` DESC  ",
                    $product->id, $product->get('Product Store Key')

                );

                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {


                        if (!array_key_exists($row['Product ID'], $see_also) and $row['Product Webpage Key']) {

                            if ($max_customers == 0) {
                                $max_customers = $row['Product Total Acc Customers'];
                            }


                            $rnd = mt_rand() / mt_getrandmax();

                            $see_also[$row['Product Webpage Key']] = array(
                                'type'     => 'Other',
                                'value'    => .1 * $rnd * ($row['Product Total Acc Customers'] == 0 ? 1 : $row['Product Total Acc Customers']) / ($max_customers == 0 ? 1 : $max_customers),
                                'page_key' => $row['Product Webpage Key']
                            );
                            $number_links                          = count($see_also);
                            if ($number_links >= $max_links) {
                                break;
                            }
                        }

                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }


                break;

                $count = 0;

                $order_value = 1;


                if (count($see_also) > 0) {


                    foreach ($see_also as $key => $row) {
                        $correlation[$key] = $row['value'];
                    }

                    //print_r($correlation);

                    array_multisort($correlation, SORT_DESC, $see_also);
                    // print_r($see_also);


                    foreach ($see_also as $see_also_page_key => $see_also_data) {

                        if ($count >= $number_items) {
                            break;
                        }
                        $items[] = $see_also_data['page_key'];

                        $count++;
                        $order_value++;
                        //print "$sql\n";
                    }

                }

            default:

                break;
        }


        return $items;


    }

    function add_section_item($item_key, $section_key = false) {


        include_once('class.Public_Webpage.php');
        include_once('class.Category.php');

        $updated_metadata = array('section_keys' => array());

        $content_data = $this->get('Content Data');

        // print_r($content_data['sections']);


        if (!$section_key) {

            foreach ($content_data['sections'] as $_key => $_data) {


                if ($_data['type'] == 'anchor') {
                    $section_key = $_data['key'];

                    break;
                }

            }

        }


        $found_section = false;
        foreach ($content_data['sections'] as $section_data) {
            if ($section_data['key'] == $section_key) {
                $found_section = true;
                break;

            }
        }


        if (!$found_section) {

            $this->msg   = 'Web page section not found in website';
            $this->error = true;

            return $updated_metadata;
        }

        $parent_category  = new Category($this->get('Webpage Scope Key'));
        $subject_category = new Category($item_key);


        //print_r($subject_category);

        $subject_webpage = new Public_Webpage('scope', ($subject_category->get('Category Subject') == 'Category' ? 'Category Categories' : 'Category Products'), $subject_category->id);


        //  print_r($subject_category);

        if ($subject_webpage->id) {

            $sql = sprintf(
                'SELECT max(`Category Webpage Index Stack`) AS stack FROM  `Category Webpage Index` WHERE `Category Webpage Index Webpage Key`=%d AND `Category Webpage Index Section Key`=%d ', $this->id, $section_key


            );


            //  print $sql;
            $stack = 0;
            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $stack = $row['stack'];

                }
            }


            $stack++;

            if ($parent_category->is_subject_associated($item_key)) {
                $subject_type = 'Subject';
            } else {
                $subject_type = 'Guest';

            }


            $subject_data = array(
                'header_text' => $subject_category->get('Label'),
                'image_src'   => $subject_category->get('Image'),
                'footer_text' => $subject_category->get('Code'),
            );


            $sql = sprintf(
                'INSERT INTO `Category Webpage Index` (`Category Webpage Index Parent Category Key`,`Category Webpage Index Category Key`,`Category Webpage Index Webpage Key`,`Category Webpage Index Category Webpage Key`,`Category Webpage Index Section Key`,`Category Webpage Index Content Data`,`Category Webpage Index Subject Type`,`Category Webpage Index Stack`) VALUES (%d,%d,%d,%d,%d,%s,%s,%d) ',
                $this->get('Webpage Scope Key'), $item_key, $this->id, $subject_webpage->id, $section_key, prepare_mysql(json_encode($subject_data)),

                prepare_mysql($subject_type), $stack
            );

            // print $sql;

            $this->db->exec($sql);


            $updated_metadata['section_keys'][] = $section_key;


        } else {
            $this->msg   = "Item don't have website";
            $this->error = true;

            return $updated_metadata;
        }

        $result = array();

        foreach ($updated_metadata['section_keys'] as $section_key) {
            foreach ($content_data['sections'] as $section_stack_index => $section_data) {
                if ($section_data['key'] == $section_key) {
                    $content_data['sections'][$section_stack_index]['items'] = get_website_section_items($this->db, $section_data);
                    $result[$section_key]                                    = $content_data['sections'][$section_stack_index]['items'];
                    break;
                }
            }
        }

        $this->update(array('Page Store Content Data' => json_encode($content_data)), 'no_history');


        return $result;


    }

    function remove_section_item($item_key) {

        $updated_metadata = array('section_keys' => array());
        $content_data     = $this->get('Content Data');

        $sql = sprintf(
            'SELECT `Category Webpage Index Key`,`Category Webpage Index Section Key`,`Category Webpage Index Stack` FROM  `Category Webpage Index` WHERE `Category Webpage Index Webpage Key`=%d AND `Category Webpage Index Category Key`=%d ', $this->id, $item_key


        );

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {


                $updated_metadata['section_keys'][] = $row['Category Webpage Index Section Key'];

                $sql = sprintf(
                    'DELETE FROM `Category Webpage Index` WHERE `Category Webpage Index Key`=%d  ',

                    $row['Category Webpage Index Key']
                );
                $this->db->exec($sql);

            } else {
                $this->msg   = 'Item not found in website';
                $this->error = true;

                return $updated_metadata;

                return;

            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }

        $result = array();

        foreach ($updated_metadata['section_keys'] as $section_key) {
            foreach ($content_data['sections'] as $section_stack_index => $section_data) {
                if ($section_data['key'] == $section_key) {
                    $content_data['sections'][$section_stack_index]['items'] = get_website_section_items($this->db, $section_data);
                    $result[$section_key]                                    = $content_data['sections'][$section_stack_index]['items'];
                    break;
                }
            }
        }

        $this->update(array('Page Store Content Data' => json_encode($content_data)), 'no_history');


        return $result;

    }

    function reindex_category_categories() {
        $content_data = $this->get('Content Data');

        $block_key = false;
        foreach ($content_data['blocks'] as $_block_key => $_block) {
            if ($_block['type'] == 'category_categories') {
                $block     = $_block;
                $block_key = $_block_key;
                break;
            }
        }

        if (!$block_key) {
            return;
        }

        $sql = sprintf(
            "SELECT  `Webpage URL`,`Category Main Image Key`,`Category Main Image`,`Category Label`,`Category Main Image Key`,`Webpage State`,`Product Category Public`,`Webpage State`,`Page Key`,`Webpage Code`,`Product Category Active Products`,`Category Code`,Cat.`Category Key` 
                FROM    `Category Bridge` B  LEFT JOIN     `Product Category Dimension` P   ON (`Subject Key`=`Product Category Key` AND `Subject`='Category' )    LEFT JOIN `Category Dimension` Cat ON (Cat.`Category Key`=P.`Product Category Key`) LEFT JOIN `Page Store Dimension` CatWeb ON (CatWeb.`Page Key`=`Product Category Webpage Key`)  WHERE  B.`Category Key`=%d  AND `Product Category Public`='Yes'  AND `Webpage State` IN ('Online','Ready')  ORDER BY  `Category Label` DESC   ",
            $this->get('Webpage Scope Key')


        );

        $items                    = array();
        $items_category_key_index = array();

        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $items[$row['Category Key']]                    = $row;
                $items_category_key_index[$row['Category Key']] = $row['Category Key'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $offline_items_category_key_index = array();
        $sql                              = sprintf(
            "SELECT  B.`Category Key` FROM    `Category Bridge` B  LEFT JOIN     `Product Category Dimension` P   ON (`Subject Key`=`Product Category Key` AND `Subject`='Category' )    LEFT JOIN `Category Dimension` Cat ON (Cat.`Category Key`=P.`Product Category Key`) LEFT JOIN `Page Store Dimension` CatWeb ON (CatWeb.`Page Key`=`Product Category Webpage Key`)  
            WHERE  B.`Category Key`=%d  AND  (`Product Category Public`='No'  OR `Webpage State` NOT IN ('Online','Ready')  )  ", $this->get('Webpage Scope Key')


        );
        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {

                $offline_items_category_key_index[$row['Category Key']] = $row['Category Key'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }

        $anchor_section_key = 0;

        foreach ($block['sections'] as $section_key => $section) {

            if ($section['type'] == 'anchor') {
                $anchor_section_key = $section_key;
            }

            foreach ($section['items'] as $item_key => $item) {
                if ($item['type'] == 'category') {


                    //print $item['category_key'];
                    //print_r($items_category_key_index);
                    //exit;

                    if (in_array($item['category_key'], $items_category_key_index)) {

                        $item_data = $items[$item['category_key']];

                        $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['item_type']       = 'Subject';
                        $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['webpage_key']     = $item_data['Page Key'];
                        $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['webpage_code']    = $item_data['Webpage Code'];
                        $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['category_code']   = $item_data['Category Code'];
                        $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['number_products'] = $item_data['Product Category Active Products'];
                        $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['link']            = $item_data['Webpage URL'];

                        unset($items_category_key_index[$item['category_key']]);
                    } else {

                        if (in_array($item['category_key'], $offline_items_category_key_index)) {
                            unset($content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]);

                        } else {

                            $sql = sprintf(
                                "SELECT  `Webpage URL`,`Category Main Image Key`,`Category Main Image`,`Category Label`,`Category Main Image Key`,`Webpage State`,`Product Category Public`,`Webpage State`,`Page Key`,`Webpage Code`,`Product Category Active Products`,`Category Code`,Cat.`Category Key` 
                                  FROM   `Product Category Dimension` P     LEFT JOIN `Category Dimension` Cat ON (Cat.`Category Key`=P.`Product Category Key`) LEFT JOIN `Page Store Dimension` CatWeb ON (CatWeb.`Page Key`=`Product Category Webpage Key`)  
                                  WHERE  `Product Category Key`=%d  AND `Product Category Public`='Yes'  AND `Webpage State` IN ('Online','Ready')    ", $item['category_key']


                            );

                            if ($result = $this->db->query($sql)) {
                                if ($row = $result->fetch()) {
                                    $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['item_type']       = 'Guest';
                                    $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['webpage_key']     = $row['Page Key'];
                                    $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['webpage_code']    = $row['Webpage Code'];
                                    $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['category_code']   = $row['Category Code'];
                                    $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['number_products'] = $row['Product Category Active Products'];
                                    $content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]['link']            = $row['Webpage URL'];


                                } else {
                                    unset($content_data['blocks'][$block_key]['sections'][$section_key]['items'][$item_key]);
                                }
                            } else {
                                print_r($error_info = $this->db->errorInfo());
                                print "$sql\n";
                                exit;
                            }


                        }


                    }

                }


            }


        }

        foreach ($items_category_key_index as $index) {
            $item_data = $items[$index];
            $item      = array(
                'type'                 => 'category',
                'category_key'         => $item_data['Category Key'],
                'header_text'          => trim(strip_tags($item_data['Category Label'])),
                'image_src'            => ($item_data['Category Main Image Key'] ? 'image_root.php?id='.$item_data['Category Main Image Key'] : '/art/nopic.png'),
                'image_mobile_website' => '',
                'image_website'        => '',
                'webpage_key'          => $item_data['Page Key'],
                'webpage_code'         => strtolower($item_data['Webpage Code']),
                'item_type'            => 'Subject',
                'category_code'        => $item_data['Category Code'],
                'number_products'      => $item_data['Product Category Active Products'],
                'link'                 => $item_data['Webpage URL'],


            );

            array_unshift($content_data['blocks'][$block_key]['sections'][$anchor_section_key]['items'], $item);
        }


        $this->update_field_switcher('Page Store Content Data', json_encode($content_data), 'no_history');

        $sql = sprintf('DELETE FROM `Website Webpage Scope Map` WHERE `Website Webpage Scope Webpage Key`=%d AND `Website Webpage Scope Type` IN ("Subject","Guest")  ', $this->id);
        $this->db->exec($sql);

        $index = 0;
        foreach ($content_data['blocks'][$block_key]['sections'] as $section_key => $section) {
            foreach ($section['items'] as $item_key => $item) {
                if ($item['type'] == 'category') {
                    $sql = sprintf(
                        'INSERT INTO `Website Webpage Scope Map` (`Website Webpage Scope Website Key`,`Website Webpage Scope Webpage Key`,`Website Webpage Scope Scope`,`Website Webpage Scope Scope Key`,`Website Webpage Scope Type`,`Website Webpage Scope Index`) VALUES (%d,%d,%s,%d,%s,%d) ',
                        $this->get('Webpage Website Key'), $this->id, prepare_mysql('Category'), $item['category_key'], prepare_mysql($item['item_type']), $index

                    );

                    $this->db->exec($sql);
                    $index++;

                }

            }


        }


    }

    function update_code($value, $options = '') {


        if ($this->type != 'Store') {
            return;
        }

        $value = _trim($value);
        if ($value == '') {
            $this->msg           .= ' '._('Invalid Code')."\n";
            $this->error_updated = true;
            $this->error         = true;

            return;
        }

        if ($value == $this->data['Page Code']) {
            $this->msg .= ' '._('Same value as the old record');

            return;
        }

        $old_value = $this->data['Page Code'];


        $sql = sprintf(
            "SELECT `Page Code`  FROM  `Page Store Dimension`  WHERE `Page Store Key`=%d AND `Page Code`=%s ", $this->data['Page Store Key'], prepare_mysql($value)

        );

        $result = mysql_query($sql);
        if ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $this->msg           .= ' '._('Code already used on this website')."\n";
            $this->error_updated = true;
            $this->error         = true;

            return;


        }


        $site = new Site($this->data['Page Site Key']);
        $url  = $site->data['Site URL'].'/'.strtolower($value);

        $sql = sprintf(
            "UPDATE `Page Store Dimension`  SET  `Page Code`=%s  WHERE `Page Key`=%d", prepare_mysql($value), $this->id
        );
        // print $sql;


        mysql_query($sql);
        $affected = mysql_affected_rows();
        if ($affected == -1) {
            $this->msg           .= ' '._('Record can not be updated')."\n";
            $this->error_updated = true;
            $this->error         = true;

            return;
        } elseif ($affected == 0) {
            $this->msg .= ' '._('Same value as the old record');

        } else {

            $this->msg               .= _('Code updated').", \n";
            $this->msg_updated       .= _('Code updated').", \n";
            $this->updated           = true;
            $this->new_value         = $value;
            $this->data['Page Code'] = $value;

            $save_history = true;
            if (preg_match('/no( |\_)history|nohistory/i', $options)) {
                $save_history = false;
            }

            if (!$this->new and $save_history) {
                $history_data = array(
                    'indirect_object' => 'Page Code',
                    'old_value'       => $old_value,
                    'new_value'       => $value

                );


                $this->add_history($history_data);


                $site = new Site($this->data['Page Site Key']);
                $url  = $site->data['Site URL'].'/'.strtolower($value);

                $sql = sprintf(
                    "UPDATE `Page Dimension`  SET  `Page URL`=%s  WHERE `Page Key`=%d", prepare_mysql($url), $this->id
                );

                mysql_query($sql);

                $sql = sprintf(
                    "UPDATE `Page Redirection Dimension`  SET  `Page Target URL`=%s  WHERE `Page Target Key`=%d", prepare_mysql($url), $this->id
                );

                mysql_query($sql);
            }


            //$this->update_field('Page URL',$url,'nohistory');

        }

    }

    function update_content_display_type($value, $options) {


        //'Front Page Store','Search','Product Description','Information','Product Category Catalogue','Family Category Catalogue','Family Catalogue','Department Catalogue','Registration','Client Section','Checkout','Login','Welcome','Not Found','Reset','Basket','Login Help','Thanks','Payment Limbo','Family Description','Department Description'
        //'System','Info','Department','Family','Product','FamilyCategory','ProductCategory'
        if ($value == 'Template') {
            if ($this->data['Page Store Section'] == 'Front Page Store') {
                $this->update_field(
                    'Page Store Content Template Filename', 'home', 'no_history'
                );
            }
        }
        $this->update_field(
            'Page Store Content Display Type', $value, $options
        );
        $this->update_store_search();
    }

    function display_found_in() {

        $found_in = '';
        foreach ($this->get_found_in() as $item) {
            $found_in .= $item['link'];
            break;
        }

        return $found_in;
    }

    function get_found_in() {

        $found_in = array();
        $sql      = sprintf(
            "SELECT `Page Store Found In Key` FROM  `Page Store Found In Bridge` WHERE `Page Store Key`=%d", $this->id
        );

        $res = mysql_query($sql);

        while ($row = mysql_fetch_assoc($res)) {
            $found_in_page = new Page($row['Page Store Found In Key']);
            if ($found_in_page->id) {

                $link = '<a class="found_in" href="http://'.$found_in_page->data['Page URL'].'">'.$found_in_page->data['Page Short Title'].'</a>';

                $found_in[] = array(
                    'link'           => $link,
                    'found_in_label' => $found_in_page->data['Page Short Title'],
                    'found_in_url'   => $found_in_page->data['Page URL'],
                    'found_in_key'   => $found_in_page->id,
                    'found_in_code'  => $found_in_page->data['Page Code']
                );
            }

        }

        return $found_in;

    }

    function get_related_products_data() {

        $related_products = array();
        $sql              = sprintf(
            "SELECT `Webpage Related Product Product ID`,`Webpage Related Product Product Page Key`  FROM  `Webpage Related Product Bridge` WHERE `Webpage Related Product Page Key`=%d ORDER BY `Webpage Related Product Order` ", $this->id
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {

                $related_products_page = new Page(
                    $row['Webpage Related Product Product Page Key']
                );
                if ($related_products_page->id) {


                    $link = '<a href="http://'.$related_products_page->data['Page URL'].'">'.$related_products_page->data['Page Short Title'].'</a>';

                    $related_products[] = array(
                        'link'       => $link,
                        'label'      => $related_products_page->data['Page Short Title'],
                        'url'        => $related_products_page->data['Page URL'],
                        'key'        => $related_products_page->id,
                        'product_id' => $row['Webpage Related Product Product ID'],
                        'code'       => $related_products_page->data['Page Code'],

                        'image_key' => $related_products_page->data['Page Store Image Key']

                    );
                }


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $data = array(
            'website_key' => $this->get('Page Site Key'),
            'webpage_key' => $this->id,
            'links'       => $related_products
        );

        return $data;
    }

    function unpublish() {


        $this->update_state('Offline');


        if ($this->get('Webpage State') == 'Online') {
            $icon = 'fa-rocket';
        } elseif ($this->get('Webpage State') == 'Offline') {
            $icon = ' fa-rocket discreet fa-flip-vertical';
        } elseif ($this->get('Webpage State') == 'Ready') {
            $icon = 'fa-check-circle';

        } elseif ($this->get('Webpage State') == 'InProcess') {
            $icon = 'fa-child';


        }


        $this->update_metadata = array(
            'class_html'      => array(
                'Webpage_State_Icon'    => $this->get('State Icon'),
                'Webpage_State'         => $this->get('State'),
                'preview_publish_label' => _('Republish')

            ),
            'hide_by_id'      => array(
                'unpublish_webpage_field',
                'launch_webpage_field'
            ),
            'show_by_id'      => array('republish_webpage_field'),
            'invisible_by_id' => array('link_to_live_webpage'),


        );


    }

    function get_see_also() {

        $see_also = array();
        $sql      = sprintf(
            "SELECT `Page Store See Also Key`,`Correlation Type`,`Correlation Value` FROM  `Page Store See Also Bridge` WHERE `Page Store Key`=%d ORDER BY `Webpage See Also Order` ", $this->id
        );
        $res      = mysql_query($sql);

        while ($row = mysql_fetch_assoc($res)) {
            $see_also_page = new Page($row['Page Store See Also Key']);
            if ($see_also_page->id) {

                if ($this->data['Page Store See Also Type'] == 'Manual') {
                    $formatted_correlation_type  = _('Manual');
                    $formatted_correlation_value = '';
                } else {

                    switch ($row['Correlation Type']) {
                        case 'Manual':
                            $formatted_correlation_type  = _('Manual');
                            $formatted_correlation_value = '';
                            break;
                        case 'Sales':
                            $formatted_correlation_type  = _('Sales');
                            $formatted_correlation_value = percentage(
                                $row['Correlation Value'], 1
                            );
                            break;
                        case 'Semantic':
                            $formatted_correlation_type  = _('Semantic');
                            $formatted_correlation_value = number(
                                $row['Correlation Value']
                            );
                            break;
                        case 'New':
                            $formatted_correlation_type  = _('New');
                            $formatted_correlation_value = number(
                                $row['Correlation Value']
                            );
                            break;
                        default:
                            $formatted_correlation_type  = $row['Correlation Type'];
                            $formatted_correlation_value = number(
                                $row['Correlation Value']
                            );
                            break;
                    }
                }
                //if ($site_url)
                //$link='<a href="http://'.$site_url.'/'.$see_also_page->data['Page URL'].'">'.$see_also_page->data['Page Short Title'].'</a>';

                //else
                $link = '<a href="http://'.$see_also_page->data['Page URL'].'">'.$see_also_page->data['Page Short Title'].'</a>';

                $see_also[] = array(
                    'link'                                 => $link,
                    'see_also_label'                       => $see_also_page->data['Page Short Title'],
                    'see_also_url'                         => $see_also_page->data['Page URL'],
                    'see_also_key'                         => $see_also_page->id,
                    'see_also_code'                        => $see_also_page->data['Page Code'],
                    'see_also_correlation_type'            => $row['Correlation Type'],
                    'see_also_correlation_formatted'       => $formatted_correlation_type,
                    'see_also_correlation_value'           => $row['Correlation Value'],
                    'see_also_correlation_formatted_value' => $formatted_correlation_value,
                    'see_also_image_key'                   => $see_also_page->data['Page Store Image Key']
                );
            }

        }

        return $see_also;

    }

    function delete($create_deleted_page_record = true) {


        $sql = sprintf('delete `Product Category Index` where `Product Category Index Website Key`=%d  ', $this->id);
        $this->db->exec($sql);


        $sql = sprintf('delete `Category Webpage Index` where `Category Webpage Index Webpage Key`=%d  ', $this->id);
        $this->db->exec($sql);


        $sql = sprintf('delete `Webpage Section Dimension` where `Webpage Section Webpage Key`=%d  ', $this->id);
        $this->db->exec($sql);


        $this->deleted = false;
        $sql           = sprintf(
            "DELETE FROM `Page Dimension` WHERE `Page Key`=%d", $this->id
        );


        $this->db->exec($sql);

        $sql = sprintf(
            "DELETE FROM `Page Store Dimension` WHERE `Page Key`=%d", $this->id
        );
        $this->db->exec($sql);
        $sql = sprintf(
            "DELETE FROM `Page Redirection Dimension` WHERE `Page Target Key`=%d", $this->id
        );
        $this->db->exec($sql);


        $sql = sprintf(
            "DELETE FROM `Page Store Found In Bridge` WHERE `Page Store Key`=%d", $this->id
        );
        $this->db->exec($sql);
        $sql = sprintf(
            "DELETE FROM `Page Store Found In Bridge` WHERE `Page Store Found In Key`=%d", $this->id
        );

        $this->db->exec($sql);

        $sql = sprintf(
            "DELETE FROM  `Page Store See Also Bridge` WHERE `Page Store Key`=%d", $this->id
        );
        $this->db->exec($sql);


        $sql = sprintf(
            "INSERT INTO `Page State Timeline`  (`Page Key`,`Site Key`,`Store Key`,`Date`,`State`,`Operation`) VALUES (%d,%d,%d,%s,'Offline','Deleted') ", $this->id, $this->data['Page Site Key'], $this->data['Page Site Key'], prepare_mysql(gmdate('Y-m-d H:i:s'))

        );
        $this->db->exec($sql);


        $sql = sprintf(
            "delete `Page Product Dimension` where `Page Key`=%d", $this->id
        );

        $this->db->exec($sql);
        $images = array();
        $sql    = sprintf(
            "SELECT `Image Key` FROM `Image Bridge` WHERE `Subject Type`='Page' AND `Subject Key`=%d", $this->id
        );

        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $images[] = $row['Image Key'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $sql = sprintf(
            "DELETE FROM  `Image Bridge` WHERE `Subject Type`='Page' AND `Subject Key`=%d", $this->id
        );

        $this->db->exec($sql);

        foreach ($images as $image_key) {
            $image = new Image($image_key);
            $image->delete();
            //if (!$image->deleted) {
            //    $image->update_other_size_data();
            // }


        }

        $sql = sprintf(
            "SELECT `Page Store Key`  FROM  `Page Store See Also Bridge` WHERE `Page Store See Also Key`=%d ", $this->id
        );

        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $_page = new Page ($row['Page Store Key']);
                $_page->update_see_also();
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $this->deleted = true;


        if (array_key_exists('Webpage Website Key', $this->data)) {
            $website = get_object('website', $this->data['Webpage Website Key']);
            $website->update_webpages();
        }


        if ($create_deleted_page_record) {


            $deleted_metadata = gzcompress(json_encode($this->data), 9);


            include_once 'class.PageDeleted.php';
            $data = array(
                'Page Code'                   => $this->data['Page Code'],
                'Page Key'                    => $this->id,
                'Site Key'                    => $this->data['Webpage Website Key'],
                'Store Key'                   => $this->data['Page Store Key'],
                'Page Store Section'          => $this->data['Page Store Section'],
                'Page Parent Key'             => $this->data['Page Parent Key'],
                'Page Parent Code'            => $this->data['Page Parent Code'],
                'Page Title'                  => $this->data['Webpage Browser Title'],
                'Page Short Title'            => $this->data['Webpage Name'],
                'Page Description'            => $this->data['Webpage Meta Description'],
                'Page URL'                    => $this->data['Webpage URL'],
                'Page Valid To'               => gmdate('Y-m-d H:i:s'),
                'Page Store Deleted Metadata' => $deleted_metadata


            );

            $deleted_page = new PageDeleted();
            $deleted_page->create($data);


            $abstract = sprintf(
                _('Webpage %s deleted'), sprintf(
                                           '<span class="button" onClick="change_view(\'webpage/%d\')">%s</span>', $this->id, $this->data['Page Code']
                                       )
            );


            $history_data = array(
                'History Abstract' => $abstract,
                'History Details'  => '',
                'Action'           => 'deleted'
            );
            $this->add_subject_history($history_data, true, 'No', 'Changes', $this->get_object_name(), $this->id);


            require_once 'class.Webpage_Type.php';

            $webpage_type = new Webpage_Type($this->get('Webpage Type Key'));
            $webpage_type->update_number_webpages();


            $this->new_value = $deleted_page->id;
        }
        $this->deleted = true;


    }

    function delete_external_file($external_file_key) {

        $sql = sprintf(
            "SELECT count(*) AS num FROM `Page Store External File Bridge` WHERE `Page Store External File Key`=%d AND `Page Key`!=%d", $external_file_key, $this->id
        );
        $res = mysql_query($sql);
        if ($row = mysql_fetch_assoc($res)) {
            if ($row['num'] == 0) {

                $sql = sprintf(
                    "DELETE FROM `Page Store External File Dimension` WHERE `Page Store External File Key`=%d", $external_file_key
                );

            }
        }

    }

    function update_product_totals() {
        if ($this->data['Page Type'] == 'Store') {
            $number_products              = 0;
            $number_out_of_stock_products = 0;
            $number_sold_out_products     = 0;
            $number_list_products         = 0;
            $number_button_products       = 0;

            $sql = sprintf(
                "SELECT PPD.`Product ID`,`Parent Type`,`Product Web State`  FROM `Page Product Dimension` PPD LEFT JOIN `Product Dimension` P ON (PPD.`Product ID`=P.`Product ID`) WHERE `Page Key`=%d GROUP BY PPD.`Product ID`", $this->id
            );
            //print $sql;
            //exit;

            $result = mysql_query($sql);
            while ($row = mysql_fetch_assoc($result)) {
                if (!($row['Product Web State'] == 'Offline' and $row['Parent Type'] == 'List')) {
                    $number_products++;
                    if ($row['Product Web State'] == 'Discontinued' or $row['Product Web State'] == 'Out of Stock') {
                        $number_out_of_stock_products++;
                    }
                    if ($row['Product Web State'] == 'Discontinued') {
                        $number_sold_out_products++;
                    }


                    if ($row['Parent Type'] == 'List') {
                        $number_list_products++;
                    }
                    if ($row['Parent Type'] == 'Button') {
                        $number_button_products++;
                    }


                }
            }

            $sql = sprintf(
                "UPDATE `Page Store Dimension` SET `Page Store Number Products`=%d,`Page Store Number Out of Stock Products`=%d,
			`Page Store Number Sold Out Products`=%d,`Page Store Number List Products`=%d,`Page Store Number Button Products`=%d
			WHERE `Page Key`=%d", $number_products, $number_out_of_stock_products, $number_sold_out_products, $number_list_products, $number_button_products, $this->id
            );
            mysql_query($sql);
            //print "$sql\n";
            $this->data['Page Store Number Products']              = $number_products;
            $this->data['Page Store Number Out of Stock Products'] = $number_out_of_stock_products;
            $this->data['Page Store Number Sold Out Products']     = $number_sold_out_products;
            $this->data['Page Store Number List Products']         = $number_list_products;
            $this->data['Page Store Number Button Products']       = $number_button_products;


        }

    }

    function get_formatted_store_section() {
        if ($this->data['Page Type'] != 'Store') {
            return;
        }


        switch ($this->data['Page Store Section']) {
            case 'Front Page Store':
                $formatted_store_section = _('Front Page Store');
                break;
            case 'Search':
                $formatted_store_section = _('Search');
                break;
            case 'Product Description':
                $formatted_store_section = _('Product details').' <a href="product.php?pid='.$this->data['Page Parent Key'].'">'.$this->data['Page Parent Code'].'</a>';

                break;
            case 'Information':
                $formatted_store_section = _('Information');
                break;
            case 'Category Catalogue':
                $formatted_store_section = _('Category Catalogue');
                break;
            case 'Family Catalogue':
                $formatted_store_section = _('Family Catalogue').' <a href="family.php?id='.$this->data['Page Parent Key'].'">'.$this->data['Page Parent Code'].'</a>';
                break;
            case 'Department Catalogue':
                $formatted_store_section = _('Department Catalogue').' <a href="department.php?id='.$this->data['Page Parent Key'].'">'.$this->data['Page Parent Code'].'</a>';
                break;
            case 'Store Catalogue':
                $formatted_store_section = _('Store Catalogue').' <a href="store.php?id='.$this->data['Page Parent Key'].'">'.$this->data['Page Parent Code'].'</a>';
                break;
            case 'Registration':
                $formatted_store_section = _('Registration');
                break;
            case 'Client Section':
                $formatted_store_section = _('Client Section');
                break;
            case 'Check Out':
                $formatted_store_section = _('Check Out');
                break;
            default:
                $formatted_store_section = $this->data['Page Store Section'];
                break;
        }

        return $formatted_store_section;
    }

    function display_buttom($tag) {
        return $this->display_button($tag);
    }

    function display_button($tag) {
        $html = '';
        include_once 'class.Product.php';
        $product = new Product(
            'code_store', $tag, $this->data['Page Store Key']
        );

        if ($product->id) {

            if ($this->logged) {

                switch ($this->site->data['Site Checkout Method']) {
                    case 'Mals':

                        $html .= $this->display_button_emals_commerce($product);
                        break;

                    case 'Inikoo':
                        $html .= $this->display_button_inikoo($product);

                        break;
                    default:
                        break;
                }
            } else {
                $html = $this->display_button_logged_out($product);
            }
        }

        return $html;
    }

    function display_button_emals_commerce($product) {


        if ($product->data['Product Web State'] == 'Out of Stock') {


            $sql = sprintf(
                "SELECT `Email Site Reminder Key` FROM `Email Site Reminder Dimension` WHERE `Trigger Scope`='Back in Stock' AND `Trigger Scope Key`=%d AND `User Key`=%d AND `Email Site Reminder In Process`='Yes' ", $product->id, $this->user->id

            );
            $res = mysql_query($sql);
            if ($row = mysql_fetch_assoc($res)) {
                $email_reminder = '<br/><span id="send_reminder_wait_'.$product->id.'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                        'Processing request'
                    ).'</span><span id="send_reminder_container_'.$product->id.'"  style="color:#777"><span id="send_reminder_info_'.$product->id.'" >'._(
                        "We'll notify you via email"
                    ).' <span style="cursor:pointer" id="cancel_send_reminder_'.$row['Email Site Reminder Key'].'"  onClick="cancel_send_reminder('.$row['Email Site Reminder Key'].','.$product->id.')"  >('._('Cancel').')</span></span></span>';
            } else {
                $email_reminder = '<br/>
					<span id="send_reminder_wait_'.$product->id.'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                        'Processing request'
                    ).'</span>
					<span id="send_reminder_container_'.$product->id.'" style="color:#777" >
						<span id="send_reminder_'.$product->id.'" style="cursor:pointer;" onClick="send_reminder('.$product->id.')">'._('Notify me when back in stock').' <img style="position:relative;bottom:-2px" src="art/send_mail.png"/></span>
					</span>
					<span id="send_reminder_msg_'.$product->id.'"></span>';

            }


            if ($product->data['Product Next Supplier Shipment'] != '') {
                $next_shipment = '. '._('Expected').': '.$product->get(
                        'Next Supplier Shipment'
                    );
            } else {
                $next_shipment = '';
            }

            $message = '<br/><span style="color:red;font-weight:800">'._(
                    'Out of Stock'
                ).'</span><span style="color:red">'.$next_shipment.$email_reminder.'</span>';
        } elseif ($product->data['Product Web State'] == 'Offline') {
            $message = '<br/><span style="color:red;font-weight:800">'._(
                    'Sold Out'
                ).'</span>';
        } elseif ($product->data['Product Web State'] == 'Discontinued') {
            $message = '<br/><span style="color:red;font-weight:800">'._(
                    'Sold Out'
                ).'</span>';
        } else {


            $form_id = 'order_button_'.$product->id;

            $button = '<img onmouseover="this.src=\'art/ordernow_hover_'.$this->site->data['Site Locale'].'.png\'" onmouseout="this.src=\'art/ordernow_'.$this->site->data['Site Locale'].'.png\'"    onClick="order_product_from_button(\''.$form_id
                .'\')"  style="height:28px;cursor:pointer;" src="art/ordernow_'.$this->site->data['Site Locale'].'.png" alt="'._(
                    'Order Product'
                ).'"> <span style="visibility:hidden" id="waiting_'.$form_id.'"><img src="art/loading.gif" style="height:22px;position:relative;bottom:3px"></span>';

            $message = sprintf(
                "<br/><div class='order_but' style='text-align:left'>

                             <input type='hidden' id='product_code_%s' value='%s'>
                             <input type='hidden' id='product_description_%s' value='%s %sx %s'>

                             <input type='hidden' id='return_%s' value='%s'>
                             <input type='hidden' id='price_%s' value='%s'>
                             <table border=0>
                             <tr>
                             <td>
                             <input style='height:20px;text-align:center' id='qty_%s'   type='text' size='2' class='qty' name='qty' value='1'>
                             </td>
                             <td>
                             %s
                             </td>
                             </table>



                             </div>", // $this->site->get_checkout_data('url').'/cf/add.cfm',$form_id,$form_id,
                $form_id, $product->data['Product Code'], $form_id, $product->data['Product Code'], $product->data['Product Units Per Case'], $product->data['Product Name'], $form_id, $this->data['Page URL'], $form_id,
                number_format($product->data['Product Price'], 2, '.', ''), $form_id, $button


            );
        }

        $data = array(
            'Product Price' => $product->data['Product Price'],


            'Product Units Per Case' => $product->data['Product Units Per Case'],
            'Product Currency'       => $product->get('Product Currency'),
            'Product Unit Type'      => $product->data['Product Unit Type'],


            'locale' => $this->site->data['Site Locale']
        );

        $price = '<span class="price">'.formatted_price($data).'</span><br>';

        $data = array(
            'Product Price'          => $product->data['Product RRP'],
            'Product Units Per Case' => $product->data['Product Units Per Case'],
            'Product Currency'       => $product->get('Product Currency'),
            'Product Unit Type'      => $product->data['Product Unit Type'],
            'Label'                  => _('RRP').":",

            'locale' => $this->site->data['Site Locale']
        );

        $rrp = '<span class="rrp">'.formatted_price($data).'</span><br>';


        $form = sprintf(
            '<div  class="ind_form">
                      <span class="code">%s</span><br/>
                      <span class="name">%sx %s</span><br>
                      %s
                      %s
                      %s
                      </div>', $product->data['Product Code'], $product->data['Product Units Per Case'], $product->data['Product Name'], $price, $rrp, $message
        );


        return $form;


    }

    function display_button_inikoo($product) {

        $quantity = $this->get_button_ordered_quantity($product);

        $message = $this->get_button_text($product, $quantity);
        $price   = $this->get_button_price($product);

        $rrp = $this->get_button_rrp($product);

        $form = sprintf(
            '<div  class="ind_form">
                      <div class="product_description" >
                      <span class="code">%s</span>
                      <div class="name">%sx %s</div>
                      %s
                      %s
                      </div>
                      %s
                      </div>', $product->data['Product Code'], $product->data['Product Units Per Case'], $product->data['Product Name'], $price, $rrp, $message
        );

        return $form;


    }

    function get_button_ordered_quantity($product) {
        $quantity = 0;
        if (isset($this->order) and $this->order) {

            $sql     = sprintf(
                "SELECT `Order Quantity` FROM `Order Transaction Fact` WHERE `Order Key`=%d AND `Product ID`=%d", $this->order->id, $product->id
            );
            $result1 = mysql_query($sql);
            if ($product1 = mysql_fetch_array($result1)) {
                $quantity = $product1['Order Quantity'];
            }


        }

        if ($quantity <= 0) {
            $quantity = '';
        }

        return $quantity;
    }

    function get_button_text($product, $quantity) {

        if ($product->data['Product Web State'] == 'Out of Stock') {

            $message = '';
            $sql     = sprintf(
                "SELECT `Email Site Reminder Key` FROM `Email Site Reminder Dimension` WHERE `Trigger Scope`='Back in Stock' AND `Trigger Scope Key`=%d AND `User Key`=%d AND `Email Site Reminder In Process`='Yes' ", $product->id, $this->user->id

            );
            $res     = mysql_query($sql);
            if ($row = mysql_fetch_assoc($res)) {
                $email_reminder = '<br/><span id="send_reminder_wait_'.$product->id.'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                        'Processing request'
                    ).'</span><span id="send_reminder_container_'.$product->id.'"  style="color:#777"><span id="send_reminder_info_'.$product->id.'" >'._(
                        "We'll notify you via email"
                    ).' <span style="cursor:pointer" id="cancel_send_reminder_'.$row['Email Site Reminder Key'].'"  onClick="cancel_send_reminder('.$row['Email Site Reminder Key'].','.$product->id.')"  >('._('Cancel').')</span></span></span>';
            } else {
                $email_reminder = '<br/>
					<span id="send_reminder_wait_'.$product->id.'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                        'Processing request'
                    ).'</span>
					<span id="send_reminder_container_'.$product->id.'" style="color:#777" >
					<span id="send_reminder_'.$product->id.'" style="cursor:pointer;" onClick="send_reminder('.$product->id.')">'._('Notify me when back in stock').' <img style="position:relative;bottom:-2px" src="art/send_mail.png"/></span>
					</span><span id="send_reminder_msg_'.$product->id.'"></span>';

            }


            if ($product->data['Product Next Supplier Shipment'] != '') {
                $next_shipment = '. '._('Expected').': '.$product->get(
                        'Next Supplier Shipment'
                    );
            } else {
                $next_shipment = '';
            }

            $message = '<br/><span style="color:red;font-weight:800">'._(
                    'Out of Stock'
                ).'</span><span style="color:red">'.$next_shipment.$email_reminder.'</span>';
        } elseif ($product->data['Product Web State'] == 'Offline') {
            $message = '<br/><span style="color:red;font-weight:800">'._(
                    'Sold Out'
                ).'</span>';
        } elseif ($product->data['Product Web State'] == 'Discontinued') {
            $message = '<br/><span style="color:red;font-weight:800">'._(
                    'Sold Out'
                ).'</span>';
        } else {


            $form_id = 'order_button_'.$product->id;

            if ($quantity) {
                $button_image_source = 'art/ordered_'.$this->data['Page Locale'].'.png';
                $button_alt          = _('Order Product');
                $feedback_class      = "accepted";

            } else {
                $button_image_source = 'art/ordernow_'.$this->data['Page Locale'].'.png';
                $button_alt          = _('Order Product');
                $feedback_class      = "empty";
            }
            $button      = '<img id="order_button_'.$product->id.'"    class="order_button"
			src="'.$button_image_source.'" alt="'.$button_alt.'">
			<img class="button_feedback waiting" style="display:none" id="waiting_'.$product->id.'" src="art/loading.gif" >
			<img class="button_feedback '.$feedback_class.'" id="done_'.$product->id.'" src="art/icons/accept.png" alt="ok" >';
            $input_field = sprintf(
                "<br/><div class='order_but' style='text-align:left'>
			<table border=0 onmouseover=\"over_order_button(".$product->id.")\" onmouseout=\"out_order_button(".$product->id.")\"  >
                             <tr>
                             <td>
                             <input maxlength=6 onClick=\"this.select();\" class='button_input ordered_qty' onKeyUp=\"button_changed(%d)\"  id='but_qty%s'   type='text' size='2' class='qty'  value='%s' ovalue='%s'>
                             </td>
                             <td>
                             %s
                             </td>
                             </table>



                             </div>", // $this->site->get_checkout_data('url').'/cf/add.cfm',$form_id,$form_id,
                $product->id, $product->id, $quantity, $quantity, $button


            );


            $message = $input_field;

        }

        return $message;

    }

    function get_button_price($product) {

        $price_data = array(
            'Product Price' => $product->data['Product Price'],


            'Product Units Per Case' => $product->data['Product Units Per Case'],
            'Product Currency'       => $product->get('Product Currency'),
            'Product Unit Type'      => $product->data['Product Unit Type'],
            'Label'                  => _('Price').':',

            'locale' => $this->data['Page Locale']
        );

        $price = '<span class="price">'.$this->get_formatted_price($price_data).'</span><br>';

        return $price;
    }

    function get_formatted_price($data, $options = false) {

        $_data = array(
            'Product Price'          => $data['Product Price'],
            'Product Units Per Case' => $data['Product Units Per Case'],
            'Product Currency'       => $this->currency,
            'Product Unit Type'      => $data['Product Unit Type'],
            'Label'                  => $data['Label'],
            'locale'                 => $this->site->data['Site Locale']

        );

        if (isset($data['price per unit text'])) {
            $_data['price per unit text'] = $data['price per unit text'];
        }

        return formatted_price($_data, $options);
    }

    function get_button_rrp($product) {

        if (!$product->data['Product RRP']) {
            return '';
        }

        $rrp_data = array(
            'Product Price'          => $product->data['Product RRP'],
            'Product Units Per Case' => $product->data['Product Units Per Case'],
            'Product Currency'       => $product->get('Product Currency'),
            'Product Unit Type'      => $product->data['Product Unit Type'],
            'Label'                  => _('RRP').":",

            'locale' => $this->data['Page Locale']
        );

        $rrp = '<span class="rrp">'.$this->get_formatted_price($rrp_data).'</span><br>';

        return $rrp;

    }

    function display_button_logged_out($product) {

        if ($product->data['Product Web State'] == 'Out of Stock') {
            $message = '<br/><span style="color:red;font-weight:800">'._(
                    'Out of Stock'
                ).'</span>';
        } elseif ($product->data['Product Web State'] == 'Offline') {
            $message = '<br/><span style="color:red;font-weight:800">'._(
                    'Sold Out'
                ).'</span>';
        } elseif ($product->data['Product Web State'] == 'Discontinued') {
            $message = '<br/><span style="color:red;font-weight:800">'._(
                    'Sold Out'
                ).'</span>';
        } else {
            $message = sprintf(
                '<br/><span style="color:green;font-style:italic;">'._(
                    'In stock'
                ).'. '._('For prices, please').' <a style="color:green;" href="login.php?from='.$this->id.'" >'._('login').'</a> '._('or').' <a style="color:green;" href="registration.php">'._(
                    'register'
                ).'</a> </span>'
            );
        }

        $form = sprintf(
            '<div  class="ind_form">
                      <span class="code">%s</span><br/>
                      <span class="name">%sx %s</span>%s
                      </div>', $product->data['Product Code'], $product->data['Product Units Per Case'], $product->data['Product Name'], $message
        );


        return $form;
    }

    function display_button_inikoo_basket_style($product) {

        $quantity = 0;
        if (isset($this->order) and $this->order) {

            $sql     = sprintf(
                "SELECT `Order Quantity` FROM `Order Transaction Fact` WHERE `Order Key`=%d AND `Product ID`=%d", $this->order->id, $product->id
            );
            $result1 = mysql_query($sql);
            if ($product1 = mysql_fetch_array($result1)) {
                $quantity = $product1['Order Quantity'];
            }


        }


        if ($quantity <= 0) {
            $quantity = '';
        }

        if ($product->data['Product Web State'] == 'Out of Stock') {


            $sql = sprintf(
                "SELECT `Email Site Reminder Key` FROM `Email Site Reminder Dimension` WHERE `Trigger Scope`='Back in Stock' AND `Trigger Scope Key`=%d AND `User Key`=%d AND `Email Site Reminder In Process`='Yes' ", $product->id, $this->user->id

            );
            $res = mysql_query($sql);
            if ($row = mysql_fetch_assoc($res)) {
                $email_reminder = '<br/><span id="send_reminder_wait_'.$product->id.'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                        'Processing request'
                    ).'</span><span id="send_reminder_container_'.$product->id.'"  style="color:#777"><span id="send_reminder_info_'.$product->id.'" >'._(
                        "We'll notify you via email"
                    ).' <span style="cursor:pointer" id="cancel_send_reminder_'.$row['Email Site Reminder Key'].'"  onClick="cancel_send_reminder('.$row['Email Site Reminder Key'].','.$product->id.')"  >('._('Cancel').')</span></span></span>';
            } else {
                $email_reminder = '<br/>
					<span id="send_reminder_wait_'.$product->id.'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                        'Processing request'
                    ).'</span>
					<span id="send_reminder_container_'.$product->id.'" style="color:#777" >
						<span id="send_reminder_'.$product->id.'" style="cursor:pointer;" onClick="send_reminder('.$product->id.')">'._('Notify me when back in stock').' <img style="position:relative;bottom:-2px" src="art/send_mail.png"/></span>
					</span>
					<span id="send_reminder_msg_'.$product->id.'"></span>';

            }


            if ($product->data['Product Next Supplier Shipment'] != '') {
                $next_shipment = '. '._('Expected').': '.$product->get(
                        'Next Supplier Shipment'
                    );
            } else {
                $next_shipment = '';
            }

            $message = '<br/><span style="color:red;font-weight:800">'._(
                    'Out of Stock'
                ).'</span><span style="color:red">'.$next_shipment.$email_reminder.'</span>';
        } elseif ($product->data['Product Web State'] == 'Offline') {
            $message = '<br/><span style="color:red;font-weight:800">'._(
                    'Sold Out'
                ).'</span>';
        } elseif ($product->data['Product Web State'] == 'Discontinued') {
            $message = '<br/><span style="color:red;font-weight:800">'._(
                    'Sold Out'
                ).'</span>';
        } else {

            $form_id = 'order_button_'.$product->id;

            $button = '<img id="order_button_'.$product->id.'"    class="order_button" onmouseover="this.src=\'art/ordernow_hover_'.$this->site->data['Site Locale'].'.png\'" onmouseout="this.src=\'art/ordernow_'.$this->site->data['Site Locale']
                .'.png\'"    onClick="order_product_from_button(\''.$product->id.'\',\''.$this->order->id.'\',\''.$this->id.'\',\''.$this->data['Page Store Section Type'].'\')"   src="art/ordernow_'.$this->site->data['Site Locale'].'.png" alt="'._('Order Product').'">
			<img class="button_feedback waiting" style="display:none" id="waiting_'.$product->id.'" src="art/loading.gif" >
			<img class="button_feedback" style="display:none" id="done_'.$product->id.'" src="art/icons/accept.png" alt="ok" >';

            $message = sprintf(
                "<div class='order_but' style='text-align:left'>


                             <table border=0 >
                             <tr>
                             <td>
                             <input class='button_input' onKeyUp=\"button_changed(%d)\"  id='but_qty%s'   type='text' size='2'   value='%s' ovalue='%s'>
                             </td>
                             <td>
                             %s
                             </td>
                             </table>



                             </div>", // $this->site->get_checkout_data('url').'/cf/add.cfm',$form_id,$form_id,
                $product->id, $product->id, $quantity, $quantity, $button


            );


        }

        $data = array(
            'Product Price' => $product->data['Product Price'],


            'Product Units Per Case' => $product->data['Product Units Per Case'],
            'Product Currency'       => $product->get('Product Currency'),
            'Product Unit Type'      => $product->data['Product Unit Type'],


            'locale' => $this->site->data['Site Locale']
        );

        $price = '<span class="price">'.formatted_price($data).'</span><br>';

        $data = array(
            'Product Price'          => $product->data['Product RRP'],
            'Product Units Per Case' => $product->data['Product Units Per Case'],
            'Product Currency'       => $product->get('Product Currency'),
            'Product Unit Type'      => $product->data['Product Unit Type'],
            'Label'                  => _('RRP').":",

            'locale' => $this->site->data['Site Locale']
        );

        $rrp = '<span class="rrp">'.formatted_price($data).'</span><br>';


        $form = sprintf(
            '<div  class="ind_form">
                      <span class="code">%s</span><br/>
                      <span class="name">%sx %s</span><br>
                      %s
                      %s
                      %s
                      </div>', $product->data['Product Code'], $product->data['Product Units Per Case'], $product->data['Product Name'], $price, $rrp, $message
        );


        return $form;


    }

    function display_list($list_code = 'default') {
        if (!$this->data['Page Type'] == 'Store' or !$this->data['Page Store Section'] == 'Family Catalogue') {
            return '';
        }

        $products        = $this->get_products_from_list($list_code);
        $this->print_rrp = false;

        if (count($products) == 0) {
            return;
        }

        if ($this->logged) {
            return $this->display_list_logged_in($products);
        } else {
            return $this->display_list_logged_out($products);
        }
    }

    function get_products_from_list($list_code) {

        $products = array();
        $sql      = sprintf(
            "SELECT * FROM `Page Product List Dimension` WHERE `Page Key`=%d AND `Page Product List Code`=%s", $this->id, prepare_mysql($list_code)
        );
        $res      = mysql_query($sql);
        if ($row = mysql_fetch_assoc($res)) {
            $family_key = $row['Page Product List Parent Key'];
            if ($row['Page Product List Type'] == 'FamilyList') {
                switch ($row['List Order']) {
                    case 'Code':
                        $order_by = '`Product Code File As`';
                        break;
                    case 'Name':
                        $order_by = '`Product Name`';
                        break;
                    case 'Special Characteristic':
                        $order_by = '`Product Special Characteristic`';
                        break;
                    case 'Price':
                        $order_by = '`Product Price`';
                        break;
                    case 'RRP':
                        $order_by = '`Product RRP`';
                        break;
                    case 'Sales':
                        $order_by = '`Product 1 Year Acc Quantity Ordered` desc';
                        break;
                    case 'Date':
                        $order_by = '`Product Valid From`';
                        break;
                    default:
                        $order_by = '`Product Code File As`';
                        break;
                }

                $limit = sprintf('limit %d', $row['List Max Items']);


                if ($row['Range'] != '') {
                    $range = preg_split('/-/', $row['Range']);

                    if ($range[0] == 'a' and $range[1] == 'z') {
                        $range_where = '';
                    } else {
                        if ($range[1] == 'z') {
                            $range_where = sprintf(
                                "and  $order_by>=%s  ", prepare_mysql($range[0])
                            );

                        } elseif ($range[0] == 'a') {
                            $range_where = sprintf(
                                "and  $order_by<=%s  ", prepare_mysql(++$range[1])
                            );

                        } else {
                            $range_where = sprintf(
                                "and  $order_by>=%s  and $order_by<=%s", prepare_mysql($range[0]), prepare_mysql(++$range[1])
                            );

                        }
                    }

                } else {
                    $range_where = '';
                }
                $sql = sprintf(
                    "SELECT `Product Next Supplier Shipment`,`Product Currency`,`Product Name`,`Product ID`,`Product Code`,`Product Price`,`Product RRP`,`Product Units Per Case`,`Product Unit Type`,`Product Web State`,`Product Special Characteristic` FROM `Product Dimension` WHERE `Product Family Key`=%d AND `Product Web State`!='Offline'  %s ORDER BY %s %s",
                    $family_key, $range_where, $order_by, $limit
                );
                //print $sql;
                $result = mysql_query($sql);
                while ($row2 = mysql_fetch_array($result, MYSQL_ASSOC)) {

                    if ($row2['Product Next Supplier Shipment'] == '') {
                        $row2['Next Supplier Shipment'] = '';
                    } else {
                        $row2['Next Supplier Shipment'] = strftime(
                            "%a, %e %b %y", strtotime(
                                              $row2['Product Next Supplier Shipment'].' +0:00'
                                          )
                        );
                    }


                    $products[$row2['Product ID']] = $row2;


                }

            }


            switch ($row['List Product Description']) {
                case 'Units Name':
                    foreach ($products as $key => $product) {
                        $products[$key]['description']      = sprintf(
                            "%dx %s", $product['Product Units Per Case'], $product['Product Name']
                        );
                        $products[$key]['long_description'] = sprintf(
                            "%dx %s", $product['Product Units Per Case'], $product['Product Name']
                        );
                    }
                    break;
                case 'Units Name RRP':


                    foreach ($products as $key => $product) {
                        $rrp = money(
                            $product['Product RRP'], $product['Product Currency'], $this->site->data['Site Locale']
                        );
                        $tmp = sprintf(
                            "%dx %s <span class='rrp' >(%s: %s)</span>", $product['Product Units Per Case'], $product['Product Name'], _('RRP'), $rrp
                        );

                        $products[$key]['description']      = $tmp;
                        $products[$key]['long_description'] = $tmp;
                    }
                    break;
                case 'Units Special Characteristic':
                    foreach ($products as $key => $product) {
                        $products[$key]['description']      = sprintf(
                            "%dx %s", $product['Product Units Per Case'], $product['Product Special Characteristic']
                        );
                        $products[$key]['long_description'] = sprintf(
                            "%dx %s", $product['Product Units Per Case'], $product['Product Name']
                        );

                    }
                    break;
                case 'Units Special Characteristic RRP':


                    foreach ($products as $key => $product) {
                        $rrp = money(
                            $product['Product RRP'], $product['Product Currency'], $this->site->data['Site Locale']
                        );

                        $products[$key]['description']      = sprintf(
                            "%dx %s <span class='rrp' >(%s: %s)</span>", $product['Product Units Per Case'], $product['Product Special Characteristic'], _('RRP'), $rrp
                        );
                        $products[$key]['long_description'] = sprintf(
                            "%dx %s <span class='rrp' >(%s: %s)</span>", $product['Product Units Per Case'], $product['Product Name'], _('RRP'), $rrp
                        );

                    }
                    break;

                default:
                    foreach ($products as $key => $product) {
                        $products[$key]['description']      = sprintf(
                            "%dx %s", $product['Product Units Per Case'], $product['Product Name']
                        );
                        $products[$key]['long_description'] = sprintf(
                            "%dx %s", $product['Product Units Per Case'], $product['Product Name']
                        );

                    }
                    break;
            }

        }


        return $products;
    }

    function display_list_logged_in($products) {

        $print_rrp      = true;
        $number_records = count($products);
        $out_of_stock   = _('OoS');
        $discontinued   = _('Sold Out');


        $form        = sprintf(
            '<table border=0  class="product_list form" style="position:relative;z-index:2;">'
        );
        $rrp_label   = '';
        $price_label = '';

        //        $form.='<tr class="list_info" ><td colspan="4"><p>'.$price_label.$rrp_label.'</p></td></tr>';

        $form .= $this->get_list_header($products);

        switch ($this->site->data['Site Checkout Method']) {
            case 'Mals':

                $form .= $this->get_list_emals_commerce($products);
                break;
            case 'AW':

                $form .= $this->get_list_aw_checkout($products);
                break;
            case 'Inikoo':
                $form .= $this->get_list_inikoo($products);

                break;
            default:
                break;
        }


        $form .= '</table>';

        return $form;
    }

    function get_list_header($products) {

        $html = '';

        switch ($this->data['Page Store Section']) {
            case 'Family Catalogue':
                $family = new Family($this->data['Page Parent Key']);
                $html   = sprintf(
                    '<tr class="list_info"><td colspan=5>%s %s</td></tr>', $family->data['Product Family Code'], $family->data['Product Family Name']
                );

                break;
            default:

                break;
        }

        $html .= sprintf(
            '<tr class="list_info price"><td style="padding-top:0;padding-bottom:0;text-align:left" colspan="6">%s </td></tr>', $this->get_list_price_header_auto($products)
        );
        $html .= sprintf(
            '<tr class="list_info rrp"><td style="padding-top:0;padding-bottom:0;" colspan="6">%s</td></tr>', $this->get_list_rrp_header_auto($products)
        );


        return $html;

    }

    function get_list_price_header_auto($products) {
        $price_label                = '';
        $min_price                  = 999999999999;
        $max_price                  = -99999999999;
        $number_products_with_price = 0;


        $same_units = true;
        $units      = 1;
        $counter    = 0;
        foreach ($products as $product) {

            if ($counter and $product['Product Units Per Case'] != $units) {
                $same_units = false;
            } else {
                $units = $product['Product Units Per Case'];
            }

            if ($product['Product Price']) {
                $number_products_with_price++;
                if ($min_price > $product['Product Price']) {
                    $min_price = $product['Product Price'];
                }
                if ($max_price < $product['Product Price']) {
                    $max_price = $product['Product Price'];
                }
            }
            $counter++;
        }


        if ($number_products_with_price and $same_units) {
            $price = $this->get_formatted_price(
                array(
                    'Product Price'          => $min_price,
                    'Product Units Per Case' => $units,
                    'Product Unit Type'      => '',
                    'Label'                  => ($min_price == $max_price ? _(
                            'Price'
                        ) : _('Price from')).':'
                )
            );

            if ($min_price == $max_price) {
                $price_label = '<span class="price">'.$price.'</span>';
            } else {
                $price_label = '<span class="price">'.$price.'</span>';
            }

        }

        return $price_label;
    }

    function get_list_rrp_header_auto($products) {
        $rrp_label                = '';
        $min_rrp                  = 999999999999;
        $max_rrp                  = -99999999999;
        $number_products_with_rrp = 0;

        $same_units = true;
        $units      = 1;
        $counter    = 0;

        foreach ($products as $product) {

            if ($counter and $product['Product Units Per Case'] != $units) {
                $same_units = false;
            } else {
                $units = $product['Product Units Per Case'];
            }


            if ($product['Product RRP']) {
                $number_products_with_rrp++;
                if ($min_rrp > ($product['Product RRP'])) {
                    $min_rrp = $product['Product RRP'];
                }
                if ($max_rrp < $product['Product RRP']) {
                    $max_rrp = $product['Product RRP'];
                }
            }

            $counter++;
        }

        if ($number_products_with_rrp and $same_units) {
            $rrp = $this->get_formatted_price(
                array(
                    'Product Price'          => $min_rrp,
                    'Product Units Per Case' => $units,
                    'Product Unit Type'      => '',
                    'Label'                  => ($min_rrp == $max_rrp ? _('RRP') : _('RRP from')).':'
                )
            );

            if ($min_rrp == $max_rrp) {
                $rrp_label = '<span class="rrp">'.$rrp.'</span>';
            } else {
                $rrp_label = '<span class="rrp">'.$rrp.'</span>';
            }

        }

        return $rrp_label;
    }

    function get_list_emals_commerce($products) {


        $form_id = "order_form".rand();

        $form = sprintf(
            '
                      <form action="%s" method="post" name="'.$form_id.'" id="'.$form_id.'" >
                      <input type="hidden" name="userid" value="%s">
                      <input type="hidden" name="nocart">
                        <input type="hidden" name="return" value="%s">
                        <input type="hidden" name="sd" value="ignore">
                      '


            , $this->site->get_checkout_data('url').'/cf/addmulti.cfm', $this->site->get_checkout_data('id'), $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']
        );

        //$form='<form><table id="list_'.$form_id.'" border=1>';
        $form    = '<tbody id="list_'.$form_id.'" >';
        $counter = 1;
        foreach ($products as $product) {


            if ($this->print_rrp) {

                $rrp = $this->get_formatted_rrp(
                    array(
                        'Product RRP'            => $product['Product RRP'],
                        'Product Units Per Case' => $product['Product Units Per Case'],
                        'Product Unit Type'      => $product['Product Unit Type']
                    ), array('show_unit' => $show_unit)
                );

            } else {
                $rrp = '';
            }


            $price = $this->get_formatted_price(
                array(
                    'Product Price'          => $product['Product Price'],
                    'Product Units Per Case' => 1,
                    'Product Unit Type'      => '',
                    'Label'                  => '',
                    'price per unit text'    => ''

                )
            );


            if ($product['Product Web State'] == 'Out of Stock') {


                $sql = sprintf(
                    "SELECT `Email Site Reminder Key` FROM `Email Site Reminder Dimension` WHERE `Trigger Scope`='Back in Stock' AND `Trigger Scope Key`=%d AND `User Key`=%d AND `Email Site Reminder In Process`='Yes' ", $product['Product ID'], $this->user->id

                );
                $res = mysql_query($sql);
                if ($row = mysql_fetch_assoc($res)) {
                    $email_reminder = '<br/><span id="send_reminder_wait_'.$product['Product ID'].'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                            'Processing request'
                        ).'</span><span id="send_reminder_container_'.$product['Product ID'].'"  style="color:#777"><span id="send_reminder_info_'.$product['Product ID'].'" >'._(
                            "We'll notify you via email"
                        ).' <span style="cursor:pointer" id="cancel_send_reminder_'.$row['Email Site Reminder Key'].'"  onClick="cancel_send_reminder('.$row['Email Site Reminder Key'].','.$product['Product ID'].')"  >('._('Cancel').')</span></span></span>';
                } else {
                    $email_reminder = '<br/><span id="send_reminder_wait_'.$product['Product ID'].'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                            'Processing request'
                        ).'</span><span id="send_reminder_container_'.$product['Product ID'].'" style="color:#777" ><span id="send_reminder_'.$product['Product ID'].'" style="cursor:pointer;" onClick="send_reminder('.$product['Product ID'].')">'._(
                            'Notify me when back in stock'
                        ).' <img style="position:relative;bottom:-2px" src="art/send_mail.png"/></span></span><span id="send_reminder_msg_'.$product['Product ID'].'"></span></span>';

                }


                $class_state = 'out_of_stock';

                if ($product['Product Next Supplier Shipment'] != '') {
                    $out_of_stock_label  = _('Out of stock').', '._('expected').': '.$product['Next Supplier Shipment'];
                    $out_of_stock_label2 = _('Expected').': '.$product['Next Supplier Shipment'];

                } else {
                    $out_of_stock_label  = _('Out of stock');
                    $out_of_stock_label2 = _('Out of stock');

                }

                $input = ' <span class="out_of_stock" style="font-size:80%" title="'.$out_of_stock_label.'">'._('OoS').'</span>';
                $input = '';


            } elseif ($product['Product Web State'] == 'Discontinued') {
                $class_state = 'discontinued';
                $input       = ' <span class="discontinued">('._('Sold Out').')</span>';

            } else {

                $input = sprintf(
                    '<input   id="qty_%s_%s"  type="text" value=""  >', $form_id, $counter
                );


            }

            $tr_style = '';

            if ($counter == 1) {
                $tr_class = 'top';
            } else {
                $tr_class = '';
            }


            if ($product['Product Web State'] == 'Out of Stock') {
                $tr_class    .= 'out_of_stock_tr';
                $tr_style    = "background-color:rgba(255,209,209,.6);border-top:1px solid #FF9999;;border-bottom:1px solid #FFB2B2;font-size:95%;padding-bottom:0px;";
                $description = $product['description']."<br/><span class='out_of_stock' style='opacity:.6;filter: alpha(opacity = 60);' >$out_of_stock_label2</span>$email_reminder";
            } else {
                $tr_style    = "padding-bottom:5px";
                $description = $product['description'];
            }


            $form .= sprintf(
                '
			<tr id="product_item_%s_%s" class="product_item %s" style="%s" counter="%s">

                           <td class="code" style="vertical-align:top;">%s</td>
                           <td class="price" style="vertical-align:top;">%s</td>
                           <td class="input" style="vertical-align:top;">
                           %s
                           <input type="hidden" id="price_%s_%s" value="%s"  />
                           <input type="hidden" id="product_%s_%s"  value="%s %s" />
                           </td>
                           <td class="description" style="vertical-align:top;">%s</td>
                           </tr>'."\n", $form_id, $counter, $tr_class, $tr_style, $counter,


                $product['Product Code'], $price,

                $input, $form_id, $counter, number_format($product['Product Price'], 2, '.', ''), $form_id, $counter, $product['Product Code'], $this->clean_accents($product['long_description']), $description


            );


            $counter++;
        }


        $form .= sprintf(
            '<tr ><td colspan="4">
                       <input type="hidden" name="xreturn" value="%s">

                       </td></tr>
                       <tr><td colspan=1></td><td colspan="3">
                       <img onmouseover="this.src=\'art/ordernow_hover_%s.png\'" onmouseout="this.src=\'art/ordernow_%s.png\'"   onClick="order_from_list(\''.$form_id.'\')" style="height:30px;cursor:pointer" src="art/ordernow_%s.png" alt="'._('Order Product').'">
                        <img src="art/loading.gif" style="height:24px;position:relative;bottom:3px;visibility:hidden" id="waiting_%s">
                        </td></tr>
                       </tbody>
                       ', $this->data['Page URL'], $this->site->data['Site Locale'], $this->site->data['Site Locale'], $this->site->data['Site Locale'], $form_id
        );

        return $form;
    }

    function get_formatted_rrp($data, $options = false) {

        $data = array(
            'Product RRP'            => $data['Product RRP'],
            'Product Units Per Case' => $data['Product Units Per Case'],
            'Product Currency'       => $this->currency,
            'Product Unit Type'      => $data['Product Unit Type'],
            'Label'                  => $data['Label'],
            'locale'                 => $this->site->data['Site Locale']
        );
        if (isset($data['price per unit text'])) {
            $_data['price per unit text'] = $data['price per unit text'];
        }

        return formatted_rrp($data, $options);
    }

    function clean_accents($str) {


        $str = preg_replace('/é|è|ê|ë|æ/', 'e', $str);
        $str = preg_replace('/á|à|â|ã|ä|å|æ|ª/', 'a', $str);
        $str = preg_replace('/ù|ú|û|ü/', 'u', $str);
        $str = preg_replace('/ò|ó|ô|õ|ö|ø|°/', 'o', $str);
        $str = preg_replace('/ì|í|î|ï/', 'i', $str);

        $str = preg_replace('/É|È|Ê|Ë|Æ/', 'E', $str);
        $str = preg_replace('/Á|À|Â|Ã|Ä|Å|Æ|ª/', 'A', $str);
        $str = preg_replace('/Ù|Ú|Û|Ü/', 'U', $str);
        $str = preg_replace('/Ò|Ó|Ô|Õ|Ö|Ø|°/', 'O', $str);
        $str = preg_replace('/Ì|Í|Î|Ï/', 'I', $str);

        $str = preg_replace('/ñ/', 'n', $str);
        $str = preg_replace('/Ñ/', 'N', $str);
        $str = preg_replace('/ç|¢|©/', 'c', $str);
        $str = preg_replace('/Ç/', 'C', $str);
        $str = preg_replace('/ß|§/i', 's', $str);

        return $str;
    }

    function get_list_aw_checkout($products) {


        $form_id = "order-form".rand();
        //<input type="hidden" name="userid" value="%s">
        $form    = sprintf(
            '
                      <form action="%s" method="post" name="'.$form_id.'" id="'.$form_id.'" >



                       <input type="hidden" name="customer_last_order" value="%s">
 						<input type="hidden" name="customer_key" value="%s">
                      <input type="hidden" name="nnocart"> ', $this->site->get_checkout_data('url').'/shopping_cart.php', // $this->site->get_checkout_data('id'),
            $this->customer->get('Customer Last Order Date'), $this->customer->id

        );
        $counter = 1;
        foreach ($products as $product) {


            if ($this->print_rrp) {

                $rrp = $this->get_formatted_rrp(
                    array(
                        'Product RRP'            => $product['Product RRP'],
                        'Product Units Per Case' => $product['Product Units Per Case'],
                        'Product Unit Type'      => $product['Product Unit Type']
                    ), array('show_unit' => $show_unit)
                );

            } else {
                $rrp = '';
            }


            $price = $this->get_formatted_price(
                array(
                    'Product Price'          => $product['Product Price'],
                    'Product Units Per Case' => 1,
                    'Product Unit Type'      => '',
                    'Label'                  => '',
                    'price per unit text'    => ''

                )
            );


            if ($product['Product Web State'] == 'Out of Stock') {


                $sql = sprintf(
                    "SELECT `Email Site Reminder Key` FROM `Email Site Reminder Dimension` WHERE `Trigger Scope`='Back in Stock' AND `Trigger Scope Key`=%d AND `User Key`=%d AND `Email Site Reminder In Process`='Yes' ", $product['Product ID'], $this->user->id

                );
                $res = mysql_query($sql);
                if ($row = mysql_fetch_assoc($res)) {
                    $email_reminder = '<br/><span id="send_reminder_wait_'.$product['Product ID'].'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                            'Processing request'
                        ).'</span><span id="send_reminder_container_'.$product['Product ID'].'"  style="color:#777"><span id="send_reminder_info_'.$product['Product ID'].'" >'._(
                            "We'll notify you via email"
                        ).' <span style="cursor:pointer" id="cancel_send_reminder_'.$row['Email Site Reminder Key'].'"  onClick="cancel_send_reminder('.$row['Email Site Reminder Key'].','.$product['Product ID'].')"  >('._('Cancel').')</span></span></span>';
                } else {
                    $email_reminder = '<br/><span id="send_reminder_wait_'.$product['Product ID'].'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                            'Processing request'
                        ).'</span><span id="send_reminder_container_'.$product['Product ID'].'" style="color:#777" ><span id="send_reminder_'.$product['Product ID'].'" style="cursor:pointer;" onClick="send_reminder('.$product['Product ID'].')">'._(
                            'Notify me when back in stock'
                        ).' <img style="position:relative;bottom:-2px" src="art/send_mail.png"/></span></span><span id="send_reminder_msg_'.$product['Product ID'].'"></span></span>';

                }


                $class_state = 'out_of_stock';

                if ($product['Product Next Supplier Shipment'] != '') {
                    $out_of_stock_label  = _('Out of stock').', '._('expected').': '.$product['Next Supplier Shipment'];
                    $out_of_stock_label2 = _('Expected').': '.$product['Next Supplier Shipment'];

                } else {
                    $out_of_stock_label  = _('Out of stock');
                    $out_of_stock_label2 = _('Out of stock');

                }

                $input = ' <span class="out_of_stock" style="font-size:80%" title="'.$out_of_stock_label.'">'._('OoS').'</span>';
                $input = '';


            } elseif ($product['Product Web State'] == 'Discontinued') {
                $class_state = 'discontinued';
                $input       = ' <span class="discontinued">('._('Sold Out').')</span>';

            } else {

                $input = sprintf(
                    '<input name="qty%s"  id="qty%s"  type="text" value=""  >', $counter, $counter
                );


            }

            $tr_style = '';

            if ($counter == 1) {
                $tr_class = 'top';
            } else {
                $tr_class = '';
            }


            if ($product['Product Web State'] == 'Out of Stock') {
                $tr_class    .= 'out_of_stock_tr';
                $tr_style    = "background-color:rgba(255,209,209,.6);border-top:1px solid #FF9999;;border-bottom:1px solid #FFB2B2;font-size:95%;padding-bottom:0px;";
                $description = $product['description']."<br/><span class='out_of_stock' style='opacity:.6;filter: alpha(opacity = 60);' >$out_of_stock_label2</span>$email_reminder";
            } else {
                $tr_style    = "padding-bottom:5px";
                $description = $product['description'];
            }


            $form .= sprintf(
                '<tr class="%s" style="%s">
                           <input type="hidden" name="price%s" value="%s"  >
                           <input type="hidden" name="product%s"  value="%s %s" >
                           <td class="code" style="vertical-align:top;">%s</td>
                           <td class="price" style="vertical-align:top;">%s</td>
                           <td class="input" style="vertical-align:top;">
                           %s
                           </td>
                           <td class="description" style="vertical-align:top;">%s</td>
                           </tr>'."\n", $tr_class, $tr_style,

                $counter, number_format($product['Product Price'], 2, '.', ''), $counter, $product['Product Code'], $this->clean_accents($product['long_description']),

                $product['Product Code'], $price,

                $input,

                $description


            );


            $counter++;
        }


        $form .= sprintf(
            '<tr ><td colspan="4">
                       <input type="hidden" name="return" value="%s">

                       </td></tr></form>
                       <tr><td colspan=1></td><td colspan="3">
                       <img onmouseover="this.src=\'art/ordernow_hover_%s.png\'" onmouseout="this.src=\'art/ordernow_%s.png\'"   onClick="document.forms[\''.$form_id.'\'].submit();" style="height:30px;cursor:pointer" src="art/ordernow_%s.png" alt="'._('Order Product').'">
                        </td></tr>
                       </table>
                       ', $this->data['Page URL'], $this->site->data['Site Locale'], $this->site->data['Site Locale'], $this->site->data['Site Locale']
        );

        return $form;


        ////========

        $form    = sprintf(
            '
                      <form action="%s" method="post">
                      <input type="hidden" name="userid" value="%s">
                      <input type="hidden" name="customer_last_order" value="%s">
 						<input type="hidden" name="customer_key" value="%s">
                      <input type="hidden" name="nnocart"> ', $this->site->get_checkout_data('url').'/shopping_cart.php', $this->site->get_checkout_data('id'), $this->customer->get('Customer Last Order Date'), $this->customer->id

        );
        $counter = 1;
        foreach ($products as $product) {


            if ($this->print_rrp) {

                $rrp = $this->get_formatted_rrp(
                    array(
                        'Product RRP'            => $product['Product RRP'],
                        'Product Units Per Case' => $product['Product Units Per Case'],
                        'Product Unit Type'      => $product['Product Unit Type']
                    ), array('show_unit' => $show_unit)
                );

            } else {
                $rrp = '';
            }


            $price = $this->get_formatted_price(
                array(
                    'Product Price'          => $product['Product Price'],
                    'Product Units Per Case' => 1,
                    'Product Unit Type'      => '',
                    'Label'                  => '',
                    'price per unit text'    => ''

                )
            );


            if ($product['Product Web State'] == 'Out of Stock') {
                $class_state = 'out_of_stock';

                $input = ' <span class="out_of_stock" style="font-size:70%">'._(
                        'OoS'
                    ).'</span>';


            } elseif ($product['Product Web State'] == 'Discontinued') {
                $class_state = 'discontinued';
                $input       = ' <span class="discontinued">('._('Sold Out').')</span>';

            } else {

                $input = sprintf(
                    '<input name="qty%s"  id="qty%s"  type="text" value=""  >', $counter, $counter
                );


            }


            if ($counter == 1) {
                $tr_class = 'class="top"';
            } else {
                $tr_class = '';
            }


            $form .= sprintf(
                '<tr %s >
                           <input type="hidden" name="price%s" value="%s"  >
                           <input type="hidden" name="product%s"  value="%s %s" >
                           <td class="code">%s</td>
                           <td class="price">%s</td>
                           <td class="input">
                           %s
                           </td>
                           <td class="description">%s</td>
                           </tr>'."\n", $tr_class,

                $counter, number_format($product['Product Price'], 2, '.', ''), $counter, $product['Product Code'], $this->clean_accents($product['long_description']),

                $product['Product Code'], $price,

                $input,


                $product['description']


            );


            $counter++;
        }


        $form .= sprintf(
            '<tr class="space"><td colspan="4">
                       <input type="hidden" name="return" value="%s">
                       <input class="button" name="Submit" type="submit"  value="'._('Order Product').'">
                       <input class="button" name="Reset" type="reset"  id="Reset" value="'._('Reset').'"></td></tr></form></table>
                       ', $this->data['Page URL']
        );

        return $form;
    }

    function get_list_inikoo($products) {

        if (isset($this->order) and $this->order) {
            $order_key = $this->order->id;
        } else {
            $order_key = 0;
        }

        $form_id = "order_form".rand();


        //$form='<form><table id="list_'.$form_id.'" border=1>';
        $form    = '<tbody id="list_'.$form_id.'" >';
        $counter = 1;


        $number_fields_with_ordered_products = 0;

        foreach ($products as $product) {


            if ($this->print_rrp) {

                $rrp = $this->get_formatted_rrp(
                    array(
                        'Product RRP'            => $product['Product RRP'],
                        'Product Units Per Case' => $product['Product Units Per Case'],
                        'Product Unit Type'      => $product['Product Unit Type']
                    ), array('show_unit' => $show_unit)
                );

            } else {
                $rrp = '';
            }


            $price = $this->get_formatted_price(
                array(
                    'Product Price'          => $product['Product Price'],
                    'Product Units Per Case' => 1,
                    'Product Unit Type'      => '',
                    'Label'                  => '',
                    'price per unit text'    => ''

                )
            );


            if ($product['Product Web State'] == 'Out of Stock') {


                $sql = sprintf(
                    "SELECT `Email Site Reminder Key` FROM `Email Site Reminder Dimension` WHERE `Trigger Scope`='Back in Stock' AND `Trigger Scope Key`=%d AND `User Key`=%d AND `Email Site Reminder In Process`='Yes' ", $product['Product ID'], $this->user->id

                );
                $res = mysql_query($sql);
                if ($row = mysql_fetch_assoc($res)) {
                    $email_reminder = '<br/><span id="send_reminder_wait_'.$product['Product ID'].'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                            'Processing request'
                        ).'</span><span id="send_reminder_container_'.$product['Product ID'].'"  style="color:#777"><span id="send_reminder_info_'.$product['Product ID'].'" >'._(
                            "We'll notify you via email"
                        ).' <span style="cursor:pointer" id="cancel_send_reminder_'.$row['Email Site Reminder Key'].'"  onClick="cancel_send_reminder('.$row['Email Site Reminder Key'].','.$product['Product ID'].')"  >('._('Cancel').')</span></span></span>';
                } else {
                    $email_reminder = '<br/><span id="send_reminder_wait_'.$product['Product ID'].'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                            'Processing request'
                        ).'</span><span id="send_reminder_container_'.$product['Product ID'].'" style="color:#777" ><span id="send_reminder_'.$product['Product ID'].'" style="cursor:pointer;" onClick="send_reminder('.$product['Product ID'].')">'._(
                            'Notify me when back in stock'
                        ).' <img style="position:relative;bottom:-2px" src="art/send_mail.png"/></span></span><span id="send_reminder_msg_'.$product['Product ID'].'"></span></span>';

                }


                $class_state = 'out_of_stock';

                if ($product['Product Next Supplier Shipment'] != '') {
                    $out_of_stock_label  = _('Out of stock').', '._('expected').': '.$product['Next Supplier Shipment'];
                    $out_of_stock_label2 = _('Expected').': '.$product['Next Supplier Shipment'];

                } else {
                    $out_of_stock_label  = _('Out of stock');
                    $out_of_stock_label2 = _('Out of stock');

                }

                $input = ' <span class="out_of_stock" style="font-size:80%" title="'.$out_of_stock_label.'">'._('OoS').'</span>';
                $input = '';


            } elseif ($product['Product Web State'] == 'Discontinued') {
                $class_state = 'discontinued';
                $input       = ' <span class="discontinued">('._('Sold Out').')</span>';

            } else {

                $old_qty = 0;
                if ($order_key) {
                    $sql     = sprintf(
                        "SELECT `Order Quantity` FROM `Order Transaction Fact` WHERE `Order Key`=%d AND `Product ID`=%d", $order_key, $product['Product ID']
                    );
                    $result1 = mysql_query($sql);
                    if ($product1 = mysql_fetch_array($result1)) {
                        $old_qty = $product1['Order Quantity'];
                    }
                }


                if ($old_qty <= 0) {
                    $old_qty = '';
                }

                $input = sprintf(
                    '<input  onKeyUp="order_product_from_list_changed(\'%s\')"  maxlength=6  id="qty_%s_%s"  type="text" value="%s" ovalue="%s" class="list_input" >', $form_id, $form_id, $product['Product ID'], $old_qty, $old_qty
                );
                if ($old_qty != '') {
                    $number_fields_with_ordered_products++;
                }

            }


            $tr_style = '';

            if ($counter == 1) {
                $tr_class = 'top';
            } else {
                $tr_class = '';
            }


            if ($product['Product Web State'] == 'Out of Stock') {
                $tr_class    .= 'out_of_stock_tr';
                $tr_style    = "background-color:rgba(255,209,209,.6);border-top:1px solid #FF9999;;border-bottom:1px solid #FFB2B2;font-size:95%;padding-bottom:0px;";
                $description = $product['description']."<br/><span class='out_of_stock' style='opacity:.6;filter: alpha(opacity = 60);' >$out_of_stock_label2</span>$email_reminder";
            } else {
                $tr_style    = "padding-bottom:5px";
                $description = $product['description'];
            }


            $form .= sprintf(
                '
			<tr id="product_item_%s_%s" class="product_item %s" style="%s" counter="%s">

                           <td class="code" style="vertical-align:top;">%s</td>
                           <td class="price" style="vertical-align:top;">%s</td>
                           <td class="input" style="vertical-align:top;">
                            <input type="hidden" id="product_%s_%s"  value="%d" />

                           %s

                           </td>
                           <td class="description" style="vertical-align:top;">%s</td>
                           </tr>'."\n", $form_id, $product['Product ID'], $tr_class, $tr_style, $product['Product ID'],


                $product['Product Code'], $price, $form_id, $product['Product ID'], $product['Product ID'],

                $input,


                $description


            );


            $counter++;
        }


        $form .= sprintf(
            '
                       <tr><td colspan=1></td><td colspan="3">
                       <img id="list_order_button_submit_%s"    onClick="order_from_list(\''.$form_id.'\',\''.$order_key.'\',\''.$this->id.'\',\''.$this->data['Page Store Section Type'].'\')" style="height:30px;cursor:pointer" src="art/'
            .($number_fields_with_ordered_products ? 'ordered' : 'ordernow').'_%s.png" alt="'._('Order Product').'">
                        <img class="list_feedback" src="art/loading.gif" style="display:none" id="waiting_%s">
                        <img class="list_feedback" src="art/icons/accept.png" style="display:none" id="done_%s">
                        </td></tr>
                       </tbody>
                       ', $form_id,

            $this->site->data['Site Locale'], $form_id, $form_id
        );

        return $form;
    }

    function display_list_logged_out($products) {


        $show_unit = true;
        //if (isset($options['unit'])) {
        //    $show_unit=$options['unit'];
        // }
        $print_header   = true;
        $print_rrp      = false;
        $print_register = true;

        $number_records = count($products);
        $out_of_stock   = _('OoS');
        $discontinued   = _('Sold Out');
        $register       = _('Please').' '.'<a href="login.php?from='.$this->id.'">'._('login').'</a> '._('or').' <a href="registration.php">'._(
                'register'
            ).'</a>';

        $register = '<span style="font-size:120%">'._('For prices, please').' <a  href="login.php?from='.$this->id.'" >'._('login').'</a> '._(
                'or'
            ).' <a  href="registration.php">'._('register').'</a> </span>';


        $form = sprintf(
            '<table class="product_list" style="position:relative;z-index:2;" >'
        );

        if ($print_header) {

            $rrp_label = '';

            if ($print_rrp) {

                if ($number_records == 1) {

                } elseif ($number_records > 2) {

                    $sql = sprintf(
                        "SELECT min(`Product RRP`/`Product Units Per Case`) min, max(`Product RRP`/`Product Units Per Case`) AS max ,avg(`Product RRP`/`Product Units Per Case`)  AS avg FROM `Product Dimension` WHERE `Product Family Key`=%d AND `Product Web State` IN ('For Sale','Out of Stock') ",
                        $family->id
                    );
                    $res = mysql_query($sql);
                    if ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
                        $rrp = $row['min'];


                        $rrp = $this->get_formatted_rrp(
                            array(
                                'Product RRP'            => $rrp,
                                'Product Units Per Case' => 1,
                                'Product Unit Type'      => ''
                            ), array(
                                'prefix'    => false,
                                'show_unit' => $show_unit
                            )
                        );

                        if ($row['rrp_avg'] <= 0) {
                            $rrp_label = '';
                            $print_rrp = false;
                        } elseif ($row['avg'] == $row['min']) {
                            $rrp_label = '<br/>'._('RRP').': '.$rrp;
                        } else {
                            $rrp_label = '<br/>'._('RRP from').' '.$rrp;
                        }


                    } else {
                        return;
                    }

                }

            }


            $form .= '<tr class="list_info" ><td colspan="4"><p>'.$rrp_label.'</p></td><td>';
            if ($print_register and $number_records > 10) {
                $form .= sprintf(
                    '<tr class="last register"><td colspan="4">%s</td></tr>', $register
                );
            }


        }
        $counter = 0;

        foreach ($products as $product) {


            if ($print_rrp) {

                $rrp = $this->get_formatted_rrp(
                    array(
                        'Product RRP'            => $product['Product RRP'],
                        'Product Units Per Case' => $product['Product Units Per Case'],
                        'Product Unit Type'      => $product['Product Unit Type']
                    ), array('show_unit' => $show_unit)
                );

            } else {
                $rrp = '';
            }
            if ($product['Product Web State'] == 'Out of Stock') {
                $class_state = 'out_of_stock';
                $state       = '('.$out_of_stock.')';

            } elseif ($product['Product Web State'] == 'Discontinued') {
                $class_state = 'discontinued';
                $state       = '('.$discontinued.')';

            } else {

                $class_state = '';
                $state       = '';


            }


            if ($counter == 0) {
                $tr_class = 'class="top"';
            } else {
                $tr_class = '';
            }
            $form .= sprintf(
                '<tr %s ><td class="code">%s</td><td class="description">%s   <span class="%s">%s</span></td><td class="rrp">%s</td></tr>', $tr_class, $product['Product Code'], $product['Product Units Per Case'].'x '.$product['Product Special Characteristic'],
                $class_state, $state, $rrp

            );


            $counter++;
        }

        if ($print_register) {
            $form .= sprintf(
                '<tr class="last register"><td colspan="4">%s</td></tr>', $register
            );
        }
        $form .= sprintf('</table>');

        return $form;
    }

    function get_list_inikoo_old($products) {
        $form    = '';
        $counter = 0;


        if (isset($this->order) and $this->order) {
            $order_key = $this->order->id;
        } else {
            $order_key = 0;
        }

        foreach ($products as $product) {

            if ($this->print_rrp) {

                $rrp = $this->get_formatted_rrp(
                    array(
                        'Product RRP'            => $product['Product RRP'],
                        'Product Units Per Case' => $product['Product Units Per Case'],
                        'Product Unit Type'      => $product['Product Unit Type']
                    ), array('show_unit' => $show_unit)
                );

            } else {
                $rrp = '';
            }


            $price = $this->get_formatted_price(
                array(
                    'Product Price'          => $product['Product Price'],
                    'Product Units Per Case' => 1,
                    'Product Unit Type'      => '',
                    'Label'                  => '',
                    'price per unit text'    => ''
                )
            );
            if ($counter == 0) {
                $tr_class = 'class="top"';
            } else {
                $tr_class = '';
            }

            $old_qty = 0;
            if ($order_key) {
                $sql     = sprintf(
                    "SELECT `Order Quantity` FROM `Order Transaction Fact` WHERE `Order Key`=%d AND `Product ID`=%d", $order_key, $product['Product ID']
                );
                $result1 = mysql_query($sql);
                if ($product1 = mysql_fetch_array($result1)) {
                    $old_qty = $product1['Order Quantity'];
                }
            }


            if ($product['Product Web State'] == 'Out of Stock') {


                $order_button = sprintf(
                    '<td></td><td colspan=2 style="padding:0px"><div style="background:#ffdada;color:red;display:table-cell; vertical-align:middle;font-size:90%%;text-align:center;;border:1px solid #ccc;height:18px;width:58px;">%s</div></td>', _('Sold Out')
                );


            } elseif ($product['Product Web State'] == 'Discontinued') {
                //    $class_state='discontinued';
                //  $state=' <span class="discontinued">('._('Sold Out').')</span>';
                $order_button = sprintf('<td colspan=3>%s</td>', _('Sold Out'));
            } else {
                // $class_state='';
                // $state='';

                $order_button = sprintf(
                    '
                                      <td ><span id="loading%d"></span></td>
                                      <td style="padding:0" class="input">
                                       <input  onKeyUp="order_product_from_list_changed(%d)"  style="height:20px" id="qty%s"  type="text" value="%s" ovalue="%s"  >
                                      </td>
                                      <td style="padding:0">
                                      	<button id="list_button%d" onClick="order_product_from_list(%d)"  style="cursor:pointer;visibility:hidden;background:#fff;border:1px solid #ccc;border-left:none;height:22px;padding:0 2px">
                                      	<img id="list_button_img%d" style="pointer:cursor;position:relative;bottom:2px;width:16px;;height:16px" src="art/icons/basket_add.png" />
                                      	</button>
                                      </td>', $product['Product ID'],

                    $product['Product ID'], $product['Product ID'],

                    ($old_qty > 0 ? $old_qty : ''), ($old_qty > 0 ? $old_qty : ''),


                    $product['Product ID'],

                    $product['Product ID'], $product['Product ID']

                );


            }


            $form .= sprintf(
                '<tr %s >
                           <td class="code">%s</td>
                           %s
                           <input type="hidden" id="order_id%d" value="%d">
                           <input type="hidden" id="pid%d" value="%d">
                           <input type="hidden" id="old_qty%d" value="%d">

                           <td class="price">%s</td>



                           <td class="description">%s <span class="rrp">%s</span></td>


                           </tr>'."\n", $tr_class, $product['Product Code'], $order_button, $product['Product ID'], $order_key, $product['Product ID'], $product['Product ID'], $product['Product ID'], $old_qty,

                $price,


                $product['Product Units Per Case'].'x '.$product['Product Special Characteristic'], $rrp

            );


            $counter++;
        }


        return $form;


    }

    function get_list_emals_commerce_old($products) {


        $form_id = "order-form".rand();

        $form    = sprintf(
            '
                      <form action="%s" method="post" name="'.$form_id.'" id="'.$form_id.'" >
                      <input type="hidden" name="userid" value="%s">
                      <input type="hidden" name="nocart">
                        <input type="hidden" name="return" value="%s">
                        <input type="hidden" name="sd" value="ignore">

                      '


            , $this->site->get_checkout_data('url').'/cf/addmulti.cfm', $this->site->get_checkout_data('id'), $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']
        );
        $counter = 1;
        foreach ($products as $product) {


            if ($this->print_rrp) {

                $rrp = $this->get_formatted_rrp(
                    array(
                        'Product RRP'            => $product['Product RRP'],
                        'Product Units Per Case' => $product['Product Units Per Case'],
                        'Product Unit Type'      => $product['Product Unit Type']
                    ), array('show_unit' => $show_unit)
                );

            } else {
                $rrp = '';
            }


            $price = $this->get_formatted_price(
                array(
                    'Product Price'          => $product['Product Price'],
                    'Product Units Per Case' => 1,
                    'Product Unit Type'      => '',
                    'Label'                  => '',
                    'price per unit text'    => ''

                )
            );


            if ($product['Product Web State'] == 'Out of Stock') {


                $sql = sprintf(
                    "SELECT `Email Site Reminder Key` FROM `Email Site Reminder Dimension` WHERE `Trigger Scope`='Back in Stock' AND `Trigger Scope Key`=%d AND `User Key`=%d AND `Email Site Reminder In Process`='Yes' ", $product['Product ID'], $this->user->id

                );
                $res = mysql_query($sql);
                if ($row = mysql_fetch_assoc($res)) {
                    $email_reminder = '<br/><span id="send_reminder_wait_'.$product['Product ID'].'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                            'Processing request'
                        ).'</span><span id="send_reminder_container_'.$product['Product ID'].'"  style="color:#777"><span id="send_reminder_info_'.$product['Product ID'].'" >'._(
                            "We'll notify you via email"
                        ).' <span style="cursor:pointer" id="cancel_send_reminder_'.$row['Email Site Reminder Key'].'"  onClick="cancel_send_reminder('.$row['Email Site Reminder Key'].','.$product['Product ID'].')"  >('._('Cancel').')</span></span></span>';
                } else {
                    $email_reminder = '<br/><span id="send_reminder_wait_'.$product['Product ID'].'"  style="display:none;color:#777"><img style="height:10px;position:relative;bottom:-1px"  src="art/loading.gif"> '._(
                            'Processing request'
                        ).'</span><span id="send_reminder_container_'.$product['Product ID'].'" style="color:#777" ><span id="send_reminder_'.$product['Product ID'].'" style="cursor:pointer;" onClick="send_reminder('.$product['Product ID'].')">'._(
                            'Notify me when back in stock'
                        ).' <img style="position:relative;bottom:-2px" src="art/send_mail.png"/></span></span><span id="send_reminder_msg_'.$product['Product ID'].'"></span></span>';

                }


                $class_state = 'out_of_stock';

                if ($product['Product Next Supplier Shipment'] != '') {
                    $out_of_stock_label  = _('Out of stock').', '._('expected').': '.$product['Next Supplier Shipment'];
                    $out_of_stock_label2 = _('Expected').': '.$product['Next Supplier Shipment'];

                } else {
                    $out_of_stock_label  = _('Out of stock');
                    $out_of_stock_label2 = _('Out of stock');

                }

                $input = ' <span class="out_of_stock" style="font-size:80%" title="'.$out_of_stock_label.'">'._('OoS').'</span>';
                $input = '';


            } elseif ($product['Product Web State'] == 'Discontinued') {
                $class_state = 'discontinued';
                $input       = ' <span class="discontinued">('._('Sold Out').')</span>';

            } else {

                $input = sprintf(
                    '<input name="qty%s"  id="qty%s"  type="text" value=""  >', $counter, $counter
                );


            }

            $tr_style = '';

            if ($counter == 1) {
                $tr_class = 'top';
            } else {
                $tr_class = '';
            }


            if ($product['Product Web State'] == 'Out of Stock') {
                $tr_class    .= 'out_of_stock_tr';
                $tr_style    = "background-color:rgba(255,209,209,.6);border-top:1px solid #FF9999;;border-bottom:1px solid #FFB2B2;font-size:95%;padding-bottom:0px;";
                $description = $product['description']."<br/><span class='out_of_stock' style='opacity:.6;filter: alpha(opacity = 60);' >$out_of_stock_label2</span>$email_reminder";
            } else {
                $tr_style    = "padding-bottom:5px";
                $description = $product['description'];
            }


            $form .= sprintf(
                '<tr class="%s" style="%s">
                           <input type="hidden" name="price%s" value="%s"  >
                           <input type="hidden" name="product%s"  value="%s %s" >
                           <td class="code" style="vertical-align:top;">%s</td>
                           <td class="price" style="vertical-align:top;">%s</td>
                           <td class="input" style="vertical-align:top;">
                           %s
                           </td>
                           <td class="description" style="vertical-align:top;">%s</td>
                           </tr>'."\n", $tr_class, $tr_style,

                $counter, number_format($product['Product Price'], 2, '.', ''), $counter, $product['Product Code'], $this->clean_accents($product['long_description']),

                $product['Product Code'], $price,

                $input,

                $description


            );


            $counter++;
        }


        $form .= sprintf(
            '<tr ><td colspan="4">
                       <input type="hidden" name="xreturn" value="%s">

                       </td></tr></form>
                       <tr><td colspan=1></td><td colspan="3">
                       <img onmouseover="this.src=\'art/ordernow_hover_%s.png\'" onmouseout="this.src=\'art/ordernow_%s.png\'"   onClick="document.forms[\''.$form_id.'\'].submit();" style="height:30px;cursor:pointer" src="art/ordernow_%s.png" alt="'._('Order Product').'">
                        </td></tr>
                       </table>
                       ', $this->data['Page URL'], $this->site->data['Site Locale'], $this->site->data['Site Locale'], $this->site->data['Site Locale']
        );

        return $form;
    }

    function display_top_bar() {

        if ($this->logged) {
            //$ecommerce_basket.ecommerceURL()
            //$ecommerce_checkout
            switch ($this->site->data['Site Checkout Method']) {
                case 'Mals':

                    $basket = '<div style="float:left;"> '._('Total').': '.$this->currency_symbol
                        .'<span id="total"> <img src="art/loading.gif" style="width:14px;position:relative;top:2px"/></span> (<span id="number_items"><img src="art/loading.gif" style="width:14px;position:relative;top:2px"/></span> '._('items')
                        .') <span class="link basket"  id="see_basket"  onClick=\'window.location="'.$this->site->get_checkout_data('url').'/cf/review.cfm?userid='.$this->site->get_checkout_data('id').'&return='.$this->data['Page URL'].'"\' >'._('Basket & Checkout')
                        .'</span>  <img src="art/gear.png" style="visibility:hidden" class="dummy_img" /></div>';

                    $basket = '<div style="float:left;position:relative;top:4px;margin-right:20px"><span>'.$this->customer->get_hello().'</span>  <span class="link" onClick=\'window.location="logout.php"\' id="logout">'._('Log Out')
                        .'</span> <span  class="link" onClick=\'window.location="profile.php"\' >'._(
                            'My Account'
                        ).'</span> </div>';

                    $basket .= '<div  style="float:right;position:relative;top:2px"><span style="cursor:pointer" onClick=\'window.location="'.$this->site->get_checkout_data('url').'/cf/review.cfm?sd=ignore&userid='.$this->site->get_checkout_data('id').'&return='
                        .$this->data['Page URL'].'"\' > '._('Total').': '.$this->currency_symbol
                        .'<span id="total"> <img src="art/loading.gif" style="width:14px;position:relative;top:2px;"/></span> (<span id="number_items"><img src="art/loading.gif" style="width:14px;position:relative;top:2px"/></span> '._('items')
                        .')</span> <img onClick=\'window.location="'.$this->site->get_checkout_data('url').'/cf/review.cfm?sd=ignore&userid='.$this->site->get_checkout_data('id').'&return='.$this->data['Page URL']
                        .'"\' src="art/basket.jpg" style="height:15px;position:relative;top:3px;margin-left:10px;cursor:pointer"/> <span style="color:#ff8000;margin-left:0px" class="link basket"  id="see_basket"  onClick=\'window.location="'
                        .$this->site->get_checkout_data(
                            'url'
                        ).'/cf/review.cfm?sd=ignore&userid='.$this->site->get_checkout_data('id').'&return='.$this->data['Page URL'].'"\' >'._('Basket & Checkout').'</span> </div>';
                    $html   = $basket;

                    break;

                default:


                    $currency_info   = '';
                    $currency_dialog = '';
                    global $valid_currencies;

                    if (count($valid_currencies) > 1) {
                        $currency_info = '
					<div class="currencies" ><span  class="inline_content" style="margin-left:0px;margin-right:10px;"><span>'._('Prices in').' '.$this->set_currency.'</span>
				 <img id="show_currency_dialog" onclick="show_currencies_dialog()"  src="art/dropdown.png">
				 <img id="hide_currency_dialog" style="display:none" onclick="hide_currencies_dialog()"  src="art/dropup.png">


				 </span></div>';

                        $currency_dialog = '<div id="currency_dialog" ><div style="margin:0px auto" class="buttons small left "><br>';
                        foreach (
                            $valid_currencies as $currency_code => $valid_currency
                        ) {
                            $currency_dialog .= '<button class="'.($this->set_currency == $currency_code ? 'selected' : '').'  '.($_SESSION['user_currency'] == $currency_code ? 'recommended' : '').'" onClick="change_currency(\''.$currency_code.'\')"  ><b>'
                                .$currency_code.'</b>, '.$valid_currency['native_name'].'</button>';
                        }
                        $currency_dialog .= '</div></div>';

                    }


                    $basket = '<div style="width:100%;">
				<div class="actions" >
					<span class="hello_customer">'.$this->customer->get_hello().'</span>
					<span class="link" onClick=\'window.location="logout.php"\' id="logout">'._('Log Out').'</span>
					<span class="link" onClick=\'window.location="profile.php"\' >'._('My Account').'</span>
				</div>';

                    $basket .= ' <div class="checkout_info" >
				<span class="basket_info">
				    <img  onClick=\'window.location="basket.php"\' src="art/basket.jpg" />
				     '._('Items total').':
				 	<span onClick=\'window.location="basket.php"\'  id="basket_total">'.money(
                            0, $this->set_currency, $this->site->data['Site Locale']
                        ).'</span>
				 	 (<span id="number_items">0</span> '._('items').') </span>
				 	  <span onClick=\'window.location="basket.php"\'  class="link basket"  id="see_basket"  >'._('Basket').'</span>
				 </div>';


                    $basket .= '<div id="top_bar_back_to_shop" style="float:right;position:relative;top:2px">
				<img  src="art/back_to_shop.jpg" style="height:15px;position:relative;top:3px;margin-left:0px;cursor:pointer"/>
				 <span onClick=\'back_to_shop()\' style="color:#ff8000;margin-left:0px" class="link basket"  id="see_basket"  >'._('Back to shop').'</span>
				</div> ';
                    $basket .= $currency_info;

                    $basket .= $currency_dialog;

                    $basket .= '</div>';

                    $html = $basket;

                    break;
            }


        } else {
            $html =
                '<div style="float:right"> <span class="link" onClick=\'window.location="registration.php"\' id="show_register_dialog">'._('Create Account').'</span> <span class="link"  onClick=\'window.location="login.php?from='.$this->id.'"\' id="show_login_dialog">'
                ._(
                    'Log in'
                ).'</span><img src="art/gear.png" style="visibility:hidden" class="dummy_img" /></div>';
        }


        return $html;


    }

    function display_label() {

        return $this->data['Page Parent Code'];
    }

    function update_list_products_to_delete() {


        if ($this->data['Page Type'] != 'Store') {
            return;
        }


        if ($this->data['Page Store Content Display Type'] == 'Source') {
            $lists = $this->get_list_products_from_source();
        } else {
            $lists = array('default');
        }

        $site = new Site($this->data['Page Site Key']);

        $valid_list_keys = array();
        foreach ($lists as $list_key) {

            $sql = sprintf(
                "SELECT `Page Product List Key` FROM `Page Product List Dimension` WHERE `Page Key`=%d AND `Page Product List Code`=%s  ", $this->id, prepare_mysql($list_key)
            );
            $res = mysql_query($sql);
            if ($row = mysql_fetch_assoc($res)) {
                $valid_list_keys[] = $row['Page Product List Key'];

            } else {
                if ($this->data['Page Store Section Type'] == 'Family') {
                    $sql = sprintf(
                        "INSERT INTO `Page Product List Dimension` (`Page Key`,`Site Key`,`Page Product List Code`,`Page Product List Type`,`Page Product List Parent Key`) VALUES  (%d,%d,%s,%s,%d)", $this->id, $this->data['Page Site Key'], prepare_mysql($list_key),
                        prepare_mysql('FamilyList'), $this->data['Page Parent Key']

                    );
                    mysql_query($sql);
                    //print "$sql\n";
                    $valid_list_keys[] = prepare_mysql(mysql_insert_id());
                }
            }

            if (count($valid_list_keys) > 0) {
                $sql = sprintf(
                    "DELETE FROM `Page Product List Dimension` WHERE `Page Key`=%d AND `Page Product List Key` NOT IN (%s) ", $this->id, join(',', $valid_list_keys)
                );
                mysql_query($sql);
            } else {
                $sql = sprintf(
                    "DELETE FROM `Page Product List Dimension` WHERE `Page Key`=%d", $this->id
                );
                mysql_query($sql);
            }
        }

        $products_from_family = array();
        $number_lists         = 0;
        $number_products      = 0;

        $sql = sprintf(
            "SELECT `Page Product List Code`,`Page Product List Key` FROM `Page Product List Dimension` WHERE `Page Key`=%d  ", $this->id

        );
        $res = mysql_query($sql);
        while ($row = mysql_fetch_assoc($res)) {


            $new_products_on_list = $this->get_products_from_list(
                $row['Page Product List Code']
            );


            $sql  = sprintf(
                "SELECT `Product ID`,`Page Product Key` FROM `Page Product Dimension` WHERE `Parent Key`=%d AND `Parent Type`='List'", $row['Page Product List Key']
            );
            $res2 = mysql_query($sql);
            //print "$sql\n";
            $old_products_on_list = array();
            while ($row2 = mysql_fetch_assoc($res2)) {
                $old_products_on_list[$row2['Product ID']] = $row2['Page Product Key'];
            }

            foreach ($new_products_on_list as $product_pid => $tmp) {


                $page_data        = array(
                    'Page Store Content Display Type'      => 'Template',
                    'Page Store Content Template Filename' => 'product',
                );
                $product_page_key = $site->add_product_page(
                    $product_pid, $page_data
                );


                if (array_key_exists($product_pid, $old_products_on_list)) {

                } else {
                    $product = new Product('id', $product_pid);

                    $sql = sprintf(
                        "INSERT INTO `Page Product Dimension` (`Parent Key`,`Site Key`,`Page Key`,`Product ID`,`Family Key`,`Parent Type`,`State`) VALUES  (%d,%d,%d,%d,%d,'List',%s) ON DUPLICATE KEY UPDATE `Site Key`=%d", $row['Page Product List Key'],
                        $this->data['Page Site Key'], $this->id,

                        $product_pid, $product->data['Product Family Key'], prepare_mysql($this->data['Page State']), $this->data['Page Site Key']

                    );
                    mysql_query($sql);

                    // print "$sql\n";
                    $product->update_pages_numbers();


                }
            }
            //print_r($old_products_on_list);
            foreach ($old_products_on_list as $product_pid => $page_product_key) {

                //print "$product_pid";
                //print_r($new_products_on_list);

                if (!array_key_exists($product_pid, $new_products_on_list)) {
                    $sql = sprintf(
                        "DELETE FROM `Page Product Dimension` WHERE `Page Product Key`=%d", $page_product_key
                    );
                    //print "$sql\n";
                    mysql_query($sql);

                    $product = new Product('id', $product_pid);
                    $product->update_pages_numbers();

                }
            }


            $sql = sprintf(
                "UPDATE `Page Product List Dimension` SET `Page Product List Number Products`=%d WHERE `Page Product List Key`=%d", count($new_products_on_list), $row['Page Product List Key']
            );
            mysql_query($sql);

            $number_products += count($new_products_on_list);
            $number_lists++;
        }


        $this->data['Number Products In Lists'] = $number_products;
        $this->data['Number Lists']             = $number_lists;
        $this->data['Number Products']          = $this->data['Number Buttons'] + $this->data['Number Products In Lists'];

        $sql = sprintf(
            "UPDATE `Page Store Dimension`  SET  `Number Products`=%d ,`Number Lists`=%d,`Number Products In Lists`=%d WHERE `Page Key`=%d",

            $this->data['Number Products'], $this->data['Number Lists'], $this->data['Number Products In Lists'], $this->id
        );
        $res = mysql_query($sql);


    }

    function get_list_products_from_source() {


        $html = $this->data['Page Store Source'];


        $lists = array();

        $regexp = '\{\s*\$page->display_list\s*\((.*)\)\s*\}';
        if (preg_match_all("/$regexp/siU", $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $lists[] = ($match[1] == '' ? 'default' : $match[1]);
            }
        }

        return $lists;
    }

    function get_body_includes() {

        $include = '';
        if ($this->data['Page Type'] != 'Store') {
            return '';
        }

        if ($this->data['Page Use Site Body Include'] == 'Yes') {
            $include .= $this->site->data['Site Body Include'];
        }
        $include .= $this->data['Page Body Include'];

        return $include;
    }

    function get_head_includes() {

        $include = '';
        if ($this->data['Page Type'] != 'Store') {
            return '';
        }

        if ($this->data['Page Use Site Head Include'] == 'Yes') {
            $include .= $this->site->data['Site Head Include'];
        }
        $include .= $this->data['Page Head Include'];

        return $include;
    }

    function update_button_products($source = 'Source') {

        if ($this->data['Page Type'] != 'Store') {
            return;
        }

        include_once 'class.Product.php';


        if ($this->data['Page Store Content Display Type'] == 'Source') {

            $buttons = $this->get_button_products_from_source();
        } else {
            $buttons = $this->get_button_products_from_parent();
        }


        $old_page_buttons_to_delete = array();
        $sql                        = sprintf(
            "SELECT `Page Product Button Key`,`Product ID` FROM  `Page Product Button Dimension`  WHERE `Page Key`=%d", $this->id
        );


        $result   = mysql_query($sql);
        $products = array();
        while ($row2 = mysql_fetch_assoc($result)) {

            $old_page_buttons_to_delete[$row2['Page Product Button Key']] = $row2['Product ID'];
        }

        $number_buttons = 0;
        foreach ($buttons as $product_data) {


            $product = new Product('id', $product_data['Product ID']);
            //print_r($product);
            if ($product->id) {

                $number_buttons++;
                if (!in_array($product->id, $old_page_buttons_to_delete)) {
                    $sql = sprintf(
                        "INSERT INTO `Page Product Button Dimension` (`Site Key`,`Page Key`,`Product ID`) VALUES  (%d,%d,%d) ON DUPLICATE KEY UPDATE `Site Key`=%d, `Page Key`=%d ,`Product ID`=%d  ", $this->data['Page Site Key'], $this->id, $product->id,
                        $this->data['Page Site Key'], $this->id, $product->id
                    );
                    mysql_query($sql);
                    //print "$sql\n";

                    $page_product_key = mysql_insert_id();
                    $sql              = sprintf(
                        "INSERT INTO `Page Product Dimension` (`Page Key`,`Site Key`,`Product ID`,`Family Key`,`Parent Key`,`Parent Type`,`State`) VALUES  (%d,%d,%d,%d,%d,'Button',%s) ON DUPLICATE KEY UPDATE `Site Key`=%d", $this->id, $this->data['Page Site Key'],
                        $product->id, $product->data['Product Family Key'], $page_product_key, prepare_mysql($this->data['Page State']), $this->data['Page Site Key']
                    );
                    mysql_query($sql);

                    $product->update_pages_numbers();


                } else {
                    $key = array_search(
                        $product->id, $old_page_buttons_to_delete
                    );
                    if (false !== $key) {
                        unset($old_page_buttons_to_delete[$key]);
                    }
                }
            }
        }
        //print count($old_page_buttons_to_delete);
        //print_r($old_page_buttons_to_delete);

        foreach ($old_page_buttons_to_delete as $key => $product_pid) {
            $sql = sprintf(
                "DELETE  FROM  `Page Product Button Dimension`  WHERE `Page Product Button Key`=%d", $key
            );
            mysql_query($sql);
            //print "$sql\n";
            $sql = sprintf(
                "DELETE  FROM  `Page Product Dimension`  WHERE `Parent Key`=%d AND `Parent Type`='Button'", $key
            );
            mysql_query($sql);
            $product = new Product('id', $product_pid);
            $product->update_pages_numbers();

        }

        $this->data['Number Buttons'] = $number_buttons;

        $this->data['Number Products'] = $this->data['Number Products In Lists'] + $this->data['Number Buttons'];
        $sql                           = sprintf(
            "UPDATE `Page Store Dimension`  SET `Number Buttons`=%d , `Number Products`=%d WHERE `Page Key`=%d", $this->data['Number Buttons'], $this->data['Number Products'],

            $this->id
        );


    }

    function get_button_products_from_source() {
        $html = $this->data['Page Store Source'];


        $html = preg_replace('/display_buttom/', 'display_button', $html);

        $buttons = array();

        $regexp = '\{\s*\$page->display_button\s*\((.*)\)\s*\}';
        if (preg_match_all("/$regexp/siU", $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {

                $id      = ($match[1] == '' ? 'default' : $match[1]);
                $id      = preg_replace('/^\"/', '', $id);
                $id      = preg_replace('/^\'/', '', $id);
                $id      = preg_replace('/\"$/', '', $id);
                $id      = preg_replace('/\'$/', '', $id);
                $product = new Product(
                    'store_code', $this->data['Page Store Key'], $id
                );
                if ($product->id) {

                    $buttons[] = array(
                        'Product Currency'               => $product->data['Product Currency'],
                        'Product Name'                   => $product->data['Product Name'],
                        'Product ID'                     => $product->data['Product ID'],
                        'Product Code'                   => $product->data['Product Code'],
                        'Product Price'                  => $product->data['Product Price'],
                        'Product RRP'                    => $product->data['Product RRP'],
                        'Product Units Per Case'         => $product->data['Product Units Per Case'],
                        'Product Unit Type'              => $product->data['Product Unit Type'],
                        'Product Web State'              => $product->data['Product Web State'],
                        'Product Special Characteristic' => $product->data['Product Special Characteristic']
                    );


                }


            }
        }


        return $buttons;
    }

    function get_button_products_from_parent() {
        $sql = sprintf(
            "SELECT `Product Currency`,`Product Name`,`Product ID`,`Product Code`,`Product Price`,`Product RRP`,`Product Units Per Case`,`Product Unit Type`,`Product Web State`,`Product Special Characteristic` FROM `Product Dimension` WHERE `Product Family Key`=%d AND `Product Web State`!='Offline'",
            $this->data['Page Parent Key']
        );

        $result   = mysql_query($sql);
        $products = array();
        while ($row2 = mysql_fetch_array($result, MYSQL_ASSOC)) {

            $products[] = $row2;
        }

        return $products;
    }

    function display_search() {
        return $this->site->display_search();
    }

    function display_menu() {
        return $this->site->display_menu();
    }

    function update_preview_snapshot($dirname = false) {


        if ($this->data['Page Type'] != 'Store') {
            return;
        }

        if (!$dirname) {
            $dirname = dirname($_SERVER['PHP_SELF']);
        }

        $r   = join('', unpack('v*', fread(fopen('/dev/urandom', 'r'), 25)));
        $pwd = uniqid('', true).sha1(mt_rand()).'.'.$r;

        $sql = sprintf(
            "INSERT INTO `MasterKey Internal Dimension` (`User Key`,`Key`,`Valid Until`,`IP`)VALUES (%s,%s,%s,%s) ", 1, prepare_mysql($pwd), prepare_mysql(gmdate("Y-m-d H:i:s", strtotime("now +5 minute"))), prepare_mysql(ip(), false)
        );

        // print $sql;
        mysql_query($sql);


        $old_image_key = $this->data['Page Preview Snapshot Image Key'];

        //   $new_image_key=$old_image_key;
        //      $image=new Image($image_key);


        $height = $this->data['Page Header Height'] + $this->data['Page Content Height'] + $this->data['Page Footer Height'] + 10;
        //ar_edit_sites.php?tipo=update_page_snapshot&id=1951;

        $url = "http://localhost".(!isset($_SERVER['SERVER_PORT']) or $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT']).$dirname."/authorization.php?url=".urlencode(
                "page_preview.php?header=0&id=".$this->id
            ).'\&mk='.$pwd;
        //print $url;
        //exit;
        ob_start();
        system("uname");


        $_system = ob_get_clean();
        if (preg_match('/darwin/i', $_system)) {
            //$url='http://www.yahoo.com';
            $command = "mantenence/scripts/webkit2png_mac.py  -C -o server_files/tmp/pp_image".$this->id."  --clipheight=".($height * 0.5)."  --clipwidth=512  -s 0.5  ".$url;

            //       $command="mantenence/scripts/webkit2png  -C -o server_files/tmp/ph_image".$this->id."  --clipheight=80  --clipwidth=488  -s 0.5   http://localhost/dw/public_header_preview.php?id=".$this->id;

        } elseif (preg_match('/linux/i', $_system)) {
            $command = 'xvfb-run --server-args="-screen 0, 1280x1024x24" python mantenence/scripts/webkit2png_linux.py --style=windows  --log=server_files/tmp/webkit2png_linux.log -o server_files/tmp/pp_image'.$this->id.'-clipped.png    '.$url;


            //  $command='xvfb-run --server-args="-screen 0, 1280x1024x24" python mantenence/scripts/webkit2png_linux.py --log=server_files/tmp/webkit2png_linux.log -o server_files/tmp/pp_image'.$this->id.'-clipped.png --scale  512 '.(ceil($height*0.5)).'    '.$url;
        } else {
            return;

        }


        ob_start();
        @system($command, $retval);
        ob_get_clean();

        //print $command;
        //  print "$url\n\n";

        $this->snapshots_taken++;


        $image_data = array(
            'file'        => "server_files/tmp/pp_image".$this->id."-clipped.png",
            'source_path' => '',
            'name'        => 'page_preview'.$this->id
        );

        //   print_r($image_data);
        $image = new Image('find', $image_data, 'create');


        if (file_exists("server_files/tmp/pp_image".$this->id."-clipped.png")) {
            unlink("server_files/tmp/pp_image".$this->id."-clipped.png");
        }
        $new_image_key = $image->id;
        if (!$new_image_key) {
            print $image->msg;
            exit("xx \n");

        }


        if ($new_image_key != $old_image_key) {
            $this->data['Page Preview Snapshot Image Key'] = $new_image_key;
            $sql                                           = sprintf(
                "DELETE FROM `Image Bridge` WHERE `Subject Type`=%s AND `Subject Key`=%d AND `Image Key`=%d ", prepare_mysql('Page Preview'), $this->id, $image->id
            );
            mysql_query($sql);
            //print $sql;
            $old_image = new Image($old_image_key);
            $old_image->delete();


            $sql = sprintf(
                "INSERT INTO `Image Bridge` VALUES (%s,%d,%d,'Yes','')", prepare_mysql('Page Preview'), $this->id, $image->id

            );
            mysql_query($sql);

            //  $image->update_other_size_data();


            $sql = sprintf(
                "UPDATE `Page Store Dimension` SET `Page Preview Snapshot Image Key`=%d,`Page Preview Snapshot Last Update`=NOW()  WHERE `Page Key`=%d", $this->data['Page Preview Snapshot Image Key'], $this->id

            );
            mysql_query($sql);


            $this->updated   = true;
            $this->new_value = $this->data['Page Preview Snapshot Image Key'];

        } else {


            $sql = sprintf(
                "UPDATE `Page Store Dimension` SET `Page Preview Snapshot Last Update`=NOW()  WHERE `Page Key`=%d", $this->id
            );
            mysql_query($sql);

        }


        //  usleep(250000);
        $this->get_data('id', $this->id);
        $new_height = $this->data['Page Header Height'] + $this->data['Page Content Height'] + $this->data['Page Footer Height'] + 10;

        if ($new_height != $height) {
            $this->update_preview_snapshot();
        }

    }

    function get_snapshot_date() {
        return strftime(
            "%a %e %b %Y %H:%M %Z", strtotime($this->data['Page Snapshot Last Update'].' UTC')
        );
    }

    function get_preview_snapshot_date() {

        if ($this->data['Page Preview Snapshot Last Update'] != '') {
            return strftime(
                "%a %e %b %Y %H:%M %Z", strtotime(
                                          $this->data['Page Preview Snapshot Last Update'].' UTC'
                                      )
            );
        }
    }

    function get_preview_snapshot_src() {

        return sprintf(
            "image.php?id=%d", $this->data['Page Preview Snapshot Image Key']
        );
    }

    function get_preview_snapshot_image_key() {
        return $this->data['Page Preview Snapshot Image Key'];
    }

    function add_found_in_link($parent_key) {

        if ($this->id == $parent_key) {
            $this->error = true;
            $this->msg   = 'same page key';

            return;
        }

        $sql = sprintf(
            "INSERT INTO `Page Store Found In Bridge` VALUES (%d,%d)  ", $this->id, $parent_key
        );

        mysql_query($sql);
        $this->update_number_found_in();
        $this->updated = true;
    }

    function update_number_found_in() {
        $number_found_in_links = 0;
        $sql                   = sprintf(
            "SELECT count(*) AS num FROM  `Page Store Found In Bridge` WHERE `Page Store Key`=%d", $this->id
        );
        $res                   = mysql_query($sql);
        if ($row = mysql_fetch_assoc($res)) {
            $number_found_in_links = $row['num'];
        }
        $this->data['Number Found In Links'] = $number_found_in_links;
        $sql                                 = sprintf(
            "UPDATE `Page Store Dimension` SET `Number Found In Links`=%d  WHERE `Page Key`=%d", $number_found_in_links, $this->id

        );
        mysql_query($sql);


    }

    function remove_found_in_link($parent_key) {


        $sql = sprintf(
            "DELETE FROM  `Page Store Found In Bridge` WHERE `Page Store Key`=%d AND `Page Store Found In Key`=%d   ", $this->id, $parent_key
        );

        mysql_query($sql);
        $this->update_number_found_in();
        $this->updated = true;
    }

    function get_page_height() {
        return $this->data['Page Header Height'] + $this->data['Page Content Height'] + $this->data['Page Footer Height'] + 22;
    }

    function add_redirect($source_url = '') {

        if ($source_url == '') {
            $this->error = true;
            $this->msg   = _('No URL provied');

            return;
        }

        $site = new Site($this->data['Page Site Key']);

        $url_array = explode("/", _trim($source_url));
        //print_r($url_array);

        if (count($url_array) < 2) {
            $this->error = true;
            $this->msg   = _('Errorr, the URL should a site subdirectory');

            return;
        }

        $host = array_shift($url_array);
        $file = array_pop($url_array);
        $path = join('/', $url_array);

        if ($file == '') {
            $file = 'index.html';
        }
        if ($host == '') {

            $host = $site->data['Site URL'];
        }


        $_source = $host.'/'.$path.'/'.$file;

        $_source_bis = strtolower(preg_replace('/^www\./', '', $_source));

        $target = strtolower($this->data['Page URL']);

        //print "$source_url -> $target";
        if ($target == strtolower($_source_bis) or $target == $_source

        ) {
            $this->error = true;
            $this->msg   = _('Same URL as the redirect');

            return;
        }


        //print $_source." --> ".$site->data['Site FTP Server']."\n";

        if (strtolower($site->data['Site FTP Server']) == $host) {
            $ftp_pass = 'Yes';
        } else {
            $ftp_pass = 'No';
        }

        $sql = sprintf(
            "INSERT INTO `Page Redirection Dimension` (`Source Host`,`Source Path`,`Source File`, `Page Target URL`,`Page Target Key`, `Can Upload`) VALUES
		(%s,%s,%s, %s,%d, %s)", prepare_mysql($host), prepare_mysql($path, false), prepare_mysql($file), prepare_mysql($this->data['Page URL']), $this->id, prepare_mysql($ftp_pass)
        );

        mysql_query($sql);
        //print "$sql\n";
        $redirection_key = mysql_insert_id();

        return $redirection_key;

    }

    function get_all_redirects_data($smarty = false) {

        $data   = array();
        $sql    = sprintf(
            "SELECT * FROM `Page Redirection Dimension` WHERE `Page Target Key`=%d", $this->id
        );
        $result = mysql_query($sql);

        while ($row = mysql_fetch_assoc($result)) {
            if ($smarty) {
                $_row = array();
                foreach ($row as $key => $value) {
                    $_row[str_replace(' ', '', $key)] = $value;
                }
                $_row['Source'] = $_row['SourceHost'].'/'.($_row['SourcePath'] ? $_row['SourcePath'].'/' : '').$_row['SourceFile'];
                $data[]         = $_row;
            } else {
                $row['Source'] = $row['Source Host'].'/'.($row['Source Path'] ? $row['Source Path'].'/' : '').$row['Source File'];
                $data[]        = $row;
            }
        }

        return $data;
    }

    function get_redirect_data($redirect_key, $smarty = false) {

        $data   = false;
        $sql    = sprintf(
            "SELECT * FROM `Page Redirection Dimension` WHERE `Page Target Key`=%d AND `Page Redirection Key`=%d", $this->id, $redirect_key
        );
        $result = mysql_query($sql);

        if ($row = mysql_fetch_assoc($result)) {
            if ($smarty) {
                $_row = array();
                foreach ($row as $key => $value) {
                    $_row[str_replace(' ', '', $key)] = $value;
                }
                $_row['Source'] = $_row['SourceHost'].'/'.($_row['SourcePath'] ? $_row['SourcePath'].'/' : '').$_row['SourceFile'];
                $data           = $_row;
            } else {
                $row['Source'] = $row['Source Host'].'/'.($row['Source Path'] ? $row['Source Path'].'/' : '').$row['Source File'];
                $data          = $row;
            }
        }

        return $data;
    }

    function display_vertical_menu() {

    }

    function update_up_today_requests() {
        $this->update_requests('Today');
        $this->update_requests('Week To Day');
        $this->update_requests('Month To Day');
        $this->update_requests('Year To Day');
    }


    //======= new methods

    function update_requests($interval) {

        if ($this->data['Page Type'] != 'Store') {
            return;
        }


        list(
            $db_interval, $from_date, $to_date, $from_date_1yb, $to_1yb
            ) = calculate_interval_dates($this->db, $interval);

        $sql = sprintf(
            "SELECT count(*) AS num_requests ,count(DISTINCT `Visitor Session Key`) num_sessions ,count(DISTINCT `Visitor Key`) AS num_visitors   FROM  `User Request Dimension`   WHERE `Page Key`=%d  %s", $this->id,
            ($from_date ? ' and `Date`>='.prepare_mysql($from_date) : '')


        );
        print "$sql\n";

        $res = mysql_query($sql);
        if ($row = mysql_fetch_assoc($res)) {
            $this->data['Page Store '.$db_interval.' Acc Requests'] = $row['num_requests'];
            $this->data['Page Store '.$db_interval.' Acc Sessions'] = $row['num_sessions'];
            $this->data['Page Store '.$db_interval.' Acc Visitors'] = $row['num_visitors'];
        } else {
            $this->data['Page Store '.$db_interval.' Acc Requests'] = 0;
            $this->data['Page Store '.$db_interval.' Acc Sessions'] = 0;
            $this->data['Page Store '.$db_interval.' Acc Visitors'] = 0;

        }

        $sql = sprintf(
            "SELECT count(*) AS num_requests ,count(DISTINCT `Visitor Session Key`) num_sessions ,count(DISTINCT `User Key`) AS num_users   FROM  `User Request Dimension`  WHERE  `Is User`='Yes' AND `Page Key`=%d  %s", $this->id,
            ($from_date ? ' and `Date`>='.prepare_mysql($from_date) : '')


        );
        $res = mysql_query($sql);
        //print "$sql\n\n\n\n";
        if ($row = mysql_fetch_assoc($res)) {
            $this->data['Page Store '.$db_interval.' Acc Users Requests'] = $row['num_requests'];
            $this->data['Page Store '.$db_interval.' Acc Users Sessions'] = $row['num_sessions'];
            $this->data['Page Store '.$db_interval.' Acc Users']          = $row['num_users'];
        } else {
            $this->data['Page Store '.$db_interval.' Acc Users Requests'] = 0;
            $this->data['Page Store '.$db_interval.' Acc Users Sessions'] = 0;
            $this->data['Page Store '.$db_interval.' Acc Users']          = 0;
        }

        $sql = sprintf(
            'UPDATE `Page Store Data Dimension` SET `Page Store '.$db_interval.' Acc Requests`=%d,
	`Page Store '.$db_interval.' Acc Sessions`=%d,
	`Page Store '.$db_interval.' Acc Visitors`=%d,
	`Page Store '.$db_interval.' Acc Users Requests`=%d,
	`Page Store '.$db_interval.' Acc Users Sessions`=%d,
	`Page Store '.$db_interval.' Acc Users`=%d
	WHERE `Page Key`=%d', $this->data['Page Store '.$db_interval.' Acc Requests'], $this->data['Page Store '.$db_interval.' Acc Sessions'], $this->data['Page Store '.$db_interval.' Acc Visitors'], $this->data['Page Store '.$db_interval.' Acc Users Requests'],
            $this->data['Page Store '.$db_interval.' Acc Users Sessions'], $this->data['Page Store '.$db_interval.' Acc Users'],

            $this->id
        );
        mysql_query($sql);
        //print "$sql\n";
    }

    function update_last_period_requests() {

        $this->update_requests('Yesterday');
        $this->update_requests('Last Week');
        $this->update_requests('Last Month');
    }

    function update_interval_requests() {
        $this->update_requests('Total');
        $this->update_requests('3 Year');
        $this->update_requests('1 Year');
        $this->update_requests('6 Month');
        $this->update_requests('1 Quarter');
        $this->update_requests('1 Month');
        $this->update_requests('10 Day');
        $this->update_requests('1 Week');
        $this->update_requests('1 Day');
        $this->update_requests('1 Hour');
    }

    function get_all_products() {
        $sql = sprintf(
            "SELECT pd.`Product ID`, `Product Code` FROM `Page Product Button Dimension` ppd LEFT JOIN `Product Dimension` pd ON (ppd.`Product ID` = pd.`Product ID`) WHERE `Page Key`=%d", $this->id
        );
        //print $sql;
        $result1  = mysql_query($sql);
        $products = array();
        while ($row1 = mysql_fetch_assoc($result1)) {
            $products[] = array('code' => $row1['Product Code']);

        }

        return $products;
    }

    function display_product_image($tag) {

        $html = '';
        include_once 'class.Product.php';
        $product = new Product(
            'code_store', $tag, $this->data['Page Store Key']
        );
        //print_r($product);
        if ($product->id) {
            $html       = $this->display_button_logged_out($product);
            $small_url  = 'public_image.php?id='.$product->data["Product Main Image Key"].'&size=small';
            $normal_url = 'public_image.php?id='.$product->data["Product Main Image Key"];
            $code       = $product->data['Product Code'];
            $html       = '<ul class="gallery clearfix"><li>
			<a  style="border:none;text-decoration:none" href="'.$normal_url.'" rel="prettyPhoto" >
			<img style="float:left;border:0px solid#ccc;padding:2px;margin:2px;cursor:pointer;width:150px" src="'.$small_url.'" alt="'.$code.'" />
			</a></li></ul>';

        }

        return $html;
    }

    function get_site_key() {

        if ($this->type == 'Store') {
            return $this->data['Page Site Key'];
        } else {
            return 0;
        }
    }

    function get_formatted_state() {

        switch ($this->data['Page State']) {
            case 'Offline':
                return _('Offline');
                break;
            case 'Online':
                return _('Online');
                break;

        }

    }

    function get_primary_content() {
        $content = '';


        return $content;

    }

    function get_departments_data() {
        $departments = array();

        $sql = sprintf(
            "SELECT `Page Key`,`Product Department Key`,`Product Department Code`,`Product Department Main Image`,`Product Department Name` FROM `Product Department Dimension` P LEFT JOIN `Page Store Dimension` PSD ON (`Page Parent Key`=`Product Department Key` AND `Page Store Section Type`='Department') WHERE `Product Department Store Key`=%d AND  `Product Department Sales Type`='Public Sale' ",
            $this->data['Page Store Key']
        );

        $counter = 0;
        $res     = mysql_query($sql);
        while ($row = mysql_fetch_assoc($res)) {


            $department_data = array(
                'code'    => $row['Product Department Code'],
                'name'    => $row['Product Department Name'],
                'id'      => $row['Product Department Key'],
                'img'     => $row['Product Department Main Image'],
                'page_id' => $row['Page Key'],
            );

            if ($counter == 0) {
                $department_data['first'] = true;
            } else {
                $department_data['first'] = false;
            }

            $department_data['col'] = fmod($counter, 4) + 1;
            $counter++;
            $departments[] = $department_data;
        }


        return $departments;

    }

    function get_families_data() {
        $families = array();

        $sql = sprintf(
            "SELECT `Page Key`,`Product Family Key`,`Product Family Code`,`Product Family Main Image`,`Product Family Name` FROM `Product Family Dimension` P LEFT JOIN `Page Store Dimension` PSD ON (`Page Parent Key`=`Product Family Key` AND `Page Store Section Type`='Family')  WHERE `Product Family Main Department Key`=%d AND  `Product Family Sales Type`='Public Sale' ",
            $this->data['Page Parent Key']
        );

        $counter = 0;
        $res     = mysql_query($sql);
        while ($row = mysql_fetch_assoc($res)) {

            $family_data = array(
                'code'    => $row['Product Family Code'],
                'name'    => $row['Product Family Name'],
                'id'      => $row['Product Family Key'],
                'img'     => $row['Product Family Main Image'],
                'page_id' => $row['Page Key'],
            );

            if ($counter == 0) {
                $family_data['first'] = true;
            } else {
                $family_data['first'] = false;
            }

            $family_data['col'] = fmod($counter, 4) + 1;
            $counter++;
            $families[] = $family_data;
        }

        return $families;

    }

    function get_product_data() {
        $product_data = array();

        $sql = sprintf(
            "SELECT `Product ID` FROM `Product Dimension` P  WHERE `Product ID`=%d ", $this->data['Page Parent Key']
        );

        $counter = 0;
        $res     = mysql_query($sql);
        if ($row = mysql_fetch_assoc($res)) {

            $product = new Product('id', $row['Product ID']);


            $quantity = $this->get_button_ordered_quantity($product);


            if ($this->logged) {
                $button      = $this->display_button_inikoo($product);
                $button_only = $this->display_button_only_inikoo($product);
                $price       = $this->get_button_price($product);
            } else {
                $button      = '';
                $button_only = '';
                $price       = '<div class="product_log_out_price">'._(
                        'For prices, please'
                    ).' <a  style="font-weight:800" href="login.php?from='.$this->id.'" >'._('login').'</a> '._('or').' <a   href="registration.php">'._('register').'</a> </div>';

            }

            $images       = array();
            $product_data = array(
                'code'            => $product->data['Product Code'],
                'name'            => $product->data['Product Name'],
                'id'              => $product->data['Product ID'],
                'img'             => $product->data['Product Main Image'],
                'normal_img'      => sprintf(
                    "image.php?id=%d", $product->data['Product Main Image Key']
                ),
                'images'          => $images,
                'quantity'        => $quantity,
                'price'           => $price,
                'rrp'             => $this->get_button_rrp($product),
                'button'          => $button,
                'button_only'     => $button_only,
                'description'     => $product->data['Product Description'],
                'unit_weight'     => $product->get('Unit Weight'),
                'package_weight'  => $product->get('Package Weight'),
                'unit_dimensions' => $product->get(
                    'Product Unit XHTML Dimensions'
                ),
                'ingredients'     => strip_tags(
                    $product->get('Product Unit XHTML Materials')
                ),
                'units'           => $product->get('Product Units Per Case'),
                'origin'          => $product->get('Origin Country'),

                'object' => $product


            );


        }


        return $product_data;

    }

    function display_button_only_inikoo($product) {

        $quantity = $this->get_button_ordered_quantity($product);

        $message = $this->get_button_text($product, $quantity);


        $form = sprintf(
            '<div  class="ind_form_button_only">%s</div><div style="clear:both"></div>',


            $message
        );


        return $form;
    }

    function get_products_data() {
        $products = array();
        $sql      = sprintf(
            "SELECT PPD.`Product ID`,PSD.`Page Key` FROM `Page Product Dimension` PPD LEFT JOIN   `Page Store Dimension` PSD ON (PSD.`Page Parent Key`=PPD.`Product ID` AND `Page Store Section Type`='Product')      WHERE  PPD.`Page Key`=%d  ", $this->id
        );


        $counter = 0;
        $res     = mysql_query($sql);
        while ($row = mysql_fetch_assoc($res)) {

            $product = new Product('id', $row['Product ID']);

            if ($this->logged) {
                $button = $this->display_button_inikoo($product);
            } else {
                $button = $this->display_button_logged_out($product);
            }

            $product_data = array(
                'code'         => $product->data['Product Code'],
                'name'         => $product->data['Product Name'],
                'id'           => $product->data['Product ID'],
                'price'        => $product->data['Product Price'],
                'special_char' => $product->data['Product Special Characteristic'],
                'img'          => $product->data['Product Main Image'],
                'button'       => $button,
                'page_id'      => $row['Page Key'],
            );


            if ($counter == 0) {
                $product_data['first'] = true;
            } else {
                $product_data['first'] = false;
            }

            $product_data['col'] = fmod($counter, 4) + 1;
            $counter++;
            $products[] = $product_data;
        }

        return $products;

    }

    function get_offer_badges() {

        $badges = array();

        switch ($this->data['Page Store Section']) {
            case 'Family Catalogue':
                $family = new Family($this->data['Page Parent Key']);
                $deals  = $family->get_deals_data();

                break;

            case 'Product Description':
                $product = new Product('id', $this->data['Page Parent Key']);
                $deals   = $product->get_deals_data();
                break;

            default:
                $deals = array();
        }

        foreach ($deals as $deal) {
            if ($deal['Status'] == 'Active' and !preg_match(
                    '/Voucher/', $deal['Terms Type']
                )) {
                $badges[] = sprintf(
                    '<div class="offer"><div class="name">%s</div><div class="allowances">%s</div> <div class="terms">%s</div></div>', $deal['Name'], $deal['Allowance Label'], $deal['Terms Label']

                );
            }
        }

        return $badges;


    }

    function update_items_order($item_key, $target_key, $target_section_key) {


        $content_data = $this->get('Content Data');

        $updated_metadata = array('section_keys' => array());


        switch ($this->scope->get_object_name()) {


            case 'Category':
                include_once('class.Category.php');
                $category = new Category($this->scope->id);
                //print'x'.$category->get('Category Subject').'x';


                if ($category->get('Category Subject') == 'Category') {

                    $item_found   = false;
                    $target_found = false;

                    $sql = sprintf(
                        'SELECT `Category Webpage Index Key`,`Category Webpage Index Section Key`,`Category Webpage Index Stack` FROM  `Category Webpage Index` WHERE `Category Webpage Index Webpage Key`=%d AND `Category Webpage Index Category Key`=%d ', $this->id,
                        $item_key


                    );

                    if ($result = $this->db->query($sql)) {
                        if ($row = $result->fetch()) {

                            $item_section_key = $row['Category Webpage Index Section Key'];
                            $item_found       = true;
                            $item_stack       = $row['Category Webpage Index Stack'];
                            $item_index_key   = $row['Category Webpage Index Key'];
                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        print "$sql\n";
                        exit;
                    }


                    if (!$item_found) {
                        $this->msg   = 'Item not found in website';
                        $this->error = true;

                        return;

                    }


                    if ($target_key) {


                        $sql = sprintf(
                            'SELECT `Category Webpage Index Section Key`,`Category Webpage Index Stack` FROM  `Category Webpage Index` WHERE `Category Webpage Index Webpage Key`=%d AND `Category Webpage Index Category Key`=%d ', $this->id, $target_key


                        );

                        if ($result = $this->db->query($sql)) {
                            if ($row = $result->fetch()) {
                                $target_section_key = $row['Category Webpage Index Section Key'];
                                $target_found       = true;
                                $target_stack       = $row['Category Webpage Index Stack'];
                            }
                        } else {
                            print_r($error_info = $this->db->errorInfo());
                            print "$sql\n";
                            exit;
                        }


                        if (!$target_found) {
                            $this->msg   = 'Target not found in website';
                            $this->error = true;

                            return;

                        }

                        if ($item_section_key == $target_section_key) {


                            $updated_metadata['section_keys'][] = $item_section_key;

                            $subjects = array();

                            $sql = sprintf(
                                "SELECT `Category Webpage Index Stack`,`Category Webpage Index Key`,`Category Webpage Index Category Key` AS subject_key FROM `Category Webpage Index`    WHERE  `Category Webpage Index Webpage Key`=%d ", $this->id
                            );


                            if ($result = $this->db->query($sql)) {
                                foreach ($result as $row) {


                                    if ($row['subject_key'] == $item_key) {

                                        $row['Category Webpage Index Stack'] = (string)($target_stack < $item_stack ? $target_stack - 0.5 : $target_stack + .5);


                                    }


                                    //print_r($row);

                                    $subjects[$row['Category Webpage Index Stack']] = $row['Category Webpage Index Key'];
                                }
                            } else {
                                print_r($error_info = $this->db->errorInfo());
                                print "$sql\n";
                                exit;
                            }


                            ksort($subjects);
                            //print_r($subjects);

                            $stack_index = 0;
                            foreach ($subjects as $tmp => $category_webpage_stack_key) {
                                $stack_index++;

                                $sql = sprintf(
                                    'UPDATE `Category Webpage Index` SET `Category Webpage Index Stack`=%d WHERE `Category Webpage Index Key`=%d ', $stack_index, $category_webpage_stack_key
                                );

                                //print "$sql\n";

                                $this->db->exec($sql);

                            }


                        } else {


                            $updated_metadata['section_keys'][] = $item_section_key;
                            $updated_metadata['section_keys'][] = $target_section_key;


                            $sql = sprintf(
                                'UPDATE `Category Webpage Index` SET `Category Webpage Index Section Key`=%d , `Category Webpage Index Stack`=%d WHERE `Category Webpage Index Key`=%d', $target_section_key, 0, $item_index_key


                            );
                            $this->db->exec($sql);


                            $subjects = array();
                            $sql      = sprintf(
                                "SELECT `Category Webpage Index Stack`,`Category Webpage Index Key`,`Category Webpage Index Category Key` AS subject_key FROM `Category Webpage Index`    WHERE  `Category Webpage Index Webpage Key`=%d AND  `Category Webpage Index Section Key`=%d ORDER BY `Category Webpage Index Stack` ",
                                $this->id, $target_section_key

                            );
                            if ($result = $this->db->query($sql)) {
                                foreach ($result as $row) {


                                    // print "aaaa_> $item_index_key\n";
                                    // print_r($row);

                                    if ($item_index_key == $row['Category Webpage Index Key']) {
                                        $tmp = (string)$target_stack - .5;

                                        //  print "x  $tmp x\n";

                                        $row['Category Webpage Index Stack'] = (string)($target_stack - .5);
                                    }
                                    $subjects[$row['Category Webpage Index Stack']] = $row['Category Webpage Index Key'];
                                }
                            }
                            // print_r($subjects);
                            ksort($subjects);

                            //  print_r($subjects);

                            $stack_index = 0;
                            foreach ($subjects as $tmp => $category_webpage_stack_key) {
                                $stack_index++;
                                $sql = sprintf(
                                    'UPDATE `Category Webpage Index` SET `Category Webpage Index Stack`=%d WHERE `Category Webpage Index Key`=%d ', $stack_index, $category_webpage_stack_key
                                );
                                $this->db->exec($sql);

                            }


                            $subjects = array();
                            $sql      = sprintf(
                                "SELECT `Category Webpage Index Stack`,`Category Webpage Index Key`,`Category Webpage Index Category Key` AS subject_key FROM `Category Webpage Index`    WHERE  `Category Webpage Index Webpage Key`=%d AND  `Category Webpage Index Section Key`=%d ORDER BY `Category Webpage Index Stack` ",
                                $this->id, $item_section_key

                            );
                            if ($result = $this->db->query($sql)) {
                                foreach ($result as $row) {
                                    $subjects[$row['Category Webpage Index Stack']] = $row['Category Webpage Index Key'];
                                }
                            }

                            $stack_index = 0;
                            foreach ($subjects as $tmp => $category_webpage_stack_key) {
                                $stack_index++;
                                $sql = sprintf(
                                    'UPDATE `Category Webpage Index` SET `Category Webpage Index Stack`=%d WHERE `Category Webpage Index Key`=%d ', $stack_index, $category_webpage_stack_key
                                );
                                $this->db->exec($sql);

                            }


                        }


                    } else {
                        // move last square

                        $sql = sprintf(
                            'SELECT max(`Category Webpage Index Stack`) AS stack FROM  `Category Webpage Index` WHERE `Category Webpage Index Webpage Key`=%d AND `Category Webpage Index Section Key`=%d ', $this->id, $target_section_key


                        );


                        //  print $sql;

                        if ($result = $this->db->query($sql)) {
                            if ($row = $result->fetch()) {
                                $stack = $row['stack'];
                                if ($target_section_key == $item_section_key) {

                                    $updated_metadata['section_keys'][] = $item_section_key;

                                    $subjects = array();
                                    $sql      = sprintf(
                                        "SELECT `Category Webpage Index Stack`,`Category Webpage Index Key`,`Category Webpage Index Category Key` AS subject_key FROM `Category Webpage Index`    WHERE  `Category Webpage Index Webpage Key`=%d ", $this->id
                                    );
                                    if ($result = $this->db->query($sql)) {
                                        foreach ($result as $row) {
                                            if ($row['subject_key'] == $item_key) {
                                                $row['Category Webpage Index Stack'] = $stack + 1;
                                            }
                                            $subjects[$row['Category Webpage Index Stack']] = $row['Category Webpage Index Key'];
                                        }
                                    }

                                    ksort($subjects);
                                    $stack_index = 0;
                                    foreach ($subjects as $tmp => $category_webpage_stack_key) {
                                        $stack_index++;
                                        $sql = sprintf(
                                            'UPDATE `Category Webpage Index` SET `Category Webpage Index Stack`=%d WHERE `Category Webpage Index Key`=%d ', $stack_index, $category_webpage_stack_key
                                        );
                                        $this->db->exec($sql);

                                    }


                                } else {
                                    $updated_metadata['section_keys'][] = $item_section_key;
                                    $updated_metadata['section_keys'][] = $target_section_key;

                                    $sql = sprintf(
                                        'UPDATE `Category Webpage Index` SET `Category Webpage Index Section Key`=%d , `Category Webpage Index Stack`=%d WHERE `Category Webpage Index Key`=%d', $target_section_key, $stack + 1, $item_index_key


                                    );
                                    $this->db->exec($sql);


                                    $subjects = array();
                                    $sql      = sprintf(
                                        "SELECT `Category Webpage Index Stack`,`Category Webpage Index Key`,`Category Webpage Index Category Key` AS subject_key FROM `Category Webpage Index`    WHERE  `Category Webpage Index Webpage Key`=%d AND  `Category Webpage Index Section Key`=%d ORDER BY `Category Webpage Index Stack` ",
                                        $this->id, $item_section_key

                                    );
                                    if ($result = $this->db->query($sql)) {
                                        foreach ($result as $row) {
                                            $subjects[$row['Category Webpage Index Stack']] = $row['Category Webpage Index Key'];
                                        }
                                    }

                                    $stack_index = 0;
                                    foreach ($subjects as $tmp => $category_webpage_stack_key) {
                                        $stack_index++;
                                        $sql = sprintf(
                                            'UPDATE `Category Webpage Index` SET `Category Webpage Index Stack`=%d WHERE `Category Webpage Index Key`=%d ', $stack_index, $category_webpage_stack_key
                                        );
                                        $this->db->exec($sql);

                                    }


                                }


                            } else {
                                $this->msg   = 'Section not found in website';
                                $this->error = true;

                                return;
                            }
                        } else {
                            print_r($error_info = $this->db->errorInfo());
                            print "$sql\n";
                            exit;
                        }

                    }


                    $result = array();

                    foreach ($updated_metadata['section_keys'] as $section_key) {
                        foreach ($content_data['sections'] as $section_stack_index => $section_data) {
                            if ($section_data['key'] == $section_key) {
                                $content_data['sections'][$section_stack_index]['items'] = get_website_section_items($this->db, $section_data);
                                $result[$section_key]                                    = $content_data['sections'][$section_stack_index]['items'];
                                break;
                            }
                        }
                    }

                    $this->update(array('Page Store Content Data' => json_encode($content_data)), 'no_history');


                    return $result;

                }
                if ($category->get('Category Subject') == 'Product') {

                    $item_found   = false;
                    $target_found = false;

                    $sql = sprintf(
                        'SELECT `Product Category Index Key`,`Product Category Index Stack` FROM  `Product Category Index` WHERE `Product Category Index Website Key`=%d AND `Product Category Index Product ID`=%d ', $this->id, $item_key


                    );
                    if ($result = $this->db->query($sql)) {
                        if ($row = $result->fetch()) {

                            $item_section_key = 0;
                            $item_found       = true;
                            $item_stack       = $row['Product Category Index Stack'];
                            $item_index_key   = $row['Product Category Index Key'];
                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        print "xx $sql\n";
                        exit;
                    }


                    if (!$item_found) {
                        $this->msg   = 'Item not found in website';
                        $this->error = true;

                        return;

                    }


                    if ($target_key) {


                        $sql = sprintf(
                            'SELECT `Product Category Index Key`,`Product Category Index Stack` FROM  `Product Category Index` WHERE `Product Category Index Website Key`=%d AND `Product Category Index Product ID`=%d ', $this->id, $target_key


                        );

                        if ($result = $this->db->query($sql)) {
                            if ($row = $result->fetch()) {
                                $target_section_key = 0;
                                $target_found       = true;
                                $target_stack       = $row['Product Category Index Stack'];
                            }
                        } else {
                            print_r($error_info = $this->db->errorInfo());
                            print "$sql\n";
                            exit;
                        }


                        if (!$target_found) {
                            $this->msg   = 'Target not found in website';
                            $this->error = true;

                            return;

                        }

                        if ($item_section_key == $target_section_key) {


                            $updated_metadata['section_keys'][] = $item_section_key;

                            $subjects = array();

                            $sql = sprintf(
                                "SELECT `Product Category Index Stack`,`Product Category Index Key`,`Product Category Index Product ID` AS subject_key FROM `Product Category Index` WHERE  `Product Category Index Website Key`=%d ", $this->id
                            );


                            if ($result = $this->db->query($sql)) {
                                foreach ($result as $row) {


                                    if ($row['subject_key'] == $item_key) {
                                        $row['Product Category Index Stack'] = (string)($target_stack < $item_stack ? $target_stack - 0.5 : $target_stack + .5);
                                    }
                                    $subjects[$row['Product Category Index Stack']] = $row['Product Category Index Key'];
                                }
                            } else {
                                print_r($error_info = $this->db->errorInfo());
                                print "$sql\n";
                                exit;
                            }


                            ksort($subjects);
                            //print_r($subjects);

                            $stack_index = 0;
                            foreach ($subjects as $tmp => $category_webpage_stack_key) {
                                $stack_index++;

                                $sql = sprintf(
                                    'UPDATE `Product Category Index` SET `Product Category Index Stack`=%d WHERE `Product Category Index Key`=%d ', $stack_index, $category_webpage_stack_key
                                );

                                //print "$sql\n";

                                $this->db->exec($sql);

                            }


                        }


                    }


                    $result = array();


                    return $result;

                }


                break;
            default:
                break;
        }


    }

    function delete_section($section_key) {

        $content_data = $this->get('Content Data');


        foreach ($content_data['sections'] as $_key => $_data) {


            if ($_data['type'] == 'anchor') {
                $anchor_section_key = $_data['key'];

                break;
            }

        }
        $sql = sprintf(
            'SELECT max(`Category Webpage Index Stack`) AS stack FROM  `Category Webpage Index` WHERE `Category Webpage Index Webpage Key`=%d 
              AND `Category Webpage Index Section Key`=%d ', $this->id, $anchor_section_key
        );


        //  print $sql;

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $stack = $row['stack'];


                $sql = sprintf(
                    'UPDATE `Category Webpage Index` SET `Category Webpage Index Section Key`=%d , `Category Webpage Index Stack`=`Category Webpage Index Stack`+%d WHERE `Category Webpage Index Section Key`=%d', $anchor_section_key, $stack, $section_key


                );


                // print $sql;

                $this->db->exec($sql);


                $subjects = array();
                $sql      = sprintf(
                    "SELECT `Category Webpage Index Stack`,`Category Webpage Index Key`,`Category Webpage Index Category Key` AS subject_key FROM `Category Webpage Index`    WHERE  `Category Webpage Index Webpage Key`=%d AND  `Category Webpage Index Section Key`=%d ORDER BY `Category Webpage Index Stack` ",
                    $this->id, $anchor_section_key

                );


                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {
                        $subjects[$row['Category Webpage Index Stack']] = $row['Category Webpage Index Key'];
                    }
                }

                ksort($subjects);

                $stack_index = 0;
                foreach ($subjects as $tmp => $category_webpage_stack_key) {
                    $stack_index++;
                    $sql = sprintf(
                        'UPDATE `Category Webpage Index` SET `Category Webpage Index Stack`=%d WHERE `Category Webpage Index Key`=%d ', $stack_index, $category_webpage_stack_key
                    );
                    $this->db->exec($sql);

                }


            } else {
                $this->msg   = 'Section not found in website';
                $this->error = true;

                return;
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        foreach ($content_data['sections'] as $_key => $_data) {
            if ($_data['key'] == $section_key) {
                unset($content_data['sections'][$_key]);
                break;
            }

        }


        $result = array();


        foreach ($content_data['sections'] as $section_stack_index => $section_data) {
            if ($section_data['key'] == $anchor_section_key) {
                $content_data['sections'][$section_stack_index]['items'] = get_website_section_items($this->db, $section_data);
                $result[$anchor_section_key]                             = $content_data['sections'][$section_stack_index]['items'];
                break;
            }
        }

        $this->update(array('Page Store Content Data' => json_encode($content_data)), 'no_history');


        return $result;

    }

    function add_section() {

        $content_data = $this->get('Content Data');


        $section = array(
            'title'    => 'Bla bla',
            'subtitle' => 'bla bla',
            'type'     => 'page_break',
            'panels'   => array(),

            'items' => array()

        );

        if (isset($content_data['sections'])) {
            $section_stack_index = count($content_data['sections']) + 1;
        } else {
            $section_stack_index = 1;
        }


        $sql = sprintf(
            'INSERT INTO `Webpage Section Dimension` (`Webpage Section Webpage Key`,`Webpage Section Webpage Stack Index`,`Webpage Section Data`) VALUES (%d,%d,%s) ', $this->id, $section_stack_index, prepare_mysql(json_encode($section))

        );


        $this->db->exec($sql);
        $section['key'] = $this->db->lastInsertId();


        $content_data['sections'][] = $section;

        $this->update(array('Page Store Content Data' => json_encode($content_data)), 'no_history');


        $updated_metadata['new_section'] = $section;

        return $updated_metadata;

    }

    function update_webpage_section_order($section_key, $target_key) {


        $content_data = $this->get('Content Data');

        $section_index = 0;
        $target_index  = 0;

        $_sections = $content_data['sections'];


        foreach ($_sections as $index => $section_data) {


            if ($section_data['key'] == $section_key) {
                $section_index  = $index;
                $moving_section = $section_data;
                unset($_sections[$index]);
            }
            if ($section_data['key'] == $target_key) {
                $target_index = $index;
            }
        }


        if (!$section_index or !$target_index) {

            $this->error = true;
            $this->msg   = "Section index or target not found";

            return;
        }

        if ($section_index == $target_index) {

            $this->error = true;
            $this->msg   = "Same section index and target ";

            return;
        }


        $sections = array();


        if ($target_index > $section_index) {

            foreach ($_sections as $index => $section_data) {


                $sections[] = $section_data;
                if ($index == $target_index) {
                    $sections[] = $moving_section;
                }


            }

        } else {

            foreach ($_sections as $index => $section_data) {


                if ($index == $target_index) {
                    $sections[] = $moving_section;
                }
                $sections[] = $section_data;

            }

        }


        $content_data['sections'] = $sections;
        $this->update(array('Page Store Content Data' => json_encode($content_data)), 'no_history');


    }

    function add_panel($section_key, $panel_data) {

        $content_data = $this->get('Content Data');
        //   print_r($content_data['sections']);

        foreach ($content_data['sections'] as $_key => $section_data) {
            //print_r($section_data);

            //   print "$_key\n";
            if ($section_data['key'] == $section_key) {


                $section_index = $_key;
                $panels        = $section_data['panels'];

                //
                //  print "xx $section_index\n";
                // break;

                // print_r($_key);
            }

        }


        //print "yy $section_index\n";
        //print_r($content_data);
        //$panels=$content_data[$section_key]

        $size_tag = $panel_data['size'].'x';

        $panel = array(
            'id'   => $panel_data['id'],
            'type' => $panel_data['type'],
            'size' => $size_tag

        );

        if ($panel_data['type'] == 'image') {


            $panel['image_src'] = '/art/panel_'.$size_tag.'_1.png';
            $panel['link']      = '';
            $panel['caption']   = '';
            $panel['image_key'] = '';
        } elseif ($panel_data['type'] == 'text') {

            $panel['content'] = 'bla bla bla';
            $panel['class']   = 'text_panel_default';

        } elseif ($panel_data['type'] == 'code') {

            $panel['content'] = '';
            $panel['class']   = 'code_panel_default';


        } elseif ($panel_data['type'] == 'page_break') {

            $panel['title']    = 'Bla bla';
            $panel['subtitle'] = 'bla bla';


        }

        $sql = sprintf(
            'INSERT INTO `Webpage Panel Dimension` (`Webpage Panel Section Key`,`Webpage Panel Id`,`Webpage Panel Webpage Key`,`Webpage Panel Type`,`Webpage Panel Data`,`Webpage Panel Metadata`) VALUES (%d,%s,%d,%s,%s,%s) ', $section_key,
            prepare_mysql($panel_data['id']), $this->id, prepare_mysql($panel_data['type']), ($panel_data['type'] == 'code' ? prepare_mysql($panel['content']) : prepare_mysql('')), prepare_mysql(json_encode($panel))

        );


        // print $sql;

        $this->db->exec($sql);
        $panel['key'] = $this->db->lastInsertId();


        $panels[$panel_data['stack_index']] = $panel;

        ksort($panels);


        //  print_r($panels);


        // print "xxa $section_index";


        $content_data['sections'][$section_index]['panels'] = $panels;

        $content_data['sections'][$section_index]['items'] = get_website_section_items($this->db, $content_data['sections'][$section_index]);

        //print_r($content_data);
        $this->update(array('Page Store Content Data' => json_encode($content_data)), 'no_history');

        $result               = array();
        $result[$section_key] = $content_data['sections'][$section_index]['items'];


        return $result;


    }

    function sort_items($type) {

        $content_data = $this->get('Content Data');

        $updated_metadata = array('section_keys' => array());


        switch ($this->scope->get_object_name()) {


            case 'Category':
                include_once('class.Category.php');
                $category = new Category($this->scope->id);
                //print'x'.$category->get('Category Subject').'x';


                if ($category->get('Category Subject') == 'Product') {

                    $item_section_key                   = 0;
                    $updated_metadata['section_keys'][] = $item_section_key;

                    $subjects = array();


                    switch ($type) {
                        case 'code_asc':
                            $_order = 'order by `Product Code File As`';
                            break;
                        case 'code_desc':
                            $_order = 'order by `Product Code File As` desc';
                            break;
                        case 'name_asc':
                            $_order = 'order by `Product Name`';
                            break;
                        case 'name_desc':
                            $_order = 'order by `Product Name` desc';
                            break;
                        case 'sales_asc':
                            $_order = 'order by `Product 1 Year Acc Invoiced Amount`';
                            break;
                        case 'sales_desc':
                            $_order = 'order by Product 1 Year Acc Invoiced Amount` desc';
                            break;
                        case 'date_asc':
                            $_order = 'order by date(`Product Valid From`),`Product Code File As` ';
                            break;
                        case 'date_desc':
                            $_order = 'order by date(`Product Valid From`) desc, `Product Code File As` ';
                            break;
                        default:
                            $_order = 'order by `Product Code File As`';

                    }


                    $sql = sprintf(
                        "SELECT `Product Category Index Stack`,`Product Category Index Key`,`Product Category Index Product ID` AS subject_key FROM `Product Category Index`  left join `Product Dimension` P on ( P.`Product ID`=`Product Category Index Product ID`) left join `Product Data` D on ( D.`Product ID`=`Product Category Index Product ID`)  WHERE  `Product Category Index Website Key`=%d  $_order",
                        $this->id
                    );

                    $count = 0;
                    if ($result = $this->db->query($sql)) {
                        foreach ($result as $row) {


                            $subjects[$count++] = $row['Product Category Index Key'];
                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        print "$sql\n";
                        exit;
                    }


                    ksort($subjects);
                    // print $sql;
                    //print_r($subjects);

                    $stack_index = 0;
                    foreach ($subjects as $tmp => $category_webpage_stack_key) {
                        $stack_index++;

                        $sql = sprintf(
                            'UPDATE `Product Category Index` SET `Product Category Index Stack`=%d WHERE `Product Category Index Key`=%d ', $stack_index, $category_webpage_stack_key
                        );

                        //print "$sql\n";

                        $this->db->exec($sql);

                    }

                }

        }


        $result = array();


        return $result;

    }

    function get_field_label($field) {


        switch ($field) {

            case 'Webpage Code':
                $label = _('code');
                break;
            case 'Webpage Name':
                $label = _('name');
                break;

            case 'Webpage Locale':
                $label = _('language');
                break;
            case 'Webpage Timezone':
                $label = _('timezone');
                break;
            case 'Webpage Email':
                $label = _('email');
                break;

            case 'Webpage Browser Title':
                $label = _('browser title');
                break;
            case 'Webpage Meta Description':
                $label = _('meta description');
                break;
            case 'Webpage Redirection Code':
                $label = _('Permanent redirection');
                break;
            default:
                $label = $field;

        }

        return $label;

    }

}
?>
