{include file='header.tpl'}
<div id="bd" >

  <span class="nav2 onright"><a href="stores.php">&uarr; {t}Up{/t}</a></span>
  {if $modify}<span class="nav2 onright"><a href="store.php?edit=1"  >{t}Edit{/t}</a></span>{/if}
  
  <span class="nav2 onleft"><a class="selected" href="store.php">{$store->get('Store Code')}</a></span>
  <span class="nav2 onleft"><a href="families.php?store_key={$store->id}">{t}Families{/t}</a></span>
  <span class="nav2 onleft"><a href="products.php?store_key={$store->id}">{t}Products{/t}</a></span>
  <span class="nav2 onleft"><a href="categories.php?store_key={$store->id}">{t}Categories{/t}</a></span>
  <span class="nav2 onleft"><a href="parts.php?store_key={$store->id}">{t}Parts{/t}</a></span>

  <div class="search_box" >
    <span class="search_title">{t}Product Code{/t}:</span> <input size="8" class="text search" id="product_search" value="" name="search"/><img align="absbottom" id="product_submit_search" class="submitsearch" src="art/icons/zoom.png" alt="Submit search"><br/>
     <span  class="search_msg"   id="product_search_msg"    ></span> <span  class="search_sugestion"   id="product_search_sugestion"    ></span>
     <br/>
     <span  id="show_details" style="float:right;{if $show_details}display:none{/if}" class="state_details"  onClick="show_details()" >{t}Show details{/t}</span>
  </div>
  
  <div style="clear:left;margin:0 20px">
    <h1>{$store->get('Store Name')} ({$store->get('Store Code')})</h1>
  </div>
  
  <div id="details" class="top_bar" style="{if !$show_details}display:none;{/if}">
    <div  class="details">
      <div id="details_general"  {if $view!='general'}style="display:none"{/if}>
	
	<table class="show_info_product" style="width:20em">
	  <tr>
	    <td>{t}Departments{/t}:</td><td class="aright">{$departments}</td>
	  </tr>
	  <tr>
	    <td>{t}Product Families{/t}:</td><td class="aright">{$store->get('Families')}</td>
	  </tr>
	  <tr>
	    <td>{t}Products for sale{/t}:</td><td class="aright">{$store->get('For Sale Products')}</td>
	  </tr>
	</table>
      </div>
      <div id="details_stock"  {if $view!='stock'}style="display:none"{/if}>
	<table class="show_info_product"  >
	  <tr>
	    <td>{t}Stock Value{/t}:</td><td class="aright">{$stock_value}</td>
	  </tr>
	</table>
      </div>
      <div id="details_sales"  {if $view!='sales'}style="display:none"{/if}>
	<table  >
	  <tr>
	    <td>{t}Total Sales{/t}:</td><td class="aright">{$total_sales}</td>
	  </tr>
	</table>
      </div>
      
      <div display="none" id="plot_info" keys="{$store->id}" ></div>
      <ul id="plot_chooser" class="tabs" style="margin:0 20px;padding:0 20px "  >
	<li>
	  <span class="item {if $plot_tipo=='store'}selected{/if}" onClick="change_plot(this)" id="plot_store" tipo="store" category="{$plot_data.store.category}" period="{$plot_data.store.period}" >
	    <span>{$store->get('Store Code')} {t}Store{/t}</span>
	  </span>
	</li>
	<li>
	  <span class="item {if $plot_tipo=='top_departments'}selected{/if}"  id="plot_top_departments" onClick="change_plot(this)" tipo="top_departments" category="{$plot_data.top_departments.category}" period="{$plot_data.top_departments.period}" name=""  >
	    <span>{t}Top Departments{/t}</span>
	  </span>
	</li>
	<li>
	  <span class="item {if $plot_tipo=='pie'}selected{/if}" onClick="change_plot(this)" id="plot_pie" tipo="pie"   category="{$plot_data.pie.category}" period="{$plot_data.pie.period}" forecast="{$plot_data.pie.forecast}" date="{$plot_data.pie.date}"  >
	    <span>{t}Department's Pie{/t}</span>
	  </span>
	</li>
      </ul> 
      
      <ul id="plot_options" class="tabs" style="{if $plot_tipo=='pie'}display:none{/if};position:relative;top:.6em;float:right;margin:0 20px;padding:0 20px;font-size:90% "  >
	<li><span class="item"> <span id="plot_category"  category="{$plot_category}" style="xborder:1px solid black;display:inline-block; vertical-align:middle">{$plot_formated_category}</span></span></li>
	<li><span class="item"> <span id="plot_period"   period="{$plot_period}" style="xborder:1px solid black;display:inline-block; vertical-align:middle">{$plot_formated_period}</span></span></li>
      </ul> 

      <div style="clear:both;margin:0 20px;padding:0 20px ;border-bottom:1px solid #999">
      </div>

      <div id="pie_options"  style="{if $plot_tipo!='pie'}display:none;{/if}border:1px solid #ddd;float:right;margin:20px 0px;margin-right:40px;width:300px;padding:10px">
	<table id="pie_category_options" style="float:none;margin-bottom:10px;margin-left:30px"  class="options_mini" >
	  <tr>
	    <td  {if $plot_data.pie.category=='sales'}class="selected"{/if} period="sales"  id="pie_category_sales" >{t}Sales{/t}</td>
	    <td {if $plot_data.pie.category=='profit'}class="selected"{/if}  period="profit"  id="pie_category_profit"  >{t}Profit{/t}</td>
	  </tr>
	</table>
	<table id="pie_period_options" style="float:none;margin-bottom:20px;margin-left:30px"  class="options_mini" >
	  <tr>
	    <td  {if $plot_data.pie.period=='all'}class="selected"{/if} period="all"  id="pie_period_all" onclick="change_plot_period('all')" >{t}All{/t}</td>
	    <td {if $plot_data.pie.period=='y'}class="selected"{/if}  period="year"  id="pie_period_year" onclick="change_plot_period('y')"  >{t}Year{/t}</td>
	    <td  {if $plot_data.pie.period=='q'}class="selected"{/if}  period="quarter"  id="pie_period_quarter" onclick="change_plot_period('q')"  >{t}Quarter{/t}</td>
	    <td {if $plot_data.pie.period =='m'}class="selected"{/if}  period="month"  id="pie_period_month" onclick="change_plot_period('m')"  >{t}Month{/t}</td>
	    <td  {if $plot_data.pie.period=='w'}class="selected"{/if} period="week"  id="pie_period_week" onclick="change_plot_period('w')"  >{t}Week{/t}</td>
	  </tr>
	</table>
	<div style="font-size:90%;margin-left:30px">
	  <span>{$plot_formated_period}</span>: <input class="text" type="text" value="{$plot_formated_date}" style="width:6em"/> <img style="display:none" src="art/icons/chart_pie.png" alt="{t}update{/t}"/>
	</div>
      </div>
      
      <div  id="plot_div" class="product_plot"  style="width:865px;xheight:325px;">
	<iframe id="the_plot" src ="{$plot_page}?{$plot_args}" frameborder=0 height="325" scrolling="no" width="{if $plot_tipo=='pie'}500px{else}100%{/if}"></iframe>
	
      </div>
      <div style="text-align:right">
	<span  class="state_details"  onClick="hide_details()"> {t}Hide details{/t}</span>
      </div>
     </div>
   
    <div style="clear:both"></div>
  </div>

  <div class="data_table" xstyle="margin:25px 20px">
    <span   class="clean_table_title">{t}Departments{/t}</span>
    <div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999"></div>
    
    <span   style="float:right;margin-left:80px" class="state_details" state="{$show_percentages}"  id="show_percentages"  atitle="{if $show_percentages}{t}Normal Mode{/t}{else}{t}Comparison Mode{/t}{/if}"  >{if $show_percentages}{t}Comparison Mode{/t}{else}{t}Normal Mode{/t}{/if}</span>
    
    
    
    <table style="float:left;margin:0 0 5px 0px ;padding:0"  class="options" >
      <tr><td  {if $view=='general'}class="selected"{/if} id="general" >{t}General{/t}</td>
	{if $view_stock}<td {if $view=='stock'}class="selected"{/if}  id="stock"  >{t}Stock{/t} {$view}</td>{/if}
	{if $view_sales}<td  {if $view=='sales'}class="selected"{/if}  id="sales"  >{t}Sales{/t}</td>{/if}
      </tr>
    </table>
	
	<table id="period_options" style="float:left;margin:0 0 0 20px ;padding:0{if $view!='sales' };display:none{/if}"  class="options_mini" >
	  <tr>
	    
	    <td  {if $period=='all'}class="selected"{/if} period="all"  id="period_all" >{t}All{/t}</td>
	   <td {if $period=='year'}class="selected"{/if}  period="year"  id="period_year"  >{t}1Yr{/t}</td>
	    <td  {if $period=='quarter'}class="selected"{/if}  period="quarter"  id="period_quarter"  >{t}1Qtr{/t}</td>
	    <td {if $period=='month'}class="selected"{/if}  period="month"  id="period_month"  >{t}1M{/t}</td>
	    <td  {if $period=='week'}class="selected"{/if} period="week"  id="period_week"  >{t}1W{/t}</td>
	  </tr>
      </table>
	
	<table  id="avg_options" style="float:left;margin:0 0 0 25px ;padding:0 {if $view!='sales'};display:none{/if}"  class="options_mini" >
	  <tr>
	    <td {if $avg=='totals'}class="selected"{/if} avg="totals"  id="avg_totals" >{t}Totals{/t}</td>
	    <td {if $avg=='month'}class="selected"{/if}  avg="month"  id="avg_month"  >{t}M AVG{/t}</td>
	    <td {if $avg=='week'}class="selected"{/if}  avg="week"  id="avg_week"  >{t}W AVG{/t}</td>
	    
	  </tr>
       </table>


    <div  class="clean_table_caption"  style="clear:both;">
      <div style="float:left;"><div id="table_info0" class="clean_table_info"><span id="rtext0"></span> <span class="rtext_rpp" id="rtext_rpp0"></span> <span class="filter_msg"  id="filter_msg0"></span></div></div>
      <div class="clean_table_filter" id="clean_table_filter0"><div class="clean_table_info"><span id="filter_name0">{$filter_name}</span>: <input style="border-bottom:none" id='f_input0' value="{$filter_value}" size=10/><div id='f_container0'></div></div></div>
      <div class="clean_table_controls" style="" ><div><span  style="margin:0 5px" id="paginator0"></span></div></div>
    </div>
    <div  id="table0"   class="data_table_container dtable btable with_total"> </div>
  </div>
  
</div> 

<div id="rppmenu" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">
       <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
      {foreach from=$paginator_menu item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_rpp({$menu},0)"> {$menu}</a></li>
      {/foreach}
    </ul>
  </div>
</div>

<div id="filtermenu" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">
      <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
      {foreach from=$filter_menu item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_filter('{$menu.db_key}','{$menu.label}',0)"> {$menu.menu_label}</a></li>
      {/foreach}
    </ul>
  </div>
</div>

<div id="plot_period_menu" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">
      <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Plot frequency{/t}:</li>
      {foreach from=$plot_period_menu item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_plot_period('{$menu.period}')"> {$menu.label}</a></li>
      {/foreach}
    </ul>
  </div>
</div>

<div id="plot_category_menu" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">
      <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Plot Type{/t}:</li>
      {foreach from=$plot_category_menu item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_plot_category('{$menu.category}')"> {$menu.label}</a></li>
      {/foreach}
    </ul>
  </div>
</div>


{include file='footer.tpl'}
