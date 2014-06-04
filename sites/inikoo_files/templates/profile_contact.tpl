<input type="hidden" id="user_key" value="{$user->id}" />
<input type="hidden" id="store_key" value="{$store->id}" />
<input type="hidden" id="site_key" value="{$site->id}" />
<input type="hidden" id="customer_key" value="{$customer->id}" />
<input type="hidden" id="parent_category_key" value="0" />
<input type="hidden" id="category_key" value="0" />
{foreach from=$other_value item=other key=key} 
<input type="hidden" id="other_value_{$key}" value="{$other}" />
{/foreach} {foreach from=$enable_other item=other key=key} 
<input type="hidden" id="enable_other_{$key}" value="{$other}" />
{/foreach} 
<input type="hidden" id="subject" value="customer"> 
<input type="hidden" id="subject_key" value="{$customer->id}"> 
<input type="hidden" id="main_address_key" value="{$customer->get('Customer Main Address Key')}"> {include file='profile_header.tpl' select='contact'} {if $site->get('Show Site Badges')=='Yes'} 
<div style="border:0px solid #ccc;padding:0px 0px 0 0;width:890px;font-size:15px;margin-left:20px;margin-top:20px">
	<div style="float:left;;border:0px solid #ccc;;height:60px;width:350px;;padding:5px 20px;margin-left:20px;font-size:80%">
		This profile page is your way to tell us something about you that will help us to help you. The awards on the right illuminate as you get to know us better. Mouse over the awards to see how to get them, a full set will trigger your <i>Most Favoured Trader</i> status. 
	</div>
	{include file='customer_badges.tpl' customer=$page->customer} 
	<div style="clear:both">
	</div>
</div>
{/if} 
<div style="padding:0px 20px;float:left">
	<h2 style="padding-top:10px">
		{t}Contact Details{/t} 
	</h2>
	<div style="border:1px solid #ccc;padding:20px;width:400px;font-size:15px">
		<div style="float:right;border:0px solid #ccc;;margin-right:0px;margin-bottom:10px" id="show_upload_image">
			{if $user->get_image_src()} <img id="avatar" src="{$user->get_image_src()}" style="cursor:pointer;border:1px solid #eee;width:50px;max-height:50px"> {else} <img id="avatar" src="art/avatar.jpg" style="cursor:pointer;"> {/if} 
		</div>
		<h3>
			<span id="customer_name_title">{$page->customer->get('Customer Name')}</span> ({$page->customer->get_formated_id()}) 
		</h3>
		<table id="customer_data" border="0" style="width:100%;margin-top:20px">
			<tr style="{if !($page->customer->get('Customer Type')=='Company')}display:none{/if}">
				<td>{t}Company{/t}:</td>
				<td><img id="show_edit_name" style="cursor:pointer" src="art/edit.gif" alt="{t}Edit{/t}" /></td>
				<td class="aright" id="customer_name">{$page->customer->get('Customer Company Name')}</td>
			</tr>
			<tr>
				<td>{t}Name{/t}:</td>
				<td><img style="cursor:pointer" id="show_edit_contact" src="art/edit.gif" alt="{t}Edit{/t}" /></td>
				<td class="aright" id="customer_contact">{$page->customer->get('Customer Main Contact Name')}</td>
			</tr>
			{if $page->customer->get('Customer Main Email Key')} 
			<tr id="main_email_tr">
				<td>{t}Email{/t}:</td>
				<td><img src="art/lock.png"></td>
				<td id="main_email" class="aright">{$page->customer->get('Customer Main Plain Email')}</td>
			</tr>
			{/if} {foreach from=$page->customer->get_other_emails_data() item=other_email key=key name=foo} 
			<tr id="other_email_tr">
				<td>{t}Email{/t}:</td>
				<td><img src="art/lock.png"></td>
				<td id="email{$key}" class="aright">{$other_email.email}</td>
			</tr>
			{/foreach} 
			<tr>
				<td>{t}Telephone{/t}:</td>
				<td><img style="cursor:pointer" src="art/edit.gif" id="show_edit_telephone" alt="{t}Edit{/t}" /></td>
				<td class="aright" id="customer_telephone">{$page->customer->get('Customer Main Plain Telephone')}</td>
			</tr>
			<tr style="border-bottom:1px solid #eee">
				<td>{t}Website{/t}:</td>
				<td><img style="cursor:pointer" src="art/edit.gif" id="show_edit_website" alt="{t}Edit{/t}" /></td>
				<td class="aright" id="customer_website">{$page->customer->get('Customer Website')}</td>
			</tr>
			{foreach from=$custom_fields item=custom_field key=key} 
			<tr>
				<td>{$custom_field.name}:</td>
				<td><img style="cursor:pointer" src="art/edit.gif" id="show_edit_{$custom_field.name}" alt="{t}Edit{/t}" /></td>
				<td class="aright">{$custom_field.value}</td>
			</tr>
			{/foreach} 
			<tr style="display:none;">
				<td> 
				<div class="buttons">
					<button style="display:none" onclick="window.location='client.php'"><img src="art/icons/chart_pie.png" alt=""> {t}Edit Profile{/t}</button> 
				</div>
				</td>
			</tr>
			<tr class="space2">
				<td></td>
			</tr>
			<tr >
				<td style="vertical-align:top;">{t}Address{/t}:</td>
				<td style="vertical-align:top;"><img style="cursor:pointer" src="art/edit.gif" id="show_edit_address" alt="{t}Edit contact address{/t}" title="{t}Edit contact address{/t}" /></td>
				<td class="aright" id="customer_address"> {$page->customer->get('Customer Main XHTML Address')} </td>
			</tr>
			
		</table>
	</div>
