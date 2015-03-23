{include file='header.tpl'} 
<div id="bd" class="no_padding">
	<div style="padding:0 20px">
		{include file='locations_navigation.tpl'} 
		<input type="hidden" id="warehouse_key" value="{$warehouse->id}" />
		<input type="hidden" id="category_key" value="{$category->id}" />
		<input type="hidden" id="state_type" value="{$state_type}" />
		<input type="hidden" id="modify_stock" value="{$modify_stock}" />
		<input type="hidden" id="link_extra_argument" value="&id={$category->id}" />
		<input type="hidden" id="from" value="{$from}" />
		<input type="hidden" id="to" value="{$to}" />
		<input type="hidden" id="parts_table_id" value="0" />
		<input type="hidden" id="parent" value="category" />
		<input type="hidden" id="parent_key" value="{$category->id}" />
		<input type="hidden" id="calendar_id" value="sales" />
		<input type="hidden" id="subject" value="part_categories" />
		<input type="hidden" id="subject_key" value="{$category->id}" />		
		
		<div class="branch">
			<span> <a href="index.php"> <img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /> </a> &rarr; {if $user->get_number_warehouses()>1}<a href="warehouses.php">{t}Warehouses{/t} </a> &rarr; {/if}<a href="inventory.php?warehouse_id={$warehouse->id}">{t}Inventory{/t} </a> &rarr; <a href="part_categories.php?&warehouse_id={$warehouse->id}"> {t}Parts Categories{/t} </a> &rarr; {$category->get('Category XHTML Branch Tree')} </span> 
		</div>
		<div class="top_page_menu">
			<div class="buttons" style="float:left">
				{if isset($navigation_prev)}<img class="previous" onmouseover="this.src='art/previous_button.gif'" onmouseout="this.src='art/previous_button.png'" title="{$navigation_prev.title}" onclick="window.location='{$navigation_prev.link}'" src="art/previous_button.png" alt="{t}Previous{/t}" />{/if} <span class="main_title"> {t}Category{/t}: <span class="id">{$category->get('Category Label')}</span> {$category->get_icon()}</span> 
			</div>
			<div class="buttons" style="float:right">
				{if isset($navigation_next)}<img class="next" onmouseover="this.src='art/next_button.gif'" onmouseout="this.src='art/next_button.png'" title="{$navigation_next.title}" onclick="window.location='{$navigation_next.link}'" src="art/next_button.png" alt="{t}Next{/t}" />{/if} <button onclick="window.location='edit_part_category.php?id={$category->id}'"> <img src="art/icons/table_edit.png" alt=""> {t}Edit Category{/t} </button> 
			</div>
			<div style="clear:both">
			</div>
		</div>
	</div>
	<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:10px">
		<li> <span class="item {if $block_view=='overview'}selected{/if}" id="overview"> <span> {t}Overview{/t}</span></span> </li>
		<li style="{if !$show_subcategories}display:none{/if}"> <span class="item {if $block_view=='subcategories'}selected{/if}" id="subcategories"> <span> {t}Subcategories{/t} ({$category->get('Number Children')})</span></span> </li>
		<li style="{if !$show_subjects}display:none{/if}"> <span class="item {if $block_view=='subjects'}selected{/if}" id="subjects"> <span> {t}Parts{/t} ({$category->get('Number Subjects')})</span></span> </li>
		<li style="{if !$show_subjects_data}display:none{/if};"> <span class="item {if $block_view=='sales'}selected{/if}" id="sales"> <span> {t}Sales{/t}</span></span> </li>
		<li> <span class="item {if $block_view=='history'}selected{/if}" id="history"> <span> {t}Changeslog{/t}</span></span> </li>
	</ul>
	<div style="clear:both;width:100%;border-bottom:1px solid #ccc">
	</div>
	<div id="block_sales" style="{if $block_view!='sales'}display:none;{/if}clear:both;margin:10px 0 40px 0;padding:0 20px;">


		<div id="period_label_container" style="{if $period==''}display:none{/if}">
			<img src="art/icons/clock_16.png"> <span id="period_label">{$period_label}</span> 
		</div>
		{include file='calendar_splinter.tpl' } 
		<div style="clear:both">
		</div>

		<div style="margin-top:20px;width:900px;{if !$show_subjects_data}display:none{/if}">

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
								<td class="aright"><img style="height:14px" src="art/loading.gif" /></td>
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
				<li> <span class="item {if $sales_sub_block_tipo=='plot_parts_sales'}selected{/if}" onclick="change_sales_sub_block(this)" id="plot_parts_sales"> <span>{t}Sales Chart{/t}</span> </span> </li>
				<li > <span class="item {if $sales_sub_block_tipo=='parts_sales_timeseries'}selected{/if}" onclick="change_sales_sub_block(this)" id="parts_sales_timeseries" tipo="store"> <span>{t}Part Sales History{/t}</span> </span> </li>
			</ul>
			<div id="sub_block_plot_parts_sales" style="min-height:400px;clear:both;border:1px solid #ccc;{if $sales_sub_block_tipo!='plot_parts_sales'}display:none{/if}">
				<script type="text/javascript" src="external_libs/amstock/amstock/swfobject.js"></script> <script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "amstock", "905", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_asset_sales.xml.php?tipo=part_category_sales&category_key={$category->id}"));
		
		so.addVariable("preloader_color", "#999999");
		so.write("sub_block_plot_parts_sales");
		// ]]>
	</script> 
	<div style="clear:both">
	</div>
