{include file='header.tpl'} 
<div id="bd" class="{if $part->get('Part Available')=='No' or $part->get('Part Status')=='Not In Use' }discontinued{/if}" style="padding:0;">
	<input type="hidden" id="part_sku" value="{$part->sku}" />
	<input type="hidden" id="page_name" value="part" />
	<input type="hidden" id="part_location" value="" />
	<input type="hidden" id="link_extra_argument" value="&sku={$part->sku}" />
	<input type="hidden" id="from" value="{$from}" />
	<input type="hidden" id="to" value="{$to}" />
	<input type="hidden" id="history_table_id" value="3"> 
	<input type="hidden" id="subject" value="part"> 
	<input type="hidden" id="subject_key" value="{$part->sku}"> 
	<input type="hidden" id="barcode_data" value="{$part->get_barcode_data()}"> 
	<input type="hidden" id="barcode_type" value="{$part->get('Part Barcode Type')}"> 
					<input type="hidden" id="calendar_id" value="sales" />

	<div style="padding: 0 20px;">
		<input type="hidden" id="modify_stock" value="{$modify_stock}" />
		{include file='locations_navigation.tpl'} 
		<div class="branch">
			<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>&rarr; {if $user->get_number_warehouses()>1}<a href="warehouses.php">{t}Warehouses{/t}</a> &rarr; {/if}<a href="inventory.php?warehouse_id={$warehouse->id}">{t}Inventory{/t}</a> &rarr; {$part->get_sku()}</span> 
		</div>
		<div class="top_page_menu">
			<div class="buttons" style="float:right">
				{if isset($next) }<img class="next" onmouseover="this.src='art/next_button.gif'" onmouseout="this.src='art/next_button.png'" title="{$next.title}" onclick="window.location='{$next.link}'" src="art/next_button.png" alt="{t}Next{/t}" />{/if} {if $modify } <button onclick="window.location='edit_part.php?sku={$part->sku}'"><img src="art/icons/cog.png" alt=""> {t}Edit Part{/t}</button> {/if} 
			</div>
			<div class="buttons" style="float:left;">
				{if isset($prev)}<img style="vertical-align:bottom;float:none" class="previous" onmouseover="this.src='art/previous_button.gif'" onmouseout="this.src='art/previous_button.png'" title="{$prev.title}" onclick="window.location='{$prev.link}'" src="art/previous_button.png" alt="{t}Previous{/t}" />{/if}<span class="main_title"><img src="art/icons/part.png" style="height:18px;position:relative;bottom:2px" /> <span style="font-weight:800"><span class="id">{$part->get_sku()}</span></span> {if $part->get('Part Reference')!=''}<span style="font-weight:600">{$part->get('Part Reference')}</span>, {/if} {$part->get('Part Unit Description')} </span> 
			</div>
			<div style="clear:both">
			</div>
		</div>
		<div id="block_info" style="margin-top:10px;width:930px;">
			<div style="float:right;width:105px;">
				<div class="buttons small">
					<button id="attach" style="width:105px;margin:0"><img src="art/icons/add.png" alt=""> {t}Attachment{/t}</button> <button id="note" style="width:105px;margin:0;margin-top:7px"><img src="art/icons/add.png" alt=""> {t}History Note{/t}</button> <button id="sticky_note_button" style="width:105px;margin:0;margin-top:7px"><img src="art/icons/note.png" alt=""> {t}Note{/t}</button> 
				</div>
				<div id="sticky_note_div" class="sticky_note" style="margin:0px;margin-top:10px;margin-right:5px;width:105px">
					<img id="sticky_note_bis" style="float:right;cursor:pointer" src="art/icons/edit.gif"> 
					<div id="sticky_note_content" style="padding:5px 5px 5px 10px;font-size:75%">
						{$sticky_note} 
					</div>
				</div>
			</div>
			<div id="photo_container" style="float:left">
				<div style="width:220px;">
					<div id="barcode" style="margin:auto;">
					</div>
				</div>
				<div style="border:1px solid #ddd;padding-stop:0;width:220px;xheight:230px;text-align:center;margin:0 10px 0 0px">
					<div id="imagediv" style="border:1px solid #ddd;width:190px;;padding:5px 5px;xborder:none;cursor:pointer;xbackground:red;margin: 10px 0 10px 9px;vertical-align:middle">
						<img id="main_image" src="{$part->get('Part Main Image')}" style="vertical-align:middle;display:block;;width:190px;;margin:0px auto" valign="center" border="1" id="image" alt="{t}Image{/t}" /> 
					</div>
				</div>
				<div style="width:160px;margin:auto;padding-top:5px">
					{foreach from=$part->get_images_slidesshow() item=image name=foo} {if $image.is_principal==0} <img style="float:left;border:1px solid#ccc;padding:2px;margin:2px;cursor:pointer" src="{$image.thumbnail_url}" title="" alt="" /> {/if} {/foreach} 
				</div>
			</div>
			<div style="width:280px;float:left;margin-left:5px">
				<table class="show_info_product">
					{foreach from=$part->get_categories() item=category name=foo } 
					<tr>
						<td>{if $smarty.foreach.foo.first}{t}Category{/t}:{/if}</td>
						<td><a href="part_category.php?id={$category.category_key}">{$category.category_label}</a></td>
					</tr>
					{/foreach} 
				</table>
				{t}Products{/t}: 
				<table border="0" id="products" class="show_info_product" style=";margin-top:0px">
					{foreach from=$part->get_current_products() item=product name=foo } 
					<tr id="product_tr_{$product.ProductID}">
						<td><a href="store.php?id={$product.StoreKey}">{$product.StoreCode} </a> </td>
						<td><a href="product.php?pid={$product.ProductID}">{$product.ProductCode} </a> </td>
						<td style="text-align:center" id="product_web_state_{$product.ProductID}"> {if $product.ProductNumberWebPages==0} <img src="art/icons/world_light_bw.png" title="{t}Not in website{/t}" /> {elseif $product.ProductWebState=='For Sale'} 
						<div style="position:relative">
							<img class="icon" src="art/icons/world.png" /> {if $product.ProductNumberWebPages>1} <span style="position:absolute;left:16px;top:6px;font-size:8px;background:red;color:white;padding:1px 1.7px 1px 2.2px;opacity:0.8;border-radius:30%">3</span> {/if} 
						</div>
						{else if $product.ProductWebState=='Out of Stock'}<img src="art/icons/no_stock.jpg" /> {else}<img src="art/icons/sold_out.gif" />{/if} </td>
						<td style="text-align:right;padding-right:10px"> <span style="cursor:pointer" id="product_web_configuration_{$product.ProductID}" onclick="change_web_configuration(this,{$product.ProductID})">{if $product.ProductWebConfiguration=='Online Auto'}{t}Automatic{/t}{elseif $product.ProductWebConfiguration=='Offline'}<img src="art/icons/police_hat.jpg" style="height:18px" /> {t}Offline{/t} {elseif $product.ProductWebConfiguration=='Online Force Out of Stock'}<img src="art/icons/police_hat.jpg" style="height:18px" /> {t}Out of stock{/t} {elseif $product.ProductWebConfiguration=='Online Force For Sale'}<img src="art/icons/police_hat.jpg" style="height:18px" /> {t}Online{/t} {/if} </span> </td>
					</tr>
					{/foreach} 
				</table>
			</div>
			{if $part->get('Part Status')=='In Use'} 
			<div style="width:280px;float:left;margin-left:15px">
				<table class="show_info_product" style="width:270px">
					<tr>
						<td>{t}Stock{/t}: <span>({$part->get_unit($part->get('Part Current On Hand Stock'))})</span></td>
						<td class="stock aright" id="stock">{$part->get('Part Current On Hand Stock')}</td>
					</tr>
					<tr>
						<td class="aright" colspan="2" style="padding-top:0;color:#777;font-size:90%"> <b id="current_stock">{$part->get('Part Current Stock')}</b> <b>-[<span id="current_stock_picked">{$part->get('Part Current Stock Picked')}</span>]</b> -(<span id="current_stock_in_process">{$part->get('Part Current Stock In Process')}</span>) &rarr; <span id="current_stock_available">{$part->get('Current Stock Available')}</span></td>
					</tr>
					<tbody style="font-size:80%">
						<tr>
							<td>{t}Value at Cost{/t}:</td>
							<td class="aright" id="value_at_cost">{$part->get_current_formated_value_at_cost()}</td>
						</tr>
						<tr>
							<td>{t}Value at Current Cost{/t}:</td>
							<td class="aright" id="value_at_current_cost">{$part->get_current_formated_value_at_current_cost()}</td>
						</tr>
						<tr>
							<td>{t}Commercial Value{/t}:</td>
							<td class="aright" id="commercial_value">{$part->get_current_formated_commercial_value()}</td>
						</tr>
					</tbody>
					<tr>
						<td style="{if $part->get('Part XHTML Available For Forecast')==''}display:none{/if}">{t}Available for{/t}:</td>
						<td class="stock aright">{$part->get('Part XHTML Available For Forecast')}</td>
					</tr>
					{foreach from=$part->get_next_shipments() item=shipments } 
					<tr>
						<td rowspan="2">{t}Next shipment{/t}:</td>
						<td>{$data.next_buy}</td>
					</tr>
					<tr>
						<td class="noborder">{$data.nextbuy_when}</td>
					</tr>
					{/foreach} 
				</table>
				{t}Locations{/t}: 
				<table border="0" id="part_locations" class="show_info_product" style="width:270px;margin-top:0px">
					{foreach from=$part->get_locations(true) item=location_data name=foo } 
					<tr id="part_location_tr_{$location_data.PartSKU}_{$location_data.LocationKey}">
						<td><a href="location.php?id={$location_data.LocationKey}">{$location_data.LocationCode} </a> <img style="{if $modify_stock}cursor:pointer{/if}" sku_formated="{$part->get_sku()}" location="{$location_data.LocationCode}" id="part_location_can_pick_{$location_data.PartSKU}_{$location_data.LocationKey}" can_pick="{if $location_data.CanPick=='Yes'}No{else}Yes{/if}" src="{if $location_data.CanPick=='Yes'}art/icons/basket.png{else}art/icons/box.png{/if}" alt="can_pick" onclick="save_can_pick({$location_data.PartSKU},{$location_data.LocationKey})" /> </td>
						<td id="picking_limit_quantities_{$location_data.PartSKU}_{$location_data.LocationKey}" min_value='{if isset($location_data.MinimumQuantity)}{$location_data.MinimumQuantity}{/if}' max_value='{if isset($location_data.MaximumQuantity)}{$location_data.MaximumQuantity}{/if}' location_key='{$location_data.LocationKey}' style="cursor:pointer; color:#808080;{if $location_data.CanPick =='No'}display:none{/if}" onclick="show_picking_limit_quantities(this)"> {literal}{{/literal}<span id="picking_limit_min_{$location_data.PartSKU}_{$location_data.LocationKey}">{if isset($location_data.MinimumQuantity)}{$location_data.MinimumQuantity}{else}?{/if}</span>,<span id="picking_limit_max_{$location_data.PartSKU}_{$location_data.LocationKey}">{if isset($location_data.MaximumQuantity)}{$location_data.MaximumQuantity}{else}?{/if}</span>{literal}}{/literal} </td>
						<td id="store_limit_quantities_{$location_data.PartSKU}_{$location_data.LocationKey}" move_qty='{if isset($location_data.MovingQuantity)}{$location_data.MovingQuantity}{/if}' location_key='{$location_data.LocationKey}' style="cursor:pointer; color:#808080;{if $location_data.CanPick =='Yes'}display:none{/if}" onclick="show_move_quantities(this)"> [<span id="store_limit_move_qty_{$location_data.PartSKU}_{$location_data.LocationKey}">{if isset($location_data.MovingQuantity)}{$location_data.MovingQuantity}{else}?{/if}</span>] </td>
						<td class="quantity" id="part_location_quantity_{$location_data.PartSKU}_{$location_data.LocationKey}" quantity="{$location_data.QuantityOnHand}">{$location_data.FormatedQuantityOnHand}</td>
						<td style="{if !$modify_stock}display:none{/if}" class="button"><img style="cursor:pointer" id="part_location_audit_{$location_data.PartSKU}_{$location_data.LocationKey}" src="art/icons/note_edit.png" title="{t}audit{/t}" alt="{t}audit{/t}" onclick="audit({$location_data.PartSKU},{$location_data.LocationKey})" /></td>
						<td style="{if !$modify_stock}display:none{/if}" class="button"> <img style="cursor:pointer" sku_formated="{$part->get_sku()}" location="{$location_data.LocationCode}" id="part_location_add_stock_{$location_data.PartSKU}_{$location_data.LocationKey}" src="art/icons/lorry.png" title="{t}add stock{/t}" alt="{t}add stock{/t}" onclick="add_stock_part_location({$location_data.PartSKU},{$location_data.LocationKey})" /></td>
						<td style="{if !$modify_stock}display:none{/if}" class="button"> <img style="{if $location_data.QuantityOnHand!=0}display:none;{/if}cursor:pointer" sku_formated="{$part->get_sku()}" location="{$location_data.LocationCode}" id="part_location_delete_{$location_data.PartSKU}_{$location_data.LocationKey}" src="art/icons/cross_bw.png" title="{t}delete{/t}" alt="{t}delete{/t}" onclick="delete_part_location({$location_data.PartSKU},{$location_data.LocationKey})" /><img style="{if $location_data.QuantityOnHand==0}display:none;{/if}cursor:pointer" id="part_location_lost_items_{$location_data.PartSKU}_{$location_data.LocationKey}" src="art/icons/package_delete.png" title="{t}lost{/t}" alt="{t}lost{/t}" onclick="lost({$location_data.PartSKU},{$location_data.LocationKey})" /></td>
						<td style="{if !$modify_stock}display:none{/if}" class="button"><img style="cursor:pointer" sku_formated="{$part->get_sku()}" location="{$location_data.LocationCode}" id="part_location_move_items_{$location_data.PartSKU}_{$location_data.LocationKey}" src="art/icons/package_go.png" title="{t}move{/t}" alt="{t}move{/t}" onclick="move({$location_data.PartSKU},{$location_data.LocationKey})" /></td>
					</tr>
					{/foreach} 
					<tr style="{if !$modify_stock}display:none{/if}">
						<td colspan="7"> 
						<div id="add_location_button" class="buttons small left">
							<button onclick="add_location({$part->sku})">{t}Add Location{/t}</button> 
						</div>
						</td>
					</tr>
				</table>
			</div>
			{else} 
			<div style="width:280px;float:left;margin-left:15px">
				<table class="show_info_product " style="margin:0;padding:5px 10px;width:100%;">
					<tr>
						<td colspan="2" class="discontinued" style="font-weight:800;font-size:160%;text-align:center">{t}No longer keeped in Warehouse{/t}</td>
					</tr>
					<tr>
						<td>{t}Discontinued{/t}:</td>
						<td>{$part->get('Valid To')}</td>
					</tr>
				</table>
			</div>
			{/if} 
			<div style="clear:both">
			</div>
		</div>
	</div>
	<div style="clear:both">
	</div>
	<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:15px">
		<li><span class="item {if $view=='description'}selected{/if}" id="description"> <span> {t}Description{/t}</span></span></li>
		<li><span class="item {if $view=='notes'}selected{/if}" id="notes"> <span> {t}History/Notes{/t}</span></span></li>
		<li><span class="item {if $view=='sales'}selected{/if}" id="sales"> <span> {t}Sales{/t}</span></span></li>
		<li><span class="item {if $view=='transactions'}selected{/if}" id="transactions"> <span> {t}Stock Transactions{/t}</span></span></li>
		<li><span class="item {if $view=='history'}selected{/if}" id="history"> <span> {t}Stock History{/t}</span></span></li>
		<li><span class="item {if $view=='delivery_notes'}selected{/if}" id="delivery_notes"> <span> {t}Delivery Notes{/t}</span></span></li>
		<li><span class="item {if $view=='purchase_orders'}selected{/if}" id="purchase_orders"> <span> {t}Purchase Orders{/t}</span></span></li>
	</ul>
	<div style="clear:both;width:100%;border-bottom:1px solid #ccc">
	</div>
	<div id="block_transactions" class="block data_table" style="{if $view!='transactions'}display:none;{/if}clear:both;margin-top:20px;;padding:0 20px 20px 30px">
		<span class="clean_table_title with_elements">{t}Part Stock Transactions{/t}</span> 
	<div class="elements_chooser">
				<span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='all_transactions'}selected{/if}" id="restrictions_all_transactions" table_type="all_transactions">{t}All{/t} (<span id="transactions_all_transactions"></span><img id="transactions_all_transactions_wait" src="art/loading.gif" style="height:11px">)</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='oip_transactions'}selected{/if}" id="restrictions_oip_transactions" table_type="oip_transactions">{t}OIP{/t} (<span id="transactions_oip_transactions"></span><img id="transactions_oip_transactions_wait" src="art/loading.gif" style="height:11px">)</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='out_transactions'}selected{/if}" id="restrictions_out_transactions" table_type="out_transactions">{t}Out{/t} (<span id="transactions_out_transactions"></span><img id="transactions_out_transactions_wait" src="art/loading.gif" style="height:11px">)</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='in_transactions'}selected{/if}" id="restrictions_in_transactions" table_type="in_transactions">{t}In{/t} ((<span id="transactions_in_transactions"></span><img id="transactions_in_transactions_wait" src="art/loading.gif" style="height:11px">)</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='audit_transactions'}selected{/if}" id="restrictions_audit_transactions" table_type="audit_transactions">{t}Audits{/t} (<span id="transactions_audit_transactions"></span><img id="transactions_audit_transactions_wait" src="art/loading.gif" style="height:11px">)</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='move_transactions'}selected{/if}" id="restrictions_move_transactions" table_type="move_transactions">{t}Movements{/t} (<span id="transactions_move_transactions"></span><img id="transactions_move_transactions_wait" src="art/loading.gif" style="height:11px">)</span> 
			</div>
		
		<div class="table_top_bar">
		</div>
		<div style="float:right;margin-top:0px;padding:0px;font-size:90%;position:relative;top:-7px">
			<div style="position:relative;left:18px;margin-top:10px">
				<span id="clear_intervalt" style="font-size:80%;color:#777;cursor:pointer;{if $to_transactions=='' and $from_transactions=='' }display:none{/if}">{t}clear{/t}</span> {t}Interval{/t}: 
				<input id="v_calpop1t" type="text" class="text" size="11" maxlength="10" name="from" value="{$from_transactions}" />
				<img style="height:14px;bottom:1px;left:-19px;" id="calpop1t" class="calpop" src="art/icons/calendar_view_month.png" align="absbottom" alt="" /> <span class="calpop" style="margin-left:4px">&rarr;</span> 
				<input class="calpop" id="v_calpop2t" size="11" maxlength="10" type="text" name="to" value="{$to_transactions}" />
				<img style="height:14px;bottom:1px;left:-37px;" id="calpop2t" class="calpop_to" src="art/icons/calendar_view_month.png" align="absbottom" alt="" /> <img style="position:relative;right:26px;cursor:pointer;height:15px" align="absbottom" src="art/icons/application_go.png" id="submit_intervalt" alt="{t}Go{/t}" /> 
			</div>
			<div id="cal1tContainer" style="position:absolute;display:none; z-index:2;;right:70px">
			</div>
			<div style="position:relative;right:-58px">
				<div id="cal2tContainer" style="display:none; z-index:2;position:absolute">
				</div>
			</div>
		</div>
		{include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1 } 
		<div style="font-size:85%" id="table1" class="data_table_container dtable btable">
		</div>
	</div>
	<div id="block_history" class="block data_table" style="{if $view!='history'}display:none;{/if}clear:both;margin-top:20px;;padding:0 20px 30px 20px ">
		<div id="stock_history_plot_subblock">
			<span class="clean_table_title">{t}Stock History Chart{/t} <img id="hide_stock_history_chart" alt="{t}hide{/t}" title="{t}Hide Chart{/t}" style="{if !$show_stock_history_chart}display:none;{/if}cursor:pointer;vertical-align:middle;position:relative;bottom:1px" src="art/icons/hide_button.png" /> <img id="show_stock_history_chart" alt="{t}show{/t}" title="{t}Show Chart{/t}" style="{if $show_stock_history_chart}display:none;{/if}cursor:pointer;vertical-align:middle" src="art/icons/show_button.png" /> </span> 
			<div id="stock_history_plot_subblock_part" style="{if !$show_stock_history_chart}display:none;{/if}">
				<div class="buttons small">
					<button id="change_plot">&#x21b6 <span id="change_plot_label_value" style="{if $stock_history_chart_output!='stock'}display:none{/if}">{t}Stock{/t}</span> <span id="change_plot_label_stock" style="{if $stock_history_chart_output!='value'}display:none{/if}">{t}Value at Cost{/t}</span> <span id="change_plot_label_end_day_value" style="{if $stock_history_chart_output!='end_day_value'}display:none{/if}">{t}Cost Value (end day){/t}</span> <span id="change_plot_label_commercial_value" style="{if $stock_history_chart_output!='commercial_value'}display:none{/if}">{t}Commercial Value{/t}</span> </button> 
				</div>
				<div id="stock_history_plot">
					<strong>You need to upgrade your Flash Player</strong> 
				</div>
