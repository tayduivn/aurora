{include file='header.tpl'}
<div id="bd" >
  <span class="nav2 onleft"><a  href="stores.php">{t}Stores{/t}</a></span>
  <span class="nav2 onleft"><a href="departments.php">{t}Departments{/t}</a></span>
  <span class="nav2 onleft"><a href="families.php">{t}Families{/t}</a></span>
  <span class="nav2 onleft"><a href="products.php">{t}Products{/t}</a></span>
  <span class="nav2 onleft"><a  class="selected"  href="categories.php">{t}Categories{/t}</a></span>
  <span class="nav2 onleft"><a href="parts.php">{t}Parts{/t}</a></span>


  <div class="search_box" >
    <span class="search_title">{t}Product Code{/t}:</span> <input size="8" class="text search" id="product_search" value="" name="search"/><img align="absbottom" id="product_submit_search" class="submitsearch" src="art/icons/zoom.png" alt="Submit search"><br/>
     <span  class="search_msg"   id="product_search_msg"    ></span> <span  class="search_sugestion"   id="product_search_sugestion"    ></span>
     <br/>
      <span  class="state_details" state="{$show_details}"  id="show_details"  atitle="{if $show_details}{t}show details{/t}{else}{t}hide details{/t}{/if}"  >{if $show_details}{t}hide details{/t}{else}{t}show details{/t}{/if}</span>

 </div>
  
  <div id="top" class="top_bar">
    <div id="short_menu" class="nodetails" style="{if $show_details}display:none;{/if}width:100%;margin-bottom:0px;padding-left:20px">
      <div    {if $departments==0    }style="display:none"{/if}  >
      <table style="float:left;margin:0 0 0 0px ;padding:0"  class="options" >
	<tr><td  {if $view=='general'}class="selected"{/if} id="general" >{t}General{/t}</td>
	  {if $view_stock}<td {if $view=='stock'}class="selected"{/if}  id="stock"  >{t}Stock{/t}</td>{/if}
	  {if $view_sales}<td  {if $view=='sales'}class="selected"{/if}  id="sales"  >{t}Sales{/t}</td>{/if}
	</tr>
      </table>
        <table id="period_options" style="float:left;margin:0 0 0 20px ;padding:0{if $view=='general'};display:none{/if}"  class="options" >
	<tr>

	  <td  {if $period=='all'}class="selected"{/if} period="all"  id="period_all" >{t}All{/t}</td>
	  <td {if $period=='year'}class="selected"{/if}  period="year"  id="period_year"  >{t}1Yr{/t}</td>
	  <td  {if $period=='quarter'}class="selected"{/if}  period="quarter"  id="period_quarter"  >{t}1Qtr{/t}</td>
	  <td {if $period=='month'}class="selected"{/if}  period="month"  id="period_month"  >{t}1M{/t}</td>
	  <td  {if $period=='week'}class="selected"{/if} period="week"  id="period_week"  >{t}1W{/t}</td>
	</tr>
      </table>


       <table  id="avg_options" style="float:left;margin:0 0 0 20px ;padding:0 {if $view!='sales'};display:none{/if}"  class="options" >
	<tr>
	  <td {if $avg=='totals'}class="selected"{/if} avg="totals"  id="avg_totals" >{t}Totals{/t}</td>
	  <td {if $avg=='month'}class="selected"{/if}  avg="month"  id="avg_month"  >{t}M AVG{/t}</td>
	  <td {if $avg=='week'}class="selected"{/if}  avg="week"  id="avg_week"  >{t}W AVG{/t}</td>

	</tr>
      </table>
     </div>
      <div style="clear:left;margin:5px 0 5px 0 ;float:left">
      <span   class="state_details" state="{$show_percentages}"  id="show_percentages"  atitle="{if $show_percentages}{t}Normal Mode{/t}{else}{t}Comparison Mode{/t}{/if}"  >{if $show_percentages}{t}Comparison Mode{/t}{else}{t}Normal Mode{/t}{/if}</span>
      </div>
    </div>

    <div id="details" class="details" style="{if !$show_details}display:none;{/if}">
      <div id="details_general"  {if $view!='general'}style="display:none"{/if}>
	<table>
	  <tr>
	    <td>{t}Number of Products{/t}:</td><td class="aright">{$products}</td>
	</tr>
	  <tr>
	    <td>{t}Number of Families{/t}:</td><td class="aright">{$families}</td>
	  </tr>
	  <tr>
	    <td>{t}Number of Departments{/t}:</td><td class="aright">{$departments}</td>
	  </tr>
	</table>
      </div>
      <div id="details_stock"  {if $view!='stock'}style="display:none"{/if}>
	<table   >
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
    </div>
  </div>
  
  <div class="data_table" style="clear:both;margin:0px 20px">
    <span id="table_title" class="clean_table_title">{t}{$table_title}{/t}</span>
    <div  class="clean_table_caption"  style="clear:both;">
      <div style="float:left;"><div id="table_info0" class="clean_table_info">{$table_info} <span class="filter_msg"  id="filter_msg0"></span></div></div>
      <div class="clean_table_controls" style="" ><div><span  style="margin:0 5px" id="paginator">{t}Showing all Records{/t}</span></div></div>
    </div>
    <div  id="table0"   class="data_table_container dtable btable with_total"> </div>
  </div>
  
</div> 
{include file='footer.tpl'}
