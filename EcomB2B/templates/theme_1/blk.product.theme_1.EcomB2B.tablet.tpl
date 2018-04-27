{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 21 March 2018 at 15:30:02 GMT+8, Sanur, Bali, Indonesia
 Copyright (c) 2017, Inikoo

 Version 3
-->
*}

<style>
    .swiper-container {
        width: 100%;

    }

    .activity-item strong{
        padding-left: 0px;
    }

</style>

{if isset($data.top_margin)}{assign "top_margin" $data.top_margin}{else}{assign "top_margin" "0"}{/if}
{if isset($data.bottom_margin)}{assign "bottom_margin" $data.bottom_margin}{else}{assign "bottom_margin" "0"}{/if}


<div id="block_{$key}"  class="{if !$data.show}hide{/if}" style="padding-top:{$top_margin}px;padding-bottom:{$bottom_margin}px">

            <div>

                <div style="padding:20px">
                <div class="images one-half-responsive">

                    <figure class="main_image" style="margin: 0px;padding:0px" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">

                        <a href="{$data.image.src}" itemprop="contentUrl" data-w="{$data.image.width}" data-h="{$data.image.height}">
                            <img style="max-height: 450px;margin:0px auto" src="{$data.image.image_website}" itemprop="image" alt="{$data.image.caption}">
                        </a>
                    </figure>

                    <div class="gallery" style="display: flex;flex-wrap: wrap ;max-width: 330px" itemscope itemtype="http://schema.org/ImageGallery">

                        {foreach from=$data.other_images item=image name=foo}
                            <figure style="margin: 0px 5px" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject"

                            >
                                <a href="{$image.src}" itemprop="contentUrl" data-w="{$image.width}" data-h="{$image.height}">
                                    <img style="height: 50px" src="{$image.image_website}" itemprop="thumbnail" alt="{$image.caption}"/>
                                </a>
                            </figure>
                        {/foreach}


                    </div>


                </div>

                <div class="one-half-responsive last-column ">
                    <h1 class="">{$product->get('Code')}</h1>
                    <h2 class="">{$product->get('Name')}</h2>

                    {if $logged_in}


                        {if $product->get('Web State')=='Out of Stock'}
                            <div style="margin-top: 10px" class="notification-small  {if $product->get('Out of Stock Class')=='launching_soon'}bg-green-light{else}bg-red-light{/if} ">
                                <strong class="{if $product->get('Out of Stock Class')=='launching_soon'}bg-green-dark{else}bg-red-dark{/if} "><i class="ion-information-circled"></i></strong>
                                <p style="line-height: 50px;">
                                    {$product->get('Out of Stock Label')}
                                </p>
                            </div>
                        {elseif $product->get('Web State')=='For Sale'}
                            <div  >




                                <div class="mobile_ordering" data-settings='{ "pid":{$product->id} }'>
                                    <i onclick="save_item_qty_change(this)" class="ordering_button one_less fa fa-fw  fa-minus-circle color-red-dark"></i>
                                    <input type="number" min="0" value="" class="needsclick order_qty">
                                    <i onclick="save_item_qty_change(this)" style="display:none" class="ordering_button save far fa-fw fa-save color-blue-dark"></i>
                                    <i onclick="save_item_qty_change(this)" class="ordering_button add_one fa fa-fw  fa-plus-circle color-green-dark"></i>
                                </div>

                            </div>
                        {/if}
                    {else}
                        <div class="notification-small bg-red-light tap-hide animate-right">
                            <strong class="bg-red-dark"><i class="ion-information-circled"></i></strong>
                            <p>
                                {if empty($labels._login_to_see)}{t}For prices, please login or register{/t}{else}{$labels._login_to_see}{/if}
                            </p>
                        </div>
                    {/if}

                    <div class="decoration half-bottom full-top"></div>

                    {if $logged_in}
                        <div class="store-product-rating half-top">
                            <h2>{t}Price{/t}: {$product->get('Price')}</h2>
                            {if $product->get('RRP')!=''}<span>{t}RRP{/t}: {$product->get('RRP')}</span>{/if}
                        </div>
                    {else}
                        <div class="container">
                            <div class="one-half">
                                <a href="/login.sys" class="button button-icon button-blue button-round button-full button-xs no-bottom"><i
                                            class="ion-log-in"></i>{if empty($labels._Login)}{t}Login{/t}{else}{$labels._Login}{/if}</a>
                            </div>
                            <div class="one-half last-column">
                                <a href="/register.sys" class="button button-icon button-green button-round button-full button-xs no-bottom"><i
                                            class="ion-android-add-circle"></i>{if empty($labels._Register)}{t}Register{/t}{else}{$labels._Register}{/if}</a>
                            </div>
                            <div class="clear"></div>
                        </div>
                    {/if}




                </div>
                    <div class="clear"></div>
                </div>

                <div class="content single_line_height clear">



                    <div class="store-product-header">

                        <div class="one-half-responsive">
                        <p>
                            {$data.text|replace:'<p><br></p>':''}
                        </p>

