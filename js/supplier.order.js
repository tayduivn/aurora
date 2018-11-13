/*Author: Raul Perusquia <raul@inikoo.com>
 Created:14 July 2016 at 13:43:34 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo
 Version 3.0*/


function show_create_delivery() {


    if ($('#new_delivery').hasClass('hide')) {


        if (state.tab == 'supplier.order.items') {

            grid.columns.findWhere({
                name: 'operations'
            }).set("renderable", true)

            /*
            grid.columns.findWhere({
                name: 'checkbox'
            }).set("renderable", true)
*/


            grid.columns.findWhere({
                name: 'state'
            }).set("renderable", false)

            grid.columns.findWhere({
                name: 'quantity_units'
            }).set("renderable", true)


            grid.columns.findWhere({
                name: 'quantity_skos'
            }).set("renderable", true)

            grid.columns.findWhere({
                name: 'quantity_cartons'
            }).set("renderable", true)


        } else {

            change_tab('supplier.order.items', {
                'create_delivery': 1
            })
        }

        $('.submitted_items_qty').addClass('button')


        $('#new_delivery').removeClass('hide')


        $('#delivery_number').val('').focus()

    } else {
        close_create_delivery()
    }

}

function close_create_delivery() {
    $('#tabs').removeClass('hide')
    $('#new_delivery').addClass('hide')

    $('.submitted_items_qty').removeClass('button')

    grid.columns.findWhere({
        name: 'checkbox'
    }).set("renderable", false)

    grid.columns.findWhere({
        name: 'operations'
    }).set("renderable", false)

    grid.columns.findWhere({
        name: 'delivery_quantity'
    }).set("renderable", false)
}

function save_delivery_qty_change(element) {
  //  console.log('x')


}


function change_on_delivery(element) {

    if ($(element).hasClass('fa-square')) {
        $(element).removeClass('fa-square').addClass('fa-check-square')
        $('.delivery_quantity_' + $(element).attr('key')).css({
            'visibility': 'visible'
        }).addClass('on')


    } else {
        $(element).addClass('fa-square').removeClass('fa-check-square')

        console.log($(element).attr('key'))

        $('.delivery_quantity_' + $(element).attr('key')).css({
            'visibility': 'hidden'
        }).removeClass('on')

        $(element).closest('tr').find('.submitted_items_qty').trigger( "click" )


    }
    validate_new_supplier_delivery()

}

function quick_create_delivery() {

    var object_data = $('#object_showcase div.order').data("object")
    $('#delivery_number').val(object_data.purchase_order_number)
    $('#quick_create_delivery_operation').addClass('valid')
    save_create_delivery('#quick_create_delivery_operation')
}


function save_create_delivery(element) {


    if (!$(element).hasClass('valid') || $(element).hasClass('wait')) {
        return;
    }

    $(element).addClass('wait')
    $(element).find('i').addClass('fa-spinner fa-spin fa-cloud').removeClass('fa-plus');
    var object_data = $('#object_showcase div.order').data("object")
    var fields_data = {};

    fields_data['Supplier Delivery Public ID'] = $('#delivery_number').val()
    fields_data['items'] = {}
    fields_data['pending_items'] = {}
    $('.order_units_qty').each(function () {



        if ($(this).closest('span').hasClass('on') == 1) {
            fields_data['items'][$(this).closest('span').data('settings').key] = $(this).val()
        }else{
            fields_data['pending_items'][$(this).closest('span').data('settings').key] = $(this).val()

        }
    });

    var request = '/ar_edit.php?tipo=new_object&object=SupplierDelivery&parent=' + object_data.object + '&parent_key=' + object_data.key + '&fields_data=' + JSON.stringify(fields_data)
    console.log(request)
    //return;

    //=====
    var form_data = new FormData();

    form_data.append("tipo", 'new_object')
    form_data.append("parent", object_data.object)
    form_data.append("parent_key", object_data.key)
    form_data.append("object", 'SupplierDelivery')
    form_data.append("fields_data", JSON.stringify(fields_data))

    var request = $.ajax({

        url: "/ar_edit.php",
        data: form_data,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: 'json'

    })


    request.done(function (data) {


        if (data.state == 200) {
            change_view(object_data.order_parent.toLowerCase() + '/' + object_data.order_parent_key + '/delivery/' + data.new_id, {
                tab: 'supplier.delivery.items'
            })
        } else if (data.state == 400) {
            $(element).removeClass('wait')
            $(element).find('i').removeClass('fa-spinner fa-spin fa-cloud').addClass('fa-plus');


            console.log(data)
        }

    })


    request.fail(function (jqXHR, textStatus) {
        console.log(textStatus)
        console.log(jqXHR.responseText)

    });


}




