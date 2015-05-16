{include file='header.tpl'}
		<input type="hidden" value="{$session_data}" id="session_data" />
 
<input id="pid" value="{$supplier_product->pid}" type="hidden"> 
<input type="hidden" id="from" value="{$from}" />
<input type="hidden" id="to" value="{$to}" />
<input type="hidden" id="history_table_id" value="3"> 
<input type="hidden" id="subject" value="supplier_product"> 
<input type="hidden" id="subject_key" value="{$supplier_product->pid}"> 



<input type="hidden" id="calendar_id" value="sales" />
<div style="display:none; position:absolute; left:10px; top:200px; z-index:2" id="cal1Container">
</div>
<div id="bd" class="no_padding">
	<div style="padding:0 20px">
		{include file='suppliers_navigation.tpl'} 
		<div class="branch">
			<span><a href="suppliers.php">{t}Suppliers{/t}</a> &rarr; <a href="supplier.php?id={$supplier->id}">{$supplier->get('Supplier Name')}</a> &rarr; {$supplier_product->get('Supplier Product Code')}</span> 
		</div>
		<div class="top_page_menu">
			<div class="buttons  " style="float:left">
				{if isset($prev_pid)}<img style="vertical-align:bottom;float:none" class="previous" onmouseover="this.src='art/previous_button.gif'" onmouseout="this.src='art/previous_button.png'" title="{$prev_pid.title}" onclick="window.location='{$prev_pid.link}'" src="art/previous_button.png" alt="{t}Previous{/t}" />{/if} 
				<span class="main_title no_buttons"><span class="id">{$supplier_product->get('Supplier Product Code')}</span> {$supplier_product->get('Supplier Product Name')} </span> 
			</div>
							{if isset($next_pid) }<img class="next" onmouseover="this.src='art/next_button.gif'" onmouseout="this.src='art/next_button.png'" title="{$next_pid.title}" onclick="window.location='{$next_pid.link}'" src="art/next_button.png" alt="{t}Next{/t}" />{/if} 

			<div class="buttons small" style="position:relative;top:5px">
							<button onclick="window.location='edit_supplier_product.php?pid={$supplier_product->pid}'"><img src="art/icons/database_edit.png" alt=""> {t}Edit{/t}</button>

			<button id="sticky_note_button"><img src="art/icons/note.png" alt=""> {t}Note{/t}</button> 
							<button id="sticky_note_for_supplier_button"><img src="art/icons/note_green.png" alt=""> {t}Note to supplier{/t}</button> 

				 <button onclick="window.location='new_part.php?parent=supplier_product&parent_key={$supplier_product->pid}'"><img src="art/icons/brick_add.png" alt=""> {t}Add Part{/t}</button> 
			</div>
			<div style="clear:both">
			</div>
		</div>
		<div id="block_info" style="width:900px;margin-top:10px">
			<div id="photo_container" style="float:left">
				<div style="width:220px;">
					<div id="barcode" style="margin:auto;">
					</div>
				</div>
				<div style="border:1px solid #ddd;padding-stop:0;width:220px;xheight:230px;text-align:center;margin:0 10px 0 0px">
					<div id="imagediv" style="border:1px solid #ddd;width:190px;;padding:5px 5px;xborder:none;cursor:pointer;xbackground:red;margin: 10px 0 10px 9px;vertical-align:middle">
						<a href="{$supplier_product->get('Supplier Product Main Image')}" class="imgpop"><img id="main_image" src="{$supplier_product->get('Supplier Product Main Image')}" style="vertical-align:middle;display:block;;width:190px;;margin:0px auto" valign="center" border="1" id="image" alt="{t}Image{/t}" /></a>
					</div>
				</div>
				<div style="width:160px;margin:auto;padding-top:5px">
					 {foreach from=$supplier_product->get_images_slidesshow() item=image name=foo} 
					 {if $image.is_principal==0}
					  <a href="{$image.normal_url}" class="imgpop">
					  <img style="float:left;border:1px solid#ccc;padding:2px;margin:2px;cursor:pointer" src="{$image.thumbnail_url}" title="" alt="" /> 
					  </a>
					  {/if}
					  
					   {/foreach} 
				</div>
			</div>
			<div style="width:340px;float:left;margin-left:10px;">
				<table class="show_info_product">
					<tr>
						<td>{t}Supplier{/t}:</td>
						<td class="aright"><a href="supplier.php?id={$supplier_product->get('Supplier Key')}">{$supplier_product->get('Supplier Code')}</a></td>
					</tr>
					<tr>
						<td>{t}Code{/t}:</td>
						<td class="aright">{$supplier_product->get('Supplier Product Code')}</td>
					</tr>
					<tr>
						<td>{t}Name{/t}:</td>
						<td class="aright">{$supplier_product->get('Supplier Product Name')}</td>
					</tr>
				</table>
				<table class="show_info_product">
				    {if $supplier_product->get('Supplier Product Units Per Case')!=1 }
					<tbody >
						<tr>
							<td>{t}Units per carton{/t}:</td>
							<td class="aright"><b>{$supplier_product->get('Units Per Case')}</b></td>
						</tr>
						<tr>
							<td>{t}Cost per carton{/t}:</td>
							<td class="aright">{$supplier_product->get_formated_price_per_case()}</td>
						</tr>
						
						<tr>
							<td>{t}Cost per unit{/t}:</td>
							<td class="aright">{$supplier_product->get_formated_price_per_unit()}</td>
						</tr>
						{if $corporate_currency!=$supplier_product->get('Supplier Product Currency')}
						<tbody class="corporate_currency_cost">
						<tr style="border-top:1px solid #ccc">
							<td>Cost per carton</td>
							<td class="aright">{$supplier_product->get_formated_price_per_case_in_corporate_currency()}</td>
						</tr>
						<tr>
							<td>{t}Cost per unit{/t}:</td>
							<td class="aright">{$supplier_product->get_formated_price_per_unit_in_corporate_currency()}</td>
						</tr>
						</tbody>
						{/if}
					</tbody>
					{else}
					<tbody >
						<tr>
							<td>{t}Units per carton{/t}:</td>
							<td class="aright">{$supplier_product->get('Units Per Case')}</td>
						</tr>
						
						<tr>
							<td>{t}Cost per carton{/t}:</td>
							<td class="aright">{$supplier_product->get_formated_price_per_case()}</td>
						</tr>
						{if $corporate_currency!=$supplier_product->get('Supplier Product Currency')}
							<tbody class="corporate_currency_cost">
						<tr style="border-top:1px solid #ccc">
							<td>{t}Cost per carton{/t}</td>
							<td class="aright">{$supplier_product->get_formated_price_per_case_in_corporate_currency()}</td>
						</tr>
						</tbosy>
						{/if}
					</tbody>
					{/if}
				</table>
				
				
			</div>
			<div style="width:280px;float:left;margin-left:20px">
				<table class="show_info_product" style="{if $supplier_product->get('Product Record Type')=='Historic'}display:none{/if};float:right;width:100%">
					<tr>
						<td>{t}Associated parts{/t}:</td>
					</tr>
					{foreach from=$supplier_product->get_parts() item=part_data} 
					<tr>
						<td style="vertical-align:bottom">1&rarr;{$part_data.Parts_Per_Supplier_Product_Unit}</td>
						<td style="vertical-align:bottom"><a href="part.php?sku={$part_data.part->sku}">{$part_data.part->get('Part Reference')}</a></td>
						<td class="stock" style="padding:0">{$part_data.part->get('Current Stock')}</td>
						<td>{$part_data.part->get('Part XHTML Available For Forecast')}</td>
					</tr>
					{/foreach} 
				</table>
				
				<div id="sticky_note_div" class="sticky_note" style="margin-left:0px;margin-bottom:10px;{if $supplier_product->get('Supplier Product Sticky Note')==''}display:none{/if}">
						<img id="sticky_note_bis" style="float:right;cursor:pointer" src="art/icons/edit.gif"> 
						<div id="sticky_note_content" style="padding:10px 15px 10px 15px;">
							{$supplier_product->get('Sticky Note')} 
						</div>
					</div>
		 <div id="sticky_note_for_supplier_div" class="sticky_note green" style="margin-left:0px;{if $supplier_product->get('Supplier Product Note to Supplier')==''}display:none{/if}">
		 
						<img id="sticky_note_for_supplier_bis" style="float:right;cursor:pointer" src="art/icons/edit.gif"> 
						<div id="sticky_note_for_supplier_content" style="padding:10px 15px 10px 15px;">
							{$supplier_product->get('Note to Supplier')} 
						</div>
					</div>
					<div style="clear:both">
					</div>
			</div>
			<div style="clear:both">
			</div>
		</div>
	</div>
	<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:15px">
		<li> <span class="item {if $block_view=='details'}selected{/if}" id="details"> <span> {t}Description{/t}</span></span></li>
		<li> <span class="item {if $block_view=='notes'}selected{/if}" id="notes"> <span> {t}History/Notes{/t}</span></span></li>
				<li> <span class="item {if $block_view=='parts'}selected{/if}" id="parts"> <span> {t}Parts{/t}</span></span></li>

		<li> <span class="item {if $block_view=='sales'}selected{/if}" id="sales"> <span> {t}Sales{/t}</span></span></li>
		<li> <span class="item {if $block_view=='stock_transactions'}selected{/if}" id="stock_transactions"> <span> {t}Stock Transactions{/t}</span></span></li>
		<li> <span class="item {if $block_view=='stock_history'}selected{/if}" id="stock_history"> <span> {t}Stock History{/t}</span></span></li>
		<li style="display:none"> <span class="item {if $block_view=='purchase_orders'}selected{/if}" id="purchase_orders"> <span> {t}Purchase Orders{/t}</span></span></li>
	</ul>
	<div class="tabbed_container no_padding blocks">
		<div id="calendar_container" style="padding:0 20px;padding-bottom:0px;{if $block_view=='details' or  $block_view=='notes'}display:none{/if}">
			<div id="period_label_container" style="{if $period==''}display:none{/if}">
				<img src="art/icons/clock_16.png"> <span id="period_label">{$period_label}</span> 
			</div>
			{include file='calendar_splinter.tpl' } 
			<div style="clear:both">
			</div>
		</div>
		<div id="block_details" style="{if $block_view!='details'}display:none;{/if}">
			<div style="padding:20px">
				<div style="width:280px;float:left;margin-right:20px">
					<table class="show_info_product">
						<tr>
							<td>{t}Unit Weight{/t}:</td>
							<td class="aright">{$supplier_product->get('Formated Weight')}</td>
						</tr>
						<tr>
							<td>{t}Unit Dimensions{/t}:</td>
							<td class="aright">{$supplier_product->get('Formated Dimensions')}</td>
						</tr>
					</table>
					<table class="show_info_product">
						<tr style="{if $supplier->get('Supplier Products Origin Country Code')==''}display:none{/if}">
						<td>{t}Origin{/t}</td>
						<td>{$supplier_product->get('Origin')}</td>
						</tr>
						<tr style="{if $supplier->get('Supplier Average Delivery Days')==0}display:none{/if}">
						<td>{t}Delivery time{/t}</td>
						<td>{$supplier_product->get('Average Delivery Days')}</td>
						</tr>
					
					</table>
				</div>
			</div>
		</div>
		<div id="block_notes" style="{if $block_view!='notes'}display:none;{/if}">
			<div style="padding:20px">
				<span id="table_title" class="clean_table_title" style="margin-right:10px">{t}History/Notes{/t}</span> 
				
				<div class="buttons small left">
				    <button id="note"><img src="art/icons/add.png" alt=""> {t}History Note{/t}</button> 
				<button id="attach"><img src="art/icons/add.png" alt=""> {t}Attachment{/t}</button> 
				</div>
				
				<div class="elements_chooser">
					<span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $elements_part_history.Changes}selected{/if} label_part_history_Changes" id="elements_part_history_Changes" table_type="elements_Changes">{t}Changes History{/t} (<span id="elements_history_Changes_number">{$elements_part_history_number.Changes}</span>)</span> 
					<span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_part_history.Notes}selected{/if} label_part_history_Notes" id="elements_part_history_notes" table_type="elements_Notes">{t}Staff Notes{/t} (<span id="elements_history_Notes_number">{$elements_part_history_number.Notes}</span>)</span> 
					<span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_part_history.Attachments}selected{/if} label_part_history_Attachments" id="elements_part_history_Attachments" table_type="elements_Attachments">{t}Attachments{/t} (<span id="elements_history_Attachments_number">{$elements_part_history_number.Attachments}</span>)</span> 
				</div>
				<div class="table_top_bar space">
				</div>
				{include file='table_splinter.tpl' table_id=3 filter_name=$filter_name3 filter_value=$filter_value3} 
				<div id="table3" class="data_table_container dtable btable">
				</div>
			</div>
		</div>
		<div id="block_sales" style="{if $block_view!='sales'}display:none;{/if}">
			<div style="clear:both">
			</div>
			<div style="padding:0 20px;">
				<div style="margin-top:20px;width:900px;">
					<div style="margin-top:5px">
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
						<li> <span class="item {if $sales_block=='plot_supplier_product_sales'}selected{/if}" onclick="change_sales_sub_block(this)" id="plot_supplier_product_sales"> <span>{t}Sales Chart{/t}</span> </span> </li>
						<li> <span class="item {if $sales_block=='supplier_product_sales_timeseries'}selected{/if}" onclick="change_sales_sub_block(this)" id="supplier_product_sales_timeseries"> <span>{t}Supplier Product Sales History{/t}</span> </span> </li>
					</ul>
					<div id="block_plot_supplier_product_sales" style="min-height:400px;clear:both;border:1px solid #ccc;{if $sales_block!='plot_supplier_product_sales'}display:none{/if}">
						<script type="text/javascript">

		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "amstock", "905", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_asset_sales.xml.php?tipo=supplier_product_sales&supplier_product_pid={$supplier_product->pid}"));
		so.addVariable("preloader_color", "#999999");
		so.write("block_plot_supplier_product_sales");
		// ]]>
	
						</script>
						<div style="clear:both">
						</div>
					</div>
					<div id="block_supplier_product_sales_timeseries" style="padding:20px;min-height:400px;clear:both;border:1px solid #ccc;{if $sales_block!='supplier_product_sales_timeseries'}display:none{/if}">
						<span class="clean_table_title">{t}Supplier Product Sales History{/t}</span> 
						<div class="table_top_bar">
						</div>
						<div class="clusters">
							<div class="buttons small cluster group">
								<button id="change_sales_history_timeline_group"> &#x21b6 {$sales_history_timeline_group_label}</button> 
							</div>
							<div style="clear:both;margin-bottom:5px">
							</div>
						</div>
						{include file='table_splinter.tpl' table_id=4 filter_name=$filter_name4 filter_value=$filter_value4 no_filter=1 } 
						<div id="table4" style="font-size:85%" class="data_table_container dtable btable">
						</div>
					</div>
					<div style="clear:both;">
					</div>
				</div>
			</div>
		</div>
		<div id="block_stock_transactions" style="{if $block_view!='stock_transactions'}display:none;{/if}clear:both;margin:10px 0 40px 0">
			<div style="padding:20px">
				<span class="clean_table_title with_elements">{t}Supplier Product Stock Transactions{/t}</span> 
				<div class="elements_chooser">
					<span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='all_transactions'}selected{/if}" id="restrictions_all_transactions" table_type="all_transactions">{t}All{/t} (<span id="transactions_all_transactions"></span><img id="transactions_all_transactions_wait" src="art/loading.gif" style="height:11px">)</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='oip_transactions'}selected{/if}" id="restrictions_oip_transactions" table_type="oip_transactions">{t}OIP{/t} (<span id="transactions_oip_transactions"></span><img id="transactions_oip_transactions_wait" src="art/loading.gif" style="height:11px">)</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='out_transactions'}selected{/if}" id="restrictions_out_transactions" table_type="out_transactions">{t}Out{/t} (<span id="transactions_out_transactions"></span><img id="transactions_out_transactions_wait" src="art/loading.gif" style="height:11px">)</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='in_transactions'}selected{/if}" id="restrictions_in_transactions" table_type="in_transactions">{t}In{/t} (<span id="transactions_in_transactions"></span><img id="transactions_in_transactions_wait" src="art/loading.gif" style="height:11px">)</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='audit_transactions'}selected{/if}" id="restrictions_audit_transactions" table_type="audit_transactions">{t}Audits{/t} (<span id="transactions_audit_transactions"></span><img id="transactions_audit_transactions_wait" src="art/loading.gif" style="height:11px">)</span> <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='move_transactions'}selected{/if}" id="restrictions_move_transactions" table_type="move_transactions">{t}Movements{/t} (<span id="transactions_move_transactions"></span><img id="transactions_move_transactions_wait" src="art/loading.gif" style="height:11px">)</span> 
				</div>
				<div class="table_top_bar space">
				</div>
				{include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1 } 
				<div style="font-size:85%" id="table1" class="data_table_container dtable btable">
				</div>
			</div>
		</div>
		<div id="block_stock_history" style="{if $block_view!='stock_history'}display:none;{/if}clear:both;margin:10px 0 40px 0">
			<div class="buttons small left tabs">
				<button class="first item {if $stock_history_block=='stock_history_plot'}selected{/if}" id="stock_history_plot">{t}Plot{/t}</button> <button class="item {if $stock_history_block=='stock_history_list'}selected{/if}" id="stock_history_list">{t}List{/t}</button> 
			</div>
			<div class="tabs_base">
			</div>
			<div id="block_stock_history_plot" class="edit_block_content" style="{if $stock_history_block!='stock_history_plot'}display:none{/if}">
				<div class="buttons small">
					<button id="change_plot">&#x21b6 <span id="change_plot_label_value" style="{if $stock_history_chart_output!='stock'}display:none{/if}">{t}Stock{/t}</span> <span id="change_plot_label_stock" style="{if $stock_history_chart_output!='value'}display:none{/if}">{t}Value at Cost{/t}</span> <span id="change_plot_label_end_day_value" style="{if $stock_history_chart_output!='end_day_value'}display:none{/if}">{t}Cost Value (end day){/t}</span> <span id="change_plot_label_commercial_value" style="{if $stock_history_chart_output!='commercial_value'}display:none{/if}">{t}Commercial Value{/t}</span> </button> 
				</div>
				<div id="stock_history_plot_div">
					<strong>You need to upgrade your Flash Player</strong> 
				</div>
				<script type="text/javascript">

		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "supplier_product_history_plot_object", "930", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
				so.addVariable("chart_id", "supplier_product_history_plot_object");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_general_candlestick.xml.php?tipo=part_stock_history&output={$stock_history_chart_output}&parent=supplier_product&parent_key={$supplier_product->pid}"));
		so.addVariable("preloader_color", "#999999");
		so.write("stock_history_plot_div");
		// ]]>
	
				</script>
				<script>


