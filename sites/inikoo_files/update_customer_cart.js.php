var t;
var timer_is_on=0;


<?php global $path;

if($_REQUEST['path']=='1')
	$_path="../";
elseif($_REQUEST['path']=='2')
	$_path="../../";
elseif($_REQUEST['path']=='3')
	$_path="../sites/";
?>


function init(){
//if(Dom.get('order_key').value!=0)
doTimer();
ids=['order_single_product34107'];
Event.addListener(ids, "click", order_single_product);

}





YAHOO.util.Event.onDOMReady(init);

function timedCount()
{
update_cart_stat();
t=setTimeout("timedCount()",10000);
}

function doTimer()
{
if (!timer_is_on)
  {
  timer_is_on=1;
  timedCount();
  }
}

function update_cart_stat(){
order_key=Dom.get('order_key').value;

var ar_file=path+'inikoo_files/ar_edit_orders.php';
	request='tipo=update_order&order_key='+order_key;
//alert(request)
	YAHOO.util.Connect.asyncRequest(
				    'POST',
				    ar_file, {
					success:function(o) {
					    //alert(o.responseText);
					    var r = YAHOO.lang.JSON.parse(o.responseText);
					    if (r.state == 200) {
						
						for(x in r.data){
							Dom.get('basket_total').innerHTML=r.data['order_total'];
							Dom.get('basket_items').innerHTML=r.data['ordered_products_number'];
							//alert('ok');
						    //Dom.get(x).innerHTML=r.data[x];
						}
							
				
					    } else {
						alert(r.msg);
						//	callback();
					    }
					},
					    failure:function(o) {
					    alert(o.statusText);
					    // callback();
					},
					    scope:this
					    },
				    request
				    
				    );  
}


function order_single_product(pid){
if(isNaN(parseInt(Dom.get('qty'+pid).value))){
	alert('Invalid Qty'); return;
}

	
	Dom.get('loading'+pid).innerHTML='<img src="'+path+'inikoo_files/art/loading.gif"/>';
	var pid=Dom.get('pid'+pid).value;
	var order_key=Dom.get('order_id'+pid).value;

	var old_qty=Dom.get('old_qty'+pid).value;
	var new_qty=parseInt(Dom.get('qty'+pid).value) + parseInt(old_qty);
	var user_key=Dom.get('user_key').value;
	//alert(pid+' ' + order_id+ ' ' + qty);
	//alert(new_qty);
	

	
 var ar_file=path+'inikoo_files/ar_edit_orders.php';
	request='tipo=is_order_exist&id='+order_key+'&key=quantity&newvalue='+new_qty+'&oldvalue=0&pid='+ pid+'&user_key='+user_key;
	//request='tipo=edit_new_order&id='+order_key+'&key=quantity&newvalue=1&oldvalue=0&pid='+ pid;
alert(request)
	YAHOO.util.Connect.asyncRequest(
				    'POST',
				    ar_file, {
					success:function(o) {
					    alert(o.responseText);
					    var r = YAHOO.lang.JSON.parse(o.responseText);
					    if (r.state == 200) {
						for(x in r.data){
							Dom.get('old_qty'+pid).value=new_qty;
							Dom.get('qty'+pid).value=new_qty;
							Dom.get('loading'+pid).innerHTML='<img src="'+path+'inikoo_files/art/icons/accept.png"/>';
							Dom.get('basket_total').innerHTML=r.data['order_total'];
							Dom.get('basket_items').innerHTML=r.data['ordered_products_number'];
							Dom.get('order_key').value=r.key;
							
							//alert('ok');
						    //Dom.get(x).innerHTML=r.data[x];
						}
							
				
					    } else {
						alert(r.msg);
						//	callback();
					    }
					},
					    failure:function(o) {
					    alert(o.statusText);
					    // callback();
					},
					    scope:this
					    },
				    request
				    
				    );  
}