<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 15 July 2017 at 13:46:05 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2017, Inikoo

 Version 3

*/

function get_webpage_blocks($theme = '') {


    $blocks = array(

        /*
                'static_banner'  => array(
                    'type'   => 'static_banner',
                    'label'  => _('Header'),
                    'icon'   => 'fa-header',
                    'show'   => 1,
                    '_top_text_left' => 'customize',
                    '_top_text_right' => 'your own',
                    '_title' => 'Chic &amp; Uniquefima Header',
                    '_text' => 'in easy peasy steps',
                    'link'=>'',
                    'bg_image'=>''
                ),
        */

        'text' => array(
            'type'        => 'text',
            'label'       => _('Text'),
            'icon'        => 'fa-font',
            'show'        => 1,
            'template'    => 't1',
            'text_blocks' => array(
                array(
                    'text' => '<h1>Title</h1>When an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also electronics typesetting, remaining essentially believable.'
                )
            ),
        ),

        'blackboard' => array(
            'type'          => 'blackboard',
            'label'         => _('Blackboard'),
            'icon'          => 'fa-image',
            'show'          => 1,
            'top_margin'    => 0,
            'bottom_margin' => 0,
            'height'        => '200',
            'images'        => array(),
            'texts'         => array()

        ),


        'images' => array(
            'type'  => 'images',
            'label' => _('Images'),
            'icon'  => 'fa-camera',
            'show'  => 1,

            'images' => array()


        ),


        /*
                'one_pack'   => array(
                    'type'      => 'one_pack',
                    'label'     => _('One-Pack').'discontinued',
                    'icon'      => 'fa-minus',
                    'show'      => 1,
                    '_title'    => _('Title'),
                    '_subtitle' => 'Here goes an abstract of your content',
                    '_text'     => 'When an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also electronics typesetting, remaining essentially believable.',
                ),
        */
        /*
                'two_pack'   => array(
                    'type'  => 'two_pack',
                    'label' => _('Two-Pack'),
                    'icon'  => 'fa-pause',
                    'show'  => 1,

                    '_image'     => '',
                    '_image_key' => '',
                    '_title'     => _('Thanks for joining us!'),
                    '_subtitle'  => 'Will cover many web sites still in their infancy various versions have evolved packages over the years.',
                    '_text'      => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet anything embarrassing hidden in the middle many web sites.',
                ),


                'three_pack' => array(
                    'type'     => 'three_pack',
                    'label'    => _('Three-Pack'),
                    'icon'     => 'fa-bars fa-rotate-90',
                    'show'     => 1,
                    'title'    => 'What We Do',
                    'subtitle' => 'Aipsum therefore always',


                    'columns' => array(

                        array(
                            'icon'  => 'icon-screen-desktop',
                            'title' => 'Modern Design',
                            'text'  => 'Mombined with handful model sentence structures to generate which looks.',
                        ),
                        array(
                            'icon'  => 'icon-social-dropbox',
                            'title' => 'Mega Blobs',
                            'text'  => 'Mombined with handful model sentence structures to generate which looks.',
                        ),
                        array(
                            'icon'  => 'icon-cup',
                            'title' => 'Diffrent Prods',
                            'text'  => 'Mombined with handful model sentence structures to generate which looks.',
                        ),

                    )


                ),


                'six_pack'   => array(
                    'type'  => 'six_pack',
                    'label' => _('Six-Pack'),
                    'icon'  => 'fa-th-large',
                    'show'  => 1,


                    'columns' => array(

                        array(
                            array(
                                'icon'  => 'icon-cursor',
                                'title' => 'Several Design Options',
                                'text'  => 'Many desktop publishing packages and web page editors now use Ipsum their defau mode various versions have over the years.',
                            ),
                            array(
                                'icon'  => 'icon-basket-loaded',
                                'title' => 'Build Own Website',
                                'text'  => 'Many desktop publishing packages and web page editors now use Ipsum their defau mode various versions have over the years.',
                            )

                        ),

                        array(
                            array(
                                'icon'  => 'icon-badge',
                                'title' => 'Clean &amp; Modern Design',
                                'text'  => 'Many desktop publishing packages and web page editors now use Ipsum their defau mode various versions have over the years.',
                            ),
                            array(
                                'icon'  => 'icon-social-dropbox',
                                'title' => 'Useful Shortcut\'s',
                                'text'  => 'Many desktop publishing packages and web page editors now use Ipsum their defau mode various versions have over the years.',
                            )

                        ),

                        array(
                            array(
                                'icon'  => 'icon-settings',
                                'title' => 'Icon Fonts Easy to Use',
                                'text'  => 'Many desktop publishing packages and web page editors now use Ipsum their defau mode various versions have over the years.',
                            ),
                            array(
                                'icon'  => 'icon-bulb',
                                'title' => 'Excellent Customer Services',
                                'text'  => 'Many desktop publishing packages and web page editors now use Ipsum their defau mode various versions have over the years.',
                            )

                        ),


                    )


                ),


                'two_one'    => array(
                    'type'  => 'two_one',
                    'label' => _('Two-One'),
                    'icon'  => ' fa-window-maximize fa-rotate-90',
                    'show'  => 1,

                    'columns' => array(
                        array(
                            'type'   => 'two_third',
                            '_title' => _('Title'),
                            '_text'  => 'text'
                        ),
                        array(
                            'type'   => 'one_third',
                            '_title' => _('Title'),
                            '_text'  => 'text'
                        )

                    )


                ),

        */

        'button' => array(
            'type'              => 'button',
            'label'             => _('Button'),
            'icon'              => 'fa-hand-pointer',
            'show'              => 1,
            'title'             => 'Great Value to Get the Dash on TF Only',
            'text'              => 'Packages and web page editors search versions have over the years sometimes.',
            'button_label'      => 'Read More',
            'link'              => '',
            'bg_image'          => '',
            'bg_color'          => '',
            'text_color'        => '',
            'button_bg_color'   => '',
            'button_text_color' => '',


        ),
        /*

        'image'      => array(
            'type'    => 'image',
            'label'   => _('Image'),
            'icon'    => 'fa-image',
            'show'    => 1,
            'src'     => '',
            'tooltip' => '',
            'link'    => '',

        ),
*/


        'iframe'    => array(
            'type'   => 'iframe',
            'label'  => 'iFrame',
            'icon'   => 'fa-window-restore',
            'show'   => 1,
            'height' => 250,
            'src'    => 'cdn.bannersnack.com/banners/bxmldll37/embed/index.html?userId=30149291&t=1499779573'
        ),
        /*
        'counter'   => array(
            'type'  => 'counter',
            'label' => _('Counter'),
            'icon'  => 'fa-sort-numeric-down',

            'show'    => 1,
            'columns' => array(
                array(
                    'label'  => 'Projects',
                    'number' => 270,
                    'link'   => ''
                ),
                array(
                    'label'  => 'Clients',
                    'number' => 225,
                    'link'   => ''

                ),
                array(
                    'label'  => 'Likes',
                    'number' => 4500,
                    'link'   => ''

                ),
                array(
                    'label'  => 'Days',
                    'number' => 365,
                    'link'   => ''

                )

            )
        ),

        */
        'telephone' => array(
            'type'       => 'telephone',
            'label'      => _('Phone'),
            'icon'       => 'fa-phone',
            'show'       => 1,
            '_title'     => 'Need help? Ready to Help you with Whatever you Need',
            '_telephone' => '+88 123 456 7890',
            '_text'      => 'Answer Desk is Ready!',
        ),
        'map'       => array(
            'type'  => 'map',
            'label' => _('Map'),
            'icon'  => 'fa-map-marker-alt',
            'show'  => 1,
            'src'   => '#map'
        ),
        'products'  => array(
            'type'              => 'products',
            'auto'              => false,
            'auto_scope'        => 'webpage',
            'auto_items'        => 5,
            'auto_last_updated' => '',
            'label'             => _('Products'),
            'icon'              => 'fa-window-restore',
            'show'              => 1,
            'top_margin'        => 0,
            'bottom_margin'     => 0,
            'item_headers'      => false,
            'items'             => array(),
            'sort'              => 'Manual',
            'title'             => _('Products'),
            'show_title'        => true


        ),
        'see_also'  => array(
            'type'              => 'see_also',
            'auto'              => true,
            'auto_scope'        => 'webpage',
            'auto_items'        => 5,
            'auto_last_updated' => '',
            'label'             => _('See also'),
            'icon'              => 'fa-link',
            'show'              => 1,
            'top_margin'        => 0,
            'bottom_margin'     => 0,
            'item_headers'      => false,
            'items'             => array(),
            'sort'              => 'Manual',
            'title'             => _('See also'),
            'show_title'        => true
        ),

        'not_found'   => array(
            'type'          => 'not_found',
            'label'         => _('Not found'),
            'icon'          => 'fa-times-octagon',
            'show'          => 1,
            'top_margin'    => 40,
            'bottom_margin' => 60,
            'labels'        => array(
                '_strong_title' => '404',
                '_title'        => _('Oops... Page Not Found!'),
                '_text'         => _('Sorry the page could not be found here.'),
                '_home_guide'   => _('Try using the button below to go to main page of the site'),
                '_home_label'   => _('Go to homepage'),

            )
        ),
        'offline'     => array(
            'type'          => 'offline',
            'label'         => _('Offline page'),
            'icon'          => 'fa-ban',
            'show'          => 1,
            'top_margin'    => 40,
            'bottom_margin' => 60,
            'labels'        => array(
                '_strong_title' => '410',
                '_title'        => _('Oops... This page is gone!'),
                '_text'         => _('Sorry this page has been removed.'),
                '_home_guide'   => _('Try using the button below to go to main page of the site'),
                '_home_label'   => _('Go to homepage'),

            )
        ),
        'profile'     => array(
            'type'          => 'profile',
            'label'         => _('Profile'),
            'icon'          => 'fa-user',
            'show'          => 1,
            'top_margin'    => 40,
            'bottom_margin' => 60,
            'labels'        => array(

                '_customer_orders_title'  => _("Customer <i>Orders</i>"),
                '_customer_profile_title' => _("Customer <i>Profile</i>"),


                '_current_order_title' => _('Current order'),
                '_last_order_title'    => _('Last order'),
                '_orders_title'        => _('Orders'),


                '_contact_details_title' => _('Contact details'),
                '_email_placeholder'     => _('Email address'),
                '_email_label'           => _('Email address (this is also your login name)'),
                '_email_tooltip'         => _('Needed to login to your account'),


                '_login_details_title' => _('Login details'),

                '_invoice_address_title'      => _('Invoice address'),
                '_invoice_address_save_label' => _('Save'),

                '_delivery_addresses_title'                 => _('Delivery address'),
                '_delivery_addresses_same_as_invoice_label' => _('Deliver to invoice address'),

                '_delivery_addresses_save_label' => _('Save'),


                '_password_placeholder' => _('New password'),
                '_password_label'       => _('Password'),

                '_password_tooltip' => _("Write new password"),

                '_password_confirm_placeholder' => _('Confirm new password'),
                '_password_confirm_label'       => _('Confirm new password'),

                '_password_conform_tooltip' => _("Don't forget your password"),

                '_mobile_placeholder' => _('Mobile'),
                '_mobile_label'       => _('Mobile'),

                '_mobile_tooltip' => _('Needed to enter your mobile/telephone'),

                '_contact_name_placeholder' => _('Contact name'),
                '_contact_name_label'       => _('Contact name'),

                '_contact_name_tooltip' => _('Needed to enter your name'),

                '_company_placeholder' => _('Company'),
                '_company_label'       => _('Company'),

                '_company_tooltip' => _('Enter your account company name (optional)'),

                '_tax_number_placeholder' => _('VAT number'),
                '_tax_number_label'       => _('VAT number'),

                '_tax_number_tooltip' => _('Enter your VAT number (optional)'),

                '_registration_number_placeholder' => _('Registration number'),
                '_registration_number_label'       => _('Registration number'),

                '_registration_number_tooltip' => _('Enter your company registration number (optional)'),


                '_save_contact_details_label'          => _('Save'),
                '_save_login_details_label'            => _('Save'),
                '_save_invoice_address_details_label'  => _('Save'),
                '_save_delivery_address_details_label' => _('Save'),


                '_username_info' => _('Your username is your email address')


            )
        ),
        'unsubscribe' => array(
            'type'          => 'unsubscribe',
            'label'         => _('Unsubscribe'),
            'icon'          => 'fa-comment-slash',
            'show'          => 1,
            'top_margin'    => 40,
            'bottom_margin' => 60,
            'labels'        => array(
                '_unsubscribe_title'      => _('Email subscriptions'),
                '_unsubscribe_text'       => _('Select which kind of emails you want to receive from us'),
                '_save_unsubscribe_label' => _('Save'),
                '_newsletter'             => _('Newsletter'),
                '_marketing_emails'       => _('Marketing emails and special offers'),
                '_unsubscribe_error_msg'=>_('Sorry, we could not access your record, please login to you account and unsubscribe in your profile section or contact our customer services'),
                '_unsubscribe_error_login_link'=>_('Login'),

                  '_unsubscribe_error_logged_in_msg'=>_('Oops..., that link is not working properly, please click link below to unsubscribe'),
                '_unsubscribe_error_profile_link'=>_('Profile')



            )
        ),
        'favourites'  => array(
            'type'          => 'favourites',
            'label'         => _('Favourites'),
            'icon'          => 'fa-heart',
            'show'          => 1,
            'top_margin'    => 40,
            'bottom_margin' => 60,
            'labels'        => array(
                'with_items' => '<h1>'._('My favourites').'</h1><p>'._('Here you can see your favourites').'</p>',
                'no_items'   => '<h1>'._('My favourites').'</h1><p>'._('You still have no favourites').'</p>',
            )
        ),

        'checkout' => array(
            'type'          => 'checkout',
            'label'         => _('Checkout'),
            'icon'          => 'fa-credit-card',
            'show'          => 1,
            'top_margin'    => 40,
            'bottom_margin' => 60,
            'labels'        => array(


                '_credit_card_label' => _('Credit card'),
                '_bank_label'        => _('Bank transfer'),

                '_credit_card_number'                      => _('Card number'),
                '_credit_card_ccv'                         => _('CVV'),
                '_credit_card_expiration_date'             => _('Expiration date'),
                '_credit_card_expiration_date_month_label' => _('Month'),
                '_credit_card_expiration_date_year_label'  => _('Year'),
                '_credit_card_save'                        => _('Save card'),


                '_form_title_credit_card'          => _('Checkout form'),
                '_form_title_paypal'               => _('Checkout form'),
                '_form_title_cond'                 => _('Checkout form'),
                '_form_title_sofort'               => _('Checkout form'),
                '_form_title_bank'                 => _('Checkout form'),
                '_form_title_other'                => _('Checkout form'),
                '_form_title_online_bank_transfer' => _('Checkout form'),
                '_form_title_cash_on_delivery'     => _('Checkout form'),


                '_bank_header' => _('Please go to your bank and make a payment of <b>[Order Amount]</b>  to our bank account, details below'),
                '_bank_footer' => _('Remember to state the order number in the payment reference').' [Order Number] . '._(
                        'Please note, we cannot process your order until payment arrives in our account'
                    ),


                '_back_to_basket' => _('Go back to basket'),

                '_place_order'                           => _('Place order'),
                '_place_order_from_bank'                 => _('Place order'),
                '_place_order_from_credit_card'          => _('Place order'),
                '_place_order_from_paypal'               => _('Place order'),
                '_place_order_from_cash_on_delivery'     => _('Place order'),
                '_place_order_from_online_bank_transfer' => _('Place order'),


            )
        ),

        'thanks' => array(
            'type'          => 'thanks',
            'label'         => _('Thanks'),
            'icon'          => 'fa-thumbs-up',
            'show'          => 1,
            'top_margin'    => 40,
            'bottom_margin' => 60,
            'text'          => '<h1 >'._('Thank you for your order').'</h1><p>'._('Thank you!  We are delighted to receive your order').'</p><p>[Pay Info]</p><p>'._('Your order details are listed below, if you have any questions please email our team')
                .'</p><p>[Order]</p>'
        ),
        'search' => array(
            'type'          => 'search',
            'label'         => _('search'),
            'icon'          => 'fa-search',
            'show'          => 1,
            'top_margin'    => 40,
            'bottom_margin' => 60,
            'labels'        => array()

        ),

    );


    return $blocks;

}

?>
