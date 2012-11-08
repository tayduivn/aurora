{include file='header.tpl'} 
<div id="bd" style="padding:0px">
	<div style="padding:0 20px">
		<input type="hidden" id="to" value="{$to}" />
		<input type="hidden" id="from" value="{$from}" />
		<div class="branch" style="width:280px;float:left;margin:0">
			<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a> &rarr; <a href="reports.php">{t}Reports{/t}</a> &rarr; {t}Out of Stock{/t}</span> 
		</div>
		{include file='calendar_splinter.tpl'} 
		<div style="clear:both">
		</div>
		<h1 style="margin-top:10px">
			{$title}, <span class="id">{$period}</span> <img id="show_calendar_browser" style="cursor:pointer;vertical-align:text-bottom;position:relative;top:-3px;{if $tipo=='f'}display:none{/if}" src="art/icons/calendar.png" alt="calendar" /> 
		</h1>
	</div>
	<div style="float:left;font-size:80%;text-align:center;padding:10px 20px 20px 20px">
		<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
			{t}Out of Stock Parts{/t} 
			<div id="number_out_of_stock_parts" style="font-size:120%;font-weight:800;margin-top:5px;margin-bottom:5px">
				<span style="visibility:hidden">1</span><img src="art/loading.gif" style="height:14px"><span style="visibility:hidden">1</span> 
			</div>
		</div>
			<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
			{t}Deliveries Affected{/t} 
			<div id="number_out_of_stock_dn" style="font-size:120%;font-weight:800;margin-top:5px;margin-bottom:5px">
				<span style="visibility:hidden">1</span><img src="art/loading.gif" style="height:14px"><span style="visibility:hidden">1</span> 
			</div>
		</div>
		
					<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
			{t}Customers Affected{/t} 
			<div id="number_out_of_stock_customers" style="font-size:120%;font-weight:800;margin-top:5px;margin-bottom:5px">
				<span style="visibility:hidden">1</span><img src="art/loading.gif" style="height:14px"><span style="visibility:hidden">1</span> 
			</div>
		</div>
		
	</div>
	<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:15px">
		<li onclick="change_view('transactions')"> <span class="item {if $view=='transactions'}selected{/if}" id="transactions_tab"> <span> {t}Transactions{/t}</span></span></li>
		<li onclick="change_view('parts')"> <span class="item {if $view=='parts'}selected{/if}" id="parts_tab"> <span> {t}Parts{/t}</span></span></li>
	</ul>
	<div style="clear:both;width:100%;border-bottom:1px solid #ccc">
	</div>
	<div style="padding:0 20px 40px 20px">
		<div id="transactions" class="data_table" style="clear:both;margin-top:15px;{if $view!='transactions'}display:none{/if}">
			<span class="clean_table_title">{t}Transactions with Out of Stock{/t}</span> 
			<div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999;margin-bottom:15px">
			</div>
			{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0} 
			<div id="table0" class="data_table_container dtable btable" style="font-size:90%">
			</div>
		</div>
		<div id="parts" class="data_table" style="clear:both;margin-top:15px;{if $view!='parts'}display:none{/if}">
			<span class="clean_table_title">{t}Parts Marked as Out of Stock{/t}</span> 
			<div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999;margin-bottom:15px">
			</div>
			{include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1} 
			<div id="table1" class="data_table_container dtable btable">
			</div>
		</div>
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
{include file='footer.tpl'} 