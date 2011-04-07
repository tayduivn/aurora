var Dom   = YAHOO.util.Dom;
var Event   = YAHOO.util.Event;


var submit_search_on_enter=function(e,tipo){
     var key;     
     if(window.YAHOO.util.Event)
          key = window.YAHOO.util.Event.keyCode; //IE
     else
          key = e.which; //firefox     

     if (key == 13)
	 submit_search(e,tipo);
};



function init_search(type){
switch(type)
{
case 'users':
search_scope='users';
    var store_name_oACDS = new YAHOO.util.FunctionDataSource(search_users);
  break;
case 'orders':
search_scope='orders';
    var store_name_oACDS = new YAHOO.util.FunctionDataSource(search_orders);
  break;
  case 'orders_store':
 
search_scope='orders_store';
    var store_name_oACDS = new YAHOO.util.FunctionDataSource(search_orders_in_store);
  break;
case 'products':
search_scope='products';
    var store_name_oACDS = new YAHOO.util.FunctionDataSource(search_products);
  break;
  case 'locations':
search_scope='locations';
    var store_name_oACDS = new YAHOO.util.FunctionDataSource(search_locations);
  break;
  case'customers':
search_scope='customers';
    var store_name_oACDS = new YAHOO.util.FunctionDataSource(search_customers);
  break;  
  case 'customers_store':
 var store_name_oACDS = new YAHOO.util.FunctionDataSource(search_customers);
  var search_scope='customers';
  break;
case 'products_store':
 var store_name_oACDS = new YAHOO.util.FunctionDataSource(search_products);
  var search_scope='products';
  break;
case 'part':
search_scope='part';
    var store_name_oACDS = new YAHOO.util.FunctionDataSource(search_part);
  
  break;  
  case 'suppliers':
search_scope='suppliers';
    var store_name_oACDS = new YAHOO.util.FunctionDataSource(search_suppliers);
  
  break;
default:
 var store_name_oACDS = new YAHOO.util.FunctionDataSource(search_all);
  var search_scope='all';

}




     store_name_oACDS.queryMatchContains = true;
     var store_name_oAutoComp = new YAHOO.widget.AutoComplete(search_scope+"_search",search_scope+"_search_Container", store_name_oACDS);

 store_name_oAutoComp.minQueryLength = 0; 
     store_name_oAutoComp.queryDelay = 0.25;
    
    
    
   
   

    
    
     YAHOO.util.Event.addListener(search_scope+"_clean_search", "click",clear_search,search_scope);

  
    YAHOO.util.Event.addListener(search_scope+"_search", "keyup",search_events,search_scope)
    
   
     x= Dom.getX(search_scope+'_clean_search');
     y= Dom.getY(search_scope+'_clean_search');
     Dom.setX(search_scope+"_search_results", x-500);




    Dom.setY(search_scope+"_search_results", y+17);

 Dom.get(search_scope+"_search_results").style.display='none';  




      
}


var submit_search=function(e,data){
//alert("caca")
    if(typeof( data ) == 'string')
	var data={tipo:data,container:data};
    
    var q =Dom.get(data.container+'_search').value;
    if(q=='')
	return;
	
    var request='ar_search.php?tipo='+data.tipo+'&q='+my_encodeURIComponent(q);
       

    YAHOO.util.Connect.asyncRequest('POST',request ,{
	    success:function(o) {
//				alert(o.responseText)
		var r =  YAHOO.lang.JSON.parse(o.responseText);
	
		if (r.state == 200){
		    window.location.href=r.url;
		}else if(r.state==400){
		    
		    Dom.get(data.container+'_search_msg').innerHTML=r.msg1;
		     Dom.get(data.container+'_search_sugestion').innerHTML=r.msg2;
		}else
		    Dom.get(data.container+'_search_msg').innerHTML=r.msg;
	    }
	});
}

function search_part(query){
    search(query,'part','');
}

function search_suppliers(query){
    search(query,'suppliers','');
}

function search_customers(query){
    search(query,'customers','customers');
}
function search_customers_in_store(query){
    search(query,'customers','store');
}
function search_products_in_store(query){
    search(query,'all','products');
}

function search_all(query){

    search(query,'all','all');
}

function search_products(query){
    search(query,'all','products');
}

function search_orders(query){
    search(query,'all','orders');
}

function search_orders_in_store(query){
    search(query,'orders_store','store');
}

