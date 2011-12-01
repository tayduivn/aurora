{include file='header.tpl'}
<div id="bd" >
 {include file='contacts_navigation.tpl'}
 <div class="branch"> 
  <span>{if $user->get_number_stores()>1}<a  href="customers_server.php">{t}Customers{/t}</a> &rarr; {/if}<a  href="customers.php?store={$store->id}">{$store->get('Store Code')} {t}Customers{/t}</a> &rarr; {$id}</span>
</div>
 
<input type="hidden" value="{$customer->id}" id="customer_key"/>
<input type="hidden" value="{$registered_email}" id="registered_email"/>
<input type="hidden" value="{$store_id}" id="store_key"/>


<div class="top_page_menu">
    <img onMouseover="this.src='art/previous_button.gif'"  onMouseout="this.src='art/previous_button.png'"   title="{t}Previous Customer{/t} {$prev.name}" onclick="window.location='customer.php?{$parent_info}id={$prev.id}{if $parent_list}&p={$parent_list}{/if}'"  src="art/previous_button.png" alt="<"  style="margin-right:0px;float:left;height:22px;cursor:pointer;{if !$parent_list}display:none{/if};position:relative;top:2px" />
        <img onMouseover="this.src='art/next_button.gif'"  onMouseout="this.src='art/next_button.png'"  title="{t}Next Customer{/t} {$next.name}"  onclick="window.location='customer.php?{$parent_info}id={$next.id}{if $parent_list}&p={$parent_list}{/if}'"   src="art/next_button.png" alt=">"  style="float:right;height:22px;cursor:pointer;{if !$parent_list}display:none;{/if}position:relative;top:2px"/ >

    <div class="buttons" >
<button style="margin-left:10px" onclick="window.location='customer.php?id={$customer->id}{if $parent_list}&p={$parent_list}{/if}'" ><img src="art/icons/door_out.png" alt=""/> {t}Exit Edit{/t}</button>
    </div>
  
    <div style="clear:both"></div>
</div>

<h1 style="float:left;padding-bottom:10px"><span style="color:SteelBlue">{$id}</span> <span id="title_name">{$customer->get('Customer Name')}</span></h1>

<div style="padding:10px;background-color:#FAF8CC;width:300px;{if $recent_merges==''}display:none{/if}">{$recent_merges}</div>

<ul class="tabs" id="chooser_ul" style="clear:both;">
    <li> <span class="item {if $edit=='details'}selected{/if}"  id="details">  <span> {t}Customer Details{/t}</span></span></li>
 {if $customer_type=='Company'}
    <li> <span class="item {if $edit=='company'}selected{/if}" style="display:none"  id="company">  <span> {t}Company Details{/t}</span></span></li>
 {/if}
 <li> <span class="item {if $edit=='billing'}selected{/if}"  id="billing">  <span> {t}Billing Information{/t}</span></span></li>
 <li> <span class="item {if $edit=='delivery'}selected{/if}"  id="delivery">  <span> {t}Delivery Options{/t}</span></span></li>
    <li> <span class="item {if $edit=='categories'}selected{/if}"  id="categories">  <span> {t}Categories{/t}</span></span></li>
    <li> <span class="item {if $edit=='communications'}selected{/if}"  id="communications">  <span> {t}Communications{/t}</span></span></li>
    <li> <span class="item {if $edit=='merge'}selected{/if}"  id="merge">  <span> {t}Merge{/t}</span></span></li>
	{if $site_customer && $no_of_sites>0}
	<li> <span class="item {if $edit=='password'}selected{/if}"  id="password" style="display:">  <span> {t}User Site{/t}</span></span></li>
	{/if}
  </ul>
