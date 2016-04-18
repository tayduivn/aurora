var columns = [
  {
    name: "id",
    label: "",
    editable: false,
     renderable: false,
    cell: "string",

},{
    name: "code",
    label: "{t}Code{/t}",
    editable: false,
     sortType: "toggle",
    {if $sort_key=='id'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    
    cell: Backgrid.StringCell.extend({
        orderSeparator: '',
        events: {
            "click": function() {
                change_view('supplier{if $data.object=='list'}/list/{$data.key}{/if}/' + this.model.get("id"))
            }
        },
        className: "link"
       
})
   
}, {
    name: "name",
    label: "Name",
     sortType: "toggle",
    cell: Backgrid.StringCell.extend({
        events: {
            "dblclick": "enterEditMode"
        }
    })
}, {
    name: "location",
    label: "{t}Location{/t}",
    sortType: "toggle",
            {if $sort_key=='location'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    
    editable: false,
    
    cell: "html"
}, {
    name: "last_purchase_order",
    label: "{t}Last PO{/t}",
     defautOrder:1,
    editable: false,
    sortType: "toggle",
    {if $sort_key=='last_invoice'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}


    cell: Backgrid.StringCell.extend({ className: "aright"} ),
        headerCell: integerHeaderCell

},
 {
    name: "supplier_parts",
    label: "{t}Parts{/t}",
    editable: false,
   
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='supplier_parts'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.StringCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},
 
 {
    name: "surplus",
    label: "{t}Surplus{/t}",
    editable: false,
   
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='surplus'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},
 {
    name: "optimal",
    label: "{t}Optimal{/t}",
    editable: false,
   
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='optimal'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},
 {
    name: "low",
    label: "{t}Low{/t}",
    editable: false,
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='low'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
    headerCell: integerHeaderCell
},
{
    name: "critical",
    label: "{t}Critical{/t}",
    editable: false,
   
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='critical'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},
 {
    name: "out_of_stock",
    label: "{t}Out of Stock{/t}",
    editable: false,
   
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='out_of_stock'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.HtmlCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},
 {
    name: "pending_po",
    label: "{t}Pending PO{/t}",
    editable: false,
   
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='pending_po'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.StringCell.extend({ className: "aright"} ),

    headerCell: integerHeaderCell
},
 {
    name: "company",
    label: "{t}Company{/t}",
    editable: true,
    sortType: "toggle",
    cell: Backgrid.StringCell.extend({
        events: {
            "dblclick": "enterEditMode"
        }
    })
}, {
    name: "contact",
    label: "{t}Main contact{/t}",
    editable: true,
    sortType: "toggle",
    cell: Backgrid.StringCell.extend({
        events: {
            "dblclick": "enterEditMode"
        }
    })
}, {
    name: "email",
    label: "{t}Email{/t}",
    editable: true,
    sortType: "toggle",
    cell: Backgrid.EmailCell.extend({
        events: {
            "dblclick": "enterEditMode"
        }
    })
},{
    name: "telephone",
    label: "{t}Telephone{/t}",
    editable: true,
    sortType: "toggle",
    cell: Backgrid.StringCell.extend({
        events: {
            "dblclick": "enterEditMode"
        }
    })
}

]

function change_table_view(view,save_state){

    $('.view').removeClass('selected');
    $('#view_'+view).addClass('selected');
    
//    grid.columns.findWhere({ name: 'formatted_id'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'name'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'location'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'last_purchase_order'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'supplier_parts'} ).set("renderable", false)

    grid.columns.findWhere({ name: 'surplus'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'optimal'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'low'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'critical'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'out_of_stock'} ).set("renderable", false)

    grid.columns.findWhere({ name: 'pending_po'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'company'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'contact'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'email'} ).set("renderable", false)
    grid.columns.findWhere({ name: 'telephone'} ).set("renderable", false)
 
    
    if(view=='overview'){
        grid.columns.findWhere({ name: 'name'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'location'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'last_purchase_order'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'supplier_parts'} ).set("renderable", true)
       
    }else if(view=='weblog'){
        grid.columns.findWhere({ name: 'logins'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'failed_logins'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'requests'} ).set("renderable", true)
    }else if(view=='contact'){
        grid.columns.findWhere({ name: 'company'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'contact'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'email'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'telephone'} ).set("renderable", true)
    }else if(view=='products'){
        grid.columns.findWhere({ name: 'supplier_parts'} ).set("renderable", true)
         grid.columns.findWhere({ name: 'surplus'} ).set("renderable", true)
    grid.columns.findWhere({ name: 'optimal'} ).set("renderable", true)
    grid.columns.findWhere({ name: 'low'} ).set("renderable", true)
    grid.columns.findWhere({ name: 'critical'} ).set("renderable", true)
    grid.columns.findWhere({ name: 'out_of_stock'} ).set("renderable", true)
  
    }else if(view=='orders'){
        grid.columns.findWhere({ name: 'last_purchase_order'} ).set("renderable", true)
        grid.columns.findWhere({ name: 'pending_po'} ).set("renderable", true)
 
    }
    
    if(save_state){
     var request = "/ar_state.php?tipo=set_table_view&tab={$tab}&table_view=" + view
   
    $.getJSON(request, function(data) {});
    }

}