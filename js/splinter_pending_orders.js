var Dom   = YAHOO.util.Dom;


function get_pending_orders_data(parent,parent_key) {
    var ar_file = 'ar_orders.php';
    var request = 'tipo=get_pending_orders_data&parent=' + Dom.get('parent').value + '&parent_key=' + Dom.get('parent_key').value 
//   alert(ar_file+'?'+request)
   YAHOO.util.Connect.asyncRequest('POST', ar_file, {
        success: function(o) {

        //alert(o.responseText)
            var r = YAHOO.lang.JSON.parse(o.responseText);
            if (r.state == 200) {
                 for (x in r.data) {

                        if (Dom.get(x) != undefined) {
                            Dom.get(x).innerHTML = r.data[x];
                        }
                    }
            }
        },
        failure: function(o) {
           // alert(o.statusText);
        },
        scope: this
    }, request

    );
}






function init() {
 get_pending_orders_data(parent,parent_key)

}

YAHOO.util.Event.onDOMReady(init);