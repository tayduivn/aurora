﻿{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 16 November 2017 at 21:30:23 GMT+8, Kuala Lumpur , Malaysia
 Copyright (c) 2017, Inikoo

 Version 3
-->
*}{include file="theme_1/_head.theme_1.EcomB2B.tpl"}
<body xmlns="http://www.w3.org/1999/html">

{include file="analytics.tpl"}

<div class="wrapper_boxed">

    <div class="site_wrapper">

        {include file="theme_1/header.theme_1.EcomB2B.tpl"}

        <div class="content_fullwidth less3">
            <div class="container">





              <span id="ordering_settings" class="hide" data-labels='{
    "ordered":"<i class=\"fa fa-thumbs-o-up fa-flip-horizontal fa-fw \" aria-hidden=\"true\"></i> <span class=\"order_button_text\"> {if empty($labels._ordering_ordered)}{t}Ordered{/t}{else}{$labels._ordering_ordered}{/if}</span>",
    "order":"<i class=\"fa fa-hand-pointer-o fa-fw \" aria-hidden=\"true\"></i>  <span class=\"order_button_text\">{if empty($labels._ordering_order_now)}{t}Order now{/t}{else}{$labels._ordering_order_now}{/if}</span>",
    "update":"<i class=\"fa fa-hand-pointer-o fa-fw \" aria-hidden=\"true\"></i>  <span class=\"order_button_text\">{if empty($labels._ordering_updated)}{t}Updated{/t}{else}{$labels._ordering_updated}{/if}</span>"
    }'></span>


                {assign 'content_data' $webpage->get('Content Published Data')}


                <div id="page_content" class="asset_container">



                    <div class="description_block">


                <div class="title"><h1 ><span id="_title" class="_title" >{$content._title}</span><span class="line"></span></h1></div>
                        <div id="_with_items_div" class="{if ($products|@count)==0}hide{/if}">{$content._text}</div>
                        <div id="_no_items_div" class="{if ($products|@count)>0}hide{/if}">{$content._text_empty}</div>

                    </div>



             <div class="warp">
                 {foreach from=$products item=product_data key=stack_index}
                     <div class="warp_element">


                         {assign 'product' $product_data.object}
                         <div id="product_target_div_{$stack_index}" stack_index="{$stack_index}" product_code="{$product->get('Code')}" product_id="{$product->id}"
                              class="product_block product_showcase product_container" style="position:relative;border-bottom:none;">

                             <a href="{$product->get('Code')|lower}">

                                 <i class="fa fa-info-circle more_info" aria-hidden="true" title="More info"></i>
                             </a>
                             {if !empty($customer)}
                                 {assign 'favourite_key' {$product->get('Favourite Key',{$customer->id})} }
                                 <span style="position:absolute;top:5px;left:5px" class="  favourite  " product_id="{$product->id}" favourite_key="{$favourite_key}">
                                                <i class="fa {if $favourite_key}fa-heart marked{else}fa-heart-o{/if}" aria-hidden="true"></i>  </span>
                             {/if}


                             <div class="product_header_text fr-view">
                                 {$product_data.header_text}
                             </div>


                             <a href="{$product->get('Code')|lower}">
                                 <div class="wrap_to_center product_image">


                                     <img src="{$product->get('Image')}"/>
                                 </div>
                             </a>

                             <div class="product_description">
                                 <span class="code">{$product->get('Code')}</span>
                                 <div class="name item_name">{$product->get('Name')}</div>

                             </div>
                             {if $logged_in}
                                 <div class="product_prices log_in ">
                                     <div class="product_price">{if empty($labels._product_price)}{t}Price{/t}{else}{$labels._product_price}{/if}: {$product->get('Price')}</div>
                                     {assign 'rrp' $product->get('RRP')}
                                     {if $rrp!=''}
                                         <div>{if empty($labels._product_rrp)}{t}RRP{/t}{else}{$labels._product_rrp}{/if}: {$rrp}</div>{/if}
                                 </div>
                             {else}
                                 <div class="product_prices log_out">
                                     <div>{if empty($labels._login_to_see)}{t}For prices, please login or register{/t}{else}{$labels._login_to_see}{/if}</div>
                                 </div>
                             {/if}


                             {if $logged_in}


                                 {if $product->get('Web State')=='Out of Stock'}

                                     {if isset($customer)}

                                         {assign 'reminder_key' {$product->get('Reminder Key',{$customer->id})} }
                                         <div class="out_of_stock_row {$product->get('Out of Stock Class')}">
                                                    <span class="label">
                                                    {$product->get('Out of Stock Label')}
                                                        <span class="label sim_button "> <i reminder_key="{$reminder_key}"
                                                                                            title="{if $reminder_key>0}{t}Click to remove notification{/t}{else}{t}Click to be notified by email{/t}{/if}"
                                                                                            class="reminder hide fa {if $reminder_key>0}fa-envelope{else}fa-envelope-o{/if}" aria-hidden="true"></i>  </span>
                                                    </span>
                                         </div>
                                     {/if}




                                 {elseif $product->get('Web State')=='For Sale'}
                                     {assign 'quantity_ordered' $product->get('Ordered Quantity',$order_key) }
                                     <div class="order_row {if $quantity_ordered!=''}ordered{else}empty{/if}">
                                         <input maxlength=6 style="" class='order_input ' id='but_qty{$product->id}' type="text"' size='2' value='{$quantity_ordered}' ovalue='{$quantity_ordered}'>
                                         {if $quantity_ordered==''}
                                             <div class="label sim_button   " style="margin-left:57px"><i class="fa fa-hand-pointer-o fa-fw" aria-hidden="true"></i> <span
                                                         class="">{if empty($labels._ordering_order_now)}{t}Order now{/t}{else}{$labels._ordering_order_now}{/if}</span></div>
                                         {else}
                                             <span class="label sim_button"><i class="fa  fa-thumbs-o-up fa-flip-horizontal fa-fw" aria-hidden="true"></i> <span
                                                         class="">{if empty($labels._ordering_ordered)}{t}Ordered{/t}{else}{$labels._ordering_ordered}{/if}</span></span>
                                         {/if}

                                     </div>
                                 {/if}


                             {else}
                                 <div class=" order_row " style="display:flex;">
                                     <div onclick='window.location.href = "/login.sys"' class="mark_on_hover" style="flex-grow:1;text-align:center;border-right:1px solid #fff;  font-weight: 800;"><span
                                                 class="sim_button">{if empty($labels._Login)}{t}Login{/t}{else}{$labels._Login}{/if}</span></div>
                                     <div onclick='window.location.href = "/register.sys"' class="mark_on_hover" style="flex-grow:1;text-align:center;border-left:1px solid #fff;  font-weight: 800;"><span
                                                 class="sim_button">{if empty($labels._Register)}{t}Register{/t}{else}{$labels._Register}{/if}</span></div>
                                 </div>
                             {/if}

                         </div>


                     </div>
                 {/foreach}
                 <div style="clear:both"></div>
             </div>



                </div>
            </div>


            <div class="clearfix marb12"></div>

        {include file="theme_1/footer.theme_1.EcomB2B.tpl"}

    </div>

</div>



{include file="theme_1/bottom_scripts.theme_1.EcomB2B.tpl"}</body>

</html>
