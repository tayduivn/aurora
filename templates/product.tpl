{include file='header.tpl'}
<input id="product_pid" value="{$product->pid}" type="hidden"/>
<div id="bd"  style="padding:0px;{if $product->get('Product Record Type')=='Discontinued'}background-position:300px 30px;background-image:url('art/stamp.discontinued.en.png');background-repeat:no-repeat;{/if}">
<div style="padding:0 20px">

{include file='assets_navigation.tpl'}
<div > 
  <span   class="branch">{if $user->get_number_stores()>1}<a  href="stores.php">{t}Stores{/t}</a> &rarr; {/if}<a href="store.php?id={$store->id}">{$store->get('Store Name')}</a> &rarr; <a href="department.php?id={$product->get('Product Main Department Key')}">{$product->get('Product Main Department Name')}</a> &rarr; <a  href="family.php?id={$product->get('Product Family Key')}">{$product->get('Product Family Code')}</a> &rarr; {$product->get('Product Code')}</span>
</div>
    <h1><span class="id">{$product->get('Product Code')}</span> (<i>{$product->get('Product ID')})</i>, {$product->get('Product Name')} </h1>
    {if $product->get('Product Record Type')=='Historic'}<h2>{t}Historic Product{/t}</h2>{/if}


    
<div  id="block_info"  style="width:890px;position:relative">

	  <div id="photo_container" style="margin-top:0px;float:left">
	    <div style="border:1px solid #ddd;padding-stop:0;width:220px;xheight:230px;text-align:center;margin:0 10px 0 0px">
	     
	      <div id="imagediv"   style="border:1px solid #ddd;width:{$div_img_width}px;height:{$div_img_height}px;padding:5px 5px;xborder:none;cursor:pointer;xbackground:red;margin: 10px 0 10px 9px;vertical-align:middle">
		<img src="{ if $num_images>0}{$images[0].small_url}{else}art/nopic.png{/if}"  style="vertical-align:middle;display:block;" width="{$img_width}px" valign="center" border=1  id="image"   alt="{t}Image{/t}"/>
	      </div>
	    </div>
	    
	    { if $num_images>1}
	    <div style="width:160px;margin:auto;padding-top:5px"  >
	      {foreach from=$images item=image  name=foo}
	      {if $image.is_principal==0}
	      <img  style="float:left;border:1px solid#ccc;padding:2px;margin:2px;cursor:pointer" src="{$image.thumbnail_url}"  title="" alt="" />
	      {/if}
	      {/foreach}
	    </div>
	    {/if}
	    
	    
	  </div>
	  <div style="float:left;margin-left:20px">
	
	  <div   style="width:100%;">
	    <div  style="width:100%;font-size:90%"   >
              <div  style="width:350px;float:left;margin-right:20px">
		<table    class="show_info_product">
		<tr><td>{$product->get('Product Record Type')}</td></tr>
		    
		     <tr >
		      <td>{t}Sale Type{/t}:</td>
		      <td  class="aright">{$product->get_formated_sales_type() }</td>
		</tr>
		     <tr >
		      <td>{t}Web Status{/t}:</td>
		      <td  class="aright">{$product->get_formated_web_state() }</td>
		</tr>
		 
		</table>
		<table    class="show_info_product">
		    <tr>
		      <td>{t}Sell Price{/t}:</td><td  class="price aright">{$product->get_formated_price()}</td>
		    </tr>
		    <tr {if $product->get('Product RRP')==''}style="display:none"{/if} >
		      <td>{t}RRP{/t}:</td><td  class="aright">{$product->get('RRP Per Unit')} {t}each{/t}</td>
		    </tr>
		    
		    <tr><td>{t}Sold Since{/t}:</td><td class="aright">{$product->get('For Sale Since Date')} </td>
		      {if $edit} <td   class="aright" ><input style="text-align:right" class="date_input" size="8" type="text"  id="v_invoice_date"  value="{$v_po_date_invoice}" name="invoice_date" /></td>{/if}
		    </tr>
		  
		</table>

	 



		{if $view_suppliers}
		<table    class="show_info_product" >
		  <tr><td>{t}Suppliers{/t}:</td><td class="aright">{$product->get('Product XHTML Supplied By')}</td></tr>
		</table>
		{/if}
	      </div>
              <div  style="width:250px;float:left">

		{if $data.sale_status=='discontinued'}
		<table  style="margin:0;padding:5px 10px;border-top:1px solid #574017;width:100%;background:#deceb2"  >
		  <tr><td style="font-weight:800;font-size:160%;text-align:center">{t}Discontinued{/t}</td></tr>
		</table>
		{/if}
		{if $data.sale_status=='tobediscontinued'}
		<table  style="margin:0;padding:5px 10px;border-top:1px solid #574017;width:100%;background:#deceb2"  >
		  <tr><td style="font-weight:800;font-size:160%;text-align:center">{t}Discontinued{/t}</td></tr>
		</table>
		{/if}
		{if $data.sale_status=='nosale'}
		<table  style="margin:0;padding:5px 10px;border-top:1px solid #c7cbe0;width:100%;background:#deceb2"  >
		  <tr><td style="font-weight:800;font-size:160%;text-align:center">{t}Not for Sale{/t}</td></tr>
		</table>
		{/if}

		<table   class="show_info_product" style="{if $product->get('Product Record Type')=='Historic'}display:none{/if}">
		  <tr>
		    <td>{t}Available{/t}:<td class="stock aright" id="stock">{$product->get('Product Availability')}</td>
		  </tr>
		  
		    {if $product->get('Product Next Supplier Shipment')   }<tr><td rowspan="2" style="font-size:75%">{$product->get('Product Next Supplier Shipment')}</td></tr>{/if}
		   		    {if $product->get('Product Availability Type')=='Discontinued'   }<tr><td rowspan="2" style="font-size:75%">{t}Discontinued{/t}</td></tr>{/if}

		</table>
		
		
		<table  class="show_info_product" style="{if $product->get('Product Record Type')=='Historic'}display:none{/if};float:right;width:100%"  >
		  <tr><td>{t}Parts{/t}:</td><td class="aright">{$product->get('Product XHTML Parts')}</td></tr>
		  <tr>
		    <td>{t}Locations{/t}:</td><td>
		      {foreach from=$product->get_part_locations(true) item=part_location name=foo }
		      <tr><td style="{if $number_parts<=1}visibility:hidden{/if};padding-right:10px" ><a href="part.php?sku={$part_location.PartSKU}">{$part_location.PartFormatedSKU}</a></td><td class="aright"> <a href="location.php?id={$part_location.LocationKey}"><b>{$part_location.LocationCode}</b></a> ({$part_location.QuantityOnHand})</td></tr>
		      {/foreach}
		</table>
		

		  
		  <table  class="show_info_product">
		    <tr ><td>{t}Unit Weight{/t}:</td><td class="aright">{$product->get('Formated Weight')}</td></tr>
		    {if $data.dimension!=''}
		    <tr><td>{t}Unit Dimensions{/t}:</td><td class="aright">{$data.dimension}</td></tr>
		    {/if}
		     {if $data.oweight!=''}
		    <tr ><td>{t}Outer Weight{/t}:</td><td class="aright">{$data.oweight}{t}Kg{/t}</td></tr>
		    {/if}
		    {if $data.odimension!=''}
		    <tr><td>{t}Outer Dimensions{/t}:</td><td class="aright">{$data.odimension}</td></tr>
		    {/if}
		  </table>
		  <table  class="show_info_product">
		    


