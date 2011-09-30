{include file='header.tpl'}
<div id="bd" >
<input value="{$email_campaign->id} id="email_campaign" type="hidden"  />
{include file='marketing_navigation.tpl'}




    <h1>{t}Mailing List{/t}: {$customer_list_name}</h1>


 <div id="the_table" class="data_table" style="clear:both">
      <span class="clean_table_title">{t}Customers List{/t}</span>
      
 
  <div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999"></div>
  <table style="float:left;margin:0 0 0 0px ;padding:0"  class="options" >
	<tr>
	  <td {if $view=='general'}class="selected"{/if} id="general" >{t}General{/t}</td>
	  <td {if $view=='contact'}class="selected"{/if}  id="contact"  >{t}Contact{/t}</td>
	  <td {if $view=='address'}class="selected"{/if}  id="address"  >{t}Address{/t}</td>
	  <td {if $view=='balance'}class="selected"{/if}  id="balance"  >{t}Balance{/t}</td>
	  <td {if $view=='rank'}class="selected"{/if}  id="rank"  >{t}Ranking{/t}</td>
	</tr>
      </table>
{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0  }
 <div  id="table0"  style="font-size:90%"  class="data_table_container dtable btable "> </div>
 </div>

</div>

<div id="dialog_export">
	<div id="export_msg"></div>
	  <table style="padding:10px;margin:20px 10px 10px 10px" >
	 <tr><td><a href="export_data.php?subject=customers_list&subject_key={$customer_list_id}&source=db">{t}Export Data (using last map){/t}</a></td></tr>
	 <tr><td><a href="export_data_maps.php?subject=customers_list&subject_key={$customer_list_id}&source=db">{t}Export from another map{/t}</a></td></tr>
	 <tr><td><a href="export_wizard.php?subject=customers_list&subject_key={$customer_list_id}">{t}Export Wizard (new map){/t}</a></td></tr>
	</table>
	</div>
  
  
  
  
  <div id="filtermenu0" class="yuimenu">
    <div class="bd">
      <ul class="first-of-type">
	<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
	{foreach from=$filter_menu0 item=menu }
	<li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_filter('{$menu.db_key}','{$menu.label}',0)"> {$menu.menu_label}</a></li>
	{/foreach}
      </ul>
    </div>
  </div>
  
  <div id="rppmenu0" class="yuimenu">
    <div class="bd">
      <ul class="first-of-type">
	<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
	{foreach from=$paginator_menu0 item=menu }
	<li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_rpp({$menu},0)"> {$menu}</a></li>
	{/foreach}
      </ul>
    </div>
  </div>
   {include file='export_csv_menu_splinter.tpl' id=0 cols=$export_csv_table_cols session_address="company_areas-table-csv_export" export_options=$csv_export_options } 
  


  {include file='footer.tpl'}