</div>
<div style="padding:0px 20px;float:left">
	<h2 style="padding-top:10px">
		{t}Billing Details{/t} 
	</h2>
	<div style="border:1px solid #ccc;padding:20px;width:400px;font-size:15px">
		
		<table id="customer_data" border="0" style="width:100%;margin-top:0px">
			<tr style="border-bottom:1px solid #eee">
			
				<td style="vertical-align:top;">{t}Billing Address{/t}:</td>
				<td style="vertical-align:top;"><img style="cursor:pointer" src="art/edit.gif" onclick="location.href='profile.php?view=billing_addresses'" alt="{t}Edit contact address{/t}" title="{t}Edit contact address{/t}" /></td>
				<td class="aright" id="customer_address"> {$page->customer->get('Customer XHTML Billing Address')} </td>
			</tr>
			<tr style="">
				<td>{t}Tax Number{/t}:</td>
				<td><img style="cursor:pointer" src="art/edit.gif" id="show_edit_tax_number" alt="{t}Edit tax number{/t}" title="{t}Edit tax number{/t}" /></td>
				<td class="aright" id="customer_tax_number"> {$page->customer->get('Customer Tax Number')} </td>
			</tr>
		</table>
	</div>
</div>
<div style="padding:0px 20px;float:left">
	<h2 style="padding-top:10px">
		{t}Delivery Details{/t} 
	</h2>
	<div style="border:1px solid #ccc;padding:20px;width:400px;font-size:15px">
		
		<table id="customer_data" border="0" style="width:100%;margin-top:0px">
			<tr style="border-bottom:1px solid #eee">
			
				<td style="vertical-align:top;">{t}Billing Address{/t}:</td>
				<td style="vertical-align:top;"><img style="cursor:pointer" src="art/edit.gif" onclick="location.href='profile.php?view=delivery_addresses'" alt="{t}Edit contact address{/t}" title="{t}Edit contact address{/t}" /></td>
				<td class="aright" id="customer_address"> {$page->customer->get('Customer XHTML Main Delivery Address')} </td>
			</tr>

		</table>
	</div>
</div>
<div style="padding:0px 20px;float:right;display:none">
	<h2 style="padding-top:10px">
		{t}Notes{/t} 
	</h2>
	<div style="border:1px solid #ccc;padding:20px;width:400px;font-size:15px">
	</div>
