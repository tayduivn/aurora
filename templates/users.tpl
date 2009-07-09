{include file='header.tpl'}
<div id="bd" >
  <div id="yui-main">
    <div style="width:300px;float:right;padding:10px;text-align:right">
      <span class="but new edit" id="add_user">Add User</span>
    </div>
<div class="data_table" style="margin-top:25px">
  <span class="clean_table_title">{t}Users{/t}</span>
  <div  class="clean_table_caption"  style="clear:both;">
      <div style="float:left;"><div id="table_info0" class="clean_table_info"><span id="rtext0"></span> <span class="rtext_rpp" id="rtext_rpp0"></span> <span class="filter_msg"  id="filter_msg0"></span></div></div>
      <div class="clean_table_filter" id="clean_table_filter0"><div class="clean_table_info"><span id="filter_name0">{$filter_name0}</span>: <input style="border-bottom:none" id='f_input0' value="{$filter_value0}" size=10/><div id='f_container0'></div></div></div>
      <div class="clean_table_controls" style="" ><div><span  style="margin:0 5px" id="paginator0"></span></div></div>
  </div>
  <div  id="table0"   class="data_table_container dtable btable "> </div>
</div>
<div class="data_table" style="margin-top:25px;width:600px">
  <span class="clean_table_title">{t}Groups{/t}</span>
  <div  class="clean_table_caption"  style="clear:both;">
    <div style="float:left;"><div class="clean_table_info">{$table_info} <span class="filter_msg"  id="filter_msg1"></span></div></div>
    <div class="clean_table_filter" style="display:none"><div class="clean_table_info">{$filter_name}: <input style="border-bottom:none" id='f_input1' value="{$filter_value}" size=10/><div id='f_container1'></div></div></div>
    <div class="clean_table_controls" style="" ><div><span  style="margin:0 5px" id="paginator1"></span></div></div>
  </div>
  <div  id="table1"   class="data_table_container dtable btable "> </div>
</div>

  </div>
</div> 



<div id="add_user_supplier"   style="background:#fff;padding:0px;border:1px solid#777;font-size:90%;position:absolute;left:-1000px;top:-1050px;width:350px"       >
 <div class="hd" style="font-weight:800;background:#238546;color:#fff;cursor:pointer;padding:4px 3px;text-align:center ;border-bottom:1px solid #999">{t}Setting an account for a supplier{/t}</div>
      <div class="bd" style="padding:20px 20px 20px 20px">
	<table border=1 id="supplier_list" class="supplier_list" style="margin:0 auto " >
	  {foreach from=$suppliers item=_supplier name=foo}
	  {if $_supplier.mod==0}<tr>{/if}
	    <td   supplier_id="{$_supplier.id}" id="supplier{$_supplier.id}" {if $_supplier.is_user}class="selected" is_in="1" {else} onClick="select_supplier(this)"  is_in="0"{/if} >{$_supplier.alias}</td>
	    {if $_supplier.mod==$supplier_cols}</tr>{/if}
	  {/foreach}
	</table>

	
	<table id="supplier_form" class="edit inbox" style="margin:0 auto ;display:none" >
	  
	  
	  <tr class="tabs"  id="supplier_choose_method">
	    <td  colspan=2>
	      <span  id="supplier_auto_pwd_but" class="tab  unselectable_text" onClick="auto_pwd('supplier')">{t}Auto Password{/t}</span>
	      <span id="supplier_user_defined_pwd_but"  onClick="user_defined_pwd('supplier')" class="tab selected  unselectable_text" style="margin-left:20px ">{t}User Defined Password{/t}</span>
	    </td>
	  </tr>
	  
	  
	  <tr style="display:none" id="supplier_v_handle_container"  ><td style="text-align:left" colspan=2>{t}Handle{/t}: <input id="supplier_v_handle" value=""  /></td></tr>
	  <tr  style="height:30px"><td colspan=2 id="supplier_password_meter" style="padding:0 30px"><div style="float:right" id="supplier_password_meter_str"></div><div id="supplier_password_meter_bar" style="visibility:hidden;;height:12px;border:1px solid #555; background:#bd0e00;width:0%;font-size:10px;text-align:left;">&nbsp;</div></td></tr>
	  <tr style="display:none" id="supplier_handle_container"  ><td  >{t}Handle{/t}:</td><td style="text-align:left"> <span style="font-weight:800" id="supplier_handle" ></span></td></tr>
	  <tbody id="supplier_auto_dialog" style="display:none">
	    <tr class="bottom" ><td>{t}Password{/t}:</td><td style="text-align:left"><span style="font-weight:800" id="supplier_passwd" ></span></td></tr>
	  </tbody>
	  <tbody id="supplier_user_defined_dialog" >
	    
	    <tr><td>{t}Password{/t}:</td><td style="text-align:left"><input onKeyup="change_meter(this.value,'supplier')" style="width:6em" type="password" id="supplier_passwd1" value=""/></td></tr>
	    <tr  class="bottom" id="supplier_repeat_password"><td><img  id="supplier_error_passwd2" style="display:none" src="art/icons/exclamation.png" alt="!"/> {t}Repeat Password{/t}:</td><td style="text-align:left"><input onKeyup="match_passwd(this.value,'supplier_passwd1','supplier')" style="width:6em" type="password" id="supplier_passwd2"  value=""/></td></tr>
	  </tbody>
	</table>
	<table  class="edit inbox">
	  <tr class="buttons" >
