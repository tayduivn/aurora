<input type="hidden" id="user_key" value="{$user->id}" />
<input type="hidden" id="store_key" value="{$store->id}" />
<input type="hidden" id="site_key" value="{$site->id}" />
<input type="hidden" id="customer_key"  value="{$page->customer->id}"/>

<div class="top_page_menu" style="padding:0px 20px 5px 20px">
<div class="buttons" style="float:left">
<button onclick="window.location='profile.php?view=change_password'" ><img src="art/icons/chart_organisation.png" alt=""> {t}Change Password{/t}</button>
<button onclick="window.location='profile.php?view=address_book'" ><img src="art/icons/chart_organisation.png" alt=""> {t}Address Book{/t}</button>
<button onclick="window.location='profile.php?view=orders'" ><img src="art/icons/table.png" alt=""> {t}Orders{/t}</button>
<button class="selected"  onclick="window.location='profile.php?view=contact'" ><img src="art/icons/chart_pie.png" alt=""> {t}My Account{/t}</button>


</div>


<div style="clear:both">

</div>
</div>



<div id="contact_block" {if $view!='contact'}style="display:none"{/if}>
<div style="border:0px solid #ccc;padding:0px 20px;width:890px;font-size:15px;margin:0px auto;margin-top:20px">
<div style="float:left;;border:1px solid #ccc;;height:60px;width:100px;;padding:5px 20px">Thank you for trading with us!</div>

{include file='customer_badges.tpl' customer=$page->customer}

<div style="clear:both"></div>
</div>

<div style="clear:both"></div>
<div style="padding:0px 20px;float:left">
<h2>{t}Contact Details{/t}</h2>
<div style="border:1px solid #ccc;padding:20px;width:400px;font-size:15px">
<h3>{$page->customer->get('Customer Name')} ({$page->customer->get_formated_id()})</h3> 

<table id="customer_data" border=0 style="width:100%;margin-top:20px">
    <tr >
        <td >{t}Company{/t}:</td>
        <td><img  id="show_edit_name" style="cursor:pointer"  src="art/edit.gif" alt="{t}Edit{/t}"/></td>
        <td  class="aright">{$page->customer->get('Customer Company Name')}</td >
    </tr>

<tr><td>{t}Name{/t}:</td><td><img style="cursor:pointer" id="show_edit_contact" src="art/edit.gif" alt="{t}Edit{/t}"/></td><td  class="aright">{$page->customer->get('Customer Main Contact Name')}</td ></tr>

{if $page->customer->get('Customer Main Email Key')}
<tr id="main_email_tr" >
<td>{t}Email{/t}</td>
<td><img src="art/lock.png"></td>
<td id="main_email"class="aright">{$page->customer->get('customer main plain email')}</td >

{/if}

{foreach from=$page->customer->get_other_emails_data() item=other_email key=key name=foo}
    <tr id="other_email_tr">
    <td>{t}Email{/t}</td>
    <td id="email{$key}"    class="aright">{$other_email.plain}</td >
    </tr>
{/foreach}


<tr><td>{t}Telephone{/t}:</td><td><img src="art/edit.gif" id="show_edit_telephone" alt="{t}Edit{/t}"/></td><td  class="aright">{$page->customer->get('Customer Main Plain Telephone')}</td ></tr>


{foreach from=$custom_fields item=custom_field key=key}
<tr>
<td>{$custom_field.name}:</td>
<td><img src="art/edit.gif" id="show_edit_{$custom_field.name}" alt="{t}Edit{/t}"/></td>
<td  class="aright">{$custom_field.value}</td >
</tr>
{/foreach}


<tr><td>
<div class="buttons">
<button style="display:none"  onclick="window.location='client.php'" ><img src="art/icons/chart_pie.png" alt=""> {t}Edit Profile{/t}</button>

</div>
</td></tr>
</table>

</div>
</div>
<div style="padding:0px 20px;float:right">
<h2>{t}Notes{/t}</h2>
<div style="border:1px solid #ccc;padding:20px;width:400px;font-size:15px"></div>
</div>