$(document).on('input propertychange', '.new_delivery_field', function (evt) {




    var field_id = $(this).attr('id')

    if (field_id == 'delivery_number') {

        var value = $(this).val()


        if(value==''){
            validate_new_supplier_delivery();
            return;
        }


        var object_data = $('#object_showcase div.order').data("object")
       // console.log(object_data)

        var parent = object_data.order_parent
        var parent_key = object_data.order_parent_key
        var object = 'Supplier Delivery'
        var key = ''
        var field = 'Supplier Delivery Public ID'



            //$(this).closest('table').find('td.buttons').addClass('changed')
            var request = '/ar_validation.php?tipo=check_for_duplicates&parent=' + parent + '&parent_key=' + parent_key + '&object=' + object + '&key=' + key + '&field=' + field + '&value=' + value + '&metadata=' + JSON.stringify({
                option: 'creating_dn_from_po'
            })

            console.log(request)

            $.getJSON(request, function (data) {



                console.log(data)

               // $('#' + field_id).removeClass('waiting invalid valid')
               // $('#' + field_id).closest('table').find('td.buttons').removeClass('waiting invalid valid')

                //$('#' + field_id).closest('table').find('td.buttons i').removeClass('fa-spinner fa-spin').addClass('fa-cloud')


                if (data.state == 200) {

                    if(data.validation!='valid'){
                        $('#delivery_number').addClass('error')
                    }else{
                        $('#delivery_number').removeClass('error')

                    }

                    //var validation = data.validation
                   // var msg = data.msg

                } else {
                    //var validation = 'invalid'
                    //var msg = "Error, can't verify value on server"
                    $('#delivery_number').addClass('error')

                }
                //$('#' + field_id).closest('table').find('td.buttons').addClass(validation)


                validate_new_supplier_delivery();

            })



    }

});

function validate_new_supplier_delivery(){

    //$('#new_delivery').find('td.buttons i').addClass('fa-spinner fa-spin').removeClass('fa-cloud')

    var with_error=false;

    if($('#delivery_number').val()==''){
        var changed=false;
    }else{
        var changed=true;

    }

    if($('#delivery_number').hasClass('error')){
         with_error=true;
    }



    $('.order_units_qty').each(function(i, obj) {
        if($(obj).hasClass('error')){
            with_error=true;
            return false;
        }


    });

    with_items=false
    $('.delivery_quantity_item_container').each(function(i, obj) {

        if($(obj).hasClass('on')){
            with_items=true;
            return false;
        }


    });

    console.log(with_error)

    var save_buttons=$('#new_delivery').find('td.buttons')

    if(with_error){
        save_buttons.addClass('invalid changed').removeClass('valid')
    }else{
        save_buttons.removeClass('invalid')

        if(changed && with_items){
            save_buttons.addClass('changed valid')
        }else{
            save_buttons.removeClass('changed valid')

        }

    }








}



$(document).on('input propertychange', '.create_delivery_item_qty', function (evt) {


    if (!validate_signed_integer($(this).val(), 4294967295) || $(this).val() == '') {

        $(this).closest('tr').find('i.plus').removeClass('fa-check-circle exclamation-circle error').addClass('fa-plus')
        $(this).closest('tr').find('i.minus').removeClass('invisible')
        $(this).closest('tr').find('input.create_delivery_item_qty').removeClass('error')




        create_delivery_change_quantity($(this).val(),$(this))

    }else{

        $(this).closest('tr').find('.create_delivery_item_qty').val($(this).val())

        $(this).closest('tr').find('i.plus').removeClass('fa-plus fa-check-circle').addClass('fa-exclamation-circle error')
        $(this).closest('tr').find('i.minus').addClass('invisible')
        $(this).closest('tr').find('input.create_delivery_item_qty').addClass('error')
        validate_new_supplier_delivery();
    }







});

