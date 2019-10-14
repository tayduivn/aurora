<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 28 May 2016 at 19:36:52 CEST, Mijas Costa, Spain
 Copyright (c) 2015, Inikoo

 Version 3

*/


include_once 'class.DB_Table.php';
include_once 'class.Image.php';
include_once 'trait.ImageSubject.php';

class Website extends DB_Table {
    use ImageSubject;


    function __construct($a1, $a2 = false, $a3 = false) {


        global $db;
        $this->db = $db;

        $this->table_name    = 'Website';
        $this->ignore_fields = array('Website Key');

        if (is_numeric($a1) and !$a2) {
            $this->get_data('id', $a1);
        } elseif ($a1 == 'find') {
            $this->find($a2, $a3);

        } else {
            $this->get_data($a1, $a2);
        }
    }


    function get_data($key, $tag) {


        if ($key == 'id') {
            $sql = sprintf(
                "SELECT * FROM `Website Dimension` WHERE `Website Key`=%d", $tag
            );
        } else {
            if ($key == 'code') {
                $sql = sprintf(
                    "SELECT  * FROM `Website Dimension` WHERE `Website Code`=%s ", prepare_mysql($tag)
                );
            } else {
                return;
            }
        }


        if ($this->data = $this->db->query($sql)->fetch()) {
            $this->id   = $this->data['Website Key'];
            $this->code = $this->data['Website Code'];

            if (empty($this->data['Website Settings'])) {
                $this->settings = array();
            } else {
                $this->settings = json_decode($this->data['Website Settings'], true);
            }

            if (empty($this->data['Website Style'])) {
                $this->style = array();
            } else {
                $this->style = json_decode($this->data['Website Style'], true);
            }

            if (empty($this->data['Website Mobile Style'])) {
                $this->mobile_style = array();
            } else {
                $this->mobile_style = json_decode($this->data['Website Mobile Style'], true);
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
            } elseif ($key == 'Website Key') {
                $data[$key] = _trim($value);
            }
        }


        if ($data['Website Code'] == '') {
            $this->error = true;
            $this->msg   = 'Website code empty';

            return;
        }

        if ($data['Website Name'] == '') {
            $data['Website Name'] = $data['Website Code'];
        }


        $sql = sprintf(
            "SELECT `Website Key` FROM `Website Dimension` WHERE `Website Code`=%s  ", prepare_mysql($data['Website Code'])
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {

                $this->found     = true;
                $this->found_key = $row['Website Key'];
                $this->get_data('id', $this->found_key);
                $this->duplicated_field = 'Website Code';

                return;
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }
        $sql = sprintf(
            "SELECT `Website Key` FROM `Website Dimension` WHERE `Website Name`=%s  ", prepare_mysql($data['Website Name'])
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {

                $this->found     = true;
                $this->found_key = $row['Website Key'];
                $this->get_data('id', $this->found_key);
                $this->duplicated_field = 'Website Name';

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
            } elseif ($key == 'Website Key') {
                $base_data[$key] = _trim($value);
            }
        }

        $keys   = '(';
        $values = 'values(';
        foreach ($base_data as $key => $value) {
            $keys .= "`$key`,";
            //   if (preg_match('/^()$/i', $key))
            //    $values.=prepare_mysql($value, false).",";
            //   else
            $values .= prepare_mysql($value).",";
        }
        $keys   = preg_replace('/,$/', ')', $keys);
        $values = preg_replace('/,$/', ')', $values);
        $sql    = sprintf(
            "INSERT INTO `Website Dimension` %s %s", $keys, $values
        );


        if ($this->db->exec($sql)) {
            $this->id  = $this->db->lastInsertId();
            $this->msg = _("Website added");
            $this->get_data('id', $this->id);
            $this->new = true;


            $sql = "INSERT INTO `Website Data` (`Website Key`) VALUES(".$this->id.");";
            $this->db->exec($sql);


            require_once 'conf/footer_data.php';
            require_once 'conf/header_data.php';


            $footer_data = array(
                'Website Footer Code' => 'default',
                'Website Footer Data' => json_encode(get_default_footer_data(1)),
                'editor'              => $this->editor

            );
            $this->create_footer($footer_data);


            $logo_image_key = $this->add_image(
                array(
                    'Image Filename'                   => 'website.logo.png',
                    'Upload Data'                      => array(
                        'tmp_name' => 'conf/website.logo.png',
                        'type'     => 'png'
                    ),
                    'Image Subject Object Image Scope' => 'Logo'

                )
            );


            $_header_data                   = get_default_header_data(1);
            $_header_data['logo_image_key'] = $logo_image_key;
            $header_data                    = array(
                'Website Header Code' => 'default',
                'Website Header Data' => json_encode($_header_data),
                'editor'              => $this->editor


            );
            $this->create_header($header_data);


            include_once 'conf/webpage_types.php';
            $webpage_types=get_webpage_types();
            foreach ($webpage_types as $webpage_type) {
                $sql = sprintf(
                    'INSERT INTO `Webpage Type Dimension` (`Webpage Type Website Key`,`Webpage Type Code`) VALUES (%d,%s) ', $this->id, prepare_mysql($webpage_type['code'])
                );
                $this->db->exec($sql);
            }

            include_once 'conf/website_system_webpages.php';
            foreach (website_system_webpages_config($this->get('Website Type')) as $website_system_webpages) {
                $this->create_system_webpage($website_system_webpages);
            }


            if (is_numeric($this->editor['User Key']) and $this->editor['User Key'] > 1) {

                $sql = sprintf("INSERT INTO `User Right Scope Bridge` VALUES(%d,'Website',%d)", $this->editor['User Key'], $this->id);
                $this->db->exec($sql);

            }


            include_once 'class.Store.php';
            $store = new Store($this->get('Website Store Key'));

            $account         = new Account($this->db);
            $account->editor = $this->editor;

            $families_category_data = array(
                'Category Code'      => 'Web.Fam.'.$store->get('Store Code'),
                'Category Label'     => 'Web families',
                'Category Scope'     => 'Product',
                'Category Subject'   => 'Product',
                'Category Store Key' => $this->get('Website Store Key')


            );


            $families = $account->create_category($families_category_data);


            $departments_category_data = array(
                'Category Code'      => 'Web.Dept.'.$store->get('Store Code'),
                'Category Label'     => 'Web departments',
                'Category Scope'     => 'Product',
                'Category Subject'   => 'Category',
                'Category Store Key' => $this->get('Website Store Key')


            );


            $departments = $account->create_category($departments_category_data);

            $this->update(
                array(

                    'Website Alt Family Category Key'     => $families->id,
                    'Website Alt Department Category Key' => $departments->id,
                ), 'no_history'
            );

            require_once 'conf/website_styles.php';


            $this->fast_update(
                array(
                    'Website Style' => json_encode($website_styles)
                )
            );


            return;
        } else {
            $this->msg = "Error can not create website";
            print $sql;
            exit;
        }
    }

    function create_footer($data) {

        include_once 'class.WebsiteFooter.php';

        if (!isset($data['Website Footer Code'])) {
            $this->error = true;
            $this->msg   = 'no footer code';

            return;
        }

        if ($data['Website Footer Code'] == '') {
            $this->error = true;
            $this->msg   = 'footer code empty';

            return;
        }

        $data['Website Footer Code'] = $this->get_unique_code($data['Website Footer Code'], 'Footer');

        $data['Website Footer Website Key'] = $this->id;


        $footer = new WebsiteFooter('find', $data, 'create');
        if (!$footer->id) {
            $this->error = true;
            $this->msg   = $footer->msg;

            return;
        }

        if (!$this->get('Website Footer Key')) {

            $this->update(
                array('Website Footer Key' => $footer->id), 'no_history'

            );

        }

    }

    function get_unique_code($code, $type) {


        for ($i = 1; $i <= 200; $i++) {

            if ($i == 1) {
                $suffix = '';
            } elseif ($i <= 100) {
                $suffix = $i;
            } else {
                $suffix = uniqid('', true);
            }

            if ($type == 'Webpage') {
                $sql = sprintf("SELECT `Page Key` FROM `Page Store Dimension`  WHERE `Webpage Website Key`=%d AND `Webpage Code`=%s  ", $this->id, prepare_mysql($code.$suffix));
            } elseif ($type == 'Footer') {
                $sql = sprintf(
                    "SELECT `Website Footer Key` FROM `Website Footer Dimension`  WHERE `Website Footer Website Key`=%d AND `Website Footer Code`=%s  ", $this->id, prepare_mysql($code.$suffix)
                );
            } elseif ($type == 'Header') {
                $sql = sprintf(
                    "SELECT `Website Header Key` FROM `Website Header Dimension`  WHERE `Website Header Website Key`=%d AND `Website Header Code`=%s  ", $this->id, prepare_mysql($code.$suffix)
                );
            } else {
                exit('error unknown type in get_unique_code ');
            }


            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {

                } else {
                    return $code.$suffix;
                }
            }


        }

        return $suffix;
    }

