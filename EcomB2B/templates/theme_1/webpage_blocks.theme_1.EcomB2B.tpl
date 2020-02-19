{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 17 July 2017 at 10:04:33 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2017, Inikoo

 Version 3
-->
*}

{include file="theme_1/_head.theme_1.EcomB2B.tpl"}
<body xmlns="http://www.w3.org/1999/html" data-device_prefix="" class="{$website->get('background_type')}"
      data-ws="{if $logged_in and $website->get('Website Type')=='EcomDS'}y{else}n{/if}" {if $logged_in} data-ws_key="{$ws_key}" {/if}>
{include file="analytics.tpl"}


{if $logged_in}
    <span id="ordering_settings" class="hide" data-website_key="{$website->id}" data-labels='{
    "zero_money":"{$zero_money}",
    "ordered":"<i class=\"fa fa-thumbs-up fa-flip-horizontal fa-fw \" aria-hidden=\"true\"></i> <span class=\"order_button_text\"> {if empty($labels._ordering_ordered)}{t}Ordered{/t}{else}{$labels._ordering_ordered}{/if}</span>",
    "order":"<i class=\"fa fa-hand-pointer fa-fw \" aria-hidden=\"true\"></i>  <span class=\"order_button_text\">{if empty($labels._ordering_order_now)}{t}Order now{/t}{else}{$labels._ordering_order_now}{/if}</span>",
    "update":"<i class=\"fa fa-hand-pointer fa-fw \" aria-hidden=\"true\"></i>  <span class=\"order_button_text\">{if empty($labels._ordering_updated)}{t}Updated{/t}{else}{$labels._ordering_updated}{/if}</span>"
    }'></span>
{/if}
<div class="wrapper_boxed">
    <div class="site_wrapper">
        {include file="theme_1/header.theme_1.EcomB2B.tpl"}
        <div id="body" class="{$website->get('content_background_type')}">
            {if $navigation.show}
                <div class="navigation top_body">
                    <div class="breadcrumbs">
                        {foreach from=$navigation.breadcrumbs item=$breadcrumb name=breadcrumbs}
                            <span class="breadcrumb {if isset($breadcrumb.class)}{$breadcrumb.class}{/if} "><a href="{$breadcrumb.link}" title="{$breadcrumb.title}">{$breadcrumb.label}</a> </span>
                            {if !$smarty.foreach.breadcrumbs.last}
                                <i class="fas padding_left_10 padding_right_10 fa-angle-double-right"></i>
                            {/if}
                        {/foreach}
                    </div>
                    <div class="nav">{if $navigation.prev}<a href="{$navigation.prev.link}" title="{$navigation.prev.title}"><i class="fas fa-arrow-left"></i></a>{/if} {if $navigation.next}<a
                            href="{$navigation.next.link}" title="{$navigation.next.title}"><i class="fas fa-arrow-right next"></i></a>{/if}</div>
                    <div style="clear:both"></div>
                </div>
            {/if}
            {if isset($discounts) and count($discounts.deals)>0 }
                <div class="discounts top_body">
                    {foreach from=$discounts.deals item=deal_data }
                        <div class="discount_card" key="{$deal_data.key}">
                            <div class="discount_icon">{$deal_data.icon}</div>
                            <span class="discount_name">{$deal_data.name}</span>
                            {if  $deal_data.until!=''}
                                <small class="padding_left_10"><span id="_offer_valid_until" class="website_localized_label">
                                {if !empty($labels._offer_valid_until)}{$labels._offer_valid_until}{else}{t}Valid until{/t}{/if}</span>: {$deal_data.until_formatted}
                                </small>
                            {/if}
                            <br/>
                            <span class="discount_term">{$deal_data.term}</span>
                            <span class="discount_allowance">{$deal_data.allowance}</span>
                        </div>
                    {/foreach}
                    <div style="clear:both"></div>
                </div>
            {/if}

            {assign "with_iframe" false}
            {assign "with_login" false}
            {assign "with_register" false}
            {assign "with_basket" false}
            {assign "with_client_basket" false}
            {assign "with_checkout" false}
            {assign "with_profile" false}
            {assign "with_client" false}
            {assign "with_favourites" false}
            {assign "with_portfolio" false}
            {assign "with_products_portfolio" false}
            {assign "with_clients" false}
            {assign "with_clients_orders" false}
            {assign "with_client_order_new" false}
            {assign "with_search" false}
            {assign "with_thanks" false}
            {assign "with_gallery" false}
            {assign "with_product_order_input" false}
            {assign "with_reset_password" false}
            {assign "with_unsubscribe" false}
            {assign "with_category_products" false}
            {assign "with_datatables" false}


            {if $webpage->get('Webpage Scope')=='Category Products'}
                {if $website->get('Website Type')=='EcomDS' and $logged_in}
                    <div style="border-bottom: 1px solid #ccc;height: 35px;line-height: 35px;padding: 0 20px">

                        <a href="catalog_images.zip.php?scope=category&scope_key={$webpage->get('Webpage Scope Key')}"><i class="fal fa-images"></i> {t}Families' images (including products){/t}</a>
                        <a style="margin-left: 30px" href="catalog_data_feed.php?output=CSV&scope=category&scope_key={$webpage->get('Webpage Scope Key')}"><i class="fal fa-database"></i> {t}Families’ products data feed{/t}</a>
                        <div class="portfolio_in_family hide" style="float:right" ><i class="fa fa-store-alt "></i> <span class="number_products_in_portfolio_in_family"></span>/<span class="number_products_in_family"></span> </div>
                    </div>


                {/if}

            {/if}

            {if $webpage->get('Webpage Code')=='portfolio.sys' and $logged_in}

                    <div style="border-bottom: 1px solid #ccc;height: 35px;line-height: 35px;padding: 0 20px">

                        <a class="hide images_zip"  href=""><i class="fal fa-images"></i> {t}Portfolio Images{/t} (.zip)</a>
                        <a class="hide data_feed" style="margin-left: 30px"  href=""><i class="fal fa-database"></i> {t}Portfolio products data feed{/t}</a>

                    </div>




            {/if}

            {if !empty($content.blocks) and  $content.blocks|is_array}
                {foreach from=$content.blocks item=$block key=key}


                    {if $block.show}

                        {if $block.type=='basket' }
                            {if $logged_in}{assign "with_basket" 1}
                                <div id="basket">
                                    <div style="text-align: center">
                                        <i style="font-size: 60px;padding:100px" class="fa fa-spinner fa-spin"></i>
                                    </div>

                                </div>
                            {else}
                                {include file="theme_1/blk.forbidden.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}
                        {elseif $block.type=='client_basket'}
                            {if $logged_in}{assign "with_client_basket" 1}
                                <div id="client_basket">
                                    <div style="text-align: center">
                                        <i style="font-size: 60px;padding:100px" class="fa fa-spinner fa-spin"></i>
                                    </div>

                                </div>
                            {else}
                                {include file="theme_1/blk.forbidden.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}
                        {elseif $block.type=='profile'}
                            {if $logged_in}
                                {assign "with_profile" 1}
                                <div id="profile">
                                    <div style="text-align: center">
                                        <i style="font-size: 60px;padding:100px" class="fa fa-spinner fa-spin"></i>
                                    </div>
                                </div>
                            {else}
                                {include file="theme_1/blk.forbidden.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}
                        {elseif $block.type=='client'}
                            {if $logged_in}
                                {assign "with_client" 1}
                                <div id="client">
                                    <div style="text-align: center">
                                        <i style="font-size: 60px;padding:100px" class="fa fa-spinner fa-spin"></i>
                                    </div>
                                </div>
                            {else}
                                {include file="theme_1/blk.forbidden.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}



                        {elseif $block.type=='checkout'}
                            {if $logged_in}{assign "with_checkout" 1}
                                <div id="checkout">
                                    <div style="text-align: center">
                                        <i style="font-size: 60px;padding:100px" class="fa fa-spinner fa-spin"></i>
                                    </div>
                                </div>
                            {else}
                                {include file="theme_1/blk.forbidden.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}
                        {elseif $block.type=='favourites'}

                            {if $logged_in}
                                {assign "with_favourites" 1}
                                {assign "with_category_products" 1}
                                <div id="favourites">
                                    <div style="text-align: center">
                                        <i style="font-size: 60px;padding:100px" class="fa fa-spinner fa-spin"></i>
                                    </div>
                                </div>
                            {else}
                                {include file="theme_1/blk.forbidden.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}
                        {elseif $block.type=='portfolio'}

                            {if $logged_in}
                                {assign "with_portfolio" 1}
                                {assign "with_datatables" 1}
                                {include file="theme_1/blk.{$block.type}.theme_1.EcomB2B.tpl" data=$block key=$key  }

                            {else}
                                {include file="theme_1/blk.forbidden.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}
                        {elseif $block.type=='clients'}

                            {if $logged_in}
                                {assign "with_clients" 1}
                                {assign "with_datatables" 1}
                                {include file="theme_1/blk.{$block.type}.theme_1.EcomB2B.tpl" data=$block key=$key  }

                            {else}
                                {include file="theme_1/blk.forbidden.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}
                        {elseif $block.type=='clients_orders'}

                            {if $logged_in}
                                {assign "with_clients_orders" 1}
                                {assign "with_datatables" 1}
                                {include file="theme_1/blk.{$block.type}.theme_1.EcomB2B.tpl" data=$block key=$key  }

                            {else}
                                {include file="theme_1/blk.forbidden.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}

                        {elseif $block.type=='client_order_new'}

                            {if $logged_in}
                                {assign "with_client_order_new" 1}
                                {assign "with_datatables" 1}
                                {include file="theme_1/blk.{$block.type}.theme_1.EcomB2B.tpl" data=$block key=$key  }

                            {else}
                                {include file="theme_1/blk.forbidden.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}

                        {elseif $block.type=='thanks'}



                            {if $logged_in}{assign "with_thanks" 1}
                                <div id="thanks">
                                    <div style="text-align: center">
                                        <i style="font-size: 60px;padding:100px" class="fa fa-spinner fa-spin"></i>
                                    </div>

                                </div>
                            {else}
                                {include file="theme_1/blk.forbidden.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}
                        {elseif $block.type=='login'}

                            {if !$logged_in}



                                {assign "with_login" 1}
                                {include file="theme_1/blk.{$block.type}.theme_1.EcomB2B.tpl" data=$block key=$key  }

                            {else}
                                {include file="theme_1/blk.already_logged_in.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}
                        {elseif $block.type=='register'}

                            {if !$logged_in}
                                {assign "with_register" 1}
                                {include file="theme_1/blk.{$block.type}.theme_1.EcomB2B.tpl" data=$block key=$key  }

                            {else}
                                {include file="theme_1/blk.already_logged_in.theme_1.EcomB2B.tpl" data=$block key=$key   }
                            {/if}
                        {else}
                            {if $block.type=='search'   }{assign "with_search" 1}{/if}
                            {if $block.type=='iframe'   }{assign "with_iframe" 1}{/if}
                            {if $block.type=='product'   }{assign "with_gallery" 1}{/if}
                            {if $block.type=='reset_password' }{assign "with_reset_password" 1}{/if}
                            {if $block.type=='unsubscribe'}{assign "with_unsubscribe" 1}{/if}

                            {if $block.type=='category_products' or   $block.type=='products'  or   $block.type=='product' }


                                {if $store->get('Store Type')=='Dropshipping'}
                                    {assign "with_products_portfolio" 1}
                                {else}
                                    {assign "with_product_order_input" 1}
                                {/if}


                            {/if}
                            {if $block.type=='category_products'  }


                                {if $store->get('Store Type')=='Dropshipping'}
                                    {assign "with_products_portfolio" 1}
                                {else}
                                    {assign "with_category_products" 1}
                                {/if}

                            {/if}



                            {include file="theme_1/blk.{$block.type}.theme_1.EcomB2B.tpl" data=$block key=$key  }

                        {/if}

                    {/if}
                {/foreach}
            {/if}

        </div>


        {include file="theme_1/footer.theme_1.EcomB2B.tpl"}


    </div>

</div>

{include file="theme_1/scripts_webpage_blocks.theme_1.EcomB2B.tpl"}

</body></html>