function search_users(query){
    search(query,'users','');
}
function search_locations(query){
    search(query,'all','locations');
}

function search_locations_in_warehouse(query){
    search(query,'locations','warehouse');

}



function go_to_result(){
    location.href=this.getAttribute('link')+this.getAttribute('key');
}


function search(query,subject,search_scope){





    var ar_file='ar_search.php';

    var request='tipo='+subject+'&q='+query+'&scope='+search_scope;

    YAHOO.util.Connect.asyncRequest(
				    'POST',
				    ar_file, {
					success:function(o) {
				//	alert(o.responseText)
					    var r = YAHOO.lang.JSON.parse(o.responseText);
					    if (r.state == 200) {
					
						    Dom.get(search_scope+'_search_results').removeChild(Dom.get(search_scope+'_search_results_table'));
                            oTbl=document.createElement("Table");
                            Dom.get(search_scope+'_clean_search').src='art/icons/cross_bw.png';
			 			    Dom.get(search_scope+'_search_results').style.display='';
					    
						    
						    var result_number=1;
						  
						    Dom.addClass(oTbl,'search_result');
						    
						    oTR= oTbl.insertRow(-1);
						    
						    oTR.setAttribute('key',r.q);
							oTR.setAttribute('link','search.php?subject='+subject+'&q=');
							Dom.addClass(oTR,'result');
						    var oTD= oTR.insertCell(0);
							Dom.addClass(oTD,'naked');
						    var oTD= oTR.insertCell(1);
						    if(r.results==0){
						    	oTD.innerHTML="No results found, try the a more comprensive search click here.";

							}else{
								oTD.innerHTML="Posible search results below. for a page with all results click here.";

							}
							
							
							
							if(subject=='all')
							oTD.setAttribute('colspan', '4');
							else
							oTD.setAttribute('colspan', '3');
						    
						     
                                Dom.addClass(oTR,'selected');
							    oTR.setAttribute('prev',1);
							    
						    oTR.onclick = go_to_result;
						    
						  
						    var link=r.link;
						  
						    
						   if(r.results>0){
						    
						    for(result_key in r.data){
							    oTR= oTbl.insertRow(-1);
	Dom.addClass(oTR,'result');
							    var oTD= oTR.insertCell(0);
							    Dom.addClass(oTD,'naked');
					

							    if(subject=='customers'){
							        oTR.setAttribute('key',result_key);
							        oTR.setAttribute('link',link);
							        var oTD= oTR.insertCell(1);
							        oTD.innerHTML=r.data[result_key ].key;
							        var oTD= oTR.insertCell(2);
							        oTD.innerHTML=r.data[result_key ].name;
							        var oTD= oTR.insertCell(3);
							        oTD.innerHTML=r.data[result_key ].address;
							    }else if(subject=='orders' || subject=='orders_store'){
							        oTR.setAttribute('key',result_key);
							        oTR.setAttribute('link',link);
							        var oTD= oTR.insertCell(1);
							    oTD.innerHTML=r.data[result_key ].public_id;
							    var oTD= oTR.insertCell(2);
							    oTD.innerHTML=r.data[result_key ].customer;
							    var oTD= oTR.insertCell(3);
							    oTD.innerHTML=r.data[result_key ].state;
							    var oTD= oTR.insertCell(4);
							    oTD.innerHTML=r.data[result_key ].balance;
							}else if(subject=='part'){
							    oTR.setAttribute('key',r.data[result_key].sku);
							    oTR.setAttribute('link',r.data[result_key].link);
							   	oTR.setAttribute('sku',r.data[result_key].fsku);
							    oTR.setAttribute('description',r.data[result_key].description);


							    var oTD= oTR.insertCell(1);
							    oTD.innerHTML=r.data[result_key ].fsku;
							    var oTD= oTR.insertCell(2);
							    oTD.innerHTML=r.data[result_key ].description ;
							  

							}else if(subject=='all'){
							    oTR.setAttribute('key',r.data[result_key ].key);
							    oTR.setAttribute('link',r.data[result_key ].link);
							   var oTD= oTR.insertCell(1);
							    oTD.innerHTML=r.data[result_key ].store_code;
                                var oTD= oTR.insertCell(2);
							    oTD.innerHTML='<img src="art/icons/'+r.data[result_key ].icon+'" alt="'+r.data[result_key ].subject+'" />';
							    Dom.setStyle(oTD,'width',20)
							    var oTD= oTR.insertCell(3);
							    oTD.innerHTML=r.data[result_key ].image;
							    //var oTD= oTR.insertCell(4);
							    //oTD.innerHTML=r.data[result_key ].name;
							    var oTD= oTR.insertCell(4);
							    oTD.innerHTML=r.data[result_key ].description;

							}else if(subject=='products'){
							    oTR.setAttribute('key',r.data[result_key ].key);
							    oTR.setAttribute('link',r.data[result_key ].link);
							   

							    var oTD= oTR.insertCell(1);
							    oTD.innerHTML=r.data[result_key ].image;
							    var oTD= oTR.insertCell(2);
							    oTD.innerHTML=r.data[result_key ].code;
							    var oTD= oTR.insertCell(3);
							    oTD.innerHTML=r.data[result_key ].description;

							}else if(subject=='locations'){
							    oTR.setAttribute('key',r.data[result_key ].key);
							    oTR.setAttribute('link',r.data[result_key ].link);
							    
							      var oTD= oTR.insertCell(1);
							    oTD.innerHTML=r.data[result_key ].code;
							    var oTD= oTR.insertCell(2);
							    oTD.innerHTML=r.data[result_key ].area;
							     var oTD= oTR.insertCell(3);
							    oTD.innerHTML=r.data[result_key ].use;
							    
							    if(r.data[result_key ].type=='Part'){
							   
							    var oTD= oTR.insertCell(4);
							    oTD.innerHTML=r.data[result_key ].sku+' ('+r.data[result_key ].used_in+')';
							    }else{
							    var oTD= oTR.insertCell(4);
							    oTD.innerHTML='';
							   
							  }

							}
							oTR.setAttribute('prev',result_number-1);
						oTR.setAttribute('next',result_number+1);
					   
						if(r.results==result_number){
						        oTR.setAttribute('next',1);
                                }							
						oTR.setAttribute('id','tr_result'+result_number);
						oTR.onclick = go_to_result;
						result_number++;
							
						
						}
						}
						
						oTbl.id=search_scope+'_search_results_table';
						Dom.get(search_scope+'_search_results').appendChild(oTbl);
						 
// 						    
						    

						//}
						
					    }
					},
					    failure:function(o) {
					    alert(o.statusText);
					    callback();
					},
					    scope:this
					    },
				    request
				    
				    );  
    
}