    function get($key, $data = false) {

        if (!$this->id) {
            return '';
        }


        switch ($key) {


            case 'Registration Type':

                switch ($this->data['Website Registration Type']) {
                    case 'Open':
                        return _('Open');
                        break;
                    case 'Closed':
                        return _('Closed');
                        break;
                    case 'ApprovedOnly':
                        return _('Only approved');
                        break;
                    default:
                        return $this->data['Website Registration Type'];

                }

                break;

            case 'User Password Recovery Email':

                global $user;

                return $user->get('User Password Recovery Email');

                break;


            case 'Palette':

                return '<img style="width:150px;height:150px;" src="/'.$this->data['Website Palette'].'"/>';

                break;

            case 'Localised Labels':

                if ($this->data['Website '.$key] == '') {
                    $labels = array();
                } else {
                    $labels = json_decode($this->data['Website '.$key], true);
                }

                return $labels;
                break;
            case 'Data':

                if ($this->data['Website '.$key] == '') {
                    $content_data = false;
                } else {
                    $content_data = json_decode($this->data['Website '.$key], true);
                }

                return $content_data;
                break;


            case 'Footer Data':
            case 'Footer Published Data':

                $sql = sprintf('SELECT `Website %s` AS data FROM `Website Footer Dimension` WHERE `Website Footer Key`=%d  ', $key, $this->get('Website Footer Key'));
                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {
                        return json_decode($row['data'], true);
                    } else {
                        return false;
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }
                break;
            case 'Header Data':
            case 'Header Published Data':

                $sql = sprintf('SELECT `Website %s` AS data FROM `Website Header Dimension` WHERE `Website Header Key`=%d  ', $key, $this->get('Website Header Key'));
                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {
                        return json_decode($row['data'], true);
                    } else {
                        return false;
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    print "$sql\n";
                    exit;
                }
                break;

            case 'Settings Info Bar Basket Amount Type':
                switch ($this->get('Website Settings Info Bar Basket Amount Type')) {
                    case 'items_net':
                        return _('Items net');
                    default:
                        return _('Total');

                }

                break;
            case 'Settings Display Stock Levels in Product':

                $value = $this->settings('Display Stock Levels in Product');
                if ($value == 'Yes') {
                    return _('Yes');
                } else {
                    return _('No');
                }
                break;
            case 'Settings Display Stock Levels in Category':


                switch ($this->settings('Display Stock Levels in Category')) {
                    case 'Hint_Bar':
                        return '<i class="fa fa-window-minimize" style="color: #13D13D"></i><i class="fa fa-window-minimize" style="color: #FCBE07"></i><i class="fa fa-window-minimize margin_right_10" style="color: #F25056"></i>'._('Bar hint');
                        break;
                    case 'Dot':
                        return '<i class="fa fa-circle" style="color: #13D13D"></i> <i class="fa fa-circle" style="color: #FCBE07"></i> <i class="fa fa-circle margin_right_10" style="color: #F25056"></i>'._('Dot');
                        break;
                    default:
                        return _('No');
                }
                break;
            case 'Settings Display Stock Quantity':


                switch ($this->settings('Display Stock Quantity')) {
                    case 'Yes':
                        return _('Yes');
                        break;
                    case 'Only_if_very_low':
                        return _('Only if stock very low');
                        break;
                    default:
                        return _('No');
                }


            default:


                if (preg_match('/^Website Settings /', $key)) {
                    $_key = preg_replace('/^Website Settings /', '', $key);

                    return $this->settings($_key);
                }

                if (preg_match('/^Settings /', $key)) {


                    $_key = preg_replace('/^Settings /', '', $key);

                    return $this->settings($_key);


                }


                if (preg_match('/^Website Style /', $key)) {


                    $_key = preg_replace('/^Website Style /', '', $key);
                    if (isset($this->style[$_key])) {
                        return $this->style[$_key];
                    } else {
                        return '';
                    }

                }

                if (preg_match('/^Style /', $key)) {


                    $_key = preg_replace('/^Style /', '', $key);
                    if (isset($this->style[$_key])) {
                        return $this->style[$_key];
                    } else {
                        return '';
                    }

                }


                if (array_key_exists($key, $this->data)) {
                    return $this->data[$key];
                }

                if (array_key_exists('Website '.$key, $this->data)) {
                    return $this->data['Website '.$key];
                }


        }

        return '';
    }