<div style="padding:0px 20px;float:right">
<h2>{t}Communication{/t}</h2>
<div style="border:1px solid #ccc;padding:20px;width:400px;font-size:15px">
	
	<table>
	 <tr class="title"><td colspan=5>{t}Emails{/t}</td></tr>

	 <tr>
	 <td class="label" style="width:200px">{t}Send Newsletter{/t}:</td>
	 <td>

	   <div   class="buttons" >
	   <button class="{if $page->customer->get('Customer Send Newsletter')=='Yes'}selected{/if} positive" onclick="save_comunications('Customer Send Newsletter','Yes')" id="Customer Send Newsletter_Yes">{t}Yes{/t}</button>
	   <button class="{if $page->customer->get('Customer Send Newsletter')=='No'}selected{/if} negative" onclick="save_comunications('Customer Send Newsletter','No')" id="Customer Send Newsletter_No">{t}No{/t}</button>

	   </div>
	 </td>
	 </tr>
	  <tr>
	 <td class="label" style="width:200px">{t}Send Marketing Emails{/t}:</td>
	 <td>
	   <div class="buttons" >
	   <button class="{if $page->customer->get('Customer Send Email Marketing')=='Yes'}selected{/if} positive" onclick="save_comunications('Customer Send Email Marketing','Yes')" id="Customer Send Email Marketing_Yes">{t}Yes{/t}</button>
	   <button class="{if $page->customer->get('Customer Send Email Marketing')=='No'}selected{/if} negative" onclick="save_comunications('Customer Send Email Marketing','No')" id="Customer Send Email Marketing_No">{t}No{/t}</button>
	   </div>
	 </td>
	 </tr>

	  <tr class="title"><td colspan=5>{t}Post{/t}</td></tr>


	  <tr>
	 <td class="label" style="width:200px">{t}Send Marketing Post{/t}:</td>
	 <td>
	   <div  class="buttons" >
	   <button class="{if $page->customer->get('Customer Send Postal Marketing')=='Yes'}selected{/if} positive" onclick="save_comunications('Customer Send Postal Marketing','Yes')" id="Customer Send Postal Marketing_Yes">{t}Yes{/t}</button> 
	   <button class="{if $page->customer->get('Customer Send Postal Marketing')=='No'}selected{/if} negative" onclick="save_comunications('Customer Send Postal Marketing','No')" id="Customer Send Postal Marketing_No">{t}No{/t}</button>
	   </div>
	 </td>
	 </tr>


	<tbody id="add_to_post_cue" style="display:none">

	  <tr class="title"><td colspan=5>{t}Send Post {/t}</td></tr>
	 <tr>
	 <td class="label" style="width:200px">{t}Add Customer To Send Post{/t}:</td>
	 <td>
	   <div    class="buttons" >
	   <button class="{if $page->customer->get('Send Post Status')=='To Send'}selected{/if} positive" onclick="save_comunications_send_post('Send Post Status','To Send')" id="Send Post Status_To Send">{t}Yes{/t}</button>
	   <button class="{if $page->customer->get('Send Post Status')=='Cancelled'}selected{/if} negative" onclick="save_comunications_send_post('Send Post Status','Cancelled')" id="Send Post Status_Cancelled">{t}No{/t}</button>
	   </div>
	 </td>
	 </tr>
	<tr>
	 <td class="label" style="width:200px">{t}Post Type{/t}:</td>
	 <td>
	   <div  class="buttons">
	   <button class="{if $page->customer->get('Post Type')=='Letter'}selected{/if} positive" onclick="save_comunications_send_post('Post Type','Letter')" id="Post Type_Letter">{t}Letter{/t}</button>
	   <button class="{if $page->customer->get('Post Type')=='Catalogue'}selected{/if} negative" onclick="save_comunications_send_post('Post Type','Catalogue')" id="Post Type_Catalogue">{t}Catalogue{/t}</button>
	   </div>
	 </td>
	 </tr>
	 </tbody>




	</table>
	
	
	
</div>
</div>
<div style="clear:both"></div>
</div>
       



<div style="padding:0px 20px 20px 20px;float:right">
<h2>{t}Questionare{/t}</h2>
<div style="border:1px solid #ccc;padding:20px;width:400px;font-size:15px">

<table style="margin:10px">
 
 {foreach from=$categories item=cat key=cat_key name=foo  }
{$other_field=''}
 <tr>
 
 <td class="label"><div style="width:150px">{t}{$cat->get('Category Name')}{/t}:</div></td>
 <td>
  <select id="cat{$cat_key}" cat_key="{$cat_key}"  onChange="save_category(this)">
    {foreach from=$cat->get_children_objects() item=sub_cat key=sub_cat_key name=foo2  }
        {if $smarty.foreach.foo2.first}

        {/if}
	{if $sub_cat->get('Category Name')=='Other'}
{assign var="other_field" value="<tr style='display:none' id='`$sub_cat_key`_tr'><td></td><td colspan='2'><textarea rows='2' cols='20' id='type_of_`$sub_cat_key`'> </textarea></tr>"}

		
	{/if}
        <option {if $categories_value[$cat_key]==$sub_cat_key }selected="selected"{/if} value="{$sub_cat->get('Category Key')}">{$sub_cat->get('Category Name')}</option>
    {/foreach}
  </select>
  
 </td>   
</tr>
{$other_field}
{/foreach}


</table>

</div>
</div>