function search_events(e,subject){

   var key;     
     if(window.YAHOO.util.Event)
          key = window.YAHOO.util.Event.keyCode; //IE
     else
          key = e.which; //firefox     

if(key==undefined)
key=e.keyCode

var state=Dom.get(subject+'_search').getAttribute('state');

     if (key == 13 )
	    goto_search_result(subject);
	 else if(key == 40 ){
	     select_next_result(subject);
	 Dom.get(subject+'_search').setAttribute('state','ready');
	 }else if(key == 38 ){
	    select_prev_result(subject);
	    Dom.get(subject+'_search').setAttribute('state','ready');
	 }else if(key == 39  && state=='ready' ){// right arrow
	    goto_search_result(subject);
	 }else if(key == 37   ){// left arrow
	    Dom.get(subject+'_search').setAttribute('state','');
	 }
	 
	 
}

function select_prev_result(subject){
elements_array=Dom.getElementsByClassName('selected', 'tr', subject+'_search_results_table');
tr=elements_array[0];
Dom.removeClass(tr,'selected');
Dom.addClass('tr_result'+tr.getAttribute('prev'),'selected');
}
function select_next_result(subject){
elements_array=Dom.getElementsByClassName('selected', 'tr', subject+'_search_results_table');
tr=elements_array[0];
Dom.removeClass(tr,'selected');
Dom.addClass('tr_result'+tr.getAttribute('next'),'selected');
}

function goto_search_result(subject){
elements_array=Dom.getElementsByClassName('selected', 'tr', subject+'_search_results_table');

tr=elements_array[0];
if(tr!= undefined)
location.href=tr.getAttribute('link')+tr.getAttribute('key');

}
function clear_search(e,subject){
Dom.get(subject+'_search').value='';
Dom.get(subject+'_search_results').style.display='none';
						   
                             Dom.get(subject+'_clean_search').src='art/icons/zoom.png';
}