    function settings($key) {
        return (isset($this->settings[$key]) ? $this->settings[$key] : '');
    }

    function create_header($data) {

        include_once 'class.WebsiteHeader.php';

        if (!isset($data['Website Header Code'])) {
            $this->error = true;
            $this->msg   = 'no header code';

            return;
        }

        if ($data['Website Header Code'] == '') {
            $this->error = true;
            $this->msg   = 'header code empty';

            return;
        }

        $data['Website Header Code'] = $this->get_unique_code($data['Website Header Code'], 'Header');

        $data['Website Header Website Key'] = $this->id;


        $header = new WebsiteHeader('find', $data, 'create');
        if (!$header->id) {
            $this->error = true;
            $this->msg   = $header->msg;

            return;
        }

        if (!$this->get('Website Header Key')) {

            $this->update(
                array('Website Header Key' => $header->id), 'no_history'

            );

        }

    }


    function create_system_webpage($data) {


        include_once 'class.Webpage_Type.php';
        include_once 'class.Page.php';


        if (empty($data['Webpage Code'])) {
            $this->error = true;
            $this->msg   = 'Webpage code empty';

            return;
        }

        if (empty($data['Webpage Name'])) {
            $this->error = true;
            $this->msg   = 'Webpage name empty';

            return;
        }

        if (empty($data['Webpage Browser Title'])) {
            $this->error = true;
            $this->msg   = 'Webpage Browser Title empty';

            return;
        }

        if (empty($data['Webpage Meta Description'])) {
            $data['Webpage Meta Description'] = '';
        }


        $webpage_type = new Webpage_Type('website_code', $this->id, $data['Webpage Type']);

        unset($data['Webpage Type']);


        $page_data = array(

            'Page URL'                 => $this->data['Website URL'].'/'.strtolower($data['Webpage Code']),
            'Page Type'                => 'Store',
            'Page Store Key'           => $this->get('Website Store Key'),
            'Page Store Creation Date' => gmdate('Y-m-d H:i:s'),
            'Number See Also Links'    => 0,

            'Page Title'             => $data['Webpage Name'],
            'Page Short Title'       => $data['Webpage Browser Title'],
            'Page Parent Key'        => 0,
            'Page State'             => 'Online',
            'Page Store Description' => $data['Webpage Meta Description'],


            'Webpage Scope'                 => $data['Webpage Scope'],
            'Webpage Scope Key'             => '',
            'Webpage Website Key'           => $this->id,
            'Webpage Store Key'             => $this->get('Website Store Key'),
            'Webpage Type Key'              => $webpage_type->id,
            'Webpage Code'                  => $data['Webpage Code'],
            'Webpage Number See Also Links' => 0,
            'Webpage Creation Date'         => gmdate('Y-m-d H:i:s'),
            'Webpage URL'                   => $this->data['Website URL'].'/'.strtolower($data['Webpage Code']),
            'Webpage Name'                  => $data['Webpage Name'],
            'Webpage Browser Title'         => $data['Webpage Browser Title'],
            'Webpage State'                 => ($data['Webpage Scope'] == 'HomepageToLaunch' ? 'Online' : 'InProcess'),
            'Webpage Meta Description'      => $data['Webpage Meta Description'],
            'Page Store Content Data'       => (isset($data['Page Store Content Data']) ? $data['Page Store Content Data'] : ''),


            'editor' => $this->editor,
        );


        $page = new Page('find', $page_data, 'create');


        $webpage_type->update_number_webpages();
        $this->update_website_webpages_data();


        if ($data['Webpage Scope'] == 'HomepageToLaunch') {
            $page->publish();
        }


        $this->new_page     = $page->new;
        $this->new_page_key = $page->id;
        $this->msg          = $page->msg;
        $this->error        = $page->error;


        return $page;

    }

