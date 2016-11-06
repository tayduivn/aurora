<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 16 October 2016 at 14:04:36 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/


$tab     = 'production.supplier_parts';
$ar_file = 'ar_production_tables.php';
$tipo    = 'supplier_parts';

$default = $user->get_tab_defaults($tab);


$table_views = array(
    'overview' => array(
        'label' => _('Overview'),
        'title' => _('Overview')
    ),
    'parts'    => array(
        'label' => _('Inventory Part'),
        'title' => _('Part details')
    ),
    'reorder'  => array('label' => _('Reorder')),

);

$table_filters = array(
    'reference' => array(
        'label' => _('Reference'),
        'title' => _('Part reference')
    ),

);

$parameters = array(
    'parent'     => $state['object'],
    'parent_key' => $state['key'],

);


if ($state['_object']->get('Supplier Type') != 'Archived') {

    $table_buttons = array();

    if ($state['_object']->get('Supplier Number Parts') > 0) {
        $table_buttons[] = array(
            'icon'  => 'edit',
            'title' => _("Edit supplier's parts"),
            'id'    => 'edit_table'
        );
    }

    $table_buttons[] = array(
        'icon'      => 'plus',
        'title'     => _("New supplier's part"),
        'reference' => "supplier/".$state['key']."/part/new"
    );

    $smarty->assign('table_buttons', $table_buttons);

    $smarty->assign(
        'upload_file', array(
            'tipo'       => 'edit_objects',
            'icon'       => 'fa-cloud-upload',
            'parent'     => $state['object'],
            'parent_key' => $state['key'],
            'object'     => 'supplier_part',
            'label'      => _("Upload supplier's parts")

        )
    );


}

include 'utils/get_table_html.php';


?>
