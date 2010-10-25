{include file='header.tpl'}
<div id="bd" >
 {include file='orders_navigation.tpl'}


  <div  id="orders_table" class="data_table" style="clear:left;margin-top:23px">
    <span class="clean_table_title">{t}Orders In Warehouse{/t}</span>

     
   <div  style="font-size:90%">
   
       
          <span   style="float:right;margin-left:20px" class="table_type  state_details {if $table_type=='all_contacts'}selected{/if}"  id="restrictions_all_contacts" table_type="all_contacts"  >{t}All Wanting Orders{/t} ()</span>
  <span   style="float:right;margin-left:20px" class="table_type  state_details {if $table_type=='all_customers'}selected{/if}"  id="restrictions_all_customers" table_type="all_customers"   >{t}Ready to Pick{/t} ()</span>
  <span   style="float:right;margin-left:20px" class="table_type  state_details {if $table_type=='active_customers'}selected{/if}"  id="restrictions_active_customers"  table_type="active_customers"  >{t}Ready to Pack{/t} ()</span>
  <span   style="float:right;margin-left:20px" class="table_type  state_details {if $table_type=='active_customers'}selected{/if}"  id="restrictions_active_customers"  table_type="active_customers"  >{t}Ready to Ship{/t} ()</span>

         
         
     </div>
  <div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999"></div>
  
  <table style="float:left;margin:0 0 0 0px ;padding:0;height:15px;"  class="options">
	<tr>
	 

	</tr>
      </table>
{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0  }

    <div  id="table0" style="font-size:90%"  class="data_table_container dtable btable "> </div>
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

{include file='footer.tpl'}


<div id="assign_picker_dialog" style="width:300px;">
<div class="options" style="width:300px;padding:10px;text-align:center" >

   <table border=1 style="margin:auto" id="assign_picker_buttons">
      {foreach from=$pickers item=picker_row name=foo}
      <tr>
	 {foreach from=$picker_row key=row_key item=picker }
	
	<td staff_id="{$picker.StaffKey}" id="picker{$picker.StaffKey}" class="assign_picker_button" onClick="select_staff(this,event)" >{$picker.StaffAlias}</td>
	{/foreach}
	</tr>
      {/foreach}
    </table>


</div>
<table class="edit">
<input type="hidden" id="assign_picker_staff_key">
<input type="hidden" id="assign_picker_dn_key">

<tr class="first"><td style="" class="label">{t}Staff Name{/t}:</td>
   <td  style="text-align:left">
     <div  style="width:190px;position:relative;top:00px" >
       <input style="text-align:left;width:180px" id="Assign_Picker_Staff_Name" value="" ovalue="" valid="0">
       <div id="Assign_Picker_Staff_Name_Container" style="" ></div>
     </div>
   </td>
   <td id="Assign_Picker_Staff_Name_msg" class="edit_td_alert"></td>
 </tr>
<tr><td>{t}Supervisor PIN{/t}:</td><td><input id="assign_picker_sup_password" type="password" /></td></tr>
</table>
<table class="edit" style="margin-top:10px;float:right">
  
  <tr><td colspan="2">
  <span class="button" onclick="close_dialog('assign_picker_dialog')">Cancel</span>
  <span class="button" onclick="assign_picker_save()" >Go</span><td></tr>
</table>
</div>

<div id="pick_it_dialog" style="width:300px;">
<div class="options" style="width:300px;padding:10px;text-align:center" >

   <table border=1 style="margin:auto" id="pick_it_buttons">
      {foreach from=$pickers item=picker_row name=foo}
      <tr>
	 {foreach from=$picker_row key=row_key item=picker }
	
	<td staff_id="{$picker.StaffKey}" id="picker_pick_it{$picker.StaffKey}" class="pick_it_button" onClick="select_staff_pick_it(this,event)" >{$picker.StaffAlias}</td>
	{/foreach}
	</tr>
      {/foreach}
    </table>


</div>
<table class="edit">
<input type="hidden" id="pick_it_staff_key">
<input type="hidden" id="pick_it_dn_key">

<tr class="first"><td style="" class="label">{t}Staff Name{/t}:</td>
   <td  style="text-align:left">
     <div  style="width:190px;position:relative;top:00px" >
       <input style="text-align:left;width:180px" id="pick_it_Staff_Name" value="" ovalue="" valid="0">
       <div id="pick_it_Staff_Name_Container" style="" ></div>
     </div>
   </td>
   <td id="pick_it_Staff_Name_msg" class="edit_td_alert"></td>
 </tr>
<tr id="pick_it_pin_tr" style="visibility:hidden"><td><span id="pick_it_pin_alias"></span> {t}PIN{/t}:</td><td><input id="pick_it_password" type="password" /></td></tr>
</table>
<table class="edit" style="margin-top:10px;float:right">
  
  <tr><td colspan="2">
  <span class="button" onclick="close_dialog('pick_it_dialog')">Cancel</span>
  <span class="button" onclick="pick_it_save()" >Go</span><td></tr>
</table>
</div>


<div id="pick_assigned_dialog" style="width:300px;">

<table class="edit">
<input type="hidden" id="pick_assigned_staff_key">
<input type="hidden" id="pick_assigned_dn_key">


<tr><td>{t}PIN{/t} (<span id="pick_assigned_pin_alias"></span>):</td><td><input id="pick_assigned_password" type="password" /></td></tr>
</table>
<table class="edit" style="margin-top:10px;float:right">
  
  <tr><td colspan="2">
  <span class="button" onclick="close_dialog('pick_assigned_dialog')">Cancel</span>
  <span class="button" onclick="pick_assigned_save()" >Go</span><td></tr>
</table>
</div>