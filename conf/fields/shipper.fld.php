<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 5 July 2018 at 15:59:35 GMT+8,  Kuala Lumpur, Malaysia

 Copyright (c) 2016, Inikoo

 Version 3.0
*/


if (isset($options['new']) and $options['new']) {
    $new = true;
} else {
    $new = false;
}

$object_fields = array();

$object_fields[] = array(
    'label'      => _('Id'),
    'show_title' => true,
    'fields'     => array(


        array(
            'edit'            => ($edit ? 'string' : ''),
            'id'              => 'Shipper_Code',
            'value'           => $object->get('Shipper Code'),
            'formatted_value' => $object->get('Code'),

            'label'             => ucfirst($object->get_field_label('Shipper Code')),
            'invalid_msg'       => get_invalid_message('string'),
            'required'          => true,
            'server_validation' => json_encode(array('tipo' => 'check_for_duplicates')),
            'type'              => 'value'
        ),
        array(
            'edit'            => ($edit ? 'string' : ''),
            'id'              => 'Shipper_Name',
            'value'           => $object->get('Shipper Name'),
            'formatted_value' => $object->get('Name'),

            'label'       => ucfirst($object->get_field_label('Shipper Name')),
            'invalid_msg' => get_invalid_message('string'),
            'required'    => true,
            'type'        => 'value'
        ),
    ),


);

$object_fields[] = array(
    'label'      => _('Tracking'),
    'show_title' => true,
    'fields'     => array(


        array(
            'edit'            => ($edit ? 'string' : ''),
            'id'              => 'Shipper_Tracking_URL',
            'value'           => $object->get('Shipper Tracking URL'),
            'formatted_value' => $object->get('Tracking URL'),

            'label'       => ucfirst($object->get_field_label('Shipper Tracking URL')),
            'invalid_msg' => get_invalid_message('www'),
            'required'    => false,
            'placeholder'=>'http://',
            'type'        => 'value'
        ),

    )

);

?>