</div>
<div id="sub_block_parts_sales_timeseries" style="padding:20px;min-height:400px;clear:both;border:1px solid #ccc;{if $sales_sub_block_tipo!='parts_sales_timeseries'}display:none{/if}">
	<span class="clean_table_title">{t}Part Sales History{/t}</span> 
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
<div id="block_subcategories" style="{if $block_view!='subcategories'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
	<div class="data_table" style="clear:both;margin-bottom:20px">
		<span class="clean_table_title" style="margin-right:5px"> {t}Subcategories{/t} </span> 
		<div class="buttons small left">
		<button id="new_deal" onclick="new_subcategory()" class="positive"><img  src="art/icons/add.png"> {t}New{/t}</button> 
		</div>
		<div class="table_top_bar">
		</div>
		<div class="clusters">
			<div class="buttons small left cluster">
				<button class="{if $subcategories_view=='sales'}selected{/if}" id="subcategories_sales" name="sales"> {t}Sales{/t} </button> 
			</div>
			<div class="buttons small left cluster" id="period_options" style="{if $subcategories_view=='general' or $subcategories_view=='locations' };display:none{/if}">
				<button class="{if $subcategories_period=='all'}selected{/if}" period="all" id="subcategories_period_all"> {t}All{/t} </button> <button style="margin-left:4px" class="{if $subcategories_period=='yeartoday'}selected{/if}" period="yeartoday" id="subcategories_period_yeartoday"> {t}YTD{/t} </button> <button class="{if $subcategories_period=='monthtoday'}selected{/if}" period="monthtoday" id="subcategories_period_monthtoday"> {t}MTD{/t} </button> <button class="{if $subcategories_period=='weektoday'}selected{/if}" period="weektoday" id="subcategories_period_weektoday"> {t}WTD{/t} </button> <button class="{if $subcategories_period=='today'}selected{/if}" period="today" id="subcategories_period_today"> {t}Today{/t} </button> <button style="margin-left:4px" class="{if $subcategories_period=='yesterday'}selected{/if}" period="yesterday" id="subcategories_period_yesterday"> {t}YD{/t} </button> <button class="{if $subcategories_period=='last_w'}selected{/if}" period="last_w" id="subcategories_period_last_w"> {t}LW{/t} </button> <button class="{if $subcategories_period=='last_m'}selected{/if}" period="last_m" id="subcategories_period_last_m"> {t}LM{/t} </button> <button style="margin-left:4px" class="{if $subcategories_period=='three_year'}selected{/if}" period="three_year" id="subcategories_period_three_year"> {t}3Y{/t} </button> <button class="{if $subcategories_period=='year'}selected{/if}" period="year" id="subcategories_period_year"> {t}1Yr{/t} </button> <button class="{if $subcategories_period=='six_month'}selected{/if}" period="six_month" id="subcategories_period_six_month"> {t}6M{/t} </button> <button class="{if $subcategories_period=='quarter'}selected{/if}" period="quarter" id="subcategories_period_quarter"> {t}1Qtr{/t} </button> <button class="{if $subcategories_period=='month'}selected{/if}" period="month" id="subcategories_period_month"> {t}1M{/t} </button> <button class="{if $subcategories_period=='ten_day'}selected{/if}" period="ten_day" id="subcategories_period_ten_day"> {t}10D{/t} </button> <button class="{if $subcategories_period=='week'}selected{/if}" period="week" id="subcategories_period_week"> {t}1W{/t} </button> 
			</div>
			<div class="buttons small left cluster" id="avg_options" style="{if $subcategories_view!='sales' };display:none{/if};display:none">
				<button class="{if $subcategories_avg=='totals'}selected{/if}" avg="totals" id="avg_totals"> {t}Totals{/t} </button> <button class="{if $subcategories_avg=='month'}selected{/if}" avg="month" id="avg_month"> {t}M AVG{/t} </button> <button class="{if $subcategories_avg=='week'}selected{/if}" avg="week" id="avg_week"> {t}W AVG{/t} </button> <button class="{if $subcategories_avg=='month_eff'}selected{/if}" style="display:none" avg="month_eff" id="avg_month_eff"> {t}M EAVG{/t} </button> <button class="{if $subcategories_avg=='week_eff'}selected{/if}" style="display:none" avg="week_eff" id="avg_week_eff"> {t}W EAVG{/t} </button> 
			</div>
			<div style="clear:both">
			</div>
		</div>
		{include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1 } 
		<div id="table1" class="data_table_container dtable btable" style="font-size:85%">
		</div>
	</div>
