﻿{include file='header.tpl'} 
<div id="bd" class="no_padding">
<script type="text/javascript" src="external_libs/amstock/amstock/swfobject.js"></script> 
	<input type="hidden" id="site_key" value="{$site->id}" />
	<input type="hidden" id="site_id" value="{$site->id}" />
	<input type="hidden" id="block_view" value="{$block_view}" />
	<div style="padding:0 20px">
		{include file='assets_navigation.tpl'} 
		<div class="branch">
			<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>&rarr; {if $user->get_number_websites()>1}<a href="sites.php">{t}Websites{/t}</a> &rarr; {/if} <img style="vertical-align:0px;margin-right:1px" src="art/icons/hierarchy.gif" alt="" /> {$site->get('Site URL')} (<a href="store.php?id={$store->id}">{$store->get('Store Code')}</a>)</span> 
		</div>
		<div class="top_page_menu">
			<div class="buttons" style="float:right">
				{if $modify} <button onclick="go_edit()"><img src="art/icons/vcard_edit.png" alt=""> {t}Edit Site{/t}</button> {/if} 
			</div>
			<div class="buttons" style="float:left">
				<span class="main_title"><img src="art/icons/site.png" style="height:18px;position:relative;bottom:2px" /> {$site->get('Site Name')} ({$site->get('Site URL')}) </span> 
			</div>
			<div style="clear:both">
			</div>
		</div>
	</div>
	<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:15px">
		<li> <span class="item {if $block_view=='details'}selected{/if}" id="details"> <span> {t}Overview{/t}</span></span></li>
		<li> <span class="item {if $block_view=='pages'}selected{/if}" id="pages"> <span> {t}Pages{/t}</span></span></li>
		<li style="display:none"> <span class="item {if $block_view=='products'}selected{/if}" id="products"> <span> {t}Products{/t}</span></span></li>
		<li> <span class="item {if $block_view=='hits'}selected{/if}" id="hits"> <span> {t}Requests{/t}</span></span></li>
		<li> <span class="item {if $block_view=='visitors'}selected{/if}" id="visitors"> <span> {t}Users{/t}</span></span></li>
		<li> <span class="item {if $block_view=='search_queries'}selected{/if}" id="search_queries"> <span> {t}Search Queries{/t}</span></span></li>
		<li> <span class="item {if $block_view=='email_reminders'}selected{/if}" id="email_reminders"> <span> {t}Reminders{/t}</span></span></li>
		<li style="display:none"> <span class="item {if $block_view=='reports'}selected{/if}" id="reports"> <span> {t}Reports{/t}</span></span></li>
		<li> <span class="item {if $block_view=='changelog'}selected{/if}" id="changelog"> <span> {t}Changelog{/t}</span></span></li>
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
				<table class="show_info_product" border="0">
					<tr>
						<td>{t}Sitemap index{/t} <a id="sitemap_link" style="{if $site->get('Site Sitemap Last Update')==''}display:none{/if}" href="sitemap_index.xml.php?id={$site->id}"><img src="art/external_link.gif" style="position:relative;top:-3px"></a> </td>
						<td colspan="2" class="aright"> <img id="update_sitemap" src="art/icons/refresh.png" style="cursor:pointer;margin-left:15px" /> <img style="display:none;height:14px" id="update_sitemap_wait" src="art/loading.gif"> </td>
					</tr>
					<tbody id="sitemap_info" style="{if $site->get('Site Sitemap Last Update')==''}display:none{/if}">
						<tr>
							<td>{t}Last updated{/t}</td>
							<td></td>
							<td id="sitemap_last_update" class="aright">{$site->get('Sitemap Last Update')}</td>
						</tr>
						<tr style="{if $site->get('Sitemap Last Ping Google')=='' and $site->get('Sitemap Last Ping Bing')=='' and $site->get('Sitemap Last Ping Ask')==''}display:none{/if}">
							<td>{t}Last pinged{/t}</td>
							<td style="width:20px"><img src="art/icons/google.png" alt="google" title="Google"></td>
							<td style="{if $site->get('Sitemap Last Ping Google')==''}display:none{/if}">{$site->get('Sitemap Last Ping Google')}</td>
						</tr>
						<tr style="{if $site->get('Sitemap Last Ping Bing')==''}display:none{/if}">
							<td></td>
							<td><img src="art/icons/bing.png" alt="bing" title="Bing"></td>
							<td>{$site->get('Sitemap Last Ping Bing')}</td>
						</tr>
						<tr style="{if $site->get('Sitemap Last Ping Bing')==''}display:none{/if}">
							<td></td>
							<td><img src="art/icons/ask.png" alt="ask" title="Ask"></td>
							<td>{$site->get('Sitemap Last Ping Ask')}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div style="float:left;font-size:80%;text-align:center;padding:00px 20px 20px 20px">
				<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
					{t}Pages{/t} 
					<div id="number_pages" style="font-size:120%;font-weight:800;margin-top:5px;margin-bottom:5px">
						{$site->get('Number Pages')} 
					</div>
				</div>
				<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
					{t}Pages with Products{/t} 
					<div id="number_pages_with_products" style="font-size:120%;font-weight:800;margin-top:5px;margin-bottom:5px">
						{$site->get('Number Pages with Products')} 
					</div>
				</div>
				<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
					{t}Pages with OoS{/t} 
					<div id="number_pages" style="font-size:120%;font-weight:800;margin-top:5px;margin-bottom:5px">
						{$site->get('Number Pages with Out of Stock Products')} <br> <span style="font-weight:400">{$site->get('Percentage Number Pages with Products')}</span> 
					</div>
				</div>
			</div>
			<div style="float:left;font-size:80%;text-align:center;padding:00px 20px 20px 20px">
				<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
					{t}Products{/t} 
					<div id="number_pages" style="font-size:120%;font-weight:800;margin-top:5px;margin-bottom:5px">
						{$site->get('Number Products')} 
					</div>
				</div>
				<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
					{t}OoS Products{/t} 
					<div id="number_pages_with_products" style="font-size:120%;font-weight:800;margin-top:5px;margin-bottom:5px">
						{$site->get('Number Out of Stock Products')} <br> <span style="font-weight:400">{$site->get('Percentage Number Out of Stock Products')}</span> 
					</div>
				</div>
			</div>
			<div style="float:left;font-size:80%;text-align:center;padding:00px 20px 20px 20px;clear:left">
				<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
					{t}Currently Logged{/t} 
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
			</div>
			<div style="width:15em;float:left;margin-left:20px">
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
	
	<div id="block_pages" style="{if $block_view!='pages'}display:none;{/if}clear:both;margin:10px 0 40px 0;padding:0">
		<div class="buttons small left tabs">
			<button class="indented item {if $pages_block_view=='pages'}selected{/if}" id="pages_pages" block_id="pages">{t}Pages{/t}</button> 
			<button class="item {if $pages_block_view=='deleted_pages'}selected{/if}" id="pages_deleted_pages" block_id="deleted_pages">{t}Deleted Pages{/t}</button> 
			<button class="item {if $pages_block_view=='page_changelog'}selected{/if}" id="pages_page_changelog" block_id="page_changelog">{t}Page changelog{/t}</button> 
			<button class="item {if $pages_block_view=='product_changelog'}selected{/if}" id="pages_product_changelog" block_id="product_changelog">{t}Product changelog{/t}</button> 
		</div>
		<div class="tabs_base">
		</div>
		<div style="padding:0 20px">
		<div id="block_pages_pages" style="{if $pages_block_view!='pages'}display:none;{/if}clear:both;margin:10px 0px 40px 0px">
		<span class="clean_table_title">{t}Pages{/t}</span> 
			<div class="elements_chooser">
				<img class="menu" id="page_element_chooser_menu_button" title="{t}Group by menu{/t}" src="art/icons/list.png" /> 
				<div id="page_section_chooser" style="{if $page_elements_type!='section'}display:none{/if}">
					<span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $page_section_elements.System}selected{/if} label_page_type" id="page_section_elements_System">{t}System{/t} (<span id="page_section_elements_System_number">{$page_section_elements_number.System}</span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $page_section_elements.Info}selected{/if} label_page_type" id="page_section_elements_Info">{t}Info{/t} (<span id="page_section_elements_Info_number">{$page_section_elements_number.Info}</span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $page_section_elements.Department}selected{/if} label_page_type" id="page_section_elements_Department">{t}Departments{/t} (<span id="page_section_elements_Department_number">{$page_section_elements_number.Department}</span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $page_section_elements.Family}selected{/if} label_page_type" id="page_section_elements_Family">{t}Families{/t} (<span id="page_section_elements_Family_number">{$page_section_elements_number.Family}</span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $page_section_elements.Product}selected{/if} label_page_type" id="page_section_elements_Product">{t}Products{/t} (<span id="page_section_elements_Product_number">{$page_section_elements_number.Product}</span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $page_section_elements.FamilyCategory}selected{/if} label_page_type" id="page_section_elements_FamilyCategory">{t}Family Categories{/t} (<span id="page_section_elements_FamilyCategory_number">{$page_section_elements_number.FamilyCategory}</span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $page_section_elements.ProductCategory}selected{/if} label_page_type" id="page_section_elements_ProductCategory">{t}Product Categories{/t} (<span id="page_section_elements_ProductCategory_number">{$page_section_elements_number.ProductCategory}</span>)</span> 
				</div>
				<div id="page_flags_chooser" style="{if $page_elements_type!='flags'}display:none{/if}">
					{foreach from=$page_flags_elements_data item=flag} 
					
					<span onClick="change_page_flags_elements(this,'flags')" style="float:right;margin-left:20px;" class="{if $page_flags_elements[$flag.color]}selected{/if} label_page_type" id="page_flags_elements_{$flag.color}"><img class="icon" src="art/icons/{$flag.img}" /> {$flag.label} (<span id="page_flags_elements_{$flag.color}_number">{$flag.number}</span>)</span> 
					{/foreach} 
				</div>
				<div id="page_state_chooser" style="{if $page_elements_type!='state'}display:none{/if}">
				</div>
			</div>
			<div class="table_top_bar">
			</div>
			<div class="clusters">
				<div class="buttons small left cluster">
					<button class="table_option {if $pages_view=='general'}selected{/if}" id="page_general">{t}Overview{/t}</button> <button class="table_option {if $pages_view=='visitors'}selected{/if}" id="page_visitors">{t}Visits{/t}</button> <button class="table_option {if $pages_view=='products'}selected{/if}" id="page_products">{t}Products{/t}</button> 
				</div>
				<div id="page_period_options" class="buttons small left cluster" style="display:{if $pages_view!='visitors' }none{/if};">
					<button class="table_option {if $page_period=='all'}selected{/if}" period="all" id="page_period_all">{t}All{/t}</button> <button class="table_option {if $page_period=='three_year'}selected{/if}" period="three_year" id="page_period_three_year">{t}3Y{/t}</button> <button class="table_option {if $page_period=='year'}selected{/if}" period="year" id="page_period_year">{t}1Yr{/t}</button> <button class="table_option {if $page_period=='yeartoday'}selected{/if}" period="yeartoday" id="page_period_yeartoday">{t}YTD{/t}</button> <button class="table_option {if $page_period=='six_month'}selected{/if}" period="six_month" id="page_period_six_month">{t}6M{/t}</button> <button class="table_option {if $page_period=='quarter'}selected{/if}" period="quarter" id="page_period_quarter">{t}1Qtr{/t}</button> <button class="table_option {if $page_period=='month'}selected{/if}" period="month" id="page_period_month">{t}1M{/t}</button> <button class="table_option {if $page_period=='ten_day'}selected{/if}" period="ten_day" id="page_period_ten_day">{t}10D{/t}</button> <button class="table_option {if $page_period=='week'}selected{/if}" period="week" id="page_period_week">{t}1W{/t}</button> <button class="table_option {if $page_period=='day'}selected{/if}" period="day" id="page_period_day">{t}1D{/t}</button> <button class="table_option {if $page_period=='hour'}selected{/if}" period="hour" id="page_period_hour">{t}1h{/t}</button> 
				</div>
				<div class="buttons small cluster group">
					<button class="selected" id="change_pages_table_type">{$pages_table_type_label}</button> 
				</div>
				<div style="clear:both">
				</div>
			</div>
			{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 no_filter=0 } 
			<div id="thumbnails0" class="thumbnails" style="border-top:1px solid SteelBlue;clear:both;{if $pages_table_type!='thumbnails'}display:none{/if}">
			</div>
			<div id="table0" class="data_table_container dtable btable" style="{if $pages_table_type=='thumbnails'}display:none{/if};font-size:85%">
			</div>
		</div>
		<div id="block_pages_deleted_pages" style="{if $pages_block_view!='deleted_pages'}display:none;{/if}clear:both;margin:10px 0px 40px 0px">
		
		<span class="clean_table_title">{t}Deleted pages{/t}</span> 
	<div class="table_top_bar space">
	</div>
	{include file='table_splinter.tpl' table_id=8 filter_name=$filter_name8 filter_value=$filter_value8 } 
	<div id="table8" class="data_table_container dtable btable history">
	</div>
	
	
		</div>
		<div id="block_pages_page_changelog" style="{if $pages_block_view!='page_changelog'}display:none;{/if}clear:both;margin:10px 0px 40px 0px">
			<span class="clean_table_title">{t}Page Changelog{/t}</span> 
	<div class="table_top_bar space">
	</div>
	{include file='table_splinter.tpl' table_id=9 filter_name=$filter_name9 filter_value=$filter_value9 } 
	<div id="table9" class="data_table_container dtable btable history">
	</div>
		</div>
		<div id="block_pages_product_changelog" style="{if $pages_block_view!='product_changelog'}display:none;{/if}clear:both;margin:10px 0px 40px 0px">
			<span class="clean_table_title">{t}Product Changelog{/t}</span> 
	<div class="table_top_bar space">
	</div>
	{include file='table_splinter.tpl' table_id=10 filter_name=$filter_name10 filter_value=$filter_value10 } 
	<div id="table10" class="data_table_container dtable btable history">
	</div>
		</div>
		
		</div>
	</div>
	<div id="block_email_reminders" style="{if $block_view!='email_reminders'}display:none;{/if}clear:both;margin:10px 0 40px 0;padding:0">
		<div class="buttons small left tabs">
			<button class="indented item {if $email_reminders_block_view=='requests'}selected{/if}" id="email_reminders_requests" block_id="requests">{t}Requests{/t}</button> <button class=" item {if $email_reminders_block_view=='customers'}selected{/if}" id="email_reminders_customers" block_id="customers">{t}Customers{/t}</button> <button class=" item {if $email_reminders_block_view=='products'}selected{/if}" id="email_reminders_products" block_id="products">{t}Products{/t}</button> 
		</div>
		<div class="tabs_base">
		</div>
		<div style="padding:0 20px">
			<div id="block_email_reminders_requests" style="{if $email_reminders_block_view!='requests'}display:none;{/if}clear:both;margin:10px 0px 40px 0px">
				<span class="clean_table_title">{t}Email Reminders Requests{/t}</span> 
				<div class="elements_chooser">
					<span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $back_in_stock_elements_email_reminders.Cancelled}selected{/if} label_page_type" id="elements_back_in_stock_email_reminders_Cancelled">{t}Cancelled{/t} (<span id="elements_back_in_stock_email_reminders_Cancelled_number"><img style="height:12.9px" src="art/loading.gif"></span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $back_in_stock_elements_email_reminders.Sent}selected{/if} label_page_type" id="elements_back_in_stock_email_reminders_Sent">{t}Sent{/t} (<span id="elements_back_in_stock_email_reminders_Sent_number"><img style="height:12.9px" src="art/loading.gif"></span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $back_in_stock_elements_email_reminders.Ready}selected{/if} label_page_type" id="elements_back_in_stock_email_reminders_Ready">{t}Ready{/t} (<span id="elements_back_in_stock_email_reminders_Ready_number"><img style="height:12.9px" src="art/loading.gif"></span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $back_in_stock_elements_email_reminders.Waiting}selected{/if} label_page_type" id="elements_back_in_stock_email_reminders_Waiting">{t}Waiting{/t} (<span id="elements_back_in_stock_email_reminders_Waiting_number"><img style="height:12.9px" src="art/loading.gif"></span>)</span> 
				</div>
				<div class="table_top_bar space">
				</div>
				{include file='table_splinter.tpl' table_id=5 filter_name=$filter_name5 filter_value=$filter_value5 no_filter=0 } 
				<div id="table5" class="data_table_container dtable btable" style="font-size:85%">
				</div>
			</div>
			<div id="block_email_reminders_customers" style="{if $email_reminders_block_view!='customers'}display:none;{/if}clear:both;margin:10px 0px 40px 0px">
				<span class="clean_table_title">{t}Customers with email reminders{/t}</span> 
				<div class="elements_chooser">
					<span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $customers_back_in_stock_elements_email_reminders.Done}selected{/if} label_page_type" id="customers_elements_back_in_stock_email_reminders_Done">{t}Completed{/t} (<span id="customers_elements_back_in_stock_email_reminders_Done_number"><img style="height:12.9px" src="art/loading.gif"></span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $customers_back_in_stock_elements_email_reminders.Pending}selected{/if} label_page_type" id="customers_elements_back_in_stock_email_reminders_Pending">{t}Pending{/t} (<span id="customers_elements_back_in_stock_email_reminders_Pending_number"><img style="height:12.9px" src="art/loading.gif"></span>)</span> 
				</div>
				<div class="table_top_bar space">
				</div>
				{include file='table_splinter.tpl' table_id=6 filter_name=$filter_name6 filter_value=$filter_value6 no_filter=0 } 
				<div id="table6" class="data_table_container dtable btable" style="font-size:85%">
				</div>
			</div>
			<div id="block_email_reminders_products" style="{if $email_reminders_block_view!='products'}display:none;{/if}clear:both;margin:10px 0px 40px 0px">
				<span class="clean_table_title">{t}Products in email reminders{/t}</span> 
				<div class="elements_chooser">
					<span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $products_back_in_stock_elements_email_reminders.Done}selected{/if} label_page_type" id="products_elements_back_in_stock_email_reminders_Done">{t}Completed{/t} (<span id="products_elements_back_in_stock_email_reminders_Done_number"><img style="height:12.9px" src="art/loading.gif"></span>)</span> <span style="float:right;margin-left:20px;" class=" table_type transaction_type state_details {if $products_back_in_stock_elements_email_reminders.Pending}selected{/if} label_page_type" id="products_elements_back_in_stock_email_reminders_Pending">{t}Pending{/t} (<span id="products_elements_back_in_stock_email_reminders_Pending_number"><img style="height:12.9px" src="art/loading.gif"></span>)</span> 
				</div>
				<div class="table_top_bar space">
				</div>
				{include file='table_splinter.tpl' table_id=7 filter_name=$filter_name7 filter_value=$filter_value7 } 
				<div id="table7" class="data_table_container dtable btable" style="font-size:85%">
				</div>
			</div>
		</div>
	</div>
	<div id="block_search_queries" style="{if $block_view!='search_queries'}display:none;{/if}clear:both;margin:10px 0 40px 0;padding:0">
		<div style="padding:0px">
			<div class="buttons small left tabs">
				<button class="indented item {if $search_queries_block_view=='queries'}selected{/if}" id="search_queries_queries" block_id="queries">{t}Queries{/t}</button> <button class=" item {if $search_queries_block_view=='history'}selected{/if}" id="search_queries_history" block_id="history">{t}History{/t}</button> 
			</div>
			<div class="tabs_base">
			</div>
			<div style="padding:0 20px">
				<div id="block_search_queries_queries" style="{if $search_queries_block_view!='queries'}display:none;{/if}clear:both;margin:10px 0px 40px 0px">
					<span class="clean_table_title">{t}Queries{/t}</span> 
					<div class="table_top_bar space">
					</div>
					{include file='table_splinter.tpl' table_id=2 filter_name=$filter_name2 filter_value=$filter_value2 no_filter=0 } 
					<div id="table2" class="data_table_container dtable btable" style="font-size:85%">
					</div>
				</div>
				<div id="block_search_queries_history" style="{if $search_queries_block_view!='history'}display:none;{/if}clear:both;margin:10px 0px 40px 0px">
					<span class="clean_table_title">{t}Query History{/t}</span> 
					<div class="table_top_bar space">
					</div>
					{include file='table_splinter.tpl' table_id=3 filter_name=$filter_name3 filter_value=$filter_value3 no_filter=0 } 
					<div id="table3" class="data_table_container dtable btable" style="font-size:85%">
					</div>
				</div>
			</div>
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
	</script> <span class="clean_table_title">{t}Users{/t}</span> 
		<div class="table_top_bar space">
		</div>
		{include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1 no_filter=0 } 
		<div id="table1" class="data_table_container dtable btable" style="font-size:85%">
		</div>
	</div>
	<div id="block_changelog" style="{if $block_view!='changelog'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
		<span class="clean_table_title">{t}Changelog{/t}</span> 
		<div class="table_top_bar space">
		</div>
		{include file='table_splinter.tpl' table_id=4 filter_name=$filter_name4 filter_value=$filter_value4 } 
		<div id="table4" class="data_table_container dtable btable history">
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
<div id="rppmenu2" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu2 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},2)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu2" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu2 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',2)"> {$menu.menu_label}</a></li>
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
<div id="rppmenu4" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu4 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},4)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu4" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu4 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',4)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu5" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu5 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},5)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu5" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu5 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',5)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu6" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu6 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},6)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu6" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu6 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',6)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu7" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu7 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},7)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu7" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu7 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',7)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>

