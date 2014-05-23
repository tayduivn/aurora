 
<div style="width:540px;float:right;text-align:left">
	<div style="border-bottom:1px solid #777;padding-bottom:5px">
		<div class="buttons">
			<button id="add_new_delivery_address"><img src="art/icons/add.png" alt=""> {t}Add Delivery Address{/t}</button> 
		</div>
		<div style="height:25px;display:table-cell; vertical-align:bottom">
			<h2>
				{t}Delivery Address Library{/t}
			</h2>
		</div>
	</div>
	<div id="dialog_new_delivery_address" style="width:540px;margin-top:10px;padding:10px 0 0 0 ;border:1px solid #ccc;display:none">
		<table class="edit" id="new_delivery_address_table" border="0" style="width:500px;margin:0 auto">
			<tr style="height:1px">
				<td style="width:110px"> </td>
				<td style="width:300px"> </td>
				<td style="width:90px"> </td>
			</tr>
			{include file='edit_address_splinter.tpl' close_if_reset=true address_identifier='delivery_' address_type='Shop' show_tel=true show_contact=true address_function='Shipping' hide_buttons=false hide_type=true hide_description=true show_form=false show_components=false show_default_country=1 default_country_2alpha="$default_country_2alpha" function_value=false } 
		</table>
	</div>
	<table>
		<tr id="tr_address_showcase">
			<td colspan="2" style="xborder:1px solid black" id="delivery_address_showcase"> 
			<div style="display:none" class="address_container" id="address_container0">
				<div class="address_display" id="address_display0">
				</div>
				<div class="address_buttons" id="address_buttons0">
					<span class="small_button small_button_edit" style="float:left" id="contacts_address_button0" address_id="0" onclick="contacts_address(event,this)"><img src="art/icons/vcard.png" alt="{t}Contacts{/t}" /></span> 
					<input type="checkbox" class='Is_Main' />
					{t}Main{/t} <span class="small_button small_button_edit" id="delete_address_button0" address_id="0" onclick="delete_address(event,this)">{t}Remove{/t}</span> <span class="small_button small_button_edit" id="edit_address_button0" address_id="0" onclick="edit_address(0)">{t}Edit{/t}</span> 
				</div>
			</div>
			<div class="address_container" style="display:none" id="delivery_address_container0">
				<div class="delivery_address_tel_div" id="delivery_address_tel_div0" style="color:#777;font-size:90%;">
					<span class="delivery_address_tel_label" id="delivery_address_tel_label0" style="visibility:hidden">{t}Tel{/t}: </span><span class="delivery_address_tel" id="delivery_address_tel0"></span> 
				</div>
				<div class="address_display" id="delivery_address_display0">
				</div>
				<div class="address_buttons" id="delivery_address_buttons0">
					<span style="float:left" id="contacts_address_button0" address_id="0" onclick="contacts_address(event,this)"> <img style="display:none" src="art/icons/user.png" alt="{t}Contacts{/t}" /></span> <span style="float:left;margin-left:5px;cursor:pointer" id="contacts_address_button0" address_id="0" onclick="contacts_address(event,this)"> <img style="display:none" src="art/icons/telephone.png" alt="{t}Telephones{/t}" /> </span> 
					<div class="buttons small">
						<div  style="float:left;{if $parent=='order'}display:none{/if}">
							<img id="delivery_main_yes_img_0" class="set_main delivery_main_yes" style="cursor:pointer;display:none" src="art/icons/star.png" /> 
							<img id="delivery_main_no_img_0" class="set_main delivery_main_no" title="{t}Set as main delivery address{/t}" style="cursor:pointer;" src="art/icons/star_dim.png" onclick="change_main_address(0,{literal}{{/literal}type:'Delivery',prefix:'delivery_',Subject:'Customer',subject_key:{$customer->get('Customer Key')}{literal}}{/literal})" /> 
						</div>
						<button id="delivery_use_this0" style="float:left;{if $parent!='order'}display:none{/if}" class="delivery_use_this small_button small_button_edit" onclick="change_main_address(0,{literal}{{/literal}type:'Delivery',prefix:'delivery_',Subject:'Customer',subject_key:{$customer->get('Customer Key')}{literal}}{/literal})">{t}Use This{/t}</button> 
						<button class="small_button small_button_edit" id="delete_address_button0" address_id="0" onclick="delete_address(0,'delivery_')"> <img class="button_img" id="delivery_remove_img_0" src="art/icons/cross.png"> {t}Remove{/t} </button> 
						<button class="small_button small_button_edit" id="edit_address_button0" address_id="0" onclick="edit_address(0,'delivery_')">{t}Edit{/t}</button> 
					</div>
				</div>
			</div>
			{foreach from=$customer->get_delivery_address_objects('no_contact') item=address key=key } 
			<div class="address_container" id="delivery_address_container{$address->id}">
				<div id="delivery_address_tel_div{$address->id}" style="color:#777;font-size:90%;">
					<span id="delivery_address_tel_label{$address->id}" style="{if !$address->get_principal_telecom_key('Telephone')}visibility:hidden;{/if}">{t}Tel{/t}: </span><span id="delivery_address_tel{$address->id}">{$address->get_formated_principal_telephone()}</span> 
				</div>
				<div class="address_display" id="delivery_address_display{$address->id}">
					{$address->display('xhtml')} 
				</div>
				<div style="clear:both" class="address_buttons" id="delivery_address_buttons{$address->id}">
					<span style="float:left" id="contacts_address_button{$address->id}" address_id="{$address->id}" onclick="contacts_address(event,this)"> <img style="display:none" src="art/icons/user.png" alt="{t}Contacts{/t}" /></span> <span style="float:left;margin-left:5px;cursor:pointer" id="contacts_address_button{$address->id}" address_id="{$address->id}" onclick="contacts_address(event,this)"> <img style="display:none" src="art/icons/telephone.png" alt="{t}Telephones{/t}" /> </span> 
					<div class="buttons small" style="">
						<div  style="float:left;{if $parent=='order'}display:none{/if}">
							<img id="delivery_main_yes_img_{$address->id}" class="delivery_main_yes" style="cursor:pointer;{if $address->id!=$customer->get('Customer Main Delivery Address Key')}display:none{/if}" src="art/icons/star.png" /> <img id="delivery_main_no_img_{$address->id}" class="delivery_main_no" title="{t}Set as main delivery address{/t}" style="cursor:pointer;{if $address->id==$customer->get('Customer Main Delivery Address Key')}display:none{/if}" src="art/icons/star_dim.png" onclick="change_main_address({$address->id},{literal}{{/literal}type:'Delivery',prefix:'delivery_',Subject:'Customer',subject_key:{$customer->get('Customer Key')}{literal}}{/literal})" /> 
						</div>
						
						<button id="delivery_use_this{$address->id}" style="float:left;{if $parent!='order'}display:none{/if}" class="delivery_use_this small_button small_button_edit" onclick="use_this_address_in_order({$address->id},true)">{t}Use this{/t}</button> 
						<button id="delete_address_button{$address->id}" style="{if $key==$customer->get('Customer Main Address Key') or $customer->get_is_billing_address($key) }display:none{/if}" class="small_button small_button_edit"  address_id="{$address->id}" onclick="delete_address({$address->id},{literal}{{/literal}type:'Delivery',prefix:'delivery_',Subject:'Customer',subject_key:{$customer->get('Customer Key')}{literal}}{/literal})"><img id="delivery_remove_img_{$address->id}" src="art/icons/cross.png"> {t}Remove{/t}</button> 
						<button id="edit_address_button{$address->id}" style="{if $key==$customer->get('Customer Main Address Key')or $customer->get_is_billing_address($key) }display:none{/if}" class="small_button small_button_edit"  address_id="{$address->id}" onclick="display_edit_delivery_address({$address->id},'delivery_')">{t}Edit{/t}</button> 
					</div>
				</div>
			</div>
			{/foreach} </td>
		</tr>
	</table>
