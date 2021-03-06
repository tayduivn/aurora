<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 6 April 2016 at 00:13:30 GMT+8, Kaula Lumpur, Mlaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/


$tab     = 'inventory.barcodes';
$ar_file = 'ar_inventory_tables.php';
$tipo    = 'barcodes';

$default = $user->get_tab_defaults($tab);


$table_views = array();

$table_filters = array(
    'number'    => array(
        'label' => _('Number'),
        'title' => _('Barcode number')
    ),
    'reference' => array(
        'label' => _('Part Reference'),
        'title' => _('Part reference')
    ),

);


$parameters = array(
    'parent'     => $state['parent'],
    'parent_key' => $state['parent_key'],

);

$table_buttons   = array();
$table_buttons[] = array(
    'icon'              => 'plus',
    'title'             => _('New barcode'),
    'id'                => 'new_record',
    'inline_new_object' => array(
        'field_id'    => 'Barcode_Range',
        'field_label' => _('Add barcodes').':',
        'field_edit'  => 'barcode_range',
        'object'      => 'Barcode',
        'parent'      => $state['parent'],
        'parent_key'  => $state['parent_key'],

    )

);
$smarty->assign('table_buttons', $table_buttons);


include 'utils/get_table_html.php';
$html = $html.'<div id="fields" object="Barcode"></div>';


?>
