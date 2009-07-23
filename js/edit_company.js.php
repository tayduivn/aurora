<?
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2009 LW
include_once('../common.php');
include_once('../classes/Contact.php');
include_once('../classes/Company.php');

$contact_id=$_SESSION['state']['contact']['id'];
$contact=new contact($contact_id);
//$main_telephone=$contact->get_main_telephone_data();
//$main_fax=$contact->get_main_fax_data();
//$main_mobile=$contact->get_main_mobile_data();
//$main_address=$contact->get_main_address_data();


$edit_block='personal';
if(isset($_REQUEST['edit'])){
$valid_edit_blocks=array('personal','work','pictures','others');
if(in_array($_REQUEST['edit'],$valid_edit_blocks))
    $edit_block=$_REQUEST['edit'];
}
$salutation="''";
$sql="select `Salutation` from `Salutation Dimension` where `Language Key`=1";
$result=mysql_query($sql);
while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
    $salutation.=',"'.$row['Salutation'].'"';
}
$sql="select `Country Key`,`Country Name`,`Country Code` from `Country Dimension`";
$result=mysql_query($sql);
$country_list='';

while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
    $country_list.=',{"id":"'.$row['Country Key'].'","name":"'.$row['Country Name'].'","code":"'.$row['Country Code'].'"}  ';
}
$country_list=preg_replace('/^\,/','',$country_list);






if( !isset($_REQUEST['id']) or !is_numeric($_REQUEST['id'])  ){
    $company_key=$_SESSION['state']['company']['id'];
}else
    $company_key=$_REQUEST['id'];
$scope_key=$company_key;
$scope='company';
if( isset($_REQUEST['scope'])    ){
    $scope=$_REQUEST['scope'];
}
if( isset($_REQUEST['scope_key'])    ){
    $scope=$_REQUEST['scope_key'];
}

print "var company_key=$company_key;";

$company=new Company($company_key);
$addresses=$company->get_addresses(1);

$address_data="\n";
$address_data.=sprintf('{"key":0,"country":"","country_code":"UNK","country_d1":"","country_d2":"","town":"","postal_code":"","town_d1":"","town_d2":"","fuzzy":"","street":"","building":"","internal":"","type":["Office"],"description":"","function":["Contact"],"descriptions":[{"Contact":""}]} ' );
 $address_data.="\n";
foreach($addresses as $index=>$address){
    $address->set_scope($scope,$scope_key);




    $type="[";
    foreach($address->get('Type') as $_type){
	$type.=prepare_mysql($_type,false).",";
    }
    $type.="]";
    $type=preg_replace('/,]$/',']',$type);
    
    $function="[";
    foreach($address->get('Function') as $value){
	$function.=prepare_mysql($value,false).",";
    }
    $function.="]";
    $function=preg_replace('/,]$/',']',$function);
    
    $descriptions="[";
    foreach($address->get('Descriptions') as $key=>$value){
	$descriptions.='{'.prepare_mysql($key,false).':'.prepare_mysql($value,false)."},";
    }
    $descriptions.="]";
    $descriptions=preg_replace('/,]$/',']',$descriptions);


  $address_data.=sprintf(',{"key":%d,"country":%s,"country_code":%s,"country_d1":%s,"country_d2":%s,"town":%s,"postal_code":%s,"town_d1":%s,"town_d2":%s,"fuzzy":%s,"street":%s,"building":%s,"internal":%s,"type":%s,"description":%s,"function":%s,"descriptions":%s} ',
			
			 $address->id
			 ,prepare_mysql($address->data['Address Country Name'],false)
			 ,prepare_mysql($address->data['Address Country Code'],false)
			 ,prepare_mysql($address->data['Address Country Primary Division'],false)
			 ,prepare_mysql($address->data['Address Country Secondary Division'],false)
			 ,prepare_mysql($address->data['Address Town'],false)
			 ,prepare_mysql($address->data['Address Postal Code'],false)
			 ,prepare_mysql($address->data['Address Town Primary Division'],false)
			 ,prepare_mysql($address->data['Address Town Secondary Division'],false)
			 ,prepare_mysql($address->data['Address Fuzzy'],false)
			 ,prepare_mysql($address->display('street',false),false)
			 ,prepare_mysql($address->data['Address Building'],false)
			 ,prepare_mysql($address->data['Address Internal'],false)
			 ,$type
			 ,prepare_mysql($address->get('Description'),false)
			 ,$function
			 ,$descriptions
			 );
  $address_data.="\n";

} 

?>
    
var Dom   = YAHOO.util.Dom;
var Event = YAHOO.util.Event;

