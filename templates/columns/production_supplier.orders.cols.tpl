{*/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created:  15-07-2019 16:41:38 MYT Kuala Lumpur, Malaysia
 Copyright (c) 2019, Inikoo

 Version 3
*/*}


var columns= [{
name: "id",
label: "",
editable: false,
cell: "integer",
renderable: false


}, {
name: "public_id",
label: "{t}Number{/t}",
editable: false,
sortType: "toggle",
cell: Backgrid.HtmlCell.extend({
})
},
{
name: "worker",
label: "{t}Worker{/t}",
editable: false,
sortType: "toggle",
cell: "html"
},

 {
name: "products",
label: "{t}Products{/t}",
editable: false,
sortable: false,
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell
},
{
name: "weight",
label: "{t}Weight{/t} (Kg)",
editable: false,
sortable: false,
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell
},
{
name: "total_amount",
label: "{t}Value{/t}",
editable: false,
sortable: false,
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell
},
{
name: "state",
label: "{t}State{/t}",
editable: false,
sortType: "toggle",
cell: "html"
},
{
name: "date",
label: "{t}Date{/t}",
editable: false,
defaultOrder:1,
sortType: "toggle",
{if $sort_key=='date'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell
},
{
name: "notes",
label: "",
editable: false,
sortable: false,
cell: Backgrid.HtmlCell.extend({ className: ""} ),
}
]

function change_table_view(view,save_state){

}