<div style="top:180px;left:490px;position:absolute;display:none;background-image:url('art/background_badge_info.jpg');width:200px;height:223px;" id="gold_reward_badge_info">
<p style="padding:40px 20px;font-size:20px;margin:20px auto">
bla bla bla
<br/>
<a href="" >More Info</a>
<p>
</div>


<div id="dialog_quick_edit_Customer_Name" style="padding:10px">
	<table style="margin:10px">
	
	<tr>
	<td>{t}Customer Name:{/t}</td>
	<td>
	<div style="width:220px">
	<input type="text" id="Customer_Name" value="{$page->customer->get('Customer Company Name')}" ovalue="{$page->customer->get('Customer Company Name')}" valid="0">
	<div id="Customer_Name_Container"  ></div>
	</div>	
	</td>

	</tr>
	<tr><td colspan=2>
	<div class="buttons" style="margin-top:10px">
	<span id="Customer_Name_msg" ></span>
	<button class="positive" onClick="save_quick_edit_name()">{t}Save{/t}</button>
	<button class="negative" id="close_quick_edit_name">{t}Cancel{/t}</button>

	</div>
	</td></tr>
	</table>

</div>

<div id="dialog_quick_edit_Customer_Contact" style="padding:10px">
	<table style="margin:10px">
	
	<tr>
	<td>{t}Name:{/t}</td>
	<td>
	<div style="width:220px">
	<input type="text" id="Customer_Contact" value="{$page->customer->get('Customer Main Contact Name')}" ovalue="{$page->customer->get('Customer Main Contact Name')}" valid="0">
	<div id="Customer_Contact_Container"  ></div>
	</div>	
	</td>

	</tr>
	<tr><td colspan=2>
	<div class="buttons" style="margin-top:10px">
	<span id="Customer_Contact_msg" ></span>
	<button class="positive" onClick="save_quick_edit_contact()">{t}Save{/t}</button>
	<button class="negative" id="close_quick_edit_contact">{t}Cancel{/t}</button>

	</div>
	</td></tr>
	</table>

</div>

<div id="dialog_quick_edit_Customer_Telephone" style="padding:10px">
	<table style="margin:10px">
	
	<tr>
	<td>{t}Telephone:{/t}</td>
	<td>
	<div style="width:220px">
	<input type="text" id="Customer_Telephone" value="{$page->customer->get('Customer Main Plain Telephone')}" ovalue="{$page->customer->get('Customer Main Plain Telephone')}" valid="0">
	<div id="Customer_Telephone_Container"  ></div>
	</div>	
	</td>

	</tr>
	<tr><td colspan=2>
	<div class="buttons" style="margin-top:10px">
	<span id="Customer_Telephone_msg" ></span>
	<button class="positive" onClick="save_quick_edit_telephone()">{t}Save{/t}</button>
	<button class="negative" id="close_quick_edit_telephone">{t}Cancel{/t}</button>

	</div>
	</td></tr>
	</table>

</div>

{foreach from=$custom_fields item=custom_field key=key}

{if $custom_field.type=='Enum'}
<div id="dialog_quick_edit_Customer_{$custom_field.name}" style="padding:10px">
	<table style="margin:10px">
	  <tr>
	 <td class="label" style="width:">{t}{$custom_field.name}{/t}:</td>
	 <td>
	   <div class="buttons" >
	   <button class="{if $custom_field.value=='Yes'}selected{/if} positive" onclick="save_custom_enum('{$custom_field.name}','Yes')" id="{$custom_field.name}_Yes">{t}Yes{/t}</button>
	   <button class="{if $custom_field.value=='No'}selected{/if} negative" onclick="save_custom_enum('{$custom_field.name}','No')" id="{$custom_field.name}_No">{t}No{/t}</button>
	   </div>
	 </td>
	 </tr>
	</table>
</div>
{else}
<div id="dialog_quick_edit_Customer_{$custom_field.name}" style="padding:10px">
	<table style="margin:10px">
	
	<tr>
	<td>{t}{$custom_field.name}:{/t}</td>
	<td>
	<div style="width:220px">
	<input type="text" id="Customer_{$custom_field.name}" value="{$custom_field.value}" ovalue="{$custom_field.value}" valid="0">
	<div id="Customer_{$custom_field.name}_Container"  ></div>
	</div>	
	</td>

	</tr>
	<tr><td colspan=2>
	<div class="buttons" style="margin-top:10px">
	<span id="Customer_{$custom_field.name}_msg" ></span>
	<button class="positive" onClick="save_quick_edit_{$custom_field.name}()">{t}Save{/t}</button>
	<button class="negative" id="close_quick_edit_{$custom_field.name}">{t}Cancel{/t}</button>

	</div>
	</td></tr>
	</table>

</div>
{/if}

{/foreach}