<script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "part_history_plot_object", "930", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
				so.addVariable("chart_id", "part_history_plot_object");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_general_candlestick.xml.php?tipo=part_stock_history&output={$stock_history_chart_output}&parent=part&parent_key={$part->sku}"));
		so.addVariable("preloader_color", "#999999");
		so.write("stock_history_plot");
		// ]]>
	</script> <script>

var flashMovie;

function reloadSettings(file) {
  flashMovie.reloadSettings(file);
}

	function amChartInited(chart_id){

  flashMovie = document.getElementById(chart_id);
  
  }
	</script> 
			</div>
		</div>
		<div id="stock_history_table_subblock">
			<span class="clean_table_title" style="clear:both;margin-top:20px">{t}Stock History{/t} </span> 
			<div id="stock_history_type" style="display:inline;color:#aaa">
				<span id="stock_history_type_day" table_type="day" style="margin-left:10px;font-size:80%;" class="table_type state_details {if $stock_history_type=='day'}selected{/if}">{t}Daily{/t}</span> <span id="stock_history_type_week" table_type="week" style="margin-left:5px;font-size:80%;" class="table_type state_details {if $stock_history_type=='week'}selected{/if}">{t}Weekly{/t}</span> <span id="stock_history_type_month" table_type="month" style="margin-left:5px;font-size:80%;" class="table_type state_details {if $stock_history_type=='month'}selected{/if}">{t}Monthly{/t}</span> 
			</div>
			</span> 
			<div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999;margin-bottom:0px">
			</div>
			<div style="float:right;margin-top:0px;padding:0px;font-size:90%;position:relative;top:-7px">
				<div style="position:relative;left:18px;margin-top:10px">
					<span id="clear_interval" style="font-size:80%;color:#777;cursor:pointer;{if $to=='' and $from=='' }display:none{/if}">{t}clear{/t}</span> {t}Interval{/t}: 
					<input id="v_calpop1" type="text" class="text" size="11" maxlength="10" name="from" value="{$from}" />
					<img style="height:14px;bottom:1px;left:-19px;" id="calpop1" class="calpop" src="art/icons/calendar_view_month.png" align="absbottom" alt="" /> <span class="calpop" style="margin-left:4px">&rarr;</span> 
					<input class="calpop" id="v_calpop2" size="11" maxlength="10" type="text" name="to" value="{$to}" />
					<img style="height:14px;bottom:1px;left:-37px;" id="calpop2" class="calpop_to" src="art/icons/calendar_view_month.png" align="absbottom" alt="" /> <img style="position:relative;right:26px;cursor:pointer;height:15px" align="absbottom" src="art/icons/application_go.png" id="submit_interval" alt="{t}Go{/t}" /> 
				</div>
				<div id="cal1Container" style="position:absolute;display:none; z-index:2">
				</div>
				<div style="position:relative;right:-80px">
					<div id="cal2Container" style="display:none; z-index:2;position:absolute">
					</div>
				</div>
			</div>
			{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 no_filter=1 } 
			<div id="table0" style="font-size:85%" class="data_table_container dtable btable">
			</div>
		</div>
	</div>
	<div id="block_description" class="block data_table" style="{if $view!='description'}display:none;{/if}clear:both;margin-top:20px;;padding:0 20px 30px 20px;min-height:300px">
		<div style="width:500px;float:left;margin-left:0px;">
			<table border="0" class="show_info_product" id="description_info">
				<tr>
					<td>{t}Referece{/t}:</td>
					<td>{$part->get('Part Reference')}</td>
				</tr>
				<tr>
					<td style="width:150px">{t}Commodity Code{/t}:</td>
					<td>{$part->get('Part Tariff Code')}</td>
				</tr>
				<tr>
					<td>{t}Duty Rate{/t}:</td>
					<td>{$part->get('Part Duty Rate')}</td>
				</tr>
			</table>
			<table class="show_info_product">
				<tr>
					<td style="width:150px">{t}Keeping since{/t}:</td>
					<td>{$part->get('Valid From Datetime')}</td>
				</tr>
				<tr>
					<td>{t}Sold as{/t}:</td>
					<td>{$part->get('Part XHTML Currently Used In')}</td>
				</tr>
				{if $part->get('Part Available')=='No'} 
				<tr class="discontinued">
					<td colspan="2" style="font-weight:800;font-size:160%;text-align:center">{t}Can't restock{/t}</td>
				</tr>
				{else} 
				<tr>
					<td>{t}Supplied by{/t}:</td>
					<td>{$part->get('Part XHTML Currently Supplied By')}</td>
				</tr>
				<tr>
					<td>{t}Cost{/t}:</td>
					<td>{$part->get_formated_unit_cost()}</td>
				</tr>
				{/if} {foreach from=$show_case key=name item=value} 
				<tr>
					<td>{$name}:</td>
					<td>{$value}</td>
				</tr>
				{/foreach} 
			</table>
		</div>
		<div style="float:left;margin-left:20px;width:400px">
			<table border="0" class="show_info_product" id="propierties_info">
				<tr>
					<td style="width:180px">{t}Package Type{/t}:</td>
					<td>{$part->get('Part Package Type')}</td>
				</tr>
				<tr>
					<td style="width:180px">{t}Package Weight{/t}:</td>
					<td>{$part->get('Package Weight')}</td>
				</tr>
				<tr>
					<td>{t}Package Dimensions{/t}:</td>
					<td>{$part->get('Part Package XHTML Dimensions')}</td>
				</tr>
				<tr>
					<td>{t}Package Volume{/t}:</td>
					<td>{$part->get('Package Volume')}</td>
				</tr>
				<tr>
					<td style="width:180px">{t}Unit Weight{/t}:</td>
					<td>{$part->get('Unit Weight')}</td>
				</tr>
				<tr>
					<td>{t}Unit Dimensions{/t}:</td>
					<td>{$part->get('Part Unit XHTML Dimensions')}</td>
				</tr>
			</table>
		</div>
		<div style="float:left;width:450px;margin-left:10px;{if !$number_part_custom_fields}display:none{/if}">
			<h2 style="clear:both">
				{t}Custom Fields{/t} 
			</h2>
			<table class="show_info_product">
				{foreach from=$part_custom_fields key=name item=value} 
				<tr>
					<td>{$name}:</td>
					<td>{$value}</td>
				</tr>
				{/foreach} 
			</table>
		</div>
		<div style="clear:both;{if !$part->get('Part General Description')}display:none{/if}">
			<h2>
				{t}General Description{/t} 
			</h2>
			<div style="margin-top:5px">
				{$part->get('Part General Description')} 
			</div>
		</div>
		<div style="clear:both;{if !$part->get('Part Health And Safety')}display:none{/if}">
			<h2>
				{t}Health & Safety{/t} 
			</h2>
			<div style="margin-top:5px">
				{$part->get('Part Health And Safety')} 
			</div>
		</div>
		<div style="clear:both">
		</div>
	</div>
	<div id="block_sales" class="block data_table" style="{if $view!='sales'}display:none;{/if}clear:both;margin-top:5px;;padding:0 20px 30px 20px ">
		{include file='calendar_splinter.tpl' calendar_id='sales' calendar_link='part.php'} 
		<div style="margin-top:20px;width:900px">
			<span><img src="art/icons/clock_16.png" style="height:12px;position:relative;bottom:2px"> {$period}</span> 
			<div style="margin-top:0px">
				<div style="width:200px;float:left;margin-left:0px;">
					<table style="clear:both" class="show_info_product">
						<tbody>
							<tr>
								<td>{t}Sales{/t}:</td>
								<td class="aright" id="sales_amount"><img style="height:14px" src="art/loading.gif" /></td>
							</tr>
							<tr>
								<td>{t}Profit{/t}:</td>
								<td class="aright" id="profits"><img style="height:14px" src="art/loading.gif" /></td>
							</tr>
							<tr>
								<td>{t}Margin{/t}:</td>
								<td class="aright" id="margin"><img style="height:14px" src="art/loading.gif" /></td>
							</tr>
							<tr>
								<td>{t}GMROI{/t}:</td>
								<td class="aright" id="gmroi"><img style="height:14px" src="art/loading.gif" /></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div style="float:left;margin-left:20px">
					<table style="width:200px;clear:both" class="show_info_product">
						<tbody id="no_supplied_tbody" style="display:none">
							<tr>
								<td>{t}Required{/t}:</td>
								<td class="aright" id="required"><img style="height:14px" src="art/loading.gif" /></td>
							</tr>
							<tr>
								<td>{t}Out of Stock{/t}:</td>
								<td class="aright error" id="out_of_stock"><img style="height:14px" src="art/loading.gif" /></td>
							</tr>
							<tr>
								<td>{t}Not Found{/t}:</td>
								<td class="aright error" id="not_found"><img style="height:14px" src="art/loading.gif" /></td>
							</tr>
						</tbody>
						<tbody>
							<tr>
								<td>{t}Sold{/t}:</td>
								<td class="aright" id="sold"><img style="height:14px" src="art/loading.gif" /></td>
							</tr>
							<tr id="given_tr" style="display:none">
								<td>{t}Given for free{/t}:</td>
								<td class="aright" id="given"><img style="height:14px" src="art/loading.gif" /></td>
							</tr>
							<tr id="dispatched_tr" style="display:none">
								<td>{t}Total Dispatched{/t}:</td>
								<td class="aright" id="dispatched" style="font-weight:800"><img style="height:14px" src="art/loading.gif" /></td>
							</tr>
							<tr id="broken_tr" style="display:none">
								<td>{t}Broken{/t}:</td>
								<td class="aright" id="broken"><img style="height:14px" src="art/loading.gif" /></td>
							</tr>
							<tr id="lost_tr" style="display:none">
								<td>{t}Lost{/t}:</td>
								<td class="aright" id="lost"><img style="height:14px" src="art/loading.gif" /></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div style="clear:both;">
			</div>
		</div>
		<div id="sales_sub_blocks" style="clear:both;">
			<ul class="tabs" id="chooser_ul" style="margin-top:10px">
				<li> <span class="item {if $sales_sub_block_tipo=='plot_part_sales'}selected{/if}" onclick="change_sales_sub_block(this)" id="plot_part_sales" tipo="store"> <span>{t}Sales Chart{/t}</span> </span> </li>
				<li> <span class="item {if $sales_sub_block_tipo=='part_sales_timeseries'}selected{/if}" onclick="change_sales_sub_block(this)" id="part_sales_timeseries" tipo="store"> <span>{t}Part Sales History{/t}</span> </span> </li>
				<li> <span style="display:none;" class="item {if $sales_sub_block_tipo=='product_breakdown_sales'}selected{/if}" onclick="change_sales_sub_block(this)" id="product_breakdown_sales" tipo="list" forecast="" interval=""> <span>{t}Products Sales Breakdown{/t}</span> </span> </li>
			</ul>
			<div id="sub_block_plot_part_sales" style="min-height:400px;clear:both;border:1px solid #ccc;{if $sales_sub_block_tipo!='plot_part_sales'}display:none{/if}">
