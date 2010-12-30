{include file='header.tpl'}
<div id="bd" >
{include file='reports_navigation.tpl'}


{include file='calendar_splinter.tpl'}


<div class="branch"> 
  <span><a  href="reports.php">{t}Reports{/t}</a> &rarr; <a  href="reports_section.php?category=Sales%20Reports">{t}Sales Reports{/t}</a>
</div>


<h1 style="clear:left">{$title}</h1>


<table class="report_sales1" id="report_sales_invoices" style="width:700px;{if $view!='invoices'}display:none{/if}">
<tr style="border-bottom:1px solid #ccc;margin-bottom:5px"><td colspan=7>
<div  style="margin-bottom:5px;color:#999;">
<span id="invoices_profits_button"  view="profit" class="state_details" style="margin-right:20px">{t}Profit{/t}</span>
<span class="state_details selected"  style="cursor:default;margin-right:20px">{t}Invoices{/t}</span>
<span id="invoices_corporate_currency_button" currency="corporate" class="state_details currency_corporate {if $currencies=='corporation'}selected{/if}" style="margin-right:5px">{$corporate_symbol}</span>
<span id="invoices_stores_currency_button" currency="stores" class="state_details currency_stores {if $currencies!='corporation'}selected{/if}">({$store_currencies})</span>

</div>
</td></tr>
<tr>
<td style="width:150px">{t}Store{/t}</td>
<td></td><td>{t}Invoices{/t}</td>
    <td style="{if $currencies=='corporation'}display:none{/if}">{t}Net Sales{/t}</td>
    <td style="{if $currencies!='corporation'}display:none{/if}">{t}Net Sales{/t}</td>
    <td></td>
    <td></td>
    <td style="{if $currencies=='corporation'}display:none{/if}">{t}Tax{/t}</td>
    <td style="{if $currencies!='corporation'}display:none{/if}">{t}Tax{/t}</td>
</tr>
{foreach from=$store_data   item=data }
<tr class="geo">
    <td class="label"> {$data.store}</td>
    <td style="text-align:left">{$data.substore}</td>
    <td>{$data.invoices}</td>
    <td class="currency_stores" style="{if $currencies=='corporation'}display:none{/if}">{$data.net}</td>
    <td class="currency_corporate" style="{if $currencies!='corporation'}display:none{/if}">{$data.eq_net}</td>
    <td>{$data.per_eq_net}</td>
    <td>{$data.sub_per_eq_net}</td>
    <td class="currency_stores" style="{if $currencies=='corporation'}display:none{/if}">{$data.tax}</td>
    <td class="currency_corporate" style="{if $currencies!='corporation'}display:none{/if}">{$data.eq_tax}</td></tr>
{/foreach}
</table>


<table  class="report_sales1"id="report_sales_profit" style="width:700px;{if $view!='profits'}display:none{/if}">
<tr style="border-bottom:1px solid #ccc;margin-bottom:5px"><td colspan=7>
<div  style="margin-bottom:5px;;color:#999;">
<span class="state_details selected" style="cursor:default;margin-right:20px">{t}Profit{/t}</span>
<span  id="profits_invoices_button" view="invoices" class="state_details"  style="margin-right:20px">{t}Invoices{/t}</span>
<span id="profits_corporate_currency_button"  currency="corporate" class="state_details {if $currencies=='corporation'}selected{/if}" style="margin-right:5px">{$corporate_symbol}</span>
<span id="profits_stores_currency_button"  currency="stores" class="state_details {if $currencies!='corporation'}selected{/if}">({$store_currencies})</span>
</div>
</td></tr>
<tr><td style="width:150px">{t}Store{/t}</td>
<td></td>
<td style="{if $currencies=='corporation'}display:none{/if}">{t}Revenue{/t}</td>
<td style="{if $currencies!='corporation'}display:none{/if}">{t}Revenue{/t}</td>
<td style="{if $currencies=='corporation'}display:none{/if}">{t}Profit{/t}</td>
<td style="{if $currencies!='corporation'}display:none{/if}">{t}Profit{/t}</td>
<td>{t}Margin{/t}</td></tr>
{foreach from=$store_data_profit   item=data }
<tr class="geo">
    <td class="label"> {$data.store}</td>
    <td style="text-align:left">{$data.substore}</td>
    <td class="currency_stores" style="{if $currencies=='corporation'}display:none{/if}">{$data.net}</td>
    <td class="currency_corporate" style="{if $currencies!='corporation'}display:none{/if}">{$data.eq_net}</td>
    <td class="currency_stores" style="{if $currencies=='corporation'}display:none{/if}">{$data.profit}</td>
    <td class="currency_corporate" style="{if $currencies!='corporation'}display:none{/if}">{$data.eq_profit}</td>
    <td>{$data.margin}</td>
