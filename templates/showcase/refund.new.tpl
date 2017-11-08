{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 5 November 2017 at 12:44:12 GMT+8, Sanur, Bali, Indonesia
 Copyright (c) 2017, Inikoo

 Version 3
-->
*}

{assign deliveries $order->get_deliveries('objects')}
{assign invoices $order->get_invoices('objects')}
{assign payments $order->get_payments('objects','Completed')}

<div id="order" class="order" style="display: flex;" data-object="{$object_data}" order_key="{$order->id}">
    <div class="block" style=" align-items: stretch;flex: 1">
        <div class="data_container" style="padding:5px 10px">
            <div class="data_field  " style="margin-bottom:10px">

              <span class="button" onclick="change_view('customers/{$order->get('Order Store Key')}/{$order->get('Order Customer Key')}')">
                <i class="fa fa-user fa-fw" aria-hidden="true" title="{t}Customer{/t}"></i> <span

                          class="button Order_Customer_Name">{$order->get('Order Customer Name')}</span> <span

                          class="link Order_Customer_Key">{$order->get('Order Customer Key')|string_format:"%05d"}</span>
              </span>
            </div>

            <div class="data_field small {if $order->get('Telephone')==''}hide{/if}  " style="margin-top:5px">
                <div class=""><i class="fa fa-phone fa-fw" aria-hidden="true" title="{t}Customer{/t}"></i><span class="Order_Telephone">{$order->get('Telephone')}</span></div>


            </div>

            <div class="data_field small {if $order->get('Email')==''}hide{/if}" style="margin-top:5px">


                <div class=""><i class="fa fa-envelope fa-fw" aria-hidden="true" title="{t}Customer{/t}"></i><span class="Order_Email">{$order->get('Email')}</span></div>

            </div>

            <div class="data_field  " style="padding:10px 0px 20px 0px;">
                <div style="float:left;padding-bottom:20px;padding-right:20px" class="Delivery_Address">
                    <div style="margin-bottom:10px">
                        <span class="{if $order->get('Order For Collection')=='Yes'}hide{/if}"><i   class="   fa fa-truck fa-flip-horizontal button" aria-hidden="true""></i>{t}Deliver to{/t}</span>
                        <span class="{if $order->get('Order For Collection')=='No'}hide{/if}"><i   class="   fa fa-hand-rock-o fa-flip-horizontal button" aria-hidden="true""></i>{t}Collection{/t}</span>

                    </div>

                    <div class="small Order_Delivery_Address " style="max-width: 140px;">{$order->get('Order Delivery Address Formatted')}</div>
                </div>
                <div style="float:right;padding-bottom:20px;" class="Billing_Address">
                    <div style="margin-bottom:10px"><i class="fa fa-dollar button" aria-hidden="true""></i>{t}Billed to{/t}</div>
                    <div class="small Order_Invoice_Address" style="max-width: 140px;">{$order->get('Order Invoice Address Formatted')}</div>
                </div>
                <div style="clear:both"></div>
            </div>


        </div>
        <div style="clear:both"></div>
    </div>

    <div class="block " style="align-items: stretch;flex: 1 ">

        <div id="delivery_notes" class="delivery_notes {if $deliveries|@count == 0}hide{/if}" style="position:relative;top:-5px;">



        {foreach from=$deliveries item=dn}

            <div class="node" id="delivery_node_{$dn->id}">
                    <span class="node_label" >
                         <i class="fa fa-truck fa-flip-horizontal fa-fw " aria-hidden="true"></i> <span class="link" onClick="change_view('delivery_notes/{$dn->get('Delivery Note Store Key')}/{$dn->id}')">{$dn->get('ID')}</span>
                        (<span class="Delivery_Note_State">{$dn->get('Abbreviated State')}</span>)
                        <a class="pdf_link {if $dn->get('State Index')<90 }hide{/if}" target='_blank' href="/pdf/dn.pdf.php?id={$dn->id}"> <img style="width: 50px;height:16px;position: relative;top:2px" src="/art/pdf.gif"></a>
                    </span>
            </div>
        {/foreach}

        </div>


        <div id="invoices" class="invoices {if $invoices|@count == 0}hide{/if}" style="margin-bottom:10px;">



            {foreach from=$invoices item=invoice}

                <div class="node" id="invoice_{$invoice->id}">
                    <span class="node_label" >
                        <i class="fa fa-file-text-o fa-fw " aria-hidden="true"></i>
                        <span class="link" onClick="change_view('invoices/{$invoice->get('Invoice Store Key')}/{$invoice->id}')">{$invoice->get('Invoice Public ID')}</span>
                        <a class="pdf_link" target='_blank' href="/pdf/invoice.pdf.php?id={$invoice->id}"> <img style="width: 50px;height:16px;position: relative;top:2px" src="/art/pdf.gif"></a>
                    </span>
                </div>
            {/foreach}

        </div>








        <div class="payments {if $order->get('Order Number Items')==0  or $order->get('State Index')<0 }hide{/if}  "  >


            {assign expected_payment $order->get('Expected Payment')}


            <div id="expected_payment" class="payment node  {if $expected_payment==''}hide{/if} " >




                <span class="node_label   ">{$expected_payment}</span>







            </div>



            <div id="create_payment" class="payment node">


             <span class="node_label very_discreet italic">{t}Payments{/t}</span>


            <div class="payment_operation {if $order->get('Order To Pay Amount')<=0     }hide{/if}  ">
                <div class="square_button right" style="padding:0;margin:0;position:relative;top:0px" title="{t}add payment{/t}">
                    <i class="fa fa-plus" aria-hidden="true" onclick="show_add_payment_to_order()"></i>

                </div>
            </div>




    </div>




        <div id="payment_nodes">
            {foreach from=$payments item=payment}
                <div class="payment node">
                    <span class="node_label"> <span class="link" onClick="change_view('order/{$order->id}/payment/{$payment->id}')">{if $payment->payment_account->get('Payment Account Block')=='Accounts'  or $payment->get('Payment Type')=='Credit' }{t}Credit{/t}{else}{$payment->get('Payment Account Code')}{/if}</span> </span>
                    <span class="node_amount"> {$payment->get('Transaction Amount')}</span>
                </div>
            {/foreach}
            <div class="payment node" style="border-top:1px solid #555;border-bottom:1px solid #555 ">
                <span class="node_label">{t}To pay{/t} </span>
                <span class="node_amount"> {$order->get('To Pay Amount')}</span>
            </div>
        </div>




    </div>

