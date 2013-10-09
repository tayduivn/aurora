var Dom   = YAHOO.util.Dom;
var Event =YAHOO.util.Event;


function button_changed(product_pid){
if(Dom.get('but_qty'+product_pid).getAttribute('ovalue')!=Dom.get('but_qty'+product_pid).value){
Dom.setStyle('but_button'+product_pid,'visibility','visible')
}else{
Dom.setStyle('but_button'+product_pid,'visibility','hidden')
}

}

function order_product_from_list_changed(product_pid){
if(Dom.get('qty'+product_pid).getAttribute('ovalue')!=Dom.get('qty'+product_pid).value){
Dom.setStyle('list_button'+product_pid,'visibility','visible')
}else{
Dom.setStyle('list_button'+product_pid,'visibility','hidden')
}

}

function order_product_from_list(product_pid){
qty=Dom.get('qty'+product_pid).value

if(qty<=0 || qty==''){
	qty=0
}

Dom.get('list_button_img'+product_pid).src='art/loading.gif';
request='ar_basket.php?tipo=edit_order_transaction&pid='+product_pid+'&qty='+qty
alert(request)
 YAHOO.util.Connect.asyncRequest(
                    'GET',
                request, {
                success: function (o) {
                alert(o.responseText)
                        var r = YAHOO.lang.JSON.parse(o.responseText);
                          
                          if(r.state==200){
                          Dom.get('basket_total').innerHTML=r.data.order_total
							Dom.get('list_button_img'+product_pid).src='art/icons/basket_add.png';
							if(r.quantity==0)
								r.quantity='';
							Dom.get('qty'+r.product_pid).setAttribute('ovalue',r.quantity)
							Dom.get('qty'+r.product_pid).value=r.quantity

							order_product_from_list_changed(r.product_pid)
                          }else{
                         
                          
                          }
                          
                     
                      
                    },
                    failure: function (o) {
                        alert(o.statusText);
                    },
                scope:this
                }
                );

}

function order_product_from_button(product_pid){
qty=Dom.get('but_qty'+product_pid).value

if(qty<=0 || qty==''){
	qty=0
}

Dom.setStyle('but_button'+product_pid,'visibility','hidden')
Dom.setStyle('but_processing'+product_pid,'display','')

request='ar_basket.php?tipo=edit_order_transaction&pid='+product_pid+'&qty='+qty
//alert(request)
 YAHOO.util.Connect.asyncRequest(
                    'GET',
                request, {
                success: function (o) {
                alert(o.responseText)
                        var r = YAHOO.lang.JSON.parse(o.responseText);
                          
                          if(r.state==200){
                          Dom.get('basket_total').innerHTML=r.data.order_total
Dom.setStyle('but_processing'+product_pid,'display','none')


							if(r.quantity==0)
								r.quantity='';
							Dom.get('but_qty'+r.product_pid).setAttribute('ovalue',r.quantity)
							Dom.get('but_qty'+r.product_pid).value=r.quantity

							order_product_from_list_changed(r.product_pid)
                          }else{
                         
                          
                          }
                          
                     
                      
                    },
                    failure: function (o) {
                        alert(o.statusText);
                    },
                scope:this
                }
                );

}
function init_basket() {

}
Event.onDOMReady(init_basket);