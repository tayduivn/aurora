{include file='header.tpl'}
<div id="bd" >

<div class="search_box" style="margin-top:15px">

  <div class="general_options">
    {foreach from=$general_options_list item=options }
        {if $options.tipo=="url"}
            <span onclick="window.location.href='{$options.url}'" >{$options.label}</span>
        {else}
            <span  id="{$options.id}" state="{$options.state}">{$options.label}</span>
        {/if}
    {/foreach}
    </div>
</div>
 <div id="no_details_title"  style="clear:left;xmargin:0 20px;{if $details!=0}display:none{/if}">
    <h1>{t}Edit Customer{/t}: <span id="title_name">{$customer->get('Customer Name')}</span></h1>
  </div>

  <ul class="tabs" id="chooser_ul" style="clear:both">
    <li> <span class="item {if $edit=='details'}selected{/if}"  id="details">  <span> {t}Customer Details{/t}</span></span></li>

    <li> <span class="item {if $edit=='company'}selected{/if}"  id="company">  <span> {t}Company Details{/t}</span></span></li>
    <li> <span class="item {if $edit=='delivery'}selected{/if}"  id="delivery">  <span> {t}Delivery Options{/t}</span></span></li>
   
  </ul>

 <div class="tabbed_container" > 
   <div  class="edit_block" style="{if $edit!="delivery"}display:none{/if}"  id="d_delivery">

     <div style="border-bottom:1px solid #777;margin-bottom:5px;width:400px">
       <span style="float:right" class="state_details" >{t}Same as Contact Address{/t}</span>
       {t}Delivery Address{/t}:
       <span class="state_details" style="float:right;display:none" address_key="" id="delivery_cancel_edit_address">{t}Cancel{/t}</span>
       <span class="state_details" style="float:right;display:none" address_key="" id="delivery_save_edit_address">{t}Save{/t}</span>

     </div>
     
     {$customer->delivery_address_xhtml()}

   </div>



   <div  class="edit_block" style="{if $edit!="details"}display:none{/if}"  id="d_details">
  
       <div class="general_options" style="float:right">
	
	<span  style="margin-right:10px;visibility:hidden"  id="save_edit_customer" class="state_details">{t}Save{/t}</span>
	<span style="margin-right:10px;visibility:hidden" id="reset_edit_customer" class="state_details">{t}Reset{/t}</span>
	
      </div>
  
   <table class="edit" border=0 style="clear:both">
 <tr><td class="label" style="width:12em">{t}Type{/t}:</td>
	       <td style="width:19em"> 
		 <div class="options" style="margin:5px 0" id="shelf_type_type_container">
		   <input type="hidden" value="{$shelf_default_type}" ovalue="{$shelf_default_type}" id="shelf_type_type"  >
		  <span class="radio{if $customer_type=='Company'} selected{/if}"  id="radio_shelf_type_{$customer_type}" radio_value="{$customer_type}">{t}Company{/t}</span> 
		    <span class="radio{if $customer_type=='Person'} selected{/if}"  id="radio_shelf_type_{$customer_type}" radio_value="{$customer_type}">{t}Person{/t}</span> 

		 </div>

		 {if $customer_type=='Company'}
 </td></tr>
 <tr class="first"><td style="" class="label">{t}Company Name{/t}:</td>
   <td  style="text-align:left">
     <div  style="width:15em;position:relative;top:00px" >
       <input style="text-align:left;width:18em" id="Customer_Name" value="{$customer->get('Customer Name')}" ovalue="{$customer->get('Customer Name')}" valid="0">
       <div id="Customer_Name_Container" style="" ></div>
     </div>
   </td>
   <td id="Customer_Name_msg" class="edit_td_alert"></td>
 </tr>
 {/if}
 <tr class=""><td style="" class="label">{t}Contact Name{/t}:</td>
   <td  style="text-align:left">
     <div  style="width:15em;position:relative;top:00px" >
       <input style="text-align:left;width:18em" id="Customer_Main_Contact_Name" value="{$customer->get('Customer Main Contact Name')}" ovalue="{$customer->get('Customer Main Contact Name')}" valid="0">
       <div id="Customer_Main_Contact_Name_Container" style="" ></div>
     </div>
   </td>
   <td id="Customer_Main_Contact_Name_msg" class="edit_td_alert"></td>
 </tr>
 <tr class=""><td style="" class="label">{t}Contact Email{/t}:</td>
   <td  style="text-align:left">
     <div  style="width:15em;position:relative;top:00px" >
       <input style="text-align:left;width:18em" id="Customer_Main_Email" value="{$customer->get('Customer Main Plain Email')}" ovalue="{$customer->get('Customer Main Plain Email')}" valid="0">
       <div id="Customer_Main_Email_Container" style="" ></div>
     </div>
   </td>
   <td id="Customer_Main_Email_msg" class="edit_td_alert"></td>
 </tr>
 <tr class=""><td style="" class="label">{t}Contact Telephone{/t}:</td>
   <td  style="text-align:left">
     <div  style="width:15em;position:relative;top:00px" >
       <input style="text-align:left;width:18em" id="Customer_Main_Telephone" value="{$customer->get('Customer Main Plain Telephone')}" ovalue="{$customer->get('Customer Main Plain Telephone')}" valid="0">
       <div id="Customer_Main_Telephone_Container" style="" ></div>
     </div>
   </td>
   <td id="Customer_Main_Telephone_msg" class="edit_td_alert"></td>
 </tr>




     </table>

   <div id="customer_contact_address" style="float:left;xborder:1px solid #ddd;width:400px;margin-right:40px">
     <div >
     <span class="state_details" style="float:right;display:none" address_key="" id="contact_cancel_edit_address">{t}Cancel{/t}</span>
     <span class="state_details" style="float:right;display:none;margin-right:10px" address_key="" id="contact_save_address_button">{t}Save{/t}</span>
     </div>
  <div style="border-bottom:1px solid #777;margin-bottom:5px">
       
       {t}Contact Address{/t}:
   
     </div>
       <table>
       {include file='edit_address_splinter.tpl' address_identifier='contact_'}

     </table>
   </div>

 <div id="customer_contact_address" style="float:left;xborder:1px solid #ddd;width:400px;">
     <div style="border-bottom:1px solid #777;margin-bottom:5px">
       {t}Billing Address{/t}:<span class="state_details" style="float:right;display:none" address_key="" id="billing_cancel_edit_address">{t}Cancel{/t}</span>
     </div>
       <table>
       {include file='edit_address_splinter.tpl' address_identifier='billing_'}
       <span style="font-weight:600">Same as contact address</span> 
       <br/><span class="state_details">Set up different address</span>
       
       </table>
   </div>
 
 <div id="customer_delivery_address" style="float:left;xborder:1px solid #ddd;width:400px;">
     <div style="border-bottom:1px solid #777;margin-bottom:5px">
       {t}Delivery Address{/t}:<span class="state_details" style="float:right;display:none" address_key="" id="billing_cancel_edit_address">{t}Cancel{/t}</span>
     </div>
     
     {if ($customer->get('Customer Delivery Address Link')=='Main') or ( $customer->get('Customer Delivery Address Link')=='Billing'  and  ($customer->get('Customer Main Address Key')==$customer->get('Customer Billing Address Key'))   )   }
     <span style="font-weight:600">Same as contact address</span> 
     <br/><span class="state_details">Set up different address</span>
    
      {elseif $customer->get('Customer Delivery Address Link')=='Billing'}
     
     <span style="font-weight:600">Same as billing address</span> 
     <br/><span class="state_details">Set up different address</span>
     {else}
     {$customer->delivery_address_xhtml()}
     <br/><span class="state_details">Set up different address</span>
     {/if}


   </div>



<div style="clear:both"></div>


   </div>
   

   <div  class="edit_block" style="{if $edit!="company"}display:none{/if}"  id="d_company">
      <div class="general_options" style="float:right">
	
	<span  style="margin-right:10px;display:none"  id="save_new_customer" class="state_details">{t}Save{/t}</span>
	<span style="margin-right:10px;display:none" id="close_add_customer" class="state_details">{t}Reset{/t}</span>
	
      </div>


      <div id="new_customer_messages" class="messages_block"></div>

      


     
	  
       {include file='edit_company_splinter.tpl'}

     
   </div>

  
   
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

{include file='footer.tpl'}
