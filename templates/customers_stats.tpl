{include file='header.tpl'}
<div id="bd" style="padding:0px">
<div style="padding:0 20px">
{include file='contacts_navigation.tpl'}

<h2 style="clear:left">{t}Customers Information{/t} ({$store->get('Store Code')})</h2>

<p style="wifth:500px;margin-top:10px">
{$overview_text}
</p>
</div>

<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:25px">
    <li> <span class="item {if $view=='population'}selected{/if}"  id="population">  <span> {t}Contacts{/t}</span></span></li>
        <li> <span class="item {if $view=='customers'}selected{/if}"  id="customers">  <span> {t}Customers{/t}</span></span></li>

           <li> <span class="item {if $view=='orders'}selected{/if}"  id="orders">  <span> {t}Orders{/t}</span></span></li>


    <li> <span class="item {if $view=='data'}selected{/if}" id="data"  ><span>  {t}Data Integrity{/t}</span></span></li>
    <li> <span class="item {if $view=='geo'}selected{/if}"  id="geo">  <span> {t}Geographic Distribution{/t}</span></span></li>
  </ul>
  <div  style="clear:both;width:100%;border-bottom:1px solid #ccc"></div>

<div id="block_customers" style="{if $view!='customers'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">

<div style="width:25em;float:left;margin-left:20px">
  <table    class="show_info_product">
       <tbody id="info_all" style="">
	 <tr >
	  <td>{t}Active Customers{/t}:</td><td class="aright">{$store->get('Active Customers')}</td>
	</tr>
	 	<tr >
	  <td>{t}New Customer (1 month){/t}:</td><td class="aright">{$store->get('1 Month New Customers')}</td>
	</tr>
	<tr >
	  <td>{t}Lost Customers (1 month){/t}:</td><td class=" aright">{$store->get('1 Month Lost Customers')}</td>
	</tr>
    </tbody>
      
      </table>
</div>

	<div id="number_of_customers"  >
		<strong>You need to upgrade your Flash Player</strong>
	</div>

<script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "amstock", "905", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_general_candlestick.xml.php?tipo=number_of_customers&store_key={$store->id}"));
		so.addVariable("preloader_color", "#999999");
		so.write("number_of_customers");
		// ]]>
	</script>
</div>

<div id="block_orders" style="{if $view!='orders'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
	<div style="float:left" id="plot_orders">
		<strong>You need to upgrade your Flash Player</strong>
	</div>

	<script type="text/javascript">
		// <![CDATA[		
		var so = new SWFObject("external_libs/ampie/ampie/ampie.swf", "ampie", "550", "550", "1", "#FFFFFF");
		so.addVariable("path", "external_libs/ampie/ampie/");
		so.addVariable("settings_file", encodeURIComponent("conf/pie_settings.xml.php"));                // you can set two or more different settings files here (separated by commas)
		so.addVariable("data_file", encodeURIComponent("plot_data.csv.php?tipo=customers_orders_pie&store_key={$store->id}")); 
		so.addVariable("loading_settings", "LOADING SETTINGS");                                         // you can set custom "loading settings" text here
		so.addVariable("loading_data", "LOADING DATA");                                                 // you can set custom "loading data" text here

		so.write("plot_orders");
		// ]]>
	</script>
</div>


<div id="block_population" style="{if $view!='population'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
<div style="width:25em;float:left;margin-left:20px">
  <table    class="show_info_product">
       <tbody id="info_all" style="">
	 <tr >
	  <td>{t}Contacts{/t}:</td><td class="aright">{$store->get('Total Customer Contacts')}</td>
	</tr>
	 	<tr >
	  <td>{t}New Contacts (1 month){/t}:</td><td class="aright">{$store->get('1 Month New Customers Contacts')}</td>
	</tr>
	
    </tbody>
      
      </table>
</div>

	<div id="number_of_contacts"  >
		<strong>You need to upgrade your Flash Player</strong>
	</div>

<script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "amstock", "905", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_general_timeseries.xml.php?tipo=number_of_contacts&store_key={$store->id}"));
		so.addVariable("preloader_color", "#999999");
		so.write("number_of_contacts");
		// ]]>
	</script>


</div>
<div id="block_data" style="{if $view!='data'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
	<div style="float:left" id="plot_data">
		<strong>You need to upgrade your Flash Player</strong>
	</div>

	<script type="text/javascript">
		// <![CDATA[		
		var so = new SWFObject("external_libs/ampie/ampie/ampie.swf", "ampie", "550", "450", "1", "#FFFFFF");
		so.addVariable("path", "external_libs/ampie/ampie/");
		so.addVariable("settings_file", encodeURIComponent("conf/pie_settings.xml.php"));                // you can set two or more different settings files here (separated by commas)
		so.addVariable("data_file", encodeURIComponent("plot_data.csv.php?tipo=customers_data_completeness_pie&store_key={$store->id}")); 
		so.addVariable("loading_settings", "LOADING SETTINGS");                                         // you can set custom "loading settings" text here
		so.addVariable("loading_data", "LOADING DATA");                                                 // you can set custom "loading data" text here

		so.write("plot_data");
		// ]]>
	</script>

</div>
<div id="block_geo" style="{if $view!='geo'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">

<div id="map_countries" style="width:700px;height:480px;">
		<strong>You need to upgrade your Flash Player</strong>
	</div>
<script type="text/javascript">
		// <![CDATA[		
		var so = new SWFObject("external_libs/ammap/ammap/ammap.swf", "ammap", "100%", "100%", "8", "#FFFFFF");
        so.addVariable("path", "external_libs/ammap/ammap/");
		so.addVariable("data_file", escape("map_data_world_countries.xml.php?report=customer_total_contacts&store_key={$store->id}"));
        so.addVariable("settings_file", escape("conf/world_heatmap_settings.xml"));		
		so.addVariable("preloader_color", "#999999");
		so.write("map_countries");
		
		
		
		// ]]>
	</script>
</div>

</div> 


{include file='footer.tpl'}
