{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created:6 April 2018 at 11:35:22 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3
-->
*}


{if isset($data.top_margin)}{assign "top_margin" $data.top_margin}{else}{assign "top_margin" "0"}{/if}
{if isset($data.bottom_margin)}{assign "bottom_margin" $data.bottom_margin}{else}{assign "bottom_margin" "0"}{/if}


<div id="block_{$key}" data-block_key="{$key}"  block="{$data.type}" class="{$data.type} _block   {if $store->get('Store Type')=='Dropshipping'}dropshipping{/if}   {if !$data.show}hide{/if}" top_margin="{$top_margin}" bottom_margin="{$bottom_margin}" style="padding-top:{$top_margin}px;padding-bottom:{$bottom_margin}px"  >

    <div class="products {if !$data.item_headers}no_items_header{/if}"  data-sort="{$data.sort}" >


        {counter start=-1 print=false assign="counter"}
        {foreach from=$data.items item=item  name=items}

            <div class="product_wrap
                {if $logged_in and isset($settings['Display Stock Levels in Category']) and $settings['Display Stock Levels in Category']=='Hint_Bar'}stock_info_hint{/if}
                wrap type_{$item.type} " data-type="{$item.type}" {if $item.type=='product'} data-sort_code="{$item.sort_code}" data-sort_name="{$item.sort_name}{/if} ">


                {if $item.type=='product'}
                    {counter print=false assign="counter"}



                    <div class="product_block item product_container" data-product_id="{$item.product_id}">
                        <div class="product_header_text _au_vw_" >
                            {$item.header_text}
                        </div>


                        <div class="wrap_to_center product_image" >
                            <a href="{$item.link}"
                               data-analytics='{ "id": "{$item.code}", "name": "{$item.name|escape:'quotes'}",{if isset($item.category)} "category": "{$item.category}",{/if}{if isset($item.raw_price)} "price": "{$item.raw_price}",{/if}"list": "Family", "position":{$counter}}'
                                data-list="Family"
                               onclick="go_product(this); return !ga.loaded;">
                                <i class="fal fa-fw fa-external-link-square more_info" aria-hidden="true" title="{t}More info{/t}" ></i>
                            </a>

                            {if $logged_in}
                                {if $store->get('Store Type')!='Dropshipping'}
                                    <i data-product_id="{$item.product_id}" data-product_code="{$item.code}" data-favourite_key="0" class="favourite_{$item.product_id} favourite far fa-heart" ></i>
                                {/if}
                                    {if isset($settings['Display Stock Levels in Category']) and $settings['Display Stock Levels in Category']=='Dot'}
                                        <i class="stock_dot stock_level_{$item.product_id}  fa fa-fw fa-circle" ></i>
                                    {/if}
                            {/if}
                            <a href="{$item.link}"

                               data-analytics='{ "id": "{$item.code}", "name": "{$item.name|escape:'quotes'}",{if isset($item.category)} "category": "{$item.category}",{/if}{if isset($item.raw_price)} "price": "{$item.raw_price}",{/if}"list": "Family", "position":{$counter}}'
                               data-list="Family"
                               onclick="go_product(this); return !ga.loaded;"

                            ><img src="{$item.image_website}"  /></a>
                        </div>


                        <div class="product_description"  >
                            <span class="code">{$item.code}</span>


                            <div class="name item_name {if $item.name|strlen > 40}small{elseif $item.name|strlen > 60} very_small{/if} ">{$item.name}</div>

                        </div>
                        {if $logged_in}
                            <div class="product_prices  " >
                                <div class="product_price">{if empty($labels._product_price)}{t}Price{/t}{else}{$labels._product_price}{/if}: {$item.price} {if isset($item.price_unit)}<small>{$item.price_unit}</small>{/if}</div>
                                {if !empty($item.rrp)}<div><small>{if empty($labels._product_rrp)}{t}RRP{/t}{else}{$labels._product_rrp}{/if}: {$item.rrp}</small></div>{/if}
                            </div>
                        {else}
                            <div class="product_prices  " >
                                <div class="product_price"><small>{if empty($labels._login_to_see)}{t}For prices, please login or register{/t}{else}{$labels._login_to_see}{/if}</small></div>
                            </div>
                        {/if}

                        {if $logged_in}
                            {if $store->get('Store Type')=='Dropshipping'}
                                <div class="portfolio_row  portfolio_row_{$item.product_id} "  style="background: none;color:#000" >

                                    <div class=" edit_portfolio_item edit_portfolio_item_trigger add_to_portfolio sim_button " style="text-align: center"> <i class="fa fa-plus padding_right_5"></i>
                                       {if empty($labels._add_to_portfolio)}{t}Add to portfolio{/t}{else}{$labels._add_to_portfolio}{/if}</span>
                                    </div>
                                    <div class="edit_portfolio_item remove_from_portfolio hide "> <i class="fa fa-store-alt padding_right_5"></i>
                                        {if empty($labels._in_portfolio)}{t}In portfolio{/t}{else}{$labels._in_portfolio}{/if} <i style="position: absolute;right:10px;bottom:-1px" class="far edit_portfolio_item_trigger fa-trash-alt  sim_button" title="{if empty($labels._remove_from_portfolio)}{t}Remove from portfolio{/t}{else}{$labels._remove_from_portfolio}{/if}"></i>
                                    </div>

                                </div>
                            {else}
                                {if $item.web_state=='Out of Stock'}


                                {if !empty($item.next_shipment_timestamp)  }
                                    <div class="  out_of_stock_row  out_of_stock {if  $item.next_shipment_timestamp<$smarty.now}hide{/if} "   style="opacity:1;font-style: italic;;position:absolute;bottom:15px;height: 16px;line-height: 16px;padding:0px;padding-top:3px;font-size: 12px;width: 226px" >
                                        <span style="padding-left: 10px">{t}Expected{/t}: {$item.next_shipment_timestamp|date_format:"%x"}</span>
                                    </div>
                                {/if}


                                <div class="ordering log_in can_not_order  out_of_stock_row  out_of_stock " >
                                    <span class="product_footer label ">{if empty($labels.out_of_stock)}{t}Out of stock{/t}{else}{$labels.out_of_stock}{/if}</span>
                                    <i data-product_id="{$item.product_id}"
                                       data-label_remove_notification="{if empty($labels.remove_notification)}{t}Click to remove notification{/t},{else}{$labels.remove_notification}{/if}"
                                       data-label_add_notification="{if empty($labels.add_notification)}{t}Click to be notified by email when back in stock{/t},{else}{$labels.add_notification}{/if}"   title="{if empty($labels.add_notification)}{t}Click to be notified by email when back in stock{/t},{else}{$labels.add_notification}{/if}"    class="far fa-envelope like_button reminder out_of_stock_reminders_{$item.product_id} margin_left_5" aria-hidden="true"></i>


                                </div>



                            {elseif  $item.web_state=='For Sale'}

                                <div class="order_row empty  order_row_{$item.product_id} ">
                                    <input maxlength=6 class='order_input  ' type="text"' size='2' value='' data-ovalue=''>

                                    <div class="label sim_button" style="margin-left:57px">
                                        <i class="hide fa fa-hand-pointer fa-fw" aria-hidden="true"></i> <span class="hide">{if empty($labels._ordering_order_now)}{t}Order now{/t}{else}{$labels._ordering_order_now}{/if}</span>
                                    </div>


                                </div>
                            {/if}
                            {/if}

                        {else}
                            <div class="ordering log_out " >

                                <div onclick='window.location.href = "/login.sys"' class="mark_on_hover" ><span class="login_button" >{if empty($labels._Login)}{t}Login{/t}{else}{$labels._Login}{/if}</span></div>
                                <div onclick='window.location.href = "/register.sys"' class="mark_on_hover"><span class="register_button" > {if empty($labels._Register)}{t}Register{/t}{else}{$labels._Register}{/if}</span></div>
                            </div>

                        {/if}
                        {if $logged_in and isset($settings['Display Stock Levels in Category']) and $settings['Display Stock Levels in Category']=='Hint_Bar'}
                            <div  style="width: 100%;height: 5px;" class=" stock_hint stock_level_{$item.product_id}" >
                            </div>
                        {/if}

                    </div>

                {elseif $item.type=='text'}
                    <div  class="panel_txt_control hide" >
                        <span class="hide"><i class="fa fa-expand" title="{t}Padding{/t}"></i> <input size="2" style="height: 16px;" value="20"></span>
                        <i class="far fa-trash-alt padding_left_10 like_button" title="{t}Delete{/t}"></i>
                        <i onclick="close_panel_text(this)" class="fa fa-window-close button" style="float: right;margin-top:6px" title="{t}Close text edit mode{/t}"></i>

                    </div>
                    <div class="_au_vw_ txt {$item.size_class}">
                        {$item.text}
                    </div>


                {elseif $item.type=='image'}

                    {if $item.link!=''}
                        <a href="{$item.link}">
                    {/if}

                    <img class="panel edit {$item.size_class}"  src="{if !preg_match('/^http/',$item.image_website)}{/if}{$item.image_website}"   alt="{$item.title}" />
                            {if $item.link!=''}
                                </a>
                                {/if}

                                {elseif $item.type=='video'}

                    <div class="panel  {$item.type} {$item.size_class}" size_class="{$item.size_class}" video_id="{$item.video_id}">
                        <iframe width="470" height="{if $data.item_headers}330{else}290{/if}" frameallowfullscreen="" src="https://www.youtube.com/embed/{$item.video_id}?rel=0&amp;controls=0&amp;showinfo=0"></iframe>

                    </div>




                {/if}

            </div>


        {/foreach}
    </div>

    <div style="clear:both"></div>
</div>

<script>
    {foreach from=$data.items item=item  name=analytics_data}
    {if $item.type=='product'}ga('auTracker.ec:addImpression', { 'id': '{$item.code}', 'name': '{$item.name|escape:'quotes'}',{if isset($item.category)} 'category': '{$item.category}',{/if}{if isset($item.raw_price)} 'price': '{$item.raw_price}',{/if}'list': 'Family', 'position':{$smarty.foreach.analytics_data.index}});
    {/if}
    {/foreach}
</script>