    function update_website_webpages_data() {
        $sql = "SELECT `Webpage State`,count(*) AS number FROM `Page Store Dimension` WHERE `Webpage Website Key`=%d  group by `Webpage State`";

        $number_online_webpages     = 0;
        $number_offline_webpages    = 0;
        $number_in_process_webpages = 0;

        $stmt = $this->db->prepare($sql);
        $stmt->execute(
            array($this->id)
        );
        while ($row = $stmt->fetch()) {
            if ($row['Webpage State'] == 'Online') {
                $number_online_webpages = $row['number'];
            } elseif ($row['Webpage State'] == 'Offline') {
                $number_offline_webpages = $row['number'];
            } elseif ($row['Webpage State'] == 'InProcess') {
                $number_in_process_webpages = $row['number'];
            }
        }


        $this->fast_update(
            array(
                'Website Number Online Webpages'     => $number_online_webpages,
                'Website Number Offline Webpages'    => $number_offline_webpages,
                'Website Number In Process Webpages' => $number_in_process_webpages,
            ), 'Website Data'
        );


    }

    function update_labels_in_localised_labels($labels, $operation = 'append') {

        $localised_labels = $this->get('Localised Labels');
       // print_r($labels);

      //  print_r($localised_labels);

        switch ($operation) {
            case 'append':
                $localised_labels = array_merge($localised_labels, $labels);


        }


        $this->fast_update(array('Website Localised Labels' => json_encode($localised_labels)));
        $this->clean_cache();

    }

