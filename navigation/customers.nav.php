<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 28 August 2015 18:15:18 GMT+8 Singapore

 Copyright (c) 2015, Inikoo

 Version 3.0
*/

function get_customers_navigation($data, $smarty, $user, $db) {


    require_once 'class.Store.php';

    switch ($data['parent']) {
        case 'store':

            break;
        default:

            break;
    }

    $block_view = $data['section'];


    $left_buttons = array();
    if ($user->stores > 1) {


        list($prev_key, $next_key) = get_prev_next(
            $data['store']->id, $user->stores
        );

        $sql = sprintf(
            "SELECT `Store Code` FROM `Store Dimension` WHERE `Store Key`=%d", $prev_key
        );
        if ($result = $db->query($sql)) {
            if ($row = $result->fetch()) {
                $prev_title = _('Customers').' '.$row['Store Code'];
            } else {
                $prev_title = '';
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }


        $sql = sprintf(
            "SELECT `Store Code` FROM `Store Dimension` WHERE `Store Key`=%d", $next_key
        );
        if ($result = $db->query($sql)) {
            if ($row = $result->fetch()) {
                $next_title = _('Customers').' '.$row['Store Code'];
            } else {
                $next_title = '';
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }


        $left_buttons[] = array(
            'icon'      => 'arrow-left',
            'title'     => $prev_title,
            'reference' => 'customers/'.$prev_key
        );
        $left_buttons[] = array(
            'icon'      => 'arrow-up',
            'title'     => _('Customers (All stores)'),
            'reference' => 'customers/all',
            'parent'    => ''
        );

        $left_buttons[] = array(
            'icon'      => 'arrow-right',
            'title'     => $next_title,
            'reference' => 'customers/'.$next_key
        );
    }


    $right_buttons = array();
    $sections      = get_sections('customers', $data['store']->id);

    if (isset($sections[$data['section']])) {
        $sections[$data['section']]['selected'] = true;
    }


    $_content = array(

        'sections_class' => '',
        'sections'       => $sections,

        'left_buttons'  => $left_buttons,
        'right_buttons' => $right_buttons,
        'title'         => _('Customers').' <span class="id">'.$data['store']->get('Store Code').'</span>',
        'search'        => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);

    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_customers_list_navigation($data, $smarty, $user, $db) {


    require_once 'class.Store.php';

    include_once 'class.List.php';


    $list  = new SubjectList($data['key']);
    $store = new Store($list->get('List Parent Key'));


    $block_view = $data['section'];


    $left_buttons  = array();
    $right_buttons = array();


    $tab = 'customers.lists';


    if (isset($_SESSION['table_state'][$tab])) {
        $number_results  = $_SESSION['table_state'][$tab]['nr'];
        $start_from      = 0;
        $order           = $_SESSION['table_state'][$tab]['o'];
        $order_direction = ($_SESSION['table_state'][$tab]['od'] == 1 ? 'desc' : '');
        $f_value         = $_SESSION['table_state'][$tab]['f_value'];
        $parameters      = $_SESSION['table_state'][$tab];
    } else {

        $default                  = $user->get_tab_defaults($tab);
        $number_results           = $default['rpp'];
        $start_from               = 0;
        $order                    = $default['sort_key'];
        $order_direction          = ($default['sort_order'] == 1 ? 'desc' : '');
        $f_value                  = '';
        $parameters               = $default;
        $parameters['parent']     = $data['parent'];
        $parameters['parent_key'] = $data['parent_key'];
    }

    include_once 'prepare_table/'.$tab.'.ptble.php';

    $_order_field       = $order;
    $order              = preg_replace('/^.*\.`/', '', $order);
    $order              = preg_replace('/^`/', '', $order);
    $order              = preg_replace('/`$/', '', $order);
    $_order_field_value = $list->get($order);


    $prev_title = '';
    $next_title = '';
    $prev_key   = 0;
    $next_key   = 0;
    $sql        = trim($sql_totals." $wheref");


    if ($result2 = $db->query($sql)) {
        if ($row2 = $result2->fetch() and $row2['num'] > 1) {


            $sql = sprintf(
                "select `List Name` object_name,`List Key` as object_key from $table   $where $wheref
	                and ($_order_field < %s OR ($_order_field = %s AND `List Key` < %d))  order by $_order_field desc , `List Key` desc limit 1",

                prepare_mysql($_order_field_value), prepare_mysql($_order_field_value), $list->id
            );

            if ($result = $db->query($sql)) {
                if ($row = $result->fetch()) {
                    $prev_key   = $row['object_key'];
                    $prev_title = _("List").' '.$row['object_name'].' ('.$row['object_key'].')';

                }
            } else {
                print_r($error_info = $db->errorInfo());
                exit;
            }


            $sql = sprintf(
                "select `List Name` object_name,`List Key` as object_key from $table   $where $wheref
	                and ($_order_field  > %s OR ($_order_field  = %s AND `List Key` > %d))  order by $_order_field   , `List Key`  limit 1", prepare_mysql($_order_field_value),
                prepare_mysql($_order_field_value), $list->id
            );
            if ($result = $db->query($sql)) {
                if ($row = $result->fetch()) {
                    $next_key   = $row['object_key'];
                    $next_title = _("List").' '.$row['object_name'].' ('.$row['object_key'].')';

                }
            } else {
                print_r($error_info = $db->errorInfo());
                exit;
            }


            if ($order_direction == 'desc') {
                $_tmp1      = $prev_key;
                $_tmp2      = $prev_title;
                $prev_key   = $next_key;
                $prev_title = $next_title;
                $next_key   = $_tmp1;
                $next_title = $_tmp2;
            }


        }
    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }


    $up_button = array(
        'icon'      => 'arrow-up',
        'title'     => _(
                "Customer's lists"
            ).' '.$store->data['Store Code'],
        'reference' => 'customers/'.$store->id.'/lists'
    );

    if ($prev_key) {
        $left_buttons[] = array(
            'icon'      => 'arrow-left',
            'title'     => $prev_title,
            'reference' => 'customers/list/'.$prev_key
        );

    } else {
        $left_buttons[] = array(
            'icon'  => 'arrow-left disabled',
            'title' => '',
            'url'   => ''
        );

    }
    $left_buttons[] = $up_button;


    if ($next_key) {
        $left_buttons[] = array(
            'icon'      => 'arrow-right',
            'title'     => $next_title,
            'reference' => 'customers/list/'.$next_key
        );

    } else {
        $left_buttons[] = array(
            'icon'  => 'arrow-right disabled',
            'title' => '',
            'url'   => ''
        );

    }


    $right_buttons = array();
    $sections      = get_sections('customers', $store->id);

    $sections['lists']['selected'] = true;


    $_content = array(

        'sections_class' => '',
        'sections'       => $sections,

        'left_buttons'  => $left_buttons,
        'right_buttons' => $right_buttons,
        'title'         => _("Customer's List").' <span class="id">'.$list->get(
                'List Name'
            ).'</span>',
        'search'        => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);

    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_customers_categories_navigation($data, $smarty, $user, $db) {


    require_once 'class.Store.php';

    switch ($data['parent']) {
        case 'store':
            $store = new Store($data['parent_key']);
            break;
        default:

            break;
    }

    $block_view = $data['section'];


    $left_buttons = array();
    if ($user->stores > 1) {


        list($prev_key, $next_key) = get_prev_next($store->id, $user->stores);

        $sql = sprintf(
            "SELECT `Store Code` FROM `Store Dimension` WHERE `Store Key`=%d", $prev_key
        );
        if ($result = $db->query($sql)) {
            if ($row = $result->fetch()) {
                $prev_title = _("Customer's Categories").' '.$row['Store Code'];
            } else {
                $prev_title = '';
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }


        $sql = sprintf(
            "SELECT `Store Code` FROM `Store Dimension` WHERE `Store Key`=%d", $next_key
        );
        if ($result = $db->query($sql)) {
            if ($row = $result->fetch()) {
                $next_title = _("Customer's Categories").' '.$row['Store Code'];
            } else {
                $next_title = '';
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }


        $left_buttons[] = array(
            'icon'      => 'arrow-left',
            'title'     => $prev_title,
            'reference' => 'customers/categories/'.$prev_key
        );
        //$left_buttons[]=array('icon'=>'arrow-up','title'=>_('Customers').' '.$store->data['Store Code'],'reference'=>'customers/'.$store->id);

        $left_buttons[] = array(
            'icon'      => 'arrow-right',
            'title'     => $next_title,
            'reference' => 'customers/categories/'.$next_key
        );
    }


    $right_buttons = array();

    $right_buttons[] = array(
        'icon'  => 'edit',
        'title' => _('Edit'),
        'url'   => "edit_customer_categories.php?store_id=".$store->id
    );

    $sections = get_sections('customers', $store->id);
    if (isset($sections[$data['section']])) {
        $sections[$data['section']]['selected'] = true;
    }

    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => _("Customer's Categories").' <span class="id">'.$store->get('Store Code').'</span>',
        'search'         => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);
    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_customers_category_navigation($data, $smarty, $user, $db) {


    require_once 'class.Category.php';
    require_once 'class.Store.php';


    $category = new Category($data['key']);

    $left_buttons  = array();
    $right_buttons = array();

    switch ($data['parent']) {
        case 'category':

            $parent_category = new Category($data['parent_key']);
            $store           = new Store(
                $data['_object']->get('Category Store Key')
            );
            break;
        case 'store':
            $store = new Store($data['parent_key']);

            $left_buttons[] = array(
                'icon'      => 'arrow-up',
                'title'     => _("Customer's Categories").' '.$store->data['Store Code'],
                'reference' => 'customers/'.$store->id.'/categories'
            );


            break;

        default:

            break;
    }


    $title = ' <span class="Category_Label">'.$data['_object']->get('Label').'</span> (<span class="Category_Code id">'.$data['_object']->get(
            'Code'
        ).'</span>)';


    //$right_buttons[]=array('icon'=>'edit', 'title'=>_('Edit'), 'url'=>"edit_customer_categories.php?store_id=".$store->id);

    $sections                           = get_sections('customers', $store->id);
    $sections['categories']['selected'] = true;

    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => $title,
        'search'         => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);
    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_customers_lists_navigation($data, $smarty, $user, $db) {


    require_once 'class.Store.php';

    switch ($data['parent']) {
        case 'store':
            $store = new Store($data['parent_key']);
            break;
        default:

            break;
    }



    $left_buttons = array();
    if ($user->stores > 1) {


        list($prev_key, $next_key) = get_prev_next($store->id, $user->stores);

        $sql = sprintf(
            "SELECT `Store Code` FROM `Store Dimension` WHERE `Store Key`=%d", $prev_key
        );

        if ($result = $db->query($sql)) {
            if ($row = $result->fetch()) {
                $prev_title = _("Customer's Lists").' '.$row['Store Code'];
            } else {
                $prev_title = '';
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }


        $sql = sprintf(
            "SELECT `Store Code` FROM `Store Dimension` WHERE `Store Key`=%d", $next_key
        );

        if ($result = $db->query($sql)) {
            if ($row = $result->fetch()) {
                $next_title = _("Customer's Lists").' '.$row['Store Code'];
            } else {
                $next_title = '';
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }


        $left_buttons[] = array(
            'icon'      => 'arrow-left',
            'title'     => $prev_title,
            'reference' => 'customers/'.$prev_key.'/lists'
        );
        //$left_buttons[]=array('icon'=>'arrow-up','title'=>_('Customers').' '.$store->data['Store Code'],'reference'=>'customers/'.$store->id);

        $left_buttons[] = array(
            'icon'      => 'arrow-right',
            'title'     => $next_title,
            'reference' => 'customers/'.$next_key.'/lists'
        );
    }


    $right_buttons = array();


    $sections = get_sections('customers', $store->id);
    if (isset($sections[$data['section']])) {
        $sections[$data['section']]['selected'] = true;
    }


    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => _("Customer's Lists").' <span class="id">'.$store->get('Store Code').'</span>',
        'search'         => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);

    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_customers_dashboard_navigation($data, $smarty, $user, $db) {


    require_once 'class.Store.php';

    switch ($data['parent']) {
        case 'store':
            $store = new Store($data['parent_key']);
            break;
        default:

            break;
    }

    $block_view = $data['section'];


    $left_buttons = array();
    if ($user->stores > 1) {


        list($prev_key, $next_key) = get_prev_next($store->id, $user->stores);

        $sql = sprintf(
            "SELECT `Store Code` FROM `Store Dimension` WHERE `Store Key`=%d", $prev_key
        );
        if ($result = $db->query($sql)) {
            if ($row = $result->fetch()) {
                $prev_title = _("Customer's Dashboard").' '.$row['Store Code'];
            } else {
                $prev_title = '';
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }


        $sql = sprintf(
            "SELECT `Store Code` FROM `Store Dimension` WHERE `Store Key`=%d", $next_key
        );
        if ($result = $db->query($sql)) {
            if ($row = $result->fetch()) {
                $next_title = _("Customer's Dashboard").' '.$row['Store Code'];
            } else {
                $next_title = '';
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }

        $left_buttons[] = array(
            'icon'      => 'arrow-left',
            'title'     => $prev_title,
            'reference' => 'customers/dashboard/'.$prev_key
        );
        //$left_buttons[]=array('icon'=>'arrow-up','title'=>_('Customers').' '.$store->data['Store Code'],'reference'=>'customers/'.$store->id);

        $left_buttons[] = array(
            'icon'      => 'arrow-right',
            'title'     => $next_title,
            'reference' => 'customers/dashboard/'.$next_key
        );
    }


    $right_buttons = array();


    $sections = get_sections('customers', $store->id);
    if (isset($sections[$data['section']])) {
        $sections[$data['section']]['selected'] = true;
    }

    $_content = array(

        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => _("Customer's Dashboard").' <span class="id">'.$store->get('Store Code').'</span>',
        'search'         => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);


    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_customers_insights_navigation($data, $smarty, $user, $db) {


    require_once 'class.Store.php';

    switch ($data['parent']) {
        case 'store':
            $store = new Store($data['parent_key']);
            break;
        default:

            break;
    }

    $block_view = $data['section'];


    $left_buttons = array();
    if ($user->stores > 1) {


        list($prev_key, $next_key) = get_prev_next($store->id, $user->stores);

        $sql = sprintf(
            "SELECT `Store Code` FROM `Store Dimension` WHERE `Store Key`=%d", $prev_key
        );
        if ($result = $db->query($sql)) {
            if ($row = $result->fetch()) {
                $prev_title = _("Customer's insights").' '.$row['Store Code'];
            } else {
                $prev_title = '';
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }

        $sql = sprintf(
            "SELECT `Store Code` FROM `Store Dimension` WHERE `Store Key`=%d", $next_key
        );
        if ($result = $db->query($sql)) {
            if ($row = $result->fetch()) {
                $next_title = _("Customer's insights").' '.$row['Store Code'];
            } else {
                $next_title = '';
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }


        $left_buttons[] = array(
            'icon'      => 'arrow-left',
            'title'     => $prev_title,
            'reference' => 'customers/'.$prev_key.'/insights/'
        );
        //$left_buttons[]=array('icon'=>'arrow-up','title'=>_('Customers').' '.$store->data['Store Code'],'reference'=>'customers/'.$store->id);

        $left_buttons[] = array(
            'icon'      => 'arrow-right',
            'title'     => $next_title,
            'reference' => 'customers/'.$next_key.'/insights/'
        );
    }


    $right_buttons = array();


    $sections = get_sections('customers', $store->id);
    if (isset($sections[$data['section']])) {
        $sections[$data['section']]['selected'] = true;
    }

    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => _("Customer's insights").' <span class="id">'.$store->get('Store Code').'</span>',
        'search'         => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);


    $html = $smarty->fetch('navigation.tpl');

    return $html;

}



function get_customers_email_campaigns_navigation($data, $smarty, $user, $db) {


    require_once 'class.Store.php';

    switch ($data['parent']) {
        case 'store':
            $store = new Store($data['parent_key']);
            break;
        default:

            break;
    }



    $left_buttons = array();
    if ($user->stores > 1) {


        list($prev_key, $next_key) = get_prev_next($store->id, $user->stores);

        $sql = sprintf(
            "SELECT `Store Code` FROM `Store Dimension` WHERE `Store Key`=%d", $prev_key
        );
        if ($result = $db->query($sql)) {
            if ($row = $result->fetch()) {
                $prev_title = _("Email campaigns").' '.$row['Store Code'];
            } else {
                $prev_title = '';
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }

        $sql = sprintf(
            "SELECT `Store Code` FROM `Store Dimension` WHERE `Store Key`=%d", $next_key
        );
        if ($result = $db->query($sql)) {
            if ($row = $result->fetch()) {
                $next_title = _("Email campaigns").' '.$row['Store Code'];
            } else {
                $next_title = '';
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }


        $left_buttons[] = array(
            'icon'      => 'arrow-left',
            'title'     => $prev_title,
            'reference' => 'customers/'.$prev_key.'/email_campaigns/'
        );
        //$left_buttons[]=array('icon'=>'arrow-up','title'=>_('Customers').' '.$store->data['Store Code'],'reference'=>'customers/'.$store->id);

        $left_buttons[] = array(
            'icon'      => 'arrow-right',
            'title'     => $next_title,
            'reference' => 'customers/'.$next_key.'/email_campaigns/'
        );
    }


    $right_buttons = array();


    $sections = get_sections('customers', $store->id);
    if (isset($sections[$data['section']])) {
        $sections[$data['section']]['selected'] = true;
    }

    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => _("Email campaigns").' <span class="id">'.$store->get('Store Code').'</span>',
        'search'         => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);


    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_customers_server_navigation($data, $smarty, $user, $db) {


    require_once 'class.Store.php';

    $branch = array(array('label'     => '',
                          'icon'      => 'home',
                          'reference' => ''
                    )
    );


    $left_buttons = array();

    if ($data['section'] == 'customers') {
        $title = _('Customers (All stores)');
    } else {
        $title = _('Pending orders (All stores)');
    }

    $right_buttons = array();
    $sections      = get_sections('customers_server');
    if (isset($sections[$data['section']])) {
        $sections[$data['section']]['selected'] = true;
    }

    $_content = array(
        'branch'         => $branch,
        'sections_class' => '',
        'sections'       => $sections,

        'left_buttons'  => $left_buttons,
        'right_buttons' => $right_buttons,
        'title'         => $title,
        'search'        => array(
            'show'        => true,
            'placeholder' => _(
                'Search customers all stores'
            )
        )

    );
    $smarty->assign('content', $_content);
    $smarty->assign('_content', $_content);

    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_customer_navigation($data, $smarty, $user, $db) {


    $customer = $data['_object'];

    if (!$customer->id) {
        return;
    }



    $block_view = $data['section'];


    $left_buttons  = array();
    $right_buttons = array();

    if ($data['parent']) {

        switch ($data['parent']) {
            case 'store':
                $tab      = 'customers';
                $_section = 'customers';
                break;
            case 'category':
                $tab      = 'customer.categories';
                $_section = 'categories';
                break;
            case 'list':
                $tab      = 'customers.list';
                $_section = 'lists';
                break;
            case 'campaign':
                $tab      = 'campaign.customers';
                $_section = 'marketing';
                break;
            case 'deal':
                $tab      = 'deal.customers';
                $_section = 'marketing';
                break;
        }


        if (isset($_SESSION['table_state'][$tab])) {
            $number_results  = $_SESSION['table_state'][$tab]['nr'];
            $start_from      = 0;
            $order           = $_SESSION['table_state'][$tab]['o'];
            $order_direction = ($_SESSION['table_state'][$tab]['od'] == 1 ? 'desc' : '');
            $f_value         = $_SESSION['table_state'][$tab]['f_value'];
            $parameters      = $_SESSION['table_state'][$tab];
        } else {

            $default                  = $user->get_tab_defaults($tab);
            $number_results           = $default['rpp'];
            $start_from               = 0;
            $order                    = $default['sort_key'];
            $order_direction          = ($default['sort_order'] == 1 ? 'desc' : '');
            $f_value                  = '';
            $parameters               = $default;
            $parameters['parent']     = $data['parent'];
            $parameters['parent_key'] = $data['parent_key'];
        }

        include_once 'prepare_table/'.$tab.'.ptble.php';

        $_order_field       = $order;
        $order              = preg_replace('/^.*\.`/', '', $order);
        $order              = preg_replace('/^`/', '', $order);
        $order              = preg_replace('/`$/', '', $order);
        $_order_field_value = $customer->get($order);


        $prev_title = '';
        $next_title = '';
        $prev_key   = 0;
        $next_key   = 0;
        $sql        = trim($sql_totals." $wheref");

        if ($result2 = $db->query($sql)) {
            if ($row2 = $result2->fetch() and $row2['num'] > 1) {


                $sql = sprintf(
                    "select `Customer Name` object_name,C.`Customer Key` as object_key from $table   $where $wheref
	                and ($_order_field < %s OR ($_order_field = %s AND C.`Customer Key` < %d))  order by $_order_field desc , C.`Customer Key` desc limit 1",

                    prepare_mysql($_order_field_value), prepare_mysql($_order_field_value), $customer->id
                );

                if ($result = $db->query($sql)) {
                    if ($row = $result->fetch()) {
                        $prev_key   = $row['object_key'];
                        $prev_title = _("Customer").' '.$row['object_name'].' ('.$row['object_key'].')';

                    }
                } else {
                    print_r($error_info = $db->errorInfo());
                    exit;
                }


                $sql = sprintf(
                    "select `Customer Name` object_name,C.`Customer Key` as object_key from $table   $where $wheref
	                and ($_order_field  > %s OR ($_order_field  = %s AND C.`Customer Key` > %d))  order by $_order_field   , C.`Customer Key`  limit 1", prepare_mysql($_order_field_value),
                    prepare_mysql($_order_field_value), $customer->id
                );

                if ($result = $db->query($sql)) {
                    if ($row = $result->fetch()) {
                        $next_key   = $row['object_key'];
                        $next_title = _("Customer").' '.$row['object_name'].' ('.$row['object_key'].')';

                    }
                } else {
                    print_r($error_info = $db->errorInfo());
                    exit;
                }


                if ($order_direction == 'desc') {
                    $_tmp1      = $prev_key;
                    $_tmp2      = $prev_title;
                    $prev_key   = $next_key;
                    $prev_title = $next_title;
                    $next_key   = $_tmp1;
                    $next_title = $_tmp2;
                }


            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }


        $placeholder=_('Search customers');
        $sections = get_sections('customers', $customer->data['Customer Store Key']);


        if ($data['parent'] == 'list') {

            include_once 'class.List.php';
            $list = new SubjectList($data['parent_key']);

            $up_button = array(
                'icon'      => 'arrow-up',
                'title'     => _("List").' '.$list->data['List Name'],
                'reference' => 'customers/list/'.$list->id
            );

            if ($prev_key) {
                $left_buttons[] = array(
                    'icon'      => 'arrow-left',
                    'title'     => $prev_title,
                    'reference' => 'customers/list/'.$data['parent_key'].'/'.$prev_key
                );

            } else {
                $left_buttons[] = array(
                    'icon'  => 'arrow-left disabled',
                    'title' => '',
                    'url'   => ''
                );

            }
            $left_buttons[] = $up_button;


            if ($next_key) {
                $left_buttons[] = array(
                    'icon'      => 'arrow-right',
                    'title'     => $next_title,
                    'reference' => 'customers/list/'.$data['parent_key'].'/'.$next_key
                );

            } else {
                $left_buttons[] = array(
                    'icon'  => 'arrow-right disabled',
                    'title' => '',
                    'url'   => ''
                );

            }


        }
        elseif ($data['parent'] == 'category') {


            include_once 'class.Category.php';
            $category = new Category($data['parent_key']);


            $category_keys = preg_split(
                '/\>/', preg_replace('/\>$/', '', $category->data['Category Position'])
            );
            array_pop($category_keys);
            if (count($category_keys) > 0) {
                $sql = sprintf(
                    "SELECT `Category Code`,`Category Key` FROM `Category Dimension` WHERE `Category Key` IN (%s)", join(',', $category_keys)
                );

                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {
                        $branch[] = array(
                            'label' => $row['Category Code'],
                            'icon'  => '',
                            'url'   => 'customer_category.php?id='.$row['Category Key']
                        );

                    }
                } else {
                    print_r($error_info = $db->errorInfo());
                    exit;
                }


            }


            $up_button = array(
                'icon'  => 'arrow-up',
                'title' => _(
                        "Category"
                    ).' '.$category->data['Category Code'],
                'url'   => 'customer_category.php?id='.$category->id
            );

        }
        elseif ($data['parent'] == 'campaign') {


            $up_button = array(
                'icon'      => 'arrow-up',
                'title'     => _("Campaign").' '.$data['_parent']->get('Name'),
                'reference' => 'campaigns/'.$store->id.'/'.$data['_parent']->id
            );

            if ($prev_key) {
                $left_buttons[] = array(
                    'icon'      => 'arrow-left',
                    'title'     => $prev_title,
                    'reference' => 'campaign/'.$data['parent_key'].'/customer/'.$prev_key
                );

            } else {
                $left_buttons[] = array(
                    'icon'  => 'arrow-left disabled',
                    'title' => '',
                    'url'   => ''
                );

            }
            $left_buttons[] = $up_button;


            if ($next_key) {
                $left_buttons[] = array(
                    'icon'      => 'arrow-right',
                    'title'     => $next_title,
                    'reference' => 'campaign/'.$data['parent_key'].'/customer/'.$next_key
                );

            } else {
                $left_buttons[] = array(
                    'icon'  => 'arrow-right disabled',
                    'title' => '',
                    'url'   => ''
                );

            }
            $sections = get_sections('products',$store->id);

            $placeholder=_('Search marketing');


        }
        elseif ($data['parent'] == 'deal') {


            $up_button = array(
                'icon'      => 'arrow-up',
                'title'     => _("Deal").' '.$data['_parent']->get('Name'),
                'reference' => 'campaigns/'.$store->id.'/'.$data['_parent']->get('Deal Campaign Key').'/deal/'.$data['_parent']->id
            );

            if ($prev_key) {
                $left_buttons[] = array(
                    'icon'      => 'arrow-left',
                    'title'     => $prev_title,
                    'reference' => 'deal/'.$data['parent_key'].'/customer/'.$prev_key
                );

            } else {
                $left_buttons[] = array(
                    'icon'  => 'arrow-left disabled',
                    'title' => '',
                    'url'   => ''
                );

            }
            $left_buttons[] = $up_button;


            if ($next_key) {
                $left_buttons[] = array(
                    'icon'      => 'arrow-right',
                    'title'     => $next_title,
                    'reference' => 'deal/'.$data['parent_key'].'/customer/'.$next_key
                );

            } else {
                $left_buttons[] = array(
                    'icon'  => 'arrow-right disabled',
                    'title' => '',
                    'url'   => ''
                );

            }
            $sections = get_sections('products',$store->id);

            $placeholder=_('Search marketing');


        }
        else {

            $up_button = array(
                'icon'      => 'arrow-up',
                'title'     => _("Customers").' '.$store->data['Store Code'],
                'reference' => 'customers/'.$store->id
            );

            if ($prev_key) {
                $left_buttons[] = array(
                    'icon'      => 'arrow-left',
                    'title'     => $prev_title,
                    'reference' => 'customers/'.$data['parent_key'].'/'.$prev_key
                );

            } else {
                $left_buttons[] = array(
                    'icon'  => 'arrow-left disabled',
                    'title' => '',
                    'url'   => ''
                );

            }
            $left_buttons[] = $up_button;


            if ($next_key) {
                $left_buttons[] = array(
                    'icon'      => 'arrow-right',
                    'title'     => $next_title,
                    'reference' => 'customers/'.$data['parent_key'].'/'.$next_key
                );

            } else {
                $left_buttons[] = array(
                    'icon'  => 'arrow-right disabled',
                    'title' => '',
                    'url'   => ''
                );

            }


        }


    } else {
        $_section = 'customers';

    }
    //$right_buttons[]=array('icon'=>'edit', 'title'=>_('Edit customer'), 'url'=>'edit_customer.php?id='.$customer->id);
    //$right_buttons[]=array('icon'=>'sticky-note-o', 'title'=>_('History note'), 'id'=>'note');
    //$right_buttons[]=array('icon'=>'paperclip', 'title'=>_('Attachement'), 'id'=>'attach');
    $right_buttons[] = array(
        'icon'  => 'shopping-cart',
        'title' => _('New order'),
        'id'    => 'take_order'
    );
    $right_buttons[] = array(
        'icon'  => 'sticky-note',
        'title' => _('Sticky note'),
        'id'    => 'sticky_note_button',
        'class' => ($customer->get('Sticky Note') == '' ? '' : 'hide')
    );



    if (isset($sections[$_section])) {
        $sections[$_section]['selected'] = true;
    }


    //  {if $customer->get_image_src()} <img id="avatar" src="{$customer->get_image_src()}" style="cursor:pointer;border:1px solid #eee;height:45px;max-width:100px"> {else} <img id="avatar" src="/art/avatar.jpg" style="cursor:pointer;"> {/if} {if $customer->get('Customer Level Type')=='VIP'}<img src="/art/icons/shield.png" style="position:absolute;xtop:-36px;left:40px">{/if} {if $customer->get('Customer Level Type')=='Partner'}<img src="/art/icons/group.png" style="position:absolute;xtop:-36px;left:40px">{/if}
    $avatar = '<div class="square_button"></div>';
    $avatar
            = '<div class="square_button left"><img id="avatar" style="height:100%" src="/art/avatar.jpg" style="cursor:pointer;"> </div> ';
    $avatar = '';

    $title = '<span class="id"><span class="Customer_Name">'.$customer->get('Customer Name').'</span> ('.$customer->get_formatted_id().')</span>';


    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'avatar'         => $avatar,
        'title'          => $title,
        'search'         => array(
            'show'        => true,
            'placeholder' => $placeholder
        )

    );
    $smarty->assign('_content', $_content);


    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_new_customer_navigation($data, $smarty, $user, $db) {


    $left_buttons  = array();
    $right_buttons = array();


    $sections = get_sections('customers', $data['parent_key']);

    $_section = 'customers';
    if (isset($sections[$_section])) {
        $sections[$_section]['selected'] = true;
    }

    $up_button = array(
        'icon'      => 'arrow-up',
        'title'     => _("Customers"),
        'reference' => 'customers/'.$data['parent_key']
    );


    $left_buttons[] = $up_button;


    $title = _('New Customer').' ('._('Store').' <span class="id">'.$data['store']->get('Code').'</span>)';


    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => $title,
        'search'         => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);


    $html = $smarty->fetch('navigation.tpl');

    return $html;

}



function get_new_list_navigation($data, $smarty, $user, $db) {


    $left_buttons  = array();
    $right_buttons = array();


    $sections = get_sections('customers', $data['parent_key']);

    $_section = 'lists';
    if (isset($sections[$_section])) {
        $sections[$_section]['selected'] = true;
    }

    $up_button = array(
        'icon'      => 'arrow-up',
        'title'     => _("Customers lists"),
        'reference' => 'customers/'.$data['parent_key'].'/lists'
    );


    $left_buttons[] = $up_button;


    $title = _('New Customers list').' ('._('Store').' <span class="id">'.$data['store']->get('Code').'</span>)';


    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => $title,
        'search'         => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);


    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_customers_new_poll_query_navigation($data, $smarty, $user, $db) {


    $left_buttons  = array();
    $right_buttons = array();


    $store=get_object('Store',$data['parent_key']);

    $sections = get_sections('customers', $data['parent_key']);

    $_section = 'insights';
    if (isset($sections[$_section])) {
        $sections[$_section]['selected'] = true;
    }



    $up_button = array(
        'icon'      => 'arrow-up',
        'title'     => sprintf(_("Customer's insights %s"),$store->get('Code')),
        'reference' => 'customers/'.$store->id.'/insights'
    );


    $left_buttons[] = $up_button;


    $title = _('New poll query').' ('._('Store').' <span class="id">'.$data['store']->get('Code').'</span>)';


    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => $title,
        'search'         => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);


    $html = $smarty->fetch('navigation.tpl');

    return $html;

}

function get_customers_poll_query_navigation($data, $smarty, $user, $db) {


    $poll_query = $data['_object'];

    $store= $data['store'];

    if (!$poll_query->id) {
        return;
    }





    $left_buttons  = array();
    $right_buttons = array();

    if ($data['parent']) {

        switch ($data['parent']) {
            case 'store':
                $tab      = 'customers_poll.queries';
                $_section = 'customers';
                break;

        }


        if (isset($_SESSION['table_state'][$tab])) {
            $number_results  = $_SESSION['table_state'][$tab]['nr'];
            $start_from      = 0;
            $order           = $_SESSION['table_state'][$tab]['o'];
            $order_direction = ($_SESSION['table_state'][$tab]['od'] == 1 ? 'desc' : '');
            $f_value         = $_SESSION['table_state'][$tab]['f_value'];
            $parameters      = $_SESSION['table_state'][$tab];
        } else {

            $default                  = $user->get_tab_defaults($tab);
            $number_results           = $default['rpp'];
            $start_from               = 0;
            $order                    = $default['sort_key'];
            $order_direction          = ($default['sort_order'] == 1 ? 'desc' : '');
            $f_value                  = '';
            $parameters               = $default;
            $parameters['parent']     = $data['parent'];
            $parameters['parent_key'] = $data['parent_key'];
        }

        include_once 'prepare_table/'.$tab.'.ptble.php';

        $_order_field       = $order;
        $order              = preg_replace('/^.*\.`/', '', $order);
        $order              = preg_replace('/^`/', '', $order);
        $order              = preg_replace('/`$/', '', $order);
        $_order_field_value = $poll_query->get($order);


        $prev_title = '';
        $next_title = '';
        $prev_key   = 0;
        $next_key   = 0;
        $sql        = trim($sql_totals." $wheref");

        if ($result2 = $db->query($sql)) {
            if ($row2 = $result2->fetch() and $row2['num'] > 1) {


                $sql = sprintf(
                    "select `Customer Poll Query Name` object_name,`Customer Poll Query Key` as object_key from $table   $where $wheref
	                and ($_order_field < %s OR ($_order_field = %s AND `Customer Poll Query Key` < %d))  order by $_order_field desc , `Customer Poll Query Key` desc limit 1",

                    prepare_mysql($_order_field_value), prepare_mysql($_order_field_value), $poll_query->id
                );



                if ($result = $db->query($sql)) {
                    if ($row = $result->fetch()) {
                        $prev_key   = $row['object_key'];
                        $prev_title = _("Poll query").' '.$row['object_name'];

                    }
                } else {
                    print_r($error_info = $db->errorInfo());
                    exit;
                }


                $sql = sprintf(
                    "select `Customer Poll Query Name` object_name,`Customer Poll Query Key` as object_key from $table   $where $wheref
	                and ($_order_field  > %s OR ($_order_field  = %s AND `Customer Poll Query Key` > %d))  order by $_order_field   , `Customer Poll Query Key`  limit 1", prepare_mysql($_order_field_value),
                    prepare_mysql($_order_field_value), $poll_query->id
                );

                if ($result = $db->query($sql)) {
                    if ($row = $result->fetch()) {
                        $next_key   = $row['object_key'];
                        $next_title = _("Poll query").' '.$row['object_name'];

                    }
                } else {
                    print_r($error_info = $db->errorInfo());
                    exit;
                }


                if ($order_direction == 'desc') {
                    $_tmp1      = $prev_key;
                    $_tmp2      = $prev_title;
                    $prev_key   = $next_key;
                    $prev_title = $next_title;
                    $next_key   = $_tmp1;
                    $next_title = $_tmp2;
                }


            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }


        $placeholder=_('Search customers');
        $sections = get_sections('customers', $poll_query->get('Customer Poll Query Store Key'));



            $up_button = array(
                'icon'      => 'arrow-up',
                'title'     => sprintf(_("Customer's insights %s"),$store->get('Code')),
                'reference' => 'customers/'.$store->id.'/insights'
            );

            if ($prev_key) {
                $left_buttons[] = array(
                    'icon'      => 'arrow-left',
                    'title'     => $prev_title,
                    'reference' => 'customers/'.$store->id.'/poll_query/'.$prev_key
                );

            } else {
                $left_buttons[] = array(
                    'icon'  => 'arrow-left disabled',
                    'title' => '',
                    'url'   => ''
                );

            }
            $left_buttons[] = $up_button;


            if ($next_key) {
                $left_buttons[] = array(
                    'icon'      => 'arrow-right',
                    'title'     => $next_title,
                    'reference' => 'customers/'.$store->id.'/poll_query/'.$next_key
                );

            } else {
                $left_buttons[] = array(
                    'icon'  => 'arrow-right disabled',
                    'title' => '',
                    'url'   => ''
                );

            }





    } else {
        $_section = 'customers';

    }


    $sections['insights']['selected'] = true;


    $title = '<span class="id"><span class="Customer_Poll_Query_Name">'.$poll_query->get('Name').'</span></span>';


    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => $title,
        'search'         => array(
            'show'        => true,
            'placeholder' => $placeholder
        )

    );
    $smarty->assign('_content', $_content);


    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_customers_new_poll_query_option_navigation($data, $smarty, $user, $db) {


    $left_buttons  = array();
    $right_buttons = array();


    $sections = get_sections('customers', $data['_parent']->get('Store Key'));

    $_section = 'insights';
    if (isset($sections[$_section])) {
        $sections[$_section]['selected'] = true;
    }

    $up_button = array(
        'icon'      => 'arrow-up',
        'title'     => sprintf(_('Poll query %s'),$data['_parent']->get('Name')),
        'reference' => 'customers/'.$data['_parent']->get('Store Key').'/poll_query/'.$data['_parent']->id
    );


    $left_buttons[] = $up_button;


    $title = _('New option').' ('._('Query').' <span class="id">'.$data['_parent']->get('Name').'</span>)';


    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => $title,
        'search'         => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);


    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_customers_deleted_poll_query_option_navigation($data, $smarty, $user, $db) {


    $left_buttons  = array();
    $right_buttons = array();


    $sections = get_sections('customers', $data['_parent']->get('Store Key'));

    $_section = 'insights';
    if (isset($sections[$_section])) {
        $sections[$_section]['selected'] = true;
    }

    $up_button = array(
        'icon'      => 'arrow-up',
        'title'     => sprintf(_('Poll query %s'),$data['_parent']->get('Name')),
        'reference' => 'customers/'.$data['_parent']->get('Store Key').'/poll_query/'.$data['_parent']->id
    );


    $left_buttons[] = $up_button;


    $title ='<span class="error">'.sprintf(_('Deleted option: %s'),'<span class="id">'.$data['_parent']->get('Name').'</span>').'</span>';


    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => $title,
        'search'         => array(
            'show'        => true,
            'placeholder' => _('Search customers')
        )

    );
    $smarty->assign('_content', $_content);


    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_customers_poll_query_option_navigation($data, $smarty, $user, $db) {


    $poll_option = $data['_object'];

    $store= $data['store'];

    if (!$poll_option->id) {
        return;
    }





    $left_buttons  = array();
    $right_buttons = array();


    if ($data['parent']) {

        switch ($data['parent']) {
            case 'customer_poll_query':
                $tab      = 'customers_poll.query.options';
             
                break;

        }


        if (isset($_SESSION['table_state'][$tab])) {
            $number_results  = $_SESSION['table_state'][$tab]['nr'];
            $start_from      = 0;
            $order           = $_SESSION['table_state'][$tab]['o'];
            $order_direction = ($_SESSION['table_state'][$tab]['od'] == 1 ? 'desc' : '');
            $f_value         = $_SESSION['table_state'][$tab]['f_value'];
            $parameters      = $_SESSION['table_state'][$tab];
        } else {

            $default                  = $user->get_tab_defaults($tab);
            $number_results           = $default['rpp'];
            $start_from               = 0;
            $order                    = $default['sort_key'];
            $order_direction          = ($default['sort_order'] == 1 ? 'desc' : '');
            $f_value                  = '';
            $parameters               = $default;
            $parameters['parent']     = $data['parent'];
            $parameters['parent_key'] = $data['parent_key'];
        }

        include_once 'prepare_table/'.$tab.'.ptble.php';

        $_order_field       = $order;
        $order              = preg_replace('/^.*\.`/', '', $order);
        $order              = preg_replace('/^`/', '', $order);
        $order              = preg_replace('/`$/', '', $order);
        $_order_field_value = $poll_option->get($order);


        $prev_title = '';
        $next_title = '';
        $prev_key   = 0;
        $next_key   = 0;
        $sql        = trim($sql_totals." $wheref");

        if ($result2 = $db->query($sql)) {
            if ($row2 = $result2->fetch() and $row2['num'] > 1) {


                $sql = sprintf(
                    "select `Customer Poll Query Option Name` object_name,`Customer Poll Query Option Key` as object_key from $table   $where $wheref
	                and ($_order_field < %s OR ($_order_field = %s AND `Customer Poll Query Option Key` < %d))  order by $_order_field desc , `Customer Poll Query Option Key` desc limit 1",

                    prepare_mysql($_order_field_value), prepare_mysql($_order_field_value), $poll_option->id
                );



                if ($result = $db->query($sql)) {
                    if ($row = $result->fetch()) {
                        $prev_key   = $row['object_key'];
                        $prev_title = _("Poll option").' '.$row['object_name'];

                    }
                } else {
                    print_r($error_info = $db->errorInfo());
                    exit;
                }


                $sql = sprintf(
                    "select `Customer Poll Query Option Name` object_name,`Customer Poll Query Option Key` as object_key from $table   $where $wheref
	                and ($_order_field  > %s OR ($_order_field  = %s AND `Customer Poll Query Option Key` > %d))  order by $_order_field   , `Customer Poll Query Option Key`  limit 1", prepare_mysql($_order_field_value),
                    prepare_mysql($_order_field_value), $poll_option->id
                );

                if ($result = $db->query($sql)) {
                    if ($row = $result->fetch()) {
                        $next_key   = $row['object_key'];
                        $next_title = _("Poll option").' '.$row['object_name'];

                    }
                } else {
                    print_r($error_info = $db->errorInfo());
                    exit;
                }


                if ($order_direction == 'desc') {
                    $_tmp1      = $prev_key;
                    $_tmp2      = $prev_title;
                    $prev_key   = $next_key;
                    $prev_title = $next_title;
                    $next_key   = $_tmp1;
                    $next_title = $_tmp2;
                }


            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }


        $placeholder=_('Search customers');
        $sections = get_sections('customers', $poll_option->get('Customer Poll Query OptionStore Key'));



        $up_button = array(
            'icon'      => 'arrow-up',
            'title'     => sprintf(_("Poll query %s"),$data['_parent']->get('Name')),
            'reference' => 'customers/'.$store->id.'/poll_query/'.$data['_parent']->id
        );

        if ($prev_key) {
            $left_buttons[] = array(
                'icon'      => 'arrow-left',
                'title'     => $prev_title,
                'reference' => 'customers/'.$store->id.'/poll_query/'.$data['_parent']->id.'/option/'.$prev_key
            );

        } else {
            $left_buttons[] = array(
                'icon'  => 'arrow-left disabled',
                'title' => '',
                'url'   => ''
            );

        }
        $left_buttons[] = $up_button;


        if ($next_key) {
            $left_buttons[] = array(
                'icon'      => 'arrow-right',
                'title'     => $next_title,
                'reference' => 'customers/'.$store->id.'/poll_query/'.$data['_parent']->id.'/option/'.$next_key
            );

        } else {
            $left_buttons[] = array(
                'icon'  => 'arrow-right disabled',
                'title' => '',
                'url'   => ''
            );

        }





    }


    $sections['insights']['selected'] = true;


    $title = sprintf(_('Poll option: %s'),'<span class="id"><span class="Customer_Poll_Query_Option_Name">'.$poll_option->get('Name').'</span></span>');  ;


    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => $title,
        'search'         => array(
            'show'        => true,
            'placeholder' => $placeholder
        )

    );
    $smarty->assign('_content', $_content);


    $html = $smarty->fetch('navigation.tpl');

    return $html;

}


function get_email_campaign_navigation($data, $smarty, $user, $db, $account) {




    $object        = $data['_object'];
    $left_buttons  = array();
    $right_buttons = array();



    $_section = 'email_campaigns';




        switch ($data['section']) {

            case 'newsletter':
                $tab      = 'email_campaigns.newsletters';
                break;


        }

        if (isset($_SESSION['table_state'][$tab])) {
            $number_results  = $_SESSION['table_state'][$tab]['nr'];
            $start_from      = 0;
            $order           = $_SESSION['table_state'][$tab]['o'];
            $order_direction = ($_SESSION['table_state'][$tab]['od'] == 1 ? 'desc' : '');
            $f_value         = $_SESSION['table_state'][$tab]['f_value'];
            $parameters      = $_SESSION['table_state'][$tab];
        } else {

            $default = $user->get_tab_defaults($tab);

            $number_results           = $default['rpp'];
            $start_from               = 0;
            $order                    = $default['sort_key'];
            $order_direction          = ($default['sort_order'] == 1 ? 'desc' : '');
            $f_value                  = '';
            $parameters               = $default;
            $parameters['parent']     = $data['parent'];
            $parameters['parent_key'] = $data['parent_key'];

        }


        include_once 'prepare_table/'.$tab.'.ptble.php';
        $_order_field       = $order;
        $order              = preg_replace('/^.*\.`/', '', $order);
        $order              = preg_replace('/^`/', '', $order);
        $order              = preg_replace('/`$/', '', $order);
        $_order_field_value = $object->get($order);


        $prev_title = '';
        $next_title = '';
        $prev_key   = 0;
        $next_key   = 0;
        $sql        = trim($sql_totals." $wheref");

        if ($result2 = $db->query($sql)) {
            if ($row2 = $result2->fetch()) {
                if ($row2['num'] > 1) {


                    $sql = sprintf(
                        "select `Email Campaign Name` object_name,`Email Campaign Key` as object_key from $table   $where $wheref
	                and ($_order_field < %s OR ($_order_field = %s AND `Email Campaign Key` < %d))  order by $_order_field desc , `Email Campaign Key` desc limit 1",

                        prepare_mysql($_order_field_value), prepare_mysql($_order_field_value), $object->id
                    );



                    if ($result = $db->query($sql)) {
                        if ($row = $result->fetch()) {
                            $prev_key   = $row['object_key'];
                            $prev_title = _("Mailshot").' '.$row['object_name'];

                        }
                    } else {
                        print $sql;

                        print_r($error_info = $db->errorInfo());
                        exit;
                    }


                    $sql = sprintf(
                        "select `Email Campaign Name` object_name,`Email Campaign Key` as object_key from $table   $where $wheref
	                and ($_order_field  > %s OR ($_order_field  = %s AND `Email Campaign Key` > %d))  order by $_order_field   , `Email Campaign Key`  limit 1", prepare_mysql($_order_field_value),
                        prepare_mysql($_order_field_value), $object->id
                    );

                    if ($result = $db->query($sql)) {
                        if ($row = $result->fetch()) {
                            $next_key   = $row['object_key'];
                            $next_title = _("Mailshot").' '.$row['object_name'];

                        }
                    } else {
                        print_r($error_info = $db->errorInfo());
                        exit;
                    }


                    if ($order_direction == 'desc') {
                        $_tmp1      = $prev_key;
                        $_tmp2      = $prev_title;
                        $prev_key   = $next_key;
                        $prev_title = $next_title;
                        $next_key   = $_tmp1;
                        $next_title = $_tmp2;
                    }


                }
            }
        } else {
            print_r($error_info = $db->errorInfo());
            exit;
        }

        if ($data['section'] == 'newsletter') {

            $up_button = array(
                'icon'      => 'arrow-up',
                'title'     => _("Email campaigns").' ('.$data['_parent']->get('Code').')',
                'reference' => 'customers/'.$data['_parent']->id.'/email_campaigns'
            );

            if ($prev_key) {
                $left_buttons[] = array(
                    'icon'      => 'arrow-left',
                    'title'     => $prev_title,
                    'reference' => 'newsletters/'.$data['_parent']->id.'/'.$prev_key
                );

            } else {
                $left_buttons[] = array(
                    'icon'  => 'arrow-left disabled',
                    'title' => '',
                    'url'   => ''
                );

            }
            $left_buttons[] = $up_button;


            if ($next_key) {
                $left_buttons[] = array(
                    'icon'      => 'arrow-right',
                    'title'     => $next_title,
                    'reference' => 'newsletters/'.$data['_parent']->id.'/'.$next_key
                );

            } else {
                $left_buttons[] = array(
                    'icon'  => 'arrow-right disabled',
                    'title' => '',
                    'url'   => ''
                );

            }


            $sections = get_sections('customers', $data['_parent']->id);


            $title = _('Newsletter').' <span class="id Email_Campaign_Name">'.$object->get('Name').'</span>';

        }




    if (isset($sections[$_section])) {
        $sections[$_section]['selected'] = true;
    }




    $_content = array(
        'sections_class' => '',
        'sections'       => $sections,
        'left_buttons'   => $left_buttons,
        'right_buttons'  => $right_buttons,
        'title'          => $title,
        'search'         => array(
            'show'        => true,
            'placeholder' => _('Search orders')
        )

    );
    $smarty->assign('_content', $_content);


    $html = $smarty->fetch('navigation.tpl');

    return $html;

}




?>