<div class="tabbed_container" > 
 
 {if $site_customer}
 <div  class="edit_block" style="{if $edit!="password"}display:none{/if};min-height:260px"  id="d_password">
 <div class="todo">No of sites: {$no_of_sites} {t}To Do{/t}</div>


   <table class="edit" border=0  style="width:100%">

    <tr class="title"><td colspan=5>{t}Reset Password{/t}</td></tr>

   <tr>
   <td style="width:300px">{t}Send an Email: ({$customer->get('Customer Main Plain Email')}){/t}</td>
   <td style="width:300px">

   <span   style="cursor:pointer" onClick="forget_password(this, '{$customer->get('Customer Main Plain Email')}')">{t}Send an Email to Reset password{/t}</span>
   </td>
   <td><span  style="cursor:pointer"  onClick="show_change_password_dialog(this, {$user_main_id})"  >Set Password</span></td>
   <td>
	<span id="password_msg" style="display:"></span></td>
	
	
	</tr>
    
	{foreach from=$registered_email item=email key=key name=foo  }
	   <tr>
   <td style="width:200px">{t}Send an Email: ({$email.email}){/t}</td>
   <td style="width:300px">

   <span   style="cursor:pointer" onClick="forget_password(this, '{$email.email}')"  email={$email.email} >{t}Send an Email to Reset password{/t}</span>
   </td>
   <td><span   style="cursor:pointer" user_key={$email.user_key} onClick="show_change_password_dialog(this,{$email.user_key})" >{t}Set Password{/t}</span></td>
   </tr>
   <tr><td><span id="password_msg_{$key}" style="display:"></span></td></tr>
   {/foreach}
   

   
   
   {if $unregistered_count>0}
   <tr class="title"><td colspan=5>{t}Unregistered Emails{/t}</td></tr>

   	{foreach from=$unregistered_email item=email key=key name=foo  }
	   <tr>
   <td style="width:200px">{t}{$email.email}{/t}</td>
   <td style="width:200px">
   <div>
       <input type="button" class="button" onclick="register_email(this)" email={$email.email} value="Register in Website"/>
   </div></td>
   <td><span id="register_msg" style="display:"></span></td>
   </tr>
   
   {/foreach}
   
   {/if}
   
   </table>
 </div>
 {/if}
 
   <div  class="edit_block" style="{if $edit!="merge"}display:none{/if};min-height:260px"  id="d_merge">
   
   <table class="edit" border=0  style="width:700px">
   <tr>
   <td style="width:200px">{t}Merge with: (Customer ID){/t}</td>
   <td style="width:200px">
   
   
   <div>
       <input style="text-align:left;width:100%" id="customer_b_id" value="" ovalue="" >
       <div id="customer_b_id_Container"  ></div>
   </div>
   
   </td>
   <td style="width:300px">
   <div class="buttons">
   <a id="go_merge"  class="positive" style="display:none" href="">{t}Go{/t}</a>
   </div>
   <span id="merge_msg" class="error" style="display:none"></span>
   </td>
   </tr>
   </table>
   
   </div>
   
   
<div  class="edit_block" style="{if $edit!="billing"}display:none{/if};min-height:260px"  id="d_billing">
     	  <div class="buttons" >
     	  	        <button  style="margin-right:10px;visibility:hidden"  id="save_edit_billing_data" class="positive">{t}Save{/t}</button>

		  <button style="margin-right:10px;visibility:hidden" id="reset_edit_billing_data" class="negative">{t}Reset{/t}</button>
	       
      </div>

 <div id="customer_billing_address" style="float:left;xborder:1px solid #ddd;width:400px;margin-bottom:20px;">
     <div style="border-bottom:1px solid #777;margin-bottom:7px">
     
       {t}Billing Information{/t}:<span class="state_details" style="float:right;display:none" address_key="" id="billing_cancel_edit_address">{t}Cancel{/t}</span>
     
     </div>
     

       <table border=0>
       
       
       <tr >
 <td  class="label">{t}Tax Number{/t}:</td>
   <td  style="text-align:left;width:280px">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_Tax_Number" value="{$customer->get('Customer Tax Number')}" ovalue="{$customer->get('Customer Tax Number')}" valid="0">
       <div id="Customer_Tax_Number_Container"  ></div>
     </div>
   </td>
   <td id="Customer_Tax_Number_msg"  class="edit_td_alert"></td>
 </tr>
       
       
	
     <tr  style="{if $customer->get('Customer Type')!='Company'}display:none{/if}"><td class="lavel">{t}Fiscal Name{/t}:</td>
        <td style="text-align:left;">
     <div   >
       <input style="text-align:left;" id="Customer_Fiscal_Name" value="{$customer->get('Customer Fiscal Name')}" ovalue="{$customer->get('Customer Fiscal Name')}" valid="0">
       <div id="Customer_Fiscal_Name_Container"  ></div>
     </div>
   </td>
   </tr>


{if $hq_country=='ES' and isset($cat2_id)}
<tr>
<td>RE:</td>
<td>
   <div id="cat_{$cat2_id}" default_cat="{$cat2.default_id}"   class="options" style="margin:0">
      <span class="{if $customer->get('Recargo Equivalencia')=='Yes'}selected{/if}" onclick="save_comunications('Recargo Equivalencia','Yes')" id="Recargo Equivalencia_Yes">{t}Yes{/t}</span> <span class="{if $customer->get('Recargo Equivalencia')=='No'}selected{/if}" onclick="save_comunications('Recargo Equivalencia','No')" id="Recargo Equivalencia_No">{t}No{/t}</span><br/><br/>
   </div>
<td>
</tr>
{else}
<tr style="display:none">
<td>{t}Tax Code{/t}:</td><td>
<select id="tax_code">
  {foreach from=$tax_codes item=sub_cat key=sub_cat_key name=foo2  }
    
        <option {if $customer->get('Customer Tax Category Code')==$sub_cat.code }selected="selected"{/if} value="{$sub_cat.code}">{$sub_cat.name}</option>
    {/foreach}
</select></td>
</tr>
{/if}
      
      
     </table>
     
       

       
       
   </div>
 <div style="clear:both">  
 {include file='edit_billing_information_splinter.tpl'}   