    function clean_cache() {


        $smarty_web               = new Smarty();
        $smarty_web->template_dir = 'EcomB2B/templates';
        $smarty_web->compile_dir  = 'EcomB2B/server_files/smarty/templates_c';
        $smarty_web->cache_dir    = 'EcomB2B/server_files/smarty/cache';
        $smarty_web->config_dir   = 'EcomB2B/server_files/smarty/configs';
        $smarty_web->addPluginsDir('./smarty_plugins');

        $smarty_web->setCaching(Smarty::CACHING_LIFETIME_CURRENT);

        $smarty_web->clearCache(null, $this->id);


    }

    function update_settings($labels, $operation = 'append') {


        switch ($operation) {
            case 'append':
                $this->settings = array_merge($this->settings, $labels);


        }


        $this->fast_update(array('Website Settings' => json_encode($this->settings)));


        if (empty($this->data['Website Settings'])) {
            $this->settings = array();
        } else {
            $this->settings = json_decode($this->data['Website Settings'], true);
        }
        include_once 'utils/image_functions.php';


        if (!empty($this->settings['favicon'])) {

            if (preg_match('/id=(\d+)/', $this->settings['favicon'], $matches)) {

                $favicon_website = 'wi.php?id='.$matches[1].'&s='.get_image_size($matches[1], 32, 32, 'fit_highest');
                $this->fast_update(array('Website Settings' => json_encode(array_merge($this->settings, array('favicon_website' => $favicon_website)))));
                $this->settings = json_decode($this->data['Website Settings'], true);
            }
        }

        if (!empty($this->settings['favicon'])) {

            if (preg_match('/id=(\d+)/', $this->settings['favicon'], $matches)) {

                $favicon_website = 'wi.php?id='.$matches[1].'&s='.get_image_size($matches[1], 32, 32, 'fit_highest');
                $this->fast_update(array('Website Settings' => json_encode(array_merge($this->settings, array('favicon_website' => $favicon_website)))));
                $this->settings = json_decode($this->data['Website Settings'], true);
            }
        }

        $this->clean_cache();
    }

    function update_styles($styles, $operation = 'append') {


        switch ($operation) {
            case 'append':
                $this->style = array_merge($this->style, $styles);


        }


        $this->fast_update(array('Website Style' => json_encode($this->style)));
        $this->clean_cache();
    }

    function update_mobile_styles($styles, $operation = 'append') {


        switch ($operation) {
            case 'append':
                $this->mobile_style = array_merge($this->mobile_style, $styles);
                break;
            case 'replace':
                $this->mobile_style = $styles;
                break;

        }


        $this->fast_update(array('Website Mobile Style' => json_encode($this->mobile_style)));
        $this->clean_cache();
    }

    function update_field_switcher($field, $value, $options = '', $metadata = '') {


        if ($this->deleted) {
            return;
        }

        switch ($field) {

            case 'User Password Recovery Email':

                global $user;
                $user->editor = $this->editor;
                $user->update(array('User Password Recovery Email' => $value));

                break;


            case 'Website URL':


                $this->update_field($field, $value, $options);

                $this->clean_cache();

                break;


            default:
                $base_data = $this->base_data();
                if (array_key_exists($field, $base_data)) {
                    if ($value != $this->data[$field]) {
                        $this->update_field($field, $value, $options);
                    }
                }


        }
    }

    function get_field_label($field) {

        switch ($field) {

            case 'Website Code':
                $label = _('code');
                break;
            case 'Website Name':
                $label = _('name');
                break;
            case 'Website Address':
                $label = _('address');
                break;
            case 'Website Registration Type':
                $label = _('registration type');
                break;
            case 'Website Settings Info Bar Basket Amount Type':
                $label = _('info bar basket amount type');
                break;
            default:


                $label = $field;

        }

        return $label;

    }

    function get_webpage($code) {

        if ($code == '') {
            $code = 'p.home';
        }

        include_once 'class.Page.php';
        $webpage = new Page('website_code', $this->id, $code);

        return $webpage;


    }

