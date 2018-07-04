{*/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 3 July 2018 at 11:15:35 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3
*/*}

var columns = [
{
name: "id",
label: "",
editable: false,
renderable: false,
cell: "string",

},

{
name: "customer",
label: "{t}Customer{/t}",
editable: false,
sortType: "toggle",
{if $sort_key=='customer'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}

cell: Backgrid.HtmlCell.extend({

})
},
{
name: "last_order",
label: "{t}Last order{/t}",
editable: false,
sortType: "toggle",
{if $sort_key=='last_order'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}

cell: Backgrid.HtmlCell.extend({ })


}


]

function change_table_view(view,save_state){


}