</div>

                        <div class="one-half-responsive last-column ">
                        {assign 'origin' $product->get('Origin')}
                        {assign 'weight' $product->get('Unit Weight')}
                        {assign 'dimensions' $product->get('Unit Dimensions')}
                        {assign 'materials' $product->get('Materials')}
                        {assign 'barcode' $product->get('Barcode Number')}
                        {assign 'cpnp' $product->get('CPNP Number')}


                        <div >

                            <div class="activity-item {if $origin==''}hide{/if}">
                                <div class=" one-half-responsive ">

                                    <strong>{if empty($labels._product_origin)}{t}Origin{/t}{else}{$labels._product_origin}{/if}</strong>
                                </div>
                                <div class="one-half-responsive last-column ">

                                    <span style="float:right" class="origin">{$origin}</span>
                                </div>
                            </div>

                            <div class="activity-item {if $weight=='' or $weight=='0Kg'}hide{/if}">
                                <div class=" one-half-responsive ">
                                    <i class="ion-record color-green-dark"></i>
                                    <strong>{if empty($labels._product_weight)}{t}Weight{/t}{else}{$labels._product_weight}{/if}</strong>
                                </div>
                                <div class="one-half-responsive last-column"  >

                                    <span style="float:right" class="origin">{$weight}</span>
                                </div>
                            </div>

                            <div class="activity-item {if $dimensions==''}hide{/if}">
                                <div class=" one-half-responsive ">
                                    <i class="ion-record color-green-dark"></i>
                                    <strong>{if empty($labels._product_dimensions)}{t}Dimensions{/t}{else}{$labels._product_dimensions}{/if}</strong>
                                </div>
                                <div class="one-half-responsive last-column"  >

                                    <span style="float:right" class="origin">{$dimensions}</span>
                                </div>
                            </div>


                            <div class="activity-item {if $barcode==''}hide{/if}">
                                <div class=" one-half-responsive ">
                                    <i class="ion-record color-green-dark"></i>
                                    <strong>{if empty($labels._product_barcode)}{t}Barcode{/t}{else}{$labels._product_barcode}{/if}</strong>
                                </div>
                                <div class="one-half-responsive last-column"  >

                                    <span style="float:right" class="origin">{$barcode}</span>
                                </div>
                            </div>

                            <div class="activity-item {if $cpnp==''}hide{/if}">
                                <div class=" one-half-responsive ">
                                    <i class="ion-record color-green-dark"></i>
                                    <strong>CPNP</strong>
                                </div>
                                <div class="one-half-responsive last-column"  >

                                    <span style="float:right" class="origin">{$cpnp}</span>
                                </div>
                            </div>


                            <div class="activity-item {if $materials==''}hide{/if}" style="border-bottom: none">
                                <div class=" one-half-responsive ">
                                    <i class="ion-record color-green-dark"></i>
                                    <strong>{if empty($labels._product_materials)}{t}Materials{/t}/{t}Ingredients{/t}{else}{$labels._product_materials}{/if}</strong>
                                </div>
                                <div class="one-half-responsive last-column"  >

                                    <div style="float:right;line-height: 150%;text-align: right">{$materials}</div>
                                    <div class="clear"></div>
                                </div>
                            </div>


                        </div>

                            <div class="clear"></div>
                        </div>


                    </div>
                </div>
                <div class="clear"></div>




            </div>
</div>


