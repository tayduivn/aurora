{include file='header.tpl'}
<div id="bd" >

 <span class="nav2 onleft"><a class="selected" href="stores.php">{t}Stores{/t}</a></span>
  <span class="nav2 onleft"><a  href="departments.php">{t}Departments{/t}</a></span>
  <span class="nav2 onleft"><a href="families.php">{t}Families{/t}</a></span>
  <span class="nav2 onleft"><a href="products.php?parent=none">{t}Products{/t}</a></span>
  <span class="nav2 onleft"><a href="categories.php">{t}Categories{/t}</a></span>
  {if $view_parts}<span class="nav2 onleft"><a href="parts.php">{t}Parts{/t}</a></span>{/if}

  <div class="search_box">
    <span class="search_title">{t}Product Code{/t}:</span> <input size="8" class="text search" id="product_search" value="" name="search"/><img align="absbottom" id="product_submit_search" class="submitsearch" src="art/icons/zoom.png" alt="Submit search"><br/>
    <span  class="search_msg"   id="product_search_msg"    ></span> <span  class="search_sugestion"   id="product_search_sugestion"    ></span>
    <br/>
    {if $modify}<a   href="stores.php?edit=1"  style="float:right;margin-left:15px" class="state_details"  >{t}Edit{/t}</a>{/if}
  </div>
  
  <div style="clear:left;">
    <h1>{t}Corporate Overview{/t}</h1>
  </div>

<div class="data_table" style="clear:both">
    <span class="clean_table_title">{t}Stores{/t}</span>
 <div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999"></div>
 <span   style="float:right;margin-left:80px" class="state_details"  id="change_display_mode" >{$display_mode_label}</span>
<table style="float:left;margin:0 0 0 0px ;padding:0"  class="options" >
	<tr><td  {if $view=='general'}class="selected"{/if} id="general" >{t}General{/t}</td>
	  {if $view_stock}<td {if $view=='stock'}class="selected"{/if}  id="stock"  >{t}Stock{/t}</td>{/if}
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


       <table  id="avg_options" style="float:left;margin:0 0 0 20px ;padding:0 {if $view!='sales'};display:none{/if}"  class="options_mini" >
	<tr>
	  <td {if $avg=='totals'}class="selected"{/if} avg="totals"  id="avg_totals" >{t}Totals{/t}</td>
	  <td {if $avg=='month'}class="selected"{/if}  avg="month"  id="avg_month"  >{t}M AVG{/t}</td>
	  <td {if $avg=='week'}class="selected"{/if}  avg="week"  id="avg_week"  >{t}W AVG{/t}</td>

	</tr>
      </table>
       
    <div  class="clean_table_caption"  style="clear:both;">
	 <div style="float:left;"><div id="table_info0" class="clean_table_info"><span id="rtext0"></span> <span class="rtext_rpp" id="rtext_rpp0"></span> <span class="filter_msg"  id="filter_msg0"></span></div></div>
	 <div class="clean_table_filter" id="clean_table_filter0">
	 <div class="clean_table_info" style="width:8.2em;padding-bottom:1px; ">
	 <span id="filter_name0" style="margin-right:5px">{$filter_name}:</span>
	 <input style="border-bottom:none;width:6em;" id='f_input0' value="{$filter_value}" size=10/>
	 <div id='f_container0'></div>
	 </div>
	 </div>
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

<div id="change_display_menu" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">
      <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Display Mode Options{/t}:</li>
      {foreach from=$mode_options_menu item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_display_mode('{$menu.mode}','{$menu.label}',0)"> {$menu.label}</a></li>
      {/foreach}
    </ul>
  </div>
</div>


{include file='footer.tpl'}
