<div id="section_{$section_data.key}_container" style="position:relative;margin-bottom:10px;;margin-top:0px" class="section" section_key="{$section_data.key}">

    <div item_key=0 class="remove_drop_zone button overview_item_droppable invisible" style="position:absolute;top:50px;left:-37px;height:60px;width: 30px;margin-right:5px;border:1px dashed #ccc;text-align:center; " ondrop="overview_delete_items_drop(event)"  ondragover="overview_items_allowDrop(event)"  >
        <i class="fa fa-trash" style="position:relative;top:20px" aria-hidden="true"></i>
    </div>

        <i class="invisible fa fa-hand-grab fa-fw {if $section_data.type=='anchor'}invisible{/if}"  style="position:relative;top:24px;left:-25.9px"  aria-hidden="true"
        draggable="true" ondragend="move_section_ondragend(event)" ondragstart="move_section_ondragstart(event)"   ondragover="move_section_allowDrop(event)"  ondrop="move_section_items_drop(event)"
        ></i>
    <i class="fa fa-trash fa-fw {if $section_data.type=='anchor'}invisible{/if} button" onClick="delete_section(this)" style="position:relative;top:50px;left:-47.5px" aria-hidden="true"></i>

    
    
    <div id="section_{$section_data.key}" style="position:relative" class="page_break  panel_4" >
        <span  ondrop="return false;" class="section_header title items_view {$section_data.type}" {if $section_data.type!='anchor'} contenteditable="true"{/if} field="title">{if $section_data.type=='anchor'}
        <i class="fa fa-th fa-fw button  box_view" style="margin-left:0px" aria-hidden="true"></i>
     
       <i class="fa fa-bars fa-fw button super_discreet overview_view" style="position:absolute;top:6.5px"  aria-hidden="true"></i>
       
       
       <i id="add_panel" class=" fa fa-cog button" aria-hidden="true" style=";font-size:80%;margin-left:40px;position:absolute;top:8px"></i>
       
        {else}{$section_data.title}{/if}</span>
        <span class="section_header sub_title items_view {$section_data.type}" {if $section_data.type!='anchor'} contenteditable="true"{/if} field="subtitle">{if $section_data.type=='anchor'}<i class="fa fa-plus button add_section" aria-hidden="true"></i>{else}{$section_data.subtitle}{/if}</span>
    </div>


    <div style="display:flex;flex-flow: row wrap;clear:both;" id="section_items_{$section_data.key}">
        {include file="webpage.preview.categories_showcase.section.items.tpl" categories=$section_data.items   }
    </div>
</div>

