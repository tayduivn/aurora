var columns = [
{
name: "id",
label: "",
editable: false,
renderable: false,
cell: "string"
},
{
name: "code",
label: "{t}Code{/t}",
editable: false,
cell: Backgrid.HtmlCell.extend({
orderSeparator: '',
events: {
"click": function() {
change_view('suppliers/category/' + this.model.get("id") )
}
},
className: "link",

render: function () {
this.constructor.__super__.render.apply(this, arguments);
if(this.model.get('id')==''){
this.$el.removeClass('link');
}
return this;
}


})
}, {
name: "label",
label:"{t}Label{/t}",
editable: false,
cell: "string"
}, {
name: "level",
label:"{t}Level{/t}",
editable: false,
cell: "string"
}, {
name: "subcategories",
label:"{t}Subcategories{/t}",
editable: false,
defaultOrder:1,
sortType: "toggle",
{if $sort_key=='subcategories'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}

cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell

}, {
name: "subjects",
label:"{t}Suppliers{/t}",
editable: false,
defaultOrder:1,
sortType: "toggle",
{if $sort_key=='subjects'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}

cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell

}, {
name: "percentage_assigned",
label:"{t}Assigned{/t}",
editable: false,
defaultOrder:1,
sortType: "toggle",
{if $sort_key=='assigned'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}

cell: Backgrid.HtmlCell.extend({ className: "aright"} ),
headerCell: integerHeaderCell

}


]
function change_table_view(view,save_state){}
