{include file='header.tpl'}
<div id="bd" >

<div class="search_box" style="margin-top:15px">
  <div class="general_options">
    {foreach from=$general_options_list item=options }
        {if $options.tipo=="url"}
            <span onclick="window.location.href='{$options.url}'" >{$options.label}</span>
        {else}
            <span  id="{$options.id}" state="{$options.state}">{$options.label}</span>
        {/if}
    {/foreach}
    </div>
</div>
<div style="clear:left;margin:0 0px">
    <h1>{t}Editing Warehouse{/t}: <span id="title_name">{$warehouse->get('Warehouse Name')}</span> (<span id="title_code">{$warehouse->get('Warehouse Code')}</span>)</h1>
</div>
<ul class="tabs" id="chooser_ul" style="clear:both">
    <li> <span class="item {if $edit=='description'}selected{/if}"  id="description">  <span> {t}Description{/t}</span></span></li>
    <li> <span class="item {if $edit=='areas'}selected{/if}"  id="areas">  <span> {t}Areas{/t}</span></span></li>
    <li> <span class="item {if $edit=='shelfs'}selected{/if}"  id="shelfs">  <span> {t}Shelfs{/t}</span></span></li>
    <li> <span class="item {if $edit=='locations'}selected{/if}"  id="locations">  <span> {t}Locations{/t}</span></span></li>
	<li> <span class="item {if $edit=='shelf_types'}selected{/if}"  id="shelf_types">  <span> {t}Shelf Types{/t}</span></span></li>
	<li> <span class="item {if $edit=='location_types'}selected{/if}"  id="location_types">  <span> {t}Location Types{/t}</span></span></li>