<script type="text/javascript" src="external_libs/amstock/amstock/swfobject.js"></script> <script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "amstock", "905", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_asset_sales.xml.php?tipo=part_sales&part_sku={$part->sku}"));
		so.addVariable("preloader_color", "#999999");
		so.write("sub_block_plot_part_sales");
		// ]]>
	</script> 
				<div style="clear:both">
				</div>
			</div>
			<div id="sub_block_part_sales_timeseries" style="padding:20px;min-height:400px;clear:both;border:1px solid #ccc;{if $sales_sub_block_tipo!='part_sales_timeseries'}display:none{/if}">
				<span class="clean_table_title">{t}Part Sales History{/t}</span> 
				<div>
					<span tipo='year' id="part_sales_history_type_year" style="float:right" class="table_type state_details {if $part_sales_history_type=='year'}selected{/if}">{t}Yearly{/t}</span> <span tipo='month' id="part_sales_history_type_month" style="float:right;margin-right:10px" class="table_type state_details {if $part_sales_history_type=='month'}selected{/if}">{t}Monthly{/t}</span> <span tipo='week' id="part_sales_history_type_week" style="float:right;margin-right:10px" class="table_type state_details {if $part_sales_history_type=='week'}selected{/if}">{t}Weekly{/t}</span> <span tipo='day' id="part_sales_history_type_day" style="float:right;margin-right:10px" class="table_type state_details {if $part_sales_history_type=='day'}selected{/if}">{t}Daily{/t}</span> 
				</div>
				<div class="table_top_bar space">
				</div>
				{include file='table_splinter.tpl' table_id=4 filter_name=$filter_name4 filter_value=$filter_value4 no_filter=1 } 
				<div id="table4" style="font-size:85%" class="data_table_container dtable btable">
				</div>
			</div>
			<div id="sub_block_product_breakdown_sales" style="min-height:400px;clear:both;border:1px solid #ccc;{if $sales_sub_block_tipo!='product_breakdown_sales'}display:none{/if}">
				<span class="clean_table_title">{t}Product Breakdown{/t}</span> 
				<div class="table_top_bar space">
				</div>
				{include file='table_splinter.tpl' table_id=5 filter_name=$filter_name5 filter_value=$filter_value5 no_filter=1 } 
				<div id="table5" style="font-size:85%" class="data_table_container dtable btable">
				</div>
			</div>
			<div style="clear:both;">
			</div>
		</div>
	</div>
	<div id="block_notes" style="{if $view!='notes'}display:none;{/if}clear:both;margin-top:20px;;padding:0 20px 30px 20px">
		<span id="table_title" class="clean_table_title">{t}History/Notes{/t}</span> 
		<div class="elements_chooser">
				<span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $elements_part_history.Changes}selected{/if} label_part_history_changes" id="elements_part_history_changes" table_type="elements_changes">{t}Changes History{/t} (<span id="elements_changes_number">{$elements_part_history_number.Changes}</span>)</span> <span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_part_history.Notes}selected{/if} label_part_history_notes" id="elements_part_history_notes" table_type="elements_notes">{t}Staff Notes{/t} (<span id="elements_notes_number">{$elements_part_history_number.Notes}</span>)</span> <span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_part_history.Attachments}selected{/if} label_part_history_attachments" id="elements_part_history_attachments" table_type="elements_attachments">{t}Attachments{/t} (<span id="elements_notes_number">{$elements_part_history_number.Attachments}</span>)</span> 
			</div>
		
		<div class="table_top_bar space">
		</div>
		{include file='table_splinter.tpl' table_id=3 filter_name=$filter_name3 filter_value=$filter_value3} 
		<div id="table3" class="data_table_container dtable btable">
		</div>
	</div>
	<div id="block_purchase_orders" class="block data_table" style="{if $view!='puchase_orders'}display:none;{/if}clear:both;margin-top:20px;;padding:0 20px 30px 20px ">
	</div>
	<div id="block_delivery_notes" class="block data_table" style="{if $view!='delivery_notes'}display:none;{/if}clear:both;margin-top:20px;;padding:0 20px 30px 20px ">
		{include file='table_splinter.tpl' table_id=2 filter_name=$filter_name2 filter_value=$filter_value2 no_filter=5 } 
		<div class="clean_table_controls">
			<div>
				<span style="margin:0 5px" id="paginator2"></span> 
			</div>
		</div>
		<div id="table2" style="font-size:85%" class="data_table_container dtable btable">
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