<div style="clear:both"></div></div>
    <div class="block " style="align-items: stretch;flex: 1 ">
    <table border="0" class="totals" style="position:relative;top:-5px">

        <tr class="total ">

            <td colspan="2" class="align_center ">{t}Original order{/t}</td>
        </tr>


        <tr>
            <td class="label">{t}Items{/t}</td>
            <td class="aright Items_Net_Amount">{$order->get('Items Net Amount')}</td>
        </tr>
       
        <tr>
            <td class="label"  id="Charges_Net_Amount_label" >{t}Charges{/t}</td>
            <td class="aright "><span id="Charges_Net_Amount_form" class="hide"><i id="set_charges_as_auto" class="fa fa-magic button" onClick="set_charges_as_auto()" aria-hidden="true"></i>  <input value="{$order->get('Order Charges Net Amount')}" ovalue="{$order->get('Order Charges Net Amount')}"  style="width: 100px" id="Charges_Net_Amount_input"  > <i id="Charges_Net_Amount_save" class="fa fa-cloud save" onClick="save_charges_value()" aria-hidden="true"></i> </span><span id="Charges_Net_Amount" class="Charges_Net_Amount button" >{$order->get('Charges Net Amount')}<span></td>
        </tr>
        <tr>
            <td class="label"  id="Shipping_Net_Amount_label" >{t}Shipping{/t}</td>
            <td class="aright "><span id="Shipping_Net_Amount_form" class="hide"><i id="set_shipping_as_auto" class="fa fa-magic button" onClick="set_shipping_as_auto()" aria-hidden="true"></i>  <input value="{$order->get('Order Shipping Net Amount')}" ovalue="{$order->get('Order Shipping Net Amount')}"  style="width: 100px" id="Shipping_Net_Amount_input"  > <i id="Shipping_Net_Amount_save" class="fa fa-cloud save" onClick="save_shipping_value()" aria-hidden="true"></i> </span><span id="Shipping_Net_Amount" class="Shipping_Net_Amount button" >{$order->get('Shipping Net Amount')}<span></td>
        </tr>
        <tr class="subtotal">
            <td class="label">{t}Net{/t}</td>
            <td class="aright Total_Net_Amount">{$order->get('Total Net Amount')}</td>
        </tr>

        <tr class="subtotal">
            <td class="label">{t}Tax{/t}</td>
            <td class="aright Total_Tax_Amount">{$order->get('Total Tax Amount')}</td>
        </tr>

        <tr class="total">
            <td class="label">{t}Total{/t}</td>
            <td class="aright Total_Amount  button " amount="{$order->get('Order To Pay Amount')}" onclick="try_to_pay(this)">{$order->get('Total Amount')}</td>
        </tr>




        </tbody>



    </table>
    <div style="clear:both"></div>