<tr>
		      <td>{t}Categories{/t}:</td>
		      <td>{$categories}</td>
		    </tr>
		    <tr>
		      <td>{t}Material{/t}:</td>
		      <td>{$materials}</td>
		    </tr>
		    <tr>
		      <td>{t}Ingredients{/t}:</td>
		      <td>{$ingredients}</td>
		    </tr>
		  </table>
		
              </div>
	    </div>
	  </div>
	</div>
     
      <div style="clear:both"></div>
      
     

    </div> 
    
</div>

<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:15px">
    <li> <span class="item {if $block_view=='details'}selected{/if}"  id="details">  <span> {t}Details{/t}</span></span></li>
     <li> <span class="item {if $block_view=='sales'}selected{/if}"  id="sales">  <span> {t}Sales{/t}</span></span></li>

    <li> <span class="item {if $block_view=='customers'}selected{/if}" id="customers" {if $view_customers}display:none{/if} ><span>  {t}Customers{/t}</span></span></li>
   <li> <span class="item {if $block_view=='orders'}selected{/if}"  id="orders"  {if $view_orders}display:none{/if}  >  <span> {t}Orders{/t}</span></span></li>
    <li> <span class="item {if $block_view=='timeline'}selected{/if}"  id="timeline">  <span> {t}History{/t}</span></span></li>
	<li> <span class="item {if $block_view=='web_site'}selected{/if}"  id="web_site">  <span> {t}Web Site{/t}</span></span></li>

  </ul>