<td><span  onclick="supplier_new_user()" id="supplier_save"  class="unselectable_text button"     style="visibility:hidden;margin-right:30px" >{t}Save{/t} <img src="art/icons/disk.png" ></span></td>
<td style="text-align:left"><span style="margin-left:30px" class="unselectable_text button" onclick="close_me('supplier');" >{t}Cancel{/t} <img src="art/icons/cross.png"/></span></td>


</tr>


	</table>
	
      </div>
</div>
<div id="add_user_customer">

</div>




<div id="add_user_staff"  style="background:#fff;padding:0px;border:1px solid#777;font-size:90%;position:absolute;left:-1000px;top:-1050px;width:350px"     >
      <div class="hd" style="font-weight:800;background:#238546;color:#fff;cursor:pointer;padding:4px 3px;text-align:center ;border-bottom:1px solid #999">{t}Setting an acount for a member of the staff {/t}</div>
      <div class="bd" style="padding:20px 20px 20px 20px">
	<table border=1 id="staff_list" class="staff_list" style="margin:0 auto " >
	  {foreach from=$staff item=_staff name=foo}
	  {if $_staff.mod==0}<tr>{/if}
	    <td   staff_id="{$_staff.id}" id="staff{$_staff.id}" {if $_staff.is_user}class="selected" is_in="1" {else} onClick="select_staff(this)"  is_in="0"{/if} >{$_staff.alias}</td>
	    {if $_staff.mod==$staff_cols}</tr>{/if}
	  {/foreach}
	</table>
	
	<table id="staff_form" class="edit inbox" style="margin:0 auto ;display:none" >
	  
	  
	  <tr class="tabs"  id="staff_choose_method">
	    <td  colspan=2>
	<span  id="staff_auto_pwd_but" class="tab  unselectable_text" onClick="auto_pwd('staff')">{t}Auto Password{/t}</span>
	<span id="staff_user_defined_pwd_but"  onClick="user_defined_pwd('staff')" class="tab selected  unselectable_text" style="margin-left:20px ">{t}User Defined Password{/t}</span>
	    </td>
	  </tr>
	  
	  
	  <tr style="display:none" id="staff_v_handle_container"  ><td style="text-align:left" colspan=2>{t}Handle{/t}: <input id="staff_v_handle" value=""  /></td></tr>
	  <tr  style="height:30px"><td colspan=2 id="staff_password_meter" style="padding:0 30px"><div style="float:right" id="staff_password_meter_str"></div><div id="staff_password_meter_bar" style="visibility:hidden;;height:12px;border:1px solid #555; background:#bd0e00;width:0%;font-size:10px;text-align:left;">&nbsp;</div></td></tr>
	  <tr style="display:none" id="staff_handle_container"  ><td  >{t}Handle{/t}:</td><td style="text-align:left"> <span style="font-weight:800" id="staff_handle" ></span></td></tr>
	  <tbody id="staff_auto_dialog" style="display:none">
	    <tr class="bottom" ><td>{t}Password{/t}:</td><td style="text-align:left"><span style="font-weight:800" id="staff_passwd" ></span></td></tr>
	  </tbody>
	  <tbody id="staff_user_defined_dialog" >
	    
	    <tr><td>{t}Password{/t}:</td><td style="text-align:left"><input onKeyup="change_meter(this.value,'staff')" style="width:6em" type="password" id="staff_passwd1" value=""/></td></tr>
	    <tr  class="bottom" id="staff_repeat_password"><td><img  id="staff_error_passwd2" style="display:none" src="art/icons/exclamation.png" alt="!"/> {t}Repeat Password{/t}:</td><td style="text-align:left"><input onKeyup="match_passwd(this.value,'staff_passwd1','staff')" style="width:6em" type="password" id="staff_passwd2"  value=""/></td></tr>
	  </tbody>
	</table>
	<table  class="edit inbox">
	  <tr class="buttons" >
	    <td style="text-align:left"><span style="margin-left:30px" class="unselectable_text button" onclick="close_me('staff');" >{t}Cancel{/t} <img src="art/icons/cross.png"/></span></td>
	    <td><span  onclick="staff_new_user()" id="staff_save"  class="unselectable_text button"     style="visibility:hidden;margin-right:30px" >{t}Save{/t} <img src="art/icons/disk.png" ></span></td></tr>
	</table>
	
      </div>
