<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: Thu 11 April  2019 12:01:29 MYT, Kuala Lumpur, Malaysia

 Copyright (c) 2019, Inikoo

 Version 3.0
*/




$object_fields = array();




$object_fields[] = array(
    'label'      => _('Id'),
    'show_title' => true,
    'fields'     => array(


        array(
            'edit'              => ($edit ? 'string' : ''),
            'id'                => 'Deal_Name',
            'value'             => $object->get('Deal Name'),
            'formatted_value'   => $object->get('Name'),
            'label'             => ucfirst($object->get_field_label('Deal Name')),
            'invalid_msg'       => get_invalid_message('string'),
            'required'          => true,
            'server_validation' => json_encode(array('tipo' => 'check_for_duplicates')),
            'type'              => 'value'
        ),



    )

);


$object_fields[] = array(
    'label'      => _('Public labels'),
    'show_title' => true,
    'fields'     => array(


        array(
            'edit'              => ($edit ? 'string' : ''),
            'id'                => 'Deal_Name_Label',
            'value'             => $object->get('Deal Name Label'),
            'formatted_value'   => $object->get('Name Label Label'),
            'label'             => _('Name'),
            'invalid_msg'       => get_invalid_message('string'),
            'required'          => false,
            'type'              => 'value'
        ),

        array(
            'edit'            => ($edit ? 'string' : ''),
            'id'              => 'Deal_Term_Label',
            'value'           => $object->get('Deal Term Label'),
            'formatted_value' => $object->get('Term Label'),
            'label'           => _('Terms info'),
            'invalid_msg'     => get_invalid_message('string'),
            'required'        => false,
            'type'            => 'value'
        ),

        array(
            'edit'            => ($edit ? 'string' : ''),
            'id'              => 'Deal_Component_Allowance_Label',
            'value'           => $object->get('Deal Component Allowance Label'),
            'formatted_value' => $object->get('Component Allowance Label'),
            'label'           => _('Allowance info'),
            'invalid_msg'     => get_invalid_message('string'),
            'required'        => false,
            'type'            => 'value'
        ),


    )

);

if($object->get('Deal Status')!='Finish') {


    $object_fields[] = array(

        'label'      => _('Dates'),
        'show_title' => true,
        'fields'     => array(


            array(
                'edit' => ($edit ? 'date' : ''),
                'time' => '00:00:00',
                'render'=>($object->get('Deal Status')=='Waiting'?true:false),

                'id'              => 'Deal_Begin_Date',
                'value'           => $object->get('Deal Begin Date'),
                'formatted_value' => $object->get('Begin Date'),
                'label'           => ucfirst($object->get_field_label('Begin Date')),
                'invalid_msg'     => get_invalid_message('date'),
                'required'        => true
            ),

            array(
                'edit' => ($edit ? 'date' : ''),
                'time' => '00:00:00',

                'id'              => 'Deal_Expiration_Date',
                'value'           => $object->get('Deal Expiration Date'),
                'formatted_value' => $object->get('Expiration Date'),
                'label'           => ucfirst($object->get_field_label('Expiration Date')),
                'invalid_msg'     => get_invalid_message('date'),
                'required'        => true
            ),
        )

    );

}


if( $object->get('Deal Status')!='Finish'){
    $operations = array(
        'label'      => _('Operations'),
        'show_title' => true,
        'class'      => 'operations',
        'fields'     => array(

            array(
                'id'        => 'suspend_deal',
                'class'     => 'operation',
                'render'=>(($object->get('Deal Status')=='Suspended' or $object->get('Deal Status')=='Finish' )?false:true),
                'value'     => '',
                'label'     => '<i class="fa fa-fw fa-lock button" onClick="toggle_unlock_delete_object(this)" style="margin-right:20px"></i> <span data-data=\'{ "object": "'.$object->get_object_name().'", "key":"'.$object->id
                    .'"}\' onClick="suspend_object(this)" class="delete_object disabled">'._("Suspend offer").' <i class="fa fa-stop error new_button link"></i></span>',
                'reference' => '',
                'type'      => 'operation'
            ),

            array(
                'id'        => 'activate_deal',
                'class'     => 'operation',
                'render'=>($object->get('Deal Status')!='Suspended'?false:true),

                'value'     => '',
                'label'     => '<i class="fa fa-fw fa-lock hide button" onClick="toggle_unlock_delete_object(this)" style="margin-right:20px"></i> <span data-data=\'{ "object": "'.$object->get_object_name().'", "key":"'.$object->id
                    .'"}\' onClick="activate_object(this)" class="button">'._("Activate offer").' <i class="fa fa-play success new_button"></i></span>',
                'reference' => '',
                'type'      => 'operation'
            ),


            array(
                'id'        => 'finish_deal',
                'class'     => 'operation',
                'render'=>(( $object->get('Deal Status')=='Active' or $object->get('Deal Status')=='Suspended')  ?true:false),

                'value'     => '',
                'label'     => '<i class="fa fa-fw fa-lock  button" onClick="toggle_unlock_delete_object(this)" style="margin-right:20px"></i> <span data-data=\'{ "object": "'.$object->get_object_name().'", "key":"'.$object->id
                    .'"}\' onClick="finish_object(this)" class="delete_object disabled">'._("End offer now").' <i class="fa fa-stop discreet new_button"></i></span>',
                'reference' => '',
                'type'      => 'operation'
            ),

            array(
                'id'        => 'finish_deal',
                'class'     => 'operation',
                'render'=>(( $object->get('Deal Status')=='Waiting')  ?true:false),

                'value'     => '',
                'label'     => '<i class="fa fa-fw fa-lock  button" onClick="toggle_unlock_delete_object(this)" style="margin-right:20px"></i> <span data-data=\'{ "object": "'.$object->get_object_name().'", "key":"'.$object->id
                    .'"}\' onClick="delete_object(this)" class="delete_object disabled">'._("Delete").' <i class="far fa-trash-alt discreet new_button"></i></span>',
                'reference' => '',
                'type'      => 'operation'
            ),

        )

    );
    $object_fields[] = $operations;
}



