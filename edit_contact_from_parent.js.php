var Contact_Changes=0;
var Contact_Name_Changes=0;
var Contact_Details_Changes=0;

var Contact_Email_Changes=0;

var Contact_Emails_to_edit=0;
var Contact_Emails_to_delete=0;
var Contact_Emails_to_add=0;

var Contact_Mobile_Changes=0;
var Contact_Telephone_Changes=0;
var Contact_Fax_Changes=0;
var Contact_Address_Changes=0;

var Contact_Personal_Keys=['Contact_First_Name','Contact_Salutation','Contact_Surname','Contact_Suffix','Contact_Gender','Contact_Title','Contact_Profession'];
var Contact_Name_Keys=['Contact_First_Name','Contact_Salutation','Contact_Surname','Contact_Suffix'];
var Contact_Details_Keys=['Contact_Gender','Contact_Title','Contact_Profession'];
var Email_Keys=['Email','Email_Description','Email_Contact_Name','Email_Is_Main'];


var Number_New_Emails=0;
var Number_New_Empty_Emails=0;

var Number_New_Mobiles=0;
var Number_New_Contact_Address=0;

var save_contact=function(){

    var table='contact';
    if(Dom.get('Contact_Key').value==0)
	create_contact();
    else
	var contact_key=Dom.get('Contact_Key').value;
    
    save_contact_elements=0;

    

    if(Contact_Name_Changes>0 || Contact_Details_Changes>0){
	var name_value=new Object();
	var items=Contact_Name_Keys;
	for(i in items){
	    name_value[items[i]]=Dom.get(items[i]).value;
	}
	var value=new Object();
	var items=Contact_Details_Keys;
	for(i in items){
	    value[items[i]]=Dom.get(items[i]).value;
	}
	value['Contact_Name_Components']=name_value;
	
	var json_value = YAHOO.lang.JSON.stringify(value); 
	var request='ar_edit_contacts.php?tipo=edit_'+escape(table)+ '&value=' + json_value+'&id='+contact_key+'&subject='+Subject+'&subject_key='+Subject_Key; 

	YAHOO.util.Connect.asyncRequest('POST',request ,{
		success:function(o) {
		    //	alert(o.responseText);
		    var r =  YAHOO.lang.JSON.parse(o.responseText);
		if(r.action=='updated'){
		    Dom.get('contact_display'+contact_key).innerHTML=r.xhtml_subject;
		    
		    for(i in r.updated_data){
			var contact_item_value=r.updated_data[i];
			if(contact_item_value==null)contact_item_value='';
			Contact_Data[contact_key][i]=contact_item_value;
		    }
		   
		    save_contact_elements++;
		}else if(r.action=='error'){
		    alert(r.msg);
		}
		
		
		
		}
	    });
    }

     if(Contact_Emails_to_edit>0 || Contact_Emails_to_add>0){
	  var elements_array=Dom.getElementsByClassName('Email', 'input');
	  for( var i in elements_array ){
		var email_key=elements_array[i].getAttribute('email_key');
		if(  email_key>0 || email_key.match('new')){

		    var value=new Object();
		    if( email_key.match('new'))
			value['Email Key']=0;
		    else
			value['Email Key']=email_key;
		    value['Email']=Dom.get('Email'+email_key).value;
		    value['Email Description']=Dom.get('Email_Description'+email_key).value;
		    value['Email Contact Name']=Dom.get('Email_Contact_Name'+email_key).value;
		    value['Email Is Main']=Dom.get('Email_Is_Main'+email_key).value;

		    var json_value = YAHOO.lang.JSON.stringify(value); 
		    
		    var request='ar_edit_contacts.php?tipo=edit_email&value=' + json_value+'&id='+contact_key+'&subject=contact&subject_key='+contact_key; 
		  
		    YAHOO.util.Connect.asyncRequest('POST',request ,{
			    success:function(o) {
				alert(o.responseText);
				var r =  YAHOO.lang.JSON.parse(o.responseText);
				if(r.action=='updated' || r.action=='created'){
				    
				    Dom.get('contact_display'+contact_key).innerHTML=r.xhtml_subject;
				    if(r.action=='created')
					Contact_Data[contact_key]['Emails'][r.email_key]=new Object();
				    for(i in r.updated_data){
					var contact_item_value=r.updated_data[i];
					if(contact_item_value==null)contact_item_value='';
					
				
					
					Contact_Data[contact_key]['Emails'][r.email_key][i]=contact_item_value;
				    }
				    set_main_email(r.main_email_key);
				    save_contact_elements++;
				}else if(r.action=='error'){
				    alert(r.msg);
				}
				
				
		
			    }
			});
		   
		}

	  }


     }

    
    if(Contact_Emails_to_delete>0 ){
	var elements_array=Dom.getElementsByClassName('Email', 'input');
	for( var i in elements_array ){
	    var email_key=elements_array[i].getAttribute('email_key');
	    if(  email_key>0 && Dom.get('Email'+email_key).getAttribute('to_delete')==1  ){
		
		var request='ar_edit_contacts.php?tipo=delete_email&value=' +email_key+'&id='+contact_key+'&subject=contact&subject_key='+contact_key; 
		 YAHOO.util.Connect.asyncRequest('POST',request ,{
			    success:function(o) {
					alert(o.responseText);
				var r =  YAHOO.lang.JSON.parse(o.responseText);
				if(r.action=='deleted' ){
				    
				    Dom.get('contact_display'+contact_key).innerHTML=r.xhtml_subject;

				 
				    delete  Contact_Data[contact_key]['Emails'][r.email_key];
				    set_main_email(r.main_email_key);
					
					

				    save_contact_elements++;
				}else if(r.action=='error'){
				    alert(r.msg);
				}
				
				
		
			    }
			});
		


	    }
	    
	}
	

    }

      cancel_edit_contact();
};


