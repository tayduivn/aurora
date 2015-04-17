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
		<input type="hidden" value="{$products_display_type}" id="products_display_type" />
		<input type="hidden" value="{$order->get('Order Currency')}" id="currency_code" />
		<input type="hidden" value="{$decimal_point}" id="decimal_point" />
		<input type="hidden" value="{$thousands_sep}" id="thousands_sep" />
		<input type="hidden" value="{$order->get('Order Customer Key')}" id="subject_key" />
		<input type="hidden" value="customer" id="subject" />
		<input type="hidden" id="to_pay_label_amount" value="{$order->get('Order To Pay Amount')}"> 
		<div class="branch ">
			<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>&rarr; {if $referral=='spo'} {if $user->get_number_stores()>1}<a href="pending_orders.php">&#8704; {t}Pending Orders{/t}</a> &rarr; {/if} <a href="store_pending_orders.php?id={$store->id}">{t}Pending Orders{/t} ({$store->get('Store Code')})</a> {else if $referral=='po'} {if $user->get_number_stores()>1}<a href="pending_orders.php">&#8704; {t}Pending Orders{/t}</a> {/if} {else}{if $user->get_number_stores()>1}<a href="orders_server.php">&#8704; {t}Orders{/t}</a> &rarr; {/if} <a href="orders.php?store={$store->id}&view=orders">{t}Orders{/t} ({$store->get('Store Code')})</a> {/if} &rarr; {$order->get('Order Public ID')}</span> 
		</div>
		<div class="top_page_menu" style="border:none">
			<div class="buttons" style="float:left">
				<span class="main_title" style="position:relative;top:3px">{t}Amending Order{/t} <class>{$order->get('Order Public ID')}</span> ({$order->get_formated_dispatch_state()})</span> 
			</div>
			<div class="buttons small" style="position:relative;top:5px">
				{*} <button style="height:24px;" onclick="window.location='order.pdf.php?id={$order->id}'"><img style="width:40px;height:12px;position:relative;bottom:3px" src="art/pdf.gif" alt=""></button> {*} 
				<button id="exit_modify_order">{t}Exit Modify Order{/t}</button> <button style="display:none" id="cancel" class="negative">{t}Cancel Order{/t}</button> 
							<button id="sticky_note_button"><img src="art/icons/note_pink.png" alt=""> {t}Note{/t}</button> 

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
				<h3>
					{$customer->get('Customer Main Contact Name')} 
				</h3>
				<div style="float:left;margin:5px 20px 0 0;color:#444;font-size:90%;width:140px">
					<span style="font-weight:500;color:#000">{t}Billing Address{/t}</span>: 
					<div style="margin-top:5px" id="billing_address">
						{$order->get('Order XHTML Billing Tos')} 
					</div>
					<div class="buttons small left" style="{if $order->get('Order Invoiced')=='Yes'}display:none{/if}">
						<button id="change_billing_address" class="state_details" style="display:block;margin-top:10px">{t}Change{/t}</button> 
					</div>
				</div>
				<div style="float:left;margin:5px 0 0 0px;color:#444;font-size:90%;width:140px">
					<div id="title_delivery_address" style="{if $order->get('Order For Collection')=='Yes'}display:none;{/if};margin-bottom:5px">
						{t}Delivery Address{/t}: 
					</div>
					<div id="title_for_collection" style="{if $order->get('Order For Collection')=='No'}display:none;{/if};margin-bottom:5px">
						<b>{t}For collection{/t}</b> 
					</div>
					<div class="address_box" id="delivery_address">
						{$order->get('Order XHTML Ship Tos')} 
					</div>
					<div id="shipping_address" style="{if $order->get('Order For Collection')=='Yes' or $order->get('Order Invoiced')=='Yes'}display:none{/if};margin-top:2px" class="buttons left small">
						<button id="change_delivery_address">{t}Change{/t}</button> <br />
						<button style="margin-top:3px;{if $store->get('Store Can Collect')=='No'}display:none{/if}" id="set_for_collection" onclick="change_shipping_type('Yes')">{t}Set for collection{/t}</button> 
					</div>
					<div id="for_collection" style="{if $order->get('Order For Collection')=='No' or $order->get('Order Invoiced')=='Yes'}display:none;{/if};margin-top:2px" class="buttons left small">
						<button id="set_for_shipping" class="state_details" onclick="change_shipping_type('No')">{t}Set for delivery{/t}</button> 
					</div>
				</div>
			</div>
			<div id="totals">
							{include file='order_totals_splinter.tpl'} {include file='order_sticky_note_splinter.tpl'} 
			</div>
			<div id="dates">
				{if $order->get_notes()} 
				<div class="notes" style="border:1px solid #ccc;padding:5px;margin-bottom:5px">
					{$order->get_notes()} 
				</div>
				{/if} 
				<table border="0" class="info_block">
					<tr>
						<td>{t}Order Date{/t}:</td>
						<td class="aright">{$order->get('Date')}</td>
					</tr>
					<tr>
						<td>{t}Payment Method{/t}:</td>
						<td class="aright">{$order->get_formated_payment_state()}</td>
					</tr>
				</table>
				
				<div id="deals_div" style="clear:both">
						{include file='order_deals_splinter.tpl'} 

					</div>
					<div id="vouchers_div">
						{include file='order_vouchers_splinter.tpl' modify_voucher=false} 
					</div>
				
			</div>
			<div style="clear:both">
			</div>
		</div>
	</div>
	<div id="payments_list">
		{include file='order_payments_splinter.tpl'} 
	</div>
	<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:10px">
		<li> <span class="item {if $block_view=='items'}selected{/if}" id="items"> <span> {t}Order Items{/t} (<span style="display:inline;padding:0px" id="ordered_products_number">{$order->get('Number Items')}</span>)</span></span></li>
		<li> <span class="item {if $block_view=='products'}selected{/if}" id="products"> <span> {t}Products{/t} (<span style="display:inline;padding:0px" id="all_products_number">{$store->get_formated_products_for_sale()}</span>)</span></span></li>
	</ul>
	<div class="tabs_base">
	</div>
	<div style="padding:0px 20px">
		<div class="data_table" style="clear:both;margin-top:15px;{if $block_view!='items'}display:none{/if}" id="items_block">
			<span id="table_title_items" class="clean_table_title" ">{t}Items{/t}</span> 
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
					<span id="clear_lookup_family" onclick="clear_lookup_family()" style="cursor:pointer;float:right;margin-right:5px;color:#777;font-style:italic;font-size:80%;{if $lookup_family==''}display:none{/if}">{t}Clear{/t}</span> <button id="show_all_products" onclick="show_all_products()" style="margin-right:50px">{t}Display all products{/t}</button> 
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
				<table id="period_options" style="float:left;margin:0 0 0 20px ;padding:0{if $view!='sales' };display:none{/if}" class="options_mini">
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
	<table style="margin:10px" border="0">
		<tr>
			<td style="padding-top:10px">{t}Copy and paste the Emals-e email here{/t}:</td>
		</tr>
		<tr>
			<td style="padding-top:10px"> <textarea style="width:100%" id="transactions_mals_e"></textarea> </td>
		</tr>
		<tr>
			<td style="padding-top:10px"> <button style="cursor:pointer" id="save_import_transactions_mals_e">{t}Import{/t}</button> </td>
		</tr>
	</table>
</div>
{include file='add_payment_splinter.tpl' subject='order'} {include file='order_not_dispatched_dialogs_splinter.tpl'} {include file='footer.tpl'} 