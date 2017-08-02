{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 29 December 2016 at 16:22:33 CET, Mijas Costa, Spain
 Copyright (c) 2016, Inikoo

 Version 3
-->
*}

<div style="padding:20px;border-bottom:1px solid #ccc">

    <span style="margin-right:20px" class="hide">
        <i class="fa fa-desktop padding_right_5 " aria-hidden="true"></i>
        <i class="fa fa-tablet padding_right_5" aria-hidden="true"></i>
        <i class="fa fa-mobile" aria-hidden="true"></i>
    </span>

    <span class="button " onclick="toggle_logged_in_view(this)"><i class="fa fa-toggle-on " aria-hidden="true" alt="{t}On{/t}"></i> <span class="unselectable">{t}Logged in{/t}</span></span>



    {if $website->get('Website Status')=='Active'}

        <a id="link_to_live_webpage" target="_blank"  class="{if $webpage->get('Webpage State')=='Offline'}invisible{/if}"  href="http://{$webpage->get('Page URL')}" ><i class="fa fa-external-link" aria-hidden="true"  style="float:right;margin-left:20px;position:relative;top:2px"></i>   </a>


        <span id="publish" webpage_key="{$webpage->id}" class="button save {if $webpage->get('Publish')}changed valid{/if}" style="float:right" onclick="publish(this,'publish_webpage')"><span class="unselectable">{t}Publish{/t}</span> <i class="fa fa-rocket" aria-hidden="true"></i></span>

    {elseif $website->get('Website Status')=='InProcess'}

        <span id="set_as_ready_webpage_field" style="margin:10px 0px;padding:10px;border:1px solid #ccc;float:right;position: relative;top:-22px" webpage_key="{$webpage->id}" onClick="publish(this,'set_webpage_as_ready')" class=" button   {if $webpage->get('Webpage State')=='Ready'}hide{/if} ">{t}Set as Ready{/t} <i class="fa fa-check-circle padding_left_5  button  "></i></span>
        <span id="set_as_not_ready_webpage_field" style="margin:10px 0px;padding:10px;border:1px solid #ccc;float:right;position: relative;top:-22px" webpage_key="{$webpage->id}" onClick="publish(this,'set_webpage_as_not_ready')" class=" button super_discreet {if $webpage->get('Webpage State')=='InProcess'}hide{/if} ">{t}Set as not Ready{/t} <i class="fa fa-child padding_left_5 hide button"></i></span>

    {/if}





</div>


{assign 'see_also'  $public_product->webpage->get_see_also() }
{assign 'css'  $public_product->webpage->get('CSS') }



{include file="category.webpage.preview.style.tpl" }

<span id="ordering_settings" class="hide" data-labels='{ "ordered":"<i class=\"fa fa-thumbs-o-up fa-flip-horizontal \" aria-hidden=\"true\"></i> {t}Ordered{/t}", "order":"<i class=\"fa fa-hand-pointer-o\" aria-hidden=\"true\"></i>  {t}Order now{/t}", "update":"<i class=\"fa fa-hand-pointer-o\" aria-hidden=\"true\"></i>  {t}Update{/t}"  }'></span>


