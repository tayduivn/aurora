/*Author: Raul Perusquia <raul@inikoo.com>
 Created: 27 August 2015 13:56:03 GMT+8 Singapoure.
 Copyright (c) 2015, Inikoo
 Version 3.0*/

var key_scope = false;




function change_browser_history_state(request) {


    if (request == undefined) {
        return;
    }

    if (request.charAt(0) !== '/') {
        request = '/' + request
    }

    window.top.history.pushState({
        request: request
    }, '', request)

}

window.addEventListener('popstate', function(event) {
    change_view(event.state.request)

});

function change_tab(tab) {
    $('#maintabs .tab').removeClass('selected')
    $('#tab_' + tab.replace(/(:|\.|\[|\])/g, "\$1")).addClass('selected')
    change_view(state.request + '&tab=' + tab)
}

function change_subtab(subtab) {
    $('#maintabs .subtab').removeClass('selected')
    $('#subtab_' + subtab.replace(/(:|\.|\[|\])/g, "\$1")).addClass('selected')
    change_view(state.request + '&subtab=' + subtab)
}


function help() {

    change_view(state.request, {
        help: true
    })

}

function change_view(_request, metadata) {

    //console.log(metadata)

    if (metadata == undefined) {
        metadata = {};
    }

    var request = "/ar_views.php?tipo=views&request=" + _request + '&metadata=' + JSON.stringify(metadata) + "&old_state=" + JSON.stringify(state)
    $.getJSON(request, function(data) {

 console.log(data);

        state = data.state;

        //console.log(data.state)
        if (typeof(data.navigation) != "undefined" && data.navigation !== null && data.navigation != '') {
            // $('#navigation').removeClass('hide')
            $('#navigation').html(data.navigation);
        } else {
            // $('#navigation').addClass('hide')
        }

        if (typeof(data.tabs) != "undefined" && data.tabs !== null) {
            $('#tabs').html(data.tabs);
        }
         
        if (typeof(data.menu) != "undefined" && data.menu !== null) {
       
            $('#menu').html(data.menu);


        }

        if (typeof(data.logout_label) != "undefined" && data.logout_label !== null) {
            $('#logout_label').html(data.logout_label);


        }



        if (typeof(data.view_position) != "undefined" && data.view_position !== null) {

            $('#view_position').html(data.view_position);
        }

        if (typeof(data.object_showcase) != "undefined" && data.object_showcase !== null && data.object_showcase != '') {
            $('#object_showcase').removeClass('hide')
            $('#object_showcase').html(data.object_showcase);
        } else {
            $('#object_showcase').addClass('hide')

        }
        if (typeof(data.tab) != "undefined" && data.tab !== null) {

            $('#tab').html(data.tab);
        }




        if (typeof(data.structure) != "undefined" && data.structure !== null) {
            structure = data.structure
        }



        change_browser_history_state(data.state.request)


    });

}




function logout() {
    window.location.href = "/logout.php";
}

function decodeEntities(a) {
    return a
}
/*
var decodeEntities = (function() {


  var element = document.createElement('div');

  function decodeHTMLEntities (str) {
    if(str && typeof str === 'string') {
      str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
      str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
      element.innerHTML = str;
      str = element.textContent;
      element.textContent = '';
    }

    return str;
  }

  return decodeHTMLEntities;
})();
*/

function htmlEncode(value) {
    return $('<div/>').text(value).html();
}

var isAdvancedUpload = function() {
        var div = document.createElement('div');
        return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
    }();