</div>
    <div class="block " style="align-items: stretch;flex: 1;">
        <div class="state" style="height:30px;margin-bottom:10px;position:relative;top:-5px">
            <div id="back_operations">

            </div>
            <span style="float:left;padding-left:10px;padding-top:5px" class="Order_State"> {t}Creating refund{/t} </span>
            <div id="forward_operations">

                <div id="create_refund_operations" class="order_operation {if {$order->get('State Index')}<100  }hide{/if}">
                    <div  class="square_button right  " title="{t}Create refund{/t}">
                        <i class="fa fa-cloud save   open_create_refund_dialog_button" aria-hidden="true" onclick="save_refund(this)"></i>

                    </div>
                </div>



            </div>
        </div>

        <table border="0" class="info_block acenter">

            <tr>

                <td>
                    <span style=""><i class="fa fa-cube fa-fw discreet" aria-hidden="true"></i> <span class="affected_items">0</span> / <span class="Order_Number_items">{$order->get('Number Items')}</span></span>
                    <span style="padding-left:20px"><i class="fa fa-percent fa-fw  " aria-hidden="true"></i> <span class="percentage_refunded">0.00</span>%</span>


                </td>
            </tr>

            <table border="0" class="totals" style="position:relative;top:20px">


                <tr class="subtotal first">
                    <td class="label">{t}Net{/t}</td>
                    <td class="aright Refund_Net_Amount">{$zero_amount}</td>
                </tr>
                <tr class="subtotal first">
                    <td class="label">{t}Tax{/t}</td>
                    <td class="aright Refund_Tax_Amount">{$zero_amount}</td>
                </tr>
                <tr class="total first">
                    <td class="label">{t}Total{/t}</td>
                    <td class="aright Refund_Total_Amount">{$zero_amount}</td>
                </tr>

            </table>




        </table>

    </div>
<div style="clear:both"></div></div>

<div id="add_payment" class="table_new_fields hide">

    <div style="align-items: stretch;flex: 1;padding:10px 20px;border-bottom: 1px solid #ccc;position: relative">

        <i style="position:absolute;top:10px;" class="fa fa-window-close fa-flip-horizontal button" aria-hidden="true" onclick="close_add_payment_to_order()"></i>

        <table border="0" style="width:50%;float:right;xborder-left:1px solid #ccc;width:100%;">
            <tr>
                <td style="width: 500px">
                <td>
                <td></td>

                <td>
                    <div id="new_payment_payment_account_buttons">
                        {foreach from=$store->get_payment_accounts('objects','Active') item=payment_account}

                            {if $payment_account->get('Payment Account Block')=='Accounts'}
                                <div class="button  {if $customer->get('Customer Account Balance')<=0}hide{/if} {$payment_account->get('Payment Account Block')}" onclick="select_payment_account(this)"
                                     data-settings='{ "payment_account_key":"{$payment_account->id}", "max_amount":"{$customer->get('Customer Account Balance')}" , "payment_method":"{$payment_account->get('Default Payment Method')}", "block":"{$payment_account->get('Payment Account Block')}" }' class="new_payment_payment_account_button unselectable
                        button {if $payment_account->get('Payment Account Block')=='Accounts' and $customer->get('Customer Account Balance')<=0  }hide{/if}" style="border:1px solid #ccc;padding:10px
                        5px;margin-bottom:2px">{t}Customer credit{/t} <span class="discreet padding_left_10">{$customer->get('Account Balance')}</span></div>
                                {else}
                                <div  class="button" onclick="select_payment_account(this)"
                                     data-settings='{ "payment_account_key":"{$payment_account->id}", "max_amount":"" , "payment_method":"{$payment_account->get('Default Payment Method')}", "block":"{$payment_account->get('Payment Account Block')}" }' class="new_payment_payment_account_button unselectable
                        button {if $payment_account->get('Payment Account Block')=='Accounts' and $customer->get('Customer Account Balance')<=0  }hide{/if}" style="border:1px solid #ccc;padding:10px
                        5px;margin-bottom:2px">{$payment_account->get('Name')}</div>
                            {/if}



                        {/foreach}
                    </div>
                    <input type="hidden" id="new_payment_payment_account_key" value="">
                    <input type="hidden" id="new_payment_payment_method" value="">


                </td>

                <td class="payment_fields " style="padding-left:30px;width: 300px">
                    <table>
                        <tr>
                            <td> {t}Amount{/t}</td>
                            <td style="padding-left:20px"><input disabled=true class="new_payment_field" id="new_payment_amount" placeholder="{t}Amount{/t}"></td>
                        </tr>
                        <tr>
                            <td>  {t}Reference{/t}</td>
                            <td style="padding-left:20px"><input disabled=true class="new_payment_field" id="new_payment_reference" placeholder="{t}Reference{/t}"></td>
                        </tr>
                    </table>
                </td>

                <td id="save_new_payment" class="buttons save" onclick="save_new_payment()"><span>{t}Save{/t}</span> <i class=" fa fa-cloud " aria-hidden="true"></i></td>
            </tr>

        </table>
    </div>