var flashMovie;

function reloadSettings(file) {
  flashMovie.reloadSettings(file);
}

	function amChartInited(chart_id){

  flashMovie = document.getElementById(chart_id);
  
  }
	
				</script>
			</div>
			<div id="block_stock_history_list" class="edit_block_content" style="{if $stock_history_block!='stock_history_list'}display:none{/if}">
				<span class="clean_table_title" style="clear:both;">{t}Stock History{/t} </span> 
				<div class="table_top_bar">
				</div>
				<div class="clusters">
					<div class="buttons small cluster group">
						<button id="change_stock_history_timeline_group"> &#x21b6 {$stock_history_timeline_group_label}</button> 
					</div>
					<div style="clear:both;margin-bottom:5px">
					</div>
				</div>
				{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 no_filter=1 } 
				<div id="table0" style="font-size:85%" class="data_table_container dtable btable">
				</div>
			</div>
		</div>
		<div id="block_purchase_orders" style="{if $block_view!='purchase_orders'}display:none;{/if}clear:both;margin:10px 0 40px 0">
			<div style="padding: 0px 20px">
				<span id="table_title" class="clean_table_title">{t}Purchase Orders with this Product{/t}</span> 
				<div class="table_top_bar space">
				</div>
				{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name2 filter_value=$filter_value2} 
				<div id="table2" class="data_table_container dtable btable">
				</div>
			</div>
		</div>
	</div>