</ul>
<div class="tabbed_container" > 
   <div id="description_block" style="{if $edit!='description'}display:none{/if}" >
														   
     <div style="float:right">
	<span class="save" style="display:none" id="description_save" onclick="save('description')">{t}Save{/t}</span>
	<span id="description_reset"  style="display:none"   class="undo" onclick="reset('description')">{t}Cancel{/t}</span>
	</div>
	
      <table style="margin:0;" class="edit" border=0>
	<tr><td class="label">{t}Warehouse Code{/t}:</td><td>
	      <input  
		 id="code" 
		 onKeyUp="changed(this)" 
		 onMouseUp="changed(this)"  
		 onChange="changed(this)"  
		 name="code" 
		 changed=0 
		 type='text' 
		 class='text' 
		 style="width:15em" 
		 MAXLENGTH="16" 
		 value="{$warehouse->get('Warehouse Code')}" 
		 ovalue="{$warehouse->get('Warehouse Code')}"  
		 />
	    </td>
	  </tr>
	  <tr><td class="label">{t}Warehouse Name{/t}:</td><td>
	      <input   
		 id="name" 
		 onKeyUp="changed(this)"    
		 onMouseUp="changed(this)"  
		 onChange="changed(this)"  
		 name="name" 
		 changed=0 
		 type='text'  
		 MAXLENGTH="255" 
		 style="width:30em"  
		 class='text' 
		 value="{$warehouse->get('Warehouse Name')}"  
		     ovalue="{$warehouse->get('Warehouse Name')}"  
		 />
	    </td>
	  </tr>
	</table>
  </div> 
   <div id="areas_block" style="{if $edit!='areas'}display:none{/if}" >
    <div class="general_options" style="float:right">
      <span   style="margin-right:10px"  id="add_area_here" class="state_details" >Add Area</span>
       <span  style="margin-right:10px;display:none"  id="save_area" class="state_details">{t}Save{/t}</span>
      <span style="margin-right:10px;display:none" id="close_add_area" class="state_details">{t}Close Dialog{/t}</span>
      
    </div>
    
     <div id="new_warehouse_area_messages" style="float:left;padding:5px;border:1px solid #ddd;width:400px;margin-bottom:15px;display:none">
      <table class="edit" >
    	<tr><td class="label">{t}Warehouse{/t}:</td><td><span style="font-weight:800">{$warehouse->get('Warehouse Name')}</span><input type="hidden" id="warehouse_key" ovalue="{$warehouse->id}" value="{$warehouse->id}"></td></tr>
	    <tr><td class="label">{t}Area Code{/t}:</td><td><input  id="area_code" ovalue=""  type="text"/></td></tr>
	    <tr><td class="label">{t}Area Name{/t}:</td><td><input  id="area_name" ovalue=""  type="text"/></td></tr>
	    <tr><td class="label">{t}Area Description{/t}:</td><td><textarea ovalue="" id="area_description"></textarea></td></tr>
       </table>  
     
    </div>
     <div id="new_warehouse_area_block" style="font-size:80%;float:left;padding:10px 15px;border:1px solid #ddd;width:200px;margin-bottom:15px;margin-left:10px;display:none">Messages
     </div>
    
    
    
   
    
    
     <div id="the_table1" class="data_table" style="margin:0px 0px;clear:left;">
       <span class="clean_table_title">{t}Warehouse Areas{/t}</span>
       <div  class="clean_table_caption"  style="clear:both;">
	 <div style="float:left;"><div id="table_info1" class="clean_table_info"><span id="rtext1"></span> <span class="filter_msg"  id="filter_msg1"></span></div></div>
	 <div class="clean_table_filter" id="clean_table_filter1"><div class="clean_table_info"><span id="filter_name1">{$filter_name1}</span>: <input style="border-bottom:none" id='f_input1' value="{$filter_value1}" size=10/><div id='f_container1'></div></div></div>
	 <div class="clean_table_controls" style="" ><div><span  style="margin:0 5px" id="paginator1"></span></div></div>
       </div>
       <div  id="table1"   class="data_table_container dtable btable "> </div>
     </div>
   </div>
   <div id="locations_block" style="{if $edit!='locations'}display:none{/if}" >
 
 
 
 
 <div id="the_table0" class="data_table" style="margin:20px 0px;clear:both">
       <span class="clean_table_title">{t}Locations{/t}</span>
       <div  class="clean_table_caption"  style="clear:both;">
	 <div style="float:left;"><div id="table_info0" class="clean_table_info"><span id="rtext0"></span> <span class="filter_msg"  id="filter_msg0"></span></div></div>
	 <div class="clean_table_filter" id="clean_table_filter0"><div class="clean_table_info"><span style="" id="filter_name0">{$filter_name0}</span>: <input  id='f_input0' value="{$filter_value0}" size=10/><div  id='f_container0'></div></div></div>
	 <div class="clean_table_controls" style="" ><div><span  style="margin:0 5px" id="paginator0"></span></div></div>
       </div>
       <div  id="table0"   class="data_table_container dtable btable "> </div>
     </div>
   </div>
   
   <div id="shelfs_block" style="{if $edit!='shelfs'}display:none{/if}" >

