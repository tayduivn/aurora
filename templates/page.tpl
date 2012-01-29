﻿{include file='header.tpl'} 
<div id="bd" style="padding:0px">
<script type="text/javascript" src="external_libs/amstock/amstock/swfobject.js"></script> 
	<input type="hidden" id="site_key" value="{$site->id}" />
	<input type="hidden" id="site_id" value="{$site->id}" />
	<input type="hidden" id="page_key" value="{$page->id}" />
	<div style="padding:0 20px">
		{include file='assets_navigation.tpl'} 
		<div class="branch">
			<span>{if $user->get_number_stores()>1}<a href="stores.php">{t}Stores{/t}</a> &rarr; <a href="store.php?id={$store->id}">{/if}{$store->get('Store Name')}</a> &rarr; <img style="vertical-align:0px;margin-right:1px" src="art/icons/hierarchy.gif" alt="" /> <a href="site.php?id={$site->id}">{$site->get('Site URL')}</a> &rarr; <img style="vertical-align:-1px;" src="art/icons/layout_bw.png" alt="" /> {$page->get('Page Code')}</span> 
		</div>
		<div class="top_page_menu">
			<div class="buttons" style="float:right">
				{if isset($next)}<img class="next" onmouseover="this.src='art/next_button.gif'" onmouseout="this.src='art/next_button.png'" title="{$next.title}" onclick="window.location='{$next.link}'" src="art/next_button.png" alt="{t}Next{/t}" />{/if} {if $modify}<button onclick="window.location='edit_page.php?id={$page->id}'"><img src="art/icons/vcard_edit.png" alt=""> {t}Edit Page{/t}</button>{/if} <button onclick="window.location='page_preview.php?id={$page->id}&logged=1'"><img src="art/icons/layout.png" alt=""> {t}View Page{/t}</button> 
			</div>
			<div class="buttons" style="float:left">
				{if isset($prev)}<img class="previous" onmouseover="this.src='art/previous_button.gif'" onmouseout="this.src='art/previous_button.png'" title="{$prev.title}" onclick="window.location='{$prev.link}'" src="art/previous_button.png" alt="{t}Previous{/t}" />{/if} <button onclick="window.location='site.php?id={$site->id}'"><img src="art/icons/house.png" alt=""> {t}Site{/t}</button> 
			</div>
			<div style="clear:both">
			</div>
		</div>
		<h1>
			<span class="id">{$page->get('Page Code')}</span> <span style="font-size:90%;color:#777">{$page->get('Page URL')}</span>
		</h1>
	</div>
	<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:5px">
		<li> <span class="item {if $block_view=='details'}selected{/if}" id="details"> <span> {t}Overview{/t}</span></span></li>
		<li> <span class="item {if $block_view=='hits'}selected{/if}" id="hits"> <span> {t}Hits{/t}</span></span></li>
		<li> <span class="item {if $block_view=='visitors'}selected{/if}" id="visitors"> <span> {t}Visitors{/t}</span></span></li>
	</ul>
	<div style="clear:both;width:100%;border-bottom:1px solid #ccc">
	</div>
	<div id="block_details" style="{if $block_view!='details'}display:none;{/if}clear:both;margin:25px 0 40px 0;padding:0 20px">
		<div style="width:450px;float:left;margin-top:0">
			<table id="page_info" class="show_info_product">
				<tr>
					<td style="width:140px">{t}Type{/t}:</td>
					<td>{$page->get_formated_store_section()}</td>
				</tr>
				<tr>
					<td style="width:140px">{t}Header Title{/t}:</td>
					<td>{$page->get('Page Store Title')}</td>
				</tr>
				<tr>
					<td style="width:140px">{t}URL{/t}:</td>
					<td>{$page->get('Page URL')}</td>
				</tr>
				<tr>
					<td style="width:140px">{t}Link Label{/t}:</td>
					<td>{$page->get('Page Short Title')}</td>
				</tr>
			</table>
			<table border="0" id="table_total_visitors" class="show_info_product">
				<tr>
					<td style="width:140px">{t}Total Hits{/t}:</td>
					<td class="number">
					<div>
						{$page->get('Visits')}
					</div>
					</td>
				</tr>
				<tr>
					<td style="width:140px">{t}Unique Visitors{/t}:</td>
					<td class="number">
					<div>
						{$page->get('Unique Visitors')}
					</div>
					</td>
				</tr>
			</table>
			<table border="0" id="table_1day_visitors" class="show_info_product">
				<tr>
					<td style="width:140px">{t}Last 24h Hits{/t}:</td>
					<td class="number">
					<div>
						{$page->get('1 Day Visits')}
					</div>
					</td>
				</tr>
				<tr>
					<td style="width:140px">{t}Last 24h Visitors{/t}:</td>
					<td class="number">
					<div>
						{$page->get('1 Day Unique Visitors')}
					</div>
					</td>
				</tr>
				<tr>
					<td style="width:140px">{t}Current Visitors{/t}:</td>
					<td class="number">
					<div>
						{$page->get('Current Visitors')}
					</div>
					</td>
				</tr>
			</table>
			<table class="show_info_product">
				<tr>
					<td style="width:140px">{t}Parent Pages{/t}:</td>
					<td> 
					<table>
						{foreach from=$page->get_found_in() item=found_in_page} 
						<tr>
							<td style="padding:0">{$found_in_page.found_in_label} <span class="id">(<a href="page.php?id={$found_in_page.found_in_key}">{$found_in_page.found_in_code}</a>)</span></td>
						</tr>
						{/foreach} 
					</table>
					</td>
				</tr>
				<tr>
					<td>{t}Related Pages{/t}:</td>
					<td> 
					<table>
						{foreach from=$page->get_see_also() item=see_also_page} 
						<tr>
							<td style="padding:0">{$see_also_page.see_also_label} <span class="id">(<a href="page.php?id={$see_also_page.see_also_key}">{$see_also_page.see_also_code}</a>)</span></td>
							<td style="padding:0 10px;font-style:italic;color:#777">{$see_also_page.see_also_correlation_formated} {$see_also_page.see_also_correlation_formated_value}</td>
						</tr>
						{/foreach} 
					</table>
					</td>
				</tr>
			</table>
		</div>
		<div style="{if $page->get('Page Upload State')!='Upload'}display:none;{/if}margin-left:20px;width:450px;float:left;position:relative;top:-12px">
			<span style="font-size:11px;color:#777;">{t}Live snapshot{/t}, {$page->get_snapshot_date()}</span> <img id="recapture_page" style="position:relative;top:-1px;cursor:pointer" src="art/icons/camera_bw.png" alt="recapture" /> <img style="width:470px" src="image.php?id={$page->get('Page Snapshot Image Key')}" alt="" /> 
		</div>
		<div style="{if $page->get('Page Upload State')=='Upload'}display:none;{/if}margin-left:20px;width:450px;float:left;position:relative;top:-12px">
			<span style="font-size:11px;color:#777;">{t}Preview snapshot{/t}<span id="capture_preview_date">, {$page->get_preview_snapshot_date()}</span></span> <img id="recapture_preview" style="position:relative;top:-1px;cursor:pointer" src="art/icons/camera_bw.png" alt="recapture" /><img id="recapture_preview_processing" style="display:none;height:12.5px;position:relative;top:-1px;" src="art/loading.png" /> <img id="page_preview_snapshot" style="width:470px" src="image.php?id={$page->get('Page Preview Snapshot Image Key')}" alt="" /> 
		</div>
		<div style="clear:both;margin-bottom:20px">
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
