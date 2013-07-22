﻿{include file='header.tpl'} 
<div id="bd" style="padding:0px">
<script type="text/javascript" src="external_libs/amstock/amstock/swfobject.js"></script> 
	<input type="hidden" id="site_key" value="{$site->id}" />
	<input type="hidden" id="site_id" value="{$site->id}" />
	<div style="padding:0 20px">
		{include file='assets_navigation.tpl'} 
		<div class="branch">
			<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>&rarr; {if $user->get_number_websites()>1}<a href="sites.php">{t}Websites{/t}</a> &rarr; {/if} <img style="vertical-align:0px;margin-right:1px" src="art/icons/hierarchy.gif" alt="" /> {$site->get('Site URL')} (<a href="store.php?id={$store->id}">{$store->get('Store Code')}</a>)</span> 
		</div>
		<div class="top_page_menu">
			<div class="buttons" style="float:right">
				{if $modify} <button onclick="window.location='edit_site.php?id={$site->id}'"><img src="art/icons/vcard_edit.png" alt=""> {t}Edit Site{/t}</button> {/if} 
			</div>
			<div class="buttons" style="float:left">
				<span class="main_title"><img src="art/icons/site.png" style="height:18px;position:relative;bottom:2px"/> {$site->get('Site Name')} ({$site->get('Site URL')}) </span> 
			</div>
			<div style="clear:both">
			</div>
		</div>
	</div>
	<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:15px">
		<li> <span class="item {if $block_view=='details'}selected{/if}" id="details"> <span> {t}Overview{/t}</span></span></li>
		<li> <span class="item {if $block_view=='pages'}selected{/if}" id="pages"> <span> {t}Pages{/t}</span></span></li>
		<li> <span class="item {if $block_view=='hits'}selected{/if}" id="hits"> <span> {t}Requests{/t}</span></span></li>
		<li> <span class="item {if $block_view=='visitors'}selected{/if}" id="visitors"> <span> {t}Users{/t}</span></span></li>
		<li> <span class="item {if $block_view=='reports'}selected{/if}" id="reports"> <span> {t}Reports{/t}</span></span></li>

	</ul>
	<div style="clear:both;width:100%;border-bottom:1px solid #ccc">
	</div>
	<div style="padding:0 20px">
		<div id="block_details" style="{if $block_view!='details'}display:none;{/if}clear:both;margin:10px 0px 40px 0px">
			<div style="width:350px;float:left;position:relative;top:-20px">
				<table border="0" style="width:100%;margin:0px;height:20">
					<tr>
						<td style="width:150px"></td>
						<td class="aright" style="width:100px">{t}All{/t}</td>
						<td class="aright" style="width:100px">{t}Users{/t}</td>
					</tr>
				</table>
				<table border="0" class="show_info_product">
					<tr>
						<td style="width:150px">{t}Page Hits{/t}:</td>
						<td style="width:100px" class="number aright">{$site->get('Total Acc Requests')}</td>
						<td style="width:100px" class="number aright">{$site->get('Total Acc Users Requests')}</td>
					</tr>
					<tr>
						<td>{t}Sessions{/t}:</td>
						<td class="number aright">{$site->get('Total Acc Sessions')}</td>
						<td class="number aright">{$site->get('Total Acc Users Sessions')}</td>
					</tr>
					<tr>
						<td>{t}Visitors{/t}:</td>
						<td class="number aright">{$site->get('Total Acc Visitors')}</td>
						<td class="number aright">{$site->get('Total Acc Users')}</td>
					</tr>
				</table>
				<table border="0" class="show_info_product">
					<tr>
						<td style="width:150px">{t}Last 24h Hits{/t}:</td>
						<td style="width:100px" class="number aright"> {$site->get('1 Day Acc Requests')} </td>
						<td style="width:100px" class="number aright">{$site->get('1 Day Acc Users Requests')}</td>
					</tr>
					<tr>
						<td>{t}Last 24h Sessions{/t}:</td>
						<td class="number aright"> {$site->get('1 Day Acc Sessions')} </td>
						<td class="number aright"> {$site->get('1 Day Acc Users Sessions')} </td>
					</tr>
					<tr>
						<td>{t}Last 24h Visitors{/t}:</td>
						<td class="number aright"> {$site->get('1 Day Acc Visitors')} </td>
						<td class="number aright">{$site->get('1 Day Acc Users')}</td>
					</tr>
				</table>
				
				<table  class="show_info_product" border=1>
				<tr><td>{t}Sitemap index{/t} 
				<a style="{if $site->get('Site Sitemap Last Update')==''}display:none{/if}" href="sitemap_index.xml.php?id={$site->id}"><img src="art/external_link.gif" style="position:relative;top:-3px"></a>
				
				</td>
				<td colspan=2 class="aright"><img id="update_sitemap" src="art/icons/refresh.png" style="cursor:pointer;margin-left:15px"/></td>
				</tr>
				<tbody id="sitemap_info" style="{if $site->get('Site Sitemap Last Update')==''}display:none{/if}">
				<tr>
				<td>{t}Last updated{/t}</td>
				<td></td>
				<td id="sitemap_last_update">{$site->get('Sitemap Last Update')}</td>
				</tr>
				
				<td>{t}Last pinged{/t}</td>
				<td style="width:20px"><img src="art/icons/google.png" alt="google" title="Google"></td>
				<td>{$site->get('Sitemap Last Ping Google')}</td>
				</tr>
				<tr>
				<td></td>
				<td><img src="art/icons/bing.png" alt="bing" title="Bing"></td>
				<td>{$site->get('Sitemap Last Ping Bing')}</td>
				</tr>
				<tr>
				<td></td>
				<td><img src="art/icons/ask.png" alt="ask" title="Ask"></td>
				<td>{$site->get('Sitemap Last Ping Ask')}</td>
				</tr>
				</tbody>
				</table>
				
			</div>
			<div style="float:left;font-size:80%;text-align:center;padding:00px 20px 20px 20px">
				<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
					{t}Current Logged{/t} 
					<div id="number_current_active_logged_users" style="font-size:120%;font-weight:800;margin-top:5px;margin-bottom:5px">
						{$site->get_current_active_logged_users()} 
					</div>
				</div>
				<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
					{t}User Sessions{/t} 
					<div id="number_open_logged_users_sessions" style="font-size:120%;font-weight:800;margin-top:5px;margin-bottom:5px">
						{$site->get_open_logged_users_sessions()} 
					</div>
				</div>
				<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
					{t}Pages{/t} 
					<div id="number_out_of_stock_customers" style="font-size:120%;font-weight:800;margin-top:5px;margin-bottom:5px">
					</div>
				</div>
			</div>
			<div style="width:15em;float:left;margin-left:20px">
			</div>
		</div>
		<div id="block_pages" style="{if $block_view!='pages'}display:none;{/if}clear:both;margin:20px 0 40px 0">
			<span class="clean_table_title">{t}Pages{/t}</span> 
			<div style="font-size:90%" id="transaction_chooser">
				<span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $elements.Other}selected{/if} label_page_type" id="elements_other">{t}Other{/t} (<span id="elements_other_number">{$elements_number.Other}</span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $elements.DepartmentCatalogue}selected{/if} label_page_type" id="elements_department_catalogue">{t}Department Catalogues{/t} (<span id="elements_department_catalogue_number">{$elements_number.DepartmentCatalogue}</span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $elements.FamilyCatalogue}selected{/if} label_page_type" id="elements_family_catalogue">{t}Family Catalogues{/t} (<span id="elements_family_catalogue_number">{$elements_number.FamilyCatalogue}</span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $elements.ProductDescription}selected{/if} label_page_type" id="elements_product_description">{t}Product Description{/t} (<span id="elements_product_description_number">{$elements_number.ProductDescription}</span>)</span> 
			</div>
			<div class="table_top_bar">
			</div>
			<div class="clusters">
				<div class="buttons small left cluster">
					<button class="table_option {if $pages_view=='general'}selected{/if}" id="page_general">{t}Overview{/t}</button> <button class="table_option {if $pages_view=='visitors'}selected{/if}" id="page_visitors">{t}Visits{/t}</button> 
				</div>
				<div id="page_period_options" class="buttons small left cluster" style="display:{if $pages_view!='visitors' }none{/if};">
					<button class="table_option {if $page_period=='all'}selected{/if}" period="all" id="page_period_all">{t}All{/t}</button> <button class="table_option {if $page_period=='three_year'}selected{/if}" period="three_year" id="page_period_three_year">{t}3Y{/t}</button> <button class="table_option {if $page_period=='year'}selected{/if}" period="year" id="page_period_year">{t}1Yr{/t}</button> <button class="table_option {if $page_period=='yeartoday'}selected{/if}" period="yeartoday" id="page_period_yeartoday">{t}YTD{/t}</button> <button class="table_option {if $page_period=='six_month'}selected{/if}" period="six_month" id="page_period_six_month">{t}6M{/t}</button> <button class="table_option {if $page_period=='quarter'}selected{/if}" period="quarter" id="page_period_quarter">{t}1Qtr{/t}</button> <button class="table_option {if $page_period=='month'}selected{/if}" period="month" id="page_period_month">{t}1M{/t}</button> <button class="table_option {if $page_period=='ten_day'}selected{/if}" period="ten_day" id="page_period_ten_day">{t}10D{/t}</button> <button class="table_option {if $page_period=='week'}selected{/if}" period="week" id="page_period_week">{t}1W{/t}</button> <button class="table_option {if $page_period=='day'}selected{/if}" period="day" id="page_period_day">{t}1D{/t}</button> <button class="table_option {if $page_period=='hour'}selected{/if}" period="hour" id="page_period_hour">{t}1h{/t}</button> 
				</div>
			</div>
			<div class="buttons small clusters">
				<button class="selected" id="change_pages_table_type">{$pages_table_type_label}</button> 
				<div style="clear:both">
				</div>
			</div>
			{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 no_filter=0 } 
			<div id="thumbnails0" class="thumbnails" style="border-top:1px solid SteelBlue;clear:both;{if $pages_table_type!='thumbnails'}display:none{/if}">
			</div>
			<div id="table0" class="data_table_container dtable btable" style="{if $pages_table_type=='thumbnails'}display:none{/if};font-size:85%">
			</div>
		</div>
	<div id="block_reports" style="{if $block_view!='reports'}display:none;{/if}clear:both;margin:10px 0px 40px 0px">
		{foreach from=$report_index item=report_category} 
	<div class="block_list" style="clear:both;">
		<h2>
			{$report_category.title}
		</h2>
		{foreach from=$report_category.reports item=report} 
		<div style="background-image:url('{$report.snapshot}');background-repeat:no-repeat;background-position:center 26px;" onclick="location.href='{$report.url}'">
			{$report.title}
		</div>
		{/foreach} 
	</div>
	{/foreach} 
	</div>
		
	</div>
	<div id="block_hits" style="{if $block_view!='hits'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 10px">
		<div id="plot1" style="clear:both;border:0px solid #ccc">
			<div id="single_data_set">
				<strong>You need to upgrade your Flash Player</strong> 
			</div>
		</div>