<div  style="clear:both;width:100%;border-bottom:1px solid #ccc"></div>

<div style="padding:0 20px">    
<div id="block_details" style="{if $block_view!='details'}display:none;{/if}clear:both;margin:10px 0 40px 0"></div>
<div id="block_sales" style="{if $block_view!='sales'}display:none;{/if}clear:both;margin:10px 0 40px 0">

 <table    class="show_info_product" style="width:250px">
      <tr >
      <td colspan="2" class="aright" style="padding-right:10px"> <span class="product_info_sales_options" id="info_period"><span id="info_title">{$family_period_title}</span></span>
      <img id="info_previous" class="previous_button" style="cursor:pointer" src="art/icons/previous.png" alt="<"  title="previous" /> <img id="info_next" class="next_button" style="cursor:pointer"  src="art/icons/next.png" alt=">" tite="next"/></td>
    </tr>
       <tbody id="info_all" style="{if $family_period!='all'}display:none{/if}">
	
	 	<tr >
	  <td>{t}Invoices{/t}:</td><td class="aright">{$product->get('Total Invoices')}</td>
	</tr>
	<tr >
	  <td>{t}Sales{/t}:</td><td class=" aright">{$product->get('Total Invoiced Amount')}</td>
	</tr>
	<tr >
	  <td>{t}Profit{/t}:</td><td class=" aright">{$product->get('Total Profit')}</td>
	</tr>
	<tr >
	  <td>{t}Outers{/t}:</td><td class="aright">{$product->get('Total Quantity Delivered')}</td>
	</tr>


      </tbody>

      <tbody id="info_year"  style="{if $family_period!='year'}display:none{/if}">
      
		<tr >
	  <td>{t}Invoices{/t}:</td><td class="aright">{$product->get('1 Year Acc Invoices')}</td>
	</tr>

	<tr >
	  <td>{t}Sales{/t}:</td><td class=" aright">{$product->get('1 Year Acc Invoiced Amount')}</td>
	</tr>
	<tr >
	  <td>{t}Profit{/t}:</td><td class=" aright">{$product->get('1 Year Acc Profit')}</td>
	</tr>
	<tr >
	  <td>{t}Outers{/t}:</td><td class="aright">{$product->get('1 Year Acc Quantity Delivered')}</td>
	</tr>

      </tbody>
        <tbody id="info_quarter" style="{if $family_period!='quarter'}display:none{/if}"  >
      
       <tr >
	     <td>{t}Invoices{/t}:</td><td class="aright">{$product->get('1 Quarter Acc Invoices')}</td>
	    </tr>
      
	<tr >
	  <td>{t}Sales{/t}:</td><td class=" aright">{$product->get('1 Quarter Acc Invoiced Amount')}</td>
	</tr>
	<tr >
	  <td>{t}Profit{/t}:</td><td class=" aright">{$product->get('1 Quarter Acc Profit')}</td>
	</tr>
	<tr >
	  <td>{t}Outers{/t}:</td><td class="aright">{$product->get('1 Quarter Acc Quantity Delivered')}</td>
	</tr>	
      </tbody>
        <tbody id="info_month" style="{if $family_period!='month'}display:none{/if}"  >
       
       <tr >
	     <td>{t}Invoices{/t}:</td><td class="aright">{$product->get('1 Month Acc Invoices')}</td>
	    </tr>
       
	<tr >
	  <td>{t}Sales{/t}:</td><td class=" aright">{$product->get('1 Month Acc Invoiced Amount')}</td>
	</tr>
	<tr >
	  <td>{t}Profit{/t}:</td><td class=" aright">{$product->get('1 Month Acc Profit')}</td>
	</tr>
	<tr >
	  <td>{t}Outers{/t}:</td><td class="aright">{$product->get('1 Month Acc Quantity Delivered')}</td>
	</tr>	
      </tbody>
       <tbody id="info_week" style="{if $family_period!='week'}display:none{/if}"  >
       
       <tr >
	     <td>{t}Invoices{/t}:</td><td class="aright">{$product->get('1 Week Acc Invoices')}</td>
	    </tr>
       
	<tr >
	  <td>{t}Sales{/t}:</td><td class=" aright">{$product->get('1 Week Acc Invoiced Amount')}</td>
	</tr>
	<tr >
	  <td>{t}Profit{/t}:</td><td class=" aright">{$product->get('1 Week Acc Profit')}</td>
	</tr>
	<tr >
	  <td>{t}Outers{/t}:</td><td class="aright">{$product->get('1 Week Acc Quantity Delivered')}</td>
	</tr>	
      </tbody>
 </table>
 
 <div  id="plots" style="clear:both">