</div>
</div>   
   
   
<div  class="edit_block" style="{if $edit!="communications"}display:none{/if};min-height:260px"  id="d_communications">
    
    
    
    
<table class="edit">
 <tr class="title"><td colspan=5>{t}Emails{/t}</td></tr>
 
 <tr>
 <td class="label" style="width:200px">{t}Send Newsletter{/t}:</td>
 <td>

   <div   class="buttons" >
   <button class="{if $customer->get('Customer Send Newsletter')=='Yes'}selected{/if} positive" onclick="save_comunications('Customer Send Newsletter','Yes')" id="Customer Send Newsletter_Yes">{t}Yes{/t}</button>
   <button class="{if $customer->get('Customer Send Newsletter')=='No'}selected{/if} negative" onclick="save_comunications('Customer Send Newsletter','No')" id="Customer Send Newsletter_No">{t}No{/t}</button>

   </div>
 </td>
 </tr>
  <tr>
 <td class="label" style="width:200px">{t}Send Marketing Emails{/t}:</td>
 <td>
   <div class="buttons" >
   <button class="{if $customer->get('Customer Send Email Marketing')=='Yes'}selected{/if} positive" onclick="save_comunications('Customer Send Email Marketing','Yes')" id="Customer Send Email Marketing_Yes">{t}Yes{/t}</button>
   <button class="{if $customer->get('Customer Send Email Marketing')=='No'}selected{/if} negative" onclick="save_comunications('Customer Send Email Marketing','No')" id="Customer Send Email Marketing_No">{t}No{/t}</button>
   </div>
 </td>
 </tr>
 
  <tr class="title"><td colspan=5>{t}Post{/t}</td></tr>
 

  <tr>
 <td class="label" style="width:200px">{t}Send Marketing Post{/t}:</td>
 <td>
   <div  class="buttons" >
   <button class="{if $customer->get('Customer Send Postal Marketing')=='Yes'}selected{/if} positive" onclick="save_comunications('Customer Send Postal Marketing','Yes')" id="Customer Send Postal Marketing_Yes">{t}Yes{/t}</button> 
   <button class="{if $customer->get('Customer Send Postal Marketing')=='No'}selected{/if} negative" onclick="save_comunications('Customer Send Postal Marketing','No')" id="Customer Send Postal Marketing_No">{t}No{/t}</button>
   </div>
 </td>
 </tr>


<tbody id="add_to_post_cue" style="display:none">

  <tr class="title"><td colspan=5>{t}Send Post {/t}</td></tr>
 <tr>
 <td class="label" style="width:200px">{t}Add Customer To Send Post{/t}:</td>
 <td>
   <div    class="buttons" >
   <button class="{if $customer->get('Send Post Status')=='To Send'}selected{/if} positive" onclick="save_comunications_send_post('Send Post Status','To Send')" id="Send Post Status_To Send">{t}Yes{/t}</button>
   <button class="{if $customer->get('Send Post Status')=='Cancelled'}selected{/if} negative" onclick="save_comunications_send_post('Send Post Status','Cancelled')" id="Send Post Status_Cancelled">{t}No{/t}</button>
   </div>
 </td>
 </tr>
<tr>
 <td class="label" style="width:200px">{t}Post Type{/t}:</td>
 <td>
   <div  class="buttons">
   <button class="{if $customer->get('Post Type')=='Letter'}selected{/if} positive" onclick="save_comunications_send_post('Post Type','Letter')" id="Post Type_Letter">{t}Letter{/t}</button>
   <button class="{if $customer->get('Post Type')=='Catalogue'}selected{/if} negative" onclick="save_comunications_send_post('Post Type','Catalogue')" id="Post Type_Catalogue">{t}Catalogue{/t}</button>
   </div>
 </td>
 </tr>
 </tbody>
 
 
{*} 
 {foreach from=$categories item=cat key=cat_key name=foo  }
 <tr>
 
 <td class="label">{t}{$cat.name}{/t}:</td>
 <td>
   {foreach from=$cat.teeth item=cat2 key=cat2_id name=foo2}
   <div id="cat_{$cat2_id}" default_cat="{$cat2.default_id}"   class="options" style="margin:0">
     {foreach from=$cat2.elements item=cat3 key=cat3_id name=foo3}
     <span  class="catbox {if $cat3.selected}selected{/if}" value="{$cat3.selected}" ovalue="{$cat3.selected}" onclick="save_radio(this)" cat_id="{$cat3_id}" id="cat{$cat3_id}" parent="{$cat3.parent}" position="{$cat3.position}" default="{$cat3.default}"  >{$cat3.name}</span>
     {/foreach}
    </div>
   {/foreach}
 </td>   
</tr>
{/foreach}
{*}

</table>
</div>
<div  class="edit_block" style="{if $edit!="categories"}display:none{/if};min-height:260px"  id="d_categories">

<table class="edit">
 <tr class="title"><td colspan=5>{t}Categories{/t}</td></tr>
 
 {foreach from=$categories item=cat key=cat_key name=foo  }
 <tr>
 
 <td class="label">{t}{$cat->get('Category Name')}{/t}:</td>
 <td>
  <select id="cat{$cat_key}" cat_key="{$cat_key}"  onChange="save_category(this)">
    {foreach from=$cat->get_children_objects() item=sub_cat key=sub_cat_key name=foo2  }
        {if $smarty.foreach.foo2.first}
        <option {if $categories_value[$cat_key]=='' }selected="selected"{/if} value="">{t}Unknown{/t}</option>
        {/if}
        <option {if $categories_value[$cat_key]==$sub_cat_key }selected="selected"{/if} value="{$sub_cat->get('Category Key')}">{$sub_cat->get('Category Name')}</option>
    {/foreach}
  </select>
  
 </td>   
