<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created:  27 December 2019  11:06::18  +0800, Kuala Lumpur Malaysia

 Copyright (c) 2019, Inikoo

 Version 3.0
*/

function get_customers_server_module() {
    return array(

        'parent'      => 'none',
        'parent_type' => 'none',
        'section'     => 'customers',
        'sections'    => array(
            'customers'            => array(
                'type'      => 'navigation',
                'label'     => _('Customers (All stores)'),
                'title'     => _('Customers (All stores)'),
                'icon'      => '',
                'reference' => 'customers/all',
                'tabs'      => array(
                    'customers_server' => array()
                )
            ),


        )

    );
}