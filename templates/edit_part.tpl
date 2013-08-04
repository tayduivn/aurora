{include file='header.tpl'} 
<div style="display:none; position:absolute; left:10px; top:200px; z-index:2" id="cal1Container">
</div>
<div id="bd">
	{include file='locations_navigation.tpl'} 
	<input type="hidden" id="part_sku" value="{$part->sku}"> 
	<div class="branch">
		<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>&rarr; {if $user->get_number_warehouses()>1}<a href="warehouses.php">{t}Warehouses{/t}</a> &rarr; {/if}<a href="inventory.php?warehouse_id={$warehouse->id}">{t}Inventory{/t}</a> &rarr; {$part->get_sku()}</span> 
	</div>
	<div class="top_page_menu">
		<div class="buttons" style="float:right">
			{if isset($next) }<img class="next" onmouseover="this.src='art/next_button.gif'" onmouseout="this.src='art/next_button.png'" title="{$next.title}" onclick="window.location='{$next.link}'" src="art/next_button.png" alt="{t}Next{/t}" />{/if} <button onclick="window.location='part.php?sku={$part->sku}'"><img src="art/icons/door_out.png" alt=""> {t}Exit Edit{/t}</button> <button onclick="window.location='associate_product.php?id={$part->sku}'"><img src="art/icons/add.png" alt=""> {t}Associate Product{/t}</button> 
		</div>
		<div class="buttons" style="float:left">
			{if isset($prev)}<img style="vertical-align:bottom;float:none" class="previous" onmouseover="this.src='art/previous_button.gif'" onmouseout="this.src='art/previous_button.png'" title="{$prev.title}" onclick="window.location='{$prev.link}'" src="art/previous_button.png" alt="{t}Previous{/t}" />{/if} 
			<span style="font-size:140%;width:600px;position:relative;bottom:-5px;left:-5px">
				<span style="font-weight:800"><span class="id">{$part->get_sku()}</span></span> 
				<span style="font-size:75%">
				<span id="part_reference_title" style="font-weight:800">{$part->get('Part Reference')}</span> 
				<span id="part_description_title" >  {$part->get('Part Unit Description')}</span>
				</span>
			</span> 
		</div>
		<div style="clear:both">
		</div>
	</div>
	<div style="display:none;clear:left;margin:0 0px">
		<h1>
			<span style="padding:0;font-size:80%">{t}Sold as{/t}: {$part->get('Part XHTML Currently Used In')}</span> 
		</h1>
	</div>
	<ul class="tabs" id="chooser_ul">
		<li><span class="item {if $edit=='description'}selected{/if}" id="description"> <span> {t}Part{/t}</span></span></li>
		<li><span class="item {if $edit=='products'}selected{/if}" id="products"> <span>{t}Products{/t}</span></span></li>
		<li><span class="item {if $edit=='suppliers'}selected{/if}" id="suppliers"> <span>{t}Suppliers{/t}</span></span></li>
		<li><span class="item {if $edit=='transactions'}selected{/if}" id="transactions"><span> {t}Stock Movements{/t}</span></span></li>
	</ul>
	<div class="tabbed_container" style="padding:0px">
		<div class="edit_block" {if $edit!="transactions" }style="display:none" {/if} id="d_transactions">
			<span class="clean_table_title">{t}Part Stock Transactions{/t}</span> 
			<div id="table_type" class="table_type">
				<div style="font-size:90%" id="transaction_chooser">
					<span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='all_transactions'}selected{/if}" id="restrictions_all_transactions" table_type="all_transactions">{t}All{/t} ({$transactions.all_transactions})</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='oip_transactions'}selected{/if}" id="restrictions_oip_transactions" table_type="oip_transactions">{t}OIP{/t} ({$transactions.oip_transactions})</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='out_transactions'}selected{/if}" id="restrictions_out_transactions" table_type="out_transactions">{t}Out{/t} ({$transactions.out_transactions})</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='in_transactions'}selected{/if}" id="restrictions_in_transactions" table_type="in_transactions">{t}In{/t} ({$transactions.in_transactions})</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='audit_transactions'}selected{/if}" id="restrictions_audit_transactions" table_type="audit_transactions">{t}Audits{/t} ({$transactions.audit_transactions})</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='move_transactions'}selected{/if}" id="restrictions_move_transactions" table_type="move_transactions">{t}Movements{/t} ({$transactions.move_transactions})</span> 
				</div>
			</div>
			<div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999;margin-bottom:10px">
			</div>
			{include file='table_splinter.tpl' table_id=3 filter_name=$filter_name3 filter_value=$filter_value3 } 
			<div style="font-size:85%" id="table3" class="data_table_container dtable btable">
			</div>
		</div>
		<div class="edit_block" {if $edit!="products" }style="display:none" {/if} id="d_products">
			<span class="clean_table_title">{t}Products{/t}</span> 
			<div class="table_top_bar">
				</div>
				
				
				<div class="clusters">
					<div class="buttons small left cluster">
					
					<button class="{if $products_view=='links'}selected{/if}" id="links">{t}Links{/t}</button>
						<button class="{if $products_view=='notes'}selected{/if}" id="notes">{t}Notes{/t}</button>
					</div>
				
					<div style="clear:both">
					</div>
				</div>
			{include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1 } 
			<div id="table1" class="data_table_container dtable btable" style="font-size:85%">
			</div>
		</div>
		<div class="edit_block" {if $edit!="suppliers" }style="display:none" {/if} id="d_suppliers">
			<span class="clean_table_title">{t}Suppliers{/t}</span> {include file='table_splinter.tpl' table_id=2 filter_name=$filter_name2 filter_value=$filter_value2 } 
			<div id="table2" class="data_table_container dtable btable" style="font-size:85%">
			</div>
			<div style="display:none">
				{t}Add new part{/t} 
				<div id="adding_new_part" style="width:200px;margin-bottom:45px">
					<input id="new_part_input" type="text"> 
					<div id="new_part_container">
					</div>
				</div>
			</div>
			{*} 
			<table class="edit" style="display:none;width:33em">
				<tbody id="new_part_form" style="display:none;background:#f1fdf2" part_id="">
					<tr class="top title">
						<td style="width:18em" class="label" colspan="2"> <img id="cancel_new" class="icon" onclick="cancel_new_part()" src="art/icons/cross.png"> <img id="save_part_new" class="icon" onclick="save_new_part()" src="art/icons/disk.png"> <span id="new_part_name"></span> <img id="save_part_{$part_id}" src="art/icons/new.png"> </td>
					</tr>
					<tr>
						<td class="label">{t}Parts product code{/t}:</td>
						<td style="text-align:left;width:11em"> 
						<input style="text-align:right;width:10em" value="" id="new_part_code" value=""></td>
					</tr>
					<tr class="last">
						<td class="label">{t}Estimated price per{/t} {$data.units_tipo_name}:</td>
						<td style="text-align:left">{$currency} 
						<input style="text-align:right;width:6em" value="" id="new_part_cost" id=""></td>
					</tr>
					<tr>
						<td style="background:white" colspan="4"></td>
					</tr>
				</tbody>
				<tbody id="current_parts_form">
					{foreach from=$parts item=part key=part_id } 
					<tr id="sup_tr1_{$part_id}" class="top title">
						<td class="label" colspan="2"> <img id="change_part_{$part_id}" class="icon" onclick="change_part({$part_id},'{$part}')" src="art/icons/arrow_refresh_bw.png"> <img id="delete_part_{$part_id}" class="icon" onclick="delete_part({$part_id},'{$part}')" src="art/icons/cross.png"> <img id="save_part_{$part_id}" class="icon" style="visibility:hidden" onclick="save_part({$part_id})" src="art/icons/disk.png"> <a href="part.php?sku={$part_id}">{$part.code}</a> </td>
					</tr>
					<tr id="sup_tr2_{$part_id}">
						<td class="label" style="width:15em">{t}Parts product code{/t}:</td>
						<td style="text-align:left;"> 
						<input style="padding-left:2px;text-align:left;width:10em" value="{$part.part_product_code}" name="code" onkeyup="part_changed(this,{$part_id})" ovalue="{$part.part_product_code}" id="v_part_code{$part_id}"></td>
					</tr>
					<tr id="sup_tr3_{$part_id}" class="last">
						<td class="label">{t}Cost per{/t} {$data.units_tipo_name}:</td>
						<td style="text-align:left">{$currency} 
						<input id="v_part_cost{$part_id}" style="text-align:right;width:6em" name="price " onblur="this.value=FormatNumber(this.value,'{$decimal_point}','{$thousand_sep}',4);part_changed(this,{$part_id})" value="{$part.price}" ovalue="{$part.price}"></td>
					</tr>
					<tr id="sup_tr4_{$part_id}">
						<td colspan="2"></td>
					</tr>
					{/foreach} 
				</tbody>
			</table>
			{*} 
		</div>
		<div class="edit_block" style="padding:0px;{if $edit!='description' }display:none{/if};border:1px solid red" id="d_description" >
			<div id="description_block_chooser" class="buttons small left tabs">
				<button class="first item {if $description_block=='status'}selected{/if}" id="description_block_status" block_id="status">{t}Status{/t}</button> 
				<button class="item {if $description_block=='description'}selected{/if}" id="description_block_description" block_id="description">{t}Reference, Codes{/t}</button> 
				<button class="item {if $description_block=='info'}selected{/if}" id="description_block_info" block_id="info">{t}Description{/t}</button> 

				<button class="item {if $description_block=='properties'}selected{/if}" id="description_block_properties" block_id="properties">{t}Properties{/t}</button> 
				<button class="item {if $description_block=='health_and_safety'}selected{/if}" id="description_block_health_and_safety" block_id="health_and_safety">{t}Health & Safety{/t}</button> <button style="display:none" class="item {if $description_block=='weight_dimension'}selected{/if}" id="description_block_weight_dimension" block_id="weight_dimension">{t}Weight/Dimensions{/t}</button> 
				<button class="item {if $description_block=='pictures'}selected{/if}" id="description_block_pictures" block_id="pictures">{t}Pictures{/t}</button> 

				
			</div>
			<div style="clear:both;height:0px;;margin-bottom:10px;border-bottom:1px solid #ccc">
				</div>
			
			<table id="d_description_block_status" class="edit" style="width:800px;{if $description_block!='status'}display:none{/if}">
				<tr class="title">
					<td colspan="6">{t}Status{/t}</td>
				</tr>
				<td class="label" style="width:200px">{t}Keeping Status{/t}:</td>
				<td> 
				<div class="buttons small">
					<button class="{if $part->get('Part Status')=='In Use'}selected{/if} positive" onclick="save_status('Part Status','In Use')" id="Part Status In Use">{t}In Use{/t}</button> <button class="{if $part->get('Part Status')=='Not In Use'}selected{/if} negative" onclick="save_status('Part Status','Not In Use')" id="Part Status Not In Use">{t}Not In Use{/t}</button> 
				</div>
				</td>
				<td style="width:300px"></td>
			</tr>
		</table>
		<table border=0 id="d_description_block_description" class="edit" style="width:890px;{if $description_block!='description'}display:none{/if}">
			<tr class="title">
				<td colspan="3">{t}Description{/t}</td>
			</tr>
			<tr class="space10">
				<td style="width:120px" class="label">{t}Units Type{/t}:</td>
				<td style="text-align:left"> 
				<input type="hidden" id="Part_Unit_Type" value="{$unit_type}" ovalue="{$unit_type}" />
				<select id="Part_Unit_Type_Select" onchange="change_part_unit_type(this)">
					{foreach from=$unit_type_options key=value item=label} 
					<option label='{$label}' value='{$value}' {if $value==$unit_type}selected{/if}>{$label}</option>
					{/foreach} 
				</select>
				</td>
				<td id="Part_Unit_Type_msg" class="edit_td_alert"></td>
			</tr>
			<tr>
				<td style="width:200px" class="label">{t}Reference{/t}:</td>
				<td style="text-align:left"> 
				<div>
					<input style="text-align:left;width:250px" id="Part_Reference" value="{$part->get('Part Reference')}" ovalue="{$part->get('Part Reference')}" valid="0"> 
					<div id="Part_Reference_Container">
					</div>
				</div>
				<span id="Part_Reference_msg" class="edit_td_alert" style="position:relative;left:260px"></span> </td>
				<td></td>
			</tr>
			
			<tr>
				<td class="label">{t}Description{/t}:</td>
				<td style="text-align:left"> 
				<div>
					<input style="text-align:left;;width:500px" id="Part_Unit_Description" value="{$part->get('Part Unit Description')}" ovalue="{$part->get('Part Unit Description')}" valid="0"> 
					<div id="Part_Unit_Description_Container">
					</div>
				</div>
				<span id="Part_Unit_Description_msg" class="edit_td_alert" style="position:relative;left:510px"></span> </td>
				<td></td>
			</tr>

			<tr>
				<td style="width:200px" class="label">{t}Commodity Code{/t}:</td>
				<td style="text-align:left"> 
				<div>
					<input style="text-align:left;width:250px" id="Part_Tariff_Code" value="{$part->get('Part Tariff Code')}" ovalue="{$part->get('Part Tariff Code')}" valid="0"> 
					<div id="Part_Tariff_Code_Container">
					</div>
				</div>
				<span id="Part_Tariff_Code_msg" class="edit_td_alert" style="position:relative;left:260px"></span> </td>
				<td></td>
			</tr>
			<tr>
				<td style="width:200px" class="label">{t}Duty Rate{/t}:</td>
				<td style="text-align:left"> 
				<div>
					<input style="text-align:left;width:250px" id="Part_Duty_Rate" value="{$part->get('Part Duty Rate')}" ovalue="{$part->get('Part Duty Rate')}" valid="0"> 
					<div id="Part_Duty_Rate_Container">
					</div>
				</div>
				<span id="Part_Duty_Rate_msg" class="edit_td_alert" style="position:relative;left:260px"></span> </td>
				<td></td>
			</tr>
			
			<tr class="space10">
				<td style="width:200px" class="label">{t}Barcode Type{/t}:</td>
				<td style="text-align:left"> 
				<input type="hidden" id="Part_Barcode_Type" value="{$part->get('Part Barcode Type')}" ovalue="{$part->get('Part Barcode Type')}"/>
				<div class="buttons left small" id="Part_Barcode_Type_options">
				<button id="Part_Barcode_Type_option_none" class="option {if $part->get('Part Barcode Type')=='none'}selected{/if}" onClick="change_barcode_type(this,'none')">{t}None{/t}</button>
				<button id="Part_Barcode_Type_option_ean8" class="option {if $part->get('Part Barcode Type')=='ean8'}selected{/if}" onClick="change_barcode_type(this,'ean8')">EAN-8</button>
				<button id="Part_Barcode_Type_option_ean13" class="option {if $part->get('Part Barcode Type')=='ean13'}selected{/if}" onClick="change_barcode_type(this,'ean13')">EAN-13</button>
				<button id="Part_Barcode_Type_option_code11" class="option {if $part->get('Part Barcode Type')=='code11'}selected{/if}" onClick="change_barcode_type(this,'code11')">Code 11</button>
				<button id="Part_Barcode_Type_option_code39" class="option {if $part->get('Part Barcode Type')=='code39'}selected{/if}" onClick="change_barcode_type(this,'code39')">Code 39</button>
				<button id="Part_Barcode_Type_option_code128" class="option {if $part->get('Part Barcode Type')=='code128'}selected{/if}" onClick="change_barcode_type(this,'code128')">Code 128</button>
				<button id="Part_Barcode_Type_option_codabar" class="option {if $part->get('Part Barcode Type')=='codabar'}selected{/if}" onClick="change_barcode_type(this,'codabar')">Codebar</button>
				</div>
				<span id="Part_Barcode_Type_msg" class="edit_td_alert" style=""></span> </td>
				<td></td>
			</tr>
			
			<tr class="space5" id="Part_Barcode_Data_Source_tr">
				<td style="width:200px" class="label">{t}Barcode Data Source{/t}:</td>
				<td style="text-align:left"> 
				<input type="hidden" id="Part_Barcode_Data_Source" value="{$part->get('Part Barcode Data Source')}" ovalue="{$part->get('Part Barcode Data Source')}"/>
				<div class="buttons left small" id="Part_Barcode_Data_Source_options">
				<button id="Part_Barcode_Data_Source_option_SKU" class="option {if $part->get('Part Barcode Data Source')=='SKU'}selected{/if}" onClick="change_barcode_data_source(this,'SKU')">{t}SKU{/t}</button>
				<button id="Part_Barcode_Data_Source_option_Reference" class="option {if $part->get('Part Barcode Data Source')=='Reference'}selected{/if}" onClick="change_barcode_data_source(this,'Reference')">{t}Reference{/t}</button>
				<button id="Part_Barcode_Data_Source_option_Other" class="option {if $part->get('Part Barcode Data Source')=='Other'}selected{/if}" onClick="change_barcode_data_source(this,'Other')">{t}Other{/t}</button>
			
				</div>
				<span id="Part_Barcode_Data_Source_msg" class="edit_td_alert" ></span> </td>
				<td></td>
			</tr>
			
			<tr id="Part_Barcode_Data_tr" style="{if $part->get('Part Barcode Data Source')!='Other'}display:none{/if}">
				<td style="width:200px" class="label">{t}Barcode Data{/t}:</td>
				<td style="text-align:left"> 
				<div>
					<input style="text-align:left;width:250px" id="Part_Barcode_Data" value="{$part->get('Part Barcode Data')}" ovalue="{$part->get('Part Barcode Data')}" valid="0"> 
					<div id="Part_Barcode_Data_Container">
					</div>
				</div>
				<span id="Part_Barcode_Data_msg" class="edit_td_alert" style="position:relative;left:260px"></span> </td>
				<td></td>
			</tr>
	
			
			
			<tr class="buttons">
				<td colspan="2"> 
				<div class="buttons" style="margin-right:360px">
					<button id="save_edit_part_unit" class="positive disabled">{t}Save{/t}</button>
									<button id="reset_edit_part_unit" class="negative disabled">{t}Reset{/t}</button> 
	
				</div>
				</td>
			</tr>
		</table>
		
		<table border=0 id="d_description_block_properties" class="edit" style="width:890px;{if $description_block!='properties'}display:none{/if}">



					<tr class="title">
						<td colspan="3">{t}Outer{/t} <span style="font-size:80%">({t}including packing{/t})</span></td>
					</tr>
					<tr class="space5" id="Part_Package_Type_tr">
						<td style="width:200px" class="label">{t}Package Type{/t}:</td>
						<td style="text-align:left"> 
						<input type="hidden" id="Part_Package_Type" value="{$part->get('Part Package Type')}" ovalue="{$part->get('Part Package Type')}" />
						<div class="buttons left small" id="Part_Package_Type_options">
							<button id="Part_Package_Type_option_Box" class="option {if $part->get('Part Package Type')=='Box'}selected{/if}" onclick="change_package_type(this,'Box')">{t}Box{/t}</button> <button id="Part_Package_Type_option_Bottle" class="option {if $part->get('Part Package Type')=='Bottle'}selected{/if}" onclick="change_package_type(this,'Bottle')">{t}Bottle{/t}</button> <button id="Part_Package_Type_option_Bag" class="option {if $part->get('Part Package Type')=='Bag'}selected{/if}" onclick="change_package_type(this,'Bag')">{t}Bag{/t}</button> <button id="Part_Package_Type_option_None" class="option {if $part->get('Part Package Type')=='None'}selected{/if}" onclick="change_package_type(this,'None')">{t}None{/t}</button> <button id="Part_Package_Type_option_Other" class="option {if $part->get('Part Package Type')=='Other'}selected{/if}" onclick="change_package_type(this,'Other')">{t}Other{/t}</button> 
						</div>
						<span id="Part_Package_Type_msg" class="edit_td_alert"></span> </td>
						<td></td>
					</tr>
					<tr class="space5">
						<td class="label">{t}Weight{/t}:</td>
						<td style="text-align:left;width:450px"> 
						<div>
							<input style="text-align:left;" id="Part_XHTML_Package_Weight" value="{$part->get('Part XHTML Package Weight')}" ovalue="{$part->get('Part XHTML Package Weight')}" valid="0"> 
							<div id="Part_XHTML_Package_Weight_Container">
							</div>
						</div>
						</td>
						<td id="Part_XHTML_Package_Weight_msg" class="edit_td_alert"></td>
					</tr>
					<tr class="space10">
						<td class="label">{t}Form factor{/t}:<br />
						</td>
						<td style="text-align:left"> 
						<input type="hidden" id="Part_Package_Dimensions_Type" value="{$part->get('Part Package Dimensions Type')}" ovalue="{$part->get('Part Package Dimensions Type')}" />
						<div class="buttons left small" id="Part_Package_Dimensions_Type_options">
							<button id="Part_Package_Dimensions_Type_option_Rectangular" class="option {if $part->get('Part Package Dimensions Type')=='Rectangular'}selected{/if}" onclick="change_package_type(this,'Rectangular')"><img src="art/icons/regtangular.png"> {t}Rectangular{/t}</button> 
							<button id="Part_Package_Dimensions_Type_option_Cilinder" class="option {if $part->get('Part Package Dimensions Type')=='Cilinder'}selected{/if}" onclick="change_package_type(this,'Cilinder')"><img src="art/icons/database.png"> {t}Cilinder{/t}</button> 
							<button id="Part_Package_Dimensions_Type_option_Sphere" class="option {if $part->get('Part Package Dimensions Type')=='Sphere'}selected{/if}" onclick="change_package_type(this,'Sphere')"><img src="art/icons/sport_golf.png" style="height:11px;width:11px;position:relative;bottom:-1px"> {t}Sphere{/t}</button> 
						</div>
						<span id="Part_Package_Dimensions_Type_msg" class="edit_td_alert"></span> </td>
						<td></td>
					</tr>	
					<tr class="space5">
						<td class="label">{t}Width{/t}:</td>
						<td style="text-align:left;width:450px"> 
						<div>
							<input style="text-align:left;" id="Part_Package_Dimensions_Width" value="{$part->get('Package Dimensions Width')}" ovalue="{$part->get('Package Dimensions Width')}" valid="0"> 
							<div id="Part_Package_Dimensions_Width_Container">
							</div>
						</div>
						</td>
						<td id="Part_Package_Dimensions_Width_msg" class="edit_td_alert"></td>
					</tr>
					<tr>
						<td class="label">{t}Depth{/t}:</td>
						<td style="text-align:left;width:450px"> 
						<div>
							<input style="text-align:left;" id="Part_Package_Dimensions_Depth" value="{$part->get('Package Dimensions Depth')}" ovalue="{$part->get('Package Dimensions Depth')}" valid="0"> 
							<div id="Part_Package_Dimensions_Depth_Container">
							</div>
						</div>
						</td>
						<td id="Part_Package_Dimensions_Depth_msg" class="edit_td_alert"></td>
					</tr>						
					<tr>
						<td class="label">{t}Length{/t}:</td>
						<td style="text-align:left;width:450px"> 
						<div>
							<input style="text-align:left;" id="Part_Package_Dimensions_Length" value="{$part->get('Package Dimensions Length')}" ovalue="{$part->get('Package Dimensions Length')}" valid="0"> 
							<div id="Part_Package_Dimensions_Length_Container">
							</div>
						</div>
						</td>
						<td id="Part_Package_Dimensions_Length_msg" class="edit_td_alert"></td>
					</tr>		
					<tr>
						<td class="label">{t}Diameter{/t}:</td>
						<td style="text-align:left;width:450px"> 
						<div>
							<input style="text-align:left;" id="Part_Package_Dimensions_Diameter" value="{$part->get('Package Dimensions Diameter')}" ovalue="{$part->get('Package Dimensions Diameter')}" valid="0"> 
							<div id="Part_Package_Dimensions_Diameter_Container">
							</div>
						</div>
						</td>
						<td id="Part_Package_Dimensions_Diameter_msg" class="edit_td_alert"></td>
					</tr>		
					
					<tr class="title">
						<td colspan="3">{t}Unit{/t}</td>
					</tr>
					<tr class="space5">
						<td class="label">{t}Weight{/t}:</td>
						<td style="text-align:left"> 
						<div>
							<input style="text-align:left;" id="Part_XHTML_Unit_Weight" value="{$part->get('Part XHTML Unit Weight')}" ovalue="{$part->get('Part XHTML Unit Weight')}" valid="0"> 
							<div id="Part_XHTML_Unit_Weight_Container">
							</div>
						</div>
						</td>
						<td id="Part_XHTML_Unit_Weight_msg" class="edit_td_alert"></td>
					</tr>
					<tr>
						<td class="label">{t}Dimensions{/t}:</td>
						<td style="text-align:left;"> 
						<div>
							<input style="text-align:left;" id="Part_XHTML_Unit_Dimensions" value="{$part->get('Part XHTML Unit Dimensions')}" ovalue="{$part->get('Part XHTML Unit Dimensions')}" valid="0"> 
							<div id="Part_XHTML_Unit_Dimensions_Container">
							</div>
						</div>
						</td>
						<td id="Part_Unit_Dimensions_msg" class="edit_td_alert"></td>
					</tr>

			
			
			<tr class="buttons">
				<td colspan="2"> 
				<div class="buttons" style="margin-right:360px">
									<button id="save_edit_part_properties" class="positive disabled">{t}Save{/t}</button> 

									<button id="reset_edit_part_properties" class="negative disabled">{t}Reset{/t}</button> 

				</div>
				</td>
			</tr>
		</table>
		<table id="d_description_block_info" class="edit" border="0" style="width:890px;{if $description_block!='info'}display:none{/if}">
			<tr class="title">
				<td>{t}Information{/t} <span id="part_general_description_msg"></span></td>
			</tr>
			<tr>
				<td style="padding:5px 0 0 0 "> 
				<form onsubmit="return false;">
