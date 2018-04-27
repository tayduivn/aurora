﻿{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 16 November 2017 at 21:30:23 GMT+8, Kuala Lumpur , Malaysia
 Copyright (c) 2017, Inikoo

 Version 3
-->
*}




{if isset($data.top_margin)}{assign "top_margin" $data.top_margin}{else}{assign "top_margin" "0"}{/if}
{if isset($data.bottom_margin)}{assign "bottom_margin" $data.bottom_margin}{else}{assign "bottom_margin" "0"}{/if}


<div id="block_{$key}" data-block_key="{$key}" block="{$data.type}" class="{$data.type}   {if !$data.show}hide{/if}"  style="padding-top:{$top_margin}px;padding-bottom:{$bottom_margin}px"  >

<div class="container fr-view">
{if  ($products|@count)==0}
    <div  >{$data.labels.no_items}</div>

{else}
    <div  >{$data.labels.with_items}</div>

{/if}


</div>


<div class="warp products no_items_header clear">
    {foreach from=$products item=item }

    <div class="product_wrap wrap type_{$item.type} " data-type="{$item.type}" {if $item.type=='product'} data-sort_code="{$item.sort_code}" data-sort_name="{$item.sort_name}{/if} ">
        <div class="product_block item product_container" data-product_id="{$item.product_id}">



            <div class="wrap_to_center product_image" >
                <a href="{$item.link}"><i class="fal fa-fw fa-external-link-square more_info" aria-hidden="true"  title="{t}More info{/t}"  ></i></a>

                {if $logged_in}
                    <i    data-product_id="{$item.product_id}" data-favourite_key="0" class="favourite_{$item.product_id} favourite far  fa-heart" aria-hidden="true"></i>
                {/if}
                <img src="{$item.image_src}"  />
            </div>


            <div class="product_description"  >
                <span class="code">{$item.code}</span>
                <div class="name item_name">{$item.name}</div>

            </div>
            {if $logged_in}
                <div class="product_prices  " >
                    <div class="product_price">{if empty($labels._product_price)}{t}Price{/t}{else}{$labels._product_price}{/if}: {$item.price}</div>
                    {assign 'rrp' $item.rrp}
                    {if $rrp!=''}<div>{if empty($labels._product_rrp)}{t}RRP{/t}{else}{$labels._product_rrp}{/if}: {$rrp}</div>{/if}
                </div>
            {else}
                <div class="product_prices  " >
                    <div class="product_price">{if empty($labels._login_to_see)}{t}For prices, please login or register{/t}{else}{$labels._login_to_see}{/if}</div>

                </div>
            {/if}


            {if $logged_in}

                {if $item.web_state=='Out of Stock'}
                    <div class="ordering log_in can_not_order  out_of_stock_row  {$item.out_of_stock_class} ">

                        <span class="product_footer label ">{$item.out_of_stock_label}</span>
                        <span class="product_footer reminder"><i class="fa fa-envelope hide" aria-hidden="true"></i>  </span>


                    </div>
                {elseif  $item.web_state=='For Sale'}

                    <div class="order_row empty  order_row_{$item.product_id} ">
                        <input maxlength=6 style="" class='order_input  ' type="text"' size='2' value='' data-ovalue=''>

                        <div class="label sim_button" style="margin-left:57px">
                            <i class="hide fa fa-hand-pointer fa-fw" aria-hidden="true"></i> <span class="hide">{if empty($labels._ordering_order_now)}{t}Order now{/t}{else}{$labels._ordering_order_now}{/if}</span>
                        </div>


                    </div>
                {/if}

            {else}
                <div class="ordering log_out " >

                    <div onclick='window.location.href = "/login.sys"' class="mark_on_hover" ><span class="login_button" >{if empty($labels._Login)}{t}Login{/t}{else}{$labels._Login}{/if}</span></div>
                    <div onclick='window.location.href = "/register.sys"' class="mark_on_hover"><span class="register_button" > {if empty($labels._Register)}{t}Register{/t}{else}{$labels._Register}{/if}</span></div>


                </div>

            {/if}






        </div>

    </div>


    {/foreach}
    <div style="clear:both"></div>
</div>

</div>