</div>
<div style="padding:0px 20px;float:right">
	<h2 style="padding-top:10px">
		{t}Let's connect together{/t} 
	</h2>
	<div style="border:1px solid #ccc;padding:20px;width:400px;font-size:15px">
		<table class="edit" style="width:390px" border="0">
			<tr>
				<td colspan="5" style="text-align:right"> 
				<div style="font-size:120%;font-weight:800">
					<a style="text-decoration:none;color:#000" href="mailto:{$store->get('Store Email')}">{$store->get('Store Email')}</a><br>{$store->get('Store Telephone')} 
				</div>
				</td>
			</tr>
			<tr style="height:10px">
				<td colspan="3"></td>
			</tr>
			<tr class="title">
				<td colspan="5">{t}Newsletter{/t}</td>
			</tr>
			<tr style="height:10px">
				<td colspan="3"></td>
			</tr>
			<tr style="height:30px;">
				<td class="label" style="width:200px">{if $site->get('Site Newsletter Custom Label')==''}{t}Newsletter{/t}{else}{t}{$site->get('Site Newsletter Custom Label')}{/t}{/if}:</td>
				<td> 
				<div class="buttons small">
					<button class="{if $page->customer->get('Customer Send Newsletter')=='Yes'}selected{/if} positive" onclick="save_comunications('Customer Send Newsletter','Yes')" id="Customer Send Newsletter_Yes">{t}Yes{/t}</button> <button class="{if $page->customer->get('Customer Send Newsletter')=='No'}selected{/if} negative" onclick="save_comunications('Customer Send Newsletter','No')" id="Customer Send Newsletter_No">{t}No{/t}</button> 
				</div>
				</td>
			</tr>
			<tr style="height:30px;">
				<td class="label" style="width:200px">{if $site->get('Site Email Marketing Custom Label')==''}{t}Latest Offers & Updates{/t}{else}{t}{$site->get('Site Email Marketing Custom Label')}{/t}{/if}:</td>
				<td> 
				<div class="buttons small">
					<button class="{if $page->customer->get('Customer Send Email Marketing')=='Yes'}selected{/if} positive" onclick="save_comunications('Customer Send Email Marketing','Yes')" id="Customer Send Email Marketing_Yes">{t}Yes{/t}</button> <button class="{if $page->customer->get('Customer Send Email Marketing')=='No'}selected{/if} negative" onclick="save_comunications('Customer Send Email Marketing','No')" id="Customer Send Email Marketing_No">{t}No{/t}</button> 
				</div>
				</td>
			</tr>
			<tr class="title">
				<td colspan="5">{t}Post{/t}</td>
			</tr>
			<tr style="height:10px">
				<td colspan="3"></td>
			</tr>
			<tr style="height:30px;">
				<td class="label" style="width:200px">{if $site->get('Site Postal Marketing Custom Label')==''}{t}Catalogues & Vouchers{/t}{else}{t}{$site->get('Site Postal Marketing Custom Label')}{/t}{/if}:</td>
				<td> 
				<div class="buttons small">
					<button class="{if $page->customer->get('Customer Send Postal Marketing')=='Yes'}selected{/if} positive" onclick="save_comunications('Customer Send Postal Marketing','Yes')" id="Customer Send Postal Marketing_Yes">{t}Yes{/t}</button> <button class="{if $page->customer->get('Customer Send Postal Marketing')=='No'}selected{/if} negative" onclick="save_comunications('Customer Send Postal Marketing','No')" id="Customer Send Postal Marketing_No">{t}No{/t}</button> 
				</div>
				</td>
			</tr>
			<tbody id="add_to_post_cue" style="display:none">
				<tr class="title">
					<td colspan="5">{t}Send Post {/t}</td>
				</tr>
				<tr>
					<td class="label" style="width:200px">{t}Add Customer To Send Post{/t}:</td>
					<td> 
					<div class="buttons small">
						<button class="{if $page->customer->get('Send Post Status')=='To Send'}selected{/if} positive" onclick="save_comunications_send_post('Send Post Status','To Send')" id="Send Post Status_To Send">{t}Yes{/t}</button> <button class="{if $page->customer->get('Send Post Status')=='Cancelled'}selected{/if} negative" onclick="save_comunications_send_post('Send Post Status','Cancelled')" id="Send Post Status_Cancelled">{t}No{/t}</button> 
					</div>
					</td>
				</tr>
				<tr>
					<td class="label" style="width:200px">{t}Post Type{/t}:</td>
					<td> 
					<div class="buttons small">
						<button class="{if $page->customer->get('Post Type')=='Letter'}selected{/if} positive" onclick="save_comunications_send_post('Post Type','Letter')" id="Post Type_Letter">{t}Letter{/t}</button> <button class="{if $page->customer->get('Post Type')=='Catalogue'}selected{/if} negative" onclick="save_comunications_send_post('Post Type','Catalogue')" id="Post Type_Catalogue">{t}Catalogue{/t}</button> 
					</div>
					</td>
				</tr>
			</tbody>
			<tbody style="display:none" id="social_media">
				<tr class="title">
					<td colspan="5">{t}Social Media{/t}</td>
				</tr>
				<tr style="height:10px">
					<td colspan="3"></td>
				</tr>
				<tr style="height:30px;{if $site->get('Site Show Twitter')=='No'}display:none{/if}">
					<td class="label" style="width:200px">{t}Follower on Twitter{/t}:</td>
					<td> 
					<div class="buttons small">
						<button class="{if $page->customer->get('Customer Follower On Twitter')=='Yes'}selected{/if} positive" onclick="save_comunications('Customer Follower On Twitter','Yes')" id="Customer Follower On Twitter_Yes">{t}Yes{/t}</button> <button class="{if $page->customer->get('Customer Follower On Twitter')=='No'}selected{/if} negative" onclick="save_comunications('Customer Follower On Twitter','No')" id="Customer Follower On Twitter_No">{t}No{/t}</button> 
					</div>
					</td>
				</tr>
				<tr style="height:30px;{if $site->get('Site Show Facebook')=='No'}display:none{/if}">
					<td class="label" style="width:200px">{t}Friend on Facebook{/t}:</td>
					<td> 
					<div class="buttons small">
						<button class="{if $page->customer->get('Customer Friend On Facebook')=='Yes'}selected{/if} positive" onclick="save_comunications('Customer Friend On Facebook','Yes')" id="Customer Friend On Facebook_Yes">{t}Yes{/t}</button> <button class="{if $page->customer->get('Customer Friend On Facebook')=='No'}selected{/if} negative" onclick="save_comunications('Customer Friend On Facebook','No')" id="Customer Friend On Facebook_No">{t}No{/t}</button> 
					</div>
					</td>
				</tr>
			</tbody>
			<tr>
				<tr style="display:{if $site->get('Site Show Facebook')=='No' && $site->get('Site Show Twitter')=='No' && $site->get('Site Show Google')=='No' && $site->get('Site Show LinkedIn')=='No' && $site->get('Site Show Youtube')=='No' && $site->get('Site Show Flickr')=='No' && $site->get('Site Show Blog')=='No' && $site->get('Site Show Digg')=='No' && $site->get('Site Show RSS')=='No' && $site->get('Site Show Skype')=='No'}none{/if}" class="title">
					<td colspan="5">{t}Social Sites{/t}</td>
				</tr>
				<tr style="height:10px">
					<td colspan="3"></td>
				</tr>
				<td colspan="3"> <a style="display:{if $site->get('Site Show Skype')=='No'}none{/if}" href="http://{$site->get('Site Skype URL')}"><img src="art/grunge_skype.png" style="height:40px" /></a> <a style="display:{if $site->get('Site Show Facebook')=='No'}none{/if}" href="http://{$site->get('Site Facebook URL')}"><img src="art/grunge_facebook.png" style="height:40px" /></a> <a style="display:{if $site->get('Site Show Twitter')=='No'}none{/if}" href="http://{$site->get('Site Twitter URL')}"><img src="art/grunge_twitter.png" style="height:40px" /></a> <a style="display:{if $site->get('Site Show Google')=='No'}none{/if}" href="http://{$site->get('Site Google URL')}"><img src="art/grunge_google_plus.png" style="height:40px" /></a> <a style="display:{if $site->get('Site Show LinkedIn')=='No'}none{/if}" href="http://{$site->get('Site LinkedIn URL')}"><img src="art/grunge_linkedin.png" style="height:40px" /></a> <a style="display:{if $site->get('Site Show Youtube')=='No'}none{/if}" href="http://{$site->get('Site Youtube URL')}"><img src="art/grunge_youtube.png" style="height:40px" /></a> <a style="display:{if $site->get('Site Show Flickr')=='No'}none{/if}" href="http://{$site->get('Site Flickr URL')}"><img src="art/grunge_flickr.png" style="height:40px" /></a> <a style="display:{if $site->get('Site Show Blog')=='No'}none{/if}" href="http://{$site->get('Site Blog URL')}"><img src="art/grunge_blog.png" style="height:40px" /></a> <a style="display:{if $site->get('Site Show Digg')=='No'}none{/if}" href="http://{$site->get('Site Digg URL')}"><img src="art/grunge_digg.png" style="height:40px" /></a> <a style="display:{if $site->get('Site Show RSS')=='No'}none{/if}" href="http://{$site->get('Site RSS URL')}"><img src="art/grunge_rss.png" style="height:40px" /></a> </td>
			</tr>
		</table>
	</div>