</tr>
{/foreach}
</table>

</div>
<div  class="edit_block" style="{if $edit!="delivery"}display:none{/if};min-height:260px"  id="d_delivery">
 {include file='edit_delivery_address_splinter.tpl'}

</div>
<div  class="edit_block" style="{if $edit!="details"}display:none{/if};"  id="d_details">
  
      

<table class="edit" border=0 style="clear:both;margin-bottom:40px;width:100%">
<tr>
<td style="width:150px"></td>
<td style="width:300px">

<div class="buttons">
<button id="delete_customer" class="negative {if $customer->get('Customer With Orders')=='Yes' || $customer->number_of_user_logins()>0}disabled{/if}" {if $customer->get('Customer With Orders')=='Yes' || $customer->number_of_user_logins()>0}style="text-decoration: line-through;"{/if}>{t}Delete Customer{/t}</button>

<button id="convert_to_person"  {if $customer_type!='Company'}style="display:none"{/if}>{t}Convert to Person{/t}</button>
<button id="convert_to_company" class="state_details" style="{if $customer_type=='Company'}display:none{/if}">{t}Convert to Company{/t}</button>

</div>

</td>
<td>
 <div class="buttons">
	        <button  style="visibility:hidden"  id="save_edit_customer" class="positive">{t}Save{/t}</button>
	        <button style="visibility:hidden" id="reset_edit_customer" class="negative">{t}Reset{/t}</button>
      </div>
</td>
</tr>

