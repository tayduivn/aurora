var columns = [
 {
    name: "id",
    label: "",
    editable: false,
    renderable: false,
    cell: "string"
}, {
    name: "code",
    label: "{t}Code{/t}",
    editable: false,
     cell: Backgrid.Cell.extend({
        orderSeparator: '',
        events: {
            "click": function() {
                change_view( '{$data['object']}/{$data['key']}/page/' + this.model.get("id"))
   
            }
        },
        className: "link",
        
         render: function () {
      this.constructor.__super__.render.apply(this, arguments);
      
      
        this.$el.empty();
        var rawValue = this.model.get(this.column.get("name"));
        var formattedValue = this.formatter.fromRaw(rawValue, this.model);
        this.$el.append(formattedValue);
        this.delegateEvents();
       
      
      
        if(this.model.get('id')==''){
            this.$el.removeClass('link');
        }
      return this;
    }
        
        
    })
}, {
    name: "type",
    label:"{t}Type{/t}",
    editable: false,
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='items'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: 'html'
}, {
    name: "title",
    label:"{t}Title{/t}",
    editable: false,
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='title'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: "string"
}, {
    name: "state",
    label:"{t}State{/t}",
    editable: false,
    defautOrder:1,
    sortType: "state",
    {if $sort_key=='title'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: "html"
}  , {
    name: "url",
    label:"URL",
    editable: false,
    defautOrder:1,
     renderable: false,
    sortType: "toggle",
    {if $sort_key=='url'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: "uri"
}, {
    name: "users",
    label:"{t}Users{/t}",
        editable: false,
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='users'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.StringCell.extend({ className: "aright"} ),
    headerCell: integerHeaderCell

}, {
    name: "pages",
    label:"{t}Pages{/t}",
       editable: false,
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='pages'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.StringCell.extend({ className: "aright"} ),
    headerCell: integerHeaderCell

}

]
function change_table_view(view,save_state){}