</div>
<div style="clear:left">
</div>
<div style="padding:0px 20px 20px 20px;float:left">
	<h2 style="padding-top:10px">
		{t}About you{/t} 
	</h2>
	<div style="border:1px solid #ccc;padding:20px;width:400px;font-size:15px;">
		<table style="margin:10px">
			{foreach from=$categories item=cat key=cat_key name=foo } 
			<tr>
				<td class="label"> 
				<div style="width:150px">
					{t}{$cat->get('Category Label')}{/t}: 
				</div>
				</td>
				<td> 
				<select id="cat{$cat_key}" cat_key="{$cat_key}" onchange="save_category(this)">
					{foreach from=$cat->get_children_objects_public_edit() item=sub_cat key=sub_cat_key name=foo2 } {if $smarty.foreach.foo2.first} 
					<option value="">{t}Unknown{/t}</option>
					{/if} 
					<option {if $categories_value[$cat_key]==$sub_cat_key }selected{/if} other="{if $sub_cat->get('Is Category Field Other')=='Yes'}true{else}false{/if}" value="{$sub_cat->get('Category Key')}">{$sub_cat->get('Category Label')}</option>
					{/foreach} 
				</select>
				</td>
			</tr>
			<tbody id="show_other_tbody_{$cat_key}" style="{if !$cat->number_of_children_with_other_value('Customer',$page->customer->id) || !$cat->get_children_key_is_other_value_public_edit()}display:none{/if}">
				<tr>
					<td> 
					<div class="buttons small">
						<button onclick="show_save_other({$cat_key})">{t}Edit{/t}</button> 
					</div>
					</td>
					<td style="border:1px solid #ccc;">{$cat->get_other_value('Customer',$page->customer->id)} </td>
				</tr>
			</tbody>
			<tbody id="other_tbody_{$cat_key}" style="display:none">
				<tr>
					<td></td>
					<td><textarea rows='2' cols="20" id="other_textarea_{$cat_key}">{$cat->get_other_value('Customer',$page->customer->id)}</textarea></td>
				</tr>
				<tr>
					<td></td>
					<td> 
					<div class="buttons small left">
						<button onclick="save_category_other_value({$cat->get_children_key_is_other_value()},{$cat->id})">{t}Save{/t}</button> 
					</div>
					</td>
				</tr>
			</tbody>
			<tr style="height:15px">
				<td colspan="2"></td>
			</tr>
			{/foreach} 
		</table>
	</div>