<tr style="height:10px">
<td colspan=3></td>
</tr>






 
 
   


		 
 
 <tr {if $customer_type!='Company'}style="display:none"{/if} class="first"><td  class="label">{t}Company Name{/t}:</td>
   <td  style="text-align:left;">
     <div  >
       <input style="text-align:left;width:100%" id="Customer_Name" value="{$customer->get('Customer Name')}" ovalue="{$customer->get('Customer Name')}" valid="0">
       <div id="Customer_Name_Container"  ></div>
     </div>
   </td>
   <td id="Customer_Name_msg"  class="edit_td_alert"></td>
 </tr>
 
  <tr  class="first"><td  class="label">{if $customer_type=='Company'}{t}Registration Number{/t}{else}{t}Identification Number{/t}{/if}:</td>
   <td  style="text-align:left;">
     <div  >
       <input style="text-align:left;width:100%" id="Customer_Registration_Number" value="{$customer->get('Customer Registration Number')}" ovalue="{$customer->get('Customer Registration Number')}" valid="0">
       <div id="Customer_Registration_Number_Container"  ></div>
     </div>
   </td>
   <td id="Customer_Registration_Number_msg"  class="edit_td_alert"></td>
 </tr>
 <tr ><td  class="label" >{t}Contact Name{/t}:</td>
   <td  style="text-align:left;">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_Main_Contact_Name" value="{$customer->get('Customer Main Contact Name')}" ovalue="{$customer->get('Customer Main Contact Name')}" valid="0">
       <div id="Customer_Main_Contact_Name_Container"  ></div>
     </div>
   </td>
   <td id="Customer_Main_Contact_Name_msg" class="edit_td_alert"></td>
 </tr>

 <tr ><td  class="label">{if   $customer->get('Customer Main Plain Email') == $login_stat.UserHandle}{/if}<img   id="comment_icon_email" src="{if $customer->get_principal_email_comment()==''}art/icons/comment.gif{else}art/icons/comment_filled.gif{/if}" style="cursor:pointer;{if $customer->get('Customer Main Email Key')==''}display:none{/if}" onClick="change_comment(this,'email',{$customer->get('Customer Main Email Key')})"> {t}Contact Email{/t}:</td>
   <td  style="text-align:left">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_Main_Email" value="{$customer->get('Customer Main Plain Email')}" ovalue="{$customer->get('Customer Main Plain Email')}" valid="0">
       <div id="Customer_Main_Email_Container"  ></div>
     </div>
   </td>
   <td>
   
   <span id="display_add_other_email" class="state_details" style="font-size:80%;color:#777;{if $customer->get('Customer Main Plain Email')==''}display:none{/if}">{t}Add other Email{/t}</span>
   <span id="Customer_Main_Email_msg" class="edit_td_alert">{$main_email_warning}</span>
   </td>
 </tr>



 {foreach from=$customer->get_other_emails_data() key=other_email_key item=other_email }
  <tr  id="tr_other_email{$other_email_key}">
  <td  class="label">
  {if $other_email_login_handle[$other_email.email] == $other_email.email}xxx{/if}<img  src="art/icons/edit.gif" style="cursor:pointer" onClick="change_other_field_label(this,'email',{$other_email_key})">  <span id="tr_other_email_label{$other_email_key}">{if $other_email.label==''}{t}Other Email{/t}{else}{$other_email.label} (Email){/if}:</span>
  </td>
   <td  style="text-align:left">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_Email{$other_email_key}" value="{$other_email.email}" ovalue="{$other_email.email}" valid="0">
       <div id="Customer_Email{$other_email_key}_Container"  ></div>
     </div>
   </td>
   <td>
   <span id="Customer_Email{$other_email_key}_msg" class="edit_td_alert"></span>
   </td>
 
 </tr>
{/foreach}


 <tr id="tr_add_other_email"  style="display:none"><td  class="label">{t}Other Email{/t}:</td>
   <td  style="text-align:left">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_Other_Email" value="" ovalue="" valid="0">
       <div id="Customer_Other_Email_Container"  ></div>
     </div>
   </td>
   <td id="Customer_Other_Email_msg" class="edit_td_alert"></td>
 </tr>




 <tr ><td  class="label"><img  id="comment_icon_telephone" src="{if $customer->get_principal_telecom_comment('Telephone')==''}art/icons/comment.gif{else}art/icons/comment_filled.gif{/if}" style="cursor:pointer;{if $customer->get('Customer Main Telephone Key')==''}display:none{/if}" onClick="change_comment(this,'telephone',{$customer->get('Customer Main Telephone Key')})"> {t}Contact Telephone{/t}:</td>
   <td  style="text-align:left">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_Main_Telephone" value="{$customer->get('Customer Main XHTML Telephone')}" ovalue="{$customer->get('Customer Main XHTML Telephone')}" valid="0">
       <div id="Customer_Main_Telephone_Container"  ></div>
     </div>
   </td>
   <td>
    <span id="display_add_other_telephone" class="state_details" style="font-size:80%;color:#777;{if $customer->get('Customer Main XHTML Telephone')==''}display:none{/if}">{t}Add other Telephone{/t}</span>
    <span id="Customer_Main_Telephone_msg" class="edit_td_alert">{$main_telephone_warning.$main_telephone_warning_key}</span>
   </td>
 </tr>
 
 
 
  {foreach from=$customer->get_other_telephones_data() key=other_telephone_key item=other_telephone }

 <tr  id="tr_other_telephone{$other_telephone_key}">
 <td  class="label">
 <img  src="art/icons/edit.gif" style="cursor:pointer" onClick="change_other_field_label(this,'telephone',{$other_telephone_key})">  <span id="tr_other_telephone_label{$other_telephone_key}">{if $other_telephone.label==''}{t}Other Telephone{/t}{else}{$other_telephone.label} (Telephone){/if}:</span></td>

   <td  style="text-align:left">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_Telephone{$other_telephone_key}" value="{$other_telephone.xhtml}" ovalue="{$other_telephone.xhtml}" valid="0">
       <div id="Customer_Telephone{$other_telephone_key}_Container"  ></div>
     </div>
   </td>
   <td>
   <span id="Customer_Telephone{$other_telephone_key}_msg" class="edit_td_alert">{$other_telephone_warning.$other_telephone_key}</span>
   </td>
 </tr>
{/foreach}

 <tr id="tr_add_other_telephone"  style="display:none"><td  class="label">{t}Other Telephone{/t}:</td>
   <td  style="text-align:left">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_Other_Telephone" value="" ovalue="" valid="0">
       <div id="Customer_Other_Telephone_Container"  ></div>
     </div>
   </td>
   <td id="Customer_Other_Telephone_msg" class="edit_td_alert"></td>
 </tr>

 
 
 
  <tr ><td  class="label"><img  id="comment_icon_mobile" src="{if $customer->get_principal_telecom_comment('Mobile')==''}art/icons/comment.gif{else}art/icons/comment_filled.gif{/if}" style="cursor:pointer;{if $customer->get('Customer Main Mobile Key')==''}display:none{/if}" onClick="change_comment(this,'mobile',{$customer->get('Customer Main Mobile Key')})"> {t}Contact Mobile{/t}:</td>
   <td  style="text-align:left">
     <div>
       <input style="text-align:left;width:100%" id="Customer_Main_Mobile" value="{$customer->get('Customer Main XHTML Mobile')}" ovalue="{$customer->get('Customer Main XHTML Mobile')}" valid="0">
       <div id="Customer_Main_Mobile_Container"  ></div>
     </div>
   </td>
  
   
      <td>
    <span id="display_add_other_mobile" class="state_details" style="font-size:80%;color:#777;{if $customer->get('Customer Main XHTML Mobile')==''}display:none{/if}">{t}Add other Mobile{/t}</span>
    <span id="Customer_Main_Mobile_msg" class="edit_td_alert">{$main_mobile_warning.$main_mobile_warning_key}</span>
   </td>
   
 </tr>
 
 
 
   {foreach from=$customer->get_other_mobiles_data() key=other_mobile_key item=other_mobile }
 <tr  id="tr_other_mobile{$other_mobile_key}"><td  class="label"><img  src="art/icons/edit.gif" style="cursor:pointer" onClick="change_other_field_label(this,'mobile',{$other_mobile_key})">  <span id="tr_other_mobile_label{$other_mobile_key}">{if $other_mobile.label==''}{t}Other Mobile{/t}{else}{$other_mobile.label} (Mobile){/if}:</span></td>
   <td  style="text-align:left">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_Mobile{$other_mobile_key}" value="{$other_mobile.number}" ovalue="{$other_mobile.number}" valid="0">
       <div id="Customer_Mobile{$other_mobile_key}_Container"  ></div>
     </div>
   </td>
   <td>
   <span id="Customer_Mobile{$other_mobile_key}_msg" class="edit_td_alert">{$other_mobile_warning.$other_mobile_key}</span>
   </td>
 </tr>
{/foreach}

 <tr id="tr_add_other_mobile"  style="display:none"><td  class="label">{t}Other Mobile{/t}:</td>
   <td  style="text-align:left">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_Other_Mobile" value="" ovalue="" valid="0">
       <div id="Customer_Other_Mobile_Container"  ></div>
     </div>
   </td>
   <td id="Customer_Other_Mobile_msg" class="edit_td_alert"></td>
 </tr>
 
 