var create_contact=function(){
    
    alert("creating contact");
    return;
    var value=new Object();
    items=Contact_Personal_Keys;
    for(i in items)
	value[items[i]]=Dom.get('contact_'+items[i]).value;
    

    var contact_type_values=new Array();
	var elements_array=Dom.getElementsByClassName('contact_type', 'span');
	for( var i in elements_array ){
	    var element=elements_array[i];
	    var label=element.getAttribute('label');
	    if(Dom.hasClass(element,'selected')){
		contact_type_values.push(label);
	    }
	    
	}
    value['type']=contact_type_values;
    
     var contact_function_values=new Array();
	var elements_array=Dom.getElementsByClassName('contact_function', 'span');
	for( var i in elements_array ){
	    var element=elements_array[i];
	    var label=element.getAttribute('label');
	    if(Dom.hasClass(element,'selected')){
		contact_function_values.push(label);
	    }
	    
	}
    value['function']=contact_function_values;



    var json_value = YAHOO.lang.JSON.stringify(value); 
    

    
    var request='ar_edit_contacts.php?tipo=new_contact&value=' + json_value+'&subject='+Subject+'&subject_key='+Subject_Key; 
    
    YAHOO.util.Connect.asyncRequest('POST',request ,{
	    success:function(o) {
	       	//alert(o.responseText);
		var r =  YAHOO.lang.JSON.parse(o.responseText);
		if(r.action=='created'){

		    

		    var new_contact_data=new Object;
		    for(i in r.updated_data){
			var contact_item_value=r.updated_data[i];
			if(contact_item_value==null)contact_item_value='';
			new_contact_data[i]=contact_item_value;
		    }

		    Contact_Data[r.contact_key]=new Object;
		    Contact_Data[r.contact_key]=new_contact_data;
		    cancel_edit_contact();
		    
		    var new_contact_container = Dom.get('contact_container0').cloneNode(true);
		    new_contact_container.id = 'contact_containe'+r.contact_key;
		    Dom.setStyle(new_contact_container, 'display', ''); 
		    display_element=Dom.getElementsByClassName('contact_display' ,'div',  new_contact_container);
		    display_element[0].innerHTML=r.xhtml_contact;
		    display_element[0].id = 'contact_display'+r.contact_key;
		    display_element=Dom.getElementsByClassName('contact_buttons' ,'div',  new_contact_container);
		    display_element[0].id = 'contact_buttons'+r.contact_key;
		    display_element=Dom.getElementsByClassName('small_button_edit' ,'span', display_element[0] );
		    display_element[0].id = 'contacts_contact_butto'+r.contact_key;
		    display_element[1].id = 'delete_contact_button'+r.contact_key;
		    display_element[2].id = 'edit_contact_butto'+r.contact_key;
		    display_element[0].setAttribute('contact_id',r.contact_key);
		    display_element[1].setAttribute('contact_id',r.contact_key);
		    display_element[2].setAttribute('contact_id',r.contact_key);


		    //new_contact_container.children[1][0].id='delete_contact_button'+r.contact_key;
		    //new_contact_container.children[1][0].setAttribute('contact_id',r.contact_key);
		    //new_contact_container.children[1][1].id='edit_contact_button'+r.contact_key;
		    //new_contact_container.children[1][1].setAttribute('contact_id',r.contact_key);
		    Dom.get('contact_showcase').appendChild(new_contact_container);

		    //new_contact_container.parent.appendChild(new_contact_container);
		    save_contact_elements++;
		}else if(r.action=='error'){
		    alert(r.msg);
		}
		
		
		
		}
	    });
   
}


var update_contact_buttons=function(){
    if(Contact_Changes>0){
	 Dom.setStyle(['save_edit_contact'], 'display', ''); 
    }

}