function create_delivery_item_icon_clicked(element) {


    if($(element).hasClass('fa-exclamation-circle')){

        return;
    }


    $(element).addClass('fa-spinner fa-spin')

    var input = $(element).closest('span').find('input')
    // var icon = $(element)

    if ($(element).hasClass('fa-plus')) {


        var _icon = 'fa-plus'

        if (isNaN(input.val()) || input.val() == '') {
            var qty = 1
        } else {
            qty = parseFloat(input.val()) + 1
        }

        input.val(qty).addClass('discreet')

    } else if ($(element).hasClass('fa-minus')) {

        if (isNaN(input.val()) || input.val() == '' || input.val() == 0) {
            var qty = 0
        } else {
            qty = parseFloat(input.val()) - 1
        }

        input.val(qty).addClass('discreet')

        var _icon = 'fa-minus'

    } else {
        qty = parseFloat(input.val())

        var _icon = 'fa-check-circle'

    }

    //console.log(_icon)

    $(element).addClass(_icon)

    if (qty == '') qty = 0;


    create_delivery_change_quantity(qty,input)



}

function use_submitted_qty_in_delivery(element,qty){


    if (!validate_signed_integer(qty, 4294967295) ) {

        $(element).closest('tr').find('i.plus').removeClass('fa-check-circle exclamation-circle error').addClass('fa-plus')
        $(element).closest('tr').find('i.minus').removeClass('invisible')
        $(element).closest('tr').find('input.create_delivery_item_qty').removeClass('error')





        $(element).closest('tr').find('input.order_units_qty').val(qty)


        create_delivery_change_quantity(qty,$(element).closest('tr').find('input.order_units_qty'))

    }




}


function create_delivery_change_quantity(qty,element) {




    var settings = $(element).closest('span').data('settings')





    var tr=$(element).closest('tr')
    switch (settings.type) {
        case 'Cartons':

            units_qty=settings.unit_factor*qty
            skos_qty=settings.sko_factor*qty
            cartons_qty=qty

            tr.find('input.order_units_qty').val(units_qty)
            tr.find('input.order_skos_qty').val(skos_qty)

            break;
        case 'SKOs':

            units_qty=settings.unit_factor*qty
            skos_qty=qty
            cartons_qty=qty/settings.carton_factor

            tr.find('input.order_units_qty').val(units_qty)
            tr.find('input.order_cartons_qty').val(cartons_qty)

            break;
        case 'Units':


            console.log(settings.carton_factor)


            units_qty=qty
            skos_qty=qty/settings.sko_factor
            cartons_qty=qty/settings.carton_factor





            tr.find('input.order_skos_qty').val(skos_qty)
            tr.find('input.order_cartons_qty').val(cartons_qty)

            break;

    }

    if(units_qty%skos_qty || units_qty%cartons_qty){
        tr.find('.create_delivery_item_qty').addClass('error')
    }else{
        tr.find('.create_delivery_item_qty').removeClass('error')

    }

    $(element).removeClass('discreet')

    $(element).closest('span').find('i.plus').removeClass('fa-check-circle exclamation-circle error').addClass('fa-plus').removeClass('fa-spinner fa-spin')
    $(element).closest('span').find('i.minus').removeClass('invisible').removeClass('fa-spinner fa-spin')

    validate_new_supplier_delivery();

}


function set_po_transaction_amount_to_current_cost(element,type,transaction_key){


    $(element).addClass('fa-spin fa-spinner')

    var form_data = new FormData();

    form_data.append("tipo", 'set_po_transaction_amount_to_current_cost')
    form_data.append("transaction_key", transaction_key)
    form_data.append("type", type)




    var request = $.ajax({

        url: "/ar_edit_orders.php",
        data: form_data,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: 'json'

    })


    request.done(function (data) {


        if (data.state == 200) {

            $('.po_amount_'+transaction_key).html(data.amount)

            for (var key in data.update_metadata.class_html) {
                $('.' + key).html(data.update_metadata.class_html[key])
            }


        } else if (data.state == 400) {


            console.log(data)
        }

    })


    request.fail(function (jqXHR, textStatus) {
        console.log(textStatus)
        console.log(jqXHR.responseText)

    });



}


