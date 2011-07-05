{include file='header.tpl'}
<div id="bd" style="padding:0">
<div style="padding: 0 20px">
{include file='locations_navigation.tpl'}


<div style="clear:left">
  <h1 style="padding:10px 0 0 0 ;font-size:140%"><span style="font-weight:800">{t}Part{/t} <span class="id">{$part->get_sku()}</span></span></h1>
    <h2 style="padding:0">{$part->get('Part XHTML Description')}</h2>

  <h3 style="padding:0">{t}Sold as{/t}: {$part->get('Part XHTML Currently Used In')}</h3>
</div>

<div class="" id="block_info"  style="margin-top:20px;width:900px">

  <div id="photo_container" style="float:left;margin-top:0px">
    <div style="border:1px solid #ddd;padding-top:10;width:220px;text-align:center;margin:0 10px 0 0px">
      <div id="imagediv"   style="border:1px solid #ddd;width:200px;height:140px;padding:0px 0;xborder:none;cursor:pointer;;margin: 10px 0 10px 9px">
	<img src="{ if $num_images>0}{$images[$data.principal_image].med}{else}art/nopic.png{/if}"     id="image"   alt="{t}Image{/t}"/>
      </div>
    </div>
    <div style="width:160px;margin:auto;padding-top:5px"  >
      {foreach from=$images item=image  name=foo}
      {if $image.principal==0} <img  style="float:left;border:1px solid#ccc;padding:2px;margin:2px" src="{$image.tb}"  />{/if}
      {/foreach}
    </div>
  </div>

  <div style="width:340px;float:left;margin-left:10px">
  
    <table    class="show_info_product" >
      <td class="aright">
	
	<tr><td>{t}Status{/t}:</td><td>{$part->get('Part Status')}</td></tr>
      <tr><td>{t}Keeping since{/t}:</td><td>{$part->get('Valid From Datetime')}</td></tr>
	<tr><td>{t}Supplied by{/t}:</td><td>{$part->get('Part XHTML Currently Supplied By')}</td></tr>
	<tr><td>{t}Cost{/t}:</td><td>{$part->get('Cost')}</td></tr>
	
	{foreach from=$show_case key=name item=value}
	<tr><td>{$name}:</td><td>{$value}</td></tr>
	{/foreach}
    </table>
    <table   {if !$details}style="display:none"{/if} class="show_info_product">
      <tr >
      <td colspan="2" class="aright" style="padding-right:10px"> <span class="product_info_sales_options" id="info_period"><span id="info_title">{$parts_period_title}</span></span>
      <img id="info_previous" class="previous_button" style="cursor:pointer" src="art/icons/previous.png" alt="<"  title="previous" /> <img id="info_next" class="next_button" style="cursor:pointer"  src="art/icons/next.png" alt=">" tite="next"/></td>
    </tr>
      <tbody id="info_all" style="{if $parts_period!='all'}display:none{/if}">
	<tr><td>{t}Sales{/t}:</td><td class="aright">{$part->get('Total Sold Amount')}</td></tr>
	<tr><td>{t}Profit{/t}:</td><td class="aright">{$part->get('Total Absolute Profit')}</td></tr>
	<tr><td>{t}Margin{/t}:</td><td class="aright">{$part->get('Total Margin')}</td></tr>
	<tr><td>{t}GMROI{/t}:</td><td class="aright">{$part->get('Total GMROI')}</td></tr>

      </tbody>
      <tbody id="info_year"  style="{if $parts_period!='year'}display:none{/if}">
	<tr><td>{t}Sales{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
	<tr><td>{t}Profit{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
	<tr><td>{t}GMROI{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>


      </tbody>
        <tbody id="info_quarter" style="{if $parts_period!='quarter'}display:none{/if}"  >
       <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>
      </tbody>
        <tbody id="info_month" style="{if $parts_period!='month'}display:none{/if}"  >
         <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>
	  
      </tbody>
       <tbody id="info_week" style="{if $parts_period!='week'}display:none{/if}"  >
         <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>
	  
      </tbody>
 </table>
</div>

 <div style="width:280px;float:left;margin-left:20px">
	<table   class="show_info_product" style="width:260px">
		  <tr>
		    <td>{t}Stock{/t}:<br>{$stock_units}</td><td class="stock aright" id="stock">{$part->get('Part Current Stock')}</td>
		  </tr>
<tr>
		    <td>{t}Available for{/t}:</td><td class="stock aright">{$part->get('Part XHTML Available For Forecast')}</td>
		  </tr>
		   
		  
		   
		    {if $nextbuy>0   }<tr><td rowspan="2">{t}Next shipment{/t}:</td><td>{$data.next_buy}</td></tr><tr><td class="noborder">{$data.nextbuy_when}</td>{/if}
		    </tr>
		  </table>
		  {t}Locations{/t}:<div id="add_location_button" style="float:right;font-size:80%;color:#777;margin-right:40px;cursor:pointer"><span onClick="add_location({$part->sku})">{t}Add Location{/t}</span></div>
		  <table  id="part_locations" class="show_info_product" style="width:260px" >
	
			{foreach from=$part->get_locations(true) item=location name=foo }
			<tr id="part_location_tr_{$location.PartSKU}_{$location.LocationKey}">
			<td><a href="location.php?id={$location.LocationKey}">{$location.LocationCode}
			</a></td>
		     <td class="quantity"  id="part_location_quantity_{$location.PartSKU}_{$location.LocationKey}" quantity="{$location.QuantityOnHand}"  >{$location.FormatedQuantityOnHand}</td>
		     <td class="button"   ><img  id="part_location_audit_{$location.PartSKU}_{$location.LocationKey}" src="art/icons/note_edit.png" alt="{t}audit{/t}" onClick="audit({$location.PartSKU},{$location.LocationKey})" /></td>
		     <td class="button"  > <img style="{if $location.QuantityOnHand!=0}display:none{/if}" sku_formated="{$part->get_sku()}" location="{$location.LocationCode}" id="part_location_delete_{$location.PartSKU}_{$location.LocationKey}"  src="art/icons/cross_bw.png" alt="{t}delete{/t}" onClick="delete_part_location({$location.PartSKU},{$location.LocationKey})" /><img style="{if $location.QuantityOnHand==0}display:none{/if}" id="part_location_lost_items_{$location.PartSKU}_{$location.LocationKey}" src="art/icons/package_delete.png" alt="{t}lost{/t}" onClick="lost({$location.PartSKU},{$location.LocationKey})" /></td>
			 <td class="button"  ><img sku_formated="{$part->get_sku()}" location="{$location.LocationCode}" id="part_location_move_items_{$location.PartSKU}_{$location.LocationKey}"  src="art/icons/package_go.png" alt="{t}move{/t}" onClick="move({$location.PartSKU},{$location.LocationKey})" /></td>
			

			</tr>
			{/foreach}
			
		  </table>
		  
		   <table  {if !$details}style="display:none"{/if}  class="show_info_product">
      <tr >
      <td colspan="2" class="aright" style="padding-right:10px"> <span class="product_info_sales_options" id="info_period"><span id="info_title">{$parts_period_title}</span></span>
      <img id="info_previous" class="previous_button" style="cursor:pointer" src="art/icons/previous.png" alt="<"  title="previous" /> <img id="info_next" class="next_button" style="cursor:pointer"  src="art/icons/next.png" alt=">" tite="next"/></td>
    </tr>
       <tbody id="info_all" style="{if $parts_period!='all'}display:none{/if}">
	<tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('Total Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('Total Provided')}</td></tr>
      <tr><td>{t}Adquired{/t}:</td><td class="aright">{$part->get('Total Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('Total Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('Total Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('Total Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('Total Lost')}</td></tr>
     </tbody>
      <tbody id="info_year"  style="{if $parts_period!='year'}display:none{/if}">
      <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>


      </tbody>
        <tbody id="info_quarter" style="{if $parts_period!='quarter'}display:none{/if}"  >
       <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>
      </tbody>
        <tbody id="info_month" style="{if $parts_period!='month'}display:none{/if}"  >
         <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>
	  
      </tbody>
       <tbody id="info_week" style="{if $parts_period!='week'}display:none{/if}"  >
         <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>
	  
      </tbody>
</table>
</div>


</div>

	
</div>
  
 <div style="clear:both"></div>
 
 
<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:25px">
    <li><span class="item {if $view=='description'}selected{/if}"  id="description">  <span> {t}Description{/t}</span></span></li>
    <li><span class="item {if $view=='sales'}selected{/if}"  id="sales">  <span> {t}Sales{/t}</span></span></li>
    <li><span class="item {if $view=='transactions'}selected{/if}"  id="transactions">  <span> {t}Stock Transactions{/t}</span></span></li>
    <li><span class="item {if $view=='history'}selected{/if}"  id="history">  <span> {t}Stock History{/t}</span></span></li>
    <li><span class="item {if $view=='purchase_orders'}selected{/if}"  id="purchase_orders">  <span> {t}Purchase Orders{/t}</span></span></li>
  </ul>
  <div  style="clear:both;width:100%;border-bottom:1px solid #ccc"></div>

 <div id="block_transactions" class="data_table" style="{if $view!='transactions'}display:none;{/if}clear:both;margin-top:20px;;padding:0 20px 20px 30px">
    <span   class="clean_table_title">{t}Part Stock Transactions{/t}</span>
     <div id="table_type" class="table_type">
        <div  style="font-size:90%"   id="transaction_chooser" >
            <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='all_transactions'}selected{/if}"  id="restrictions_all_transactions" table_type="all_transactions"  >{t}All{/t} ({$transactions.all_transactions})</span>
            <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='oip_transactions'}selected{/if}"  id="restrictions_oip_transactions" table_type="oip_transactions"   >{t}OIP{/t} ({$transactions.oip_transactions})</span>
            <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='out_transactions'}selected{/if}"  id="restrictions_out_transactions" table_type="out_transactions"   >{t}Out{/t} ({$transactions.out_transactions})</span>
            <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='in_transactions'}selected{/if}"  id="restrictions_in_transactions" table_type="in_transactions"   >{t}In{/t} ({$transactions.in_transactions})</span>
            <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='audit_transactions'}selected{/if}"  id="restrictions_audit_transactions" table_type="audit_transactions"   >{t}Audits{/t} ({$transactions.audit_transactions})</span>
            <span style="float:right;margin-left:20px" class="table_type transaction_type state_details {if $transaction_type=='move_transactions'}selected{/if}"  id="restrictions_move_transactions" table_type="move_transactions"   >{t}Movements{/t} ({$transactions.move_transactions})</span>
        </div>
     </div>
    
    <div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999;margin-bottom:10px"></div>
   
 
    {include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1   }
    <div class="clean_table_controls" style="" ><div><span  style="margin:0 5px" id="paginator1"></span></div></div>
<div  style="font-size:85%"  id="table1"   class="data_table_container dtable btable "> </div>
</div>
 <div id="block_history" class="data_table" style="{if $view!='history'}display:none;{/if}clear:both;margin-top:20px;;padding:0 20px 30px 20px ">
    <span   class="clean_table_title">{t}Stock History{/t}</span>


 <div id="stock_history_plot"  >
		<strong>You need to upgrade your Flash Player</strong>
	</div>

<script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("external_libs/amstock/amstock/amstock.swf", "amstock", "930", "500", "8", "#FFFFFF");
		so.addVariable("path", "");
		so.addVariable("settings_file", encodeURIComponent("conf/plot_general_candlestick.xml.php?tipo=part_stock_history&sku={$part->sku}"));
		so.addVariable("preloader_color", "#999999");
		so.write("stock_history_plot");
		// ]]>
	</script>

    <span   class="clean_table_title" style="margin-top:30px">{t}Stock History{/t}</span>






     <div id="stock_history_type">
        <span id="stock_history_type_month" table_type="month" style="float:right" class="table_type state_details {if $stock_history_type=='month'}selected{/if}">{t}Monthly{/t}</span>
        <span id="stock_history_type_week" table_type="week" style="float:right;margin-right:10px" class="table_type state_details {if $stock_history_type=='week'}selected{/if}">{t}Weekly{/t}</span>
        <span id="stock_history_type_day" table_type="day" style="float:right;margin-right:10px" class="table_type state_details {if $stock_history_type=='day'}selected{/if}">{t}Daily{/t}</span>
     </div>
    
    <div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999;margin-bottom:10px"></div>
   
 
    {include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0   no_filter=1   }
    <div class="clean_table_controls" style="" ><div><span  style="margin:0 5px" id="paginator0"></span></div></div>
<div  id="table0"  style="font-size:85%"   class="data_table_container dtable btable "> </div>
</div>
 <div id="block_description" class="data_table" style="{if $view!='description'}display:none;{/if}clear:both;margin-top:20px;;padding:0 20px 30px 20px ">
 
 <h2 style="clear:both">{t}Unit Details{/t}</h2>

<div style="float:left;width:450px">
<table    class="show_info_product">

	<tr>
		<td>{t}Unit Type{/t}:</td><td>{$part->get('Units Type')}</td>
	</tr>
	<tr>
		<td>{t}Unit Description{/t}:</td><td>{$part->get('Part Unit Description')}</td>
	</tr>
	<tr>
		<td>{t}Gross Weight{/t}:</td><td>{$part->get('Part Gross Weight')}</td>
	</tr>
	<tr>
		<td>{t}Package Volume{/t}:</td><td>{$part->get('Part Package Volume')}</td>
	</tr>
	<tr>
		<td>{t}Package MOV{/t}:</td><td>{$part->get('Part Package Minimun Orthogonal Volume')}</td>
	</tr>
</table>
</div>
 
 <h2 style="clear:both">{t}Description{/t}</h2>

<div style="float:left;width:450px">
<table    class="show_info_product">

	<tr>
		<td>{t}General Description{/t}:</td><td>{$part->get('Part General Description')}</td>
	</tr>
	<tr>
		<td>{t}Health & Safety{/t}:</td><td>{$part->get('Part Health And Safety')}</td>
	</tr>

</table>


<h2 style="clear:both">{t}Custom Fields{/t}</h2>

<div style="float:left;width:450px">
<table    class="show_info_product">

		  {foreach from=$part_custom_fields key=name item=value}
		  <tr>
		  <td>{$name}:</td><td>{$value}</td>
		  </tr>
		  {/foreach}
		</table>
</div>  
  
</div> 
 
 
 </div>
 <div id="block_sales" class="data_table" style="{if $view!='sales'}display:none;{/if}clear:both;margin-top:20px;;padding:0 20px 30px 20px ">
  
  <div   style="margin-top:20px;width:900px">


  <div style="width:340px;float:left;margin-left:10px">
  
  
    <table   class="show_info_product">
      <tr >
      <td colspan="2" class="aright" style="padding-right:10px"> <span class="product_info_sales_options" id="info_period"><span id="info_title">{$parts_period_title}</span></span>
      <img id="info_previous" class="previous_button" style="cursor:pointer" src="art/icons/previous.png" alt="<"  title="previous" /> <img id="info_next" class="next_button" style="cursor:pointer"  src="art/icons/next.png" alt=">" tite="next"/></td>
    </tr>
      <tbody id="info_all" style="{if $parts_period!='all'}display:none{/if}">
	<tr><td>{t}Sales{/t}:</td><td class="aright">{$part->get('Total Sold Amount')}</td></tr>
	<tr><td>{t}Profit{/t}:</td><td class="aright">{$part->get('Total Absolute Profit')}</td></tr>
	<tr><td>{t}Margin{/t}:</td><td class="aright">{$part->get('Total Margin')}</td></tr>
	<tr><td>{t}GMROI{/t}:</td><td class="aright">{$part->get('Total GMROI')}</td></tr>

      </tbody>
      <tbody id="info_year"  style="{if $parts_period!='year'}display:none{/if}">
	<tr><td>{t}Sales{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
	<tr><td>{t}Profit{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
	<tr><td>{t}GMROI{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>


      </tbody>
        <tbody id="info_quarter" style="{if $parts_period!='quarter'}display:none{/if}"  >
       <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>
      </tbody>
        <tbody id="info_month" style="{if $parts_period!='month'}display:none{/if}"  >
         <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>
	  
      </tbody>
       <tbody id="info_week" style="{if $parts_period!='week'}display:none{/if}"  >
         <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>
	  
      </tbody>
 </table>
</div>

 <div style="width:280px;float:left;margin-left:20px">

		  
		   <table   class="show_info_product">
      <tr >
      <td colspan="2" class="aright" style="padding-right:10px"> <span class="product_info_sales_options" id="info_period"><span id="info_title">{$parts_period_title}</span></span>
      <img id="info_previous" class="previous_button" style="cursor:pointer" src="art/icons/previous.png" alt="<"  title="previous" /> <img id="info_next" class="next_button" style="cursor:pointer"  src="art/icons/next.png" alt=">" tite="next"/></td>
    </tr>
       <tbody id="info_all" style="{if $parts_period!='all'}display:none{/if}">
	<tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('Total Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('Total Provided')}</td></tr>
      <tr><td>{t}Adquired{/t}:</td><td class="aright">{$part->get('Total Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('Total Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('Total Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('Total Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('Total Lost')}</td></tr>
     </tbody>
      <tbody id="info_year"  style="{if $parts_period!='year'}display:none{/if}">
      <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>


      </tbody>
        <tbody id="info_quarter" style="{if $parts_period!='quarter'}display:none{/if}"  >
       <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>
      </tbody>
        <tbody id="info_month" style="{if $parts_period!='month'}display:none{/if}"  >
         <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>
	  
      </tbody>
       <tbody id="info_week" style="{if $parts_period!='week'}display:none{/if}"  >
         <tr><td>{t}Required{/t}:</td><td class="aright">{$part->get('1 Year Acc Required')}</td></tr>
      <tr><td>{t}Provided{/t}:</td><td class="aright">{$part->get('1 Year Acc Provided')}</td></tr>
      <tr><td>{t}Acquired{/t}:</td><td class="aright">{$part->get('1 Year Acc Adquired')}</td></tr>
      <tr><td>{t}Sold{/t}:</td><td class="aright">{$part->get('1 Year Sold')}</td></tr>
      <tr><td>{t}Given{/t}:</td><td class="aright">{$part->get('1 Year Given')}</td></tr>
	  <tr><td>{t}Broken{/t}:</td><td class="aright">{$part->get('1 Year Broken')}</td></tr>
      <tr><td>{t}Lost{/t}:</td><td class="aright">{$part->get('1 Year Lost')}</td></tr>
	  
      </tbody>
</table>
</div>


</div>
  
  </div>
 <div id="block_purchase_orders" class="data_table" style="{if $view!='purchase_orders'}display:none;{/if}clear:both;margin-top:20px;;padding:0 20px 30px 20px "></div>

</div>
</div>


</div>{include file='footer.tpl'}
{include file='stock_splinter.tpl'}

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

