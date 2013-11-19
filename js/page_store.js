var Event = YAHOO.util.Event;
var Dom = YAHOO.util.Dom;

function init() {


    //t=footer_region.height+header_region.height+content_region.height;
    //alert(Dom.getDocumentHeight()+' '+t);
    //return;



    if (Dom.get('update_heights').value == 1 || Dom.get('take_snapshot').value == 1) {
        update_heights();
    }



}



function take_snapshot() {

    YAHOO.util.Connect.asyncRequest('POST', 'ar_edit_sites.php?tipo=update_page_preview_snapshot&id=' + Dom.get('page_key').value, {
        success: function(o) {
            var r = YAHOO.lang.JSON.parse(o.responseText);
            if (parent.Dom.get('page_preview_snapshot_image') != null) {
                parent.Dom.get('page_preview_snapshot_image').src = 'image.php?id=' + r.image_key

            }


        }
    });



}

function update_heights() {



    content_region = Dom.getRegion('content');

    elements = Dom.getChildren('content')
    max_bottom = 0;
    for (var i = elements.length - 1; i >= 0; --i) {
        region = Dom.getRegion(elements[i])
        if (region && region.bottom > max_bottom) {
            max_bottom = region.bottom;
        }
    }
    elements = Dom.getElementsByClassName('ind_form', 'div', 'content')
    for (var i = elements.length - 1; i >= 0; --i) {
        region = Dom.getRegion(elements[i])
        if (region && region.bottom > max_bottom) {
            max_bottom = region.bottom + 40;
        }
    }
    elements = Dom.getElementsByClassName('product_list', 'table', 'content')
    for (var i = elements.length - 1; i >= 0; --i) {
        region = Dom.getRegion(elements[i])
        if (region && region.bottom > max_bottom) {
            max_bottom = region.bottom + 40;
        }
    }

    altura = max_bottom - content_region.top;




    Dom.setStyle('content', 'height', altura + 'px');


	if(Dom.getRegion('ft')  !=undefined)
    footer_height = Dom.getRegion('ft').height
    else
    footer_height=0;
    
if(Dom.getRegion('hd')  !=undefined)
    header_height = Dom.getRegion('hd').height
    else
    header_height=0;
if(Dom.getRegion('bd')  !=undefined)
    content_height = Dom.getRegion('bd').height
  else
    content_height=0;

	if(footer_height ==undefined){
	footer_height=0;
	}
	
	if(header_height == undefined){
	header_height=0;
	}

    //alert(Dom.get('page_key'))
    //alert('ar_edit_sites.php?tipo=update_page_height&id='+Dom.get('page_key').value+'&footer='+footer_height+'&header='+header_height+'&content='+content_height)
   
   
   request='ar_edit_sites.php?tipo=update_page_height&id=' + Dom.get('page_key').value + '&footer=' + footer_height + '&header=' + header_height + '&content=' + content_height
   alert(request)
   YAHOO.util.Connect.asyncRequest('POST', request, {
        success: function(o) {

            if (Dom.get('take_snapshot').value == 1) {
                take_snapshot();
            }

        }

    }

    );


}

Event.onDOMReady(init);