</div>
<div id="block_subjects" style="{if $block_view!='subjects'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
	<div id="children_table" class="data_table">
		<span class="clean_table_title"> {t}Parts in this category{/t} <img class="export_data_link" id="export_parts" label="{t}Export (CSV){/t}" alt="{t}Export (CSV){/t}" src="art/icons/export_csv.gif"></span> 
		<div class="elements_chooser">
			<img class="menu" id="part_element_chooser_menu_button" title="{t}Group by menu{/t}" src="art/icons/list.png" /> 
			<div id="part_use_chooser" style="{if $elements_part_elements_type!='use'}display:none{/if}">
				<span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_use.NotInUse}selected{/if} label_part_NotInUse" id="elements_NotInUse" table_type="NotInUse">{t}Not In Use{/t} (<span id="elements_NotInUse_number"><img src="art/loading.gif" style="height:12.9px" /></span>)</span> <span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_use.InUse}selected{/if} label_part_InUse" id="elements_InUse" table_type="InUse">{t}In Use{/t} (<span id="elements_InUse_number"><img src="art/loading.gif" style="height:12.9px" /></span>)</span> 
			</div>
			<div id="part_state_chooser" style="{if $elements_part_elements_type!='state'}display:none{/if}">
				<span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_state.NotKeeping}selected{/if} label_part_NotKeeping" id="elements_NotKeeping" table_type="NotKeeping">{t}NotKeeping{/t} (<span id="elements_NotKeeping_number"><img src="art/loading.gif" style="height:12.9px" /></span>)</span> <span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_state.Discontinued}selected{/if} label_part_Discontinued" id="elements_Discontinued" table_type="Discontinued">{t}Discontinued{/t} (<span id="elements_Discontinued_number"><img src="art/loading.gif" style="height:12.9px" /></span>)</span> <span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_state.LastStock}selected{/if} label_part_LastStock" id="elements_LastStock" table_type="LastStock">{t}LastStock{/t} (<span id="elements_LastStock_number"><img src="art/loading.gif" style="height:12.9px" /></span>)</span> <span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_state.Keeping}selected{/if} label_part_Keeping" id="elements_Keeping" table_type="Keeping">{t}Keeping{/t} (<span id="elements_Keeping_number"><img src="art/loading.gif" style="height:12.9px" /></span>)</span> 
			</div>
			<div id="part_stock_state_chooser" style="{if $elements_part_elements_type!='stock_state'}display:none{/if}">
				<span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_stock_state.Error}selected{/if} label_part_Error" id="elements_Error" table_type="Error">{t}Error{/t} (<span id="elements_Error_number"><img src="art/loading.gif" style="height:12.9px" /></span>)</span> <span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_stock_state.OutofStock}selected{/if} label_part_OutofStock" id="elements_OutofStock" table_type="OutofStock">{t}Out of Stock{/t} (<span id="elements_OutofStock_number"><img src="art/loading.gif" style="height:12.9px" /></span>)</span> <span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_stock_state.VeryLow}selected{/if} label_part_VeryLow" id="elements_VeryLow" table_type="VeryLow">{t}Very Low{/t} (<span id="elements_VeryLow_number"><img src="art/loading.gif" style="height:12.9px" /></span>)</span> <span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_stock_state.Low}selected{/if} label_part_Low" id="elements_Low" table_type="Low">{t}Low{/t} (<span id="elements_Low_number"><img src="art/loading.gif" style="height:12.9px" /></span>)</span> <span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $elements_stock_state.Normal}selected{/if} label_part_Normal" id="elements_Normal" table_type="Normal">{t}Ok{/t} (<span id="elements_Normal_number"><img src="art/loading.gif" style="height:12.9px" /></span>)</span> <span style="float:right;margin-left:30px" class=" table_type transaction_type state_details {if $elements_stock_state.Excess}selected{/if} label_part_Excess" id="elements_Excess" table_type="Excess">{t}Excess{/t} (<span id="elements_Excess_number"><img src="art/loading.gif" style="height:12.9px" /></span>)</span> <span style="float:right;margin-left:2px" class=" table_type transaction_type state_details  label_part_NotInUse">]</span> <span style="float:right;margin-left:2px" class=" table_type transaction_type state_details {if $elements_use.NotInUse}selected{/if} label_part_NotInUse" id2="elements_NotInUse" id="elements_NotInUse_bis" table_type="NotInUse" title="{t}Not In Use{/t}">{t}NiU{/t}</span> <span style="float:right;margin-left:2px" class=" table_type transaction_type state_details ">|</span> <span style="float:right;margin-left:2px" class=" table_type transaction_type state_details {if $elements_use.InUse}selected{/if} label_part_InUse" id2="elements_InUse" id="elements_InUse_bis" table_type="InUse" title="{t}In Use{/t}">{t}iU{/t}</span> <span style="float:right;margin-left:0px" class=" table_type transaction_type state_details  label_part_NotInUse">[</span> 
			</div>
		</div>
		<div class="table_top_bar">
		</div>
		<div class="clusters">
			<div class="buttons small left cluster">
				<button class="{if $parts_view=='general'}selected{/if}" id="parts_general" name="general"> {t}Description{/t} </button> <button class="{if $parts_view=='stock'}selected{/if}" id="parts_stock" name="stock"> {t}Stock{/t} </button> <button class="{if $parts_view=='locations'}selected{/if}" id="parts_locations" name="locations"> {t}Locations{/t} </button> <button class="{if $parts_view=='sales'}selected{/if}" id="parts_sales" name="sales"> {t}Sales{/t} </button> <button class="{if $parts_view=='forecast'}selected{/if}" id="parts_forecast" name="forecast"> {t}Forecast{/t} </button> 
			</div>
			<div class="buttons small left cluster" id="part_period_options" style="{if $parts_view=='general' or $parts_view=='locations' };display:none{/if}">
				<button class="{if $parts_period=='all'}selected{/if}" period="all" id="parts_period_all">{t}All{/t}</button> <button style="margin-left:4px" class="{if $parts_period=='yeartoday'}selected{/if}" period="yeartoday" id="parts_period_yeartoday">{t}YTD{/t}</button> <button class="{if $parts_period=='monthtoday'}selected{/if}" period="monthtoday" id="parts_period_monthtoday">{t}MTD{/t}</button> <button class="{if $parts_period=='weektoday'}selected{/if}" period="weektoday" id="parts_period_weektoday">{t}WTD{/t}</button> <button class="{if $parts_period=='today'}selected{/if}" period="today" id="parts_period_today">{t}Today{/t}</button> <button style="margin-left:4px" class="{if $parts_period=='yesterday'}selected{/if}" period="yesterday" id="parts_period_yesterday">{t}YD{/t}</button> <button class="{if $parts_period=='last_w'}selected{/if}" period="last_w" id="parts_period_last_w">{t}LW{/t}</button> <button class="{if $parts_period=='last_m'}selected{/if}" period="last_m" id="parts_period_last_m">{t}LM{/t}</button> <button style="margin-left:4px" class="{if $parts_period=='three_year'}selected{/if}" period="three_year" id="parts_period_three_year">{t}3Y{/t}</button> <button class="{if $parts_period=='year'}selected{/if}" period="year" id="parts_period_year">{t}1Yr{/t}</button> <button class="{if $parts_period=='six_month'}selected{/if}" period="six_month" id="parts_period_six_month">{t}6M{/t}</button> <button class="{if $parts_period=='quarter'}selected{/if}" period="quarter" id="parts_period_quarter">{t}1Qtr{/t}</button> <button class="{if $parts_period=='month'}selected{/if}" period="month" id="parts_period_month">{t}1M{/t}</button> <button class="{if $parts_period=='ten_day'}selected{/if}" period="ten_day" id="parts_period_ten_day">{t}10D{/t}</button> <button class="{if $parts_period=='week'}selected{/if}" period="week" id="parts_period_week">{t}1W{/t}</button> 
			</div>
			<div class="buttons small left cluster" id="avg_options" style="{if $parts_view!='sales' };display:none{/if};display:none">
				<button class="{if $parts_avg=='totals'}selected{/if}" avg="totals" id="avg_totals"> {t}Totals{/t} </button> <button class="{if $parts_avg=='month'}selected{/if}" avg="month" id="avg_month"> {t}M AVG{/t} </button> <button class="{if $parts_avg=='week'}selected{/if}" avg="week" id="avg_week"> {t}W AVG{/t} </button> <button class="{if $parts_avg=='month_eff'}selected{/if}" style="display:none" avg="month_eff" id="avg_month_eff"> {t}M EAVG{/t} </button> <button class="{if $parts_avg=='week_eff'}selected{/if}" style="display:none" avg="week_eff" id="avg_week_eff"> {t}W EAVG{/t} </button> 
			</div>
			<div style="clear:both">
			</div>
		</div>
		{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0} 
		<div id="table0" class="data_table_container dtable btable" style="font-size:90%">
		</div>
	</div>
