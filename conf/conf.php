<?php


$report_data=array('ES1'=>array('umbral'=>3000,'year'=>date('Y')-1));


$myconf=array(

            'tax_rates'=>array(),
            'data_from'=>"2003-06-01 09:00:00",
            'order_id_type'=>'Order Header Numeric ID',
            'customer_min_number_zeros_id'=>4,
            'contact_min_number_zeros_id'=>4,
            'company_min_number_zeros_id'=>4,
            'supplier_min_number_zeros_id'=>3,
            'staff_min_number_zeros_id'=>3,

            'max_session_time'=>36000,
            'name'=>'Kaktus',
            'sname'=>'AW',
            'country'=>'UK',
            'country_code'=>'GBR',
            'country_2acode'=>'GB',
            'lang'=>'en',
            'country_id'=>30,
            'home_id'=>30,
            'extended_home_id'=>array(30,241,240,242),
            'extended_home_2acode'=>array('GB','GG','JE','IM'),
            'region_id'=>array(75,30,241,240,242),
            'region_2acode'=>array('GB','GG','JE','IM'),
            'org_id'=>array(80,21,33,208,108,201,228,193,165,177,104,216,75,78,110,116,117,126,2,162,160,169,188,189,47,171,30),
            'org_2acode'=>array('NL','BE','GB','BG','ES','IE','IT','AT','GR','CY','LV','LT','LU','MT','PT','PL','FR','RO','SE','DE','SK','SI','FI','DK','CZ','HU','EE'),
            'tax_obligatory'=>array('GB'),

            'tax_conditional0'=>array(80,21,33,208,108,201,228,193,165,177,104,216,75,78,110,116,117,126,2,162,160,169,188,189,47,171),
            'tax_conditional0_2acode'=>array('NL','BE','BG','ES','IE','IT','AT','GR','CY','LV','LT','LU','MT','PT','PL','FR','RO','SE','DE','SK','SI','FI','DK','CZ','HU','EE'),
            'continent_id'=>array(228,110,116,242,241,240,30,75,33,188,135,169,208,215,216,226,162,221,70,171,193,149,53,76,201,181,243,189,160,78,47,86,104,105,27,121,126,7,224,4,58,21,136,2,80,117,177,115,165,196),
            'continent_2acode'=>array('NL','AL','AD','BE','BA','GB','BG','ES','FO','GI','SJ','IE','IS','IT','AT','YU','GR','HR','LV','LI','LT','LU','MK','MT','MD','MC','NO','PT','PL','FR','RO','SE','DE','SM','SK','SI','FI','CH','DK','CZ','UA','HU','BY','VA','RU','EE','GG','JE','IM'),

            'home'=>'United Kingdom',
            '_home'=>'UK',

            's_extended_home'=>'UK,CI&IM',
            'extended_home'=>'United Kigndom, Channel Islands & Isle of Man',


            's_extended_home_nohome'=>'CI&IM',
            'extended_home_nohome'=>'Channel Islands & Isle of Man',
            'region'=>'British Isles',
            'org'=>'European Union',
            's_org'=>'EU',
            'continent'=>'Europe',
            'outside'=>'Ouside Europe',
            's_outside'=>'Rest',
            'encoding'=>'UTF-8',
            'currency_symbol'=>'£',

            'currency_code'=>'GBP',
            'currency'=>'Pound',
            'decimal_point'=>'.',
            'thousand_sep'=>',',

            'theme'=>'yui-skin-sam',
            'template_dir'=>'templates',
            'compile_dir'=> 'server_files/smarty/templates_c',
            'cache_dir' => 'server_files/smarty/cache',
            'config_dir' => 'server_files/smarty/configs',
            'images_dir' => 'server_files/images/',
            'yui_version'=>'2.8.1',
            'staff_prefix'=>'SF',
            'supplier_id_prefix'=>'S',
            'po_id_prefix'=>'PO',
            'invoice_id_prefix'=>'I',

            'customer_id_prefix'=>'C',
            'contact_id_prefix'=>'p',
            'company_id_prefix'=>'B',
            'dn_id_prefix'=>'NE',
            'order_id_prefix'=>'',
            'data_since'=>'14-06-2004',
            'product_code_separator'=>'-',
            'unknown_company'=>'Unknown Company',
            'unknown_contact'=>'Unknown Contact',
            'unknown_customer'=>'Unknown Customer',
            'unknown_supplier'=>'Unknown Supplier',
            'unknown_informal_greting'=>'Hello',
            'unknown_formal_greting'=>'Dear Sir or Madam'
        );