<div id="page_content" style="position:relative">



    <div id="product_bd" style="padding:5px 20px 0px 20px;clear:both;">

            <div class="product" style="display: flex; ">
                <div style="float:left" >




                    <div class="fotorama"
                         data-auto="false"
                         data-nav="thumbs"
                         data-width="450"
                    >
                        {foreach from=$public_product->get_images_slidesshow() item=image name=foo}
                            <a href="/{$image.normal_url}"><img src="/{$image.small_url}"></a>
                        {/foreach}


                    </div>



                </div>
                <div class="information" style="float:left;margin-left:40px;">
                    <h1 style="padding-top:5px;margin:2px 0;font-size:150%">
                        {$public_product->get('Name')}  <i class="fa fa-heart-o" style="margin-left:20px" aria-hidden="true"></i>
                    </h1>
                    <div class="highlight_box">
                        <div style="float:left;margin-right:4px;min-width:200px">
                            {t}Product code{/t}: <span class="code">{$public_product->get('Code')} </span>
                        </div>

                    </div>


                    <div class="ordering-container  log_in" style="display: flex;margin-top:40px;" >

                    <div class="product_prices log_in " style="margin-left:0px;padding-left:0px;font-size: 120%;width:250px" >
                        <div class="product_price">{t}Price{/t}: {$public_product->get('Price')}</div>
                        {assign 'rrp' $public_product->get('RRP')}
                        {if $rrp!=''}<div style="margin-top:4px">{t}RRP{/t}: {$rrp}</div>{/if}
                    </div>

                    <div style="margin-left:10px;">
                        <div class="ordering log_in " >
                            <input maxlength=6  class='order_input ' id='but_qty{$public_product->id}'   type="text" size='2'  value='{$public_product->get('Ordered Quantity')}' ovalue='{$public_product->get('Ordered Quantity')}'>
                            <span class="product_footer order_button"   ><i class="fa fa-hand-pointer-o" aria-hidden="true"></i> {t}Order now{/t}</span>



                        </div>
                    </div>

                    </div>


                    <div class="product_prices log_out hide" >
                        <div >{if empty($labels._login_to_see)}{t}For prices, please login or register{/t}{else}{$labels._login_to_see}{/if}</div>
                    </div>



                    <div id="text_edit_toolbar" class="edit_toolbar hide" section="description_block" style=" z-index: 200;position:relative;">
                        <i class="fa fa-window-close fa-fw button" style="margin-bottom:10px" aria-hidden="true"></i><br>


                        <i class="fa  fa-trash error fa-fw button hide  " style="margin-top:20px" aria-hidden="true"></i><br>
                    </div>


                    <div id="product_description" class="product_description_block fr-view {$content_data.description_block.class}">
                       {$content_data.description_block.content}
                    </div>


                </div>

                <div style="clear: both;height: 10px"></div>



            </div>
    </div>

    <section class="product_tabs" style="margin-top:20px">

        <input id="tab-properties" type="radio" name="grp" class="{if !$has_properties_tab}hide{/if}" {if $has_properties_tab}checked="checked"{/if} />
        <label for="tab-properties">{t}Properties{/t}</label>
        <div>



            <table class="properties">
                <tr class="{if $Origin==''}hide{/if}"> <td >{t}Origin{/t}</td> <td>{$Origin}</td></tr>

                <tr class="{if $Weight==''}hide{/if}"> <td>{$public_product->get_field_label('Product Unit Weight')|ucfirst}</td> <td>{$Weight}</td></tr>
                <tr class="{if $Dimensions==''}hide{/if}">  <td>{$public_product->get_field_label('Product Unit Dimensions')|ucfirst}</td> <td>{$Dimensions}</td></tr>
                <tr class="{if $Materials==''}hide{/if}"> <td>{t}Materials{/t}/{t}Ingredients{/t}</td> <td> <section style="width:70%"> {$Materials}</section></td></tr>
                <tr class="{if $CPNP==''}hide{/if}"> <td title="{t}Cosmetic Products Notification Portal{/t} - Europa.eu">CPNP</td> <td>{$CPNP}</td></tr>

                {foreach from=$product_attachments item=attachment}
                    <tr>
                        <td>{$attachment.label} <i class="fa fa-paperclip" style="margin-left:5px" aria-hidden="true"></i></td>
                        <td  >   <span class="link">{$attachment.name}</span></td>
                    </tr>
                {/foreach}
            </table>


        </div>


        <input id="tab-new" type="radio" name="grp" />
        <label  class="hide" for="tab-new">{t}New tab{/t}</label>
        <div>
            bla bla bla
        </div>



    </section>




    <div style="clear:both"></div>

</div>



<script>




    {include file="js/webpage.preview.publish.tpl.js" }
    {include file="js/webpage.preview.product_description.tpl.js" }
    {include file="js/webpage.preview.tabs.tpl.js" }





    function toggle_logged_in_view(element){

        var icon=$(element).find('i')
        if(icon.hasClass('fa-toggle-on')){
            icon.removeClass('fa-toggle-on').addClass('fa-toggle-off')

            $('.product_prices.log_in').addClass('hide')
            $('.product_prices.log_out').removeClass('hide')

            $('.ordering.log_in').addClass('hide')
            $('.ordering.log_out').removeClass('hide')


        }else{
            icon.addClass('fa-toggle-on').removeClass('fa-toggle-off')

            $('.product_prices.log_in').removeClass('hide')
            $('.product_prices.log_out').addClass('hide')
            $('.ordering.log_in').removeClass('hide')
            $('.ordering.log_out').addClass('hide')

        }



    }


    $(function () {
        $('.fotorama').fotorama();
    });


</script>