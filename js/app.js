/*Author: Raul Perusquia <raul@inikoo.com>
 Created: 27 August 2015 13:56:03 GMT+8 Singapoure.
 Copyright (c) 2015, Inikoo
 Version 3.0*/




function change_browser_history_state(request) {

    if (request.charAt(0) !== '/') {
        request = '/' + request
    }

    window.top.history.pushState({
        request: request
    }, '', request)

}

window.addEventListener('popstate', function(event) {


});

function change_tab(tab) {

    var request = "/ar_views.php?tipo=tab&tab=" + tab + "&state=" + JSON.stringify(state)

    $.getJSON(request, function(data) {

        if (typeof(data.tab) != "undefined" && data.tab !== null) {
            $('#tab').html(data.tab);
        }

    });


}

function change_view(_request) {

    var request = "/ar_views.php?tipo=views&request=" + _request + "&old_state=" + JSON.stringify(state)
    $.getJSON(request, function(data) {



        state = data.state;

        if (typeof(data.navigation) != "undefined" && data.navigation !== null && data.navigation != '') {
            $('#navigation').removeClass('hide')
            $('#navigation').html(data.navigation);
        } else {
            $('#navigation').addClass('hide')

        }

        if (typeof(data.tabs) != "undefined" && data.tabs !== null) {
            $('#tabs').html(data.tabs);
        }
        if (typeof(data.menu) != "undefined" && data.menu !== null) {
            $('#menu').html(data.menu);


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

$(document).ready(function() {



    state = {
        module: '',
        section: '',
        parent: '',
        parent_key: '',
        object: '',
        key: ''
    }
    structure = {}

    change_view($('#_request').val())




})


function show_results_per_page() {
    var $results_per_page = $('#results_per_page')
    if ($results_per_page.hasClass('showing_options')) {
        $results_per_page.removeClass('showing_options')
        $('.results_per_page').addClass('hide')
    } else {
        $results_per_page.addClass('showing_options')
        $('.results_per_page').removeClass('hide')

    }

}

function change_results_per_page(results_per_page) {
    $('.results_per_page').removeClass('selected')
    $('#results_per_page_' + results_per_page).addClass('selected')
    rows.setPageSize(results_per_page)

}