</div>
<div style="width:260px;position:relative;bottom:-1px">
	<div style="border-bottom:1px solid #777;padding-bottom:5px">
		<div style="margin-top:5px;height:27px;display:table-cell; vertical-align:bottom;">
			<h2>
				{t}Contact Address{/t}:
			</h2>
		</div>
	</div>
	<div id="contact_address_in_delivery_edit" style="font-size:120%;margin-top:15px;border:1px solid #ccc;padding:10px;min-height:90px">
		{$customer->display_contact_address('xhtml')} 
	</div>
	<div class="buttons small">
	<div  id="delivery_address_showcase_bis" style="{if $parent=='order'}display:none{/if}">
		<img id="delivery_main_yes_img_{$customer->get('Customer Main Address Key')}" class="delivery_main_yes" style="cursor:pointer;{if $customer->get('Customer Main Address Key')!=$customer->get('Customer Main Delivery Address Key')}display:none{/if}" src="art/icons/star.png" /> <img id="delivery_main_no_img_{$customer->get('Customer Main Address Key')}" class="delivery_main_no" title="{t}Set as main delivery address{/t}" style="cursor:pointer;{if $customer->get('Customer Main Address Key')==$customer->get('Customer Main Delivery Address Key')}display:none{/if}" src="art/icons/star_dim.png" onclick="change_main_address({$customer->get('Customer Main Address Key')},{literal}{{/literal}type:'Delivery',prefix:'delivery_',Subject:'Customer',subject_key:{$customer->get('Customer Key')}{literal}}{/literal})" /> 
		</div>
		<button id="delivery_use_this{$customer->get('Customer Main Address Key')}" style="margin-top:3px;float:left;{if $parent!='order'}display:none{/if}" class="delivery_use_this small_button small_button_edit" onclick="use_this_address_in_order({$customer->get('Customer Main Address Key')},true)">{t}Use This{/t}</button> 

	 </div>
</div>
<div style="clear:both">
</div>