</div>


<div id="change_staff_password" style="display:nonex;position:absolute;left:-100px;top:-150px;background:#fff;padding:20px;border:1px solid#777;font-size:90%">
  <div class="bd" >
    <span style="text-weight:800">{t}Change Password for{/t} <span user_id='' id="change_staff_password_alias"></span></span>
  <table class="edit inbox" >
    
    <tr class="tabs"  id="change_staff_choose_method">
      <td  colspan=2 >
	<span  id="change_staff_auto_pwd_but" class="tab unselectable_text" onClick="auto_pwd('change_staff')">{t}Change (Random){/t}</span>
	<span id="change_staff_user_defined_pwd_but"  onClick="user_defined_pwd('change_staff')" class="tab selected unselectable_text" style="margin-left:20px ">{t}Change (User Defined){/t}</span>
      </td>
    </tr>
     <tr style="height:30px"><td colspan=2 id="change_staff_password_meter" style="padding:0 30px"><div style="float:right" id="change_staff_password_meter_str"></div><div id="change_staff_password_meter_bar" style="visibility:hidden;;height:12px;border:1px solid #555; background:#bd0e00;width:0%;font-size:10px;text-align:left;">&nbsp;</div></td></tr>
    <tbody id="change_staff_auto_dialog" style="display:none">
      <tr class="bottom"><td>{t}Password{/t}:</td><td style="text-align:left"><span style="font-weight:800" id="change_staff_passwd" ></span></td></tr>
    </tbody>

    <tbody id="change_staff_user_defined_dialog" >
      <tr><td>{t}Password{/t}:</td><td style="text-align:left"><input onKeyup="change_meter(this.value,'change_staff')" style="width:11em" type="password" id="change_staff_passwd1" value=""/></td></tr>
      <tr id="change_staff_repeat_password" class="bottom"><td style="vertical-align:top" ><img  id="change_staff_error_passwd2" style="visibility:hidden" src="art/icons/exclamation.png" alt="!"/> {t}Repeat Password{/t}:</td><td style="text-align:left"><input onKeyup="match_passwd(this.value,'change_staff_passwd1','change_staff')" style="width:11em" type="password" id="change_staff_passwd2"  value=""/></td></tr>
    </tbody>
    <tr class="buttons" ><td style="text-align:left"><span id="change_staff_cancel" style="margin-left:30px" class="unselectable_text button" onClick="close_dialog('change_staff')">{t}Cancel{/t} <img src="art/icons/cross.png"/></span></td><td><span  onclick="change_staff_pwd()" id="change_staff_save"   class="unselectable_text button"     style="visibility:hidden;margin-right:30px">{t}Save{/t} <img src="art/icons/disk.png" ></span></td></tr>
  </table>
  </div>
