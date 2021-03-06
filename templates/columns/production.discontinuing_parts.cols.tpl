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
sortable:false,
cell: Backgrid.HtmlCell.extend({
className: "width_20"
})

},{
name: "reference",
label: "{t}Reference{/t}",
editable: false,
sortType: "toggle",

cell: Backgrid.HtmlCell.extend({
events: {
"click": function() {

}
},

})

},
{
name: "sko_description",
label: "{t}SKO description{/t}",
editable: false,
sortType: "toggle",

cell: Backgrid.HtmlCell.extend({


})

},
{
name: "stock_weight",
label: "{t}Stock weight{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='stock_weight'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "stock_value",
label: "{t}Stock value{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='stock_value'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},


{
name: "stock",
label: "{t}Stock{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='stock'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},

{
name: "available_forecast",
label: "{t}Available forecast{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='available_forecast'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},

{
name: "products",
label: "{t}Active products{/t}",
editable: false,
sortable: false,

cell: Backgrid.HtmlCell.extend({} ),

},
{
name: "dispatched",
label: "{t}Dispatched{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='sold'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "dispatched_1y",
label: "{t}1YB{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='sold'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "dispatched_year0",
label: new Date().getFullYear(),
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='dispatched_year0'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "dispatched_year1",
label: new Date().getFullYear()-1,
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='dispatched_year1'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "dispatched_year2",
label: new Date().getFullYear()-2,
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='dispatched_year2'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "dispatched_year3",
label: new Date().getFullYear()-3,
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='dispatched_year3'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "revenue",
label: "{t}Revenue{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='revenue'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "revenue_1y",
label: "{t}1YB{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='revenue_1y'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "revenue_year0",
label: new Date().getFullYear(),
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='revenue_year0'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "revenue_year1",
label: new Date().getFullYear()-1,
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='revenue_year1'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "revenue_year2",
label: new Date().getFullYear()-2,
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='revenue_year2'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "revenue_year3",
label: new Date().getFullYear()-3,
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='revenue_year3'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "lost",
label: "{t}Lost{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='lost'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "bought",
label: "{t}Bought{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='bought'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},

{
name: "dispatched_per_week",
label: "{t}Dispatched/w{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='stock'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
{
name: "weeks_available",
label: "{t}Weeks available{/t}",
editable: false,

defaultOrder:1,
sortType: "toggle",
{if $sort_key=='stock'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

headerCell: integerHeaderCell
},
]

function change_table_view(view,save_state){

$('.view').removeClass('selected');
$('#view_'+view).addClass('selected');


close_columns_period_options()
$('#columns_period').addClass('hide');

grid.columns.findWhere({ name: 'sko_description'} ).set("renderable", false)
grid.columns.findWhere({ name: 'stock'} ).set("renderable", false)
grid.columns.findWhere({ name: 'stock_value'} ).set("renderable", false)
grid.columns.findWhere({ name: 'stock_weight'} ).set("renderable", false)

grid.columns.findWhere({ name: 'dispatched'} ).set("renderable", false)
grid.columns.findWhere({ name: 'revenue'} ).set("renderable", false)
grid.columns.findWhere({ name: 'dispatched_1y'} ).set("renderable", false)
grid.columns.findWhere({ name: 'revenue_1y'} ).set("renderable", false)
grid.columns.findWhere({ name: 'lost'} ).set("renderable", false)
grid.columns.findWhere({ name: 'bought'} ).set("renderable", false)
grid.columns.findWhere({ name: 'dispatched_year0'} ).set("renderable", false)
grid.columns.findWhere({ name: 'dispatched_year1'} ).set("renderable", false)
grid.columns.findWhere({ name: 'dispatched_year2'} ).set("renderable", false)
grid.columns.findWhere({ name: 'dispatched_year3'} ).set("renderable", false)
grid.columns.findWhere({ name: 'revenue_year0'} ).set("renderable", false)
grid.columns.findWhere({ name: 'revenue_year1'} ).set("renderable", false)
grid.columns.findWhere({ name: 'revenue_year2'} ).set("renderable", false)
grid.columns.findWhere({ name: 'revenue_year3'} ).set("renderable", false)
grid.columns.findWhere({ name: 'dispatched_per_week'} ).set("renderable", false)
grid.columns.findWhere({ name: 'weeks_available'} ).set("renderable", false)
grid.columns.findWhere({ name: 'available_forecast'} ).set("renderable", false)




if(view=='overview'){
grid.columns.findWhere({ name: 'sko_description'} ).set("renderable", true)
grid.columns.findWhere({ name: 'stock'} ).set("renderable", true)
grid.columns.findWhere({ name: 'stock_value'} ).set("renderable", true)
grid.columns.findWhere({ name: 'available_forecast'} ).set("renderable", true)
grid.columns.findWhere({ name: 'stock_weight'} ).set("renderable", true)

$('#columns_period').removeClass('hide');


}else if(view=='dispatched'){
grid.columns.findWhere({ name: 'dispatched'} ).set("renderable", true)
grid.columns.findWhere({ name: 'dispatched_1y'} ).set("renderable", true)
grid.columns.findWhere({ name: 'dispatched_year0'} ).set("renderable", true)
grid.columns.findWhere({ name: 'dispatched_year1'} ).set("renderable", true)
grid.columns.findWhere({ name: 'dispatched_year2'} ).set("renderable", true)
grid.columns.findWhere({ name: 'dispatched_year3'} ).set("renderable", true)

$('#columns_period').removeClass('hide');


}else if(view=='revenue'){
grid.columns.findWhere({ name: 'revenue'} ).set("renderable", true)
grid.columns.findWhere({ name: 'revenue_1y'} ).set("renderable", true)
grid.columns.findWhere({ name: 'revenue_year0'} ).set("renderable", true)
grid.columns.findWhere({ name: 'revenue_year1'} ).set("renderable", true)
grid.columns.findWhere({ name: 'revenue_year2'} ).set("renderable", true)
grid.columns.findWhere({ name: 'revenue_year3'} ).set("renderable", true)
$('#columns_period').removeClass('hide');


}else if(view=='stock'){
grid.columns.findWhere({ name: 'stock'} ).set("renderable", true)
grid.columns.findWhere({ name: 'dispatched_per_week'} ).set("renderable", true)

grid.columns.findWhere({ name: 'weeks_available'} ).set("renderable", true)


}

if(save_state){
var request = "/ar_state.php?tipo=set_table_view&tab={$tab}&table_view=" + view

$.getJSON(request, function(data) {});
}

}