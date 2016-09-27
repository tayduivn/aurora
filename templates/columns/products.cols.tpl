var columns = [{
    name: "id",
    label: "",
    editable: false,
    cell: "integer",
    renderable: false


}, {
    name: "store_key",
    label: "",
    editable: false,
    renderable: false,
    cell: "string"
}, {
    name: "store",
    label: "{t}Store{/t}",
     renderable: {if ($data['parent']=='account' or $data['parent']=='warehouse' ) }true{else}false{/if},
    editable: false,
      sortType: "toggle",
    cell: Backgrid.StringCell.extend({
        events: {
            "click": function() {
                change_view('products/'+this.model.get("store_key"))
            }
        },
        className: "link width_150",
    })
},

 {
    name: "associated",
    label: "",
    renderable: {if ($data['parent']=='category') }true{else}false{/if},
    editable: false,
    sortType: "toggle",
    cell: Backgrid.HtmlCell.extend({
        className: "width_20"
    })

}, 
{
    name: "status",
    label: "{t}Status{/t}",
    editable: false,
      sortType: "toggle",
     cell: Backgrid.HtmlCell.extend({} ),

},

 {
    name: "code",
    label: "{t}Code{/t}   ",
    editable: false,
      sortType: "toggle",
    cell: Backgrid.StringCell.extend({
        events: {
            "click": function() {
                change_view( {if $data['section']=='part'}'part/{$data['key']}/product/' + this.model.get("id"){else if $data['section']=='category'}'products/{$data['store']->id}/category/{$data['_object']->get('Category Position')}/product/' + this.model.get("id"){else}'products/{$data['parent_key']}/'+this.model.get("id"){/if})
            }
        },
        className: "link width_150",
    })
}, {
    name: "name",
    label: "{t}Name{/t}",
    editable: false,
      sortType: "toggle",
    cell: "string"
},
 {
    name: "price",
    label: "{t}Price{/t}",
    editable: false,
      sortType: "toggle",
     cell: Backgrid.StringCell.extend({ } ),

},
 {
    name: "margin",
    label: "{t}Margin{/t}",
    editable: false,
      sortType: "toggle",
     cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},
 {
    name: "web_state",
    label: "{t}Web state{/t}",
    editable: false,
      sortType: "toggle",
     cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},
{
    name: "sales",
    label: "{t}Sales{/t}",
   editable: false,
   
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='money_in'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},
{
    name: "sales_1yb",
    label: "1YB",
   editable: false,
   
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='sales_1y'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.HtmlCell.extend({ className: "aright "} ),
    headerCell: integerHeaderCell

},
{
    name: "sales_year0",
    label: new Date().getFullYear(),
   editable: false,
   
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='sales_year0'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},
{
    name: "sales_year1",
    label: new Date().getFullYear()-1,
   editable: false,
   
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='sales_year1'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},
{
    name: "sales_year2",
    label: new Date().getFullYear()-2,
   editable: false,
   
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='sales_year2'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},
{
    name: "sales_year3",
    label: new Date().getFullYear()-3,
   editable: false,
   
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='sales_year3'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},

]


function change_table_view(view, save_state) {



    $('.view').removeClass('selected');
    $('#view_'+view).addClass('selected');
    
    close_columns_period_options()
    $('#columns_period').addClass('hide');



    grid.columns.findWhere({ name: 'name'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'status'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'price'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'margin'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'web_state'} ).set("renderable", false)


    grid.columns.findWhere({ name: 'sales'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'sales_1yb'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'sales_year0'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'sales_year1'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'sales_year2'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'sales_year3'} ).set("renderable", false)   
    
    if(view=='overview'){
        grid.columns.findWhere({ name: 'name'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'price'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'status'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'margin'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'web_state'} ).set("renderable", true)
    }else if(view=='status'){
        grid.columns.findWhere({ name: 'status'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'active'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'suspended'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'discontinuing'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'discontinued'} ).set("renderable", true)
    }else if(view=='sales'){
        $('#columns_period').removeClass('hide');
        grid.columns.findWhere({ name: 'sales'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'sales_1yb'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'sales_year0'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'sales_year1'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'sales_year2'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'sales_year3'} ).set("renderable", true)
      
    }else if(view=='stock'){
        

    }
    
    if(save_state){
     var request = "/ar_state.php?tipo=set_table_view&tab={$tab}&table_view=" + view
   
    $.getJSON(request, function(data) {});
    }



}
