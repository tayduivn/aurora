var columns = [
{
name: "id",
label: "",
editable: false,
renderable: false,
cell: "string",

}, {
name: "stock_status",
label: "",
editable: false,
sortType: "toggle",
cell: Backgrid.HtmlCell.extend({
className: "width_20"
})

},{
name: "reference",
label: "{t}Part{/t}",
editable: false,
sortType: "toggle",

cell: Backgrid.HtmlCell.extend({ })

},
{
name: "location",
label: "{t}Picking location{/t}",
editable: false,
sortType: "toggle",

cell: Backgrid.HtmlCell.extend({ })

},

{
name: "to_pick",
label: "{t}Ordered{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='stock'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},

{
name: "quantity_in_picking",
label: "{t}Stock in picking{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='stock'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},

{
name: "total_stock",
label: "{t}Total stock{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='stock'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},

{
name: "_storing_locations",
label: "{t}Storing locations{/t}",
editable: false,
sortable: false,

sortType: "toggle",

cell: Backgrid.HtmlCell.extend({})

},
{
name: "next_deliveries",
label: "{t}Next deliveries{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='next_deliveries'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({

})}


]

function change_table_view(view,save_state){

}