{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 11 April 2018 at 01:38:44 GMT+8, Kaula Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3
-->
*}






<div id="edit_mode_{$key}" class=" edit_mode " type="{$block.type}" key="{$key}" style="height: 22px;line-height: 22px">
    <div id="edit_mode_main_{$key}" class="main" style="float:left;margin-right:20px;min-width: 200px;">


        <i class="toggle_view_items fa-fw fal fa-cogs   button hide" title="{t}Backstage view{/t}" title_alt="{t}Display view{/t}" style="position: relative;left:-12px;bottom:1.05px"></i>


        <span style="margin-left:50px">{t}Margin{/t}:</span>
        <input data-margin="top" class="edit_block_margin edit_block_input top" value="{if isset($block.top_margin)}{$block.top_margin}{else}0{/if}" placeholder="0"><input data-margin="bottom" class="edit_block_margin edit_block_input bottom"
                                                                                                                                                      value="{if isset($block.bottom_margin)}{$block.bottom_margin}{else}0{/if}"
                                                                                                                                                      placeholder="0">


        {*
        <span onclick="toggle_product_properties(this)" class="toggle_product_properties padding_left_20 unselectable button"><i class="fa {if $block.show_properties}fa-toggle-on{else}fa-toggle-off{/if}"></i> {t}Properties{/t}</span>
*}


    </div>


    <div style="clear: both"></div>
</div>

{*
for scripts look webpage_preview.tpl
*}
<script>




</script>