</div>
<div style="clear:both;margin-bottom:25px">
</div>
<div style="top:180px;left:490px;position:absolute;display:none;background-image:url('art/background_badge_info.jpg');width:200px;height:223px;" id="gold_reward_badge_info">
	<p style="padding:40px 20px;font-size:20px;margin:20px auto">
		bla bla bla <br />
		<a href="">More Info</a> 
	</p>
</div>
<div id="dialog_quick_edit_Customer_Name" style="padding:10px">
	<table style="margin:10px">
		<tr>
			<td>{t}Customer Name:{/t}</td>
			<td> 
			<div style="width:220px">
				<input type="text" id="Customer_Name" value="{$page->customer->get('Customer Company Name')}" ovalue="{$page->customer->get('Customer Company Name')}" valid="0"> 
				<div id="Customer_Name_Container">
				</div>
			</div>
			</td>
		</tr>
		<tr>
			<td colspan="2"> 
			<div class="buttons" style="margin-top:10px">
				<span id="Customer_Name_msg"></span> <button class="positive" onclick="save_quick_edit_name()">{t}Save{/t}</button> <button class="negative" id="close_quick_edit_name">{t}Cancel{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>



<div id="dialog_quick_edit_Customer_Contact" style="padding:10px">
	<table style="margin:10px">
		<tr>
			<td>{t}Name:{/t}</td>
			<td> 
			<div style="width:220px">
				<input type="text" id="Customer_Contact" value="{$page->customer->get('Customer Main Contact Name')}" ovalue="{$page->customer->get('Customer Main Contact Name')}" valid="0"> 
				<div id="Customer_Contact_Container">
				</div>
			</div>
			</td>
		</tr>
		<tr>
			<td colspan="2"> 
			<div class="buttons" style="margin-top:10px">
				<span id="Customer_Contact_msg"></span> <button class="positive" onclick="save_quick_edit_contact()">{t}Save{/t}</button> <button class="negative" id="close_quick_edit_contact">{t}Cancel{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>
<div id="dialog_quick_edit_Customer_Telephone" style="padding:10px">
	<table style="margin:10px">
		<tr>
			<td>{t}Telephone:{/t}</td>
			<td> 
			<div style="width:220px">
				<input type="text" id="Customer_Telephone" value="{$page->customer->get('Customer Main Plain Telephone')}" ovalue="{$page->customer->get('Customer Main Plain Telephone')}" valid="0"> 
				<div id="Customer_Telephone_Container">
				</div>
			</div>
			</td>
		</tr>
		<tr>
			<td colspan="2"> 
			<div class="buttons" style="margin-top:10px">
				<span id="Customer_Telephone_msg"></span> <button class="positive" onclick="save_quick_edit_telephone()">{t}Save{/t}</button> <button class="negative" id="close_quick_edit_telephone">{t}Cancel{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>
<div id="dialog_quick_edit_Website" style="padding:10px">
	<table style="margin:10px">
		<tr>
			<td>{t}Website:{/t}</td>
			<td> 
			<div style="width:220px">
				<input type="text" id="Customer_Website" value="{$page->customer->get('Customer Website')}" ovalue="{$page->customer->get('Customer Website')}" valid="0"> 
				<div id="Customer_Website_Container">
				</div>
			</div>
			</td>
		</tr>
		<tr>
			<td colspan="2"> 
			<div class="buttons" style="margin-top:10px">
				<span id="Customer_Website_msg"></span> <button class="positive" onclick="save_quick_edit_website()">{t}Save{/t}</button> <button class="negative" id="close_quick_edit_website">{t}Cancel{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>
{foreach from=$custom_fields item=custom_field key=key} {if $custom_field.type=='Enum'} 
<div id="dialog_quick_edit_Customer_{$custom_field.name}" style="padding:10px">
	<table style="margin:10px">
		<tr>
			<td class="label" style="width:">{t}{$custom_field.name}{/t}:</td>
			<td> 
			<div class="buttons">
				<button class="{if $custom_field.value=='Yes'}selected{/if} positive" onclick="save_custom_enum('{$custom_field.name}','Yes')" id="{$custom_field.name}_Yes">{t}Yes{/t}</button> <button class="{if $custom_field.value=='No'}selected{/if} negative" onclick="save_custom_enum('{$custom_field.name}','No')" id="{$custom_field.name}_No">{t}No{/t}</button> 
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
				<div id="Customer_{$custom_field.name}_Container">
				</div>
			</div>
			</td>
		</tr>
		<tr>
			<td colspan="2"> 
			<div class="buttons" style="margin-top:10px">
				<span id="Customer_{$custom_field.name}_msg"></span> <button class="positive" onclick="save_quick_edit_{$custom_field.name}()">{t}Save{/t}</button> <button class="negative" id="close_quick_edit_{$custom_field.name}">{t}Cancel{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>
{/if} {/foreach} {section name=foo loop=5} 
<div id="dialog_badge_info_{$smarty.section.foo.iteration}" style="padding:10px;display:none">
	<table style="margin:10px">
		<tr>
			<td>{$page->customer->badge_info($smarty.section.foo.iteration)}</td>
			<td></td>
		</tr>
		<tr>
			<td> 
			<div class="buttons" style="margin-top:10px">
				<button class="negative" id="close_badge_info_{$smarty.section.foo.iteration}">{t}Cancel{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>
{/section} 
<div id="dialog_image_upload" style="padding:10px">
	<table>
		<tr style="{if $user->get_image_src()}display:inline{else}display:none{/if}">
			<td> 
			<div class="buttons left" image_id="{$user->get_image_key()}">
				<button onclick="delete_image(this)"> {t}Delete Image{/t} </button> 
			</div>
			</td>
		</tr>
		<tr style="height:10px">
			<td></td>
		</tr>
		<tr>
			<td>{if $user->get_image_src()}{t}Change Image{/t}{else}{t}Upload Image{/t}{/if}</td>
		</tr>
		<tr style="height:10px">
			<td></td>
		</tr>
		<tr>
			<td> 
			<form action="upload.php" enctype="multipart/form-data" method="post" id="testForm">
				<input id="upload_image_input" style="border:1px solid #ddd;" type="file" name="testFile" />
			</form>
			</td>
			<td> 
			<div class="buttons left">
				<button id="uploadButton" class="positive">{t}Upload{/t}</button> 
			</div>
			</td>
		</tr>
	</table>
</div>
<div id="dialog_quick_edit_addresss" style="float:left;xborder:1px solid #ddd;width:500px;padding:0px 10px 10px 10px">
	<table border="0" style="margin-top:20px; width:100%" class="edit">
		<tr style="height:1px">
			<td style="width:230px"> </td>
			<td style="width:220px"> </td>
			<td style="width:90px"> </td>
		</tr>
		{include file='edit_address_splinter.tpl' address_identifier='contact_' hide_description=true hide_buttons=false default_country_2alpha="$default_country_2alpha" show_form=1 show_default_country=1 address_type=false function_value='' address_function='' show_contact=false show_tel=false close_if_reset=true hide_type=true hide_description=true show_components=true} 
	</table>
	<div style="display:none" id='contact_current_address'>
	</div>
	<div style="display:none" id='contact_address_display{$customer->get("Customer Main Address Key")}'>
	</div>
</div>

	<div id="dialog_check_tax_number" style="padding:10px 20px 10px 10px">
		<table style="width:100%;margin:5px auto;padding:0px 10px" class="edit">
			<tr class="title">
				<td colspan="2">{t}Tax Number:{/t} {$customer->get('Customer Tax Number')} </td>
			</tr>
			<tr id="check_tax_number_result_tr" style="display:none">
				<td colspan="2" id="check_tax_number_result"> </td>
			</tr>
			<tr id="check_tax_number_name_tr" style="display:none">
				<td>{t}Name:{/t}</td>
				<td id="check_tax_number_name"> </td>
			</tr>
			<tr id="check_tax_number_address_tr" style="display:none">
				<td>{t}Address:{/t}</td>
				<td id="check_tax_number_address"> </td>
			</tr>
			<tr id="check_tax_number_wait">
				<td colspan="2"> <img src="art/loading.gif" alt=""> {t}Processing Request{/t} </td>
			</tr>
			<tr id="check_tax_number_buttons" style="display:none">
				<td colspan="2"> 
				<div class="buttons" style="margin-top:10px">
					<button id="save_tax_details_match">{t}Details Match{/t}</button> <button id="save_tax_details_not_match">{t}Details not match{/t}</button> <button id="close_check_tax_number">{t}Close{/t}</button> 
				</div>
				</td>
			</tr>
		</table>
	</div>
	<div id="dialog_quick_edit_Customer_Tax_Number" style="padding:10px">
		<table style="margin:10px">
			<tr>
				<td>{t}Tax Number:{/t}</td>
				<td> 
				<div style="width:220px">
					<input type="text" id="Customer_Tax_Number" value="{$customer->get('Customer Tax Number')}" ovalue="{$customer->get('Customer Tax Number')}" valid="0"> 
					<div id="Customer_Tax_Number_Container">
					</div>
				</div>
				</td>
			</tr>
			<tr>
				<td colspan="2"> 
				<div class="buttons" style="margin-top:10px">
					<span id="Customer_Tax_Number_msg" class="edit_td_alert"></span> <button onclick="save_quick_edit_tax_number()" class="positive" id="save_quick_edit_tax_number">{t}Save{/t}</button> <button class="negative" id="close_quick_edit_tax_number">{t}Cancel{/t}</button> 
				</div>
				</td>
			</tr>
		</table>
	</div>
