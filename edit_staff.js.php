<?php  include_once('common.php'); ?>

var Event = YAHOO.util.Event;
var Dom   = YAHOO.util.Dom;




function init(){

 var ids = ['description','pin']; 
    YAHOO.util.Event.addListener(ids, "click", change_block);

init_search('staff');

validate_scope_data=
{

    'staff_description':{
	'alias':{'changed':false,'validated':true,'required':true,'group':1,'type':'item','name':'Staff_Alias','ar':false,'validation':[{'regexp':"[a-z\\d]+",'invalid_msg':'<?php echo _('Invalid Staff Alias')?>'}]}
	}
    ,'staff_pin':{
	'pin':{'changed':false,'validated':true,'required':true,'group':1,'type':'item','name':'Staff_PIN','ar':false,'validation':[{'regexp':"\\d{4}",'invalid_msg':'<?php echo _('Invalid PIN')?>'}]}
	,'pin_confirm':{'changed':false,'validated':true,'required':true,'group':1,'type':'item','name':'Staff_PIN_Confirm','ar':false,'validation':[{'regexp':"\\d{4}",'invalid_msg':'<?php echo _('Invalid PIN')?>'}]}
	}
};
	
	

	
validate_scope_metadata={
    'staff_description':{'type':'edit','ar_file':'ar_edit_staff.php','key_name':'staff_key','key':Dom.get('staff_key').value}
	,'staff_pin':{'type':'edit','ar_file':'ar_edit_staff.php','key_name':'staff_key','key':Dom.get('staff_key').value}
    

};

    var product_units_oACDS = new YAHOO.util.FunctionDataSource(validate_staff_alias);
    product_units_oACDS.queryMatchContains = true;
    var product_units_oAutoComp = new YAHOO.widget.AutoComplete("Staff_Alias","Staff_Alias_Container", product_units_oACDS);
    product_units_oAutoComp.minQueryLength = 0; 
    product_units_oAutoComp.queryDelay = 0.1;

    var product_units_oACDS = new YAHOO.util.FunctionDataSource(validate_staff_pin);
    product_units_oACDS.queryMatchContains = true;
    var product_units_oAutoComp = new YAHOO.widget.AutoComplete("Staff_PIN","Staff_PIN_Container", product_units_oACDS);
    product_units_oAutoComp.minQueryLength = 0; 
    product_units_oAutoComp.queryDelay = 0.1;

    var product_units_oACDS = new YAHOO.util.FunctionDataSource(validate_staff_pin_confirm);
    product_units_oACDS.queryMatchContains = true;
    var product_units_oAutoComp = new YAHOO.widget.AutoComplete("Staff_PIN_Confirm","Staff_PIN_Confirm_Container", product_units_oACDS);
    product_units_oAutoComp.minQueryLength = 0; 
    product_units_oAutoComp.queryDelay = 0.1;

    Event.addListener('save_edit_staff_description', "click", save_staff);
    Event.addListener('reset_edit_staff_description', "click", reset_staff);

    Event.addListener('save_edit_staff_pin', "click", save_pin);



}

function save_staff(){
	save_edit_general('staff_description');
}

function save_pin(){
	save_edit_general('staff_pin');
}

function reset_staff(){
}

function validate_staff_alias(query){
	validate_general('staff_description','alias',unescape(query));
}

function validate_staff_pin(query){
	validate_pin_with_confirmation();
	validate_general('staff_pin','pin',unescape(query));
	
}

function validate_staff_pin_confirm(query){
	validate_pin_with_confirmation();
	validate_general('staff_pin','pin_confirm',unescape(query));

}

function validate_pin_with_confirmation(){
	if(Dom.get('Staff_PIN').value != Dom.get('Staff_PIN_Confirm').value){
		validate_scope_data['staff_pin']['pin'].validated=false;
		validate_scope_data['staff_pin']['pin_confirm'].validated=false;
	}
	else{
		validate_scope_data['staff_pin']['pin'].validated=true;
		validate_scope_data['staff_pin']['pin_confirm'].validated=true;
	}

}

function change_block(e){
   
     Dom.setStyle(['description_block','pin_block'],'display','none');
 	 Dom.get(this.id+'_block').style.display='';
	 Dom.removeClass(['description','pin'],'selected');
	 Dom.addClass(this, 'selected');
	 YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=staff-edit&value='+this.id ,{});
   
}

YAHOO.util.Event.onDOMReady(init);
