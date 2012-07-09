{include file='header.tpl'} 
<div id="bd" style="padding:0px">
	<div style="padding:0 20px">
{include file='orders_navigation.tpl'}
		<input type="hidden" id="category_key" value="{$category->id}" />
	
	<div  class="branch"> 
  <span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home"/></a>&rarr;  {if $user->get_number_stores()>1}   <a href="orders_server.php?view=invoices" id="branch_type_invoices"  >&#8704; {t}Invoices{/t}</a>    &rarr; {/if}
    <a href="orders.php?store={$store->id}&view=invoices" >{t}Invoices{/t}</span>  ({$store->get('Store Code')})</a>  &rarr; <a href="invoice_categories.php?id=0&store={$store->id}">{t}Invoice Categories{/t} ({$store->get('Store Code')})</a> &rarr;  {$category->get_smarty_tree('invoice_categories.php')}</span>
</div>
	

		<div class="top_page_menu">
			<div class="buttons" style="float:left">
				{if isset($parent_category)} <button onclick="window.location='invoice_categories.php?store={$store->id}&id={$parent_category->id}'"><img src="art/icons/arrow_up.png" alt=""> {$parent_category->get('Category Name')}</button> {/if} <button onclick="window.location='part_categories.php?store={$store->id}&id=0'"><img src="art/icons/house.png" alt=""> {t}Invoice Categories{/t}</button> 
			</div>
			<div class="buttons" style="float:right">
				<button onclick="window.location='edit_part_category.php?id={$category->id}'"><img src="art/icons/table_edit.png" alt=""> {t}Edit Category{/t}</button> <button id="new_category"><img src="art/icons/add.png" alt=""> {t}Add Subcategory{/t}</button> 
			</div>
			<div style="clear:both">
			</div>
		</div>
		<div style="clear:left;">
			<h1>
				{t}Category{/t}: {$category->get('Category Label')}
			</h1>
		</div>
	</div>
	<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:5px">
		<li style="{if $category->get('Category Children')==0 }display:none{/if}" > <span class="item {if $block_view=='subcategories'}selected{/if}" id="subcategories"> <span> {t}Subcategories{/t} ({$category->get('Category Children')})</span></span></li>
		<li style="{if $category->get('Category Number Subjects')==0 }display:none{/if}"> <span class="item {if $block_view=='subjects'}selected{/if}" id="subjects"> <span> {t}Invoices{/t}</span></span></li>
		<li style="{if $category->get('Category Number Subjects')==0 }display:none{/if}"> <span class="item {if $block_view=='sales'}selected{/if}" id="sales"> <span> {t}Sales{/t}</span></span></li>

		<li  style="{if $category->get('Category Children')==0 }display:none{/if}"  > <span class="item {if $block_view=='subcategories_charts'}selected{/if}" id="subcategories_charts"> <span> {t}Charts{/t}</span></span></li>
		<li> <span style="display:none" class="item {if $block_view=='history'}selected{/if}" id="history"> <span> {t}History{/t}</span></span></li>
	</ul>
	<div style="clear:both;width:100%;border-bottom:1px solid #ccc">
	</div>
	<div id="block_sales" style="{if $block_view!='sales'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
	<div style="margin-top:20px;width:900px">
			<div class="clusters">
					<div class="buttons small left cluster">
					<button class="{if $category_period=='all'}class=&quot;selected&quot;{/if}" period="all" id="category_period_all" style="padding-left:7px;padding-right:7px">{t}All{/t}</button>
				</div>
				<div class="buttons small left cluster">				<tr>
					<button class="{if $category_period=='yeartoday'}selected{/if}" period="yeartoday" id="category_period_yeartoday">{t}YTD{/t}</button>
					<button class="{if $category_period=='monthtoday'}selected{/if}" period="monthtoday" id="category_period_monthtoday">{t}MTD{/t}</button>
					<button class="{if $category_period=='weektoday'}selected{/if}" period="weektoday" id="category_period_weektoday">{t}WTD{/t}</button>
					<button class="{if $category_period=='today'}selected{/if}" period="today" id="category_period_today">{t}Today{/t}</button>
					</div>
					
						<div class="buttons small left cluster">				<tr>
					<button class="{if $category_period=='yesterday'}selected{/if}" period="yesterday" id="category_period_yesterday">{t}Yesterday{/t}</button>
					<button class="{if $category_period=='last_w'}selected{/if}" period="last_w" id="category_period_last_w">{t}Last Week{/t}</button>
					<button class="{if $category_period=='last_m'}selected{/if}" period="last_m" id="category_period_last_m">{t}Last Month{/t}</button>
					</div>
					
					<div class="buttons small left cluster">				<tr>
					<button class="{if $category_period=='three_year'}selected{/if}" period="three_year" id="category_period_three_year">{t}3Y{/t}</button>
					<button class="{if $category_period=='year'}selected{/if}" period="year" id="category_period_year">{t}1Yr{/t}</button>
					<button class="{if $category_period=='six_month'}selected{/if}" period="six_month" id="category_period_six_month">{t}6M{/t}</button>
					<button class="{if $category_period=='quarter'}selected{/if}" period="quarter" id="category_period_quarter">{t}1Qtr{/t}</button>
					<button class="{if $category_period=='month'}selected{/if}" period="month" id="category_period_month">{t}1M{/t}</button>
					<button class="{if $category_period=='ten_day'}selected{/if}" period="ten_day" id="category_period_ten_day">{t}10D{/t}</button>
					<button class="{if $category_period=='week'}selected{/if}" period="week" id="category_period_week">{t}1W{/t}</button>
				
				</div>

			<div style="clear:both"></div>
	
	</div>
			<div style="margin-top:20px">
				<div style="width:200px;float:left;margin-left:0px;">
				<table style="clear:both" class="show_info_product">
					
					{foreach from=$period_tags item=period }
					<tbody id="info_{$period.key}" style="{if $category_period!=$period.key}display:none{/if}">
						<tr>
							<td>{t}Sales{/t}:</td>
							<td class="aright">{$category->get_period($period.db,"Acc Sold Amount")}</td>
						</tr>
						<tr>
							<td>{t}Profit{/t}:</td>
							<td class="aright">{$category->get_period($period.db,'Acc Profit')}</td>
						</tr>
						<tr>
							<td>{t}Margin{/t}:</td>
							<td class="aright">{$category->get_period($period.db,'Acc Margin')}</td>
						</tr>
						<tr>
							<td>{t}GMROI{/t}:</td>
							<td class="aright">{$category->get_period($period.db,'Acc GMROI')}</td>
						</tr>
					</tbody>
					{/foreach}
		
				</table>
			</div>
			<div style="float:left;margin-left:20px">
				<table style="width:200px;clear:both" class="show_info_product">
				{foreach from=$period_tags item=period }
					<tbody id="info2_{$period.key}" style="{if $category_period!=$period.key}display:none{/if}">
						{if $category->get_period($period.db,'Acc No Supplied')!=0} 
						<tr>
							<td>{t}Required{/t}:</td>
							<td class="aright">{$category->get_period($period.db,'Acc Required')}</td>
						</tr>
						<tr style="display:none">
							<td>{t}No Supplied{/t}:</td>
							<td class="aright error">{$category->get_period($period.db,'Acc No Supplied')}</td>
						</tr>
						{/if} 
						<tr>
							<td>{t}Sold{/t}:</td>
							<td class="aright">{$category->get_period($period.db,'Acc Sold')}</td>
						</tr>
						{if $category->get_period($period.db,'Acc Given')!=0} 
						<tr>
							<td>{t}Given for free{/t}:</td>
							<td class="aright">{$category->get_period($period.db,'Acc Given')}</td>
						</tr>
						{/if} {if $category->get_period($period.db,'Acc Given')!=0} 
						<tr>
							<td>{t}Broken{/t}:</td>
							<td class="aright">{$category->get('Total Acc Broken')}</td>
						</tr>
						{/if} {if $category->get_period($period.db,'Acc Given')!=0} 
						<tr>
							<td>{t}Lost{/t}:</td>
							<td class="aright">{ $category->get_period($period.db,'Acc Lost')}</td>
						</tr>
						{/if} 
					</tbody>
					{/foreach}
				</table>
			</div>
		</div>	
		
		<div id="sales_plots" style="clear:both;display:none">
				<ul class="tabs" id="chooser_ul" style="margin-top:25px">
					<li> <span class="item {if $plot_tipo=='store'}selected{/if}" onclick="change_plot(this)" id="plot_store" tipo="store"> <span>{t}Parts Sales{/t}</span> </span> </li>
					{* 
					<li> <span class="item {if $plot_tipo=='top_departments'}selected{/if}" id="plot_top_departments" onclick="change_plot(this)" tipo="top_departments"> <span>{t}Top Products{/t}</span> </span> </li>
					
					<li> <span class="item {if $plot_tipo=='pie'}selected{/if}" onclick="change_plot(this)" id="plot_pie" tipo="pie" forecast="{$plot_data.pie.forecast}" interval="{$plot_data.pie.interval}"> <span>{t}Products{/t}</span> </span> </li>
				*} 
				</ul>
