<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 27 January 2019 at 15:19:06 GMT+8, Kuala Lumpur, Malaysia

 Copyright (c) 2018, Inikoo

 Version 3.0
*/


include_once 'utils/static_data.php';

$account = new Account();

if (isset($options['new']) and $options['new']) {
    $new = true;
} else {
    $new = false;
}


if (isset($options['part_scope']) and $options['part_scope']) {
    $part_scope = true;
} else {
    $part_scope = false;
}


if (isset($options['show_full_label']) and $options['show_full_label']) {
    $show_full_label = true;
    $field_prefix    = 'Part ';
} else {
    $show_full_label = false;
    $field_prefix    = '';
}


$options_yn = array(
    'Yes' => _('Yes'),
    'No'  => _('No')
);


$options_status = array(
    'Available'    => _('Available'),
    'NoAvailable'  => _('No stock'),
    'Discontinued' => _('Discontinued')
);


$supplier_part_fields = array();


$supplier_part_fields[] = array(
    'label' => ($show_full_label
        ? _("Supplier's product description")
        : _(
            'Description'
        )),

    'show_title' => true,
    'fields'     => array(
        array(
            'id'                       => 'Supplier_Part_Supplier_Key',
            'render'                   => (($new and $part_scope) ? true : false),
            'edit'                     => 'dropdown_select',
            'scope'                    => 'suppliers',
            'parent'                   => 'account',
            'parent_key'               => 1,
            'value'                    => htmlspecialchars(
                $object->get('Supplier Part Supplier Key')
            ),
            'formatted_value'          => $object->get('Supplier Key'),
            'stripped_formatted_value' => $object->get('Supplier Key'),
            'label'                    => ("Supplier's code"),
            'placeholder'              => _("Supplier's code"),
            'required'                 => ($part_scope ? true : false),
            'type'                     => 'value'
        ),
        array(
            'id'   => 'Supplier_Part_Reference',
            'edit' => ($edit ? 'string' : ''),

            'value'             => htmlspecialchars($object->get('Supplier Part Reference')),
            'formatted_value'   => $object->get('Reference'),
            'label'             => ucfirst($object->get_field_label('Supplier Part Reference')),
            'required'          => true,
            'server_validation' => json_encode(
                array('tipo' => 'check_for_duplicates')
            ),
            'type'              => 'value'
        ),

        array(
            'id'     => 'Part_Reference',
            'edit'   => ($edit ? 'string' : ''),
            'render' => (($new) ? true : false),

            'value'             => htmlspecialchars(
                $object->get('Part Part Reference')
            ),
            'formatted_value'   => $object->get('Part Reference'),
            'label'             => _('Part reference'),
            'required'          => true,
            'server_validation' => json_encode(
                array(
                    'tipo'       => 'check_for_duplicates',
                    'field'      => 'Part_Reference',
                    'parent'     => 'account',
                    'parent_key' => 1,
                    'object'     => 'Part',
                    'key'        => (isset($object->part->id) ? $object->part->id : false)
                )
            ),
            'type'              => 'value'
        ),

        array(
            'id'     => 'Supplier_Part_Package_Description',
            'render' => (($new) ? true : false),

            'edit' => ($edit ? 'string' : ''),

            'value'           => htmlspecialchars($object->get('Part Part Package Description')),
            'formatted_value' => $object->get('Part Package Description'),
            'label'           => _('Part SKO description').' ('._('for picking aid').')',
            'required'        => true,
            'type'            => 'value'
        ),


        array(
            'id'     => 'Part_SKO_Barcode',
            'render' => (($new) ? true : false),

            'edit' => ($edit ? 'string' : ''),

            'value'             => htmlspecialchars($object->get('Part Part SKO Barcode')),
            'formatted_value'   => $object->get('Part SKO Barcode'),
            'label'             => _('Part SKO barcode').' ('._('stock control').')',
            'required'          => false,
            'server_validation' => json_encode(
                array(
                    'tipo'       => 'check_for_duplicates',
                    'parent'     => 'account',
                    'parent_key' => 1,
                    'object'     => 'Part',
                    'key'        => $object->id
                )
            ),
            'type'              => 'value'
        ),

        array(
            'id'     => 'Part_Units_Per_Package',
            'render' => (($new) ? true : false),

            'edit'            => 'smallint_unsigned',
            'value'           => ($new ? 1 : htmlspecialchars($object->get('Part Part Units Per Package'))),
            'formatted_value' => ($new ? 1 : $object->get('Part Units Per Package')),
            'label'           => ucfirst($object->get_field_label('Part Units Per Package')),
            'required'        => true,
            'type'            => 'value'
        ),
        array(
            'id'     => 'Part_Barcode',
            'render' => (($new) ? true : false),

            'edit' => ($edit ? 'barcode' : ''),

            'value'             => htmlspecialchars($object->get('Part Part Barcode Number')),
            'formatted_value'   => $object->get('Part Barcode Number'),
            'label'             => _('Unit barcode (EAN-13, for website)'),
            'required'          => false,
            'invalid_msg'       => get_invalid_message('barcode_ean'),
            'server_validation' => json_encode(
                array(
                    'tipo'       => 'check_for_duplicates',
                    'parent'     => 'account',
                    'parent_key' => 1,
                    'object'     => 'Part',
                    'key'        => (isset($object->part) ? $object->part->id : 0)
                )
            ),
            'type'              => 'value'
        ),
        array(
            'id'              => 'Part_Barcode_Number_Next_Available',
            'edit'            => ($edit ? 'string' : ''),
            'render'          => false,
            'value'           => 'No',
            'formatted_value' => 'No',
            'label'           => 'Next barcode',
            'required'        => false,
            'type'            => 'value'
        ),

        array(
            'id'              => 'Part_Barcode_Number_Next_Available',
            'edit'            => ($edit ? 'string' : ''),
            'render'          => false,
            'value'           => 'No',
            'formatted_value' => 'No',
            'label'           => 'Next barcode',
            'required'        => false,
            'type'            => 'value'
        ),
        array(
            'id'   => 'Supplier_Part_Description',
            'edit' => ($edit ? 'string' : ''),

            'value'           => htmlspecialchars($object->get('Supplier Part Description')),
            'formatted_value' => $object->get('Supplier Part Description'),
            'label'           => ucfirst(
                $object->get_field_label('Supplier Part Description')
            ),
            'required'        => true,
            'type'            => 'value'
        ),
        array(
            'id' => 'Supplier_Part_Unit_Label',

            'edit' => ($edit ? 'string' : ''),

            'value'           => ($new
                ? _('Piece')
                : htmlspecialchars(
                    $object->get('Part Part Unit Label')
                )),
            'formatted_value' => ($new
                ? _('Piece')
                : $object->get(
                    'Part Unit Label'
                )),
            'label'           => ucfirst(
                $object->get_field_label('Part Unit Label')
            ),
            'required'        => true,
            'type'            => 'value'
        ),


    )
);