<div class="general_options" style="float:right">
      <span   style="margin-right:10px"  id="add_shelf" class="state_details" >Add Shelf</span>
       <span  style="margin-right:10px;display:none"  id="save_shelf" class="state_details disabled">{t}Save{/t}</span>
      <span style="margin-right:10px;display:none" id="close_add_shelf" class="state_details">{t}Close Dialog{/t}</span>
      
    </div>
    
     <div id="new_warehouse_shelf_messages" style="float:left;padding:5px;border:1px solid #ddd;width:400px;margin-bottom:15px;xdisplay:none">
      <table class="edit" >
    	<tr><td class="label">{t}Warehouse{/t}:</td><td><span style="font-weight:800">{$warehouse->get('Warehouse Name')}</span><input type="hidden" id="shelf_warehouse_key" ovalue="{$warehouse->id}" value="{$warehouse->id}"></td></tr>
	
	
	 <tr class="first"><td style="width:11em" class="label">Warehouse Area:</td>
	  <td  style="text-align:left;width:19em">
	    <div  style="width:15em;position:relative;top:00px" >
	      <input style="text-align:left;width:18em" id="shelf_warehouse_area" value="" ovalue="" >
	      <div id="shelf_warehouse_area_Container" style="" ></div>
	    </div>
	  </td>
	  <td id="shelf_warehouse_area_msg" class="edit_td_alert"><input type="hidden" value="{$warehouse->get('Warehouse Key')}" id="shelf_warehouse_area_key"></td>
	</tr>
	
	 <tr class="first"><td style="width:11em" class="label">Shelf Code:</td>
	  <td  style="text-align:left;width:19em">
	    <div  style="width:15em;position:relative;top:00px" >
	      <input style="text-align:left;width:18em" id="shelf_code" value="" ovalue="" >
	      <div id="shelf_code_Container" style="" ></div>
	    </div>
	  </td>
	  <td id="shelf_code_msg" class="edit_td_alert"></td>
	</tr>
	
	<tr class="first"><td style="" class="label">{t}Shelf Type{/t}:</td>
	  <td  style="text-align:left">
	    <div  style="width:15em;position:relative;top:00px" >
	      <input style="text-align:left;width:18em" id="shelf_shelf_type" value="" ovalue="">
	      <div id="shelf_shelf_type_Container" style="" ></div>
	    </div>
	  </td>
	  	  <td id="shelf_shelf_type_msg" class="edit_td_alert"><input type="hidden" value="" id="shelf_shelf_type_key"></td>

	</tr>
	   
	    <tr id="tr_layout" style="display:none"><td class="label">{t}Layout{/t}:</td><td>{t}Columns{/t}:<input style="width:2em"  id="shelf_columns" ovalue=""  type="text"/> {t}Rows{/t}:<input style="width:2em" id="shelf_rows" ovalue=""  type="text"/></td></tr>

	   
       </table>  
    
    </div>
    
    
    <div id="new_warehouse_shelf_block" style="font-size:80%;float:left;padding:10px 15px;border:1px solid #ddd;width:200px;margin-bottom:15px;margin-left:10px;display:none">

     </div>
    <div class="shelf_locations"  id="shelf_locations_layout">
    </div>
    
    
     <div id="the_table2" class="data_table" style="margin:0px 0px;clear:left;">
       <span class="clean_table_title">{t}Warehouse Shelfs{/t}</span>
       <div  class="clean_table_caption"  style="clear:both;">
	 <div style="float:left;"><div id="table_info2" class="clean_table_info"><span id="rtext2"></span> <span class="filter_msg"  id="filter_msg2"></span></div></div>
	 <div class="clean_table_filter" id="clean_table_filter2"><div class="clean_table_info"><span id="filter_name2">{$filter_name2}</span>: <input style="border-bottom:none" id='f_input0' value="{$filter_value2}" size=10/><div id='f_container0'></div></div></div>
	 <div class="clean_table_controls" style="" ><div><span  style="margin:0 5px" id="paginator2"></span></div></div>
       </div>
       <div  id="table2"   class="data_table_container dtable btable "> </div>
     </div>


