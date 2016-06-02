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
        events: {
            "click": function() {
                {if $data['parent']=='website'}
                change_view( 'website/{$data['website']->id}/node/' + this.model.get("id"))
                {else}
                 change_view( '{$data['object']}/{$data['key']}/node/' + this.model.get("id"))
               
                {/if}
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
    name: "name",
    label:"{t}Name{/t}",
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
    name: "requests",
    label:"{t}Views{/t}",
       editable: false,
    defautOrder:1,
    sortType: "toggle",
    {if $sort_key=='requests'}direction: '{if $sort_order==1}descending{else}ascending{/if}',{/if}
    cell: Backgrid.StringCell.extend({ className: "aright"} ),
    headerCell: integerHeaderCell

}

]
function change_table_view(view,save_state){}