$supplier_part_fields[] = array(
    'label' => ($show_full_label
        ? _("Supplier's product ordering")
        : _(
            'Ordering'
        )),

    'show_title' => true,
    'fields'     => array(
        array(
            'render' => ($new ? false : true),
            'id'     => 'Supplier_Part_Status',
            'edit'   => ($edit ? 'option' : ''),

            'options'         => $options_status,
            'value'           => htmlspecialchars(
                $object->get('Supplier Part Status')
            ),
            'formatted_value' => $object->get('Status'),
            'label'           => ucfirst(
                $object->get_field_label('Supplier Part Status')
            ),
            'required'        => ($new ? false : true),
            'type'            => 'skip'
        ),

        array(

            'render' => true,

            'id'   => 'Supplier_Part_On_Demand',
            'edit' => ($edit ? 'option' : ''),

            'options'         => $options_yn,
            'value'           => ($new
                ? 'No'
                : $object->get(
                    'Supplier Part On Demand'
                )),
            'formatted_value' => ($new ? _('No') : $object->get('On Demand')),
            'label'           => ucfirst(
                    $object->get_field_label('Supplier Part On Demand')
                ).' <i class="fa fa-fighter-jet" aria-hidden="true"></i>',
            'required'        => false,
            'type'            => 'value',

        ),
        array(

            'render' => ($object->get('Supplier Part On Demand') == 'Yes' ? true : false),

            'id'   => 'Supplier_Part_Fresh',
            'edit' => ($edit ? 'option' : ''),

            'options'         => $options_yn,
            'value'           => ($new
                ? 'No'
                : $object->get(
                    'Supplier Part Fresh'
                )),
            'formatted_value' => ($new ? _('No') : $object->get('Fresh')),
            'label'           => ucfirst(
                    $object->get_field_label('Supplier Part Fresh')
                ).' <i class="fa fa-lemon" aria-hidden="true"></i>',
            'required'        => false,
            'type'            => 'value'

        ),

        array(
            'id'              => 'Supplier_Part_Packages_Per_Carton',
            'edit'            => 'smallint_unsigned',
            'value'           => ($new ? 1 : htmlspecialchars($object->get('Supplier Part Packages Per Carton'))),
            'formatted_value' => ($new ? 1 : $object->get('Packages Per Carton')),
            'label'           => ucfirst($object->get_field_label('Supplier Part Packages Per Carton')).'<div class="warning" style="line-height: normal;font-size: 80%;position: relative;top:-4px"> <i class="fa fa-exclamation-triangle yellow" title="'._(
                    "This field is independent of parts's SKOs per selling carton"
                ).'" aria-hidden="true"></i> '._("This field is independent of parts's SKOs per selling carton").'</div>',
            'required'        => true,
            'type'            => 'value'
        ),
        array(
            'id'              => 'Supplier_Part_Minimum_Carton_Order',
            'edit'            => 'smallint_unsigned',
            'value'           => ($new
                ? 1
                : htmlspecialchars(
                    $object->get('Supplier Part Minimum Carton Order')
                )),
            'formatted_value' => ($new
                ? 1
                : $object->get(
                    'Minimum Carton Order'
                )),
            'label'           => ucfirst(
                $object->get_field_label('Supplier Part Minimum Carton Order')
            ),
            'placeholder'     => _('cartons'),

            'required' => true,
            'type'     => 'value'
        ),
        array(
            'id'   => 'Supplier_Part_Average_Delivery_Days',
            'edit' => ($edit ? 'numeric' : ''),

            'value' => ($new
                ? ($part_scope
                    ? ''
                    : $options['parent_object']->get(
                        'Supplier Average Delivery Days'
                    ))
                : htmlspecialchars(
                    $object->get('Supplier Part Average Delivery Days')
                )),

            'formatted_value' => ($new ? ($part_scope
                ? ''
                : $options['parent_object']->get(
                    'Supplier Average Delivery Days'
                )) : $object->get('Average Delivery Days')),
            'label'           => ucfirst(
                $object->get_field_label('Supplier Part Average Delivery Days')
            ),
            'placeholder'     => _('days'),

            'required' => false,
            'type'     => 'value'
        ),
        array(
            'id'   => 'Supplier_Part_Carton_CBM',
            'edit' => ($edit ? 'numeric' : ''),

            'value'           => htmlspecialchars(
                $object->get('Supplier Part Carton CBM')
            ),
            'formatted_value' => $object->get('Carton CBM'),
            'label'           => ucfirst(
                $object->get_field_label('Supplier Part Carton CBM')
            ),
            'placeholder'     => _('cubic meters'),
            'required'        => false,
            'type'            => 'value'
        ),


    )
);

