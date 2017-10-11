{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 2 October 2017 at 18:53:20 GMT+8, Kuala Lumpur, Malaydia
 Copyright (c) 2016, Inikoo

 Version 3
-->
*}


<div class="timeline_horizontal with_time   {if $email_campaign->get('State Index')<0}hide{/if}  ">

    <ul class="timeline">
        <li id="submitted_node" class="li {if $email_campaign->get('State Index')>=30}complete{/if}">
            <div class="label">
                <span class="state ">{t}Setup mail list{/t}</span>
            </div>
            <div class="timestamp">
                <span class="Email_Campaign_Setup_Mail_List_Date">&nbsp;{$email_campaign->get('Setup Mail List Date')}</span> <span class="start_date">{$email_campaign->get('Creation Date')}</span>
            </div>
            <div class="dot"></div>
        </li>

        <li id="in_warehouse_node" class="li {if $email_campaign->get('State Index')>=40}complete{/if} ">
            <div class="label">
                <span class="state" >&nbsp;{t}Compose email{/t}&nbsp;<span></i></span></span>
            </div>
            <div class="timestamp">
			<span class="Order_In_Warehouse" ">&nbsp;

                    <span class="Order_Send_to_Warehouse_Date">&nbsp;{$email_campaign->get('Send to Warehouse Date')}</span>

                &nbsp;</span>
            </div>
            <div class="dot"></div>
        </li>

        <li id="packed_done_node" class="li {if $email_campaign->get('State Index')>=80}complete{/if} ">
            <div class="label">
                <span class="state">{t}Schedule send{/t}</span>
            </div>
            <div class="timestamp">
                <span>&nbsp;<span class="Order_Packed_Done_Date">&nbsp;{$email_campaign->get('Packed Done Date')}</span></span>
            </div>
            <div class="dot"></div>
        </li>

        <li  id="approved_node"  class="li {if $email_campaign->get('State Index')>=90}complete{/if} ">
            <div class="label">
                <span class="state">{t}Start send{/t}</span>
            </div>
            <div class="timestamp">
                <span>&nbsp;<span class="Order_Invoiced_Date">&nbsp;{$email_campaign->get('Invoiced Date')}</span></span>
            </div>
            <div class="dot"></div>
        </li>

        <li  id="dispatched_node"  class="li  {if $email_campaign->get('State Index')>=100}complete{/if}">
            <div class="label">
                <span class="state">{t}Send{/t}</span>
            </div>
            <div class="timestamp">
                <span>&nbsp;<span class=""></span>
                    &nbsp;</span>
            </div>
            <div class="dot"></div>
        </li>

    </ul>


</div>


<div class="timeline_horizontal  {if $email_campaign->get('State Index')>0}hide{/if}">
    <ul class="timeline" id="timeline">
        <li id="submitted_node" class="li complete">
            <div class="label">
                <span class="state ">{t}Submitted{/t}</span>
            </div>
            <div class="timestamp">
                <span class="Purchase_Order_Submitted_Date">&nbsp;{$email_campaign->get('Submitted Date')}</span> <span class="start_date">{$email_campaign->get('Created Date')} </span>
            </div>
            <div class="dot"></div>
        </li>

        <li id="send_node" class="li  cancelled">
            <div class="label">
                <span class="state ">{t}Cancelled{/t} <span></i></span></span>
            </div>
            <div class="timestamp">
                <span class="Cancelled_Date">{$email_campaign->get('Cancelled Date')} </span>
            </div>
            <div class="dot"></div>
        </li>


    </ul>
</div>

