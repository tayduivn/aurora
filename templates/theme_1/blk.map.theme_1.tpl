{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 13 July 2017 at 03:36:52 GMT+8, Cyberjaya, Malaysia
 Copyright (c) 2017, Inikoo

 Version 3
-->
*}



<div id="block_{$key}" block="{$data.type}" class="{$data.type} _block {if !$data.show}hide{/if} " style="Width:100%;" >

    <div class="one_full">
        <iframe     class="google-map2"  _src="{$data.src}" src="{if $data.src=='#map'}{$store->get('Store Google Map URL')}{else}{$data.src}{/if}" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" allowfullscreen></iframe>

    </div>

    <div class="clearfix marb6"></div>

</div>
