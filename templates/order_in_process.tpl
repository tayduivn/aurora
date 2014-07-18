{include file='header.tpl'} 
<div id="bd" class="no_padding">
	<div style="padding:0px 20px">
		{include file='assets_navigation.tpl'} 
		<input type="hidden" value="{$order->get('Order Shipping Method')}" id="order_shipping_method" />
		<input type="hidden" value="{$store->id}" id="store_id" />
		<input type="hidden" value="{$store->id}" id="store_key" />
		<input type="hidden" value="{$order->id}" id="order_key" />
		<input type="hidden" value="{$order->get('Order Current Dispatch State')}" id="dispatch_state" />
		<input type="hidden" value="{$order->get('Order Customer Key')}" id="customer_key" />
		<input type="hidden" value="{$referral}" id="referral" />
		<input type="hidden" value="{$block_view}" id="products_display_type" />
		<input type="hidden" value="{$lookup_family}" id="lookup_family" />
		<input type="hidden" id="subject" value="customer"> 
		<input type="hidden" id="subject_key" value="{$customer->id}"> 
		<input type="hidden" id="default_country_2alpha" value="{$store->get('Store Home Country Code 2 Alpha')}" />
		<input type="hidden" id="items_table_index" value="0" />
		<input type="hidden" value="{$order->get('Order Currency')}" id="currency_code" />
		<input type="hidden" value="{$decimal_point}" id="decimal_point" />
		<input type="hidden" value="{$thousands_sep}" id="thousands_sep" />
				<input type="hidden" value="{$order->get('Order Customer Key')}" id="subject_key" />
					<input type="hidden" value="customer" id="subject" />
