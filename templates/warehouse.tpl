{include file='header.tpl'} 
<div id="bd" class="no_padding">
	<div style="padding:0 20px">
		{include file='locations_navigation.tpl'} 
		<input type="hidden" id="warehouse_key" value="{$warehouse->id}" />
		<div class="branch">
			<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>&rarr; {if $user->get_number_warehouses()>1}<a href="warehouses.php">{t}Warehouses{/t}</a> &rarr; {/if}{t}Warehouse{/t}: {$warehouse->get('Warehouse Name')}  <span id="areas_view" style="{if $view!='areas'}display:none{/if}">({t}Areas{/t})</span><span id="locations_view" style="{if $view!='locations'}display:none{/if}">({t}Locations{/t})</span></span> 
		</div>
		<div class="top_page_menu">
			<div class="buttons" style="float:right">
				{if $modify} <button onclick="window.location='edit_warehouse.php?id={$warehouse->id}'"><img src="art/icons/vcard_edit.png" alt=""> {t}Edit Warehouse{/t}</button> {/if} <button onclick="window.location='warehouse_stats.php?id={$warehouse->id}'"><img src="art/icons/chart_pie.png" alt=""> {t}Statistics{/t}</button> <button onclick="window.location='warehouse_map.php?id={$warehouse->id}'"><img src="art/icons/application_view_gallery.png" alt=""> {t}Map{/t}</button> <button id="location_audit"><img src="art/icons/application_view_gallery.png" alt=""> {t}Audit{/t}</button> 
			</div>
			<div class="buttons" style="float:left">
				<span class="main_title"><img src="art/icons/warehouse.png" style="height:20px;position:relative;bottom:2px" title="{t}Warehouse{/t}" /> {$warehouse->get('Warehouse Name')} ({$warehouse->get('Warehouse Code')})</span> 
			</div>
			<div style="clear:both">
			</div>
		</div>
	</div>
	<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:10px">
		<li> <span class="item {if $view=='locations'}selected{/if}" id="locations"> <span> {t}Locations{/t}</span></span></li>
		<li> <span class="item {if $view=='areas'}selected{/if}" id="areas"> <span> {t}Areas{/t}</span></span></li>
		<li style="display:none"> <span class="item {if $view=='shelfs'}selected{/if}" id="shelfs"> <span> {t}Shelfs{/t}</span></span></li>
		<li style="display:none"> <span class="item {if $view=='map'}selected{/if}" id="map"><span> {t}Map{/t}</span></span></li>
		<li style="display:none"> <span class="item {if $view=='movements'}selected{/if}" id="movements"> <span> {t}Movements{/t}</span></span></li>
		<li style="display:none"> <span class="item {if $view=='stats'}selected{/if}" id="stats"> <span> {t}Stats{/t}</span></span></li>
		<li> <span class="item {if $view=='replenishment'}selected{/if}" id="replenishment"> <span> {t}Replenishments{/t} ({$replenishments_number})</span></span></li>
		<li> <span class="item {if $view=='part_locations'}selected{/if}" id="part_locations"> <span> {t}Part-Locations{/t} ({$part_location_number})</span></span></li>

	</ul>
	<div style="clear:both;width:100%;border-bottom:1px solid #ccc">
	</div>
	<div id="block_locations" style="{if $view!='locations'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
		<div id="the_table0" class="data_table" style="margin:20px 0px;clear:both">
			<span class="clean_table_title">{t}Locations{/t}
										<img  id="export_locations" class="export_data_link" label="{t}Export Table{/t}" alt="{t}Export Table{/t}" src="art/icons/export_csv.gif">

			</span> 
			<div class="elements_chooser">
				{foreach from=$location_flags_elements_data item=flag}
				<span onClick="change_location_elements(this,'flags')" style="float:right;margin-left:20px;" class="{if $location_flags_elements[$flag.color]}selected{/if} label_page_type" id="elements_{$flag.color}"><img class="icon" src="art/icons/{$flag.img}" /> {$flag.label} (<span id="elements_{$flag.color}_number">{$flag.number}</span>)</span> 
				{/foreach}
			</div>
			<div class="table_top_bar space">
			</div>
			{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 } 
			<div id="table0" class="data_table_container dtable btable">
			</div>
		</div>
	</div>
	<div id="block_areas" style="{if $view!='areas'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
		<div id="the_table1" class="data_table" style="margin:0px 0px;clear:both">
			<span class="clean_table_title">{t}Warehouse Areas{/t}</span> 
			<div class="table_top_bar space">
			</div>
			{include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1 } 
			<div id="table1" class="data_table_container dtable btable">
			</div>
		</div>
	</div>
	<div id="block_map" style="{if $view!='map'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
		<div style="border:1px solid #ccc;text-align:left;margin:0px;padding:20px;height:270px;width:600px;margin: 0 0 10px 0;float:left">
		</div>
	</div>
