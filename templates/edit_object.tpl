<div id="fields"  class="edit_object" object="{$state._object->get_object_name()}" key="{$state.key}" >
<span id="invalid_msg" class="hide">{t}Invalid value{/t}</span>

{if isset({$preferred_countries})}
<input id="preferred_countries" type="hidden" value="{$preferred_countries}">
{/if}
<table border=0>
{foreach from=$object_fields item=field_group } 
    <tr class="title" >
        <td  colspan=3>{$field_group.label}</td>
    </tr>
 
 	{if isset($field_group.class)}{assign "field_class" $field_group.class}{else}{assign "field_class" ""}{/if} 
    {if $field_class=='links'}
        {foreach from=$field_group.fields item=field name=fields} 
	        {if isset($field.render)}{assign "render" $field.render}{else}{assign "render" true}{/if} 
                <tr  class="link {if !$render}hide{/if}" onClick="change_view('{$field.reference}')">
                    <td  colspan=3><i style="margin-right:10px" class="fa fa-link"></i> {$field.label}</td>
                 </tr>
        {/foreach}
    
    {else}
    
 	{foreach from=$field_group.fields item=field name=fields} 
	    {if isset($field.edit)}{assign "edit" $field.edit}{else}{assign "edit" ""}{/if} 
	    {if isset($field.field_type)}{assign "field_type" $field.field_type}{else}{assign "field_type" $edit}{/if} 

	    {if isset($field.class)}{assign "class" $field.class}{else}{assign "class" ""}{/if} 
	    {if isset($field.render)}{assign "render" $field.render}{else}{assign "render" true}{/if} 
	    {if isset($field.required)}{assign "required" $field.required}{else}{assign "required" true}{/if} 
	    {if isset($field.server_validation)}{assign "server_validation" $field.server_validation}{else}{assign "server_validation" ""}{/if} 
	    {if isset($field.invalid_msg)}{assign "invalid_msg" $field.invalid_msg}{else}{assign "invalid_msg" ""}{/if} 
	
        {if $class=='directory'}
	     <tr id="{$field.id}_field" class="{if !$render}hide{/if} ">
	     <td class="label">{$field.label}</td>
	     <td colspan="3" id="{$field.id}_directory" class="with_vertical_padding">{$field.formatted_value}</td>
	     </tr>
        {else}
        <tr id="{$field.id}_field" field="{$field.id}" class="{if $smarty.foreach.fields.last}last{/if} {if !$render}hide{/if}  {$class} "  {if $class=='new' and $field.reference!=''}onClick="change_view('{$field.reference}')"{/if} >
		    <td id="{$field.id}_label" class="label" ><span>{$field.label}</span></td>
		    <td class="show_buttons  {if $edit=='address'}address {/if}" > 
		
		<i class="fa fa-lock fw {if $edit!='' or $class=='new'}hide{/if} edit"></i>  
		<i id="{$field.id}_reset_button" class="fa fa-sign-out fa-flip-horizontal fw reset hide reset_button" onclick="close_edit_this_field(this)"></i> 
		<i id="{$field.id}_edit_button" class="fa fa-pencil fw edit {if $edit==''}hide{/if} edit_button" onclick="open_edit_this_field(this)"></i> 
		
		</td>
		<td  id="{$field.id}_container" class="container value  " _required="{$required}" field_type='{$field_type}' server_validation='{$server_validation}' object='{$state._object->get_object_name()}' key='{$state.key}' parent='{$state.parent}' parent_key='{$state.parent_key}'> 
		
		
		<span id="{$field.id}_formatted_value"   class="{$field.id} {$edit} formatted_value " ondblclick="open_edit_this_field(this)">{if isset($field.formatted_value)}{$field.formatted_value}{else}{$field.value}{/if}</span>
		
       <input id="{$field.id}_value" type='hidden' class="unformatted_value" value="{$field.value}" />
    
        
		{if $edit=='string' or  $edit=='handle' or  $edit=='email' or $edit=='new_email' or  $edit=='int_unsigned' or $edit=='smallint_unsigned' or $edit=='mediumint_unsigned' or $edit=='int' or $edit=='smallint' or $edit=='mediumint' or $edit=='anything' or $edit=='numeric' } 
		
	
		<input id="{$field.id}" class="input_field hide" value="{$field.value}" has_been_valid="0"/>
		<i id="{$field.id}_save_button" class="fa fa-cloud save {$edit} hide" onclick="save_this_field(this)"></i> 
		<span id="{$field.id}_msg" class="msg"></span> 
		
		{elseif $edit=='working_hours'  } 
		{include file="working_hours.edit.tpl" field=$field working_hours=$working_hours } 
		{elseif $edit=='salary'  } 
		{include file="salary.edit.tpl" field=$field salary=$salary } 
		{elseif $edit=='textarea'  } 
		
	
		<textarea id="{$field.id}" class="input_field hide"   has_been_valid="0">{$field.value}</textarea>
		<i id="{$field.id}_save_button" class="fa fa-cloud  save {$edit} hide" onclick="save_field('{$state._object->get_object_name()}','{$state.key}','{$field.id}')"></i> 
		<span id="{$field.id}_msg" class="msg"></span> 
	    
	    {elseif $edit=='address'  or $edit=='new_delivery_address' or $edit=='address_to_clone' } 
	    
	    <div class="address_edit_fields_container" >
	        <table id="{$field.id}" border=0 class="address hide" field="{$field.id}" >
	            <tr id="{$field.id}_recipient" class="recipient">
	            <td class="show_buttons error super_discret"><i class="fa fa-asterisk"></i></td>
	            <td class="label">{t}Recipient{/t}</td>
	            <td><input value="" class="address_input_field" field_name="Address Recipient" ></td>
	            </tr>
	             <tr id="{$field.id}_organization" class="organization">
	            <td class="show_buttons error super_discret"><i class="fa fa-asterisk"></i></td>
	            <td class="label">{t}Organization{/t}</td>
	            <td><input   value="" class="address_input_field" field_name="Address Organization" ></td>
	            </tr>
	            <tr id="{$field.id}_addressLine1" class="addressLine1">
	            <td class="show_buttons error super_discret"><i class="fa fa-asterisk"></i></td>
	            <td>{t}Line 1{/t}</td>
	            <td><input  value="" class="address_input_field" field_name="Address Line 1" ></td>
	            </tr>
	            
	            <tr id="{$field.id}_addressLine2" class="addressLine2"> 
	            <td class="show_buttons error super_discret"><i class="fa fa-asterisk"></i></td>
	            <td class="label">{t}Line 2{/t}</td>
	            <td><input  value="" class="address_input_field" field_name="Address Line 2" ></td>
	            </tr>
	            <tr id="{$field.id}_sortingCode" class="sortingCode"> 
	            <td class="show_buttons error super_discret"><i class="fa fa-asterisk"></i></td>
	            <td class="label">{t}Sorting code{/t}</td>
	            <td><input  value="" class="address_input_field" field_name="Address Sorting Code" ></td>
	            </tr>
	           
	            <tr id="{$field.id}_postalCode" class="postalCode"> 
	            <td class="show_buttons error super_discret"><i class="fa fa-asterisk"></i></td>
	            <td class="label">{t}Postal code{/t}</td>
	            <td><input  value="" class="address_input_field" field_name="Address Postal Code" ></td>
	            </tr>
	             <tr id="{$field.id}_dependentLocality" class="dependentLocality"> 
	            <td class="show_buttons error super_discret"><i class="fa fa-asterisk"></i></td>
	            <td class="label">{t}Dependent locality{/t}</td>
	            <td><input  value="" class="address_input_field" field_name="Address Dependent Locality" ></td>
	            </tr>
	            <tr id="{$field.id}_locality" class="locality"> 
	            <td class="show_buttons error super_discret"><i class="fa fa-asterisk"></i></td>
	            <td class="label">{t}Locality (City){/t}</td>
	            <td><input  value="" class="address_input_field" field_name="Address Locality" ></td>
	            </tr>
	             <tr  id="{$field.id}_administrativeArea" class="administrativeArea"> 
	            <td class="show_buttons error super_discret"><i class="fa fa-asterisk"></i></td>
	            <td class="label">{t}Administrative area{/t}</td>
	            <td><input value="" class="address_input_field" field_name="Address Administrative Area" ></td>
	            </tr>
	            <tr id="{$field.id}_country" class="country"> 
	            <td class="show_buttons error super_discret"><i class="fa fa-asterisk"></i></td>
	            <td class="label">{t}Country{/t}</td>
	            <td>
	            <input value="" class="address_input_field" type="hidden" field_name="Address Country 2 Alpha Code" >
	            <input id="{$field.id}_country_select" value="" class="country_select"> 
	            </td>
	            </tr>
	            <tr>
	            <td colspan=2></td>
	            <td>
	            <i id="{$field.id}_save_button" class="fa fa-cloud save {$edit}" onclick="save_this_address(this)"></i> 
		        
	            </td>
	            </tr>
	            
	            </table>
	    </div>
	    <div><span id="{$field.id}_msg" class="msg" ></span></div>
	    {if  $edit!='address_to_clone'}
	    <script>
	
	
	
	    {if $edit=='address' and  $field.value!='' } 
	
	
	
        var address_fields = jQuery.parseJSON($('#{$field.id}_value').val())



        $('#{$field.id}_recipient  input ').val(decodeEntities(address_fields['Address Recipient']))
       
        $('#{$field.id}_organization  input ').val(decodeEntities(address_fields['Address Organization']))
        $('#{$field.id}_addressLine1  input ').val(decodeEntities(address_fields['Address Line 1']))
        $('#{$field.id}_addressLine2  input ').val(decodeEntities(address_fields['Address Line 2']))
        $('#{$field.id}_sortingCode  input ').val(decodeEntities(address_fields['Address Sorting Code']))
        $('#{$field.id}_postalCode  input ').val(decodeEntities(address_fields['Address Postal Code']))
        $('#{$field.id}_dependentLocality  input ').val(decodeEntities(address_fields['Address Dependent Locality']))
        $('#{$field.id}_locality  input ').val(decodeEntities(address_fields['Address Locality']))
        $('#{$field.id}_administrativeArea  input ').val(decodeEntities(address_fields['Address Administrative Area']))
        
        var initial_country=address_fields['Address Country 2 Alpha Code'].toLowerCase();
            
        {else}
    
         var initial_country='{$default_country|lower}';
        {/if}
	
	
	
	 telInput_{$field.id} = $("#{$field.id}_country_select")

	  telInput_{$field.id}.intlTelInput({
	     initialCountry: initial_country,
	     preferredCountries: [{$preferred_countries}]
	 });
	 
	 



	 telInput_{$field.id}.on("country-change", function(event,arg) {

        
	        var country_name = telInput_{$field.id}.intlTelInput("getSelectedCountryData").name
	        var country_code = telInput_{$field.id}.intlTelInput("getSelectedCountryData").iso2.toUpperCase()
        
	     
	     if (country_name.match(/\)\s+\(.+\)$/)) {
	         country_name = country_name.replace(/\)\s+\(.+\)$/, ")")
	     } else {
	         country_name = country_name.replace(/\s+\(.+\)$/, "")

	     }
        
      
      
      
        $('#{$field.id}_country  input.address_input_field ').val(country_code)


	     
	    update_address_fields('{$field.id}',country_code, hide_recipient_fields=false)
	    $("#{$field.id}_country_select").val(country_name)
	    if(arg!='init'){
	   
        on_changed_address_value("{$field.id}", '{$field.id}_country', country_code) 
        }

	 });

	 telInput_{$field.id}.trigger("country-change",'init');

	
	
	</script>
	{/if}
				{elseif $edit=='telephone'  or $edit=='new_telephone' } 
	<input  id="{$field.id}" class="input_field telephone_input_field hide" value="" has_been_valid="0"/>
		<i id="{$field.id}_save_button" class="fa fa-cloud  save {$edit} hide" onclick="save_field('{$state._object->get_object_name()}','{$state.key}','{$field.id}')"></i> 
		<span id="{$field.id}_msg" class="msg"></span> 
	
		
		<script>
		
		$("#{$field.id}").intlTelInput(
		{
		utilsScript: "/js/telephone_utils.js",
		defaultCountry:'{$account->get('Account Country 2 Alpha Code')}',
		preferredCountries:['{$account->get('Account Country 2 Alpha Code')}']
		}
		);
		
		
		
		$("#{$field.id}").intlTelInput("setNumber", "{$field.value}");
		</script>
		{elseif $edit=='pin' or  $edit=='password'} 
		<input id="{$field.id}" type="password" class="input_field hide" value="{$field.value}" has_been_valid="0" />
		<i id="{$field.id}_save_button" class="fa fa-cloud  save {$edit} hide" onclick="save_field('{$state._object->get_object_name()}','{$state.key}','{$field.id}')"></i> 
		<span id="{$field.id}_msg" class="msg"></span> 
		
		{elseif $edit=='pin_with_confirmation' or  $edit=='password_with_confirmation'} 
		<span id="not_match_invalid_msg" class="hide">{t}Values don't match{/t}</span> 
	    <span id="{$field.id}_cancel_confirm_button" class="hide"><span class="link" onclick="cancel_confirm_field('{$field.id}')"  >({t}start again{/t})</span> </span> 

	
		<input id="{$field.id}" type="password" class="input_field hide" value="{$field.value}" has_been_valid="0" />
		<input id="{$field.id}_confirm" placeholder="{t}Retype new password{/t}" type="password" confirm_field="{$field.id}" class="confirm_input_field hide" value="{$field.value}"  />
				<i id="{$field.id}_confirm_button"  class="fa fa-repeat  save {$edit} hide" onclick="confirm_field('{$field.id}')"></i> 

		
		<i id="{$field.id}_save_button" class="fa fa-cloud  save {$edit} hide" onclick="save_field('{$state._object->get_object_name()}','{$state.key}','{$field.id}')"></i> 
		<span id="{$field.id}_msg" class="msg"></span> 
		
		
		{elseif $edit=='option' } 
		
		<input id="{$field.id}" type="hidden" value="{$field.value}" has_been_valid="0" />
		{*}
		<input id="{$field.id}_formatted"  class="option_input_field hide" value="{$field.formatted_value|strip_tags}" readonly />
		{*}
		<i id="{$field.id}_save_button" class="fa fa-cloud  save {$edit} radio_option hide" onclick="save_field('{$state._object->get_object_name()}','{$state.key}','{$field.id}')"></i> 
		<span id="{$field.id}_msg" class="msg"></span> 
		
				<div id="{$field.id}_options" class="dropcontainer radio_option hide" >

			<ul>
				{foreach from=$field.options item=option key=value} 
				<li id="{$field.id}_option_{$value}" label="{$option}" value="{$value}" class="{if $value==$field.value}selected{/if}" onclick="select_option('{$field.id}','{$value}','{$option}' )">{$option} <i class="fa fa-circle fw current_mark {if $value==$field.value}current{/if}"></i></li>
				{/foreach} 
			</ul>
			</div>
			
		


		{elseif $edit=='radio_option' } 
		
		<input id="{$field.id}" type="hidden" value="{$field.value}" has_been_valid="0"/>
		{*}
		<input id="{$field.id}_formatted"  type="hidden" class="option_input_field hide" value="{$field.formatted_value}" readonly />
		{*}
		<i  id="{$field.id}_save_button" class="fa fa-cloud save {$edit} hide" onclick="save_field('{$state._object->get_object_name()}','{$state.key}','{$field.id}')"></i> 
        <span id="{$field.id}_msg" class="msg"></span> 
		<div id="{$field.id}_options" class="dropcontainer radio_option hide" >
			<ul>
				{foreach from=$field.options item=option key=value} 
				<li id="{$field.id}_option_{$value}" label="{$option.label}" value="{$value}" is_selected="{$option.selected}" onclick="select_radio_option('{$field.id}','{$value}','{$option.label}' )"><i class="fa fa-fw checkbox {if $option.selected}fa-check-square-o{else}fa-square-o{/if}"></i> {$option.label} <i class="fa fa-circle fw current_mark {if $option.selected}current{/if}"></i></li>
				{/foreach} 
			</ul>
		</div>

	
		{elseif $edit=='date' } 
		<input id="{$field.id}" type="hidden" value="{$field.value}" has_been_valid="0"/>
		<input id="{$field.id}_time" type="hidden" value="{$field.time}" />
		<input id="{$field.id}_formatted" class="option_input_field hide"  value="{$field.formatted_value}" />
		<i id="{$field.id}_save_button" class="fa fa-cloud save {$edit} hide" onclick="save_field('{$state._object->get_object_name()}','{$state.key}','{$field.id}')"></i> 
		<span id="{$field.id}_msg" class="msg"></span> 
		<div id="{$field.id}_datepicker" class="hide datepicker"></div>
		<script>
		    $(function() {
		        $("#{$field.id}_datepicker").datepicker({
		            showOtherMonths: true,
		            selectOtherMonths: true,
		            defaultDate: new Date('{$field.value}'),
		            altField: "#{$field.id}",
		            altFormat: "yy-mm-dd",
		            onSelect: function() {
		                $('#{$field.id}').change();
		                $('#{$field.id}_formatted').val($.datepicker.formatDate("yy-mm-dd", $(this).datepicker("getDate")))
		            }
		        });
		    });
		    $('#{$field.id}_formatted').on('input', function() {

		        var _moment = moment($('#{$field.id}_formatted').val(), ["DD-MM-YYYY", "MM-DD-YYYY"], 'en');


		        if (_moment.isValid()) {
		            var date = new Date(_moment)
		        } else {
		            var date = chrono.parseDate($('#{$field.id}_formatted').val())
		        }

		        if (date == null) {
		            var value = '';
		        } else {
		            var value = date.toISOString().slice(0, 10)
		            $("#{$field.id}_datepicker").datepicker("setDate", date);
		        }
		        $('#{$field.id}').val(value)
		        $('#{$field.id}').change();

		    });
		    $('#{$field.id}').on('change', function() {
		        on_changed_value('{$field.id}', $('#{$field.id}').val())
		    });
        </script> 
        {elseif $edit=='' } 
            {if $class=='new'}
            <span id="{$field.id}_msg" class="msg"></span> 
            {/if}
        {/if} 
  	    
  
	
	
	{if isset($field.invalid_msg)} 
	        {foreach from=$field.invalid_msg item=msg key=msg_key } 
	            <span id="{$field.id}_{$msg_key}_invalid_msg" class="hide">{$msg}</span> 
	        {/foreach} 
	    {/if} 
	</td>
	    </tr>
	    {/if}
	{/foreach} 
    {/if}
 