<input type="hidden" id="to_pay_label_amount" value="{$order->get('Order To Pay Amount')}">
		
		
		<div class="branch ">
			{if $referral=='spo'} <span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>&rarr; {if $user->get_number_stores()>1}<a href="pending_orders.php">&#8704; {t}Pending Orders{/t}</a> &rarr; {/if} <a href="store_pending_orders.php?id={$store->id}">{t}Pending Orders{/t} ({$store->get('Store Code')})</a> &rarr; {$order->get('Order Public ID')} ({$order->get_formated_dispatch_state()})</span> {else} <span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>&rarr; {if $user->get_number_stores()>1}<a href="orders_server.php">&#8704; {t}Orders{/t}</a> &rarr; {/if} <a href="orders.php?store={$store->id}&view=orders">{t}Orders{/t} ({$store->get('Store Code')})</a> &rarr; {$order->get('Order Public ID')} ({$order->get_formated_dispatch_state()})</span> {/if} 
		</div>
		<div class="top_page_menu" style="border:none">
			<div class="buttons" style="float:left">
				<span class="main_title">{t}Order{/t} <span>{$order->get('Order Public ID')}</span> <span class="subtitle">({$order->get_formated_dispatch_state()})</span></span> 
			</div>
			<div class="buttons">
						<button onclick="window.open('proforma.pdf.php?id={$order->id}')"><img style="width:40px;height:12px;vertical-align:1px" src="art/pdf.gif" alt=""> {t}Proforma{/t}</button> 

				<button style="display:none;height:24px;" onclick="window.location='order.pdf.php?id={$order->id}'"><img style="width:40px;height:12px;position:relative;bottom:3px" src="art/pdf.gif" alt=""></button> <button {if $order->get('Order Current Dispatch State')!='In Process'}style="display:none"{/if} id="import_transactions_mals_e" >{t}Import{/t}</button> <button {if $order->get('Order Current Dispatch State')!='In Process by Customer'}style="display:none"{/if} onclick="window.location='order.php?id={$order->id}'" >{t}Exit Modify Order{/t}</button> <button id="cancel" class="negative">{t}Cancel Order{/t}</button> 
											<button style="{if {$order->get('Order Number Products')}==0    or $order->get('Order Current Dispatch State')!='In Process'}display:none{/if} "  id="send_to_basket"><img id="send_to_warehouse_img" src="art/icons/basket_back.png" alt=""> {t}Send to Basket{/t}</button> 

			<button class="{if {$order->get('Order Number Products')}==0}disabled{/if}" id="done"><img id="send_to_warehouse_img" src="art/icons/cart_go.png" alt=""> {t}Send to Warehouse{/t}</button> 


			</div>
			<div style="clear:both">
			</div>
		</div>
		<div style="clear:both">
		</div>
		<div id="control_panel">
			<div id="addresses">
				<h2 style="padding:0">
					<img src="art/icons/id.png" style="width:20px;position:relative;bottom:2px"> {$order->get('Order Customer Name')} <a href="customer.php?id={$order->get('order customer key')}"><span class="id">{$customer->get_formated_id()}</span></a> 
				</h2>
				
				<div style="float:left;margin:5px 20px 0 0;color:#444;font-size:90%;width:140px">
					
					<div id="title_billing_address" style="border-bottom:1px solid #ccc;margin-bottom:5px">
						{t}Billing to{/t}: 
					</div>
					
					<div  id="billing_address">
						{$order->get('Order XHTML Billing Tos')} 
					</div>
					<div class="buttons small left">
						<button id="change_billing_address" class="state_details" style="display:block;margin-top:10px">{t}Change{/t}</button> 
					</div>
				</div>
				<div style="float:left;margin:5px 0 0 0px;color:#444;font-size:90%;width:140px">
					<div id="title_delivery_address" style="border-bottom:1px solid #ccc;{if $order->get('Order For Collection')=='Yes'}display:none;{/if};margin-bottom:5px">
						{t}Delivering to{/t}: 
					</div>
					<div id="title_for_collection" style="border-bottom:1px solid #ccc;{if $order->get('Order For Collection')=='No'}display:none;{/if};margin-bottom:5px">
						<b>{t}For collection{/t}</b> 
					</div>
					<div class="address_box" id="delivery_address">
						{$order->get('Order XHTML Ship Tos')} 
					</div>
					<div id="shipping_address" style="{if $order->get('Order For Collection')=='Yes'}display:none{/if};margin-top:10px" class="buttons left small">
						<button id="change_delivery_address">{t}Change{/t}</button> <br />
						<button style="margin-top:3px;{if $store->get('Store Can Collect')=='No'}display:none{/if}" id="set_for_collection" onclick="change_shipping_type('Yes')">{t}Set for collection{/t}</button> 
					</div>
					<div id="for_collection" style="{if $order->get('Order For Collection')=='No'}display:none;{/if};margin-top:10px" class="buttons left small">
						<button id="set_for_shipping" class="state_details" onclick="change_shipping_type('No')">{t}Set for delivery{/t}</button> 
					</div>
				</div>
			</div>
			<div id="totals">
				<table border="0" style="width:100%;border-top:1px solid #333;border-bottom:1px solid #333;width:100%,padding:0;margin:0;float:right;margin-left:0px">
					<tr {if $order->
						get('Order Items Discount Amount')==0 }style="display:none"{/if} id="tr_order_items_gross" > 
						<td class="aright">{t}Items Gross{/t}</td>
						<td width="100" class="aright" id="order_items_gross">{$order->get('Items Gross Amount')}</td>
					</tr>
					<tr {if $order->
						get('Order Items Discount Amount')==0 }style="display:none"{/if} id="tr_order_items_discounts" > 
						<td class="aright">{t}Discounts{/t}</td>
						<td width="100" class="aright">-<span id="order_items_discount">{$order->get('Items Discount Amount')}</span></td>
					</tr>
					<tr>
						<td class="aright">{t}Items Net{/t}</td>
						<td width="100" class="aright" id="order_items_net">{$order->get('Items Net Amount')}</td>
					</tr>
					<tr id="tr_order_credits" {if $order->
						get('Order Net Credited Amount')==0}style="display:none"{/if}> 
						<td class="aright"><img style="visibility:hidden;cursor:pointer" src="art/icons/edit.gif" id="edit_button_credits" /> {t}Credits{/t}</td>
						<td width="100" class="aright" id="order_credits">{$order->get('Net Credited Amount')}</td>
					</tr>
					<tr id="tr_order_items_charges">
						<td class="aright"><img style="visibility:hidden;cursor:pointer" src="art/icons/edit.gif" id="edit_button_items_charges" /> {t}Charges{/t}</td>
						<td id="order_charges" width="100" class="aright">{$charges_deal_info}{$order->get('Charges Net Amount')}</td>
					</tr>
					<tr id="tr_order_shipping">
						<td class="aright"> <img style="{if $order->get('Order Shipping Method')=='On Demand'}visibility:visible{else}visibility:hidden{/if};cursor:pointer" src="art/icons/edit.gif" id="edit_button_shipping" /> {t}Shipping{/t}</td>
						<td id="order_shipping" width="100" class="aright">{$order->get('Shipping Net Amount')}</td>
					</tr>
					<tr {if $order->
						get('Order Insurance Net Amount')==0 }style="display:none"{/if} id="tr_order_insurance" > 
						<td class="aright"> {t}Insurance{/t}</td>
						<td id="order_insurance" width="100" class="aright">{$order->get('Insurance Net Amount')}</td>
					</tr>
					<tr style="border-top:1px solid #777">
						<td class="aright">{t}Net{/t}</td>
						<td id="order_net" width="100" class="aright">{$order->get('Balance Net Amount')}</td>
					</tr>
					<tr id="tr_order_tax" style="border-bottom:1px solid #777">
						<td class="aright"><img style="visibility:hidden;cursor:pointer" src="art/icons/edit.gif" id="edit_button_tax" /> <span id="tax_info">{$order->get_formated_tax_info_with_operations()}</span></td>
						<td id="order_tax" width="100" class="aright">{$order->get('Balance Tax Amount')}</td>
					</tr>
					<tr style="border-bottom:1px solid #777">
						<td class="aright">{t}Total{/t}</td>
						<td id="order_total" width="100" class="aright" style="font-weight:800">{$order->get('Balance Total Amount')}</td>
					</tr>
					<tr id="tr_order_total_paid" style="border-top:1px solid #777;">
						<td class="aright"> <img style="display:none" id="order_paid_info" src="art/icons/information.png" title="{$order->get('Order Current XHTML Payment State')}"> {t}Paid{/t}</td>
						<td id="order_total_paid" width="100" class="aright">{$order->get('Payments Amount')}</td>
					</tr>
					<tr id="tr_order_total_to_pay" style="{if $order->get('Order To Pay Amount')==0}display:none{/if}">
						<td class="aright"> 
						<div class="buttons small left">
							<button style="{if $order->get('Order To Pay Amount')<0}display:none{/if}" id="show_add_payment_to_order" amount="{$order->get('Order To Pay Amount')}" onclick="add_payment('order','{$order->id}')"><img src="art/icons/add.png"> {t}Payment{/t}</button> 
							<button style="{if $order->get('Order To Pay Amount')>0}display:none{/if}" id="show_add_credit_note_to_customer" amount="{$order->get('Order To Pay Amount')}" onclick="add_credit_note_to_customer('order','{$order->id}')"><img src="art/icons/add.png"> {t}Credit{/t}</button> 
						</div>
						{t}To Pay{/t}</td>
						<td id="order_total_to_pay" width="100" class="aright" style="font-weight:800">{$order->get('To Pay Amount')}</td>
					</tr>
				</table>
				<div class="buttons small" style="display:none;{if $has_credit}display:none;{/if}clear:both;margin:0px;padding-top:10px">
					<button id="add_credit" style="margin:0px;">{t}Add debit/credit{/t}</button> 
				</div>
				<div style="clear:both">
			</div>
			</div>
			<div id="dates">
				{if $order->get_notes()} 
				<div class="notes" style="border:1px solid #ccc;padding:5px;margin-bottom:5px">
					{$order->get_notes()} 
				</div>
				{/if} 
				<table border="0" class="info_block">
					<tr>
						<td>{t}Created{/t}:</td>
						<td class="aright">{$order->get('Created Date')}</td>
					</tr>
					{if $order->get('Order Current Dispatch State')=='In Process by Customer' } 
					<tr>
						<td>{t}Last updated{/t}:</td>
						<td class="aright">{$order->get('Last Updated Date')}</td>
					</tr>
					<tr style="border-top:1px solid #ccc">
						<td>{t}On website{/t}:</td>
						<td class="aright">{$order->get('Interval Last Updated Date')}</td>
					</tr>
					{elseif $order->get('Order Current Dispatch State')=='Waiting for Payment Confirmation'} 
					<tr>
						<td>{t}Submit Payment{/t}:</td>
						<td class="aright">{$order->get('Checkout Submitted Payment Date')}</td>
					</tr>
					{else} 
					<tr>
						<td>{t}Last updated{/t}:</td>
						<td class="aright">{$order->get('Last Updated Date')}</td>
					</tr>
					{/if} 
				</table>
			</div>
			
			
			
			
			<div style="clear:both">
			</div>
			<img id="show_order_details" style="cursor:pointer" src="art/icons/arrow_sans_lowerleft.png" /> 
			
			
			
			
			<div id="order_details_panel" style="display:none;border-top:1px solid #ccc;padding-top:10px;margin-top:10px">
				<div class="buttons small right">
				<button style="{if $order->get('Order Apply Auto Customer Account Payment')=='No'}display:none{/if}" onclick="update_auto_account_payments('No')" >{t}Don't add account credits{/t}</button>
				<button style="{if $order->get('Order Apply Auto Customer Account Payment')=='Yes'}display:none{/if}" onclick="update_auto_account_payments('Yes')" >{t}Add account credits{/t}</button>
			</div>
				
				<div style="width:450px">
					<table border="0" class="info_block">
					<tr>
						<td>{t}Created{/t}:</td>
						<td class="aright">{$order->get('Created Date')}</td>
					</tr>
					{if $order->get('Order Current Dispatch State')=='In Process by Customer' } 
					<tr>
						<td>{t}Last updated{/t}:</td>
						<td class="aright">{$order->get('Last Updated Date')}</td>
					</tr>
					<tr style="border-top:1px solid #ccc">
						<td>{t}On website{/t}:</td>
						<td class="aright">{$order->get('Interval Last Updated Date')}</td>
					</tr>
					{elseif $order->get('Order Current Dispatch State')=='Waiting for Payment Confirmation'} 
					<tr>
						<td>{t}Submit Payment{/t}:</td>
						<td class="aright">{$order->get('Checkout Submitted Payment Date')}</td>
					</tr>
					{else} 
					<tr>
						<td>{t}Last updated{/t}:</td>
						<td class="aright">{$order->get('Last Updated Date')}</td>
						<td class="aright">{$order->get('Interval Last Updated Date')}</td>
					</tr>
					{/if} 
				</table>
				
				<table border="0" class="info_block">
				<tr>
						<td>{t}Customer Fiscal Name{/t}:</td>
						<td class="aright">{$order->get('Order Customer Fiscal Name')}</td>
					</tr>
					<tr>
						<td>{t}Customer Name{/t}:</td>
						<td class="aright">{$order->get('Order Customer Name')}</td>
					</tr>
						<tr>
						<td>{t}Contact Name{/t}:</td>
						<td class="aright">{$order->get('Order Customer Contact Name')}</td>
					</tr>
						<tr>
						<td>{t}Telephone{/t}:</td>
						<td class="aright">{$order->get('Order Telephone')}</td>
					</tr>
						<tr>
						<td>{t}Email{/t}:</td>
						<td class="aright">{$order->get('Order Email')}</td>
					</tr>
				</table>
				
				
				<table border="0" class="info_block">
				<tr>
						<td>{t}Tax Code{/t}:</td>
						<td class="aright">{$order->get('Order Tax Code')}  {$order->get('Tax Rate')} </td>
					</tr>
					<tr>
						<td>{t}Tax Info{/t}:</td>
						<td class="aright">{$order->get('Order Tax Name')}</td>
				</tr>
				</table>
				
					<table border="0" class="info_block">
				<tr>
						<td>{t}Weight {/t}:</td>
						<td class="aright">{$order->get('Weight')}</td>
					</tr>
					
				</table>
				
				</div>
				<div style="clear:both">
				</div>
				<img id="hide_order_details" style="cursor:pointer;position:relative;top:5px" src="art/icons/arrow_sans_topleft.png" /> 
			</div>
			
			
			<div style="clear:both">
			</div>
		</div>
	</div>
	{include file='order_payments_splinter.tpl'} 
	<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:10px">
		<li> <span class="item {if $block_view=='items'}selected{/if}" id="items"> <span> {t}Order Items{/t} (<span style="display:inline;padding:0px" id="ordered_products_number">{$order->get('Number Products')}</span>)</span></span></li>
		<li> <span class="item {if $block_view=='products'}selected{/if}" id="products"> <span> {t}Products{/t} (<span style="display:inline;padding:0px" id="all_products_number">{$store->get_formated_products_for_sale()}</span>)</span></span></li>
	</ul>
	<div class="tabs_base">
	</div>
	<div style="padding:0px 20px">
		<div class="data_table" style="clear:both;margin-top:15px;{if $block_view!='items'}display:none{/if}" id="items_block">
			<span id="table_title_items" class="clean_table_title">{t}Items{/t}</span> 
			<div class="table_top_bar space">
			</div>
			<div id="list_options0" style="display:none">
				<table style="float:left;margin:0 0 5px 0px ;padding:0" class="options">
					<tr>
						<td class="{if $items_view=='general'}selected{/if}" id="general">{t}General{/t}</td>
						<td class="{if $items_view=='stock'}selected{/if}" id="stock">{t}Discounts{/t}</td>
						<td class="{if $items_view=='sales'}selected{/if}" id="sales">{t}Properties{/t}</td>
					</tr>
				</table>
				<table id="period_options" style="float:left;margin:0 0 0 20px ;padding:0{if $items_view!='sales' };display:none{/if}" class="options_mini">
					<tr>
						<td class="{if $items_period=='all'}selected{/if}" period="all" id="period_all">{t}All{/t}</td>
						<td class="{if $items_period=='year'}selected{/if}" period="year" id="period_year">{t}1Yr{/t}</td>
						<td class="{if $items_period=='quarter'}selected{/if}" period="quarter" id="period_quarter">{t}1Qtr{/t}</td>
						<td class="{if $items_period=='month'}selected{/if}" period="month" id="period_month">{t}1M{/t}</td>
						<td class="{if $items_period=='week'}selected{/if}" period="week" id="period_week">{t}1W{/t}</td>
					</tr>
				</table>
			</div>
			{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 } 
			<div id="table0" style="font-size:90%" class="data_table_container dtable btable">
			</div>
		</div>
		<div class="data_table" style="clear:both;margin-top:15px;{if $block_view!='products'}display:none{/if}" id="products_block">
			<span id="table_title_products" class="clean_table_title">{t}Products{/t}</span> 
			<div id="products_lookups">
				<div class="buttons small">
					<button id="lookup_family" onclick="lookup_family()">{t}Lookup Family{/t}</button> 
					<input id="lookup_family_query" style="width:100px;float:right" value="{$lookup_family}" />
					<span id="clear_lookup_family" onclick="clear_lookup_family()" style="cursor:pointer;float:right;margin-right:5px;color:#777;font-style:italic;font-size:80%;{if $lookup_family==''}display:none{/if}">{t}Clear{/t}</span> <button id="show_all_products" onclick="clear_lookup_family()" style="margin-right:50px">{t}Display all products{/t}</button> 
				</div>
			</div>
			<div class="table_top_bar space">
			</div>
			<div id="list_options1" style="display:none">
				<table style="float:left;margin:0 0 5px 0px ;padding:0" class="options">
					<tr>
						<td class="{if $products_view=='general'}selected{/if}" id="general">{t}General{/t}</td>
						<td class="{if $products_view=='stock'}selected{/if}" id="stock">{t}Discounts{/t}</td>
						<td class="{if $products_view=='sales'}selected{/if}" id="sales">{t}Properties{/t}</td>
					</tr>
				</table>
				<table id="period_options" style="float:left;margin:0 0 0 20px ;padding:0{if $products_view!='sales' };display:none{/if}" class="options_mini">
					<tr>
						<td class="{if $products_period=='all'}selected{/if}" period="all" id="period_all">{t}All{/t}</td>
						<td class="{if $products_period=='year'}selected{/if}" period="year" id="period_year">{t}1Yr{/t}</td>
						<td class="{if $products_period=='quarter'}selected{/if}" period="quarter" id="period_quarter">{t}1Qtr{/t}</td>
						<td class="{if $products_period=='month'}selected{/if}" period="month" id="period_month">{t}1M{/t}</td>
						<td class="{if $products_period=='week'}selected{/if}" period="week" id="period_week">{t}1W{/t}</td>
					</tr>
				</table>
			</div>
			{include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1 } 
			<div id="table1" style="font-size:90%" class="data_table_container dtable btable">
			</div>
			
		</div>
		<div id="order_deal_bonus" style="font-size:85%;clear:both;padding-top:5px;{if !$order->has_deal_with_bonus() }display:none{/if};border:0px solid red">
			{include file='order_deal_bonus_splinter.tpl' order=$order} 
	</div>
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
<div id="dialog_import_transactions_mals_e" style="border:1px solid #ccc;text-align:left;padding:10px">
	<div id="import_transactions_mals_e_msg">
	</div>
	<table style="margin:10px" border="1">
		<tr>
			<td> 
			<div class="buttons small left">
				<button class="selected">CSV</button><button>TSV</button> 
			</div>
			</td>
		</tr>
		<tr>
			<td style="padding-top:10px;width:300px;"> <textarea style="width:100%;height:200px" id="transactions_mals_e"></textarea> </td>
		</tr>
		<tr>
			<td style="padding-top:10px"> 
			<div class="buttons">
				<button id="save_import_transactions_mals_e">{t}Import{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>
{include file='add_payment_splinter.tpl'}
{include file='order_not_dispatched_dialogs_splinter.tpl'} 
{include file='footer.tpl'} 