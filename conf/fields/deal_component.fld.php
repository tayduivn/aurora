<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 26 February 2019 at 14:04:01 GMT+8, Kuala Lumpur, Malaysia

 Copyright (c) 2019, Inikoo

 Version 3.0
*/


$new = false;


$object_fields = array();

$object_fields[] = array(
    'label'      => _('Id'),
    'show_title' => true,
    'fields'     => array(


        array(
            'edit'              => ($edit ? 'string' : ''),
            'id'                => 'Deal_Component_Name_Label',
            'value'             => $object->get('Deal Component Name Label '),
            'formatted_value'   => $object->get('Name Label'),
            'label'             => ucfirst($object->get_field_label('Deal Component Name Label')),
            'invalid_msg'       => get_invalid_message('string'),
            'required'          => true,
            'server_validation' => json_encode(array('tipo' => 'check_for_duplicates')),
            'type'              => 'value'
        ),



    )

);

$object_fields[] = array(

        'label'      => _('Dates'),
        'show_title' => true,
        'fields'     => array(


            array(
                'edit' => ($edit ? 'date' : ''),
                'time' => '00:00:00',

                'id'              => 'Deal_Component_Begin_Date',
                'value'           => $object->get('Deal Component Begin Date'),
                'formatted_value' => $object->get('Begin Date'),
                'label'           => ucfirst($object->get_field_label('Begin Date')),
                'invalid_msg'     => get_invalid_message('date'),
                'required'        => true
            ),

            array(
                'edit' => ($edit ? 'date' : ''),
                'time' => '23:59:59',

                'id'              => 'Deal_Component_Expiration_Date',
                'value'           => $object->get('Deal Component Expiration Date'),
                'formatted_value' => $object->get('Expiration Date'),
                'label'           => ucfirst($object->get_field_label('Expiration Date')),
                'invalid_msg'     => get_invalid_message('date'),
                'required'        => true
            ),
        )

);

$operations = array(
    'label'      => _('Operations'),
    'show_title' => true,
    'class'      => 'operations',
    'fields'     => array(

        array(
            'id'        => 'suspend_deal',
            'class'     => 'operation',
            'render'=>($object->get('Deal Component Status')=='Suspended'?false:true),
            'value'     => '',
            'label'     => '<i class="fa fa-fw fa-lock button" onClick="toggle_unlock_delete_object(this)" style="margin-right:20px"></i> <span data-data=\'{ "object": "'.$object->get_object_name().'", "key":"'.$object->id
                .'"}\' onClick="suspend_object(this)" class="delete_object disabled">'._("Suspend offer").' <i class="fa fa-stop error new_button link"></i></span>',
            'reference' => '',
            'type'      => 'operation'
        ),

        array(
            'id'        => 'activate_deal',
            'class'     => 'operation',
            'render'=>($object->get('Deal Component Status')!='Suspended'?false:true),

            'value'     => '',
            'label'     => '<i class="fa fa-fw fa-lock hide button" onClick="toggle_unlock_delete_object(this)" style="margin-right:20px"></i> <span data-data=\'{ "object": "'.$object->get_object_name().'", "key":"'.$object->id
                .'"}\' onClick="activate_object(this)" class="button">'._("Activate offer").' <i class="fa fa-play success new_button"></i></span>',
            'reference' => '',
            'type'      => 'operation'
        ),


    )

);

$object_fields[] = $operations;


?>
