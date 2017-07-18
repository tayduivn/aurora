{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 15 July 2017 at 18:09:10 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2017, Inikoo

 Version 3
-->
*}

<div id="edit_mode_{$key}" class=" edit_mode " type="{$block.type}" key="{$key}" style="height: 22px;line-height: 22px">
    <div style="float:left;margin-right:20px;min-width: 200px;">
        <div style="float:left;min-width: 200px;position: relative;top:2px">
            <i class="fa fa-fw {$block.icon}" style="margin-left:10px" aria-hidden="true" title="{$block.label}"></i>
            <span class="label">{$block.label}</span>
        </div>


        <div id="button_link_edit_block_{$key}" name="button_link_edit_block" class="hide edit_block" style="position:absolute;padding:10px;background-color: #FFF;border:1px solid #ccc;z-index: 4000">
            <input value="{$block.link}" style="width: 450px"> <i class="apply_changes  fa button fa-check-square" style="margin-left: 10px" aria-hidden="true"></i>
        </div>

        <span id="button_link_{$key}" key="{$key}" class="button_link button" style="margin-left:10px">
    <i class="fa fa-link   {if $block.link=='' }very_discreet{/if} " aria-hidden="true"></i>
    <span class="button  {if $block.link=='' }hide{/if} " style="border:1px solid #ccc;padding:2px 4px;">{$block.link|truncate:30}</span>
</span>

    </div>
    <div style="clear: both"></div>
</div>