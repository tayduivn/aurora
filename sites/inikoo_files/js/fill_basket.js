var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;

var button_queue_size=0

function button_changed(product_pid) {

    if (Dom.get('but_qty' + product_pid).getAttribute('ovalue') != '') {

        if (Dom.get('but_qty' + product_pid).getAttribute('ovalue') != Dom.get('but_qty' + product_pid).value) {
            Dom.get('order_button_' + product_pid).src = 'art/update_' + Dom.get('site_locale').value + '.png'
        } else {
            Dom.get('order_button_' + product_pid).src = 'art/ordered_' + Dom.get('site_locale').value + '.png'

        }
    }
}

function order_product_from_list_changed(product_pid) {
    if (Dom.get('qty' + product_pid).getAttribute('ovalue') != Dom.get('qty' + product_pid).value) {
        Dom.setStyle('list_button' + product_pid, 'visibility', 'visible')
    } else {
        Dom.setStyle('list_button' + product_pid, 'visibility', 'hidden')
    }

}



function order_from_list(code, order_key, page_key, page_section_type) {

    items = Dom.getElementsByClassName('product_item', 'tr', 'list_' + code)
    
    

    Dom.setStyle('waiting_' + code, 'display', '')
    Dom.setStyle('done_' + code, 'opacity', '1')

    var products_to_update = {};

    var i;

    for (i = 0; i < items.length; ++i) {
        counter = items[i].getAttribute('counter')

        if (Dom.get('qty_' + code + '_' + counter) != undefined && Dom.get('qty_' + code + '_' + counter).value != Dom.get('qty_' + code + '_' + counter).getAttribute('ovalue')) {

            qty = parseInt(Dom.get('qty_' + code + '_' + counter).value)
            if (isNaN(qty)) qty = 0




            product_id = Dom.get('product_' + code + '_' + counter).value

            products_to_update[product_id] = qty;






        }

    }

    transactions_data = YAHOO.lang.JSON.stringify(products_to_update)

    request = 'ar_basket.php?tipo=edit_multiple_order_transactios&transactions_data=' + transactions_data + '&order_key=' + order_key + '&page_key=' + page_key + '&page_section_type=' + page_section_type
    //alert(request)
    YAHOO.util.Connect.asyncRequest('GET', request, {
        success: function(o) {
            // alert(o.responseText)
            var r = YAHOO.lang.JSON.parse(o.responseText);



            if (r.state == 200) {
                Dom.get('basket_total').innerHTML = r.data.order_total
                Dom.get('number_items').innerHTML = r.data.ordered_products_number

                Dom.setStyle('waiting_' + code, 'display', 'none')
                Dom.setStyle('done_' + code, 'display', '')

                Dom.setStyle('list_order_button_submit_' + code, 'border-color', '#fff')


                var removeElement = function() {
                        var el = this.getEl();
                        Dom.setStyle(el, 'display', 'none')
                    }

                var myAnim = new YAHOO.util.Anim('done_' + code, {
                    opacity: {
                        from: 1,
                        to: 0
                    },


                }, 4, YAHOO.util.Easing.easeOut);
                myAnim.onComplete.subscribe(removeElement);
                myAnim.animate();


                for (i in r.updated_transactions) {
                    qty = r.updated_transactions[i]['qty'];
                    if (qty == 0) qty = ''
                    Dom.get('qty_' + code + '_' + i).value = qty
                    Dom.get('qty_' + code + '_' + i).setAttribute('ovalue', qty)
                }


            } else if (r.state == 201) {
                window.location.href = 'waiting_payment_confirmation.php?referral_key=' + Dom.get('page_key').value;

            } else {


            }



        },
        failure: function(o) {
            alert(o.statusText);
        },
        scope: this
    });







}


function over_ordernow_field(product_pid) {


    if (Dom.get('but_qty' + product_pid).value == '') Dom.get('but_qty' + product_pid).value = 1;

    Dom.get('order_button_' + product_pid).src = 'art/ordernow_hover_' + Dom.get('site_locale').value + '.png'

}

function out_ordernow_field(product_pid) {

    if (Dom.get('but_qty' + product_pid).getAttribute('ovalue') == '' && Dom.get('but_qty' + product_pid).value == 1) {
        Dom.get('but_qty' + product_pid).value = '';
        Dom.get('order_button_' + product_pid).src = 'art/ordernow_' + Dom.get('site_locale').value + '.png'
    }
}



