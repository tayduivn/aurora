<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 10 May 2016 at 14:19:03 GMT+8, Kuala Lumpur, Malaysia

 Copyright (c) 2016, Inikoo

 Version 3.0
*/


if (isset($options['new']) and $options['new']) {
    $new = true;
} else {
    $new = false;
}


$object_fields = array(
    array(
        'label'      => _('Id'),
        'show_title' => true,
        'fields'     => array(


            array(
                'edit'              => ($edit ? 'string' : ''),
                'id'                => 'Deal_Campaign_Name',
                'value'             => $object->get('Deal Campaign Name'),
                'label'             => ucfirst(
                    $object->get_field_label('Deal Campaign Name')
                ),
                'invalid_msg'       => get_invalid_message('string'),
                'required'          => true,
                'server_validation' => json_encode(
                    array('tipo' => 'check_for_duplicates')
                ),

                'type' => 'value'
            ),

            array(
                'id'              => 'Deal_Campaign_Description',
                'edit'            => ($edit ? 'editor' : ''),
                'render'          => ($new ? false : true),
                'class'           => 'editor',
                'editor_data'     => array(
                    'id'      => 'Deal_Campaign_Description',
                    'content' => $object->get('Deal Campaign Description'),

                    'data' => base64_encode(
                        json_encode(
                            array(
                                'mode'     => 'edit_object',
                                'field'    => 'Deal_Campaign_Description',
                                'plugins'  => array(
                                    'align',
                                    'draggable',
                                    'image',
                                    'link',
                                    'save',
                                    'entities',
                                    'emoticons',
                                    'fullscreen',
                                    'lineBreaker',
                                    'table',
                                    'codeView',
                                    'codeBeautifier'
                                ),
                                'metadata' => array(
                                    'tipo'   => 'edit_field',
                                    'object' => 'Deal_Campaign',
                                    'key'    => $object->id,
                                    'field'  => 'Deal Campaign Description',


                                )
                            )
                        )
                    )

                ),
                'value'           => $object->get('Deal Campaign Description'),
                'formatted_value' => $object->get('Deal Campaign Description'),

                'label'    => ucfirst($object->get_field_label('Deal Campaign Description')),
                'required' => false,
                'type'     => ''
            ),


        )
    ),



);


?>
