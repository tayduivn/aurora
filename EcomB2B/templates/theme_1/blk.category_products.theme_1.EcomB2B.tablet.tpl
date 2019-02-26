{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created:6 April 2018 at 13:45:33 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3
-->
*}


{if isset($data.top_margin)}{assign "top_margin" $data.top_margin}{else}{assign "top_margin" "0"}{/if}
{if isset($data.bottom_margin)}{assign "bottom_margin" $data.bottom_margin}{else}{assign "bottom_margin" "0"}{/if}


<div id="block_{$key}" data-block_key="{$key}"  block="{$data.type}" class="{$data.type} _block {if !$data.show}hide{/if}" top_margin="{$top_margin}" bottom_margin="{$bottom_margin}" style="padding-top:{$top_margin}px;padding-bottom:{$bottom_margin}px"  >


                    <span id="ordering_settings" class="hide" data-labels='{
    "ordered":"<i class=\"fa fa-thumbs-up fa-flip-horizontal fa-fw \" aria-hidden=\"true\"></i> <span class=\"order_button_text\"> {if empty($labels._ordering_ordered)}{t}Ordered{/t}{else}{$labels._ordering_ordered}{/if}</span>",
    "order":"<i class=\"fa fa-hand-pointer fa-fw \" aria-hidden=\"true\"></i>  <span class=\"order_button_text\">{if empty($labels._ordering_order_now)}{t}Order now{/t}{else}{$labels._ordering_order_now}{/if}</span>",
    "update":"<i class=\"fa fa-hand-pointer fa-fw \" aria-hidden=\"true\"></i>  <span class=\"order_button_text\">{if empty($labels._ordering_updated)}{t}Updated{/t}{else}{$labels._ordering_updated}{/if}</span>"
    }'></span>

    <div class="products {if !$data.item_headers}no_items_header{/if}"  data-sort="{$data.sort}" >
        {counter start=-1 print=false assign="counter"}
        {foreach from=$data.items item=item}


            <div class="product_wrap wrap type_{$item.type} " data-type="{$item.type}" {if $item.type=='product'} data-sort_code="{$item.sort_code}" data-sort_name="{$item.sort_name}{/if} ">


                {if $item.type=='product'}
                    {counter print=false assign="counter"}

                    <div class="product_block tablet item product_container" data-product_id="{$item.product_id}">
                        <div class="product_header_text fr-view" >
                            {$item.header_text}
                        </div>

                        <div class="wrap_to_center product_image" >
                            <a href="{$item.link}"
                               data-analytics='{ "id": "{$item.code}", "name": "{$item.name|escape:'quotes'}",{if isset($item.category)} "category": "{$item.category}",{/if}{if isset($item.raw_price)} "price": "{$item.raw_price}",{/if}"list": "Family", "position":{$counter}}'
                               data-list="Family"
                               onclick="onProductClick(this); return !ga.loaded;"

                            ><i class="fal fa-fw fa-external-link-square more_info" aria-hidden="true"></i></a>
                            {if $logged_in}<i    data-product_code="{$item.code}" data-product_id="{$item.product_id}" data-favourite_key="0" class="favourite_{$item.product_id} favourite far  fa-heart" aria-hidden="true"></i>
                            {/if}
                            <img src="{$item.image_website}"  />
                        </div>


                        <div class="product_description" style="font-size: 14px;padding:0px 4px;margin-bottom:4px" >
                            <span class="code">{$item.code}</span>
                            <div class="name item_name">{$item.name}</div>

                        </div>
                        {if $logged_in}
                            <div class="product_prices  " style="font-size: 14px;padding:0px 4px">
                                <div class="product_price">{if empty($labels._product_price)}{t}Price{/t}{else}{$labels._product_price}{/if}: {$item.price} {if isset($item.price_unit)}<small>{$item.price_unit}</small>{/if}</div>
                                {if !empty($item.rrp)}<div><small>{if empty($labels._product_rrp)}{t}RRP{/t}{else}{$labels._product_rrp}{/if}: {$item.rrp}</small></div>{/if}
                            </div>
                        {else}
                            <div class="product_prices  " style="font-size: 14px;padding:0px 4px">
                                <div class="product_price">{if empty($labels._login_to_see)}{t}For prices, please login or register{/t}{else}{$labels._login_to_see}{/if}</div>

                            </div>
                        {/if}


                        {if $logged_in}

                            {if $item.web_state=='Out of Stock'}

                                {if !empty($item.next_shipment_timestamp)  and $item.next_shipment_timestamp>$smarty.now }
                                    <div class="  out_of_stock_row  out_of_stock "
                                         style="z-index:1000;opacity:1;font-style: italic;;position:absolute;bottom:28px;height: 16px;line-height: 16px;padding:0px;padding-top:3px;font-size: 12px;width: 224px" >
                                        <span style="padding-left: 10px">{t}Expected{/t}: {$item.next_shipment_timestamp|date_format:"%x"}</span>
                                    </div>
                                {/if}


                                <div class="ordering log_in can_not_order  out_of_stock_row  out_of_stock ">
                                    <span {if !empty($item.next_shipment_timestamp)  and $item.next_shipment_timestamp>$smarty.now }style="position: relative;top:5px;"{/if}>
                                    <span class="product_footer label ">{if empty($labels.out_of_stock)}{t}Out of stock{/t}{else}{$labels.out_of_stock}{/if}</span>
                                    <i data-product_id="{$item.product_id}"
                                       data-label_remove_notification="{if empty($labels.remove_notification)}{t}Click to remove notification{/t},{else}{$labels.remove_notification}{/if}"
                                       data-label_add_notification="{if empty($labels.add_notification)}{t}Click to be notified by email when back in stock{/t},{else}{$labels.add_notification}{/if}"   title="{if empty($labels.add_notification)}{t}Click to be notified by email when back in stock{/t},{else}{$labels.add_notification}{/if}"    class="far fa-envelope like_button reminder out_of_stock_reminders_{$item.product_id} margin_left_5" aria-hidden="true"></i>
                                    </span>
                                </div>



                            {elseif  $item.web_state=='For Sale'}

                                <div class="mobile_ordering" style="text-align:center;font-size: 14px;" data-settings='{ "pid":{$item.product_id} }'>
                                    <i onclick="save_item_qty_change(this)" class="ordering_button one_less fa fa-fw  fa-minus-circle color-red-dark"></i>
                                    <input type="number" min="0" value="" class="order_qty_{$item.product_id}  needsclick order_qty">
                                    <i onclick="save_item_qty_change(this)" class="hide ordering_button save fa fa-save fa-fw color-blue-dark"></i>
                                    <i onclick="save_item_qty_change(this)" class="ordering_button add_one fa fa-fw  fa-plus-circle color-green-dark"></i>
                                </div>
                            {/if}

                        {else}
                            <div class=" log_out_prod_links" >
                                <div onclick='window.location.href = "/login.sys"'  ><span>{if empty($labels._Login)}{t}Login{/t}{else}{$labels._Login}{/if}</span></div>
                                <div onclick='window.location.href = "/register.sys"'><span>{if empty($labels._Register)}{t}Register{/t}{else}{$labels._Register}{/if}</span></div>
                            </div>

                        {/if}






                    </div>

                {elseif $item.type=='text'}
                    <div  class="panel_txt_control hide" >
                        <span class="hide"><i class="fa fa-expand" title="{t}Padding{/t}"></i> <input size="2" style="height: 16px;" value="20"></span>
                        <i class="far fa-trash-alt padding_left_10 like_button" title="{t}Delete{/t}"></i>
                        <i onclick="close_panel_text(this)" class="fa fa-window-close button" style="float: right;margin-top:6px" title="{t}Close text edit mode{/t}"></i>

                    </div>
                    <div style="padding:{$item.padding}px" size_class="{$item.size_class}" data-padding="{$item.padding}" class="fr-view txt {$item.size_class}">{$item.text}</div>


                {elseif $item.type=='image'}


                    <img class="panel edit {$item.size_class}" size_class="{$item.size_class}" src="{if !preg_match('/^http/',$item.image_website)}{/if}{$item.image_website}"  data-image_website="{$item.image_website}"  data-src="{$item.image_src}"    link="{$item.link}"  alt="{$item.title}" />


                {elseif $item.type=='video'}

                    <div class="panel  {$item.type} {$item.size_class}" size_class="{$item.size_class}" video_id="{$item.video_id}">
                        <iframe width="470" height="{if $data.item_headers}330{else}290{/if}" frameborder="0" allowfullscreen="" src="https://www.youtube.com/embed/{$item.video_id}?rel=0&amp;controls=0&amp;showinfo=0"></iframe>
                        <div class="block_video" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div>
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