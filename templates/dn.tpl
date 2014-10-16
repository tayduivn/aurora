{include file='header.tpl'} 
<div id="bd">
	<input type="hidden" id="dn_key" value="{$delivery_note->id}" />
	<input type="hidden" id="dn_state" value="{$delivery_note->get('Delivery Note State')}" />
	<input type="hidden" id="dn_picker_key" value="{$delivery_note->get('Delivery Note Assigned Picker Key')}" />
	<input type="hidden" id="dn_packer_key" value="{$delivery_note->get('Delivery Note Assigned Packer Key')}" />
	<input value="{t}Invalid number{/t}" id="label_invalid_number" type="hidden" />
	{include file='orders_navigation.tpl'} 
	<div class="branch">
		<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>&rarr; {if $user->get_number_stores()>1}<a href="orders_server.php?view=dns"> &#8704; {t}Delivery Notes{/t}</a> &rarr; {/if}<a href="orders.php?store={$store->id}&view=dns">{$store->get('Store Code')} {t}Delivery Notes{/t}</a> &rarr; {$delivery_note->get('Delivery Note ID')} ({$delivery_note->get_formated_state()})</span> 
	</div>
	<div class="top_page_menu" style="border:none">
		<div class="buttons" style="float:right">
			<button style="height:24px;" onclick="window.open('dn.pdf.php?id={$delivery_note->id}')"><img style="width:40px;height:12px;position:relative;bottom:3px" src="art/pdf.gif" alt=""></button> {if $delivery_note->get('Delivery Note Fraction Picked')==1 and $delivery_note->get('Delivery Note Fraction Packed')==1} {if $delivery_note->get('Delivery Note Approved Done')=='No'} {if $user->get('User Type')!='Warehouse'} <button id="approve_packing" onclick="approve_packing({$delivery_note->id},{$user->get('User Parent Key')},'dn')" style="height:24px;"><img id="approve_packing_img_{$delivery_note->id}" src="art/icons/flag_green.png" alt="" /> {t}Approve Picking/Packing{/t}</button> {/if} {else} {if $delivery_note->get('Delivery Note Approved To Dispatch')=='No'} <button onclick="approve_dispatching({$delivery_note->id},{$user->get('User Parent Key')},'dn')"><img id="approve_dispatching_img_{$delivery_note->id}" src="art/icons/package_green.png" alt=""> {t}Approve Dispatching{/t}</button> {else if $delivery_note->get('Delivery Note State')!='Dispatched' } <button onclick="set_as_dispatched({$delivery_note->id},{$user->get('User Parent Key')},'dn')"><img id="set_as_dispatched_img_{$delivery_note->id}" src="art/icons/lorry_go.png" alt=""> {t}Set as Dispatched{/t}</button> {/if} {if !$delivery_note->get_number_invoices() and $delivery_note->get('Delivery Note Type')=='Order'} <button style="height:24px;display:none" id="create_invoice"><img src="art/icons/money.png" alt=""> {t}Create Invoice{/t}</button> {/if} {/if} {else if $delivery_note->get('Delivery Note Fraction Picked')==0 and $delivery_note->get('Delivery Note Fraction Packed')==0} {if $delivery_note->get('Delivery Note Assigned Picker Key')} <button style="height:24px;" onclick="window.location='order_pick_aid.php?id={$delivery_note->id}'"><img src="art/icons/basket_put.png" alt=""> {t}Picking Aid Sheet{/t}</button> {else} <button style="height:24px;" id="pick_it_">{t}Process Delivery Note{/t} ({t}Picking{/t})</button> {/if} {else if $delivery_note->get('Delivery Note Fraction Picked')>0 and $delivery_note->get('Delivery Note Fraction Picked')<1 } <button style="height:24px;" onclick="window.location='order_pick_aid.php?id={$delivery_note->id}'"><img src="art/icons/basket_put.png" alt=""> {t}Picking Aid Sheet{/t}</button> <button style="height:24px;" onclick="window.location='order_pick_aid.php?id={$delivery_note->id}'"><img src="art/icons/basket_put.png" alt=""> {t}Picking Aid Sheet{/t}</button> {if $delivery_note->get('Delivery Note Fraction Packed')>0} <button style="height:24px;" onclick="window.location='order_pack_aid.php?id={$delivery_note->id}'"><img src="art/icons/package.png" alt=""> {t}Packing Aid Sheet{/t}</button> {/if} {else if $delivery_note->get('Delivery Note Fraction Picked')==1 } <button style="height:24px;" onclick="window.location='order_pick_aid.php?id={$delivery_note->id}'"><img src="art/icons/basket_put.png" alt=""> {t}Picking Aid Sheet{/t}</button> {if $delivery_note->get('Delivery Note Assigned Packer Key')} <button style="height:24px;" onclick="window.location='order_pack_aid.php?id={$delivery_note->id}'"><img src="art/icons/package.png" alt=""> {t}Packing Aid Sheet{/t}</button> {else} <button style="height:24px;" id="process_dn_packing">{t}Process Delivery Note{/t} ({t}Packing{/t})</button> {/if} {/if} 
		</div>
		<div class="buttons" style="float:left">
			<span class="main_title">{t}Delivery Note{/t} <span>{$delivery_note->get('Delivery Note ID')}</span> <span class="subtitle">({$delivery_note->get_formated_state()})</span></span> {*} {if isset($referal) and $referal=='store_pending_orders'} <button onclick="window.location='$referal_url'"><img src="art/icons/text_list_bullets.png" alt=""> {t}Pending Orders (Store){/t}</button> {else} <button onclick="window.location='warehouse_orders.php?id={$delivery_note->get('Delivery Note Warehouse Key')}'"><img src="art/icons/basket.png" alt=""> {t}Pending Orders{/t}</button> {/if} {*} 
		</div>
		<div style="clear:both">
		</div>
	</div>
	<div id="control_panel">
		<div id="dn_address">
			<h2 style="padding:0">
				<img src="art/icons/id.png" style="width:20px;position:relative;bottom:2px"> {$delivery_note->get('Delivery Note Customer Name')} <a class="id" href="customer.php?id={$delivery_note->get('Delivery Note Customer Key')}">{$customer->get_formated_id()}</a> 
			</h2>
			<div style="float:left;line-height: 1.0em;margin:5px 0px;color:#444">
				<span style="font-weight:500;color:#000">{$delivery_note->get('Order Customer Contact Name')}</span> 
			</div>
			<div style="float:left;line-height: 1.0em;margin:5px 0 0 0px;color:#444">
				{$delivery_note->get('Delivery Note XHTML Ship To')} 
			</div>
			<div style="clear:both">
			</div>
		</div>
		<div id="pp_data">
			<table border="0" class="info_block">
				<tr id="edit_weight_tr" ">
					<td class="aright"> {t}Weight{/t}:</td>
					<td class="aright"><span id="formated_parcels_weight">{if $weight==''}<span onclick="show_dialog_set_dn_data()" style="font-style:italic;color:#777;cursor:pointer">{t}Set weight{/t}{else}{$weight}{/if}</span></span></td>
				</tr>
				<tr id="edit_parcels_tr">
					<td class="aright"> {t}Parcels{/t}:</td>
					<td class="aright"><span id="formated_number_parcels">{if $parcels==''}<span onclick="show_dialog_set_dn_data()" style="font-style:italic;color:#777;cursor:pointer">{t}Set parcels{/t}{else}{$parcels}{/if}</span></span></td>
				</tr>
				<tr id="edit_consignment_tr">
					<td class="aright"> {t}Courier{/t}:</td>
					<td class="aright"><span id="formated_consignment">{if $consignment==''}<span onclick="show_dialog_set_dn_data()" style="font-style:italic;color:#777;cursor:pointer">{t}Set consignment{/t}{else}{$consignment}{/if}</span></span></td>
				</tr>
				{if $delivery_note->get('Delivery Note Date Start Picking')!='' or $delivery_note->get('Delivery Note Picker Assigned Alias')!=''} 
				<tr>
					<td class="aright"> {if $delivery_note->get('Delivery Note Date Finish Picking')==''}{t}Picking by{/t}{else}{t}Picked by{/t}{/if}: </td>
					<td width="200px" class="aright">{$delivery_note->get('Delivery Note Assigned Picker Alias')} </td>
					{if $delivery_note->get('Delivery Note Date Finish Picking')!=''} 
					<tr>
						<td class="aright">{t}Finish picking{/t}:</td>
						<td class="aright">{$delivery_note->get('Date Finish Picking')}</td>
					</tr>
					{else if $delivery_note->get('Delivery Note Date Finish Picking')!=''} 
					<tr>
						<td class="aright">{t}Start picking{/t}:</td>
						<td class="aright">{$delivery_note->get('Date Start Picking')}</td>
					</tr>
					{/if} 
				</tr>
				{/if} {if $delivery_note->get('Delivery Note Date Start Packing')!='' or $delivery_note->get('Delivery Note Packer Assigned Alias')!=''} 
				<tr>
					<td class="aright"> {if $delivery_note->get('Delivery Note Date Finish Packing')==''}{t}Packing by{/t}{else}{t}Packed by{/t}{/if}: </td>
					<td width="200px" class="aright">{$delivery_note->get('Delivery Note XHTML Packers')} </td>
					{if $delivery_note->get('Delivery Note Date Finish Packing')!=''} 
					<tr>
						<td class="aright">{t}Finish packing{/t}:</td>
						<td class="aright">{$delivery_note->get('Date Finish Packing')}</td>
					</tr>
					{else if $delivery_note->get('Delivery Note Date Finish Packing')!=''} 
					<tr>
						<td class="aright">{t}Start packing{/t}:</td>
						<td class="aright">{$delivery_note->get('Date Start Packing')}</td>
					</tr>
					{/if} 
				</tr>
				{/if} 
			</table>
			<div style="display:none" id="dn_state">
				{$delivery_note->get('Delivery Note XHTML State')} 
			</div>
		</div>
		<div id="dates">
			{if $delivery_note->get_notes()} 
			<div class="notes" style="border:1px solid #ccc;padding:5px;margin-bottom:5px">
				{$delivery_note->get_notes()|nl2br} 
			</div>
			{/if} 
			<table border="0" class="info_block">
				<tr>
					<td>{t}Created{/t}:</td>
					<td class="aright">{$delivery_note->get('Date Created')}</td>
				</tr>
			</table>
			<table border="0" class="info_block with_title">
				<tr style="border-bottom:1px solid #333;">
					<td colspan="2">{t}Orders{/t}:</td>
				</tr>
				{foreach from=$delivery_note->get_orders_objects() item=order} 
				<tr>
					<td class="aleft"><a href="order.php?id={$order->id}">{$order->get('Order Public ID')}</a> 
					<td class="aright"></td>
				</tr>
				{/foreach} 
			</table>
			{if $delivery_note->get_number_invoices()>0} 
			<table border="0" class="info_block with_title">
				<tr style="border-bottom:1px solid #333;">
					<td colspan="2">{t}Invoices{/t}:</td>
				</tr>
				{foreach from=$delivery_note->get_invoices_objects() item=invoice} 
				<tr>
					<td class="aleft"><a href="invoice.php?id={$invoice->id}">{$invoice->get('Invoice Public ID')}</a> <a target='_blank' href="invoice.pdf.php?id={$invoice->id}"> <img style="height:10px;vertical-align:0px" src="art/pdf.gif"></a> <img onclick="print_pdf('invoice',{$invoice->id})" style="cursor:pointer;margin-left:2px;height:10px;vertical-align:0px" src="art/icons/printer.png"> 
					<td class="aright">{$invoice->get_xhtml_payment_state()}</td>
				</tr>
				{/foreach} {/if} 
			</table>
		</div>
		<div style="clear:both">
		</div>
		<img id="show_dn_details" style="cursor:pointer" src="art/icons/arrow_sans_lowerleft.png" /> 
		<div id="dn_details_panel" style="display:none;border-top:1px solid #ccc;padding-top:10px;margin-top:10px">
			<div class="buttons">
				<button id="show_edit_dn_data" onclick="show_dialog_set_dn_data()"><img src="art/icons/basket_edit.png" alt="" /> {t}Set Parcels Data{/t}</button> {if $delivery_note->get('Delivery Note State')=='Dispatched'} <button onclick="undo_dispatch()"><img id="undo_dispatch_icon" src="art/icons/arrow_rotate_anticlockwise.png"> {t}Undo dispatch{/t}</button> {/if} 
			</div>
			<div style="width:550px">
				<table border="0" class="info_block">
					<tr>
						<td>{t}Order placed{/t}:</td>
						<td class="aright">{$delivery_note->get('Order Date Placed')}</td>
					</tr>
					<tr>
						<td>{t}Send to warehouse{/t}:</td>
						<td class="aright">{$delivery_note->get('Date Created')}</td>
					</tr>
					<tr>
						<td>{t}Start picking{/t}:</td>
						<td class="aright">{$delivery_note->get('Date Start Picking')}</td>
					</tr>
					<tr>
						<td>{t}Start picking{/t}:</td>
						<td class="aright">{$delivery_note->get('Date Start Picking')}</td>
					</tr>
					<tr>
						<td>{t}Finish picking{/t}:</td>
						<td class="aright">{$delivery_note->get('Date Finish Picking')}</td>
					</tr>
					<tr>
						<td>{t}Start packing{/t}:</td>
						<td class="aright">{$delivery_note->get('Date Start Packing')}</td>
					</tr>
					<tr>
						<td>{t}Finish packing{/t}:</td>
						<td class="aright">{$delivery_note->get('Date Finish Packing')}</td>
					</tr>
				</table>
				<table border="0" class="info_block">
					<tr>
						<td>{t}Customer Name{/t}:</td>
						<td class="aright">{$delivery_note->get('Delivery Note Customer Name')}</td>
					</tr>
					<tr>
						<td>{t}Contact Name{/t}:</td>
						<td class="aright">{$delivery_note->get('Delivery Note Customer Contact Name')}</td>
						<tr>
							<td>{t}Telephone{/t}:</td>
							<td class="aright">{$delivery_note->get('Delivery Note Telephone')}</td>
						</tr>
						<tr>
							<td>{t}Email{/t}:</td>
							<td class="aright">{$delivery_note->get('Delivery Note Email')}</td>
						</tr>
					</table>
					<table border="0" class="info_block">
						<tr>
							<td>{t}Pickers{/t}:</td>
							<td class="aright">{$delivery_note->get('Delivery Note XHTML Pickers')} </td>
						</tr>
						<tr>
							<td>{t}Packers{/t}:</td>
							<td class="aright">{$delivery_note->get('Delivery Note XHTML Packers')} </td>
						</tr>
					</table>
				</div>
				<div style="clear:both">
				</div>
				<div style="clear:both">
				</div>
				<img id="hide_dn_details" style="cursor:pointer;position:relative;top:5px" src="art/icons/arrow_sans_topleft.png" /> 
			</div>
		</div>
		<div class="data_table" style="clear:both;margin-top:20px">
			<span id="table_title" class="clean_table_title">{t}Items{/t}</span> 
			<div class="table_top_bar space">
			</div>
			{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 } 
			<div id="table0" style="font-size:80%" class="data_table_container dtable btable">
			</div>
		</div>
	</div>
	<div id="dialog_pick_it" style="padding:20px 20px 10px 20px">
		<div id="pick_it_msg">
		</div>
		<div class="buttons">
			<button class="positive" onclick="assign_picker(this,{$delivery_note->id})">{t}Assign Picker{/t}</button> <button class="positive" onclick="start_picking({$delivery_note->id},{$user->get_staff_key()})"><img src="art/icons/basket_put.png" alt=""> {t}Start Picking{/t}</button> <button class="negative" id="close_dialog_pick_it">{t}Cancel{/t}</button> 
		</div>
	</div>
	<div id="dialog_pack_it" style="padding:20px 20px 10px 20px">
		<div id="pack_it_msg">
		</div>
		<div class="buttons">
			<button class="positive" onclick="assign_packer(this,{$delivery_note->id})">{t}Assign Packer{/t}</button> <button class="positive" onclick="pack_it(this,{$delivery_note->id})"><img src="art/icons/package_add.png" alt=""> {t}Start Packing{/t}</button> <button class="negative" id="close_dialog_pack_it">{t}Cancel{/t}</button> 
		</div>
	</div>
	{include file='assign_picker_packer_splinter.tpl'} {include file='splinter_edit_delivery_note.tpl'} {include file='footer.tpl'} 