<ul class="tabs" id="chooser_ul" style="margin-top:25px">
    <li>
	  <span class="item {if $plot_tipo=='store'}selected{/if}" onClick="change_plot(this)" id="plot_store" tipo="store"    >
	    <span>{t}Product Sales{/t}</span>
	  </span>
	</li>


  </ul>
  
<script type="text/javascript" src="external_libs/amstock/amstock/swfobject.js"></script>

<div id="plot" style="clear:both;border:1px solid #ccc" >
	<div id="single_data_set"  >
		<strong>You need to upgrade your Flash Player</strong>
	</div>
</div>
<script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "amstock", "905", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_asset_sales.xml.php?tipo=product_id_sales&product_id={$product->pid}"));
		so.addVariable("preloader_color", "#999999");
		so.write("plot");
		// ]]>
	</script>
  
  
  <div style="clear:both"></div>
</div>

 
</div>

<div id="block_timeline" style="{if $block_view!='timeline'}display:none;{/if}clear:both;margin:10px 0 40px 0">
    
    
<div  class="data_table" >
    <span id="table_title" class="clean_table_title">{t}Product Code Timeline{/t}</span>
    {include file='table_splinter.tpl' table_id=3 filter_name=$filter_name3 filter_value=$filter_value3}
    <div  id="table3"   class="data_table_container dtable btable "> </div>
  </div>
  
    <div  id="block_history" class="data_table" style="{if $display.history==0}display:none;{/if}clear:both;margin:25px 0px">
    <span id="table_title" class="clean_table_title">{t}Product History{/t}</span>
    {include file='table_splinter.tpl' table_id=2 filter_name=$filter_name2 filter_value=$filter_value2}
    <div  id="table2"   class="data_table_container dtable btable "> </div>
  </div>


</div>

<div id="block_web_site" style="{if $block_view!='web_site'}display:none;{/if}clear:both;margin:10px 0 40px 0">

<h2 style="clear:both">{t}Web Site Details{/t}</h2>
<div style="float:left;width:450px">
<table    class="show_info_product">
{if $web_site.available}
		<tr ><td>{t}Page URL{/t}:</td><td>{$web_site.url}</td></tr>
		<tr ><td>{t}Page Type{/t}:</td><td>{$web_site.type}</td></tr>
{else}
<tr ><td>{t}No web page exist{/t}:</td></tr>
{/if}
</table>
</div>

</div>