</div>
   <div id="shelf_types_block" style="{if $edit!='shelf_types'}display:none{/if}" >
    <div class="general_options" style="float:right">
      <span   style="margin-right:10px"  id="add_shelf_type" class="state_details" >Create Type</span>
       <span  style="margin-right:10px;display:none"  id="save_shelf_type" class="state_details">{t}Save{/t}</span>
      <span style="margin-right:10px;display:none" id="close_add_shelf_type" class="state_details">{t}Close Dialog{/t}</span>
      
    </div>
    
     <div id="new_warehouse_shelf_type_messages" style="float:left;padding:5px;border:1px solid #ddd;width:480px;margin-bottom:15px;display:none">
      <table class="edit" >
    	<tr><td class="label">{t}Warehouse{/t}:</td><td><span style="font-weight:800">{$warehouse->get('Warehouse Name')}</span><input type="hidden" id="warehouse_key" ovalue="{$warehouse->id}" value="{$warehouse->id}"></td></tr>
	    <tr><td class="label">{t}Shelf Type Name{/t}:</td><td><input  id="shelf_type_name" ovalue=""  type="text"/></td></tr>
	    <tr><td class="label">{t}Description{/t}:</td><td><textarea ovalue="" id="shelf_type_description"></textarea></td></tr>
	     <tr><td class="label">{t}Type{/t}:</td>
	       <td> 
		 <div class="options" style="margin:5px 0" id="shelf_type_type_container">
		   <input type="hidden" value="{$shelf_default_type}" ovalue="{$shelf_default_type}" id="shelf_type_type"  >
		  {foreach from=$shelf_types item=unit_tipo key=name} <span class="radio{if $unit_tipo.selected} selected{/if}"  id="radio_shelf_type_{$name}" radio_value="{$name}">{$unit_tipo.fname}</span> {/foreach}
		</div>
	     </td></tr>

	     <tr><td class="label">{t}Typical Layout{/t}:</td><td>{t}Columns{/t}:<input style="width:2em"  id="shelf_type_columns" ovalue=""  type="text"/> {t}Rows{/t}:<input style="width:2em" id="shelf_type_rows" ovalue=""  type="text"/></td></tr>

	<tr><td class="label">{t}Average Location{/t}</td><td></td></tr>
	<tr><td class="label">{t}Length{/t}:</td><td><input  id="shelf_type_length" ovalue=""  type="text"/></td></tr>
	<tr><td class="label">{t}Deep{/t}:</td><td><input  id="shelf_type_deep" ovalue=""  type="text"/></td></tr>
	<tr><td class="label">{t}Height{/t}:</td><td><input  id="shelf_type_height" ovalue=""  type="text"/></td></tr>
	<tr><td class="label">{t}Max Weight{/t}:</td><td><input  id="shelf_type_weight" ovalue=""  type="text"/></td></tr>
	<tr><td class="label">{t}Max Volume{/t}:</td><td><input  id="shelf_type_volume" ovalue=""  type="text"/></td></tr>
	

       </table>  
     
    </div>
     <div id="new_warehouse_shelf_type_block" style="font-size:80%;float:left;padding:10px 15px;border:1px solid #ddd;width:200px;margin-bottom:15px;margin-left:10px;display:none">Messages
     </div>
    
    
    
     <div id="the_table3" class="data_table" style="margin:0px 0px;clear:left;">
       <span class="clean_table_title">{t}Shelf Types{/t}</span>
       <div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999"></div>
       <table style="float:left;margin:0 0 5px 0px ;padding:0"  class="options" >
	 <tr><td  {if $shelf_type_view=='general'}class="selected"{/if} tipo="general" id="shelf_type_general_view" >{t}General{/t}</td>
	   <td {if $shelf_type_view=='dimensions'}class="selected"{/if} tipo="dimensions"  id="shelf_type_dimensions_view"  >{t}Dimensions{/t}</td>

      </tr>
    </table>
       <div  class="clean_table_caption"  style="clear:both;">
	 <div style="float:left;"><div id="table_info3" class="clean_table_info"><span id="rtext3"></span> <span class="filter_msg"  id="filter_msg3"></span></div></div>
	 <div class="clean_table_filter" id="clean_table_filter3"><div class="clean_table_info"><span id="filter_name3">{$filter_name3}</span>: <input style="border-bottom:none" id='f_input0' value="{$filter_value3}" size=10/><div id='f_container0'></div></div></div>
	 <div class="clean_table_controls" style="" ><div><span  style="margin:0 5px" id="paginator3"></span></div></div>
       </div>
       <div  id="table3"   class="data_table_container dtable btable "> </div>
     </div>
   </div>
   <div id="location_types_block" style="{if $edit!='location_types'}display:none{/if}" >
</div>

</div>







</div>

<div id="filtermenu0" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">
       <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
      {foreach from=$filter_menu item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_filter('{$menu.db_key}','{$menu.label}',0)"> {$menu.menu_label}</a></li>
      {/foreach}
    </ul>
  </div>
</div>
<div id="rppmenu0" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">
       <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
      {foreach from=$paginator_menu item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_rpp({$menu},0)"> {$menu}</a></li>
      {/foreach}
    </ul>
  </div>
</div>
{include file='footer.tpl'}
