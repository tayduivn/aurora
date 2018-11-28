{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created:  23 November 2018 at 14:16:42 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2017, Inikoo

 Version 3
-->
*}

var columns = [
{
name: "id",
label: "",
editable: false,
cell: "integer",
renderable: false


},{
name: "code",
label: "{t}Product code{/t}",
editable: false,
cell: Backgrid.HtmlCell.extend({
}),
},

{
name: "quantity_order",
label: "{t}Qty (products){/t}",
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='quantity_order'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ } ),
},
{
name: "reference",
label: "{t}Part reference{/t}",
editable: false,
cell: Backgrid.HtmlCell.extend({
}),
},

{
name: "description",
label: "{t}Part description{/t}",
editable: false,
cell: "html"

},
{
name: "quantity",
label: "{t}Quantity dispatched{/t}",
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='quantity'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell
}, {
name: "to_return",
label: "{t}Returned{/t}",
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='to_return'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright width_100"} ),
headerCell: integerHeaderCell
}
]


function change_table_view(view, save_state) {}
