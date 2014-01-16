{include file='header.tpl'} 
<div id="bd">
	{include file='top_search_splinter.tpl'} 
	<input value="{$delivery_note->id}" id="dn_key" type="hidden" />
	<input value="{$warehouse->id}" id="warehouse_key" type="hidden" />
	<input value="{$user->can_edit('assign_pp')}" id="can_assign_pp" type="hidden" />
	
	
	<div class="branch">
		<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>&rarr;{if $user->get('User Type')!='Warehouse'} {if $user->get_number_warehouses()>1}<a href="warehouses.php">{t}Warehouses{/t}</a> &rarr; {/if}<a href="inventory.php?warehouse_id={$warehouse->id}">{t}Inventory{/t}</a> &rarr;{/if} <a href="warehouse_orders.php?id={$warehouse->id}">{t}Pending Orders{/t}</a> &rarr; {$delivery_note->get('Delivery Note ID')} ({t}Pack Aid{/t})</span> 
	</div>
	<div id="top_page_menu" class="top_page_menu">
		<div style="float:left">
			<span class="main_title no_buttons"> {t}Pack aid{/t} <a class="id" href="dn.php?id={$delivery_note->id}">{$delivery_note->get('Delivery Note ID')}</a> <span id="dn_formated_state" class="subtitle">{$delivery_note->get_formated_state()}</span></span> 
		</div>
		<div class="buttons" style="float:right">
			<button id="picking_aid" onclick="window.location='order_pick_aid.php?id={$delivery_note->id}'" href="order_pick_aid.php?id={$delivery_note->id}" class="{if $delivery_note->get('Delivery Note Fraction Picked')==1}disabled{/if}"><img src="art/icons/basket.png" alt="" /> {t}Picking Aid{/t}</button>
			<button id="pack_all" style="height:24px;{if $delivery_note->get('Delivery Note Fraction Packed')==1}display:none{/if}"><img src="art/icons/accept.png" alt="" /> {t}Set all as Packed{/t}</button> 
			<span  style="{if  $warehouse->get('Warehouse Approve PP Locked')=='No' or !$user->can_edit('assign_pp')}display:none{/if}"><button id="approve_packing" style="height:24px;{if $delivery_note->get('Delivery Note State')!='Packed'}display:none{/if}"><img id="approve_packing_img" src="art/icons/flag_green.png" alt="" /> {t}Approve Picking/Packing{/t}</button> </span>
		</div>
		<div style="clear:both">
		</div>
	</div>
	<div id="control_panel" style="clear:both;margin-top:15px">
		<div style="border:1px solid #ccc;text-align:left;padding:10px;margin: 0px 0 10px 0;xheight:15em">
			<div style="xborder:1px solid #ddd;width:350px;float:left">
				<h2 style="padding:0">
					{$delivery_note->get('Delivery Note Customer Name')} <a class="id" href="customer.php?id={$customer->id}">{$customer->get_formated_id()}</a> ({$delivery_note->get('Delivery Note Country 2 Alpha Code')}) 
				</h2>
				<div style="clear:both">
				</div>
			</div>
			<div style="border:0px solid #ddd;width:330px;float:right;">
				<table style="xdisplay:none;width:100%;xborder-top:1px solid #333;xborder-bottom:1px solid #333;width:100%,padding:0;margin:0;float:right;margin-left:0px">
					<tbody id="resend" style="xdisplay:none">
						<tr>
							
							<td colspan=2 id="dn_state" class="aright">
							<div style=";margin-bottom:5px;font-size:80%">
								<span id="dn_xhtml_state" style="border:1px solid #ccc;padding:4px 12px">{$delivery_note->get('Delivery Note XHTML State')}</span>
							</div>
							</td>
						</tr>
						<tr>
							<td class="aright">{t}Packer{/t}:</td>
							<td id="assigned_packer" key="{$delivery_note->get('Delivery Note Assigned Packer Key')}" class="aright">{$delivery_note->get('Delivery Note Assigned Packer Alias')}</td>
						</tr>
						<tr>
							<td class="aright">{t}Transactions{/t}:</td>
							<td class="aright"> <span id="number_packed_transactions">{$number_packed_transactions}</span>/<span id="number_picked_transactions">{$number_picked_transactions}</span>/<span id="number_transactions">{$number_transactions}</span> <span style="margin-left:10px" id="percentage_packed">{$delivery_note->get('Fraction Packed')}</span></td>
						</tr>
						<tr>
							<td class="aright">{t}Packing Stated{/t}:</td>
							<td class="aright"><span id="start_packing_date">{$delivery_note->get('Date Start Packing')}</span></span></td>
						</tr>
						<tr>
							<td class="aright">{t}Packing Finished{/t}:</td>
							<td class="aright"><span id="finish_packing_date">{$delivery_note->get('Date Finish Packing')}</span></span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div style="clear:both">
			</div>
		</div>
		<div class="data_table" style="clear:both">
			<span id="table_title" class="clean_table_title">{t}Items{/t}</span> 
			<div id="table_type" style="display:none">
				<span id="set_pending_as_packed" style="float:right;color:brown" class="table_type state_details ">{t}Set pending as Packed{/t}</span> 
			</div>
			<div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999;margin-bottom:15px">
			</div>
			{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 } 
			<div id="table0" style="font-size:80%" class="data_table_container dtable btable">
			</div>
		</div>
	</div>
</div>

{include file='footer.tpl'} 