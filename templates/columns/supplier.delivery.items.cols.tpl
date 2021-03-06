var columns = [
{
name: "id",
label: "",
editable: false,
cell: "integer",
renderable: false


},{
name: "product_pid",
label: "",
editable: false,
cell: "integer",
renderable: false


},{
name: "checkbox",
renderable:false,
label: "",
editable: false,
cell: Backgrid.HtmlCell.extend({ className: "width_20"} ),

},{
name: "operations",
renderable:false,
label: "",
editable: false,
cell: Backgrid.HtmlCell.extend({ className: "width_20"} ),

},
{
name: "reference",
label: "{t}S. Code{/t}",
renderable: {if $data['_object']->get('State Index')>=40}false{else}true{/if},

editable: false,
cell: Backgrid.HtmlCell.extend({
}),
},{
name: "part_reference",
label: "{t}Part{/t}",
renderable: {if $data['_object']->get('State Index')>=40}true{else}false{/if},

editable: false,
cell: Backgrid.HtmlCell.extend({

}),
},{
name: "description",
label: "{t}Unit description{/t}",
editable: false,
cell: "html"

},


{
name: "items_qty",
label: "{t}Qty{/t}",
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='items_qty'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: ""} ),

},
{
name: "weight",
label: "{t}Weight{/t}",
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='weight'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: ""} ),

},
{
name: "cbm",
label: "{t}CBM{/t}",
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='cbm'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: ""} ),

},
{
name: "amount",
label: "{t}Amount{/t}",
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='amount'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: ""} ),

},

 {
name: "quantity",
label: "{t}Cartons{/t}",
renderable: false,
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='quantity'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell
}, {
name: "qty",
label: "{t}Cartons{/t}",
renderable: false,
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='ordered'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell
},
{
name: "delivery_quantity",
label: "{t}Delivery{/t}",
renderable: false,
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='quantity'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell
}, {
name: "c_sko_u",
label: "{t}C/SKO/U{/t}",
renderable: {if $data['_object']->get('State Index')>=40}true{else}false{/if},
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='quantity'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell
}, {
name: "received_quantity",
label: "{t}Received SKO{/t}",
renderable: {if $data['_object']->get('State Index')>=40}true{else}false{/if},
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='quantity'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell
}, {
name: "placement",
label: "{t}Placements{/t}",
renderable: {if $data['_object']->get('State Index')>=40}true{else}false{/if},

editable: false,

cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell
}
]


function change_table_view(view, save_state) {

{if isset($data['metadata']['create_delivery']) and $data['metadata']['create_delivery'] }

    grid.columns.findWhere({
    name: 'checkbox'
    }).set("renderable", true)

    grid.columns.findWhere({
    name: 'operations'
    }).set("renderable", true)

    grid.columns.findWhere({
    name: 'delivery_quantity'
    }).set("renderable", true)
{/if}

}