function over_order_button(product_pid) {


    if (Dom.get('but_qty' + product_pid).getAttribute('ovalue') == '') {

        if (Dom.get('but_qty' + product_pid).value == '') Dom.get('but_qty' + product_pid).value = 1;

        Dom.get('order_button_' + product_pid).src = 'art/ordernow_hover_' + Dom.get('site_locale').value + '.png'


    } else {

        if (Dom.get('but_qty' + product_pid).getAttribute('ovalue') != Dom.get('but_qty' + product_pid).value) {
            Dom.get('order_button_' + product_pid).src = 'art/update_hover_' + Dom.get('site_locale').value + '.png'
        } else {
            Dom.get('order_button_' + product_pid).src = 'art/ordered_hover_' + Dom.get('site_locale').value + '.png'

        }

    }
}

function out_order_button(product_pid) {

    if (Dom.get('but_qty' + product_pid).getAttribute('ovalue') == '') {

        if (Dom.get('but_qty' + product_pid).value == 1) {
            Dom.get('but_qty' + product_pid).value = '';
            Dom.get('order_button_' + product_pid).src = 'art/ordernow_' + Dom.get('site_locale').value + '.png'
        }


    } else {

        if (Dom.get('but_qty' + product_pid).getAttribute('ovalue') != Dom.get('but_qty' + product_pid).value) {
            Dom.get('order_button_' + product_pid).src = 'art/update_' + Dom.get('site_locale').value + '.png'
        } else {
            Dom.get('order_button_' + product_pid).src = 'art/ordered_' + Dom.get('site_locale').value + '.png'

        }

    }
}



function order_product_from_button(product_pid, order_key, page_key, page_section_type) {



    //form_id='order_button_'+product_pid;
    if (Dom.get('but_qty' + product_pid).getAttribute('ovalue') == Dom.get('but_qty' + product_pid).value) return;


    var qty = Dom.get('but_qty' + product_pid).value

    if (isNaN(qty)) qty = 0

    if (qty <= 0 || qty == '') {
        qty = 0
    }

    // Dom.setStyle('but_button' + form_id, 'visibility', 'hidden')
    Dom.setStyle('waiting_' + product_pid, 'display', '')
    Dom.setStyle('done_' + product_pid, 'display', 'none')
    Dom.setStyle('done_' + product_pid, 'opacity', 1)


//console.log (button_queue_size)

//if(button_queue_size){

//setTimeout(order_product_from_button(product_pid, order_key, page_key, page_section_type) ,100)
//return;
//}

//button_queue_size++;



    request = 'ar_basket.php?tipo=edit_order_transaction&pid=' + product_pid + '&qty=' + qty + '&order_key=' + order_key + '&page_key=' + page_key + '&page_section_type=' + page_section_type
   
    YAHOO.util.Connect.asyncRequest('GET', request, {
        success: function(o) {
            // alert("caca")
            
           // button_queue_size--;
            
            var r = YAHOO.lang.JSON.parse(o.responseText);

            if (r.state == 200) {
                Dom.get('basket_total').innerHTML = r.data.order_total
                Dom.get('number_items').innerHTML = r.data.ordered_products_number

                Dom.setStyle('waiting_' + r.product_pid, 'display', 'none')
                Dom.setStyle('done_' + r.product_pid, 'display', '')
                Dom.setStyle('order_button_' + r.product_pid, 'border-color', '#fff')



                var removeElement = function() {
                        var el = this.getEl();
                        //  Dom.setStyle(el, 'display', 'none')
                    }

			  if (r.quantity == 0) r.quantity = '';
			  
			  if(r.quantity){
			   to_opacity=.6
			  }else{
			   to_opacity=0
			  }

                var myAnim = new YAHOO.util.Anim('done_' + r.product_pid, {
                    opacity: {
                        from: 1,
                        to: to_opacity
                    },


                }, 4, YAHOO.util.Easing.easeOut);
                myAnim.onComplete.subscribe(removeElement);
                myAnim.animate();

              


                Dom.get('but_qty' + r.product_pid).setAttribute('ovalue', r.quantity)
                Dom.get('but_qty' + r.product_pid).value = r.quantity

                if (r.quantity == '') {
                    Dom.get('order_button_' + product_pid).src = 'art/ordernow_' + Dom.get('site_locale').value + '.png'


                } else {
                    Dom.get('order_button_' + product_pid).src = 'art/ordered_hover_' + Dom.get('site_locale').value + '.png'


                }


                //order_product_from_list_changed(r.product_pid)
            } else if (r.state == 201) {
            
                window.location.href = 'waiting_payment_confirmation.php?referral_key=' + Dom.get('page_key').value;

            } else {


            }



        },
        failure: function(o) {
        alert("cx")
           // button_queue_size--;
        },
        scope: this
    });

}

function init_basket() {

}
Event.onDOMReady(init_basket);