    function get_default_template_key($scope, $device = 'Desktop') {

        $template_key = false;

        $sql = sprintf(
            'SELECT `Template Key` FROM `Template Dimension` WHERE `Template Website Key`=%d AND `Template Scope`=%s AND `Template Device`=%s ', $this->id, prepare_mysql($scope), prepare_mysql($device)

        );
        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $template_key = $row['Template Key'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }

        if (!$template_key) {


            $sql = sprintf(
                'SELECT `Template Key` FROM `Template Dimension` WHERE `Template Website Key`=%d AND `Template Scope`=%s AND `Template Device`="Desktop" ', $this->id, prepare_mysql($scope)

            );
            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $template_key = $row['Template Key'];
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                print "$sql\n";
                exit;
            }

        }

        if (!$template_key) {


            $sql = sprintf(
                'SELECT `Template Key` FROM `Template Dimension` WHERE `Template Website Key`=%d AND `Template Scope`="Blank" AND `Template Device`=%s ', $this->id, prepare_mysql($scope)

            );
            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $template_key = $row['Template Key'];
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                print "$sql\n";
                exit;
            }

        }

        // print $template_key;


        return $template_key;

    }

    function create_category_webpage($category_key) {

        include_once 'class.Webpage_Type.php';
        include_once 'class.Page.php';
        include_once 'class.Category.php';

        $category = new Category($category_key);

        $sql = sprintf(
            "SELECT `Page Key` FROM `Page Store Dimension` WHERE `Webpage Scope`=%s AND `Webpage Scope Key`=%d  AND `Webpage Website Key`=%d ", prepare_mysql(($category->get('Category Subject') == 'Product' ? 'Category Products' : 'Category Categories')), $category_key,
            $this->id
        );

        //print "$sql\n";

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                return $row['Page Key'];
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $page_code = $this->get_unique_code($category->get('Code'), 'Webpage');


        $webpage_type = new Webpage_Type('website_code', $this->id, ($category->get('Category Subject') == 'Product' ? 'Prods' : 'Cats'));


        $page_data = array(
            'Page URL'                 => $this->data['Website URL'].'/'.strtolower($page_code),
            'Page Type'                => 'Store',
            'Page Store Key'           => $category->get('Category Store Key'),
            'Page Store Creation Date' => gmdate('Y-m-d H:i:s'),
            'Number See Also Links'    => ($category->get('Category Subject') == 'Product' ? 5 : 0),


            'Webpage Scope'                 => ($category->get('Category Subject') == 'Product' ? 'Category Products' : 'Category Categories'),
            'Webpage Scope Key'             => $category->id,
            'Webpage Website Key'           => $this->id,
            'Webpage Store Key'             => $category->get('Category Store Key'),
            'Webpage Type Key'              => $webpage_type->id,
            'Webpage Code'                  => $page_code,
            'Webpage Number See Also Links' => ($category->get('Category Subject') == 'Product' ? 5 : 0),
            'Webpage Creation Date'         => gmdate('Y-m-d H:i:s'),
            'Webpage Name'                  => $category->get('Label'),
            'Webpage Browser Title'         => $category->get('Label'),


            'Page Parent Key'                        => $category->id,
            'Page Parent Code'                       => $category->get('Code'),
            'Page Store Section Type'                => 'Department',
            'Page Store Section'                     => 'Department Catalogue',
            'Page Store Last Update Date'            => gmdate('Y-m-d H:i:s'),
            'Page Store Last Structural Change Date' => gmdate('Y-m-d H:i:s'),
            'Page Locale'                            => $this->data['Website Locale'],
            'Page Source Template'                   => '',
            'Page Description'                       => '',
            'Page Title'                             => $category->get('Label'),
            'Page Short Title'                       => $category->get('Label'),
            'Page Store Title'                       => $category->get('Label'),

            'editor' => $this->editor,

        );


        //  print_r($page_data);
        // exit;
        $page = new Page('find', $page_data, 'create');

        $category->update(array('Product Category Webpage Key' => $page->id), 'no_history');


        $sql = sprintf(
            'UPDATE `Product Category Index` SET `Product Category Index Website Key`=%d WHERE `Product Category Index Category Key`=%d  ', $page->id, $category->id
        );
        //        print "$sql\n";
        $this->db->exec($sql);


        if ($page->new) {
            $page->reset_object();
        }


        //   print_r($page->data);
        //  print_r($category->get('Category Subject'));

        $webpage_type->update_number_webpages();
        $this->update_website_webpages_data();


        $this->new_page     = $page->new;
        $this->new_page_key = $page->id;
        $this->msg          = $page->msg;
        $this->error        = $page->error;


        return $page->id;

    }

    function create_product_webpage($product_id) {


        include_once 'class.Webpage_Type.php';
        include_once 'class.Page.php';
        include_once 'class.Product.php';


        $sql = sprintf(
            "SELECT `Page Key` FROM `Page Store Dimension` WHERE `Webpage Scope`='Product' AND `Webpage Scope Key`=%d  AND `Webpage Website Key`=%d ", $product_id, $this->id
        );

        //print $sql;

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {

                $product = new Product($product_id);

                $product->fast_update(array('Product Webpage Key' => $row['Page Key']));

                return $row['Page Key'];


            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $product = get_object('product', $product_id);

        $page_code = $this->get_unique_code($product->get('Code'), 'Webpage');


        $webpage_type = new Webpage_Type('website_code', $this->id, 'Prod');


        $page_data = array(
            'Page URL'                 => $this->data['Website URL'].'/'.strtolower($page_code),
            'Page Type'                => 'Store',
            'Page Store Key'           => $product->get('Product Store Key'),
            'Page Store Creation Date' => gmdate('Y-m-d H:i:s'),
            'Number See Also Links'    => 5,


            'Webpage Scope'                          => 'Product',
            'Webpage Scope Key'                      => $product->id,
            'Webpage Website Key'                    => $this->id,
            'Webpage Store Key'                      => $product->get('Product Store Key'),
            'Webpage Type Key'                       => $webpage_type->id,
            'Webpage Code'                           => $page_code,
            'Webpage Number See Also Links'          => 5,
            'Webpage Creation Date'                  => gmdate('Y-m-d H:i:s'),
            'Webpage Name'                           => $product->get('Name'),
            'Webpage Browser Title'                  => $product->get('Name'),

            //--------   to remove ??
            'Page Parent Key'                        => $product->id,
            'Page Parent Code'                       => $product->get('Code'),
            'Page Store Section Type'                => 'Product',
            'Page Store Section'                     => 'Product Description',
            'Page Store Last Update Date'            => gmdate('Y-m-d H:i:s'),
            'Page Store Last Structural Change Date' => gmdate('Y-m-d H:i:s'),
            'Page Locale'                            => $this->data['Website Locale'],
            'Page Source Template'                   => '',
            'Page Description'                       => '',
            'Page Title'                             => $product->get('Name'),
            'Page Short Title'                       => $product->get('Name'),
            'Page Store Title'                       => $product->get('Name'),


            'editor' => $this->editor

        );


        $page = new Page('find', $page_data, 'create');


        // print $page->id;


        $product->fast_update(array('Product Webpage Key' => $page->id));


        $webpage_type->update_number_webpages();
        $this->update_website_webpages_data();


        $this->new_page     = $page->new;
        $this->new_page_key = $page->id;
        $this->msg          = $page->msg;
        $this->error        = $page->error;


        $page->reset_object();

        $sql = "INSERT INTO `Product Webpage Bridge` (`Product Webpage Product ID`,`Product Webpage Webpage Key`,`Product Webpage Block`,`Product Webpage Type`) values (?,?,'product','product')";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $product->id);
        $stmt->bindValue(2, $page->id);

        $stmt->execute();


        //todo: AUR-33
        // 'InProcess','Active','Suspended','Discontinuing','Discontinued'
        if ($product->get('Product Status') == 'Discontinued' or $product->get('Product Status') == 'Suspended') {
            $page->unpublish();
        } elseif ($product->get('Product Status') == 'Discontinuing' or $product->get('Product Status') == 'Active') {
            $page->publish();
        }


        return $page->id;

    }

    function reset_element($type) {

        if ($type == 'website_footer') {


            $footer         = get_object('footer', $this->get('Website Footer Key'));
            $footer->editor = $this->editor;
            $footer->reset();

        } elseif ($type == 'website_header') {


            $header         = get_object('header', $this->get('Website Header Key'));
            $header->editor = $this->editor;
            $header->reset();


        }
    }


    function get_system_webpage_key($code) {

        $sql = sprintf(
            'SELECT `Page Key` FROM `Page Store Dimension` WHERE `Webpage Code`=%s AND `Webpage Website Key`=%d  ', prepare_mysql($code), $this->id
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                return $row['Page Key'];
            } else {
                return 0;
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }

    }

    function launch() {

        include_once 'class.Page.php';


        $this->update(array('Website Status' => 'Active'));


        $sql = sprintf(
            "SELECT `Page Key` FROM `Page Store Dimension`  P LEFT JOIN `Webpage Type Dimension` WTD ON (WTD.`Webpage Type Key`=P.`Webpage Type Key`)  WHERE `Webpage Website Key`=%d AND `Webpage Type Code` IN ('Info','Home','Ordering','Customer','Portfolio','Sys')   ",
            $this->id
        );

        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {

                $webpage         = new Page($row['Page Key']);
                $webpage->editor = $this->editor;

                if ($webpage->get('Webpage Code') == 'launching.sys') {
                    $webpage->update(array('Webpage State' => 'Offline'));
                } else {
                    $webpage->update(array('Webpage State' => 'Online'));
                    $webpage->update(array('Webpage Launch Date' => gmdate('Y-m-d H:i:s')), 'no_history');
                }


            }
        }

        include_once 'utils/new_fork.php';
        global $account;
        new_housekeeping_fork(
            'au_housekeeping', array(
            'type'        => 'website_launched',
            'website_key' => $this->id,
            'editor'      => $this->editor,

        ), $account->get('Account Code')
        );


    }

    function create_user($data) {

        include_once 'class.Website_User.php';

        $this->new = false;

        $data['editor']                   = $this->editor;
        $data['Website User Website Key'] = $this->id;
        $data['Website User Active']      = 'Yes';


        $website_user = new Website_User('new', $data);


        if ($website_user->id) {

            if ($website_user->new) {


            } else {
                $this->error = true;
                $this->msg   = $website_user->msg;


            }

            return $website_user;
        } else {
            $this->error = true;
            $this->msg   = $website_user->msg;
        }
    }

    function update_users_data() {

        $users = 0;
        $sql   = 'select count(*) as num from `Website User Dimension` where `Website User Website Key`=? and `Website User Has Login`="Yes"  ';
        $stmt  = $this->db->prepare($sql);
        $stmt->execute(
            array($this->id)
        );
        while ($row = $stmt->fetch()) {
            $users = $row['num'];
        }


        $this->fast_update(array('Website Total Acc Users' => $users), 'Website Data');

    }

    function update_sitemap() {


        $sql = sprintf(
            "DELETE FROM `Sitemap Dimension` WHERE `Sitemap Website Key`=%d  ", $this->id
        );


        $this->db->exec($sql);


        include_once 'class.Sitemap.php';
        $sitemap = new Sitemap($this->id);
        $sitemap->page('info');


        //ENUM('About','Basket','Catalogue','Category Categories','Category Products','Checkout','Contact','Homepage','HomepageLogout','HomepageNoOrders','HomepageToLaunch','Info','InProcess','Login','NotFound','Offline','Product','Register','ResetPwd','Search','ShippingInfo','TandC','Thanks','UserProfile','Welcome') NOT NULL


        $sql = sprintf(
            "SELECT `Webpage Launch Date`,`Webpage URL` FROM `Page Store Dimension`  WHERE `Webpage Website Key`=%d  AND  `Webpage Scope`  NOT IN  ('Category Categories','Category Products','Product') AND `Webpage Code` not in 
                                                          ('in_process.sys','profile.sys','basket.sys','checkout.sys','favourites.sys','home.sys','not_found.sys','offline.sys','reset_pwd.sys','search.sys',
                                                          'thanks.sys','welcome.sys','unsubscribe.sys'
                                                          )  and   `Webpage State`='Online'   ", $this->id
        );

        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $updated = $row['Webpage Launch Date'];
                $sitemap->url($row['Webpage URL'], $updated, 'monthly');
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $sitemap->page('products');

        $sql = sprintf(
            "SELECT `Webpage Launch Date`,`Webpage URL` FROM `Page Store Dimension`  WHERE `Webpage Website Key`=%d  AND  `Webpage Scope`  IN  ('Category Categories','Category Products','Product') AND `Webpage State`='Online'   ", $this->id
        );

        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $updated = $row['Webpage Launch Date'];
                $sitemap->url($row['Webpage URL'], $updated, 'weekly');
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        $sitemap->close();
        unset ($sitemap);


        $this->update_field('Website Sitemap Last Update', gmdate("Y-m-d H:i:s"), 'no_history');


    }

    function get_number_images() {

    }

    function get_main_image_key() {

    }

    function update_gsc_data() {

        $sql  = 'select `Website GSCD Clicks`,`Website GSCD Impressions`,`Website GSCD CTR`,`Website GSCD Position` from `Website GSC Data` where `Website GSCD Interval`=? and `Website GSCD Website Key`=?  ';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(
            array(
                '1 Month',
                $this->id
            )
        );
        if ($row = $stmt->fetch()) {
            $this->fast_update(
                array(
                    'Website GSC Clicks'      => $row['Website GSCD Clicks'],
                    'Website GSC Impressions' => $row['Website GSCD Impressions'],
                    'Website GSC CTR'         => $row['Website GSCD CTR'],
                    'Website GSC Position'    => $row['Website GSCD Position'],

                ), 'Website Data'
            );
        }

    }

}