<div  id="block_orders" class="data_table"  style="{if $block_view!='orders'}display:none;{/if}clear:both;margin:10px 0 40px 0">
 {if $view_orders} 
    <span id="table_title" class="clean_table_title">{t}Orders with this Product{/t}</span>
    {include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0}
    <div  id="table0"   class="data_table_container dtable btable "> </div>
   {/if}
 </div>
 <div  id="block_customers" class="data_table"  style="{if $block_view!='customers'}display:none;{/if}clear:both;margin:10px 0 40px 0">
   
     
      <table    class="show_info_product" style="width:250px">
      <tr >
      <td colspan="2" class="aright" style="padding-right:10px"> <span class="product_info_sales_options" id="info_period"><span id="info_title">{$family_period_title}</span></span>
      <img id="info_previous" class="previous_button" style="cursor:pointer" src="art/icons/previous.png" alt="<"  title="previous" /> <img id="info_next" class="next_button" style="cursor:pointer"  src="art/icons/next.png" alt=">" tite="next"/></td>
    </tr>
       <tbody id="info_all" style="{if $family_period!='all'}display:none{/if}">
	 <tr >
	  <td>{t}Customers{/t}:</td><td class="aright">{$product->get('Total Customers')}</td>
	</tr>
	 	


      </tbody>

      <tbody id="info_year"  style="{if $family_period!='year'}display:none{/if}">
      	<tr >
	  <td>{t}Customers{/t}:</td><td class="aright">{$product->get('1 Year Acc Customers')}</td>
	</tr>
	

      </tbody>
        <tbody id="info_quarter" style="{if $family_period!='quarter'}display:none{/if}"  >
         <tr >
	  <td>{t}Customers{/t}:</td><td class="aright">{$product->get('1 Quarter Acc Customers')}</td>
	</tr>
     
      </tbody>
        <tbody id="info_month" style="{if $family_period!='month'}display:none{/if}"  >
        <tr >
	  <td>{t}Customers{/t}:</td><td class="aright">{$product->get('1 Month Acc Customers')}</td>
	</tr>
    
      </tbody>
       <tbody id="info_week" style="{if $family_period!='week'}display:none{/if}"  >
        <tr >
	  <td>{t}Customers{/t}:</td><td class="aright">{$product->get('1 Week Acc Customers')}</td>
	</tr>
      
      </tbody>
 </table>
     
     
     
   <span id="table_title" class="clean_table_title">{t}Customer who order this Product{/t}</span>
    {include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1}
  <div  id="table1"   class="data_table_container dtable btable "> </div>
 
  </div>





</div>

</div>
<div id="web_status_menu" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">

      {foreach from=$web_status_menu key=status_id item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_web_status('{$status_id}')"> {$menu}</a></li>
      {/foreach}
    </ul>
  </div>
</div>

</div>{include file='footer.tpl'}

<div id="rppmenu0" class="yuimenu" >
  <div class="bd">
    <ul class="first-of-type">
       <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
      {foreach from=$paginator_menu0 item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_rpp_with_totals({$menu},0)"> {$menu}</a></li>
      {/foreach}
    </ul>
  </div>
</div>
<div id="filtermenu0" class="yuimenu" >
  <div class="bd">
    <ul class="first-of-type">
      <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
      {foreach from=$filter_menu0 item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_filter('{$menu.db_key}','{$menu.label}',0)"> {$menu.menu_label}</a></li>
      {/foreach}
    </ul>
  </div>
</div>

<div id="rppmenu1" class="yuimenu" >
  <div class="bd">
    <ul class="first-of-type">
       <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
      {foreach from=$paginator_menu1 item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_rpp_with_totals({$menu},1)"> {$menu}</a></li>
      {/foreach}
    </ul>
  </div>
</div>
<div id="filtermenu1" class="yuimenu" >
  <div class="bd">
    <ul class="first-of-type">
      <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
      {foreach from=$filter_menu1 item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_filter('{$menu.db_key}','{$menu.label}',1)"> {$menu.menu_label}</a></li>
      {/foreach}
    </ul>
  </div>
</div>

<div id="rppmenu2" class="yuimenu" >
  <div class="bd">
    <ul class="first-of-type">
       <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
      {foreach from=$paginator_menu2 item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_rpp_with_totals({$menu},2)"> {$menu}</a></li>
      {/foreach}
    </ul>
  </div>
</div>
<div id="filtermenu2" class="yuimenu" >
  <div class="bd">
    <ul class="first-of-type">
      <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
      {foreach from=$filter_menu2 item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_filter('{$menu.db_key}','{$menu.label}',2)"> {$menu.menu_label}</a></li>
      {/foreach}
    </ul>
  </div>
</div>