var cancel_edit_contact=function (){

     Contact_Changes=0;
     Contact_Name_Details_Changes=0;
     Contact_Email_Changes=0;
     Contact_Mobile_Changes=0;
     Contact_Telephone_Changes=0;
     Contact_Fax_Changes=0;
     Contact_Address_Changes=0;
     Number_New_Emails=0;
     Number_New_Empty_Emails=0;
     
     Number_New_Mobiles=0;
     Number_New_Contact_Address=0;
     
     index=Dom.get("cancel_edit_contact_button").getAttribute('contact_key');
     Dom.setStyle(['contact_showcase','add_contact_button'], 'display', ''); 
    Dom.setStyle(['contact_form','cancel_edit_contact_button','save_contact_button'], 'display', 'none'); 
    Dom.get("cancel_edit_contact_button").setAttribute('contact_key','');
    Dom.get("contact_messages").innerHTML='';

    
    var elements_to_clean=['Contact_Name','Contact_Salutation','Contact_First_Name','Contact_Surname','Contact_Suffix','Contact_Title','Contact_Profession'];
    for (i in elements_to_clean){
	var element_to_clean=elements_to_clean[i];
	Dom.get(element_to_clean).value='';Dom.get(element_to_clean).setAttribute('ovalue','');
    }
    var elements_to_unselect=Dom.getElementsByClassName('Contact_Gender');
    Dom.removeClass(elements_to_unselect,'selected');
    Dom.get('Contact_Gender').value='Unknown';
    Dom.get('Contact_Gender').setAttribute('ovalue','Unknown');
    Dom.addClass('Contact_Gender_Unknown','selected');
    
    var elements_to_unselect=Dom.getElementsByClassName('Contact_Salutation');
    Dom.removeClass(elements_to_unselect,'selected');


    elements_to_delete=Dom.getElementsByClassName('cloned_editor');
    for (i in elements_to_delete){
	var parent=elements_to_delete[i].parentNode;
	//alert(elements_to_delete.parentNode+' '+elements_to_delete.parent+' '+elements_to_delete)
	parent.removeChild(elements_to_delete[i]);
    }
    
 
	
 


};

var delete_contact=function (e,contact_button){

}