<textarea id="part_general_description" ovalue="{$part->get('Part General Description')|escape}" rows="20" cols="75">{$part->get('Part General Description')|escape}</textarea> 
				</form>
				</td>
			</tr>
			<tr>
				<td> 
				<div class="buttons">
									<button id="save_edit_part_description" class="positive disabled">{t}Save{/t}</button> 

					<button id="reset_edit_part_description" class="negative disabled">{t}Reset{/t}</button> 
				</div>
				</td>
			</tr>
		</table>
		<table id="d_description_block_health_and_safety" class="edit" border="0" style="width:890px;;{if $description_block!='health_and_safety'}display:none{/if}">
			<tr class="title">
				<td>{t}Attachments{/t}</td>
			</tr>
			
			<tr class="first">
			<td style="width:200px" class="label">{t}MSDS File{/t}:</td>
				
		
				<td colspan=2>
				<span id="MSDS_File" style="float:left;margin-right:20px">{$part->get('Part MSDS Attachment XHTML Info')}</span>
				
				<form id="upload_MSDS_File_form" style="{if $part->get('Part MSDS Attachment Bridge Key')}display:none{/if}" enctype="multipart/form-data" method="post" >
				<input id="upload_MSDS_File_file" style="float:left;border:1px solid #ddd;position:relative;bottom:3px;margin-right:10px" type="file" name="attach" />
				
				</form>
				<div class="buttons small left" style="display:inline">
				<button id="delete_MSDS_File" style="{if !$part->get('Part MSDS Attachment Bridge Key')}display:none{/if}" class="negative">{t}Delete{/t}</button>
				<button id="replace_MSDS_File" style="{if !$part->get('Part MSDS Attachment Bridge Key')}display:none{/if}" class="">{t}Replace{/t}</button>
				<button id="cancel_replace_MSDS_File" style="display:none" class="negative">{t}Cancel{/t}</button>

				<button id="upload_MSDS_File_button" style="{if $part->get('Part MSDS Attachment Bridge Key')}display:none{/if}" class="disabled">{t}Upload{/t}</button>

				</div>
				<span id="MSDS_File_msg" class="error"></span>
				</td>
				
			</tr>	
			<tr class="title">
				<td>{t}Health & Safety{/t} <span id="part_health_and_safety_msg"></span></td>
			</tr>
			<tr class="space10">
			<td style="width:200px" class="label">{t}UN Number{/t}:</td>
				<td style="text-align:left"> 
				<div>
					<input style="text-align:left;width:100px" id="Part_UN_Number" value="{$part->get('Part UN Number')}" ovalue="{$part->get('Part UN Number')}" valid="0"> 
					<div id="Part_UN_Number_Container">
					</div>
				</div>
				<span id="Part_UN_Number_msg" class="edit_td_alert" style="position:relative;left:260px"></span> </td>
				<td></td>
			</tr>	
		
			<tr>
			<td style="width:200px" class="label">{t}UN Number Class{/t}:</td>
				<td style="text-align:left"> 
				<div>
					<input style="text-align:left;width:100px" id="Part_UN_Number_Class" value="{$part->get('Part UN Class')}" ovalue="{$part->get('Part UN Class')}" valid="0"> 
					<div id="Part_UN_Number_Class_Container">
					</div>
				</div>
				<span id="Part_UN_Number_Class_msg" class="edit_td_alert" style="position:relative;left:260px"></span> </td>
				<td></td>
			</tr>
			
			<tr class="space5" id="Part_Packing_Group_tr">
				<td style="width:200px" class="label">{t}Packing Group{/t}:</td>
				<td style="text-align:left"> 
				<input type="hidden" id="Part_Packing_Group" value="{$part->get('Part Packing Group')}" ovalue="{$part->get('Part Packing Group')}"/>
				<div class="buttons left small" id="Part_Packing_Group_options">
				<button id="Part_Packing_Group_option_None" class="option {if $part->get('Part Packing Group')=='None'}selected{/if}" onClick="change_packing_group(this,'None')">{t}None{/t}</button>
				<button id="Part_Packing_Group_option_I" class="option {if $part->get('Part Packing Group')=='I'}selected{/if}" onClick="change_packing_group(this,'I')">I</button>
				<button id="Part_Packing_Group_option_II" class="option {if $part->get('Part Packing Group')=='II'}selected{/if}" onClick="change_packing_group(this,'II')">II</button>
				<button id="Part_Packing_Group_option_III" class="option {if $part->get('Part Packing Group')=='III'}selected{/if}" onClick="change_packing_group(this,'III')">III</button>
			
				</div>
				<span id="Part_Packing_Group_msg" class="edit_td_alert" ></span> </td>
				<td></td>
			</tr>
			
			
			<tr>
			<td style="width:200px" class="label">{t}Proper Shipping Name{/t}:</td>
				<td style="text-align:left;width:450px"> 
				<div>
					<input style="text-align:left;width:100%" id="Part_Proper_Shipping_Name" value="{$part->get('Part Proper Shipping Name')}" ovalue="{$part->get('Part Proper Shipping Name')}" valid="0"> 
					<div id="Part_Proper_Shipping_Name_Container">
					</div>
				</div>
				<span id="Part_Proper_Shipping_Name_msg" class="edit_td_alert"></span> </td>
				<td></td>
			</tr>
			
				<tr>
			<td style="width:200px" class="label">{t}Hazard Indentification (HIN){/t}:</td>
				<td style="text-align:left"> 
				<div>
					<input style="text-align:left;width:100px" id="Part_Hazard_Indentification_Number" value="{$part->get('Part Hazard Indentification Number')}" ovalue="{$part->get('Part Hazard Indentification Number')}" valid="0"> 
					<div id="Part_Hazard_Indentification_Number_Container">
					</div>
				</div>
				<span id="Part_Hazard_Indentification_Number_msg" class="edit_td_alert" style="position:relative;left:260px"></span> </td>
				<td></td>
			</tr>
			<tr class="space10">
			<td style="width:200px" class="label">{t}More info{/t}:</td>
				<td>
					<div class="buttons small left">
					<button id="show_part_health_and_safety_editor" style="{if $part->get('Part Health And Safety')!=''}display:none{/if}">{t}Show editor{/t}</button>
					</div>
				</td>
			</tr>
			
			<tr style="{if $part->get('Part Health And Safety')==''}display:none{/if}" id="part_health_and_safety_editor_tr">
				<td colspan=3 style="padding:5px 0 0 0 "> 
				<form onsubmit="return false;">