{/foreach}

</table>
</div>
 <script>
 $(document).on('input propertychange', '.input_field', function(evt) {
 
 
     if ($('#' + $(this).attr('id') + '_container').attr('server_validation')) {
         var delay = 200;
     } else {
         var delay = 10;
     }
     if (window.event && event.type == "propertychange" && event.propertyName != "value") return;
     delayed_on_change_field($(this), delay)
 });

 
 $(document).on('input propertychange', '.address_input_field', function(evt) {
     if ($('#' + $(this).attr('id') + '_container').attr('server_validation')) {
         var delay = 200;
     } else {
         var delay = 10;
     }
     if (window.event && event.type == "propertychange" && event.propertyName != "value") return;
     delayed_on_change_address_field($(this), delay)
 });

 $(".confirm_input_field").on("input propertychange", function(evt) {
     if (window.event && event.type == "propertychange" && event.propertyName != "value") return;
     on_changed_confirm_value($(this).attr('confirm_field'), $(this).val())
 });

 $("#fields").on("click", "#show_new_email_field", function() {

     $('#new_email_field').removeClass('hide')
     open_edit_field('{$state._object->get_object_name()}', '{$state.key}', 'new_email')
     $('#show_new_email_field').addClass('hide')
 });
 
  $("#fields").on("click", "#show_new_telephone_field", function() {

     $('#new_telephone_field').removeClass('hide')
     open_edit_field('{$state._object->get_object_name()}', '{$state.key}', 'new_telephone')
     $('#show_new_telephone_field').addClass('hide')
 });
 
 $("#fields").on("click", "#show_new_delivery_address_field", function() {

     $('#new_delivery_address_field').removeClass('hide')
     open_edit_field('{$state._object->get_object_name()}', '{$state.key}', 'new_delivery_address')
     $('#show_new_delivery_address_field').addClass('hide')
 });
 
 

   {if isset($js_code) }
{include file="string:$js_code" } 
{/if}

   
</script> 