<script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "amstock", "905", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_general_volume_timeseries.xml.php?tipo=site_requests&site_key={$site->id}"));
		so.addVariable("preloader_color", "#999999");
		so.write("plot1");
		// ]]>
	</script> 
		<div style="clear:both">
		</div>
	</div>
	<div id="block_visitors" style="{if $block_view!='visitors'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
		<div id="plot2" style="clear:both;border:1px solid #ccc;display:none">
			<div id="single_data_set">
				<strong>You need to upgrade your Flash Player</strong> 
			</div>
		</div>
<script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "amstock", "905", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_general_timeseries.xml.php?tipo=site_visitors&site_key={$site->id}"));
		so.addVariable("preloader_color", "#999999");
		so.write("plot2");
		// ]]>
	</script> {include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1 no_filter=1 } 
		<div id="table1" class="data_table_container dtable btable" style="font-size:85%">
		</div>
	</div>
</div>

<div id="change_pages_table_type_menu" style="padding:10px 20px 0px 10px">
	<table class="edit" border="0" style="width:200px">
		<tr class="title">
			<td>{t}View items as{/t}:</td>
		</tr>
		<tr style="height:5px">
			<td></td>
		</tr>
		{foreach from=$pages_table_type_menu item=menu } 
		<tr>
			<td> 
			<div class="buttons">
				<button style="float:none;margin:0px auto;min-width:120px" onclick="change_table_type('pages','{$menu.mode}','{$menu.label}',0)"> {$menu.label}</button> 
			</div>
			</td>
		</tr>
		{/foreach} 
	</table>
</div>
<div id="rppmenu0" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu0 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp_with_totals({$menu},0)"> {$menu}</a></li>
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
{include file='footer.tpl'} 