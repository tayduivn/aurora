var columns = [
{
name: "id",
label: "",
editable: false,
cell: "integer",
renderable: false


},
{
name: "reference",
label: "{t}S. Code{/t}",
editable: false,
cell: Backgrid.HtmlCell.extend({
})
},
{
name: "image",
label: "{t}Image{/t}",
editable: false,
sortable: false,

cell: "html"

},
{
name: "description",
label: "{t}Carton description{/t}",
editable: false,
cell: "html"

},

{
name: "description_sales",
label: "{t}Carton description{/t}",
editable: false,
cell: "html"

},
{
name: "info",
label: "{t}Info{/t}",
editable: false,
cell: "html"

},
{
name: "unit",
label: "{t}Unit description{/t}",
editable: false,
cell: "html"
},

{
name: "unit_per_carton",
label: "U/C",
editable: false,
cell: "html"
},
{
name: "unit_cost",
label: "{t}Unit cost{/t}",
editable: false,
cell: "html"
},
{
name: "subtotals",
label: "{t}Subtotals{/t}",
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='subtotals'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: ""} ),

},
{
name: "quantity",
label: "{t}Quantity{/t}",
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='quantity'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell
}
]


function change_table_view(view, save_state) {


grid.columns.findWhere({ name: 'description'} ).set("renderable", false)
grid.columns.findWhere({ name: 'description_sales'} ).set("renderable", false)
grid.columns.findWhere({ name: 'unit'} ).set("renderable", false)
grid.columns.findWhere({ name: 'unit_per_carton'} ).set("renderable", false)
grid.columns.findWhere({ name: 'unit_cost'} ).set("renderable", false)




if(view=='overview'){
grid.columns.findWhere({ name: 'description_sales'} ).set("renderable", true)

}else if(view=='sales'){
grid.columns.findWhere({ name: 'description_sales'} ).set("renderable", true)

}else if(view=='unit'){
grid.columns.findWhere({ name: 'unit'} ).set("renderable", true)
grid.columns.findWhere({ name: 'unit_per_carton'} ).set("renderable", true)
grid.columns.findWhere({ name: 'unit_cost'} ).set("renderable", true)

}




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