<div id="dialog_edit_web_state" style="padding:20px 20px 10px 20px ">
	<div id="new_customer_msg">
	</div>
	<input type="hidden" value="" id="product_pid"> 
	<div id="edit_web_state_wait" style="text-align:right;display:none">
		<img src="art/loading.gif" /> {t}Processing Request{/t} 
	</div>
	<div class="buttons" id="edit_web_state_buttons">
		<button onclick="set_web_configuration('Offline')">{t}Sold Out{/t}</button> <button onclick="set_web_configuration('Online Force Out of Stock')">{t}Out of Stock{/t}</button> <button onclick="set_web_configuration('Online Force For Sale')">{t}In Stock{/t}</button> <button onclick="set_web_configuration('Online Auto')">{t}Automatic{/t}</button> 
	</div>
</div>

<div id="change_plot_menu" style="padding:10px 20px 0px 10px">
	<table class="edit" border="0" style="width:200px">
		<tr class="title">
			<td>{t}Choose chart{/t}:</td>
		</tr>
		<tr style="height:5px">
			<td></td>
		</tr>
		<tr>
			<td> 
			<div class="buttons">
				<button style="float:none;margin:0px auto;min-width:140px" onclick="change_plot('stock')"> {t}Stock{/t}</button> <button style="float:none;margin:0px auto;min-width:140px" onclick="change_plot('value')"> {t}Value at Cost{/t}</button> <button style="float:none;margin:0px auto;min-width:140px" onclick="change_plot('end_day_value')"> {t}Cost Value (end day){/t}</button> <button style="float:none;margin:0px auto;min-width:140px" onclick="change_plot('commercial_value')"> {t}Commercial Value{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>
{include file='stock_splinter.tpl'} {include file='notes_splinter.tpl'} {include file='footer.tpl'} 