</div>


<div id="add_user_other"  style="background:#fff;padding:0px;border:1px solid#777;font-size:90%;position:absolute;left:-1000px;top:-1050px;width:350px"   >
  <div class="hd"  style="font-weight:800;background:#238546;color:#fff;cursor:pointer;padding:4px 3px;text-align:center ;border-bottom:1px solid #999"  >{t}New user{/t}</div>
  <div class="bd" style="padding:10px">
    <div class="resp" ></div>
<form id="other_the_form">
      <table id="other_data_form" class="inbox_form" style="margin-bottom:10px;">
      <tr><td><label for="handle" id="other_handle_label" class="no_valid">{t}Handle{/t}:</label></td><td><input ok="0" id="other_handle" class="text"  type="text" value="" name="handle"/></td></tr>
      <tr><td><label for="name" id="other_name_label" class="no_valid" >{t}Name{/t}  :</label></td><td><input   ok="0"  id="other_name"  class="text" type="text" value="" name="name"/></td></tr>
      <tr><td><label for="surname" id="other_surname_label" class="no_valid">{t}Surname{/t}:</label></td><td><input  ok="0" id="other_surname"  class="text" type="text" value="" name="surname"/></td></tr>
      <tr><td><label for="email" id="other_email_label" class="no_valid">{t}Email{/t}:</label></td><td><input   ok="0" id="other_email" class="text" type="text" value="" name="email"/></td></tr>
      <tr><td>
	  <div style="display:none" id="other_container"></div>
      <label for="lang[]">{t}Language{/t}:</label></td><td>
      <select name="lang[]" id="other_lang">
	{foreach from=$newuser_langs item=lang key=lang_id}
	<option value="{$lang_id}">{$lang}</option>
	{/foreach}
      </select> 
      </td></tr>
      <tr><td>
      <label for="isactive">{t}Activate Account{/t}:</label></td><td>
      <input type="radio" value="1" name="isactive" checked="checked"  />{t}Yes{/t}
      </td></tr>
      <tr><td><label style="visibility:hidden">isactive:</label> </td><td>
      <input type="radio" value="0" name="isactive"  />{t}No{/t}
      </td></tr>
     <tr><td>
      <label for="group">{t}Groups{/t}:</label></td><td>
      {foreach from=$newuser_groups item=group key=group_id}
      <tr><td><label style="visibility:hidden">g</label></td><td><input type="checkbox" name="group" value="{$group_id}" />{$group}</td></tr>
      {/foreach}
</form>
      <tr class="buttons" ><td style="text-align:left"><span id="change_other_cancel" style="margin-left:30px" class="unselectable_text button" onClick="close_me('other')">{t}Cancel{/t} <img src="art/icons/cross.png"/></span></td><td><span  onclick="close_me('other');display_dialog('other2')" id="other_continue"   class="unselectable_text button"     style="visibility:hidden;margin-right:30px">{t}Continue{/t} <img src="art/icons/application_go.png" ></span></td></tr>



      </table>






    
  </div>
</div>