<tr ><td  class="label"><img  id="comment_icon_fax" src="{if $customer->get_principal_telecom_comment('FAX')==''}art/icons/comment.gif{else}art/icons/comment_filled.gif{/if}" style="cursor:pointer;{if $customer->get('Customer Main FAX Key')==''}display:none{/if}" onClick="change_comment(this,'fax',{if $customer->get('Customer Main FAX Key') == NULL}{0}{else}{$customer->get('Customer Main FAX Key')}{/if})"> {t}Contact Fax{/t}:</td>
   <td  style="text-align:left">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_Main_FAX" value="{$customer->get('Customer Main XHTML FAX')}" ovalue="{$customer->get('Customer Main XHTML FAX')}" valid="0">
       <div id="Customer_Main_FAX_Container"  ></div>
     </div>
   </td>
   <td>
<span id="display_add_other_fax" class="state_details" style="font-size:80%;color:#777;{if $customer->get('Customer Main XHTML FAX')==''}display:none{/if}">{t}Add other Fax{/t}</span>
    <span id="Customer_Main_FAX_msg" class="edit_td_alert">{$main_fax_warning.$main_fax_warning_key}</span>
   </td>
 </tr>

 {foreach from=$customer->get_other_faxes_data() key=other_fax_key item=other_fax }
 <tr  id="tr_other_fax{$other_fax_key}"><td  class="label"><img  src="art/icons/edit.gif" style="cursor:pointer" onClick="change_other_field_label(this,'fax',{$other_fax_key})">  <span id="tr_other_fax_label{$other_fax_key}">{if $other_fax.label==''}{t}Other Fax{/t}{else}{$other_fax.label} (Fax){/if}:</span></td>
   <td  style="text-align:left">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_FAX{$other_fax_key}" value="{$other_fax.number}" ovalue="{$other_fax.number}" valid="0">
       <div id="Customer_FAX{$other_fax_key}_Container"  ></div>
     </div>
   </td>
   <td>
   <span id="Customer_FAX{$other_fax_key}_msg" class="edit_td_alert">{$other_fax_warning.$other_fax_key}</span>
   </td>
 </tr>
{/foreach}

 <tr id="tr_add_other_fax"  style="display:none"><td  class="label">{t}Other Fax{/t}:</td>
   <td  style="text-align:left">
     <div   >
       <input style="text-align:left;width:100%" id="Customer_Other_FAX" value="" ovalue="" valid="0">
       <div id="Customer_Other_FAX_Container"  ></div>
     </div>
   </td>
   <td id="Customer_Other_FAX_msg" class="edit_td_alert"></td>
 </tr>
 
 {*Edit custom fields*}
 
 {foreach from=$show_case key=custom_field_key item=custom_field_value }
 <tr  id="tr_{$custom_field_value.lable}"><td  class="label">{$custom_field_key}:</td>
   <td  style="text-align:left">
     <div>
       <input style="text-align:left;width:100%" id="Customer_{$custom_field_value.lable}" value="{$custom_field_value.value}" ovalue="{$custom_field_value.value}" valid="0">
       <div id="Customer_{$custom_field_value.lable}_Container"  ></div>
     </div>
   </td>
   <td>
   <span id="Customer_{$custom_field_value.lable}_msg" class="edit_td_alert"></span>
   </td>
 </tr>
{/foreach}


 <tr id="tr_Customer_Preferred_Contact_Number"   style="{if $customer->get('Customer Main XHTML Mobile')=='' or $customer->get('Customer Main XHTML Telephone')==''   }display:none{/if}" >
 <td class="label" style="width:200px">{t}Preferred contact number{/t}:</td>
 <td>
   <div id="cat_{$cat2_id}" default_cat="{$cat2.default_id}"   class="options" style="margin:0">
   <span class="{if $customer->get('Customer Preferred Contact Number')=='Telephone'}selected{/if}" onclick="save_preferred(this,'Telephone')" id="Customer_Preferred_Contact_Number_Telephone">{t}Telephone{/t}</span> <span class="{if $customer->get('Customer Preferred Contact Number')=='Mobile'}selected{/if}" onclick="save_preferred(this,'Mobile')" id="Customer_Preferred_Contact_Number_Mobile">{t}Mobile{/t}</span>
   </div>
 </td>
 </tr>


     </table>

   <div id="customer_contact_address" style="float:left;xborder:1px solid #ddd;width:430px;margin-right:20px;min-height:300px">
     <div style="border-bottom:1px solid #777;margin-bottom:5px">
       {t}Contact Address{/t}:
     </div>
     <table border=0 style="width:100%">
       {include file='edit_address_splinter.tpl' address_identifier='contact_' hide_type=true hide_description=true  show_components=true}
     </table>
     <div style="display:none" id='contact_current_address' ></div>
     <div style="display:none" id='contact_address_display{$customer->get("Customer Main Address Key")}' ></div>
   </div>


 
 <div id="customer_delivery_address" style="display:none;float:left;xborder:1px solid #ddd;width:400px;">
     <div style="border-bottom:1px solid #777;margin-bottom:5px">
       {t}Delivery Address{/t}:<span class="state_details" style="float:right;display:none" address_key="" id="billing_cancel_edit_address">{t}Cancel{/t}</span>
     </div>
     
     <div id="delivery_current_address_bis" style="margin-bottom:10px">
     {if ($customer->get('Customer Delivery Address Link')=='Contact') or ( $customer->get('Customer Delivery Address Link')=='Billing'  and  ($customer->get('Customer Main Address Key')==$customer->get('Customer Billing Address Key'))   )   }
     
     <span style="font-weight:600">{t}Same as contact address{/t}</span> 

     
     {elseif $customer->get('Customer Delivery Address Link')=='Billing'}
     
     <span style="font-weight:600">{t}Same as billing address{/t}</span> 

     
     {else}
     {$customer->delivery_address_xhtml()}
    
     
     {/if}
     <div id="billing_address_display{$customer->get('Customer Billing Address Key')}" style="display:none"></div>
      </div>
    <span id="delivery2" class="state_details">Set up different address</span>

    

   </div>