$default_state=array(
                   'export'=>'xls',
                   'home'=>array(
                              'display'=>'top_customers',
                              'splinters'=>array(
                                              'top_products'=>array('nr'=>20,'period'=>'all','order'=>'net_sales','order_dir'=>'desc'),
                                              'top_customers'=>array('nr'=>20,'period'=>'all','order'=>'net_balance','order_dir'=>'desc'),
                                              'orders_in_process'=>array(
                                                                      'store_keys'=>'all',
                                                                      'sf'=>0,
                                                                      'nr'=>50,
                                                                      'f_value'=>'',
                                                                      'f_show'=>false,
                                                                      'f_field'=>'customer',
                                                                      'from'=>'',
                                                                      'to'=>'',
                                                                      'order'=>'date',
                                                                      'order_dir'=>'',
                                                                      'where'=>''
                                                                  ),
                                              'messages'=>array()
                                          )
                          ),
                   'report_data'=>$report_data,
                   'porder'=>array(
                                'id'=>'',
                                'show_all'=>false, 'supplier_key'=>0,
                                'view'=>'used_in',
                                'products'=>array(
                                               'order'=>'code',
                                               'order_dir'=>'',
                                               'sf'=>0,
                                               'nr'=>25,
                                               'where'=>'where true',
                                               'f_field'=>'code',
                                               'f_value'=>'',2,'f_show'=>false,
                                               'from'=>'',
                                               'to'=>''
                                           )
                            ),
                   'supplier_dn'=>array(
                                     'id'=>'',
                                     'show_all'=>false,
                                     'supplier_key'=>0,
                                     'pos'=>'',
                                     'view'=>'used_in',
                                     'products'=>array(
                                                    'order'=>'code',
                                                    'order_dir'=>'',
                                                    'sf'=>0,
                                                    'nr'=>25,
                                                    'where'=>'where true',
                                                    'f_field'=>'code',
                                                    'f_value'=>'','f_show'=>false,
                                                    'from'=>'',
                                                    'to'=>''
                                                )
                                 ),
                   'supplier_invoice'=>array(
                                          'id'=>'',
                                          'show_all'=>false,
                                          'supplier_key'=>0,
                                          'pos'=>'',
                                          'view'=>'used_in',
                                          'products'=>array(
                                                         'order'=>'code',
                                                         'order_dir'=>'',
                                                         'sf'=>0,
                                                         'nr'=>25,
                                                         'where'=>'where true',
                                                         'f_field'=>'code',
                                                         'f_value'=>'','f_show'=>false,
                                                         'from'=>'',
                                                         'to'=>''
                                                     )
                                      ),
                   'porders'=>array(
                                 'id'=>'',
                                 'show_all'=>false,
                                 'parent'=>'supplier',
                                 'parent_key'=>0,
                                 'table'=>array(
                                             'order'=>'date',
                                             'view'=>'general',
                                             'order_dir'=>'',
                                             'sf'=>0,
                                             'nr'=>25,
                                             'where'=>'where true',
                                             'f_field'=>'public_id',
                                             'f_value'=>'','f_show'=>false,
                                             'from'=>'',
                                             'to'=>''
                                         )
                             ),
                   'supplier_invoices'=>array(
                                           'id'=>'',
                                           'show_all'=>false,
                                           'parent'=>'supplier',
                                           'parent_key'=>0,
                                           'table'=>array(
                                                       'order'=>'date',
                                                       'view'=>'general',
                                                       'order_dir'=>'',
                                                       'sf'=>0,
                                                       'nr'=>25,
                                                       'where'=>'where true',
                                                       'f_field'=>'public_id',
                                                       'f_value'=>'','f_show'=>false,
                                                       'from'=>'',
                                                       'to'=>''
                                                   )
                                       ),
                   'supplier_dns'=>array(
                                      'id'=>'',
                                      'show_all'=>false,
                                      'parent'=>'supplier',
                                      'parent_key'=>0,
                                      'table'=>array(
                                                  'order'=>'date',
                                                  'view'=>'general',
                                                  'order_dir'=>'',
                                                  'sf'=>0,
                                                  'nr'=>25,
                                                  'where'=>'where true',
                                                  'f_field'=>'public_id',
                                                  'f_value'=>'','f_show'=>false,
                                                  'from'=>'',
                                                  'to'=>''
                                              )
                                  ),


                   'dn'=>array(
                            'id'=>''),
                   'order'=>array(
                               'id'=>'',
                               'show_all'=>false,
                               'store_key'=>0,
                               'all_products'=>array(
                                                  'order'=>'code',
                                                  'order_dir'=>'',
                                                  'sf'=>0,
                                                  'nr'=>25,
                                                  'where'=>'where true',
                                                  'f_field'=>'code',
                                                  'f_value'=>'','f_show'=>false,
                                                  'from'=>'',
                                                  'to'=>''
                                              )
                           ),

                   'marketing'=>array(

                                   'table'=>array(
                                               'order'=>'date',
                                               'order_dir'=>'desc',
                                               'sf'=>0,
                                               'nr'=>25,
                                               'where'=>'where true',
                                               'f_field'=>'subject',
                                               'f_value'=>'','f_show'=>false,
                                               'from'=>'',
                                               'to'=>''
                                           )
                               ),
                   'reports'=>array(
                                 'view'=>'sales',



                                 'sales'=>array(
                                             'plot'=>'total_sales_month',
                                             'store_key'=>1,
                                             'tipo'=>'y',
                                             'y'=>date('Y'),
                                             'm'=>date('m'),
                                             'd'=>date('d'),
                                             'w'=>date('W'),
                                         )
                                         ,'stock'=>array(
                                                      'plot'=>'total_outofstock_month'
                                                  )
                                                  ,'geosales'=>array(
                                                                  'level'=>'region',
                                                                  'region'=>'world',
                                                                  'map_exclude'=>'',
                                                                  'table'=>array(
                                                                              'order'=>'country_code',
                                                                              'order_dir'=>'',
                                                                              'sf'=>0,
                                                                              'nr'=>25,
                                                                              'where'=>'where true',
                                                                              'f_field'=>'country',
                                                                              'f_value'=>'','f_show'=>false,
                                                                              'from'=>'',
                                                                              'to'=>''

                                                                          ),
                                                              )

                             ),



                   'orders'=>array(
                                'details'=>false,
                                'store'=>'',
                                'view'=>'orders',
                                'only'=>'',
                                'from'=>'',
                                'to'=>'',

                                'table'=>array(
                                            'order'=>'last_date',
                                            'order_dir'=>'desc',
                                            'sf'=>0,
                                            'nr'=>25,
                                            'where'=>'where true',
                                            'f_field'=>'customer_name',
                                            'f_value'=>'','f_show'=>false,
                                            'from'=>'',
                                            'to'=>'',
                                            'elements'=>array(),
                                            'dispatch'=>'all_orders',
                                            'paid'=>'',
                                            'order_type'=>'',
// --------------------------------------------export csv for order list in order page -----------------------------
                                            'csv_export'=>array(
                                                             'code'=>true,
                                                             'last_date'=>true,
                                                             'customer'=>true,
                                                             'status'=>true,
                                                             'totaltax'=>false,
                                                             'totalnet'=>false,
                                                             'total'=>true,
                                                             'balancenet'=>false,
                                                             'balancetax'=>false,
                                                             'balancetotal'=>false,
                                                             'outstandingbalancenet'=>false,
                                                             'outstandingbalancetax'=>false,
                                                             'outstandingbalancetotal'=>false,
                                                             'contactname'=>false,
                                                             'sourcetype'=>false,
                                                             'paymentstate'=>false,
                                                             'actiontaken'=>false,
                                                             'ordertype'=>false,
                                                             'shippingmethod'=>false


                                                         )
// -----------------------------------------------------------------------------------------------------------------



                                        ),
                                'invoices'=>array(
                                               'order'=>'date',
                                               'order_dir'=>'',
                                               'sf'=>0,
                                               'nr'=>25,
                                               'invoice_type'=>'all',
                                               'where'=>'where true',
                                               'f_field'=>'public_id',
                                               'f_value'=>'','f_show'=>false,
                                               'from'=>'',
                                               'to'=>'',
                                               'elements'=>array(),
                                               'csv_export'=>array(
                                                                'code'=>true,
                                                                'date'=>true,
                                                                'name'=>true,
                                                                'paymentmethod'=>false,
                                                                'invoicefor'=>false,
                                                                'invoicepaid'=>false,

                                                                'invoice_total_amount'=>true,
                                                                'invoice_total_profit'=>false,
                                                                'invoice_total_tax_amount'=>false,
                                                                'invoice_total_tax_adjust_amount'=>false,
                                                                'invoice_total_adjust_amount'=>false



                                                            )
                                           ),
                                'dn'=>array(
                                         'order'=>'date',
                                         'order_dir'=>'',
                                         'sf'=>0,
                                         'nr'=>25,
                                         'where'=>'where true',
                                         'f_field'=>'public_id',
                                         'f_value'=>'','f_show'=>false,
                                         'from'=>'',
                                         'to'=>'',
                                         'dn_state_type'=>'all',
                                         'elements'=>array(),
                                         'csv_export'=>array(
                                                          'id'=>true,
                                                          'date'=>true,
                                                          'type'=>true,
                                                          'customer_name'=>true,
                                                          'weight'=>false,
                                                          'parcels_no'=>false,

                                                          'start_picking_date'=>false,
                                                          'finish_picking_date'=>false,
                                                          'start_packing_date'=>false,
                                                          'finish_packing_date'=>false,
                                                          'state'=>false,
                                                          'dispatched_method'=>false,
                                                          'parcel_type'=>false,
                                                          'boxes_no'=>false



                                                      )
                                     )
                                     ,'ready_to_pick_dn'=>array(
                                                             'order'=>'date',
                                                             'order_dir'=>'',
                                                             'sf'=>0,
                                                             'nr'=>25,
                                                             'where'=>'where true',
                                                             'f_field'=>'public_id',
                                                             'f_value'=>'','f_show'=>false,
                                                             'from'=>'',
                                                             'to'=>'',
                                                             'elements'=>array(),
                                         'csv_export'=>array(
                                                          'id'=>true,
                                                          'date'=>true,
                                                          'type'=>true,
                                                          'customer_name'=>false,
                                                          'weight'=>false,
                                                          'picks'=>false,
							  'parcel_type'=>false
                                                      )
                                                         )


                            ),

                   'product_categories'=>array(
                                            'category'=>0,
                                            'from'=>'',
                                            'to'=>'',
                                            'period'=>'year',
                                            'percentages'=>0,
                                            'mode'=>'all',
                                            'avg'=>'totals',
                                            'view'=>'general',
                                            'from'=>'',
                                            'to'=>'',
                                            'exchange_type'=>'day2day',
                                            'stores'=>'all',
                                            'stores_mode'=>'grouped',
                                            'exchange_value'=>1,
                                            'show_default_currency'=>false,
                                            'subcategories'=>array(

                                                                'order'=>'name',
                                                                'order_dir'=>'',
                                                                'sf'=>0,
                                                                'nr'=>1000,
                                                                'where'=>'where true',
                                                                'f_field'=>'name',
                                                                'f_value'=>'','f_show'=>false,

                                                            ),
                                            'products'=>array(

                                                           'order'=>'code',
                                                           'order_dir'=>'',
                                                           'sf'=>0,
                                                           'nr'=>25,
                                                           'where'=>'where true',
                                                           'f_field'=>'code',
                                                           'f_value'=>'','f_show'=>false,

                                                       ),
                                        ),

                   'products'=>array(
                                  'details'=>false,
                                  'percentages'=>false,
                                  'view'=>'general',
                                  'from'=>'',
                                  'to'=>'',
                                  'period'=>'year',
                                  'percentage'=>0,
                                  'mode'=>'same_code',//same_code,same_id,all
                                  'parent'=>'none',//store,dement,family,none
                                  'restrictions'=>'forsale',
                                  'avg'=>'totals',
                                  'table'=>array(
                                              'order'=>'code',
                                              'order_dir'=>'',
                                              'sf'=>0,
                                              'nr'=>25,
                                              'where'=>'where true',
                                              'f_field'=>'code',
                                              'f_value'=>'','f_show'=>false,
                                              'from'=>'',
                                              'to'=>'',
                                              'elements'=>array(),
                                              'mode'=>'same_code',//same_code,same_id,all
                                              'parent'=>'none',//store,department,family,none
                                              'restrictions'=>'forsale',
                                              'family_code'=>'',
                                              'csv_export'=>array(
                                                               'code'=>true,
                                                               'name'=>true,
                                                               'state'=>true,

                                                               'web'=>false,
                                                               'product_short_description'=>false,
                                                               'product_total_invoiced_amount'=>false,
                                                               'product_total_profit'=>false,
                                                               'product_1y_acc_invoiced_amount'=>false,
                                                               'product_1y_acc_profit_amount'=>false,
                                                               'product_1q_acc_invoiced_amount'=>false,
                                                               'product_1q_acc_profit_amount'=>false,

                                                           )
                                          ),
                              ),

                   'supplier_product'=>array(
                                          'display'=>'',
                                          'supplier_key'=>0,
                                          'supplier_code'=>'',
                                          'editing'=>'prices',
                                          'porders'=>array(
                                                        'order'=>'date',
                                                        'view'=>'general',
                                                        'order_dir'=>'',
                                                        'sf'=>0,
                                                        'nr'=>25,
                                                        'where'=>'where true',
                                                        'f_field'=>'public_id',
                                                        'f_value'=>'',
                                                        'f_show'=>false,
                                                        'from'=>'',
                                                        'to'=>''

                                                    ),
                                                   'history'=>array(
                                               'where'=>'where true',
                                               'f_field'=>'abstract',
                                               'f_value'=>'','f_show'=>false,
                                               'order'=>'date',
                                               'order_dir'=>'desc',
                                               'sf'=>0,
                                               'nr'=>25,
                                               'from'=>'',
                                               'to'=>'',
                                               'elements'=>''
                                           )  
                                      ),



                   'supplier_products'=>array(
                                           'details'=>false,
                                           'percentages'=>false,
                                           'view'=>'general',
                                           'from'=>'',
                                           'to'=>'',
                                           'period'=>'year',
                                           'percentage'=>0,
                                           'mode'=>'',
                                           'avg'=>'totals',
                                           'table'=>array(
                                                       'order'=>'code',
                                                       'order_dir'=>'',
                                                       'sf'=>0,
                                                       'nr'=>25,
                                                       'where'=>'where true',
                                                       'f_field'=>'sup_code',
                                                       'f_value'=>'','f_show'=>false,
                                                       'from'=>'',
                                                       'to'=>'',
                                                       'elements'=>array()
                                                   )
                                       ),

                   'report_pp'=>array(

                                   'warehouse_key'=>1
                                                   ,'tipo'=>'y'
                                                           ,'y'=>date('Y')
                                                                ,'m'=>date('m')
                                                                     ,'d'=>date('d')
                                                                          ,'w'=>date('W')
                               ),


                   'report_sales'=>array(
                                      'tipo'=>'m',
                                      'y'=>date('Y'),
                                      'm'=>date('m'),
                                      'd'=>date('d'),
                                      'w'=>1,
                                      'activity'=>array('compare'=>'last_year','period'=>'week'),

                                      'store_keys'=>'all',
                                      'from'=>'',
                                      'to'=>'',
                                      'period'=>'',
                                      'order'=>'date',
                                      'order_dir'=>'desc',
                                      'currency'=>'stores',
                                      'view'=>'invoices',
                                      'sf'=>0,
                                      'nr'=>25,
                                      'plot'=>'per_store',
                                      'plot_data'=>array('per_store'=>array(
                                                                         'category'=>'sales',
                                                                         'page'=>'plot.php',
                                                                         'period'=>'m'

                                                                     )
                                                                     ,'per_category'=>array(
                                                                                         'category'=>'sales',
                                                                                         'page'=>'plot.php',
                                                                                         'period'=>'m'
                                                                                     )
                                                        ),
                                  ),
                   'report_first_order'=>array(
                                            'tipo'=>'y',
                                            'y'=>date('Y'),
                                            'm'=>date('m'),
                                            'd'=>date('d'),
                                            'w'=>date('W'),
                                            'department_key'=>false,
                                            'share'=>.8,
                                            'from'=>'',
                                            'to'=>'',
                                            'products'=>array(
                                                           'order'=>'date',
                                                           'order_dir'=>'',
                                                           'sf'=>0,
                                                           'nr'=>30,
                                                           'where'=>'where true',
                                                           'f_field'=>'',
                                                           'f_value'=>'',
                                                           'f_show'=>false,

                                                           'elements'=>array()
                                                       )


                                        ),

                   'report_sales_with_no_tax'=>array(
                                                  'tipo'=>'y',
                                                  'y'=>date('Y'),
                                                  'm'=>date('m'),
                                                  'd'=>date('d'),
                                                  'w'=>date('W'),
                                                  'stores'=>false,
                                                  'currency_type'=>'original',
                                                  'invoices'=>array(
                                                                 'order'=>'date',
                                                                 'order_dir'=>'',
                                                                 'sf'=>0,
                                                                 'nr'=>25,
                                                                 'where'=>'where true',
                                                                 'f_field'=>'public_id',
                                                                 'f_value'=>'',
                                                                 'f_show'=>false,
                                                                 'from'=>'',
                                                                 'to'=>'',
                                                                 'elements'=>array()
                                                             )
                                                             ,'customers'=>array(
                                                                              'order'=>'name',
                                                                              'order_dir'=>'',
                                                                              'sf'=>0,
                                                                              'nr'=>25,
                                                                              'where'=>'where true',
                                                                              'f_field'=>'customer',
                                                                              'f_value'=>'',
                                                                              'f_show'=>false,
                                                                              'from'=>'',
                                                                              'to'=>'',
                                                                              'elements'=>array()
                                                                          )

                                              ),

                   'report_outofstock'=>array(
                                           'from'=>'',
                                           'to'=>'',
                                           'table'=>array(
                                                       'order'=>'code',
                                                       'order_dir'=>'',
                                                       'sf'=>0,
                                                       'nr'=>25,
                                                       'where'=>'where true',
                                                       'f_field'=>'code',
                                                       'f_value'=>'','f_show'=>false,
                                                       'elements'=>array()
                                                   )
                                       ),



                   'warehouse'=>array(
                                   'id'=>1,
                                   'edit'=>'description',




                                   'display'=>array('details'=>0,'plot'=>1,'orders'=>0,'customers'=>0,'stock_history'=>0),

                                   'stock_history'=>array(
                                                       'order'=>'date',
                                                       'order_dir'=>'desc',
                                                       'sf'=>0,
                                                       'nr'=>15,
                                                       'type'=>'week',
                                                       'where'=>'where true',
                                                       'f_field'=>'location',
                                                       'f_value'=>'',
                                                       'f_show'=>false,
                                                       'from'=>'',
                                                       'to'=>'',
                                                       'elements'=>array()
                                                   ),
                                   'transactions'=>array(
                                                      'order'=>'date',
                                                      'order_dir'=>'desc',
                                                      'sf'=>0,
                                                      'nr'=>15,
                                                      'where'=>'where true',
                                                      'f_field'=>'note',
                                                      'f_value'=>'',
                                                      'f_show'=>false,
                                                      'from'=>'',
                                                      'to'=>'',
                                                      'elements'=>array()
                                                  ),


                               ),
                   'warehouse_stock_history'=>array(


                                                 'plot'=>'part_stock_history',
                                                 'plot_interval'=>array(
                                                                     'y'=>array('plot_bins'=>5,'plot_forecast_bins'=>3),
                                                                     'd'=>array('plot_bins'=>30,'plot_forecast_bins'=>5),
                                                                     'q'=>array('plot_bins'=>12,
                                                                                'plot_forecast_bins'=>3),
                                                                     'm'=>array('plot_bins'=>18,
                                                                                'plot_forecast_bins'=>3),
                                                                     'w'=>array('plot_bins'=>26,
                                                                                'plot_forecast_bins'=>3),
                                                                 ),
                                                 'plot_period'=>'m',
                                                 'plot_category'=>'stock',



                                                 'table'=>array(
                                                             'order'=>'date',
                                                             'order_dir'=>'desc',
                                                             'sf'=>0,
                                                             'nr'=>15,
                                                             'type'=>'week',
                                                             'where'=>'where true',
                                                             'f_field'=>'author',
                                                             'f_value'=>'',
                                                             'f_show'=>false,
                                                             'from'=>'',
                                                             'to'=>'',
                                                             'elements'=>array()
                                                         ),

                                                 'plot_data'=>array('part_stock_history'=>array(
                                                                                             'label'=>_('Product Sales')
                                                                                                     ,'page'=>'plot.php'
                                                                                         ),

                                                                    'part_out'=>array(
                                                                                   'label'=>_('Stock History')
                                                                                           ,'parts'=>'plot.php'
                                                                               )
                                                                   ),






                                             ),

                   'locations'=>array(
                                   'parent'=>'none',
                                   'table'=>array(
                                               'order'=>'code',
                                               'order_dir'=>'',
                                               'sf'=>0,
                                               'nr'=>25,
                                               'where'=>'where true',
                                               'f_field'=>'code',
                                               'f_value'=>'','f_show'=>false,
                                               'elements'=>array()
                                           )

                               ),
                   'shelfs'=>array(
                                'parent'=>'none',
                                'table'=>array(
                                            'order'=>'code',
                                            'order_dir'=>'',
                                            'sf'=>0,
                                            'nr'=>25,
                                            'where'=>'where true',
                                            'f_field'=>'code',
                                            'f_value'=>'','f_show'=>false,
                                            'elements'=>array()
                                        )

                            ),
                   'warehouses'=>array(

                                    'table'=>array(
                                                'order'=>'code',
                                                'order_dir'=>'',
                                                'sf'=>0,
                                                'nr'=>50,
                                                'where'=>'where true',
                                                'f_field'=>'code',
                                                'f_value'=>'','f_show'=>false,
                                                'elements'=>array()
                                            )


                                ),
                   'warehouse_areas'=>array(
                                         'parent'=>'none',
                                         'table'=>array(
                                                     'order'=>'code',
                                                     'order_dir'=>'',
                                                     'sf'=>0,
                                                     'nr'=>50,
                                                     'where'=>'where true',
                                                     'f_field'=>'code',
                                                     'f_value'=>'','f_show'=>false,
                                                     'elements'=>array()
                                                 )


                                     ),
                   'warehouse_area'=>array(
                                        'id'=>1

                                    ),
                   'shelf_types'=>array(
                                     'view'=>'general',
                                     'table'=>array(
                                                 'order'=>'name',
                                                 'order_dir'=>'',
                                                 'sf'=>0,
                                                 'nr'=>25,
                                                 'where'=>'where true',
                                                 'f_field'=>'name',
                                                 'f_value'=>'','f_show'=>false,
                                                 'elements'=>array()
                                             )
                                 ),
                   'shelf_location_types'=>array(

                                              'table'=>array(
                                                          'order'=>'name',
                                                          'order_dir'=>'',
                                                          'sf'=>0,
                                                          'nr'=>25,
                                                          'where'=>'where true',
                                                          'f_field'=>'name',
                                                          'f_value'=>'','f_show'=>false,
                                                          'elements'=>array()
                                                      )
                                          ),
                   'shelfs'=>array(

                                'table'=>array(
                                            'order'=>'code',
                                            'order_dir'=>'',
                                            'sf'=>0,
                                            'nr'=>25,
                                            'where'=>'where true',
                                            'f_field'=>'code',
                                            'f_value'=>'','f_show'=>false,
                                            'elements'=>array()
                                        )
                            ),

                   'customers'=>array(
                                   'store'=>'',
                                   'view'=>'general',
                                   'details'=>0,
                                   'plot'=>'active_customers',
                                   'plot_data'=>array(
                                                   'active_customers'=>array(
                                                                          'period'=>'m',
                                                                          'category'=>'total',
                                                                          'page'=>'plot.php'
                                                                      ),
                                                   'customers'=>array(
                                                                   'period'=>'m',
                                                                   'category'=>'total',
                                                                   'page'=>'plot.php'
                                                               )

                                               ),
                                   'table'=>array(
                                               'order'=>'name',
                                               'order_dir'=>'last_orders',
                                               'sf'=>0,
                                               'nr'=>25,
                                               'type'=>'all_contacts',

                                               'where'=>'where true',
                                               'f_field'=>'customer name',
                                               'f_value'=>'',

                                               'csv_export'=>array(
                                                                'id'=>true,
                                                                'name'=>true,
                                                                'location'=>true,
                                                                'last_orders'=>true,
                                                                'orders'=>true,
                                                                'status'=>true
                                                                         /* 'surplus'=>false,
                                                                          'ok'=>false,
                                                                          'low'=>false,
                                                                          'critical'=>false,
                                                                          'gone'=>false,
                                                                          'unknown'=>false,
                                                                          'sales_all'=>false,
                                                                          'sales_1y'=>false,
                                                                          'sales_1q'=>false,
                                                                          'sales_1m'=>false,
                                                                          'sales_1w'=>false,
                                                                          'profit_all'=>false,
                                                                          'profit_1y'=>false,
                                                                          'profit_1q'=>false,
                                                                          'profit_1m'=>false,
                                                                          'profit_1w'=>false */


                                                            )

                                           ),
                                   'advanced_search'=>array(
                                                         'order'=>'name',
                                                         'order_dir'=>'',
                                                         'sf'=>0,
                                                         'nr'=>25,
                                                         'where'=>'',
                                                         'f_field'=>'',
                                                         'f_value'=>'',
                                                         'view'=>'general'
                                                     )
                                                     ,'list'=>array(
                                                                 'order'=>'name',
                                                                 'order_dir'=>'',
                                                                 'sf'=>0,
                                                                 'nr'=>25,
                                                                 'where'=>'',
                                                                 'f_field'=>'',
                                                                 'f_value'=>'',
                                                                 'view'=>'general'
                                                             )


                               ),
                   'contacts'=>array(
                                  'view'=>'general',
                                  'details'=>0,
                                  'table'=>array(
                                              'order'=>'name',
                                              'order_dir'=>'',
                                              'sf'=>0,
                                              'nr'=>25,
                                              'where'=>'where true',
                                              'f_field'=>'contact name',
                                              'f_value'=>'','f_show'=>false,
                                              'mode'=>'',
                                              'restrictions'=>'none',
                                              'parent'=>''
                                          ),
                                  'advanced_search'=>array(
                                                        'order'=>'name',
                                                        'order_dir'=>'',
                                                        'sf'=>0,
                                                        'nr'=>25,
                                                        'where'=>'',
                                                        'f_field'=>'',
                                                        'f_value'=>''
                                                    )


                              ),
                   'positions'=>array(
                                   'view'=>'general',
                                   'parent'=>'none',
                                   'details'=>0,
                                   'table'=>array(
                                               'order'=>'code',
                                               'order_dir'=>'',
                                               'sf'=>0,
                                               'nr'=>25,
                                               'where'=>'where true',
                                               'f_field'=>'name',
                                               'f_value'=>'','f_show'=>false,
                                               'mode'=>'',
                                               'restrictions'=>'none',
                                               'parent'=>''
                                           )
                               ),
                   'company_departments'=>array(
                                             'view'=>'general',
                                             'parent'=>'none',
                                             'details'=>0,
                                             'table'=>array(
                                                         'order'=>'code',
                                                         'order_dir'=>'',
                                                         'sf'=>0,
                                                         'nr'=>25,
                                                         'where'=>'where true',
                                                         'f_field'=>'company name',
                                                         'f_value'=>'','f_show'=>false,
                                                         'mode'=>'',
                                                         'restrictions'=>'none',
                                                         'parent'=>'',
                                                         'csv_export'=>array(
                                                                          'area'=>true,
                                                                          'code'=>true,
                                                                          'name'=>true,
                                                                          'department_description'=>false,
                                                                          'no_of_department_employee'=>false,
                                                                          'company_area_name'=>false,
                                                                          'company_area_description'=>false,
                                                                      )),
                                         ),
                   'company_areas'=>array(
                                       'view'=>'general',
                                       'parent'=>'none',
                                       'details'=>0,
                                       'table'=>array(
                                                   'order'=>'name',
                                                   'order_dir'=>'',
                                                   'sf'=>0,
                                                   'nr'=>25,
                                                   'where'=>'where true',
                                                   'f_field'=>'company name',
                                                   'f_value'=>'','f_show'=>false,
                                                   'mode'=>'',
                                                   'restrictions'=>'none',
                                                   'parent'=>''
                                               )
                                   ),
                   'companies'=>array(
                                   'view'=>'general',
                                   'details'=>0,
                                   'table'=>array(
                                               'order'=>'name',
                                               'order_dir'=>'',
                                               'sf'=>0,
                                               'nr'=>25,
                                               'where'=>'where true',
                                               'f_field'=>'company name',
                                               'f_value'=>'','f_show'=>false,
                                               'mode'=>'',
                                               'restrictions'=>'none',
                                               'parent'=>''
                                           ),
                                   'advanced_search'=>array(
                                                         'order'=>'name',
                                                         'order_dir'=>'',
                                                         'sf'=>0,
                                                         'nr'=>25,
                                                         'where'=>'',
                                                         'f_field'=>'',
                                                         'f_value'=>''
                                                     )


                               ),

                   'customer'=>array(
                                  'id'=>1,
                                  'action_after_create'=>'continue',
                                  'edit'=>'details',
                                  'details'=>false,

                                  'assets'=>array(
                                               'order'=>'subject',
                                               'order_dir'=>'',
                                               'sf'=>0,
                                               'nr'=>25,
                                               'where'=>'where true',
                                               'f_field'=>'code',
                                               'f_value'=>'','f_show'=>false,
                                               'from'=>'',
                                               'to'=>'',
                                               'type'=>'Family'

                                           ),

                                  'table'=>array(
                                              'order'=>'date',
                                              'order_dir'=>'desc',
                                              'sf'=>0,
                                              'nr'=>10,
                                              'where'=>'where true',
                                              'f_field'=>'notes',
                                              'f_value'=>'','f_show'=>false,
                                              'from'=>'',
                                              'to'=>'',
                                              'details'=>0,
                                              'elements'=>array('orden'=>1,'h_cust'=>1,'h_cont'=>1,'note'=>1)
                                          )
                              ),
                   'company'=>array(
                                 'id'=>1,
                                 'action_after_create'=>'continue',
                                 'table'=>array(
                                             'order'=>'date',
                                             'order_dir'=>'desc',
                                             'sf'=>0,
                                             'nr'=>10,
                                             'where'=>'where true',
                                             'f_field'=>'notes',
                                             'f_value'=>'','f_show'=>false,
                                             'from'=>'',
                                             'to'=>'',
                                             'details'=>0,
                                             'elements'=>array('h_comp'=>1,'h_cont'=>1,'note'=>1)
                                         ),
                                 'history'=>array(
                                               'where'=>'where true',
                                               'f_field'=>'abstract',
                                               'f_value'=>'','f_show'=>false,
                                               'order'=>'date',
                                               'order_dir'=>'desc',
                                               'sf'=>0,
                                               'nr'=>25,
                                               'from'=>'',
                                               'to'=>'',
                                               'elements'=>''
                                           ),


                             ),
                   'contact'=>array(
                                 'id'=>1,
                                 'action_after_create'=>'continue',
                                 'table'=>array(
                                             'order'=>'date',
                                             'order_dir'=>'desc',
                                             'sf'=>0,
                                             'nr'=>10,
                                             'where'=>'where true',
                                             'f_field'=>'notes',
                                             'f_value'=>'','f_show'=>false,
                                             'from'=>'',
                                             'to'=>'',
                                             'details'=>0,
                                             'elements'=>array('h_cont'=>1,'note'=>1)
                                         ),
                                 'history'=>array(
                                               'where'=>'where true',
                                               'f_field'=>'abstract',
                                               'f_value'=>'','f_show'=>false,
                                               'order'=>'date',
                                               'order_dir'=>'desc',
                                               'sf'=>0,
                                               'nr'=>25,
                                               'from'=>'',
                                               'to'=>'',
                                               'elements'=>''
                                           )

                             ),
                   'suppliers'=>array(
                                   'details'=>false,
                                   'view'=>'general',
                                   'edit'=>'suppliers',
                                   'table'=>array(
                                               'order'=>'name',
                                               'order_dir'=>'',
                                               'sf'=>0,
                                               'nr'=>25,
                                               'where'=>'where true',
                                               'f_field'=>'name',
                                               'f_value'=>'',
                                           'csv_export'=>array(
							    'id'=>true,
                                                            'code'=>true,
                                                            'name'=>true,
                                                            'opo'=>true,
                                                            'contact_name'=>false,
                                                            'telephone'=>true,
                                                            'email'=>false,
                                                            'currency'=>false,
                                                            'discontinued'=>false,      
                                                            'surplus'=>false,
                                                            'ok'=>false,
                                                            'low'=>false,
                                                            'critical'=>false,
                                                            'gone'=>false,
                                                            'cost_all'=>false,
                                                            'cost_1y'=>false,
                                                            'cost_1q'=>false,
                                                            'cost_1m'=>false,
                                                            'cost_1w'=>false,
                                                            'profit_all'=>false,
                                                            'profit_1y'=>false,
                                                            'profit_1q'=>false,
                                                            'profit_1m'=>false,
                                                            'profit_1w'=>false


                                                        )

                                           )
                               ),
                   'hr'=>array(
                            'view'=>'staff',
                            'staff'=>array(
                                        'order'=>'name',
                                        'order_dir'=>'',
                                        'sf'=>0,
                                        'nr'=>50,
                                        'where'=>'where true',
                                        'f_field'=>'name',
                                        'f_value'=>''
                                    ),
                            'areas'=>array(
                                        'order'=>'name',
                                        'order_dir'=>'',
                                        'sf'=>0,
                                        'nr'=>50,
                                        'where'=>'where true',
                                        'f_field'=>'name',
                                        'f_value'=>''
                                    ),
                            'departments'=>array(
                                              'order'=>'name',
                                              'order_dir'=>'',
                                              'sf'=>0,
                                              'nr'=>50,
                                              'where'=>'where true',
                                              'f_field'=>'name',
                                              'f_value'=>''
                                          ),'positions'=>array(
                                                            'order'=>'name',
                                                            'order_dir'=>'',
                                                            'sf'=>0,
                                                            'nr'=>50,
                                                            'where'=>'where true',
                                                            'f_field'=>'name',
                                                            'f_value'=>''
                                                        )
                        ),
                   'users'=>array(
                               'staff'=>array(
                                           'display'=>'all',
                                           'order'=>'alias',
                                           'order_dir'=>'',
                                           'sf'=>0,
                                           'nr'=>50,
                                           'where'=>'where true',
                                           'f_field'=>'alias',
                                           'f_value'=>''
                                       ),
				'supplier'=>array(
                                           'display'=>'all',
                                           'order'=>'alias',
                                           'order_dir'=>'',
                                           'sf'=>0,
                                           'nr'=>50,
                                           'where'=>'where true',
                                           'f_field'=>'alias',
                                           'f_value'=>''
                                       ),
                               'loginhistory'=>array(
                                                  'display'=>'all',
                                                  'order'=>'login_date',
                                                  'order_dir'=>'',
                                                  'sf'=>0,
                                                  'nr'=>50,
                                                  'where'=>'where true',
                                                  'f_field'=>'user',
                                                  'f_value'=>''
                                              ),
                               'groups'=>array(
                                            'order'=>'name',
                                            'order_dir'=>'',
                                            'sf'=>0,
                                            'nr'=>50,
                                            'where'=>'where true',
                                            'f_field'=>'name',
                                            'f_value'=>''
                                        ),

                           ),
                   'page'=>array(
                              'id'=>0,
                              'edit'=>'properties'
                          ),
                   'store'=>array(
                               'details'=>false,
                               'percentages'=>false,
                               'view'=>'general',
                               'period'=>'year',
                               'percentage'=>0,
                               'mode'=>'all',
                               'avg'=>'totals',

                               'plot_interval'=>array(
                                                   'd'=>array('plot_bins'=>15,
                                                              'plot_forecast_bins'=>3),
                                                   'y'=>array('plot_bins'=>5,
                                                              'plot_forecast_bins'=>3),
                                                   'q'=>array('plot_bins'=>12,
                                                              'plot_forecast_bins'=>3),
                                                   'm'=>array('plot_bins'=>18,
                                                              'plot_forecast_bins'=>3),
                                                   'w'=>array('plot_bins'=>26,
                                                              'plot_forecast_bins'=>3),
                                               ),
                               'edit'=>'description',
                               'editing'=>false,
                               'id'=>1,
                               'plot'=>'store',
                               'plot_period'=>'m',
                               'plot_category'=>'sales',
                               'plot_data'=>array('store'=>array(

                                                              'page'=>'plot.php'
                                                          )
                                                          ,'top_departments'=>array(

                                                                                 'page'=>'plot.php'
                                                                             )
                                                                             ,'pie'=>array(
                                                                                        'period'=>'m',
                                                                                        'interval'=>'1y',

                                                                                        'page'=>'pie.php',
                                                                                        'forecast'=>'no',
                                                                                        'from'=>'',
                                                                                        'to'=>''
                                                                                    )
                                                 ),





                               'table'=>array(
                                           'where'=>'where true',
                                           'f_field'=>'code',
                                           'f_value'=>'','f_show'=>false,
                                           'order'=>'name',
                                           'order_dir'=>'',
                                           'sf'=>0,
                                           'nr'=>25,
                                           'csv_export'=>array(
                                                            'code'=>true,
                                                            'name'=>true,
                                                            'families'=>false,
                                                            'products'=>false,
                                                            'discontinued'=>false,
                                                            'new'=>false,
                                                            'surplus'=>false,
                                                            'ok'=>false,
                                                            'low'=>false,
                                                            'critical'=>false,
                                                            'gone'=>false,
                                                            'unknown'=>false,
                                                            'sales_all'=>false,
                                                            'sales_1y'=>false,
                                                            'sales_1q'=>false,
                                                            'sales_1m'=>false,
                                                            'sales_1w'=>false,
                                                            'profit_all'=>false,
                                                            'profit_1y'=>false,
                                                            'profit_1q'=>false,
                                                            'profit_1m'=>false,
                                                            'profit_1w'=>false


                                                        )

                                       ),



                               'history'=>array(
                                             'where'=>'where true',
                                             'f_field'=>'abstract',
                                             'f_value'=>'','f_show'=>false,
                                             'order'=>'date',
                                             'order_dir'=>'desc',
                                             'sf'=>0,
                                             'nr'=>25,
                                             'from'=>'',
                                             'to'=>'',
                                             'elements'=>''
                                         ),
                               'charges'=>array(
                                             'where'=>'where true',
                                             'f_field'=>'description',
                                             'f_value'=>'','f_show'=>false,
                                             'order'=>'description',
                                             'order_dir'=>'',
                                             'sf'=>0,
                                             'nr'=>25,
                                         ),
                               'shipping_country'=>array(
                                                      'where'=>'where true',
                                                      'f_field'=>'name',
                                                      'f_value'=>'','f_show'=>false,
                                                      'order'=>'name',
                                                      'order_dir'=>'',
                                                      'sf'=>0,
                                                      'nr'=>25,
                                                  ),
                               'shipping_world_region'=>array(
                                                           'where'=>'where true',
                                                           'f_field'=>'name',
                                                           'f_value'=>'','f_show'=>false,
                                                           'order'=>'name',
                                                           'order_dir'=>'',
                                                           'sf'=>0,
                                                           'nr'=>25,
                                                       ),
                               'campaigns'=>array(
                                               'where'=>'where true',
                                               'f_field'=>'name',
                                               'f_value'=>'','f_show'=>false,
                                               'order'=>'name',
                                               'order_dir'=>'',
                                               'sf'=>0,
                                               'nr'=>25,
                                           ),
                               'deals'=>array(
                                           'where'=>'where true',
                                           'f_field'=>'name',
                                           'f_value'=>'','f_show'=>false,
                                           'order'=>'name',
                                           'order_dir'=>'',
                                           'sf'=>0,
                                           'nr'=>25,
                                       ),

                               'pages'=>array(
                                           'where'=>'where true',
                                           'f_field'=>'section',
                                           'f_value'=>'',
                                           'f_show'=>false,
                                           'order'=>'section',
                                           'order_dir'=>'',
                                           'sf'=>0,
                                           'nr'=>25,
                                       ),

                           ),

                   'departments'=>array(
                                     'details'=>false,
                                     'percentages'=>false,
                                     'view'=>'general',
                                     'period'=>'year',
                                     'percentage'=>0,
                                     'mode'=>'all',
                                     'avg'=>'totals',
                                     'edit'=>false,
                                     'id'=>1,
                                     'table'=>array(
                                                 'where'=>'where true',
                                                 'f_field'=>'code',
                                                 'f_value'=>'','f_show'=>false,
                                                 'order'=>'name',
                                                 'order_dir'=>'',
                                                 'sf'=>0,
                                                 'nr'=>25,
                                                 'parent'=>'',
                                                 'csv_export'=>array(
                                                                  'code'=>true,
                                                                  'name'=>true,
                                                                  'families'=>true,
                                                                  'products'=>true,
                                                                  'discontinued'=>false,
                                                                  'surplus'=>false,
                                                                  'ok'=>false,
                                                                  'low'=>false,
                                                                  'critical'=>false,
                                                                  'gone'=>false,
                                                                  'unknown'=>false,
                                                                  'sales_all'=>false,
                                                                  'sales_1y'=>false,
                                                                  'sales_1q'=>false,
                                                                  'sales_1m'=>false,
                                                                  'sales_1w'=>false,
                                                                  'profit_all'=>false,
                                                                  'profit_1y'=>false,
                                                                  'profit_1q'=>false,
                                                                  'profit_1m'=>false,
                                                                  'profit_1w'=>false)


                                             )
                                 ),

                   'department'=>array(
                                    'details'=>false,
                                    'percentages'=>false,
                                    'view'=>'general',
                                    'id'=>1,
                                    'period'=>'year',
                                    'percentage'=>0,
                                    'mode'=>'all',
                                    'avg'=>'totals',
                                    'restrictions'=>'for_sale',
                                    'editing'=>'details',
                                    'table_type'=>'list',

                                    'plot'=>'department',
                                    'plot_data'=>array('department'=>array(
                                                                        'period'=>'m',
                                                                        'category'=>'sales',
                                                                        'page'=>'plot.php'
                                                                    )
                                                                    ,'top_families'=>array(
                                                                                        'period'=>'m',
                                                                                        'category'=>'sales',
                                                                                        'page'=>'plot.php'
                                                                                    )
                                                                                    ,'pie'=>array(
                                                                                               'period'=>'m',
                                                                                               'category'=>'sales',
                                                                                               'page'=>'pie.php',
                                                                                               'forecast'=>'no',
                                                                                               'date'=>'today'
                                                                                           )
                                                      ),








                                    'table'=>array(
                                                'order'=>'code',
                                                'order_dir'=>'',
                                                'sf'=>0,
                                                'nr'=>200,
                                                'where'=>'where true',
                                                'f_field'=>'code',
                                                'f_value'=>''

                                            ),
                                    'history'=>array(
                                                  'where'=>'where true',
                                                  'f_field'=>'abstract',
                                                  'f_value'=>'','f_show'=>false,
                                                  'order'=>'date',
                                                  'order_dir'=>'desc',
                                                  'sf'=>0,
                                                  'nr'=>25,
                                                  'from'=>'',
                                                  'to'=>'',
                                                  'elements'=>''
                                              ),
                                    'deals'=>array(
                                                'where'=>'where true',
                                                'f_field'=>'name',
                                                'f_value'=>'','f_show'=>false,
                                                'order'=>'name',
                                                'order_dir'=>'',
                                                'sf'=>0,
                                                'nr'=>25,
                                            ),

                                ),
                   'family'=>array(
                                'details'=>false,
                                'percentages'=>false,
                                'view'=>'general',

                                'edit_view'=>'view_name',
                                'id'=>1,
                                'period'=>'year',
                                'percentage'=>0,
                                'mode'=>'all',
                                'avg'=>'totals',
                                'edit'=>'details',
                                'editing'=>false,
                                'table_type'=>'list',
                                'plot'=>'family',
                                'plot_data'=>array('family'=>array(
                                                                'period'=>'m',
                                                                'category'=>'sales',
                                                                'page'=>'plot.php'
                                                            )
                                                            ,'top_products'=>array(
                                                                                'period'=>'m',
                                                                                'category'=>'sales',
                                                                                'page'=>'plot.php'
                                                                            )
                                                                            ,'pie'=>array(
                                                                                       'period'=>'m',
                                                                                       'category'=>'sales',
                                                                                       'page'=>'pie.php',
                                                                                       'forecast'=>'no',
                                                                                       'date'=>'today'
                                                                                   )
                                                  ),





                                'table'=>array(
                                            'show_only'=>'forsale',
                                            'order'=>'code',
                                            'order_dir'=>'',
                                            'sf'=>0,
                                            'nr'=>20,
                                            'where'=>'where true',
                                            'f_field'=>'code',
                                            'f_value'=>'',
                                            'csv_export'=>array(
                                                             'code'=>true,
                                                             'name'=>true,
                                                             'web'=>true,
                                                             'status'=>true,
                                                             'products'=>false,

                                                             'sales_all'=>false,
                                                             'sales_1y'=>false,
                                                             'sales_1q'=>false,
                                                             'sales_1m'=>false,
                                                             'sales_1w'=>false,
                                                             'profit_all'=>false,
                                                             'profit_1y'=>false,
                                                             'profit_1q'=>false,
                                                             'profit_1m'=>false,
                                                             'profit_1w'=>false


                                                         )

                                        ),
                                'history'=>array(
                                              'where'=>'where true',
                                              'f_field'=>'abstract',
                                              'f_value'=>'','f_show'=>false,
                                              'order'=>'date',
                                              'order_dir'=>'desc',
                                              'sf'=>0,
                                              'nr'=>25,
                                              'from'=>'',
                                              'to'=>'',
                                              'elements'=>''
                                          ),
                                'deals'=>array(
                                            'where'=>'where true',
                                            'f_field'=>'name',
                                            'f_value'=>'','f_show'=>false,
                                            'order'=>'name',
                                            'order_dir'=>'',
                                            'sf'=>0,
                                            'nr'=>25,
                                        ),
                            ),


                   'parts'=>array(
                               'details'=>false,
                               'view'=>'general',
                               'period'=>'year',
                               'percentage'=>0,
                               'mode'=>'all',
                               'avg'=>'totals',
                               'table'=>array(
                                           'order'=>'sku',
                                           'order_dir'=>'',
                                           'sf'=>0,
                                           'nr'=>20,
                                           'where'=>'where true',
                                           'f_field'=>'used_in',
                                           'f_value'=>'',
                                           'csv_export'=>array(
                                                            'sku'=>true,
                                                            'used_in'=>true,
                                                            'description'=>true,
                                                            'stock'=>true,
                                                            'stock_cost'=>true,
                                                            'unit'=>false,
                                                            'status'=>false,
                                                            'valid_from'=>false,
                                                            'valid_to'=>false,
                                                            'total_lost'=>false,
                                                            'total_broken'=>false,
                                                            'total_sold'=>false,
                                                            'total_given'=>false,
                                                            'sales_all'=>false,
                                                            'profit_all'=>false,
                                                            'sales_1y'=>false,
                                                            'profit_1y'=>false,
                                                            'sales_1q'=>false,
                                                            'profit_1q'=>false,
                                                            'sales_1m'=>false,
                                                            'profit_1m'=>false,
                                                            'sales_1w'=>false,
                                                            'profit_1w'=>false
                                                        )

                                       )
                           ),

                   'families'=>array(
                                  'details'=>false,
                                  'view'=>'general',
                                  'percentages'=>false,
                                  'period'=>'year',
                                  'mode'=>'all',
                                  'avg'=>'totals',
                                  'mode'=>'same_code',//same_code,same_id,all
                                  'parent'=>'none',//store,department,family,none
                                  'restrictions'=>'forsale',
                                  'table'=>array(
                                              'order'=>'code',
                                              'order_dir'=>'',
                                              'sf'=>0,
                                              'nr'=>20,
                                              'where'=>'where true',
                                              'f_field'=>'code',
                                              'f_value'=>'','f_show'=>false,
                                              'mode'=>'same_code',//same_code,same_id,all
                                              'parent'=>'none',//store,department,family,none
                                              'restrictions'=>'forsale',
                                              'csv_export'=>array(
                                                               'code'=>true,
                                                               'name'=>true,
                                                               'stores'=>true,
                                                               'products'=>false,
                                                               'surplus'=>false,
                                                               'ok'=>false,
                                                               'low'=>false,
                                                               'critical'=>false,
                                                               'gone'=>false,
                                                               'unknown'=>false,
                                                               'sales_all'=>false,
                                                               'sales_1y'=>false,
                                                               'sales_1q'=>false,
                                                               'sales_1m'=>false,
                                                               'sales_1w'=>false,
                                                               'profit_all'=>false,
                                                               'profit_1y'=>false,
                                                               'profit_1q'=>false,
                                                               'profit_1m'=>false,
                                                               'profit_1w'=>false
                                                           )

                                          ),),
// --------------------------------------------product under family export(csv) starts----------------------
                   'product_delete'=>array(
                                 'details'=>false,
                                 'view'=>'general',
                                 'percentages'=>false,
                                 'period'=>'year',
                                 'mode'=>'all',
                                 'avg'=>'totals',
                                 'mode'=>'all',//same_code,same_id,all
                                 'parent'=>'none',//store,department,family,none
                                 'restrictions'=>'forsale',
                                 'table'=>array(
                                             'order'=>'code',
                                             'order_dir'=>'',
                                             'sf'=>0,
                                             'nr'=>20,
                                             'where'=>'where true',
                                             'f_field'=>'code',
                                             'f_value'=>'','f_show'=>false,
                                             'mode'=>'all',//same_code,same_id,all
                                             'parent'=>'none',//store,department,family,none
                                             'restrictions'=>'forsale',
                                             'csv_export'=>array(
                                                              'code'=>true,
                                                              'name'=>true,
                                                              'status'=>true,
                                                              'web'=>false,
                                                              'sales_all'=>false,
                                                              'sales_1y'=>false,
                                                              'sales_1q'=>false,
                                                              'sales_1m'=>false,
                                                              'sales_1w'=>false,
                                                              'profit_all'=>false,
                                                              'profit_1y'=>false,
                                                              'profit_1q'=>false,
                                                              'profit_1m'=>false,
                                                              'profit_1w'=>false
                                                          )

                                         ), ),
//--------------------------------------------product under family export(csv) ends----------------------


                   'product'=>array(
                                 'details'=>false,
                                 'plot'=>'product',
                                 'plot_interval'=>array(
                                                        'd'=>array('plot_bins'=>7,
                                                                'plot_forecast_bins'=>3),
                                                     'y'=>array('plot_bins'=>5,
                                                                'plot_forecast_bins'=>3),
                                                     'q'=>array('plot_bins'=>12,
                                                                'plot_forecast_bins'=>3),
                                                     'm'=>array('plot_bins'=>18,
                                                                'plot_forecast_bins'=>3),
                                                     'w'=>array('plot_bins'=>26,
                                                                'plot_forecast_bins'=>3),
                                                 ),
                                 'plot_period'=>'m',
                                 'plot_category'=>'sales',


                                 'plot_data'=>array('product'=>array(
                                                                  'label'=>_('Product Sales')
                                                                          ,'page'=>'plot.php'
                                                              ),

                                                    'parts'=>array(
                                                                'label'=>_('Stock History')
                                                                        ,'parts'=>'plot.php'
                                                            )
                                                   ),



                                 'mode'=>'pid',
                                 'tag'=>1,
                                 'edit'=>'description',
                                 'display'=>array('details'=>0,'plot'=>1,'orders'=>1,'customers'=>1,'stock_history'=>0),
                                 'server'=>array(
                                              'tag'=>'',
                                              'order'=>'store',
                                              'order_dir'=>'',
                                              'sf'=>0,
                                              'nr'=>15,
                                              'where'=>'where true',
                                              'f_field'=>'id',
                                              'f_value'=>'','f_show'=>false,
                                              'from'=>'',
                                              'to'=>''
                                          ),
                                 'orders'=>array(
                                              'order'=>'date',
                                              'order_dir'=>'',
                                              'sf'=>0,
                                              'nr'=>15,
                                              'where'=>'where true',
                                              'f_field'=>'id',
                                              'f_value'=>'','f_show'=>false,
                                              'from'=>'',
                                              'to'=>''
                                          ),
                                 'code_timeline'=>array(
                                                     'code'=>'',
                                                     'order'=>'from',
                                                     'order_dir'=>'desc',
                                                     'sf'=>0,
                                                     'nr'=>15,
                                                     'where'=>'where true',
                                                     'f_field'=>'pid',
                                                     'f_value'=>'','f_show'=>false,
                                                     'from'=>'',
                                                     'to'=>''
                                                 ),
                                 'customers'=>array(
                                                 'order'=>'dispached',
                                                 'order_dir'=>'desc',
                                                 'sf'=>0,
                                                 'nr'=>15,
                                                 'where'=>'where true',
                                                 'f_field'=>'name',
                                                 'f_value'=>''
                                             ),
                                 'parts'=>array(
                                             'order'=>'sku',
                                             'order_dir'=>'desc',
                                             'sf'=>0,
                                             'nr'=>50,
                                             'where'=>'where true',
                                             'f_field'=>'sku',
                                             'f_value'=>''
                                         ),
                                 'history'=>array(
                                               'order'=>'date',
                                               'order_dir'=>'desc',
                                               'sf'=>0,
                                               'nr'=>15,
                                               'where'=>'where true',
                                               'f_field'=>'id',
                                               'f_value'=>'','f_show'=>false,
                                               'from'=>'',
                                               'to'=>'',
                                               'elements'=>array()
                                           ),
                                 'stock_history'=>array(
                                                     'order'=>'date',
                                                     'order_dir'=>'',
                                                     'sf'=>0,
                                                     'nr'=>15,
                                                     'where'=>'where true',
                                                     'f_field'=>'id',
                                                     'f_value'=>'','f_show'=>false,
                                                     'from'=>'',
                                                     'to'=>'',
                                                     'elements'=>array()
                                                 )
                             ),
                   'deals'=>array(
                               'where'=>'where true',
                               'f_field'=>'name',
                               'f_value'=>'','f_show'=>false,
                               'order'=>'name',
                               'order_dir'=>'',
                               'sf'=>0,
                               'nr'=>25,
                           ),
                   'part'=>array(
                              'details'=>false,


                              'plot'=>'part_stock_history',
                              'plot_interval'=>array(
                                                  'y'=>array('plot_bins'=>5,'plot_forecast_bins'=>3),'d'=>array('plot_bins'=>30,'plot_forecast_bins'=>5),
                                                  'q'=>array('plot_bins'=>12,
                                                             'plot_forecast_bins'=>3),
                                                  'm'=>array('plot_bins'=>18,
                                                             'plot_forecast_bins'=>3),
                                                  'w'=>array('plot_bins'=>26,
                                                             'plot_forecast_bins'=>3),
                                              ),
                              'plot_period'=>'m',
                              'plot_category'=>'stock',


                              'plot_data'=>array('part_stock_history'=>array(
                                                                          'label'=>_('Product Sales')
                                                                                  ,'page'=>'plot.php'
                                                                      ),

                                                 'part_out'=>array(
                                                                'label'=>_('Stock History')
                                                                        ,'parts'=>'plot.php'
                                                            )
                                                ),




                              'id'=>1,
                              'edit'=>'description',
                              'display'=>array('details'=>0,'plot'=>1,'orders'=>0,'customers'=>0,'stock_history'=>0),

                              'stock_history'=>array(
                                                  'order'=>'date',
                                                  'order_dir'=>'desc',
                                                  'sf'=>0,
                                                  'nr'=>15,
                                                  'type'=>'week',
                                                  'where'=>'where true',
                                                  'f_field'=>'location',
                                                  'f_value'=>'',
                                                  'f_show'=>false,
                                                  'from'=>'',
                                                  'to'=>'',
                                                  'elements'=>array()
                                              ),
                              'transactions'=>array(
                                                 'view'=>'all_transactions',
                                                 'order'=>'date',
                                                 'order_dir'=>'desc',
                                                 'sf'=>0,
                                                 'nr'=>15,
                                                 'where'=>'where true',
                                                 'f_field'=>'note',
                                                 'f_value'=>'',
                                                 'f_show'=>false,
                                                 'from'=>'',
                                                 'to'=>'',
                                                 'elements'=>array()
                                             ),

                          ),


                   'po'=>array(
                            'id'=>'',
                            'new'=>'',
                            'new_data'=>array('num_items'=>0,'name'=>'','total'=>0),

                            'new_timestamp'=>'',
                            'items'=>array(
                                        'order'=>'code',
                                        'order_dir'=>'',
                                        'sf'=>0,
                                        'nr'=>25,
                                        'where'=>'where true',
                                        'f_field'=>'p.code',
                                        'f_value'=>'','f_show'=>false,
                                        'all_products'=>false,
                                        'all_products_supplier'=>false
                                    ),
                        ),
                   'location'=>array(
                                  'location'=>false,
                                  'edit'=>'description',
                                  'id'=>1,
                                  'details'=>false,
                                  'parts'=>array(
                                              'order'=>'sku',
                                              'order_dir'=>'',
                                              'sf'=>0,
                                              'nr'=>25,
                                              'where'=>'where true',
                                              'f_field'=>'sku',
                                              'f_value'=>'','f_show'=>false,
                                              'elements'=>array()
                                          ),
                                  'stock_history'=>array(
                                                      'order'=>'date',
                                                      'order_dir'=>'desc',
                                                      'sf'=>0,
                                                      'nr'=>25,
                                                      'from'=>'',
                                                      'to'=>'',
                                                      'where'=>'where true',
                                                      'f_field'=>'author',
                                                      'f_value'=>'','f_show'=>false,
                                                      'elements'=>array()
                                                  )
                              ),
                   'report_to_delete'=>array(
                                          'tipo'=>'m'
                                                 ,'y'=>date('Y')
                                                      ,'m'=>date('m')
                                                           ,'d'=>date('d')
                                                                ,'w'=>1
                                                                     ,'activity'=>array('compare'=>'last_year','period'=>'week'),

                                          'sales'=>array(
                                                      'store_keys'=>'all',
                                                      'from'=>'',
                                                      'to'=>'',
                                                      'period'=>'',
                                                      'order'=>'date',
                                                      'order_dir'=>'desc',
                                                      'invoice_type'=>'all',
                                                      'dn_state'=>'all',
                                                      'sf'=>0,
                                                      'nr'=>25,
                                                      'plot'=>'per_store',
                                                      'plot_data'=>array('per_store'=>array(
                                                                                         'category'=>'sales',
                                                                                         'page'=>'plot.php',
                                                                                         'period'=>'m'

                                                                                     )
                                                                                     ,'per_category'=>array(
                                                                                                         'category'=>'sales',
                                                                                                         'page'=>'plot.php',
                                                                                                         'period'=>'m'
                                                                                                     )
                                                                        ),
                                                  ),
                                          'pickers'=>array(
                                                        'order'=>'units',
                                                        'order_dir'=>'',
                                                        'sf'=>0,
                                                        'nr'=>10000,
                                                        'from'=>'',
                                                        'to'=>'',
                                                        'where'=>'where true',
                                                        'f_field'=>'',
                                                        'f_value'=>'','f_show'=>false,
                                                        'elements'=>array()

                                                    ),
                                          'packers'=>array(
                                                        'order'=>'tipo',
                                                        'order_dir'=>'',
                                                        'sf'=>0,
                                                        'nr'=>10000,
                                                        'from'=>'',
                                                        'to'=>'',
                                                        'where'=>'where true',
                                                        'f_field'=>'',
                                                        'f_value'=>'','f_show'=>false,
                                                        'elements'=>array()

                                                    ),
                                          'customers'=>array(
                                                          'store_keys'=>'all',
                                                          'top'=>100,
                                                          'criteria'=>'net_balance',
                                                          'f_value'=>'',
                                                          'f_show'=>false,
                                                          'f_field'=>'customer_name',
                                                          'from'=>'',
                                                          'to'=>''
                                                      ),
                                          'products'=>array('store_keys'=>'all',
                                                            'top'=>100,
                                                            'criteria'=>'net_sales',
                                                            'f_value'=>'',
                                                            'f_show'=>false,
                                                            'f_field'=>'code',
                                                            'from'=>'',
                                                            'to'=>''
                                                           ),
                                          'orders_in_process'=>array(
                                                                  'store_keys'=>'all',
                                                                  'sf'=>0,
                                                                  'nr'=>50,
                                                                  'f_value'=>'',
                                                                  'f_show'=>false,
                                                                  'f_field'=>'customer',
                                                                  'from'=>'',
                                                                  'to'=>'',
                                                                  'order'=>'date',
                                                                  'order_dir'=>'',
                                                                  'where'=>''
                                                              )



                                      ),
                   'stores'=>array(
                                'details'=>false,
                                'percentages'=>false,
                                'view'=>'general',
                                'period'=>'year',
                                'mode'=>'all',
                                'avg'=>'totals',
                                'edit'=>'stores',
                                'orders_view'=>'orders',
                                'editing'=>false,
                                'table'=>array(
                                            'exchange_type'=>'day2day',
                                            'exchange_value'=>1,
                                            'show_default_currency'=>false,
                                            'where'=>'where true',
                                            'f_field'=>'code',
                                            'f_value'=>'','f_show'=>false,
                                            'order'=>'name',
                                            'order_dir'=>'',
                                            'sf'=>0,
                                            'nr'=>25,
                                            'csv_export'=>array(
                                                             'code'=>true,
                                                             'name'=>true,
                                                             'departments'=>false,
                                                             'families'=>false,
                                                             'products'=>false,
                                                             'discontinued'=>false,
                                                             'new'=>false,
                                                             'surplus'=>false,
                                                             'ok'=>false,
                                                             'low'=>false,
                                                             'critical'=>false,
                                                             'gone'=>false,
                                                             'unknown'=>false,
                                                             'sales_all'=>false,
                                                             'sales_1y'=>false,
                                                             'sales_1q'=>false,
                                                             'sales_1m'=>false,
                                                             'sales_1w'=>false,
                                                             'profit_all'=>false,
                                                             'profit_1y'=>false,
                                                             'profit_1q'=>false,
                                                             'profit_1m'=>false,
                                                             'profit_1w'=>false


                                                         )

                                        ),
                                'orders'=>array(
                                             'percentages'=>false,
                                             'view'=>'general',
                                             'period'=>'year',
                                             'mode'=>'all',
                                             'avg'=>'totals',
                                             'where'=>'where true',
                                             'f_field'=>'code',
                                             'f_value'=>'','f_show'=>false,
                                             'order'=>'name',
                                             'order_dir'=>'',
                                             'sf'=>0,
                                             'nr'=>25,

                                             'csv_export'=>array(
                                                              'code'=>true,
                                                              'name'=>true,
                                                              'orders'=>true,
                                                              'cancelled'=>false,
                                                              'suspended'=>false,
                                                              'pending'=>false,
                                                              'dispatched'=>false,


                                                              'sales_all'=>false,
                                                              'sales_1y'=>false,
                                                              'sales_1q'=>false,
                                                              'sales_1m'=>false,
                                                              'sales_1w'=>false,
                                                              'profit_all'=>false,
                                                              'profit_1y'=>false,
                                                              'profit_1q'=>false,
                                                              'profit_1m'=>false,
                                                              'profit_1w'=>false


                                                          )



                                         ),
                                'invoices'=>array(
                                               'percentages'=>false,
                                               'view'=>'general',
                                               'invoice_type'=>'all',
                                               'period'=>'year',
                                               'mode'=>'all',
                                               'avg'=>'totals',
                                               'where'=>'where true',
                                               'f_field'=>'code',
                                               'f_value'=>'','f_show'=>false,
                                               'order'=>'name',
                                               'order_dir'=>'',
                                               'sf'=>0,
                                               'nr'=>25,


                                               'csv_export'=>array(
                                                                'code'=>true,
                                                                'name'=>true,
                                                                'invoices'=>true,
                                                                'invpaid'=>true,
                                                                'invtopay'=>false,
                                                                'refunds'=>false,
                                                                'refpaid'=>false,
                                                                'reftopay'=>false,

                                                                'sales_all'=>false,
                                                                'sales_1y'=>false,
                                                                'sales_1q'=>false,
                                                                'sales_1m'=>false,
                                                                'sales_1w'=>false,
                                                                'profit_all'=>false,
                                                                'profit_1y'=>false,
                                                                'profit_1q'=>false,
                                                                'profit_1m'=>false,
                                                                'profit_1w'=>false


                                                            )

                                           ),
                                'delivery_notes'=>array(
                                                     'percentages'=>false,
                                                     'dn_state'=>'all',
                                                     'view'=>'dn_state',
                                                     'period'=>'year',
                                                     'mode'=>'all',
                                                     'avg'=>'totals',
                                                     'where'=>'where true',
                                                     'f_field'=>'code',
                                                     'f_value'=>'','f_show'=>false,
                                                     'order'=>'name',
                                                     'order_dir'=>'',
                                                     'sf'=>0,
                                                     'nr'=>25,
                                                     'csv_export'=>array(
                                                                      'code'=>true,
                                                                      'name'=>true,
                                                                      'total'=>true,
                                                                      'topick'=>false,
                                                                      'picking'=>false,
                                                                      'packing'=>false,
                                                                      'ready'=>false,
                                                                      'send'=>false,
                                                                      'returned'=>false,

                                                                      'sales_all'=>false,
                                                                      'sales_1y'=>false,
                                                                      'sales_1q'=>false,
                                                                      'sales_1m'=>false,
                                                                      'sales_1w'=>false,
                                                                      'profit_all'=>false,
                                                                      'profit_1y'=>false,
                                                                      'profit_1q'=>false,
                                                                      'profit_1m'=>false,
                                                                      'profit_1w'=>false


                                                                  )
                                                 ),
                                'customers'=>array(
                                                'percentages'=>false,
                                                'view'=>'general',
                                                'period'=>'year',
                                                'mode'=>'all',
                                                'avg'=>'totals',
                                                'where'=>'where true',
                                                'f_field'=>'code',
                                                'f_value'=>'','f_show'=>false,
                                                'order'=>'name',
                                                'order_dir'=>'',
                                                'sf'=>0,
                                                'nr'=>25,
                                                'csv_export'=>array(
                                                                 'code'=>true,
                                                                 'name'=>true,
                                                                 'total_customer_contacts'=>true,
                                                                 'new_customer_contacts'=>true,
                                                                 'total_customer'=>true,
                                                                 'active_customer'=>true,
                                                                 'new_customer'=>true,
                                                                 'lost_customer'=>true,


                                                                 'sales_all'=>false,
                                                                 'sales_1y'=>false,
                                                                 'sales_1q'=>false,
                                                                 'sales_1m'=>false,
                                                                 'sales_1w'=>false,
                                                                 'profit_all'=>false,
                                                                 'profit_1y'=>false,
                                                                 'profit_1q'=>false,
                                                                 'profit_1m'=>false,
                                                                 'profit_1w'=>false


                                                             )
                                            )


                            ),

                   'supplier'=>array(
                                  'details'=>false,
                                  'edit'=>'details',
                                  'action_after_create'=>'continue',
                                  'plot'=>'sales_month',
                                  'orders_view'=>'pos',
                                  'id'=>1,
                                  'display'=>array('details'=>0,'history'=>0,'products'=>1,'po'=>0),
                                  'plot_options'=>array('weeks'=>'','from'=>'','to'=>'','months'=>''),
                                  'products'=>array(
                                                 'view'=>'product_general',
                                                 'percentage'=>0,
                                                 'period'=>'year',
                                                 'order'=>'code',
                                                 'order_dir'=>'',
                                                 'sf'=>0,
                                                 'nr'=>15,
                                                 'where'=>'where true',
                                                 'f_field'=>'p.code',
                                                 'f_value'=>'','f_show'=>false,
                                                 'from'=>'',
                                                 'to'=>'',
                                                'csv_export'=>array(
                                                                 'code'=>true,
                                                                 'supplier'=>true,
                                                                 'product_name'=>true,
                                                                 'product_description'=>true,
                                                                 'unit_type'=>true,
                                                                 'currency'=>true,
                                                                 'valid_from'=>true,
                                                                 'valid_to'=>true,
                                                                 'buy_state'=>false,
                                                                 'cost_all'=>false,
                                                                 'cost_1y'=>false,
                                                                 'cost_1q'=>false,
                                                                 'cost_1m'=>false,
                                                                 'cost_1w'=>false,
                                                                 'profit_all'=>false,
                                                                 'profit_1y'=>false,
                                                                 'profit_1q'=>false,
                                                                 'profit_1m'=>false,
                                                                 'profit_1w'=>false


                                                             )
                                             ),
                                  'po'=>array(
                                           'order'=>'date_index',
                                           'order_dir'=>'desc',
                                           'sf'=>0,
                                           'nr'=>15,
                                           'where'=>'where true',
                                           'f_field'=>'max',
                                           'f_value'=>'','f_show'=>false,
                                           'to'=>'',
                                           'from'=>'',
                                           'view'=>'all'

                                       ),
                                  'history'=>array(
                                                'order'=>'date',
                                                'order_dir'=>'desc',
                                                'sf'=>0,
                                                'nr'=>15,
                                                'where'=>'where true',
                                                'f_field'=>'id',
                                                'f_value'=>'','f_show'=>false,
                                                'from'=>'',
                                                'to'=>'',
                                                'elements'=>array()
                                            )
                              ),
                   'company_area'=>array(
                                      'id'=>0,
                                      'action_after_create'=>'continue',
                                      'edit'=>'details',
                                      'departments'=>array(
                                                        'order'=>'code',
                                                        'order_dir'=>'desc',
                                                        'sf'=>0,
                                                        'nr'=>10,
                                                        'where'=>'where true',
                                                        'f_field'=>'name',
                                                        'f_value'=>'','f_show'=>false,
                                                        'from'=>'',
                                                        'to'=>'',
                                                        'details'=>0,
                                                        'elements'=>array('h_comp'=>1,'h_cont'=>1,'note'=>1)
                                                    ),
                                      'history'=>array(
                                                    'where'=>'where true',
                                                    'f_field'=>'abstract',
                                                    'f_value'=>'','f_show'=>false,
                                                    'order'=>'date',
                                                    'order_dir'=>'desc',
                                                    'sf'=>0,
                                                    'nr'=>25,
                                                    'from'=>'',
                                                    'to'=>'',
                                                    'elements'=>''
                                                ),


                                  ),
                   'company_department'=>array(
                                            'id'=>0,
                                            'action_after_create'=>'continue',
                                            'edit'=>'details',
                                            'table'=>array(
                                                        'order'=>'code',
                                                        'order_dir'=>'desc',
                                                        'sf'=>0,
                                                        'nr'=>10,
                                                        'where'=>'where true',
                                                        'f_field'=>'name',
                                                        'f_value'=>'','f_show'=>false,
                                                        'from'=>'',
                                                        'to'=>'',
                                                        'details'=>0,
                                                        'elements'=>array('h_comp'=>1,'h_cont'=>1,'note'=>1)
                                                    ),
                                            'history'=>array(
                                                          'where'=>'where true',
                                                          'f_field'=>'abstract',
                                                          'f_value'=>'','f_show'=>false,
                                                          'order'=>'date',
                                                          'order_dir'=>'desc',
                                                          'sf'=>0,
                                                          'nr'=>25,
                                                          'from'=>'',
                                                          'to'=>'',
                                                          'elements'=>''
                                                      ),


                                        ),
                   'deals'=>array('table'=>array(
                                              'where'=>'where true',
                                              'f_field'=>'name',
                                              'f_value'=>'','f_show'=>false,
                                              'order'=>'name',
                                              'order_dir'=>'',
                                              'sf'=>0,
                                              'nr'=>25,
						'csv_export'=>array(
                                                                 'name'=>true,
                                                                 'trigger'=>true,
                                                                 'target'=>true,
                                                                 'status'=>false,
                                                                 'terms_description'=>false,
                                                                 'allowance_description'=>false,
                                                                 'terms_type'=>false

                                                             )


                                          )),
                   'position'=>array(
                                  'id'=>0,
                                  'action_after_create'=>'continue',
                                  'edit'=>'details',
                                  'employees'=>array(
                                                  'order'=>'code',
                                                  'order_dir'=>'desc',
                                                  'sf'=>0,
                                                  'nr'=>10,
                                                  'where'=>'where true',
                                                  'f_field'=>'name',
                                                  'f_value'=>'','f_show'=>false,
                                                  'from'=>'',
                                                  'to'=>'',
                                                  'details'=>0,
                                                  'elements'=>array('h_comp'=>1,'h_cont'=>1,'note'=>1)
                                              ),
                                  'history'=>array(
                                                'where'=>'where true',
                                                'f_field'=>'abstract',
                                                'f_value'=>'','f_show'=>false,
                                                'order'=>'date',
                                                'order_dir'=>'desc',
                                                'sf'=>0,
                                                'nr'=>25,
                                                'from'=>'',
                                                'to'=>'',
                                                'elements'=>''
                                            ),


                              ),
     
		'world'=>array(
           		 'countries'=>array(
                            'display'=>'all',
                            'order'=>'country_name',
                            'order_dir'=>'asc',
                            'sf'=>0,
                            'nr'=>20,
                            'where'=>'where true',
                            'f_field'=>'country_name',
                            'f_value'=>'',
                     	   ),
          		  'wregions'=>array(
			   'wregion_code'=>'',
                           'display'=>'all',
                           'order'=>'wregion_name',
                           'order_dir'=>'asc',
                           'sf'=>0,
                           'nr'=>20,
                           'where'=>'where true',
                           'f_field'=>'wregion_name',
                           'f_value'=>'',
                     	  ),
          		  'continents'=>array(
			     'continent_code'=>'',
                             'display'=>'all',
                             'order'=>'continent_name',
                             'order_dir'=>'desc',
                             'sf'=>0,
                             'nr'=>20,
                             'where'=>'where true',
                             'f_field'=>'continent_code',
                             'f_value'=>'',
                       	  ),
        ),	




			 'continent'=>array(
                           'code'=>'',
                           'wregions'=>array(
			   'wregion_code'=>'',
                           'display'=>'all',
                           'order'=>'wregion_name',
                           'order_dir'=>'asc',
                           'sf'=>0,
                           'nr'=>20,
                           'where'=>'where true',
                           'f_field'=>'wregion_name',
                           'f_value'=>'',
                               ),
                            'countries'=>array(
                             'display'=>'all',
                             'order'=>'country_name',
                             'order_dir'=>'desc',
                             'sf'=>0,
                             'nr'=>20,
                             'where'=>'where true',
                             'f_field'=>'country_name',
                             'f_value'=>'',
                                 ),
                            ),
                          
			 'wregion'=>array(
                           'code'=>'',
                           
                            'countries'=>array(
                             'display'=>'all',
                             'order'=>'country_name',
                             'order_dir'=>'desc',
                             'sf'=>0,
                             'nr'=>20,
                             'where'=>'where true',
                             'f_field'=>'country_name',
                             'f_value'=>'',
                                 ),
        )

               );


$yui_path="external_libs/yui/".$myconf['yui_version']."/build/";
$tmp_images_dir='app_files/pics/';

$customers_ids[0]='Id';
$customers_ids[1]='Act Id';
$customers_ids[2]='Post Code';

//overwrite configuration


$keys = array(
            "PATH_INFO",
            "PATH_TRANSLATED",
            "PHP_SELF",
            "REQUEST_URI",
            "SCRIPT_FILENAME",
            "SCRIPT_NAME",
            "QUERY_STRING"
        );

// Works in linux
$file=preg_replace('/conf.php/','myconf.php',__FILE__);

if (file_exists($file)) {
    include_once('myconf.php');
    if (isset($_myconf))
        foreach($_myconf as $key=>$value) {
        if (array_key_exists($key,$myconf))
            $myconf[$key]=$value;
    }
}


?>
