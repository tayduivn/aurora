var columns = [
{
name: "id",
label: "",
editable: false,
renderable: false,
cell: "string",

},
{
name: "type",
label: "",
editable: false,
sortable: false,
cell: Backgrid.HtmlCell.extend({
className: "width_30 align_center"
})

},
{
name: "code",
label: "{t}Code{/t}",
editable: false,
sortType: "toggle",

cell: Backgrid.HtmlCell.extend({
orderSeparator: '',
events: {
"click": function() {
}
},
className: ""

})

},{
name: "name",
label: "{t}Name{/t}",
editable: false,
sortType: "toggle",

cell: Backgrid.HtmlCell.extend({
orderSeparator: '',
events: {

},

})

}
, {
name: "locations",
label: "{t}Locations{/t}",
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='locations'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}


cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell

}
, {
name: "parts",
label: "{t}Parts{/t}",
defaultOrder:1,
editable: false,
sortType: "toggle",
{if $sort_key=='parts'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}


cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell

}

]

function change_table_view(view,save_state){}