<textarea id="part_health_and_safety" ovalue="{$part->get('Part Health And Safety')|escape}" rows="20" cols="75">{$part->get('Part Health And Safety')|escape}</textarea> 
				</form>
				</td>
			</tr>
			<tr class="buttons">
			
				<td colspan=3> 
				<div id="edit_part_health_and_safety_buttons" class="buttons left" style="margin-left:{if $part->get('Part Health And Safety')==''}400px{else}700px{/if}">
					<button id="reset_edit_part_health_and_safety" class="negative disabled">{t}Reset{/t}</button> 
					<button id="save_edit_part_health_and_safety" class="positive disabled">{t}Save{/t}</button> 
				</div>
				</td>
			</tr>
		</table>
	<table id="d_description_block_pictures" class="edit" border="0" style="width:890px;;{if $description_block!='pictures'}display:none{/if}">
			<tr class="title">
				<td>{t}Pictures{/t} <span id="part_pictures_msg"></span></td>
			</tr>
			<tr class="space10">
				<td > 
				{include file='edit_images_splinter.tpl' parent=$part} 
				</td>
			</tr>
			
		</table>
		{*} 
		<table class="edit">
			<tr class="title">
				<td colspan="5">{t}Categories{/t}</td>
			</tr>
			{foreach from=$categories item=cat key=cat_key name=foo } 
			<tr>
				<td class="label">{t}{$cat.name}{/t}:</td>
				<td> {foreach from=$cat.teeth item=cat2 key=cat2_id name=foo2} 
				<div id="cat_{$cat2_id}" default_cat="{$cat2.default_id}" class="options" style="margin:5px 0">
					{foreach from=$cat2.elements item=cat3 key=cat3_id name=foo3} <span class="catbox {if $cat3.selected}selected{/if}" value="{$cat3.selected}" ovalue="{$cat3.selected}" onclick="checkbox_changed(this)" cat_id="{$cat3_id}" id="cat{$cat3_id}" parent="{$cat3.parent}" position="{$cat3.position}" default="{$cat3.default}">{$cat3.name}</span> {/foreach} 
				</div>
				{/foreach} </td>
			</tr>
			{/foreach} 
		</table>
		<div class="buttons">
					<button style="margin-right:10px;" id="save_edit_part_custom_field" class="positive disabled">{t}Save{/t}</button> 

			<button style="margin-right:10px;" id="reset_edit_part_custom_field" class="negative disabled">{t}Reset{/t}</button> 
		</div>
		<table class="edit">
			<tr class="title">
				<td colspan="5">{t}Custom Fields{/t}</td>
			</tr>
			{foreach from=$show_case key=custom_field_key item=custom_field_value } 
			<tr id="tr_{$custom_field_value.lable}">
				<td class="label">{$custom_field_key}:</td>
				<td style="text-align:left"> 
				<div>
					<input style="text-align:left;width:100%" id="Part_{$custom_field_value.lable}" value="{$custom_field_value.value}" ovalue="{$custom_field_value.value}" valid="0"> 
					<div id="Part_{$custom_field_value.lable}_Container">
					</div>
				</div>
				</td>
				<td> <span id="Part_{$custom_field_value.lable}_msg" class="edit_td_alert"></span> </td>
			</tr>
			{/foreach} 
		</table>
		{*} 
	</div>