<script type="text/javascript" src="external_libs/amstock/amstock/swfobject.js"></script> 
				<div id="sales_plot" style="clear:both;border:1px solid #ccc">
					<div id="single_data_set">
						<strong>You need to upgrade your Flash Player</strong> 
					</div>
				</div>
<script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "amstock", "905", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_asset_sales.xml.php?tipo=family_sales&family_key=1"));
		so.addVariable("preloader_color", "#999999");
		so.write("sales_plot");
		// ]]>
	</script> 
				<div style="clear:both">
				</div>
			</div>
		
		
		
		</div>
	</div>
	<div id="block_subcategories" style="{if $block_view!='subcategories'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
		<div class="data_table" style="clear:both;margin-bottom:20px">
			<span class="clean_table_title">Subcategories</span> 
			
			<div class="table_top_bar">
			</div>
			<div class="clusters">
				<div class="buttons small left cluster">
					
					<button class="{if $subcategories_view=='sales'}selected{/if}" id="subcategories_sales" name="sales">{t}Sales{/t}</button> 
				</div>
				<div class="buttons small left cluster" id="period_options" style="{if $subcategories_view=='general' or $subcategories_view=='locations' };display:none{/if}">
					<button class="{if $subcategories_period=='all'}selected{/if}" period="all" id="subcategories_period_all">{t}All{/t}</button> <button class="{if $subcategories_period=='three_year'}selected{/if}" period="three_year" id="subcategories_period_three_year">{t}3Y{/t}</button> <button class="{if $subcategories_period=='year'}selected{/if}" period="year" id="subcategories_period_year">{t}1Yr{/t}</button> <button class="{if $subcategories_period=='six_month'}selected{/if}" period="six_month" id="subcategories_period_six_month">{t}6M{/t}</button> <button class="{if $subcategories_period=='quarter'}selected{/if}" period="quarter" id="subcategories_period_quarter">{t}1Qtr{/t}</button> <button class="{if $subcategories_period=='month'}selected{/if}" period="month" id="subcategories_period_month">{t}1M{/t}</button> <button class="{if $subcategories_period=='ten_day'}selected{/if}" period="ten_day" id="subcategories_period_ten_day">{t}10D{/t}</button> <button class="{if $subcategories_period=='week'}selected{/if}" period="week" id="subcategories_period_week">{t}1W{/t}</button> <button class="{if $subcategories_period=='yeartoday'}selected{/if}" period="yeartoday" id="subcategories_period_yeartoday">{t}YTD{/t}</button> <button class="{if $subcategories_period=='monthtoday'}selected{/if}" period="monthtoday" id="subcategories_period_monthtoday">{t}MTD{/t}</button> <button class="{if $subcategories_period=='weektoday'}selected{/if}" period="weektoday" id="subcategories_period_weektoday">{t}WTD{/t}</button> <button class="{if $subcategories_period=='today'}selected{/if}" period="today" id="subcategories_period_today">{t}Today{/t}</button> 
				</div>
				<div class="buttons small left cluster" id="avg_options" style="{if $subcategories_view!='sales' };display:none{/if};display:none">
					<button class="{if $subcategories_avg=='totals'}selected{/if}" avg="totals" id="avg_totals">{t}Totals{/t}</button> <button class="{if $subcategories_avg=='month'}selected{/if}" avg="month" id="avg_month">{t}M AVG{/t}</button> <button class="{if $subcategories_avg=='week'}selected{/if}" avg="week" id="avg_week">{t}W AVG{/t}</button> <button class="{if $subcategories_avg=='month_eff'}selected{/if}" style="display:none" avg="month_eff" id="avg_month_eff">{t}M EAVG{/t}</button> <button class="{if $subcategories_avg=='week_eff'}selected{/if}" style="display:none" avg="week_eff" id="avg_week_eff">{t}W EAVG{/t}</button> 
				</div>
				<div style="clear:both">
				</div>
			</div>
			{include file='table_splinter.tpl' table_id=1 filter_name=$filter_name0 filter_value=$filter_value0 } 
		
		
		
		
		<div id="table1" class="data_table_container dtable btable ">
			</div>
		</div>
	</div>
	<div id="block_subjects" style="{if $block_view!='subjects'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
		<div id="children_table" class="data_table">
			<span class="clean_table_title">{t}Invoices in this category{/t} <img class="export_data_link" id="export_csv2" label="{t}Export (CSV){/t}" alt="{t}Export (CSV){/t}" src="art/icons/export_csv.gif"></span> 
			 <div id="table_type" class="table_type">

        <div  style="font-size:90%"   id="invoice_chooser"  style="display:{if $block_view!='orders'}none{/if}">
           
            <span style="float:right;margin-left:20px" class="table_type invoice_type state_details {if $invoice_type=='all'}selected{/if}"  id="restrictions_all_invoices" table_type="all"  >{t}All{/t} ({$total_invoices_and_refunds})</span>
            <span style="float:right;margin-left:20px" class="table_type invoice_type state_details {if $invoice_type=='invoices'}selected{/if}"  id="restrictions_invoices" table_type="invoices"   >{t}Invoices{/t} ({$total_invoices})</span>
            <span style="float:right;margin-left:20px" class="table_type invoice_type state_details {if $invoice_type=='refunds'}selected{/if}"  id="restrictions_refunds"  table_type="refunds"  >{t}Refunds{/t} ({$total_refunds})</span>
            <span style="float:right;margin-left:20px" class="table_type invoice_type state_details {if $invoice_type=='to_pay'}selected{/if}"  id="restrictions_to_pay"  table_type="to_pay"  >{t}To pay{/t} ({$total_to_pay})</span>
            <span style="float:right;margin-left:20px" class="table_type invoice_type state_details {if $invoice_type=='paid'}selected{/if}"  id="restrictions_paid"  table_type="paid"  >{t}Paid{/t} ({$total_paid})</span>
        </div>
     </div>
			
			<div class="table_top_bar">
			</div>
			   <div id="list_options0"> 
    <div style="float:right;margin-top:0px;padding:0px;font-size:90%;position:relative;top:-7px">  
    <form action="orders.php?" method="GET" style="margin-top:10px">
      <div style="position:relative;left:18px">
      <span id="clear_intervali" style="font-size:80%;color:#777;cursor:pointer;{if $to=='' and $from=='' }display:none{/if}">{t}clear{/t}
      </span> {t}Interval{/t}: <input id="v_calpop1i" type="text" class="text" size="11" maxlength="10" name="from" value="{$from}"/>
      <img   id="calpop1i" class="calpop" src="art/icons/calendar_view_month.png" align="absbottom" alt=""   /> 
      <span class="calpop">&rarr;</span> 
      <input   class="calpop" id="v_calpop2i" size="11" maxlength="10"   type="text" class="text" size="8" name="to" value="{$to}"/>
      <img   id="calpop2i" class="calpop_to" src="art/icons/calendar_view_month.png" align="absbottom" alt=""   /> 
	<img style="position:relative;right:26px;cursor:pointer;height:15px" align="absbottom" src="art/icons/application_go.png" style="cursor:pointer" id="submit_intervali"  xonclick="document.forms[1].submit()" alt="{t}Go{/t}" /> 
      </div>
    </form>
    <div id="cal1iContainer" style="position:absolute;display:none; z-index:2"></div>
    <div style="position:relative;right:-80px"><div id="cal2iContainer" style="display:none; z-index:2;position:absolute"></div></div>
      </div>
    
    </div>
			{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0} 
			<div id="table0" class="data_table_container dtable btable " style="font-size:90%">
			</div>
		</div>
	</div>
	<div id="block_subcategories_charts" style="{if $block_view!='subcategories_charts'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
		<div style="float:left" id="plot_referral_1">
			<strong>You need to upgrade your Flash Player</strong> 
		</div>