</tr>
{/foreach}
</table>

<div id="plot" class="top_bar" style="position:relative;left:-20px;clear:both;padding:0;margin:0;{if !$display_plot}display:none{/if}">
<div display="none" id="plot_info" keys="{$formated_store_keys}"  invoice_category_keys="{$invoice_category_keys}"   ></div>
    <ul id="plot_chooser" class="tabs" style="margin:0 20px;padding:0 20px "  >
	    <li>
	        <span class="item {if $plot_tipo=='per_store'}selected{/if}" onClick="change_plot(this)" id="plot_per_store" tipo="par_store" category="{$plot_data.per_store.category}" period="{$plot_data.per_store.period}" >
	            <span>Invoices per Store</span>
	        </span>
	    </li>
	    <li>
	        <span class="item {if $plot_tipo=='per_category'}selected{/if}"  id="plot_per_category" onClick="change_plot(this)" tipo="per_category" category="{$plot_data.per_category.category}" period="{$plot_data.per_category.period}" name=""  >
	            <span>{t}Invoices per Category{/t}</span>
	        </span>
	    </li>
    </ul> 
    <script type="text/javascript" src="external_libs/amstock/amstock/swfobject.js"></script>

	<div id="flashcontent" style="clear:both;border:1px solid #ccc" >
		<strong>You need to upgrade your Flash Player</strong>
	</div>

	<script type="text/javascript">
		// <![CDATA[
		
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "amstock", "905", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_asset_sales.xml.php?tipo=store_sales&store_key={$store_keys}"));
		so.addVariable("preloader_color", "#999999");

		
		

		so.write("flashcontent");
		// ]]>
	</script>
  
  
  <div style="clear:both"></div>
    
</div>

{*
<div id="plot" class="top_bar" style="position:relative;left:-20px;clear:both;padding:0;margin:0;{if !$display_plot}display:none{/if}">
<div display="none" id="plot_info" keys="{$formated_store_keys}"  invoice_category_keys="{$invoice_category_keys}"   ></div>
      <ul id="plot_chooser" class="tabs" style="margin:0 20px;padding:0 20px "  >
	<li>
	  <span class="item {if $plot_tipo=='per_store'}selected{/if}" onClick="change_plot(this)" id="plot_per_store" tipo="par_store" category="{$plot_data.per_store.category}" period="{$plot_data.per_store.period}" >
	    <span>Invoices per Store</span>
	  </span>
	</li>
	<li>
	  <span class="item {if $plot_tipo=='per_category'}selected{/if}"  id="plot_per_category" onClick="change_plot(this)" tipo="per_category" category="{$plot_data.per_category.category}" period="{$plot_data.per_category.period}" name=""  >
	    <span>{t}Invoices per Category{/t}</span>
	  </span>
	</li>

      </ul> 
      
      <ul id="plot_options" class="tabs" style="{if $plot_tipo=='pie'}display:none{/if};position:relative;top:.6em;float:right;margin:0 20px;padding:0 20px;font-size:90% "  >
	<li><span class="item"> <span id="plot_category"  category="{$plot_category}" style="xborder:1px solid black;display:inline-block; vertical-align:middle">{$plot_formated_category}</span></span></li>
	<li><span class="item"> <span id="plot_period"   period="{$plot_period}" style="xborder:1px solid black;display:inline-block; vertical-align:middle">{$plot_formated_period}</span></span></li>
    	
      </ul> 

      <div style="clear:both;margin:0 20px;padding:0 20px ;border-bottom:1px solid #999">
      </div>
      
      <div  id="plot_div" class="product_plot"  style="width:865px;height:425px;">
	<iframe id="the_plot" src ="{$plot_page}?{$plot_args}" frameborder=0 height="425" scrolling="no" width="{if $plot_tipo=='pie'}500px{else}100%{/if}"></iframe>
      </div>
      

   
    
  </div>
*}

</div>

{include file='footer.tpl'}