</div>
<div class="buttons small" style="margin-top:0">
	<button id="show_history" style="{if $show_history}display:none{/if};margin-right:0px" onclick="show_history()">{t}Show changelog{/t}</button> <button id="hide_history" style="{if !$show_history}display:none{/if};margin-right:0px" onclick="hide_history()">{t}Hide changelog{/t}</button> 
</div>
<div id="history_table" class="data_table" style="clear:both;{if !$show_history}display:none{/if}">
	<span class="clean_table_title">{t}Changelog{/t}</span> 
	<div class="table_top_bar" style="margin-bottom:15px">
	</div>
	{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 } 
	<div id="table0" class="data_table_container dtable btable">
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
<div id="dialog_delete_transaction" style="display:none;border:1px solid #ccc;text-align:left;padding:10px;">
	<div id="delete_transaction_msg">
	</div>
	<table style="margin:10px" border="0">
		<tr>
			<td style="padding-top:10px">{t}Are you sure you want to delet this transaction{/t}:</td>
		</tr>
		<tr>
			<td> 
			<div class="buttons">
				<img id="save_delete_transaction_wait" style="display:none;position:relative;left:20px" src="art/loading.gif" alt="" /> <button id="save_delete_transaction" class="positive">{t}Yes delete it{/t}</button> <button id="cancel_delete_transaction" class="negative">{t}No{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>
<div id="dialog_delete_part_location_transaction" style="padding:10px 10px 10px 10px;">
	<h2 style="padding-top:0px">
		{t}Delete Transaction{/t} 
	</h2>
	<h2 style="padding-top:0px" id="dialog_delete_part_location_transaction_data">
	</h2>
	<input type="hidden" id="dialog_delete_part_location_transaction_key" value=""> 
	<input type="hidden" id="dialog_delete_part_location_transaction_table_id" value=""> 
	<input type="hidden" id="dialog_delete_part_location_transaction_recordIndex" value=""> 
	<p>
		{t}This operation cannot be undone{/t}.<br> {t}Would you like to proceed?{/t} 
	</p>
	<div style="display:none" id="deleting">
		<img src="art/loading.gif" alt=""> {t}Deleting part location transaction, wait please{/t} 
	</div>
	<div id="delete_part_location_transaction_buttons" class="buttons">
		<button onclick="save_delete('delete','part_location_transaction')" class="positive">{t}Yes, delete it!{/t}</button> <button onclick="cancel_delete('delete','part_location_transaction')" class="negative">{t}No i dont want to delete it{/t}</button> 
	</div>
</div>


<div id="dialog_delete_MSDS_File" style="padding:10px 10px 10px 10px;">
	<h2 style="padding-top:0px">
		{t}Delete File{/t} 
	</h2>

	<p>
		{t}This operation cannot be undone{/t}.<br> {t}Would you like to proceed?{/t} 
	</p>
	<div style="display:none" id="deleting">
		<img src="art/loading.gif" alt=""> {t}Deleting file, wait please{/t} 
	</div>
	<div  class="buttons">
		<button id="save_delete_MSDS_File"  class="positive">{t}Yes, delete it!{/t}</button> <button id="cancel_delete_MSDS_File"   class="negative">{t}No i dont want to delete it{/t}</button> 
	</div>
</div>


{include file='footer.tpl'} 