<div id="add_user_other2"  style="background:#fff;padding:0px;border:1px solid#777;font-size:90%;position:absolute;left:-1000px;top:-1050px;width:350px"   >
  <div class="hd"  style="font-weight:800;background:#238546;color:#fff;cursor:pointer;padding:4px 3px;text-align:center ;border-bottom:1px solid #999"  >{t}New user (2){/t}</div>
  <div class="bd" style="padding:10px">
    <div class="resp" ></div>


	<table  class="edit inbox" style="margin:0 auto ;" >
	  
	  
	  <tr class="tabs"  id="other_choose_method">
	    <td  colspan=2>
	<span  id="other_auto_pwd_but" class="tab  unselectable_text" onClick="auto_pwd('other')">{t}Auto Password{/t}</span>
	<span id="other_user_defined_pwd_but"  onClick="user_defined_pwd('other')" class="tab selected  unselectable_text" style="margin-left:20px ">{t}User Defined Password{/t}</span>
	    </td>
	  </tr>
	  
	  
	
	  <tr  style="height:30px"><td colspan=2 id="other_password_meter" style="padding:0 30px"><div style="float:right" id="other_password_meter_str"></div><div id="other_password_meter_bar" style="visibility:hidden;;height:12px;border:1px solid #555; background:#bd0e00;width:0%;font-size:10px;text-align:left;">&nbsp;</div></td></tr>
	  <tr  id="other_handle_container"  ><td  >{t}Handle{/t}:</td><td style="text-align:left"> <span style="font-weight:800" id="other_handle_show" ></span></td></tr>
	  <tbody id="other_auto_dialog" style="display:none">
	    <tr class="bottom" ><td>{t}Password{/t}:</td><td style="text-align:left"><span style="font-weight:800" id="other_passwd" ></span></td></tr>
	  </tbody>
	  <tbody id="other_user_defined_dialog" >
	    
	    <tr><td>{t}Password{/t}:</td><td style="text-align:left"><input onKeyup="change_meter(this.value,'other')" style="width:6em" type="password" id="other_passwd1" value=""/></td></tr>
	    <tr  class="bottom" id="other_repeat_password"><td><img  id="other_error_passwd2" style="display:none" src="art/icons/exclamation.png" alt="!"/> {t}Repeat Password{/t}:</td><td style="text-align:left"><input onKeyup="match_passwd(this.value,'other_passwd1','other')" style="width:6em" type="password" id="other_passwd2"  value=""/></td></tr>
	  </tbody>
	</table>
	<table  class="edit inbox" style="margin-bottom:10px;">
	  <tr class="buttons" >
	    <td style="text-align:left"><span style="margin-left:30px" class="unselectable_text button" onclick="close_me('other');" >{t}Cancel{/t} <img src="art/icons/cross.png"/></span></td>
	    <td style="text-align:center"><span  class="unselectable_text button"  onclick="close_me('other2');display_dialog('other')"  >{t}Back{/t} <img src="art/icons/application_ungo.png"/></span></td>
		    
	    <td><span  onclick="other_new_user()" id="other_save"  class="unselectable_text button"     style="visibility:hidden;margin-right:30px" >{t}Save{/t} <img src="art/icons/disk.png" ></span></td>
	  </tr>
	</table>
  </div>
</div>


<div id="add_user_dialog">
  
  <div class="bd">
    <span>{t}Choose kind of user{/t}</span>
    <ul>
      <li><span style="cursor:pointer" onCLick="add_user_dialog.cfg.setProperty('visible', false);display_dialog('staff')">Staff</span></li>
      <li><span style="cursor:pointer"onCLick="add_user_dialog.cfg.setProperty('visible', false);display_dialog('supplier')">Supplier</span></li>
      <li><span style="cursor:pointer"onCLick="add_user_dialog.cfg.setProperty('visible', false);add_user_dialog_customer.show()">Customer</span></li>
      <li><span style="cursor:pointer"onCLick="add_user_dialog.cfg.setProperty('visible', false);display_dialog('other')">Other</span></li>
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


{include file='footer.tpl'}