<div id="order" class="order" style="display: flex;" data-object="{$object_data}" order_key="{$email_campaign->id}">
    <div class="block" style="padding:10px 20px; align-items: stretch;flex: 1">

        <span>{t}Abandoned cart mailshot{/t}</span>

        <div style="clear:both"></div>
    </div>
    <div class="block " style="align-items: stretch;flex: 1;">
        <div class="state" style="height:30px;margin-bottom:10px;position:relative;top:-5px">
            <div id="back_operations">

                <div id="cancel_operations" class="order_operation {if $email_campaign->get('State Index')<0}hide{/if}">
                    <div class="square_button left" title="{t}Cancel{/t}">
                        <i class="fa fa-minus-circle error " aria-hidden="true" onclick="toggle_order_operation_dialog('cancel')"></i>
                        <table id="cancel_dialog" border="0" class="order_operation_dialog hide">
                            <tr class="top">
                                <td colspan="2">{t}Cancel order{/t}</td>
                            </tr>
                            <tr class="changed buttons">
                                <td>
                                    <i class="fa fa-sign-out fa-flip-horizontal button" aria-hidden="true" onclick="close_dialog('cancel')"></i>
                                </td>
                                <td class="aright">
                                    <span data-data='{ "field": "Order State","value": "Cancelled","dialog_name":"cancel"}' id="cancel_save_buttons" class="error save button"
                                          onclick="save_order_operation(this)">
                                        <span class="label">{t}Cancel{/t}</span>
                                        <i class="fa fa-cloud fa-fw  " aria-hidden="true"></i>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="undo_submit_operations" class="order_operation {if $email_campaign->get('State Index')!=30}hide{/if}">
                    <div class="square_button left" title="{t}Send back to basket{/t}">
												<span class="fa-stack" onclick="toggle_order_operation_dialog('undo_submit')">
						<i class="fa fa-paper-plane-o discreet " aria-hidden="true"></i>
						<i class="fa fa-ban fa-stack-1x discreet error"></i>
						</span>


                        <table id="undo_submit_dialog" border="0" class="order_operation_dialog hide">
                            <tr class="top">
                                <td class="label" colspan="2">{t}Send back to basket{/t}</td>
                            </tr>
                            <tr class="changed buttons">
                                <td><i class="fa fa-sign-out fa-flip-horizontal button" aria-hidden="true" onclick="close_dialog('undo_submit')"></i></td>
                                <td class="aright"><span data-data='{  "field": "Order State","value": "InBasket","dialog_name":"undo_submit"}' id="undo_submit_save_buttons" class="valid save button"
                                                         onclick="save_order_operation(this)"><span class="label">{t}Save{/t}</span> <i class="fa fa-cloud fa-fw  " aria-hidden="true"></i></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="undo_send_operations" class="order_operation {if $email_campaign->get('Order State')!='Send'}hide{/if}">
                    <div class="square_button left" xstyle="padding:0;margin:0;position:relative;top:-5px" title="{t}Unmark as send{/t}">
						<span class="fa-stack" onclick="toggle_order_operation_dialog('undo_send')">
						<i class="fa fa-plane discreet " aria-hidden="true"></i>
						<i class="fa fa-ban fa-stack-1x very_discreet error"></i>
						</span>
                        <table id="undo_send_dialog" border="0" class="order_operation_dialog hide">
                            <tr class="top">
                                <td colspan="2" class="label">{t}Unmark as send{/t}</td>
                            </tr>
                            <tr class="buttons changed">
                                <td><i class="fa fa-sign-out fa-flip-horizontal button" aria-hidden="true" onclick="close_dialog('undo_send')"></i></td>
                                <td class="aright"><span id="undo_send_save_buttons" class="valid save button" onclick="save_order_operation('undo_send','Submitted')"><span class="label">{t}Save{/t}</span> <i
                                                class="fa fa-cloud fa-fw  " aria-hidden="true"></i></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <span style="float:left;padding-left:10px;padding-top:5px" class="Order_State"> {$email_campaign->get('State')} </span>
            <div id="forward_operations">


                <div id="create_invoice_operations" class="order_operation {if {$email_campaign->get('State Index')}!=80  }hide{/if}">
                    <div  class="square_button right  " title="{t}Create invoice{/t}">
                        <i class="fa fa-file-text-o  " aria-hidden="true" onclick="toggle_order_operation_dialog('create_invoice')"></i>
                        <table id="create_invoice_dialog" border="0" class="order_operation_dialog hide">
                            <tr class="top">
                                <td class="label" colspan="2">{t}Invoice order{/t}</td>
                            </tr>
                            <tr class="changed buttons">
                                <td><i class="fa fa-sign-out fa-flip-horizontal button" aria-hidden="true" onclick="close_dialog('create_invoice')"></i></td>
                                <td class="aright"><span data-data='{  "field": "Order State","value": "Approved","dialog_name":"create_invoice"}' id="create_invoice_save_buttons" class="valid save button"
                                                         onclick="save_order_operation(this)"><span class="label">{t}Save{/t}</span> <i class="fa fa-cloud fa-fw  " aria-hidden="true"></i></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div id="send_to_warehouse_operations" class="order_operation {if {$email_campaign->get('State Index')|intval}>30 or  {$email_campaign->get('State Index')|intval}<10   or $email_campaign->get('Order Number Items')==0   }hide{/if}">
                    <div  class="square_button right  " title="{t}Send to warehouse{/t}">
                        <i class="fa fa-map   " aria-hidden="true" onclick="toggle_order_operation_dialog('send_to_warehouse')"></i>
                        <table id="send_to_warehouse_dialog" border="0" class="order_operation_dialog hide">
                            <tr class="top">
                                <td class="label" colspan="2">{t}Send to warehouse{/t}</td>
                            </tr>
                            <tr class="changed buttons">
                                <td><i class="fa fa-sign-out fa-flip-horizontal button" aria-hidden="true" onclick="close_dialog('send_to_warehouse')"></i></td>
                                <td class="aright"><span data-data='{  "field": "Order State","value": "InWarehouse","dialog_name":"send_to_warehouse"}' id="send_to_warehouse_save_buttons" class="valid save button"
                                                         onclick="save_order_operation(this)"><span class="label">{t}Save{/t}</span> <i class="fa fa-cloud fa-fw  " aria-hidden="true"></i></span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div id="submit_operations" class="order_operation {if $email_campaign->get('State Index')!=10   or  $email_campaign->get('Order Number Items')==0 }hide{/if}">
                    <div  class="square_button right  " title="{t}Submit{/t}">
                        <i class="fa fa-paper-plane-o   " aria-hidden="true" onclick="toggle_order_operation_dialog('submit')"></i>
                        <table id="submit_dialog" border="0" class="order_operation_dialog hide">
                            <tr class="top">
                                <td class="label" colspan="2">{t}Submit order{/t}</td>
                            </tr>
                            <tr class="changed buttons">
                                <td><i class="fa fa-sign-out fa-flip-horizontal button" aria-hidden="true" onclick="close_dialog('submit')"></i></td>
                                <td class="aright"><span data-data='{  "field": "Order State","value": "InProcess","dialog_name":"submit"}' id="submit_save_buttons" class="valid save button"
                                                         onclick="save_order_operation(this)"><span class="label">{t}Save{/t}</span> <i class="fa fa-cloud fa-fw  " aria-hidden="true"></i></span></td>
                            </tr>
                        </table>
                    </div>
                </div>


            </div>
        </div>

        <table border="0" class="hide info_block acenter">

            <tr>

                <td>
                    <span style=""><i class="fa fa-cube fa-fw discreet" aria-hidden="true"></i> <span class="Order_Number_items">{$email_campaign->get('Number Items')}</span></span>
                    <span style="padding-left:20px"><i class="fa fa-tag fa-fw  " aria-hidden="true"></i> <span class="Order_Number_Items_with_Deals">{$email_campaign->get('Number Items with Deals')}</span></span>
                    <span class="error {if $email_campaign->get('Order Number Items Out of Stock')==0}hide{/if}" style="padding-left:20px"><i class="fa fa-cube fa-fw  " aria-hidden="true"></i> <span
                                class="Order_Number_Items_with_Out_of_Stock">{$email_campaign->get('Number Items Out of Stock')}</span></span>
                    <span class="error {if $email_campaign->get('Order Number Items Returned')==0}hide{/if}" style="padding-left:20px"><i class="fa fa-thumbs-o-down fa-fw   " aria-hidden="true"></i> <span
                                class="Order_Number_Items_with_Returned">{$email_campaign->get('Number Items Returned')}</span></span>
                </td>
            </tr>






        </table>

    </div>
    <div class="block " style="align-items: stretch;flex: 1 ">









        <div class="payments {if $email_campaign->get('Order Number Items')==0  or $email_campaign->get('State Index')<0 }hide{/if}  "  >


            {assign expected_payment $email_campaign->get('Expected Payment')}


            <div id="expected_payment" class="payment node  {if $expected_payment==''}hide{/if} " >




                <span class="node_label   ">{$expected_payment}</span>







            </div>



            <div id="create_payment" class="payment node">


             <span class="node_label very_discreet italic">{t}Payments{/t}</span>


            <div class="payment_operation {if $email_campaign->get('Order To Pay Amount')<=0     }hide{/if}  ">
                <div class="square_button right" style="padding:0;margin:0;position:relative;top:0px" title="{t}add payment{/t}">
                    <i class="fa fa-plus" aria-hidden="true" onclick="show_add_payment_to_order()"></i>

                </div>
            </div>




    </div>




    </div>