var Country_Address_Labels=[
			  {
			     'UNK':{
				  'country_d1':{'name':'Region','oname':'Region','hide':false,'in_use':true}
				  ,'country_d2':{'name':'Subregion','oname':'Subregion','hide':false,'in_use':true}
				  
			      }
			      ,'GBR':{
				  'country_d1':{'name':'Union Country','oname':'Union Country','hide':true,'in_use':true}
				  ,'country_d2':{'name':'County','oname':'County','hide':false,'in_use':true}
			      }
			      ,'MEX':{
				  'country_d1':{'name':'State','oname':'Estado','hide':false,'in_use':true}
				  ,'country_d2':{'name':'Municipality','oname':'Municipio','hide':true,'in_use':true}
			     }
			      ,'USA':{
				  'country_d1':{'name':'State','oname':'State','hide':false,'in_use':true}
				  ,'country_d2':{'name':'County','oname':'County','hide':true,'in_use':true}
				  ,'postal_code':{'name':'Postal Code','oname':'Zip','hide':true,'in_use':true}
			     }
			     ,'IRL':{
				 'country_d1':{'name':'County','oname':'Co','hide':false,'in_use':true}
				 ,'country_d2':{'in_use':false}
				 ,'postal_code':{'in_use':false}
			     }
			  }
		       ];

var Country_List=[<?=$country_list?>];
var Address_Data=[<?=$address_data?>];
var Address_Keys=["key","country","country_code","country_d1","country_d2","town","postal_code","town_d1","town_d2","fuzzy","street","building","internal"];
var Address_Meta_Keys=["type","function","description","descriptions"];

var current_salutation='salutation<?=$contact->get('Salutation Key')?>';
var current_block='<?=$edit_block?>';
var old_salutation=current_salutation;
var Current_Address_Index=0;

var changes_details=0;
var changes_address=0;

var saved_details=0;
var error_details=0;



var CountryDS = new YAHOO.widget.DS_JSFunction(function (sQuery) {
	if (!sQuery || sQuery.length == 0) return false;
	var query = sQuery.toLowerCase();
	var aResults = [];
	
	code_match='';
	if(query.length==3){
	    for(var i = 0 ; i < Country_List.length ; i++) {
		var desc = Country_List[i].c.toLowerCase();
		if( query==desc  ) {
		    aResults.push([Country_List[i].n, Country_List[i]]);
		    code_match=Country_List[i].c;
		    break;
		}
	    }
	    

	}


	patt1 = new RegExp("^"+query); 
	
	for(var i = 0 ; i < Country_List.length ; i++) {
	    var desc = Country_List[i].n.toLowerCase();
	    if( desc.match(patt1) ) {
		if(code_match!= Country_List[i].c )
		    aResults.push([Country_List[i].n, Country_List[i]]);
		
	    }
	}
	return aResults;
    });
CountryDS.maxCacheEntries = 100;


function update_full_address(){
    var full_address=Dom.get(current_salutation).innerHTML+' '+Dom.get("v_first_name").value+' '+Dom.get("v_surname").value;
    Dom.get("full_name").value=full_address;
    calculate_num_changed_in_personal()
}


function update_salutation(o){
    if(Dom.hasClass(o, 'selected'))
	return;
    Dom.removeClass(current_salutation, 'selected');
    Dom.addClass(o, 'selected');
    current_salutation=o.id;
    calculate_num_changed_in_personal()
    update_full_address();

}

function calculate_num_changed_in_personal(){
    var changes=0;
    if(current_salutation!=old_salutation)
	changes++;
    
    var first_name=Dom.get("v_first_name");
    if(first_name.getAttribute('ovalue')!=first_name.value)
	changes++;
    
    var surname=Dom.get("v_surname");
    if(surname.getAttribute('ovalue')!=surname.value)
	changes++;
    
    Dom.get("personal_num_changes").innerHTML=changes;

}
var change_block = function(e){
    if(Dom.hasClass(this, 'selected'))
	return;
    Dom.removeClass(current_block, 'selected');
    Dom.addClass(this, 'selected');
    Dom.setStyle('d_'+current_block, 'display','none');
    Dom.setStyle('d_'+this.id, 'display','');

    current_block=this.id;
    
};



var save_details=function(e){
    var items = ["name","fiscal_name","tax_number","registration_number"];
    var table='company';
    save_details=0;
    for ( var i in items )
	{
	    var key=items[i];
	    var value=Dom.get(items[i]).value;
	    var request='ar_edit_contacts.php?tipo=edit_'+escape(table)+'&key=' + key + '&value=' + escape(value)+'&id='+company_key; 
	   
	    YAHOO.util.Connect.asyncRequest('POST',request ,{
		    success:function(o) {
			//alert(o.responseText);
			var r =  YAHOO.lang.JSON.parse(o.responseText);
			if(r.action=='updated'){
			    Dom.get(items[i]).value=r.value;
			    Dom.get(items[i]).getAttribute('ovalue')=Dom.get(items[i]).value;
			    save_details++;
			}else if(r.action=='error'){
			    alert(r.msg);
			}
			    

			
		    }
		});

	} 
    
}
var cancel_save_details=function(e){
    var items = ["name","fiscal_name","tax_number","registration_number"];
    for ( var i in items )
	{
	    Dom.get(items[i]).value=Dom.get(items[i]).getAttribute('ovalue');
	} 
    
    Dom.get('details_messages').innerHTML='';
    Dom.setStyle(['save_details_button', 'cancel_save_details_button'], 'display', 'none'); 
}

