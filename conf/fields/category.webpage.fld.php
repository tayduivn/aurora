<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 1 December 2016 at 13:23:45 GMT+8, Kuta, Bali

 Copyright (c) 2016, Inikoo

 Version 3.0
*/


$object->get_webpage();




if (isset($options['new']) and $options['new']) {
    $new = true;
} else {
    $new = false;
}






$subject_render = false;

if($object->get('Category Subject')=='Category'){
    $template_options = array(
        'blank'    => _('White canvas'),
        'categories_showcase'    => _('Categories showcase')
    );

}else{
    $template_options = array(
        'blank'    => _('White canvas'),
        'family_buttons'    => 'Old products showcase',
        'products_showcase'    => _('Products showcase')
    );

}

asort($template_options);


$subject_options         = array(
    'Product'  => _('Products'),
    'Category' => _('Categories')
);
$subject_value           = 'Product';
$subject_formatted_value = _('Products');
$subject_render          = true;






$category_fields = array(
    array(
        'label'      => _('Webpage'),
        'show_title' => true,
        'fields'     => array(

/*
            array(
                'id'              => 'Webpage_URLs',
                'edit'            => 'Webpage_URLs',
                'value'           => '',
                'formatted_value' => $object->get('Webpage URLs'),
                'label'           => _('URLs'),
                'required'        => false,
                'type'            => ''
            ),

*/
            array(
                'id'                       => 'Category_Website_Node_Parent_Key',
                'edit'                     => 'dropdown_select',
                'scope'                    => 'web_node',
                'parent'                   => 'website',
                'parent_key'               => ($new ?: $object->webpage->get('Page Site Key')),
                'value'                    => htmlspecialchars($object->webpage->get('Page Found In Page Key')),
                'formatted_value'          => $object->webpage->get('Found In Page Key'),
                'stripped_formatted_value' => '',
                'label'                    => _('Found in'),
                'required'                 => true,
                'type'                     => ''


            ),

            array(
                'id'   => 'Category_Webpage_Name',
                'edit' => ($edit ? 'string' : ''),

                'value'           => htmlspecialchars($object->get('Category Webpage Name')),
                'formatted_value' => $object->get('Webpage Name'),
                'label'           => ucfirst($object->get_field_label('Category Webpage Name')),
                'required'        => true,
                'type'            => ''


            ),

            array(
                'id'   => 'Category_Webpage_Browser_Title',
                'edit' => ($edit ? 'string' : ''),

                'value'           => htmlspecialchars($object->get('Category Webpage Browser Title')),
                'formatted_value' => $object->get('Webpage Browser Title'),
                'label'           => ucfirst($object->get_field_label('Category Webpage Browser Title')),
                'required'        => true,
                'type'            => ''


            ),

            array(
                'id'   => 'Category_Webpage_Meta_Description',
                'edit' => ($edit ? 'textarea' : ''),

                'value'           => htmlspecialchars($object->get('Category Webpage Meta Description')),
                'formatted_value' => $object->get('Webpage Meta Description'),
                'label'           => ucfirst($object->get_field_label('Category Webpage Meta Description')),
                'required'        => true,
                'type'            => ''


            ),



            /*
                        array(
                            'id'              => 'Webpage_Versions',
                            'edit'            => 'Webpage_Versions',
                            'value'           => '',
                            'formatted_value' => $object->get('Webpage Versions'),
                            'label'           => _('Versions'),
                            'required'        => false,
                            'type'            => ''
                        ),
            */

        )
    ),


);



$template_field = array(





    array(
        'label'      => _('Template'),
        'show_title' => true,
        'fields'     => array(



            array(
                'edit' => ($edit ? 'option' : ''),

                'id'              => 'Webpage_Template',
                'value'           => $object->get('Category Webpage Template'),
                'formatted_value' => $object->get('Webpage Template'),
                'options'         => $template_options,
                'label'           => _('Template'),
                'invalid_msg'     => get_invalid_message('string'),
                'required'        => true,
                'type'            => 'value'
            ),






        )
    ),


);



$template_fields = array(





    array(
        'label'      => _('Template settings'),
        'show_title' => true,
        'fields'     => array(







            array(
                'id'              => 'Webpage_Related_Products',
                'edit'            => 'webpage_related_products',
                'value'           => '',
                'formatted_value' => $object->get('Webpage Related Products'),
                'label'           => _('Related products links'),
                'required'        => false,
                'type'            => ''
            ),
            array(
                'id'              => 'Webpage_See_Also',
                'edit'            => 'webpage_see_also',
                'value'           => '',
                'formatted_value' => $object->get('Webpage See Also'),
                'label'           => _('See also links'),
                'required'        => false,
                'type'            => ''
            ),


        )
    ),


);

$category_fields = array_merge(
    $category_fields, $template_field,$template_fields
);






/*

        $store = new Store($object->get('Store Key'));



        $object->get_webpage();


        if ($store->get('Store Family Category Key') == $object->get(
                'Category Root Key'
            )
        ) {

            include 'family.fld.php';
            $category_fields = array_merge(
                $category_fields, $category_product_fields
            );

        } elseif ($store->get('Store Department Category Key') == $object->get(
                'Category Root Key'
            )
        ) {

            include 'department.fld.php';
            $category_fields = array_merge(
                $category_fields, $category_product_fields
            );

        } else {
            include 'category.product.fld.php';
            $category_fields = array_merge(
                $category_fields, $category_product_fields
            );

        }

*/




?>