<div style="clear:both"></div></div>
<div class="block " style="align-items: stretch;flex: 1 ">
    <table border="0" class="totals {if $email_campaign->get('State Index')<30}hide{/if}" style="position:relative;top:-5px;margin-bottom:20px">

        <tr>
            <td class="label">{t}Send{/t}</td>
            <td class="aright Send_Emails">{$email_campaign->get('Send Email')}</td>
        </tr>
        <tr class="subtotal">
            <td class="label">{t}Soft bounced{/t}</td>
            <td class="aright Soft_Bounced_Emails">{$email_campaign->get('Soft Bounced Emails')}</td>
        </tr>
        <tr>
            <td class="label">{t}Hard bounced{/t}</td>
            <td class="aright Hard_Bounced_Emails">{$email_campaign->get('Hard Bounced Emails')}</td>
        </tr>


        <tr class="subtotal">
            <td class="label">{t}Read{/t}</td>
            <td class="aright Read_Emails">{$email_campaign->get('Read Emails')}</td>
        </tr>
        <tr class="subtotal">
            <td class="label">{t}Basket updated{/t}</td>
            <td class="aright Goal_A_Emails">{$email_campaign->get('Goal A Emails')}</td>
        </tr>
        <tr class="total">
            <td class="label">{t}Order placed{/t}</td>
            <td class="aright Goal_B_Emails">{$email_campaign->get('Goal B Emails')}</td>
        </tr>




    </table>
    <div style="clear:both"></div>
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

        ajaxData.append("order_key", '{$email_campaign->id}')
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

    ajaxData.append("order_key", '{$email_campaign->id}')



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

        ajaxData.append("order_key", '{$email_campaign->id}')
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

        ajaxData.append("order_key", '{$email_campaign->id}')



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

        ajaxData.append("parent_key", '{$email_campaign->id}')
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