var update_details=function(e){
    var changes=0;
    
    var items = ["name","fiscal_name","tax_number","registration_number"];
    for ( var i in items )
	{
	    if(Dom.get(items[i]).value!=Dom.get(items[i]).getAttribute('ovalue'))
		changes++; 
	} 

    
    if(changes==0){
	Dom.get('details_messages').innerHTML='';
	Dom.setStyle(['save_details_button', 'cancel_save_details_button'], 'display', 'none'); 
    }else if (changes==1){
	Dom.get('details_messages').innerHTML=changes+'<?=' '._('change')?>';
	Dom.setStyle(['save_details_button', 'cancel_save_details_button'], 'display', ''); 
    }else{
	Dom.get('details_messages').innerHTML=changes+'<?=' '._('changes')?>';
	Dom.setStyle(['save_details_button', 'cancel_save_details_button'], 'display', ''); 
    }


};


    





function init(){
    
    //   var ids = ["personal","pictures","work","other"]; 
    //	YAHOO.util.Event.addListener(ids, "click", change_block);
    YAHOO.util.Event.addListener('save_details_button', "click",save_details );

    YAHOO.util.Event.addListener('cancel_save_details_button', "click",cancel_save_details );
    YAHOO.util.Event.addListener('add_address_button', "click",edit_address,0 );


    var ids = ["name","fiscal_name","tax_number","registration_number"]; 
    YAHOO.util.Event.addListener(ids, "keyup", update_details);
    
    var ids = ["address_country_d1","address_country_d2","address_town","address_town_d2","address_town_d1","address_postal_code","address_street","address_internal","address_building"]; 
    YAHOO.util.Event.addListener(ids, "keyup", update_address);
    YAHOO.util.Event.addListener(ids, "change", update_address);
    //TODO: event when paste with the middle mouse (peroblem in  linux only)
    
    // addEvent(Dom.get('address_internal'), 'paste', update_address);

    

     // Use a FunctionDataSource
    var Countries_DS = new YAHOO.util.FunctionDataSource(match_country);
    Countries_DS.responseSchema = {
        fields: ["id", "name", "code"]
    }

    // Instantiate AutoComplete
    var Countries_AC = new YAHOO.widget.AutoComplete("address_country", "address_country_container", Countries_DS);
    Countries_AC.useShadow = true;
    Countries_AC.resultTypeList = false;

    // Custom formatter to highlight the matching letters
    Countries_AC.formatResult = function(oResultData, sQuery, sResultMatch) {
        var query = sQuery.toLowerCase(),
	name = oResultData.name,
	code = oResultData.code,
	
	query = sQuery.toLowerCase(),
	nameMatchIndex = name.toLowerCase().indexOf(query),
	codeMatchIndex = code.toLowerCase().indexOf(query),
        
	displayname, displaycode;
            
        if(nameMatchIndex > -1) {
            displayname = highlightMatch(name, query, nameMatchIndex);
        }
        else {
            displayname = name;
        }

        if(codeMatchIndex > -1) {
            displaycode = highlightMatch(code, query, codeMatchIndex);
        }
        else {
            displaycode = code;
        }

     
        return displayname + " (" + displaycode + ")";
        
    };

    // Helper function for the formatter
    var highlightMatch = function(full, snippet, matchindex) {
        return full.substring(0, matchindex) + 
                "<span class='match'>" + 
                full.substr(matchindex, snippet.length) + 
                "</span>" +
                full.substring(matchindex + snippet.length);
    };

    // Define an event handler to populate a hidden form field
    // when an item gets selected and populate the input field
    var myHiddenField = YAHOO.util.Dom.get("address_country_code");
    var onCountrySelected = function(sType, aArgs) {
        var myAC = aArgs[0]; // reference back to the AC instance
        var elLI = aArgs[1]; // reference to the selected LI element
        var oData = aArgs[2]; // object literal of selected item's result data
        
        // update hidden form field with the selected item's ID
        myHiddenField.value = oData.id;
        
        myAC.getInputEl().value = oData.name + " (" + oData.code + ") ";

	update_address_labels(oData.code);

    };
    Countries_AC.itemSelectEvent.subscribe(onCountrySelected);

 

} 
YAHOO.util.Event.onDOMReady(init);