</div>
<div id="dialog_sales_history_timeline_group" style="padding:10px 20px 0px 10px">
	<table class="edit" border="0" style="width:200px">
		<tr style="height:5px">
			<td></td>
		</tr>
		<tbody id="sales_history_timeline_group_options">
			{foreach from=$timeline_group_sales_history_options item=menu } 
			<tr>
				<td> 
				<div class="buttons small">
					<button id="sales_history_timeline_group_{$menu.mode}" class="timeline_group {if $sales_history_timeline_group==$menu.mode}selected{/if}" style="float:none;margin:0px auto;min-width:120px" onclick="change_timeline_group(4,'sales_history','{$menu.mode}','{$menu.label}')"> {$menu.label}</button> 
				</div>
				</td>
			</tr>
			{/foreach} 
		</tbody>
	</table>
</div>
<div id="dialog_sticky_note_for_supplier" style="padding:20px 20px 0px 20px;width:340px">
	<div id="sticky_note_for_supplier_msg">
	</div>
	<table>
		<tr>
			<td> <textarea style="width:330px;height:125px" id="sticky_note_for_supplier_input" onkeyup="change(event,this,'sticky_note_for_supplier')">{$supplier_product->get('Note to Supplier')}</textarea> </td>
		</tr>
		<tr>
			<td> 
			<div class="buttons">
				<button class="positive" onclick="save_sticky_note_for_supplier()">{t}Save{/t}</button> <button class="negative" onclick="close_dialog_sticky_note_for_supplier()">{t}Cancel{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>
{include file='notes_splinter.tpl'}

{include file='footer.tpl'} 