<div id="rppmenu8" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu8 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},8)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu8" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu8 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',8)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>

<div id="rppmenu9" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu9 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},9)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu9" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu9 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',9)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>

<div id="rppmenu10" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu10 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},10)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu10" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu10 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',10)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>


<div id="dialog_change_page_element_chooser" style="padding:10px 20px 0px 10px">
	<table class="edit" border="0" style="width:200px">
		<tr class="title">
			<td>{t}Group pages by{/t}:</td>
		</tr>
		<tr style="height:5px">
			<td></td>
		</tr>
		<tr>
			<td> 
			<div class="buttons small">
				<button id="pages_element_chooser_section" style="float:none;margin:0px auto;min-width:120px" onclick="change_pages_element_chooser('section')" class="{if $page_elements_type=='section'}selected{/if}"> {t}Sections{/t}</button> 
			</div>
			</td>
		</tr>
		<tr>
			<td> 
			<div class="buttons small">
				<button id="pages_element_chooser_flags" style="float:none;margin:0px auto;min-width:120px" onclick="change_pages_element_chooser('flags')" class="{if $page_elements_type=='flags'}selected{/if}"> {t}Flags{/t}</button> 
			</div>
			</td>
		</tr>
		<tr>
			<td> 
			<div class="buttons small">
				<button id="pages_element_chooser_state" style="float:none;margin:0px auto;min-width:120px" onclick="change_pages_element_chooser('state')" class="{if $page_elements_type=='state'}selected{/if}"> {t}State{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>


<div id="dialog_edit_flag" style="padding:20px 20px 5px 20px">
<table>
	<tr>
 		<td>
 				<input id="edit_flag_page_key" value="" type="hidden">
 				 <input id="edit_flag_table_record_index" value="" type="hidden">


		<div id="site_flags"   class="buttons small" >
			{foreach from=$page_flags_elements_data item=cat key=cat_id name=foo}
				<button  class="buttons" onclick="save_page_flag('Site Flag Key','{$cat.key}')"  id="flag_{$cat.color}"><img src="art/icons/{$cat.img}"  > {$cat.label}</button>
	    	{/foreach}
		</div>
		</td>
	</tr> 
</table>
</div>

{include file='footer.tpl'} 