<div id="block_shelfs" style="{if $view!='shelfs'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
</div>
<div id="block_movements" style="{if $view!='movements'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
</div>
<div id="block_stats" style="{if $view!='stats'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
</div>
	<div id="block_replenishment" style="{if $view!='replenishment'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
		<div id="the_table2" class="data_table" style="margin:20px 0px;clear:both">
			<span class="clean_table_title">{t}Picking Replenishments{/t}</span> 
			
			<div class="table_top_bar space">
			</div>
			{include file='table_splinter.tpl' table_id=2 filter_name=$filter_name2 filter_value=$filter_value2 } 
			<div id="table2" class="data_table_container dtable btable" style="font-size:85%">
			</div>
		</div>
	</div>
	<div id="block_part_locations" style="{if $view!='part_locations'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
		<div id="the_table2" class="data_table" style="margin:20px 0px;clear:both">
			<span class="clean_table_title">{t}Part Location Pairs{/t}
							<img  id="export_part_locations" class="export_data_link" label="{t}Export Table{/t}" alt="{t}Export Table{/t}" src="art/icons/export_csv.gif">

			</span> 
			
			<div class="table_top_bar space">
			</div>
			{include file='table_splinter.tpl' table_id=3 filter_name=$filter_name3 filter_value=$filter_value3 } 
			<div id="table3" class="data_table_container dtable btable" style="font-size:85%">
			</div>
		</div>
	</div>
	
</div>

<div id="filtermenu0" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu0 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',0)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu0" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu0 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},0)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu1" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu1 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',1)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu1" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu1 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},1)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>

<div id="filtermenu2" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu2 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',2)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu2" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu2 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},2)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>


<div id="filtermenu3" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu3 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',3)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu3" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu3 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},3)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>


<div id="dialog_edit_flag" style="padding:20px 20px 5px 20px">
<table>
	<tr>
 		<td>
 				<input id="edit_flag_location_key" value="" type="hidden">
 				 <input id="edit_flag_table_record_index" value="" type="hidden">
 				 <input id="edit_flag_table_id" value="" type="hidden">


		<div id="warehouse_flags"   class="buttons small" >
			{foreach from=$location_flags_elements_data item=cat key=cat_id name=foo}
				<button  class="buttons" onclick="save_location_flag('flag','{$cat.key}')"  id="flag_{$cat.color}"><img src="art/icons/{$cat.img}"  > {$cat.label}</button>
	    	{/foreach}
		</div>
		</td>
	</tr> 
</table>
</div>
{include file='export_splinter.tpl' id='locations' export_fields=$export_locations_fields map=$export_locations_map is_map_default={$export_locations_map_is_default}}
{include file='export_splinter.tpl' id='part_locations' export_fields=$export_part_locations_fields map=$export_part_locations_map is_map_default={$export_part_locations_map_is_default}}
<div id="Editor_limit_quantities" style="padding:10px">
<input type="hidden" id="quantity_limits_location_key" value="" />
<input type="hidden" id="quantity_limits_part_sku" value="" />
<input type="hidden" id="quantity_limits_table_record_index" value="" />
<input type="hidden" id="quantity_limits_table_id" value="" />




	<table style="margin:10px">
		<tr style="display:none" id="dialog_qty_msg">
			<td colspan="2"> 
			<div id="dialog_qty_msg_text" class="error_message">
				x
			</div>
			</td>
		</tr>
		<tr>
			<td>{t}Min Qty:{/t}</td>
			<td> 
			<input type="text" value="" id="min_qty" />
			</td>
		</tr>
		<tr>
			<td>{t}Max Qty:{/t}</td>
			<td> 
			<input type="text" value="" id="max_qty" />
			</td>
		</tr>
		<tr>
			<td colspan="2"> 
			<div class="buttons" style="margin-top:10px">
				<button class="positive" onclick="save_picking_quantity_limits()">{t}Save{/t}</button> <button class="negative" id="close_quantity_limits_">{t}Cancel{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>
{include file='footer.tpl'} 