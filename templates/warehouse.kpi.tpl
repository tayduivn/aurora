{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 5 February 2017 at 21:13:29 GMT+8, Cyberjaya, Malaysia
 Copyright (c) 2017, Inikoo

 Version 3
-->
*}

{assign "kpi" $warehouse->get_kpi('Month To Day')}
<div style="border-bottom:1px solid #ccc;padding:20px">
<div style="border:1px solid #eee;padding:10px;font-size:150%;width:200px" title="{$kpi.formatted_amount} /{$kpi.formatted_hrs} ">{$kpi.formatted_kpi}</div>
</div>