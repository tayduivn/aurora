/*Author: Raul Perusquia <raul@inikoo.com>
 Created: 6 November 2015 at 18:17:38 GMT, Sheffield UK
 Copyright (c) 2015, Inikoo
 Version 3.0*/


function show_filter() {
    $('#show_filter').addClass('hide')
    $('.filter').removeClass('hide')
    $('#filter input').focus()

}

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

    $('#results_per_page').attr('title', $('#results_per_page').attr('title').replace(/\(.*\)/g, '(' + results_per_page + ')'))


}


function change_period(period) {

    var parameters = jQuery.parseJSON(rows.parameters);

    if (period == 'date') {

        parameters.from = $('#select_date').val()
        parameters.to = $('#select_date').val()


    } else if (period == 'interval') {

        var components = $('#select_interval_from').val().split(/\//)
        parameters.from = components[2] + '-' + components[0] + '-' + components[1]
        components = $('#select_interval_to').val().split(/\//)
        parameters.to = components[2] + '-' + components[0] + '-' + components[1]


    } else {

    }

    $('#date_chooser div').removeClass('selected')
    $('#' + period).addClass('selected')

    $('#select_date_control_panel').addClass('hide')
    $('#select_interval_control_panel').addClass('hide')


    parameters.period = period;

    rows.parameters = JSON.stringify(parameters)

    rows.url = '/' + rows.ar_file + '?tipo=' + rows.tipo + '&parameters=' + rows.parameters
    rows.fetch({
        reset: true
    });
    if (with_elements) get_elements_numbers(rows.tab, rows.parameters)

}


function get_elements_numbers(tab, parameters) {
    var request = "/ar_elements.php?tab=" + tab + "&parameters=" + parameters
    $.getJSON(request, function (data) {
        if (data.state == 200) {
            for (element in data.elements_numbers) {

                for (item in data.elements_numbers[element]) {
                   //  console.log(item)
                    $("#element_qty_" + item).html('(' + data.elements_numbers[element][item] + ')')


                }
            }
        }
    })

}


function close_columns_period_options() {
    var icon = $('#columns_period .fa')

    icon.addClass('fa-bars')
    icon.removeClass('fa-chevron-up')
    $('#columns_period_chooser').addClass('hide')

}

function close_columns_frequency_options() {
    var icon = $('#columns_frequency .fa')

    icon.addClass('fa-bars')
    icon.removeClass('fa-chevron-up')
    $('#columns_frequency_chooser').addClass('hide')

}


function show_columns_period_options() {

    var button = $('#columns_period')
    var icon = $('#columns_period .fa')

    if (!icon.hasClass('fa-bars')) {

        close_columns_period_options()
    } else {
        icon.removeClass('fa-bars')
        icon.addClass('fa-chevron-up')

        var offset = $('#columns_period .fa').position();
        var height = button.height();

        //  console.log(icon.width())
        $('#columns_period_chooser').removeClass('hide').offset({
            top: $('#columns_period').position().top + height, left: offset.left - $('#columns_period_chooser').width() + icon.width() + 20
        })


    }

}

function show_columns_frequency_options() {

    var button = $('#columns_frequency')
    var icon = $('#columns_frequency .fa')
    if (!icon.hasClass('fa-bars')) {

        close_columns_frequency_options()
    } else {
        icon.removeClass('fa-bars')
        icon.addClass('fa-chevron-up')

        var offset = $('#columns_frequency .fa').position();
        var height = button.height();

        console.log($('#columns_frequency').position().top + height)
        console.log(offset.left - $('#columns_frequency_chooser').width() + icon.width() + 20)

        $('#columns_frequency_chooser').removeClass('hide').offset({
            top: $('#columns_frequency').position().top + height, left: offset.left - $('#columns_frequency_chooser').width() + icon.width() + 20
        })


    }

}

function show_f_options(element) {

    if (!$('#filter_field').hasClass('button')) return

    if ($('#f_options_chooser').hasClass('hide')) {

        var offset = $('#filter_field').position();
        var height = $('#filter_field').height();

        $('#f_options_chooser').removeClass('hide').offset({
            top: offset.top + height, left: offset.left - 27.2
        })

    } else {
        $('#f_options_chooser').addClass('hide')
    }


}

function change_f_option(option) {

    if (!$(option).hasClass('selected')) {
        $('#f_options_chooser div').removeClass('selected')
        $('#f_options_chooser div i').removeClass('fa-circle').addClass('fa-circle')
        $(option).addClass('selected')
        $(option).find('i').addClass('fa-circle')
        $('#filter').attr('f_field', $(option).attr('f_field')).find('input').val('')


        $('#filter_field .label').html($(option).find('.label').html())

        rows.fetch({
            reset: true
        });
        show_f_options()

    }
}


function show_elements_types() {

    var button = $('#element_type')
    var icon = $('#element_type .fa')
    if (icon.hasClass('fa-bars')) {
        $('#f_options_chooser').addClass('hide')
        icon.removeClass('fa-bars')
        icon.addClass('fa-chevron-up')

        var offset = button.position();
        var height = button.height();

        $('#elements_chooser').removeClass('hide').offset({
            top: offset.top + height, left: offset.left
        })


    } else {
        icon.addClass('fa-bars')
        icon.removeClass('fa-chevron-up')
        $('#elements_chooser').addClass('hide')

    }


}

function change_columns_frequency(frequency, frequency_label) {

    $('#columns_frequency span.label').html(frequency_label)


    var parameters = jQuery.parseJSON(rows.parameters);
    parameters.frequency = frequency;

    rows.parameters = JSON.stringify(parameters)

    rows.url = '/' + rows.ar_file + '?tipo=' + rows.tipo + '&parameters=' + rows.parameters
    rows.fetch({
        reset: true
    });

    close_columns_frequency_options()
    $('#columns_frequency_chooser div').removeClass('selected')
    $('#columns_frequency_chooser .fa').removeClass('fa-circle').addClass('fa-circle')
    $('#element_group_option_' + frequency).addClass('selected')
    $('#element_group_option_' + frequency + ' .fa').removeClass('fa-circle').addClass('fa-circle')

}

function change_columns_period(period, period_label) {

    $('#columns_period span.label').html(period_label)


    var parameters = jQuery.parseJSON(rows.parameters);
    parameters.f_period = period;

    rows.parameters = JSON.stringify(parameters)

    rows.url = '/' + rows.ar_file + '?tipo=' + rows.tipo + '&parameters=' + rows.parameters
    rows.fetch({
        reset: true
    });

    close_columns_period_options()
    $('#columns_period_chooser div').removeClass('selected')
    $('#columns_period_chooser .fa').removeClass('fa-circle').addClass('fa-circle')
    $('#element_group_option_' + period).addClass('selected')
    $('#element_group_option_' + period + ' .fa').removeClass('fa-circle').addClass('fa-circle')

}

function change_elements_type(elements_type) {

    $('#elements .elements_group').addClass('hide')

    $("#elements_group_" + elements_type).removeClass('hide')


    $('#elements_chooser  div').removeClass('selected')

    $('#element_group_option_' + elements_type).addClass('selected')


    $('#elements_chooser  i').removeClass('fa').addClass('far')

    $('#element_group_option_' + elements_type + ' i').addClass('fa')
    $('#element_group_option_' + elements_type + ' i').removeClass('far')


    var icon = $('#element_type .fa')

    icon.addClass('fa-bars')
    icon.removeClass('fa-chevron-up')
    $('#elements_chooser').addClass('hide')


    var parameters = jQuery.parseJSON(rows.parameters);
    parameters.elements_type = elements_type;

    rows.parameters = JSON.stringify(parameters)

    rows.url = '/' + rows.ar_file + '?tipo=' + rows.tipo + '&parameters=' + rows.parameters
    rows.fetch({
        reset: true
    });


}

var element_delay = 300, element_clicks = 0, element_timer = null;


$(document).on("click",'#elements .element', function(e){

    element_clicks++;  //count clicks

    var item= $(this).data('item')

    if(element_clicks === 1) {

        element_timer = setTimeout(function() {
            change_table_element('click',item)
            element_clicks = 0;             //after action performed, reset counter

        }, element_delay);

    } else {


        clearTimeout(element_timer);    //prevent single-click action
        change_table_element('double_click', item)
        element_clicks = 0;             //after action performed, reset counter
    }

})
    .on("dblclick", function(e){
        e.preventDefault();  //cancel system double-click event
    });


function change_table_element(event, item) {


console.log(event)

    if (event=='double_click') {


        $('#elements i.element_checkbox').removeClass('fa-check-square').addClass('fa-square')
        $('#elements .element').removeClass('selected')

        $("#element_" + item).addClass('selected')
        $("#element_checkbox_" + item).removeClass('fa-square')
        $("#element_checkbox_" + item).addClass('fa-check-square')

    } else {



        if ($("#element_" + item).hasClass('selected')) {
            $("#element_" + item).removeClass('selected')
            $("#element_checkbox_" + item).removeClass('fa-check-square')
            $("#element_checkbox_" + item).addClass('fa-square')
        } else {

            $("#element_" + item).addClass('selected')

            $("#element_checkbox_" + item).removeClass('fa-square')
            $("#element_checkbox_" + item).addClass('fa-check-square')
        }
    }


    var parameters = jQuery.parseJSON(rows.parameters);
    $("#elements_group_" + parameters.elements_type + " .element").each(function (index) {
        //console.log($(this).attr('item_key') + ": " + $(this).hasClass('selected'));
        parameters['elements'][parameters.elements_type]['items'][$(this).attr('item_key')]['selected'] = $(this).hasClass('selected')

        // alert(parameters['elements'][rows.parameters.elements_type])
    });


    rows.parameters = JSON.stringify(parameters)

    rows.url = '/' + rows.ar_file + '?tipo=' + rows.tipo + '&parameters=' + rows.parameters

    rows.fetch({
        reset: true
    });

}


function show_export_dialog_left_button() {
    if ($('#export_dialog').hasClass('hide')) {
        $('#export_dialog').removeClass('hide')

       // console.log($('#show_export_dialog').position().left)
        $("#export_dialog").css('left', 0);
        $("#export_dialog").css('top', $("#show_export_dialog").height());
    } else {
        hide_export_dialog()
    }

}

function show_export_dialog() {
    if ($('#export_dialog').hasClass('hide')) {
        $('#export_dialog').removeClass('hide')
        $("#export_dialog").css('left', -1 * $("#export_dialog").width());
        $("#export_dialog").css('top', $("#show_export_dialog").height());
    } else {
        hide_export_dialog()
    }

}

function show_download_edit_items_dialog() {
    if ($('#download_edit_items_dialog').hasClass('hide')) {



        $('#download_edit_items_dialog').removeClass('hide')
        $('#download_edit_items_dialog_config').removeClass('hide')




        $("#download_edit_items_dialog").offset({ top:  $('#edit_table_dialog').offset().top,
            left:  $('#edit_table_dialog').offset().left + $('#edit_table_dialog').width()-$("#download_edit_items_dialog").width()  })

        $("#download_edit_items_dialog_config").offset({ top:  $('#edit_table_dialog').offset().top, left:  $('#download_edit_items_dialog').offset().left -$("#download_edit_items_dialog_config").width() -30    })
        $('#edit_table_dialog').addClass('hide')


    } else {

    }

}





function show_edit_table_dialog() {
//

    if ($('#edit_table_dialog').hasClass('hide')) {
        $('#edit_table_dialog').removeClass('hide')
        $("#edit_table_dialog").offset({ top:  $('#show_edit_table_dialog_button').offset().top+$('#show_edit_table_dialog_button').height()-3, left:  $('#show_edit_table_dialog_button').offset().left-$('#edit_table_dialog').width()+20 })


    } else {
        $('#edit_table_dialog').addClass('hide')
    }
}


function table_edit_view(){
    $('#table .table_item_editable').attr('contenteditable',true).addClass('editing')
   $('#inline_edit_table_items_buttons').removeClass('hide')
    $('#show_edit_table_dialog_button').addClass('hide')

    $('.table_item_editable.editing').each(function(i, obj) {

        $(obj).attr('ovalue',$(obj).html())

    });



    show_edit_table_dialog()


}







function hide_export_dialog() {
    $('#export_dialog').addClass('hide')
    hide_export_config_dialog()


    $('.export_download').addClass('hide').attr('title', '').click(function () {
    })
    $('.export_progress_bar_bg').addClass('hide').html('')
    $('.export_progress_bar').css('width', '0px').removeClass('hide').attr('title', '').html('')


}

function open_export_config() {
    if ($('#export_dialog_config').hasClass('hide')) {
        $('#export_dialog_config').removeClass('hide')
        $("#export_dialog_config").css('left', -1 * ($("#export_dialog_config").width() + 40 + $("#export_dialog").width()));


        $("#export_dialog_config").css('top', $("#show_export_dialog").height());
    } else {
        hide_export_config_dialog()
    }
}

function open_export_config_left_button() {
    if ($('#export_dialog_config').hasClass('hide')) {
        $('#export_dialog_config').removeClass('hide')
        $("#export_dialog_config").css('left', 20 + $("#export_dialog").width());
        $("#export_dialog_config").css('top', $("#show_export_dialog").height());
    } else {
        hide_export_config_dialog()
    }
}

function hide_export_config_dialog() {
    $('#export_dialog_config').addClass('hide')
}


function toggle_all_export_fields(element) {
    if ($(element).hasClass('fa-check-square')) {
        $(element).removeClass('fa-check-square')
        $(element).addClass('fa-square')

        $('#export_fields i').removeClass('fa-check-square')
        $('#export_fields i').addClass('fa-square')


    } else {
        $(element).addClass('fa-check-square')
        $(element).removeClass('fa-square')
        $('#export_fields i').addClass('fa-check-square')
        $('#export_fields i').removeClass('fa-square')
    }


}

function toggle_export_field(element) {




    field_element=$(element)

    if (field_element.hasClass('fa-check-square')) {


       field_element.removeClass('fa-check-square')
        field_element.addClass('fa-square')

    } else {
        console.log('unckeced')


        field_element.addClass('fa-check-square')
        field_element.removeClass('fa-square')
    }
}

function export_table(type) {

    $('#export_progress_bar_bg_' + type+'_export').removeClass('hide').html('&nbsp;' + $('#export_queued_msg').html())

    $('#export_table_excel').removeClass('link').addClass('disabled')
    $('#export_table_csv').removeClass('link').addClass('disabled')
    $('.field_export').removeClass('button').addClass('disabled')
    $('#stop_export_table_' + type+'_export').removeClass('hide')
    $('#stop_export_table_' + type+'_export').attr('stop', 0);

    var fields = []
    $('#export_dialog_config .field_export i').each(function (index, obj) {
        if ($(obj).hasClass('fa-check-square')) fields.push($(obj).attr('key'))
    });

    var request = "/ar_export.php?ar_file=" + rows.ar_file + "&tipo=" + rows.tipo + "&parameters=" + rows.parameters + '&type=' + type + '&state=' + JSON.stringify(state) + '&fields=' + JSON.stringify(fields)

     console.log(request)
    $.getJSON(request, function (data) {
        if (data.state == 200) {
            get_export_process_bar(data.fork_key, data.tipo, type,'_export');
        }
    })

}


function stop_export(type) {
    $('#stop_export_table_' + type+'_export').attr('stop', 1);
}

function get_export_process_bar(fork_key, tag, type,suffix) {

   // console.log(type+' xx '+suffix)
    
    
    request = '/ar_fork.php?tipo=get_process_bar&fork_key=' + fork_key + '&tag=' + tag
    $.getJSON(request, function (data) {
        if (data.state == 200) {


            if ($('#stop_export_table_' + type+suffix).attr('stop') == 1) {

                $('.export_download').addClass('hide').attr('title', '').click(function () {
                })
                $('.export_progress_bar_bg').addClass('hide').html('')
                $('.export_progress_bar').css('width', '0px').removeClass('hide').attr('title', '').html('')
                $('#export_table_excel').addClass('link').removeClass('disabled')
                $('#export_table_csv').addClass('link').removeClass('disabled')
                $('.field_export').addClass('button').removeClass('disabled')
                $('#stop_export_table_' + type).addClass('hide')
                return;

            }

            if (data.fork_state == 'Queued') {
                setTimeout(function () {
                    get_export_process_bar(data.fork_key, data.tag, type,suffix)
                }, 100);


            } else if (data.fork_state == 'In Process') {

                $('#export_download_' + type+suffix).addClass('hide')

                $('#export_progress_bar_bg_' + type+suffix).removeClass('hide').html('&nbsp;' + data.forks_info)
                $('#export_progress_bar_' + type+suffix).css('width', data.percentage).removeClass('hide').attr('title', data.progress).html('&nbsp;' + data.forks_info);
                setTimeout(function () {
                    get_export_process_bar(data.fork_key, data.tag, type,suffix)
                }, 250);

            } else if (data.fork_state == 'Finished') {

                $('#download_' + type+suffix).attr('href', '/download.php?file=' + data.result)
                $('#export_download_' + type+suffix).removeClass('hide').attr('title', data.result_info).click(function () {

                    download_exported_file(type,suffix)

                });
                $('#export_progress_bar_bg_' + type+suffix).addClass('hide').html('')
                $('#export_progress_bar_' + type+suffix).css('width', '0px').removeClass('hide').attr('title', '').html('')


                $('#export_table_excel').addClass('link').removeClass('disabled')
                $('#export_table_csv').addClass('link').removeClass('disabled')
                $('.field_export').addClass('button').removeClass('disabled')
                $('#stop_export_table_' + type+suffix).addClass('hide')


                console.log('cacacaca')

            }


        }
    })


}

function download_exported_file(type,suffix) {


    if(suffix=='_export'){
        $("#download_" + type+suffix)[0].click();
        $('#table_edit_items_file_upload2_button').removeClass('disabled')



    }else{
        $("#download_" + type)[0].click();
        $('#upload_icon').removeClass('very_discreet').addClass('valid_save')
        setTimeout(
            function () {
                $('#upload_icon').addClass('valid_save')
                hide_export_dialog()
            }
            , 1000)
    }


}


function edit_category_subject(element) {

    if ($(element).hasClass('wait')) return;

    if ($(element).hasClass('fa-unlink')) {
        $(element).removeClass('fa-unlink very_discreet').addClass('fa-link wait')
        operation = 'link';
    } else {
        $(element).addClass('fa-unlink very_discreet wait').removeClass('fa-link')
        operation = 'unlink';
    }

    var request = '/ar_edit.php?tipo=edit_category_subject&category_key=' + state.key + '&subject_key=' + $(element).attr('key') + '&operation=' + operation

    $.getJSON(request, function (data) {

        $(element).removeClass('wait')

        if (data.state == 200) {


        } else if (data.state == 400) {
            if (operation == 'link') {
                $(element).addClass('fa-unlink very_discreet wait').removeClass('fa-link')
            } else {
                $(element).removeClass('fa-unlink very_discreet').addClass('fa-link wait')

            }

        }


    })


}

function post_table_rendered(otable) {


    switch (state.tab){
        case 'location.parts':
            $('.table_edit_cell').awesomeCursor('pencil', {
                color: 'rgba(0, 0, 0, 0.5)',
                hotspot: 'bottom left'
            })
            break;

    }

}




function get_editable_data(element,type) {

    console.log($(element).data('data'))

    _data=$(element).data('data')

    $('#export_progress_bar_bg_' + type).removeClass('hide').html('&nbsp;' + $('#export_queued_msg').html())

    $('#export_table_excel').removeClass('link').addClass('disabled')
    $('#export_table_csv').removeClass('link').addClass('disabled')
    $('.field_export').removeClass('button').addClass('disabled')
    $('#stop_export_table_' + type).removeClass('hide')
    $('#stop_export_table_' + type).attr('stop', 0);

    var fields = []
    $('#download_edit_items_dialog_config .field_export i.object_field').each(function (index, obj) {
        if ($(obj).hasClass('fa-check-square')) fields.push($(obj).attr('key'))
    });

    var request = "/ar_export_edit_template.php?parent="+_data.parent+"&parent_key="+_data.parent_key+"&parent_code="+_data.parent_code+"&objects="+_data.object+"&fields=" + JSON.stringify(fields) + '&type=' + type + '&metadata=' + JSON.stringify({})

    console.log(request)

    $.getJSON(request, function (data) {
        if (data.state == 200) {
            get_export_process_bar(data.fork_key, data.tipo, type,'');
        }
    })

}