<script type="text/javascript">
		// <![CDATA[		
		var so = new SWFObject("external_libs/ampie/ampie/ampie.swf", "ampie", "350", "300", "1", "#FFFFFF");
		so.addVariable("path", "external_libs/ampie/ampie/");
		so.addVariable("settings_file", encodeURIComponent("conf/pie_settings.xml.php"));                // you can set two or more different settings files here (separated by commas)
		so.addVariable("data_file", encodeURIComponent("plot_data.csv.php?tipo=category&category_key={$category->id}")); 
		so.addVariable("loading_settings", "LOADING SETTINGS"); 
			
		// you can set custom "loading settings" text here
		so.addVariable("loading_data", "LOADING DATA");                                                 // you can set custom "loading data" text here

		so.write("plot_referral_1");
		// ]]>
	</script> 
		<div style="float:left" id="plot_referral_2">
			<strong>You need to upgrade your Flash Player</strong> 
		</div>
<script type="text/javascript">
		// <![CDATA[		
		var so = new SWFObject("external_libs/ampie/ampie/ampie.swf", "ampie", "550", "550", "8", "#FFFFFF");
		so.addVariable("path", "external_libs/ampie/ampie/");
		so.addVariable("settings_file", encodeURIComponent("conf/pie_settings.xml.php"));                // you can set two or more different settings files here (separated by commas)
		so.addVariable("data_file", encodeURIComponent("plot_data.csv.php?tipo=category_subjects&category_key={$category->id}")); 
		so.addVariable("loading_settings", "LOADING SETTINGS");
		so.addVariable("loading_settings", "LOADING SETTINGS");  // you can set custom "loading settings" text here
		so.addVariable("loading_data", "LOADING DATA");                                                 // you can set custom "loading data" text here

		so.write("plot_referral_2");
		// ]]>
	</script> 
	</div>
	<div id="block_history" style="{if $block_view!='history'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
		<span class="clean_table_title">{t}History{/t}</span> 
		{include file='table_splinter.tpl' table_id=2 filter_name=$filter_name2 filter_value=$filter_value2 } 
		<div id="table2" class="data_table_container dtable btable ">
		</div>
	</div>
</div>
{include file='footer.tpl'} {include file='new_category_splinter.tpl'} 

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

