{include file='header.tpl'}
<div id="bd" >
{include file='contacts_navigation.tpl'}

<div style=""> 
  <span   class="branch">{if $user->get_number_stores()>1}<a  href="customers_server.php">{t}Customers{/t}</a> &rarr; {/if}{$store->get('Store Code')} {t}Customers{/t}</span>
</div>


<div id="no_details_title"  style="margin-bottom:10px;">
    <h1>{t}Customers{/t} ({$store->get('Store Code')})</h1>
</div>
<div style="margin-bottom:10px;margin-top:20px;background:red">


<div style="width:400px;float:left">
<p style="padding:2px 10px;border-top:1px solid black;border-bottom:1px solid black">
{$overview_text}
</p>
</div>

<div style="float:left;font-size:80%;text-align:center">
<div style="margin-left:20px;border:1px solid #777;float:left;width:110px;padding:5px 0px">
{t}Total Contacts{/t}<div style="font-size:120%;font-weight:800">{$store->get('Contacts')}</div>
<div style="margin-top:2px;color:#555">
{t}Live Contacts{/t}<div style="font-size:120%"><span style="font-weight:800">{$store->get('Active Contacts')}</span> <span>({$store->get('Percentage Active Contacts')})</span></div>
</div>
</div>
<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px"><a href="new_customers_list.php?store={$store->get('Store Key')}&potential=1&auto=1">{t}Potential Customers{/t}<div style="font-size:120%;font-weight:800">{$store->get('Potential Customers')}</div></a></div>

<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px"><a href="new_customers_list.php?store={$store->get('Store Key')}&active=1&auto=1">{t}Active Customers{/t}<div style="font-size:120%;font-weight:800">{$store->get('Active Contacts With Orders')}</div></a></div>
<div style="margin-left:10px;border:1px solid #777;float:left;width:110px;padding:5px 0px"><a href="new_customers_list.php?store={$store->get('Store Key')}&lost=1&auto=1">{t}Lost Customers{/t}<div style="font-size:120%;font-weight:800">{$store->get('Lost Contacts With Orders')}</div></a></div>

</div>


  </div>


<div style="clear:both"></div>


	<!-- added code by kallol for demo only -->
	<div id="dialog_export">
	<div id="export_msg"></div>
	  <table style="padding:10px;margin:20px 10px 10px 10px" >
	 <tr><td><a href="export_data.php?subject=customers&subject_key={$store_id}&source=db">{t}Export Data (using last map){/t}</a></td></tr>
	 <tr><td><a href="export_data_maps.php?subject=customers&subject_key={$store_id}&source=db">{t}Export from another map{/t}</a></td></tr>
	 <tr><td><a href="export_wizard.php?subject=customers&subject_key={$store_id}">{t}Export Wizard (new map){/t}</a></td></tr>
	</table>
	</div>
	<!-- up to this -->

    <div id="the_table" class="data_table" style="clear:both;margin-top:10px">
      <span class="clean_table_title">{t}Customers List{/t} <img id="export_csv0"   tipo="customers_per_store" style="position:relative;top:0px;left:5px;cursor:pointer;vertical-align:text-bottom;" label="{t}Export (CSV){/t}" alt="{t}Export (CSV){/t}" src="art/icons/export_csv.gif"></span>
      
   <div  style="font-size:90%">
	
          <span style="float:right;margin-left:20px" class="table_type  state_details {if $type=='all_contacts'}selected{/if}"  id="all_contacts"   >{t}All Contacts{/t} ({$store->get('Contacts')})</span>
             <span style="float:right;margin-left:20px" class="table_type  state_details {if $type=='active_contacts'}selected{/if}"  id="active_contacts"   >{t}Active Contacts{/t} ({$store->get('Active Contacts')})</span>
			 
                  <span style="float:right;margin-left:20px;display:none" class="table_type  state_details {if $type=='contacts_with_orders'}selected{/if}"  id="contacts_with_orders"   >{t}Contacts with Orders{/t} ({$store->get('Contacts With Orders')})</span>

   {*

        <span style="float:right;margin-left:20px" class="table_type  state_details {if $type=='lost_contacts'}selected{/if}"  id="restrictions_all_customers" table_type="all_customers"   >{t}Lost Contacts{/t} ({$store->get('Lost Contacts')})</span>

*}
	

     </div>
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


  </div>
</div>
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




<div id="dialog_new_customer" style="padding:10px">
  <div id="new_customer_msg"></div>
  {t}Create new Customer{/t}:
  <table style="margin:10px">
    <tr>
        <td> <span  style="margin:0 10px" class="unselectable_text state_details" onClick="new_customer()" >{t}Manually{/t}</span></td>
           <td > <span class="unselectable_text state_details" onClick="new_customer_from_file()" >{t}Import from file{/t}</span></td>

   </tr>
  
  </table>
</div>

{include file='export_csv_menu_splinter.tpl' id=0 cols=$export_csv_table_cols session_address="customers-csv_export0" export_options=$csv_export_options0 }

{include file='footer.tpl'}
