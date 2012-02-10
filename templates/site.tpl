﻿{include file='header.tpl'} 
<div id="bd" style="padding:0px">
<script type="text/javascript" src="external_libs/amstock/amstock/swfobject.js"></script> 
	<input type="hidden" id="site_key" value="{$site->id}" />
	<input type="hidden" id="site_id" value="{$site->id}" />
	<div style="padding:0 20px">
		{include file='assets_navigation.tpl'} 
		<div class="branch">
			<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home"/></a>&rarr; 
			{if $user->get_number_websites()>1}<a href="sites.php">{t}Websites{/t}</a>  &rarr; {/if}
			<img style="vertical-align:0px;margin-right:1px" src="art/icons/hierarchy.gif" alt="" /> {$site->get('Site URL')} (<a href="store.php?id={$store->id}">{$store->get('Store Code')}</a>)</span> 
		</div>
		<div class="top_page_menu">
			<div class="buttons" style="float:right">
				{if $modify} <button onclick="window.location='edit_site.php?id={$site->id}'"><img src="art/icons/vcard_edit.png" alt=""> {t}Edit Site{/t}</button> {/if} 
			</div>
			<div class="buttons" style="float:left">
			</div>
			<div style="clear:both">
			</div>
		</div>
		<h1>
			{$site->get('Site Name')} ({$site->get('Site URL')})
		</h1>
	</div>
	<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:5px">
		<li> <span class="item {if $block_view=='details'}selected{/if}" id="details"> <span> {t}Overview{/t}</span></span></li>
		<li> <span class="item {if $block_view=='pages'}selected{/if}" id="pages"> <span> {t}Pages{/t}</span></span></li>
		<li> <span class="item {if $block_view=='hits'}selected{/if}" id="hits"> <span> {t}Hits{/t}</span></span></li>
		<li> <span class="item {if $block_view=='visitors'}selected{/if}" id="visitors"> <span> {t}Visitors{/t}</span></span></li>
	</ul>
	<div style="clear:both;width:100%;border-bottom:1px solid #ccc">
	</div>
	<div style="padding:0 20px">
		<div id="block_details" style="{if $block_view!='details'}display:none;{/if}clear:both;margin:20px 0 40px 0">
			<div style="width:350px;float:left">
				<table class="show_info_product">
					<tr>
						<td>{t}Name{/t}:</td>
						<td>{$site->get('Site Name')}</td>
					</tr>
					<tr>
						<td>{t}Home Page{/t}:</td>
						<td>{$site->get('Site URL')}</td>
					</tr>
				</table>
				<table class="show_info_product">
					<tr>
						<td>{t}Number Pages{/t}:</td>
						<td class="number">
						<div>
							{$site->get('Number Pages')}
						</div>
						</td>
					</tr>
				</table>
			</div>
			<div style="margin-left:20px;width:350px;float:left">
				<table class="show_info_product">
					<tr>
						<td>{t}Total Hits{/t}:</td>
						<td class="number">
						<div>
							{$site->get('Visits')}
						</div>
						</td>
					</tr>
					<tr>
						<td>{t}Unique Visitors{/t}:</td>
						<td class="number">
						<div>
							{$site->get('Unique Visitors')}
						</div>
						</td>
					</tr>
				</table>
				<table class="show_info_product">
					<tr>
						<td>{t}Last 24h Hits{/t}:</td>
						<td class="number">
						<div>
							{$site->get('1 Day Visits')}
						</div>
						</td>
					</tr>
					<tr>
						<td>{t}Last 24h Visitors{/t}:</td>
						<td class="number">
						<div>
							{$site->get('1 Day Unique Visitors')}
						</div>
						</td>
					</tr>
					<tr>
						<td>{t}Current Visitors{/t}:</td>
						<td class="number">
						<div>
							{$site->get('Current Visitors')}
						</div>
						</td>
					</tr>
				</table>
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
			<div class="buttons small clusters">
					<button class="selected" id="change_pages_table_type">{$pages_table_type_label}</button> 
				<div style="clear:both">
					</div>
				</div>
			
			
			{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 no_filter=0 } 
				<div id="thumbnails0" class="thumbnails" style="border-top:1px solid SteelBlue;clear:both;{if $pages_table_type!='thumbnails'}display:none{/if}">
				</div>
				<div id="table0"  class="data_table_container dtable btable" style="{if $pages_table_type=='thumbnails'}display:none{/if};font-size:85%">
				</div>

			</div>
		</div>
		<div id="block_hits" style="{if $block_view!='hits'}display:none;{/if}clear:both;margin:20px 0 40px 0">
			<div id="plot1" style="clear:both;border:1px solid #ccc">
				<div id="single_data_set">
					<strong>You need to upgrade your Flash Player</strong> 
				</div>
			</div>
<script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "amstock", "905", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_general_timeseries.xml.php?tipo=site_hits&site_key={$site->id}"));
		so.addVariable("preloader_color", "#999999");
		so.write("plot1");
		// ]]>
	</script> 
		</div>
		<div id="block_visitors" style="{if $block_view!='visitors'}display:none;{/if}clear:both;margin:20px 0 40px 0">
			<div id="plot2" style="clear:both;border:1px solid #ccc">
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
	</script> 
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
{include file='footer.tpl'} 
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
