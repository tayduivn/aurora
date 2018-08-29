﻿{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 29 August 2018 at 13:58:04 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3
-->
*}


<table class="order_items" >
    <thead>
    <tr>
        <th class="text-left">{t}Code{/t}</th>
        <th class="text-left">{t}Description{/t}</th>
        <th class="text-right">{t}Quantity{/t}</th>
        <th class="text-right">{t}Amount net{/t}</th>
    </tr>
    </thead>
    <tbody>

    {foreach from=$order->get_items() item="item" }

    <tr>
        <td>{$item.code}</td>
        <td>{$item.description}</td>
        {if $edit}
        <td class="text-right">{$item.edit_qty}</td>
        {else}
            <td class="text-right">{$item.qty}</td>

        {/if}
        <td class="text-right">{$item.amount}</td>
    </tr>


    {/foreach}
    </tbody>
</table>