<div style="clear:both"></div>


   </div>
   
 {if $customer_type=='Company'}
   <div  class="edit_block" style="{if $edit!="company"}display:none{/if}"  id="d_company">
      <div class="general_options" style="float:right">
	
	<span  style="margin-right:10px;display:none"  id="save_new_customer" class="state_details">{t}Save{/t}</span>
	<span style="margin-right:10px;display:none" id="close_add_customer" class="state_details">{t}Reset{/t}</span>
	
      </div>


      <div id="new_customer_messages" class="messages_block"></div>

      


     
	  
       {include file='edit_company_splinter.tpl'}

     
   </div>
{else}
<div  class="edit_block" style="display:none"  id="d_company"></div>
{/if}
  
   
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

<div id="dialog_country_list" style="position:absolute;left:-1000;top:0">
    <div class="splinter_cell" style="padding:10px 15px 10px 0;border:none">
        <div id="the_table" class="data_table" >
            <span class="clean_table_title">{t}Country List{/t}</span>
            {include file='table_splinter.tpl' table_id=100 filter_name=$filter_name100 filter_value=$filter_value100}
            <div  id="table100"   class="data_table_container dtable btable "> </div>
        </div>
    </div>
 </div>


<div id="dialog_other_field_label">
  <div id="other_field_label_msg"></div>
    <input type="hidden" value="" id="other_field_label_scope"/>

  <input type="hidden" value="" id="other_field_label_scope_key"/>
  <table style="padding:20px;margin:20px 10px 10px 5px" >
 
    <tr><td colspan=2>
	<input  id="other_field_label" value=""  /> (<span id="other_field_label_scope_name"></span>)
      </td>
    <tr>
    <tr class="buttons" style="font-size:100%;">
  <td style="text-align:center;width:50%">
    <span  class="unselectable_text button"    style="visibility:hidden;" >{t}Cancel{/t}</span></td>
  <td style="text-align:center;width:50%">
    <span  style="display:block;margin-top:5px" onclick="save_other_field_label()" id="note_save"  class="unselectable_text button"   >{t}Save{/t}</span></td></tr>
</table>
</div>


<div id="dialog_comment">
  <div id="comment_msg"></div>
    <input type="hidden" value="" id="comment_scope"/>

  <input type="hidden" value="" id="comment_scope_key"/>
  
  
    <input type="hidden" value="{$customer->get_principal_telecom_comment('Telephone')}" id="comment_telephone"/>
        <input type="hidden" value="{$customer->get_principal_telecom_comment('FAX')}" id="comment_fax"/>
    <input type="hidden" value="{$customer->get_principal_telecom_comment('Mobile')}" id="comment_mobile"/>
    <input type="hidden" value="{$customer->get_principal_email_comment()}" id="comment_email"/>

  <table style="padding:20px;margin:20px 10px 10px 5px" >
 <tr><td>{t}Comment{/t}:</td></tr>
    <tr><td colspan=2>
	<input  id="comment" value="" ovalue="" /> 
      </td>
    <tr>
    <tr>
    <td>
    <div class="buttons">
        <button  onclick="save_comment()" id="comment_save"  class="positive"   >{t}Save{/t}</button>
    <button  class="cancel"  onclick="cancel_comment()"  >{t}Cancel{/t}</button>
</td>
    </div>
    </td>
    </tr>
    
    
</table>
</div>


<div id="dialog_set_password_main">
Change Password

<table border=0 id="change_password_form" >


