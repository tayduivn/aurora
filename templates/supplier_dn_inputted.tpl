{include file='header.tpl'} 
<div id="time2_picker" class="time_picker_div">
</div>
<div id="bd">
<input id="supplier_delivery_note_key" value="{$supplier_dn->id}" type="hidden" />
	<input id="supplier_key" value="{$supplier->id}" type="hidden" />
<input type="hidden" value="{$products_display_type}" id="products_display_type"> 

{include file='suppliers_navigation.tpl'} 

		<div class="branch">
		<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>&rarr; <a href="suppliers.php">{t}Suppliers{/t}</a> &rarr; <a href="supplier.php?id={$supplier->id}">{$supplier->get('Supplier Name')}</a> &rarr; {$supplier_dn->get('Supplier Delivery Note Public ID')} ({$supplier_dn->get('Supplier Delivery Note Current State')})</span> 
		</div>
	
	<div class="top_page_menu" style="border:none">
	<div class="buttons" style="float:left">
			<span class="main_title">{t}Supplier Delivery Note{/t} <span class="id">{$supplier_dn->get('Supplier Delivery Note Public ID')}</span></span> 
		</div>
	<div class="buttons">
		<button class="negative" id="delete_dn">{t}Delete{/t}</button> 
		<button id="save_inputted_dn">{t}Save Delivery Note{/t}</button>
	</div>
	<div style="clear:both"></div>
	</div>
	
	
	<div class="prodinfo" style="margin-top:2px;font-size:85%;border:1px solid #ddd;padding:10px">
		<div style="border:0px solid red;width:290px;float:right">
			<table border="0" class="order_header" style="margin-right:30px;float:right">
				<tr>
					<td class="aright" style="padding-right:40px">{t}Created{/t}:</td>
					<td>{$supplier_dn->get('Creation Date')}</td>
				</tr>
				<tr>
					<td class="aright" style="padding-right:40px">{t}Captured{/t}:</td>
					<td>{$supplier_dn->get('Input Date')}</td>
				</tr>
			</table>
		</div>
		
		<table border="0">
			<tr>
				<td>{t}Supplier Delivery Note Key{/t}:</td>
				<td class="aright">{$supplier_dn->get('Supplier Delivery Note Key')}</td>
			</tr>
			<tr>
				<td>{t}Supplier{/t}:</td>
				<td class="aright"><a href="supplier.php?id={$supplier->get('Supplier Key')}">{$supplier->get('Supplier Name')}</a></td>
			</tr>
			<tr>
				<td>{t}Items{/t}:</td>
				<td class="aright" id="distinct_products">{$supplier_dn->get('Number Items')}</td>
			</tr>
		</table>
		<table style="clear:both;border:none;display:none" class="notes">
			<tr>
				<td style="border:none">{t}Notes{/t}:</td>
				<td style="border:none"><textarea id="v_note" rows="2" cols="60"></textarea></td>
			</tr>
		</table>
		<div style="clear:both">
		</div>
	</div>
	<div id="the_table" class="data_table" style="margin:20px 0px;clear:both">
	<span class="clean_table_title">{t}Supplier Products{/t}</span> 
		
		<div class="elements_chooser">
			<span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $products_display_type=='all_products'}selected{/if} label_all_products" id="all_products">{t}Supplier Products{/t} (<span id="all_products_number">{$supplier->get_formated_number_products_to_buy()}</span>)</span> 
			<span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $products_display_type=='ordered_products'}selected{/if} label_ordered_products" id="ordered_products">{t}Ordered Products{/t} (<span id="ordered_products_number">{$supplier_dn->get('Number Items')}</span>)</span> 
		</div>
		
		<div class="table_top_bar space">
		</div>
		
		{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0} 
		<div id="table0" style="font-size:80%" class="data_table_container dtable btable">
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
<div id="received_dialog" style="padding:10px 15px">
	<div id="received_dialog_msg">
	</div>
	<div class="options" style="margin:0px 0;width:200px" id="received_method_container">
	</div>
	<table>
		<input type="hidden" id="date_type" value="now" />
		<tr id="tr_manual_received_date">
			<td class="aright" style="width:150px"><img src="art/icons/edit.gif" alt="{t}Edit{/t}" onclick="submit_date_manually()" /> {t}Received Date{/t}:</td>
			<td style="width:150px;padding-left:5px">{t}Now{/t}</td>
		</tr>
		<tbody style="display:none" id="tbody_manual_received_date">
			<tr>
				<td class="aright">{t}Received Date{/t}:</td>
				<td>
				<input id="v_calpop1" style="text-align:right;" class="text" name="submites_date" type="text" size="10" maxlength="10" value="" />
				<img id="calpop1" style="cursor:pointer" src="art/icons/calendar_view_month.png" align="top" alt="" /> 
				<div id="cal1Container" style="position:absolute;display:none; z-index:2">
				</div>
				</td>
			</tr>
			<tr>
				<td class="aright">{t}Time{/t}:</td>
				<td>
				<input id="v_time" style="text-align:right;" class="text" name="expected_date" type="text" size="5" maxlength="5" value="" />
				<img id="calpop1" style="cursor:pointer" src="art/icons/time.png" align="top" alt="" /> </td>
			</tr>
		</tbody>
		<tr>
			<input type="hidden" id="received_by" value="{$user->get_staff_key()}" />
			<td class="aright"><img src="art/icons/edit.gif" alt="{t}Edit{/t}" id="get_receiver" /> {t}Received By{/t}:</td>
			<td style="width:150px;padding-left:5px"><span id="received_by_alias">{$user->get_staff_alias()}</span></td>
		</tr>
		<input type="hidden" id="location_key" value="{$default_loading_location_key}" />
		<tr>
			<td class="aright"><img src="art/icons/edit.gif" alt="{t}Edit{/t}" id="get_location" /> {t}Receiving Location{/t}:</td>
			<td style="width:150px;padding-left:5px"> <span id="location_code">{$default_loading_location_code}</span> </td>
		</tr>
		<tr>
			<td colspan="2" style="border-top:1px solid #ddd;text-align:center;padding:10px 0 0 0"> <span style="margin-left:50px" class="state_details" onclick="received_order_save(this)">Save</span> </td>
		</tr>
	</table>
</div>
<div id="staff_dialog" class="yuimenu options_list">
	<div class="bd">
		<table border="1">
			{foreach from=$staff item=_staff name=foo} {if $_staff.mod==0}
			<tr>
				{/if} 
				<td staff_id="{$_staff.id}" id="receivers{$_staff.id}" onclick="select_staff(this,event)">{$_staff.alias}</td>
				{if $_staff.mod==$staff_cols}
			</tr>
			{/if} {/foreach} 
		</table>
		<span class="state_details" style="float:right" onclick="close_dialog('staff')">{t}Close{/t}</span> 
	</div>
</div>



<div id="location_dialog" class="yuimenu location_list">
	<div class="bd">
		<table border="1">
			{foreach from=$location item=_location name=foo} {if $_location.mod==0}
			<tr>
				{/if} 
				<td location_key="{$_location.key}" id="receivers{$_location.key}" onclick="select_location(this,event)">{$_location.code}</td>
				{if $_location.mod==$location_cols}
			</tr>
			{/if} {/foreach} 
		</table>
		<span class="state_details" style="float:right" onclick="close_dialog('location')">{t}Close{/t}</span> 
	</div>
</div>
{include file='footer.tpl'} 