</div>

<script>




    function select_payment_account(element) {

        var settings = $(element).data('settings')

        console.log(settings)
        $('#new_payment_payment_account_key').val(settings.payment_account_key).data('settings',settings)
        $('#new_payment_payment_method').val(settings.payment_method)

        $("#add_payment :input").attr("disabled", false);
        $("#add_payment .payment_fields").removeClass("just_hinted");


        if(settings.block=='Accounts'){
            $('#new_payment_reference').closest('tr').addClass('hide')
        }else{
            $('#new_payment_reference').closest('tr').removeClass('hide')
        }


        $('.new_payment_payment_account_button').addClass('super_discreet')
        $(element).removeClass('super_discreet')


        if ($('#new_payment_amount').val() == '') {
            $('#new_payment_amount').focus()
        } else {
            $('#new_payment_reference').focus()
        }

    }


    function try_to_pay(element) {

        if ($(element).attr('amount') > 0) {

            if ($('#add_payment').hasClass('hide')) {
                show_add_payment_to_order()


            }
            if(!$('#new_payment_amount').is(':disabled')){
                $('#new_payment_amount').val($(element).attr('amount'))

                validate_new_payment();
            }




        } else if ($(element).attr('amount') < 0) {


            if ($('#payment_refund_amount').is(':visible')) {

                var amount=Math.abs($(element).attr('amount'))

                var max_amount= $('#payment_refund_dialog').data('settings').amount

              if(max_amount<amount){
                  amount=max_amount
              }



                $('#payment_refund_amount').val(amount)
                validate_refund_form()
            }

            //payment_refund_amount

        }


    }


    function show_add_payment_to_order() {


        if ($('#add_payment').hasClass('hide')) {

            change_tab('order.payments', {
                'add_payment': 1
            })
            $('#tabs').addClass('hide')


            $("#add_payment :input").attr("disabled", true);
            $(".payment_fields").addClass("just_hinted");
            $('#new_payment_reference').closest('tr').removeClass('hide')


            $('#add_payment').removeClass('hide')

            $('#new_payment_payment_account_key').val('')
            $('#new_payment_payment_method').val('')

            $('.new_payment_payment_account_button').removeClass('super_discreet')


            // $('#delivery_number').val('').focus()

        }

    }


    function close_add_payment_to_order() {

        $('#add_payment').addClass('hide')

        //change_tab('order.items')
        $('#tabs').removeClass('hide')


    }




    $(document).on('click', '#Shipping_Net_Amount', function (evt) {
        $('#Shipping_Net_Amount').addClass('hide')
        $('#Shipping_Net_Amount_form').removeClass('hide')

    })

    $(document).on('click', '#Shipping_Net_Amount_label', function (evt) {
        $('#Shipping_Net_Amount').removeClass('hide')
        $('#Shipping_Net_Amount_form').addClass('hide')

    })

    $(document).on('input propertychange', '#Shipping_Net_Amount_input', function (evt) {


        $('#Shipping_Net_Amount_save').addClass('changed')

        if( ! validate_number($('#Shipping_Net_Amount_input').val(), 0, 999999999) || $('#Shipping_Net_Amount_input').val()=='' ){
          $('#Shipping_Net_Amount_save').addClass('valid').removeClass('error')

      }else{
          $('#Shipping_Net_Amount_save').removeClass('valid').addClass('error ')

      }


    })

    function save_shipping_value() {


        if ($('#Shipping_Net_Amount_save').hasClass('wait')) {
            return;
        }
        $('#Shipping_Net_Amount_save').addClass('wait')

        $('#Shipping_Net_Amount_save').removeClass('fa-cloud').addClass('fa-spinner fa-spin');


        if($('#Shipping_Net_Amount_input').val()==''){
            $('#Shipping_Net_Amount_input').val(0)
        }

        var amount=parseFloat($('#Shipping_Net_Amount_input').val())




        var ajaxData = new FormData();

        ajaxData.append("tipo", 'set_shipping_value')

        ajaxData.append("order_key", '{$order->id}')
        ajaxData.append("amount", amount)



        $.ajax({
            url: "/ar_edit_orders.php", type: 'POST', data: ajaxData, dataType: 'json', cache: false, contentType: false, processData: false, complete: function () {
            }, success: function (data) {


                $('#Shipping_Net_Amount_save').removeClass('wait')
                $('#Shipping_Net_Amount_save').addClass('fa-cloud').removeClass('fa-spinner fa-spin');


                console.log(data)

                if (data.state == '200') {


                    $('#Shipping_Net_Amount').removeClass('hide')
                    $('#Shipping_Net_Amount_form').addClass('hide')

                    $('.Total_Amount').attr('amount', data.metadata.to_pay)
                    $('.Order_To_Pay_Amount').attr('amount', data.metadata.to_pay)



                    $('#Shipping_Net_Amount_input').val(data.metadata.shipping).attr('ovalue',data.metadata.shipping)
                    $('#Charges_Net_Amount_input').val(data.metadata.charges).attr('ovalue',data.metadata.charges)

                    if (data.metadata.to_pay == 0 || data.metadata.payments == 0) {
                        $('.Order_Payments_Amount').addClass('hide')
                        $('.Order_To_Pay_Amount').addClass('hide')

                    } else {
                        $('.Order_Payments_Amount').removeClass('hide')
                        $('.Order_To_Pay_Amount').removeClass('hide')

                    }

                    if (data.metadata.to_pay != 0 || data.metadata.payments == 0) {
                        $('.Order_Paid').addClass('hide')
                    } else {
                        $('.Order_Paid').removeClass('hide')
                    }

                    if (data.metadata.to_pay <= 0) {
                        $('.payment_operation').addClass('hide')

                    } else {
                        $('.payment_operation').removeClass('hide')
                    }


                    if (data.metadata.to_pay == 0) {
                        $('.Order_To_Pay_Amount').removeClass('button').attr('amount', data.metadata.to_pay)

                    } else {
                        $('.Order_To_Pay_Amount').addClass('button').attr('amount', data.metadata.to_pay)

                    }


                    $('#payment_nodes').html(data.metadata.payments_xhtml)


                    for (var key in data.metadata.class_html) {
                        $('.' + key).html(data.metadata.class_html[key])
                    }


                    for (var key in data.metadata.hide) {
                        $('#' + data.metadata.hide[key]).addClass('hide')
                    }
                    for (var key in data.metadata.show) {
                        $('#' + data.metadata.show[key]).removeClass('hide')
                    }




                } else if (data.state == '400') {
                    swal("{t}Error{/t}!", data.msg, "error")
                }


            }, error: function () {
                $('#Shipping_Net_Amount_save').removeClass('wait')
                $('#Shipping_Net_Amount_save').addClass('fa-cloud').removeClass('fa-spinner fa-spin');

            }
        });


    }

    function set_shipping_as_auto(){


    if ($('#set_shipping_as_auto').hasClass('wait')) {
        return;
    }
    $('#set_shipping_as_auto').addClass('wait')

    $('#set_shipping_as_auto').removeClass('fa-magic').addClass('fa-spinner fa-spin');



    var ajaxData = new FormData();

    ajaxData.append("tipo", 'set_shipping_as_auto')

    ajaxData.append("order_key", '{$order->id}')



    $.ajax({
        url: "/ar_edit_orders.php", type: 'POST', data: ajaxData, dataType: 'json', cache: false, contentType: false, processData: false, complete: function () {
        }, success: function (data) {


            $('#set_shipping_as_auto').removeClass('wait')
            $('#set_shipping_as_auto').addClass('fa-magic').removeClass('fa-spinner fa-spin');


            console.log(data)

            if (data.state == '200') {


                $('#Shipping_Net_Amount').removeClass('hide')
                $('#Shipping_Net_Amount_form').addClass('hide')

                $('.Total_Amount').attr('amount', data.metadata.to_pay)
                $('.Order_To_Pay_Amount').attr('amount', data.metadata.to_pay)



                $('#Shipping_Net_Amount_input').val(data.metadata.shipping).attr('ovalue',data.metadata.shipping)
                $('#Charges_Net_Amount_input').val(data.metadata.charges).attr('ovalue',data.metadata.charges)

                if (data.metadata.to_pay == 0 || data.metadata.payments == 0) {
                    $('.Order_Payments_Amount').addClass('hide')
                    $('.Order_To_Pay_Amount').addClass('hide')

                } else {
                    $('.Order_Payments_Amount').removeClass('hide')
                    $('.Order_To_Pay_Amount').removeClass('hide')

                }

                if (data.metadata.to_pay != 0 || data.metadata.payments == 0) {
                    $('.Order_Paid').addClass('hide')
                } else {
                    $('.Order_Paid').removeClass('hide')
                }

                if (data.metadata.to_pay <= 0) {
                    $('.payment_operation').addClass('hide')

                } else {
                    $('.payment_operation').removeClass('hide')
                }


                if (data.metadata.to_pay == 0) {
                    $('.Order_To_Pay_Amount').removeClass('button').attr('amount', data.metadata.to_pay)

                } else {
                    $('.Order_To_Pay_Amount').addClass('button').attr('amount', data.metadata.to_pay)

                }


                $('#payment_nodes').html(data.metadata.payments_xhtml)


                for (var key in data.metadata.class_html) {
                    $('.' + key).html(data.metadata.class_html[key])
                }


                for (var key in data.metadata.hide) {
                    $('#' + data.metadata.hide[key]).addClass('hide')
                }
                for (var key in data.metadata.show) {
                    $('#' + data.metadata.show[key]).removeClass('hide')
                }





            } else if (data.state == '400') {
                swal("{t}Error{/t}!", data.msg, "error")
            }


        }, error: function () {
            $('#set_shipping_as_auto').removeClass('wait')
            $('#set_shipping_as_auto').addClass('fa-magic').removeClass('fa-spinner fa-spin');


        }
    });

}




    $(document).on('click', '#Charges_Net_Amount', function (evt) {
        $('#Charges_Net_Amount').addClass('hide')
        $('#Charges_Net_Amount_form').removeClass('hide')

    })

    $(document).on('click', '#Charges_Net_Amount_label', function (evt) {
        $('#Charges_Net_Amount').removeClass('hide')
        $('#Charges_Net_Amount_form').addClass('hide')

    })

    $(document).on('input propertychange', '#Charges_Net_Amount_input', function (evt) {


        $('#Charges_Net_Amount_save').addClass('changed')

        if( ! validate_number($('#Charges_Net_Amount_input').val(), 0, 999999999) || $('#Charges_Net_Amount_input').val()=='' ){
            $('#Charges_Net_Amount_save').addClass('valid').removeClass('error')

        }else{
            $('#Charges_Net_Amount_save').removeClass('valid').addClass('error ')

        }


    })

    function save_charges_value() {


        if ($('#Charges_Net_Amount_save').hasClass('wait')) {
            return;
        }
        $('#Charges_Net_Amount_save').addClass('wait')

        $('#Charges_Net_Amount_save').removeClass('fa-cloud').addClass('fa-spinner fa-spin');


        if($('#Charges_Net_Amount_input').val()==''){
            $('#Charges_Net_Amount_input').val(0)
        }

        var amount=parseFloat($('#Charges_Net_Amount_input').val())




        var ajaxData = new FormData();

        ajaxData.append("tipo", 'set_charges_value')

        ajaxData.append("order_key", '{$order->id}')
        ajaxData.append("amount", amount)



        $.ajax({
            url: "/ar_edit_orders.php", type: 'POST', data: ajaxData, dataType: 'json', cache: false, contentType: false, processData: false, complete: function () {
            }, success: function (data) {


                $('#Charges_Net_Amount_save').removeClass('wait')
                $('#Charges_Net_Amount_save').addClass('fa-cloud').removeClass('fa-spinner fa-spin');


                console.log(data)

                if (data.state == '200') {


                    $('#Charges_Net_Amount').removeClass('hide')
                    $('#Charges_Net_Amount_form').addClass('hide')

                    $('.Total_Amount').attr('amount', data.metadata.to_pay)
                    $('.Order_To_Pay_Amount').attr('amount', data.metadata.to_pay)



                    $('#Charges_Net_Amount_input').val(data.metadata.charges).attr('ovalue',data.metadata.charges)
                    $('#Charges_Net_Amount_input').val(data.metadata.charges).attr('ovalue',data.metadata.charges)

                    if (data.metadata.to_pay == 0 || data.metadata.payments == 0) {
                        $('.Order_Payments_Amount').addClass('hide')
                        $('.Order_To_Pay_Amount').addClass('hide')

                    } else {
                        $('.Order_Payments_Amount').removeClass('hide')
                        $('.Order_To_Pay_Amount').removeClass('hide')

                    }

                    if (data.metadata.to_pay != 0 || data.metadata.payments == 0) {
                        $('.Order_Paid').addClass('hide')
                    } else {
                        $('.Order_Paid').removeClass('hide')
                    }

                    if (data.metadata.to_pay <= 0) {
                        $('.payment_operation').addClass('hide')

                    } else {
                        $('.payment_operation').removeClass('hide')
                    }


                    if (data.metadata.to_pay == 0) {
                        $('.Order_To_Pay_Amount').removeClass('button').attr('amount', data.metadata.to_pay)

                    } else {
                        $('.Order_To_Pay_Amount').addClass('button').attr('amount', data.metadata.to_pay)

                    }


                    $('#payment_nodes').html(data.metadata.payments_xhtml)


                    for (var key in data.metadata.class_html) {
                        $('.' + key).html(data.metadata.class_html[key])
                    }


                    for (var key in data.metadata.hide) {
                        $('#' + data.metadata.hide[key]).addClass('hide')
                    }
                    for (var key in data.metadata.show) {
                        $('#' + data.metadata.show[key]).removeClass('hide')
                    }




                } else if (data.state == '400') {
                    swal("{t}Error{/t}!", data.msg, "error")
                }


            }, error: function () {
                $('#Charges_Net_Amount_save').removeClass('wait')
                $('#Charges_Net_Amount_save').addClass('fa-cloud').removeClass('fa-spinner fa-spin');

            }
        });


    }

    function set_charges_as_auto(){


        if ($('#set_charges_as_auto').hasClass('wait')) {
            return;
        }
        $('#set_charges_as_auto').addClass('wait')

        $('#set_charges_as_auto').removeClass('fa-magic').addClass('fa-spinner fa-spin');



        var ajaxData = new FormData();

        ajaxData.append("tipo", 'set_charges_as_auto')

        ajaxData.append("order_key", '{$order->id}')



        $.ajax({
            url: "/ar_edit_orders.php", type: 'POST', data: ajaxData, dataType: 'json', cache: false, contentType: false, processData: false, complete: function () {
            }, success: function (data) {


                $('#set_charges_as_auto').removeClass('wait')
                $('#set_charges_as_auto').addClass('fa-magic').removeClass('fa-spinner fa-spin');


                console.log(data)

                if (data.state == '200') {


                    $('#Charges_Net_Amount').removeClass('hide')
                    $('#Charges_Net_Amount_form').addClass('hide')

                    $('.Total_Amount').attr('amount', data.metadata.to_pay)
                    $('.Order_To_Pay_Amount').attr('amount', data.metadata.to_pay)



                    $('#Charges_Net_Amount_input').val(data.metadata.charges).attr('ovalue',data.metadata.charges)
                    $('#Charges_Net_Amount_input').val(data.metadata.charges).attr('ovalue',data.metadata.charges)

                    if (data.metadata.to_pay == 0 || data.metadata.payments == 0) {
                        $('.Order_Payments_Amount').addClass('hide')
                        $('.Order_To_Pay_Amount').addClass('hide')

                    } else {
                        $('.Order_Payments_Amount').removeClass('hide')
                        $('.Order_To_Pay_Amount').removeClass('hide')

                    }

                    if (data.metadata.to_pay != 0 || data.metadata.payments == 0) {
                        $('.Order_Paid').addClass('hide')
                    } else {
                        $('.Order_Paid').removeClass('hide')
                    }

                    if (data.metadata.to_pay <= 0) {
                        $('.payment_operation').addClass('hide')

                    } else {
                        $('.payment_operation').removeClass('hide')
                    }


                    if (data.metadata.to_pay == 0) {
                        $('.Order_To_Pay_Amount').removeClass('button').attr('amount', data.metadata.to_pay)

                    } else {
                        $('.Order_To_Pay_Amount').addClass('button').attr('amount', data.metadata.to_pay)

                    }


                    $('#payment_nodes').html(data.metadata.payments_xhtml)


                    for (var key in data.metadata.class_html) {
                        $('.' + key).html(data.metadata.class_html[key])
                    }


                    for (var key in data.metadata.hide) {
                        $('#' + data.metadata.hide[key]).addClass('hide')
                    }
                    for (var key in data.metadata.show) {
                        $('#' + data.metadata.show[key]).removeClass('hide')
                    }





                } else if (data.state == '400') {
                    swal("{t}Error{/t}!", data.msg, "error")
                }


            }, error: function () {
                $('#set_charges_as_auto').removeClass('wait')
                $('#set_charges_as_auto').addClass('fa-magic').removeClass('fa-spinner fa-spin');


            }
        });

    }




    $('#add_payment').on('input propertychange', '.new_payment_field', function (evt) {

        validate_new_payment();
    })


    function validate_new_payment() {

       // console.log($('#new_payment_reference').val() != '')
       // console.log(!validate_number($('#new_payment_amount').val(), 0, 999999999))
        //console.log($('#new_payment_payment_account_key').val() > 0)


        var settings=$('#new_payment_payment_account_key').data('settings')



        if(settings.block=='Accounts'){
            var valid_reference=true;


        }else{
            var valid_reference=($('#new_payment_reference').val()==''?false:true);
        }


        if(settings.max_amount!=''){

            console.log(settings.max_amount)
            console.log($('#new_payment_amount').val())

            var valid_max_amount=(parseFloat(settings.max_amount)<parseFloat($('#new_payment_amount').val())?false:true)

        }else{
            var valid_max_amount=true;

        }


        if( !validate_number($('#new_payment_amount').val(), 0, 999999999) && $('#new_payment_payment_account_key').val() > 0){
            var valid_amount=true;
        }else{
            var valid_amount=false;

        }

        console.log(valid_reference)
        console.log(valid_max_amount)

        console.log(valid_amount)

        if (valid_reference && valid_max_amount &&  valid_amount) {
            console.log('xx')
            $('#save_new_payment').addClass('valid changed')
        } else {
            $('#save_new_payment').removeClass('valid changed')
        }


    }

    function save_new_payment() {


        if ($('#save_new_payment').hasClass('wait')) {
            return;
        }
        $('#save_new_payment').addClass('wait')

        $('#save_new_payment i').removeClass('fa-cloud').addClass('fa-spinner fa-spin');


        var ajaxData = new FormData();

        ajaxData.append("tipo", 'new_payment')
        ajaxData.append("parent", 'order')

        ajaxData.append("parent_key", '{$order->id}')
        ajaxData.append("payment_account_key", $('#new_payment_payment_account_key').val())
        ajaxData.append("amount", $('#new_payment_amount').val())
        ajaxData.append("payment_method", $('#new_payment_payment_method').val())

        ajaxData.append("reference", $('#new_payment_reference').val())


        $.ajax({
            url: "/ar_edit_orders.php", type: 'POST', data: ajaxData, dataType: 'json', cache: false, contentType: false, processData: false, complete: function () {
            }, success: function (data) {


                $('#save_new_payment').removeClass('wait')
                $('#save_new_payment i').addClass('fa-cloud').removeClass('fa-spinner fa-spin');


                console.log(data)

                if (data.state == '200') {

                    $('#new_payment_amount').val('')
                    $('#new_payment_reference').val('')

                    validate_new_payment()

                    $('.Total_Amount').attr('amount', data.metadata.to_pay)
                    $('.Order_To_Pay_Amount').attr('amount', data.metadata.to_pay)



                    $('#Shipping_Net_Amount_input').val(data.metadata.shipping).attr('ovalue',data.metadata.shipping)
                    $('#Charges_Net_Amount_input').val(data.metadata.charges).attr('ovalue',data.metadata.charges)

                    if (data.metadata.to_pay == 0 || data.metadata.payments == 0) {
                        $('.Order_Payments_Amount').addClass('hide')
                        $('.Order_To_Pay_Amount').addClass('hide')

                    } else {
                        $('.Order_Payments_Amount').removeClass('hide')
                        $('.Order_To_Pay_Amount').removeClass('hide')

                    }

                    if (data.metadata.to_pay != 0 || data.metadata.payments == 0) {
                        $('.Order_Paid').addClass('hide')
                    } else {
                        $('.Order_Paid').removeClass('hide')
                    }

                    if (data.metadata.to_pay <= 0) {
                        $('.payment_operation').addClass('hide')

                    } else {
                        $('.payment_operation').removeClass('hide')
                    }


                    if (data.metadata.to_pay == 0) {
                        $('.Order_To_Pay_Amount').removeClass('button').attr('amount', data.metadata.to_pay)

                    } else {
                        $('.Order_To_Pay_Amount').addClass('button').attr('amount', data.metadata.to_pay)

                    }


                    $('#payment_nodes').html(data.metadata.payments_xhtml)


                    for (var key in data.metadata.class_html) {
                        $('.' + key).html(data.metadata.class_html[key])
                    }


                    for (var key in data.metadata.hide) {
                        $('#' + data.metadata.hide[key]).addClass('hide')
                    }
                    for (var key in data.metadata.show) {
                        $('#' + data.metadata.show[key]).removeClass('hide')
                    }


                    if (state.tab == 'order.payments') {
                        rows.fetch({
                            reset: true
                        });
                    }

                    close_add_payment_to_order()


                } else if (data.state == '400') {
                    swal("{t}Error{/t}!", data.msg, "error")
                }


            }, error: function () {
                $('#save_new_payment').removeClass('wait')
                $('#save_new_payment i').addClass('fa-cloud').removeClass('fa-spinner fa-spin');

            }
        });


    }


</script>