</div>
<div id="block_overview" style="{if $block_view!='overview'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
</div>
<div id="block_history" style="{if $block_view!='history'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
	<span class="clean_table_title"> {t}Changeslog{/t} </span> 
	<div class="elements_chooser">
		<span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $history_elements.Changes}selected{/if} label_part_Changes" id="elements_Changes" table_type="Changes">{t}Changes{/t} (<span id="elements_Changes_number">{$history_elements_number.Changes}</span>)</span> <span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $history_elements.Assign}selected{/if} label_part_Assign" id="elements_Assign" table_type="Assign">{t}Assig{/t} (<span id="elements_Assign_number">{$history_elements_number.Assign}</span>)</span> 
	</div>

	<div class="table_top_bar space">
	</div>
	{include file='table_splinter.tpl' table_id=2 filter_name=$filter_name2 filter_value=$filter_value2 } 
	<div id="table2" class="data_table_container dtable btable">
	</div>
</div>
</div>
<div id="rppmenu0" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd"> {t}Rows per Page{/t}: </li>
			{foreach from=$paginator_menu0 item=menu } 
			<li class="yuimenuitem"> <a class="yuimenuitemlabel" onclick="change_rpp({$menu},0)"> {$menu}</a> </li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu0" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd"> {t}Filter options{/t}: </li>
			{foreach from=$filter_menu0 item=menu } 
			<li class="yuimenuitem"> <a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',0)"> {$menu.menu_label}</a> </li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu1" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd"> {t}Rows per Page{/t}: </li>
			{foreach from=$paginator_menu1 item=menu } 
			<li class="yuimenuitem"> <a class="yuimenuitemlabel" onclick="change_rpp({$menu},1)"> {$menu}</a> </li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu1" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd"> {t}Filter options{/t}: </li>
			{foreach from=$filter_menu1 item=menu } 
			<li class="yuimenuitem"> <a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',1)"> {$menu.menu_label}</a> </li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu2" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd"> {t}Rows per Page{/t}: </li>
			{foreach from=$paginator_menu2 item=menu } 
			<li class="yuimenuitem"> <a class="yuimenuitemlabel" onclick="change_rpp({$menu},2)"> {$menu}</a> </li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu2" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd"> {t}Filter options{/t}: </li>
			{foreach from=$filter_menu2 item=menu } 
			<li class="yuimenuitem"> <a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',2)"> {$menu.menu_label}</a> </li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="dialog_change_parts_element_chooser" style="padding:10px 20px 0px 10px">
	<table class="edit" border="0" style="width:200px">
		<tr class="title">
			<td>{t}Group parts by{/t}:</td>
		</tr>
		<tr style="height:5px">
			<td></td>
		</tr>
		<tr>
			<td> 
				<div class="buttons small">
					<button id="parts_element_chooser_use" style="float:none;margin:0px auto;min-width:120px" onclick="change_parts_element_chooser('use')" class="{if $elements_part_elements_type=='use'}selected{/if}"> State</button> 
				</div>
			</td>
		</tr>
		<tr>
			<td> 
				<div class="buttons small">
					<button id="parts_element_chooser_state" style="float:none;margin:0px auto;min-width:120px" onclick="change_parts_element_chooser('state')" class="{if $elements_part_elements_type=='state'}selected{/if}"> State/Availability</button> 
				</div>
			</td>
		</tr>
		<tr>
			<td> 
				<div class="buttons small">
					<button id="parts_element_chooser_stock_state" style="float:none;margin:0px auto;min-width:120px" onclick="change_parts_element_chooser('stock_state')" class="{if $elements_part_elements_type=='stock_state'}selected{/if}"> Stock Level</button> 
				</div>
			</td>
		</tr>
	</table>
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

{include file='export_splinter.tpl' id='parts' export_fields=$export_parts_fields map=$export_parts_map is_map_default={$export_parts_map_is_default}} {include file='stock_splinter.tpl'} {include file='footer.tpl'} 