<tr style="display:none;width:120px"><td class="label" >{t}Current Password{/t}: </td><td><input type="password" id="current_password_password1"></td></tr>
<tr><td style="width:120px" class="label">{t}New Password{/t}: </td><td><input type="password" id="change_password_password1"></td></tr>
<tr><td style="width:120px" class="label">{t}Confirm Password{/t}: </td><td><input type="password" id="change_password_password2"></td></tr>
<input id="epwcp1" value="{$main_email.epwcp1}" type="hidden"/>
<input id="epwcp2" value="{$main_email.epwcp2}" type="hidden"/>
<input id="user_key" value="{$main_email.user_key}" type="hidden"/>



<tr  id="tr_change_password_buttons"  class="button space" >
<td colspan=2><span style="display:none" id="change_password_error_no_password">{t}Write new password{/t}</span><span style="display:none" id="change_password_error_password_not_march">{t}Passwords don't match{/t}</span><span style="display:none" id="change_password_error_password_too_short">{t}Password is too short{/t}</span><span>
</span><button id="submit_change_password">{t}Change Password{/t}</button> 
</td></tr>
<tr id="tr_change_password_wait"  style="display:none" class="button" ><td colspan=2><img style="weight:24px" src="<?php echo $path ?>inikoo_files/art/wait.gif"> <span style="position:relative;top:-5px">{t}Submitting changes{/t}</span></td></tr>




</table>
</div>
 

<div id="dialog_set_password_">
{t}Change Password{/t}

<table border=0 id="change_password_form_" >


<tr style="display:none;width:120px"><td class="label" >{t}Current Password{/t}: </td><td><input type="password" id="current_password_password1_"></td></tr>
<tr><td style="width:120px" class="label">{t}New Password{/t}: </td><td><input type="password" id="change_password_password1_"></td></tr>
<tr><td style="width:120px" class="label">{t}Confirm Password{/t}: </td><td><input type="password" id="change_password_password2_"></td></tr>

<input id="user_key_in_change_password_form" value="" type="hidden"/>



<tr  id="tr_change_password_buttons_"  class="button space" >
<td colspan=2><span style="display:none" id="change_password_error_no_password_">{t}Write new password{/t}</span><span style="display:none" id="change_password_error_password_not_march_">{t}Passwords don't match{/t}</span><span style="display:none" id="change_password_error_password_too_short_">{t}Password is too short{/t}</span><span>
</span><button id="submit_change_password_" user={$email.user_key}>{t}Change Password{/t}</button> 
</td></tr>
<tr id="tr_change_password_wait_"  style="display:none" class="button" ><td colspan=2><img style="weight:24px" src="art/wait.gif"> <span style="position:relative;top:-5px">{t}Submitting changes{/t}</span></td></tr>




</table>
</div>


<div id="dialog_delete_customer"  style="padding:20px 10px 10px 10px;">

<h2 style="padding-top:0px">{t}Delete Customer{/t}</h2>
<p>
{t}This operation cannot be undone{/t}.<br> {t}Would you like to proceed?{/t}
</p>


<div style="display:none" id="deleting">
<img src="art/loading.gif" alt=""> {t}Deleting customer, wait please{/t}
</div>

<div  id="delete_customer_buttons" class="buttons">
<button id="save_delete_customer"  class="positive">{t}Yes, delete it!{/t}</button>

<button id="cancel_delete_customer"  class="negative" >{t}No i dont want to delete it{/t}</button>
</div>


</div>






<div  id="dialog_convert_to_person" style="padding:20px 10px 10px 10px;">
<p>
{t}Setting the contact as a person will delete the company name{/t}
</p>

<input type="hidden" value="{$delete_button_tooltip}" id="delete_button_tooltip">
<div class="buttons">
<button id="save_convert_to_person" class="negative" >{t}Convert to Person{/t}</button>
<button id="cancel_convert_to_person" class="positive"  >{t}Cancel{/t}</button>
</div>

<div style="clear:both"></div>
</div>


<div  id="dialog_convert_to_company" style="padding:20px 10px 10px 10px;width:400px">
<table class="edit" style="width:400px" border=0>
<tr  class="first">
  <td    style="width:100px" class="label">{t}Company Name{/t}:</td>
   <td  style="text-align:left;width:200px">
     <div  >
       <input style="text-align:left;width:100%" id="New_Company_Name" value="" ovalue="" valid="0">
       <div id="New_Company_Name_Container"  ></div>
     </div>
   </td>

 </tr> 

<tr>
<tr style="height:10px"><td colspan=2></td></tr>
<td colspan=2>
<span style="float:left"  id="New_Company_Name_msg" style="text-align:left;width:50px" class="edit_td_alert" ></span>

<div id="convert_to_person_buttons" class="buttons">
<button id="save_convert_to_company" >{t}Convert to Company{/t}</button>
<button id="cancel_convert_to_company" class="negative"  >{t}Cancel{/t}</button>
</div>
<div style="">
<span id="convert_to_company_processing" style="display:none;float:right"><img src="art/loading.gif"/> {t}Processing your request{/t}</span>
</div>
</td>
</tr>
</table>

</div>


 







{include file='footer.tpl'}