var edit_contact=function (e,contact_button){
    
   
    if(contact_button==false)
	index=0;
    else
	index=contact_button.getAttribute('contact_id')

    Current_Contact_Index=index;
    changes_contact=0;
    Dom.setStyle(['contact_showcase','move_contact_button','add_contact_button'], 'display', 'none'); 
    Dom.setStyle(['contact_form','cancel_edit_contact_button'], 'display', ''); 
    Dom.get("cancel_edit_contact_button").setAttribute('contact_key',index);
    
    Dom.get('Contact_Key').value=Current_Contact_Index;
    if(Current_Contact_Index==0){
	Dom.get("save_contact_button").innerHTML='<?php echo _('Save New Contact')?>';
	Dom.get("cancel_edit_contact_button").innerHTML='<?php echo _('Cancel Add New Contact')?>';
    }else{
	Dom.get("save_contact_button").innerHTML='<?php echo _('Save Changes')?>';
	Dom.get("cancel_edit_contact_button").innerHTML='<?php echo _('Cancel Edit Contact')?>';
    }
    
    data=Contact_Data[index];
   
    for (key in data){
	


	if(key=='Name_Data'){
	  
	    var contact_name_parts=data[key];
	    for (key2 in contact_name_parts){

		    if(key2=='Contact_Salutation'){
			Dom.addClass('Contact_Salutation_'+contact_name_parts[key2],'selected');
			
		    }
			
		    item2=Dom.get(key2);
		    item2.value=contact_name_parts[key2];
		    item2.setAttribute('ovalue',contact_name_parts[key2]);
		    
	    }
	}else if(key=='Contact_Gender'){
	    var elements_to_unselect=Dom.getElementsByClassName('Contact_Gender');
	    Dom.removeClass(elements_to_unselect,'selected');
	    Dom.addClass('Contact_Gender_'+data[key],'selected');
	    Dom.get('Contact_Gender').value=data[key];
	    Dom.get('Contact_Gender').setAttribute('ovalue',data[key]);

	}else if(key=='Mobiles'){
	  var mobiles=data['key'];
	  for (mobile_key in mobiles) {
	    var mobile_data=emails[mobiles_key];
	    if(mobile_data==null)
	      continue;
	    clone_mobiles(mobile_key);
	    
	  }


	}else if(key=='Emails'){
	    var emails=data[key];
	    for (email_key in emails) {
		    var email_data=emails[email_key];
		    
		    if(email_data==null)
			continue;
		    clone_email(email_key);
		   
		    Dom.get('Email'+email_key).value=email_data['Email'];
		    Dom.get('Email'+email_key).setAttribute('ovalue',email_data['Email']);
		    validate_email(Dom.get('Email'+email_key));

		    Dom.get('Email_Contact_Name'+email_key).value=email_data['Email_Contact_Name'];
		    Dom.get('Email_Contact_Name'+email_key).setAttribute('ovalue',email_data['Email_Contact_Name']);
		    Dom.get('Email_Description'+email_key).value=email_data['Email_Description'];
		    Dom.get('Email_Description'+email_key).setAttribute('ovalue',email_data['Email_Description']);
		    

		    if(email_data['Email_Is_Main']=='Yes')
			Dom.get('Email_Is_Main'+email_key).checked=true;
		    else
			Dom.get('Email_Is_Main'+email_key).checked=false;
		    Dom.get('Email_Is_Main'+email_key).value=email_data['Email_Is_Main'];
		    Dom.get('Email_Is_Main'+email_key).setAttribute('ovalue',email_data['Email_Is_Main']);


		    Dom.addClass(Dom.get("Email_Description_"+email_data['Email_Description']+email_key),'selected');
		    
		   
		    


		}
		
	    
	    
	}else if(key=='Addresses'){
	    var addresses=data[key];
	    for (address_key in addresses) {
		var address_data=addresses[address_key];
		var new_address_container = Dom.get('address_mould').cloneNode(true);
		var the_parent=Dom.get('last_tr').parentNode;
		var insertedElement = the_parent.insertBefore(new_address_container,Dom.get('last_tr') );
		var element_array=Dom.getElementsByClassName('tr_telecom', 'tr',insertedElement);
		element_array[0].id='telephone_mould'+address_key;
		element_array[1].id='fax_mould'+address_key;
		element_array[2].id='after_fax'+address_key;

		Dom.addClass(insertedElement,'cloned_editor');
		Dom.setStyle(insertedElement,'display','');
		var element_array=Dom.getElementsByClassName('Address', 'td',insertedElement);
		element_array[0].innerHTML=address_data['Address'];


		tels=address_data['Telephones'];
		for(tel_key in tels) {
		    var tel_data=tels[tel_key];
		    var new_tel_container = Dom.get('telephone_mould'+address_key).cloneNode(true);
		    var the_parent=Dom.get('fax_mould'+address_key).parentNode;

		    var insertedElement = the_parent.insertBefore(new_tel_container,Dom.get('fax_mould'+address_key) );
		    Dom.addClass(insertedElement,'cloned_editor');
		    Dom.setStyle(insertedElement,'display','');
		    var element_array=Dom.getElementsByClassName('Telephone', 'input',insertedElement);
		    element_array[0].value=tel_data['Telephone'];
		}
		faxes=address_data['Faxes'];
		for(fax_key in faxes) {
		    var fax_data=faxes[fax_key];
		    var new_fax_container = Dom.get('fax_mould'+address_key).cloneNode(true);
		    var the_parent=Dom.get('after_fax'+address_key).parentNode;
		    var insertedElement = the_parent.insertBefore(new_fax_container,Dom.get('after_fax'+address_key) );
		    Dom.addClass(insertedElement,'cloned_editor');
		    Dom.setStyle(insertedElement,'display','');
		    var element_array=Dom.getElementsByClassName('Fax', 'input',insertedElement);
		    element_array[0].value=fax_data['FAX'];
		}
		

	    }
		


	}else{
	   
	    item=Dom.get(key);
	    item.value=data[key];
	    item.setAttribute('ovalue',data[key]);

	}
	

	
    }
    
    
};


  






    
var render_after_contact_item_change=function(){
    
    Contact_Changes=Contact_Name_Changes+Contact_Email_Changes+Contact_Mobile_Changes;
	
    if(Contact_Changes==0){
	Dom.get('contact_messages').innerHTML='';
	Dom.setStyle(['save_contact_button', 'cancel_save_contact_button'], 'display', 'none'); 
    }else if (Contact_Changes==1){
	    Dom.get('contact_messages').innerHTML=Contact_Changes+'<?php echo' '._('change')?>';
	    Dom.setStyle(['save_contact_button', 'cancel_save_contact_button'], 'display', ''); 
    }else{
	Dom.get('contact_messages').innerHTML=Contact_Changes+'<?php echo' '._('changes')?>';
	Dom.setStyle(['save_contact_button', 'cancel_save_contact_button'], 'display', ''); 
    }
};



function calculate_num_changed_in_personal_details(){
    var changes=0;
   
    var items=Contact_Details_Keys;
    
    for (i in items){
	var item=Dom.get(items[i]);

	if(item.getAttribute('ovalue')!=item.value)
	    changes++;
    }
    
  
    Contact_Details_Changes=changes;
}
