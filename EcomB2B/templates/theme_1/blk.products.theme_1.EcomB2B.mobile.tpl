{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 8 April 2018 at 17:59:34 GMT+8, Cyberjaya, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3
-->
*}


{if isset($data.top_margin)}{assign "top_margin" $data.top_margin}{else}{assign "top_margin" "0"}{/if}
{if isset($data.bottom_margin)}{assign "bottom_margin" $data.bottom_margin}{else}{assign "bottom_margin" "0"}{/if}



<div id="block_{$key}"  class=" _block {if !$data.show}hide{/if}"
     style="clear:both;padding-top:{$top_margin}px;padding-bottom:{$bottom_margin}px">
    <h1 class="products_title {if !$block.show_title}hide{/if}" style="margin-left:20px;">{$data.title}</h1>
    <div >
        {foreach from=$data.items item=item}
            <div class="store-item-list">
                    <span class="sub_wrap" style="">


                        <a href="{$item.link}" style="z-index: 10000;"><img src="{$item.image_mobile_website}" alt="{$item.name|escape}"></a>



                        <em style="margin-left:185px;padding-left: 0px;" class="single_line_height">

                            <div class="description"  {if ($item.name|count_characters)>40} style="font-size: 80% {elseif ($item.name|count_characters)>35}{/if}">{$item.name}</div>
                            {if $logged_in}
                                <div class="price" style="margin-top: 5px">
                                {t}Price{/t}: {$item.price}
                                </div>
                                {if $item.rrp!=''}
                                <div class="price">
                                  {t}RRP{/t}: {$item.rrp}
                                </div>
                            {/if}

                            {if $item.web_state=='Out of Stock'}

                                <div style="margin-top:10px;"><span style="padding:5px 10px" class="{if $item.out_of_stock_class=='launching_soon'}highlight-green color-white{else}highlight-red color-white{/if}">{$item.out_of_stock_label}</span></div>
                            {elseif $item.web_state=='For Sale'}
                                <div class="mobile_ordering"  data-settings='{ "pid":{$item.product_id} }'>
                                <i onclick="save_item_qty_change(this)" class="ordering_button one_less fa fa-fw  fa-minus-circle color-red-dark"></i>
                                <input type="number" min="0" value="" class="order_qty_{$item.product_id} needsclick order_qty">
                                <i onclick="save_item_qty_change(this)" style="display:none" class="ordering_button save far fa-save fa-fw color-blue-dark"></i>
                                <i onclick="save_item_qty_change(this)" class="ordering_button add_one fa fa-fw  fa-plus-circle color-green-dark"></i>
                            </div>
                            {/if}

                            {/if}
                        </em>
                             <u>{$item.code}</u>

                    </span>


            </div>
        {/foreach}
    </div>
    <div style="clear: both"></div>
</div>