$supplier_part_fields[] = array(
    'label' => ($show_full_label
        ? _("Supplier's product cost/price")
        : _(
            'Cost/price'
        )),

    'show_title' => true,
    'fields'     => array(
        array(
            'render' => false,
            'id'     => 'Supplier_Part_Currency_Code',
            'edit'   => ($edit ? 'string' : ''),

            'value'           => ($new
                ? ($part_scope
                    ? ''
                    : $options['parent_object']->get(
                        'Supplier Default Currency Code'
                    ))
                : htmlspecialchars(
                    $object->get('Supplier Part Currency Code')
                )),
            'formatted_value' => ($new ? ($part_scope ? '' : $options['parent_object']->get('Default Currency Code ')) : htmlspecialchars($object->get('Currency Code'))),
            'label'           => ucfirst(
                $object->get_field_label('Supplier Part Currency Code')
            ),
            'required'        => false,
            'type'            => 'value'
        ),

        array(
            'id'              => 'Supplier_Part_Unit_Cost',
            'edit'            => ($edit ? 'amount' : ''),
            'locked'          => ($part_scope ? 1 : 0),
            'value'           => htmlspecialchars(
                $object->get('Supplier Part Unit Cost')
            ),
            'formatted_value' => $object->get('Unit Cost'),
            'label'           => ucfirst(
                $object->get_field_label('Supplier Part Unit Cost')
            ),
            'required'        => true,
            'placeholder'     => ($part_scope
                ? ''
                : sprintf(
                    _('amount in %s '), $options['parent_object']->get('Default Currency Code')
                )),
            'type'            => 'value'
        ),

        array(
            'id'              => 'Supplier_Part_Unit_Extra_Cost',
            'render'          => false,
            'edit'            => 'amount_percentage',
            'locked'          => ($part_scope ? 1 : 0),
            'value'           => htmlspecialchars(
                $object->get('Supplier Part Unit Extra Cost')
            ),
            'formatted_value' => $object->get('Unit Extra Cost'),
            'label'           => ucfirst(
                $object->get_field_label('Supplier Part Unit Extra Cost')
            ),
            'required'        => false,
            'placeholder'     => ($part_scope
                ? ''
                : sprintf(
                    _('amount in %s or %%'), $options['parent_object']->get('Default Currency Code')
                )),
            'type'            => 'value'
        ),

        array(
            'id'              => 'Supplier_Part_Unit_Extra_Cost_Percentage',
            'edit'            => 'percentage',
            'locked'          => ($part_scope ? 1 : 0),
            'value'           => htmlspecialchars($object->get('Supplier Part Unit Extra Cost Percentage')),
            'formatted_value' => $object->get('Unit Extra Cost Percentage'),
            'label'           => ucfirst($object->get_field_label('Supplier Part Unit Extra Cost Percentage')),
            'required'        => false,
            'placeholder'     => ($part_scope ? '' : '%'),
            'type'            => 'value'
        ),


    )
);

if ($new) {
    $supplier_part_fields[] = array(
        'label' => _('Recommended product values').' ('._('for website').')',

        'show_title' => true,
        'fields'     => array(


            array(
                'id'   => 'Part_Unit_Price',
                'edit' => 'amount_margin',

                'value'           => htmlspecialchars($object->get('Part Part Unit Price')),
                'formatted_value' => $object->get('Part Unit Price'),
                'label'           => ucfirst($object->get_field_label('Part Unit Price')),
                'required'        => false,
                'placeholder'     => sprintf(_('amount in %s or margin (%%)'), $account->get('Currency Code')),
                'type'            => 'value'
            ),
            array(
                'id'              => 'Part_Unit_RRP',
                'edit'            => 'amount_margin',
                'value'           => htmlspecialchars($object->get('Part Part Unit RRP')),
                'formatted_value' => $object->get('Part Unit RRP'),
                'label'           => ucfirst($object->get_field_label('Part Unit RRP')),
                'required'        => false,
                'placeholder'     => sprintf(_('amount in %s or margin (%%)'), $account->get('Currency Code')),
                'type'            => 'value'
            ),


            array(
                'id'   => 'Part_Recommended_Product_Unit_Name',
                'edit' => ($edit ? 'string' : ''),

                'value'           => htmlspecialchars($object->get('Part Part Recommended Product Unit Name')),
                'formatted_value' => $object->get('Part Recommended Product Unit Name'),
                'label'           => ucfirst($object->get_field_label('Part Recommended Product Unit Name')).' ('._('website').')',
                'required'        => true,
                'type'            => 'value'
            ),


        )
    );

}

?>
