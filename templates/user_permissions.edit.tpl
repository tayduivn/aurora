{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 22-08-2019 14:38:29 MYT, National Rute 1 (North Wrast Cost), Australia
 Copyright (c) 2019, Inikoo

 Version 3
-->
*}
<style>

    table.permissions tr {
        line-height: normal;
        border: none;
        height: 27px;
    }

    .permissions td.icons {
        width: 60px;
    }

    .permissions td.label {
        width: 40%;
    }
</style>


<table class="permissions unselectable" style="margin-top: 10px">


    <tr style="border-bottom: 1px solid #ccc;height: 37px">
        <td style="padding-top: 10px" class="icons"><i class="far fa-fw fa-tachometer-alt"></i> <i class="far fa-fw fa-chart-line"></i></td>


        <td style="padding-top: 10px" colspan="2">
            <div style="height: 30px">
             <span data-group_id="5" class=" button permission_type {if 5|in_array:$user_groups}{else}discreet_on_hover{/if}">
            <i class="far {if 5|in_array:$user_groups}fa-check-square{else}fa-square{/if}  fa-fw "></i>
            <span title="{t}View all stores products and sales reports{/t}" class="padding_right_10">{t}Products & sales{/t}</span>
            </span>
                <span data-group_id="26" class=" button permission_type {if 26|in_array:$user_groups}{else}discreet_on_hover{/if}">
            <i class="far {if 26|in_array:$user_groups}fa-check-square{else}fa-square{/if} fa-fw "></i>
            <span title="{t}View customers data from all stores{/t}" class="padding_right_10">{t}Customers & orders{/t}</span>
            </span>


                <span data-group_id="28" class=" button permission_type {if 14|in_array:$user_groups}{else}discreet_on_hover{/if}">
            <i class="far {if 14|in_array:$user_groups}fa-check-square{else}fa-square{/if} fa-fw "></i>
            <span title="{t}View suppliers data, purchase orders{/t}" class="padding_right_10">{t}Suppliers, costs & profits{/t}</span>
            </span>

                <span data-group_id="27" class=" button permission_type {if 27|in_array:$user_groups}{else}discreet_on_hover{/if}">
                <i class="far {if 27|in_array:$user_groups}fa-check-square{else}fa-square{/if} fa-fw "></i>
            <span title="{t}View inventory and warehouse data{/t}" class="padding_right_10">{t}Inventory{/t}</span>
            </span>

            </div>
            <div style="height: 30px">

        <span data-group_id="14" class=" button permission_type {if 14|in_array:$user_groups}{else}discreet_on_hover{/if}">
                <i class="far {if 14|in_array:$user_groups}fa-check-square{else}fa-square{/if} fa-fw "></i>
            <span title="{t}View employees performance reports and KPIs{/t}" class="padding_right_10">{t}Employees performance{/t}</span>
            </span>

                <span data-group_id="15" class=" button permission_type {if 15|in_array:$user_groups}{else}discreet_on_hover{/if}">
                <i class="far {if 15|in_array:$user_groups}fa-check-square{else}fa-square{/if} fa-fw "></i>
            <span title="{t}View users activity{/t}" class="padding_right_10">{t}Users activity{/t}</i></span>
            </span>
            </div>

        </td>

    </tr>

    <tr style="height: 37px">
        <td style="padding-top: 10px" class="icons"><i class="far fa-fw fa-briefcase"></i> <i class="far fa-fw fa-users-class"></i></td>

        <td style="padding-top: 10px" class="label">{t}System management & Users{/t} </td>

        <td style="padding-top: 10px">
             <span data-group_id="1" class=" button permission_type {if 1|in_array:$user_groups}{else}discreet_on_hover{/if} ">
            <i class="far {if 1|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Create and edit system users, employees and contractors, create stores, edit account settings{/t}" class="padding_right_10">{t}Admin{/t}</span>
            </span>


        </td>

    </tr>


    <tr>
        <td class="icons"><i class="far fa-fw fa-sitemap"></i></td>

        <td class="label">{t}Employees{/t} </td>

        <td>
             <span data-group_id="6" class=" button permission_type  {if 6|in_array:$user_groups}{else}discreet_on_hover{/if}  ">
            <i class="far   {if 6|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw  "></i>
            <span title="{t}Create and edit employees and contractors{/t}" class="padding_right_10">{t}Admin{/t}</span>
            </span>
            <span data-group_id="20" class=" button permission_type {if 20|in_array:$user_groups}{else}discreet_on_hover{/if} ">
            <i class="far {if 20|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Restricted view (time sheets) employees under user supervision{/t}" class="padding_right_10">{t}Only workers under supervision{/t}</span>
            </span>


        </td>

    </tr>
    <tr>
        <td class="icons"><i class="far fa-fw fa-hand-holding-box"></i> <i class="far fa-fw  fa-box"></i></td>

        <td class="label">{t}Purchasing and inventory life circle{/t} </td>

        <td>
             <span data-group_id="21" class=" button permission_type {if 21|in_array:$user_groups}{else}discreet_on_hover{/if} ">
            <i class="far {if 21|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Worker permissions plus discontinuing products{/t}" class="padding_right_10">{t}Supervisor{/t}</span>
            </span>
            <span data-group_id="8" class=" button permission_type {if 8|in_array:$user_groups}{else}discreet_on_hover{/if} ">
            <i class="far {if 8|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Create, edit suppliers and agents, create an edit inventory, process purchase orders and deliveries{/t}" class="padding_right_10">{t}Worker{/t}</span>
            </span>


        </td>

    </tr>
    <tr class="{if $account->get('Account Manufacturers') ==0}hide{/if}">
        <td class="icons"><i class="far fa-fw fa-industry"></i></td>

        <td class="label">{t}Production{/t} </td>

        <td>
             <span data-group_id="7" class=" button permission_type {if 7|in_array:$user_groups}{else}discreet_on_hover{/if} ">
            <i class="far {if 7|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Manage production products and assign jobs{/t}" class="padding_right_10">{t}Supervisor{/t}</span>
            </span>
            <span data-group_id="4" class=" button permission_type {if 4|in_array:$user_groups}{else}discreet_on_hover{/if} ">
            <i class="far {if 4|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Production operative{/t}" class="padding_right_10">{t}Worker{/t}</span>
            </span>


        </td>

    </tr>

    <tr>
        <td class="icons"><i class="far fa-fw fa-warehouse-alt"></i> <i class="far fa-fw  fa-box"></i></td>

        <td class="label">{t}Warehouse and stock control{/t} </td>

        <td>
             <span data-group_id="22" class=" button permission_type {if 22|in_array:$user_groups}{else}discreet_on_hover{/if} ">
            <i class="far {if 22|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Worker permissions plus lost & found, create and edit locations, set up replenishment levels{/t}" class="padding_right_10">{t}Supervisor{/t}</span>
            </span>
            <span data-group_id="3" class=" button permission_type {if 3|in_array:$user_groups}{else}discreet_on_hover{/if} ">
            <i class="far {if 3|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Book in, move and audit stock of parts{/t}" class="padding_right_10">{t}Worker{/t}</span>
            </span>


        </td>

    </tr>


    <tr>
        <td class="icons"><i class="far fa-fw fa-abacus"></i></td>

        <td class="label">{t}Invoices, credits and payments{/t} </td>

        <td>

            <span data-group_id="23" class=" button permission_type {if 23|in_array:$user_groups}{else}discreet_on_hover{/if}">
            <i class="far {if 23|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Can edit invoices, payments and customer credits{/t}" class="padding_right_10">{t}Accounting{/t}</span>
            </span>


        </td>

    </tr>


    <tr>
        <td class="icons"><i class="far fa-fw fa-conveyor-belt-alt"></i></td>

        <td class="label">{t}Order fulfillment{/t} </td>

        <td>
             <span data-group_id="17" class=" button permission_type {if 17|in_array:$user_groups}{else}discreet_on_hover{/if}">
            <i class="far {if 17|in_array:$user_groups}fa-check-square{else}fa-square{/if} fa-fw "></i>
            <span title="{t}Assign pickers and packers{/t}" class="padding_right_10">{t}Supervisor{/t}</span>
            </span>
            <span data-group_id="24" class=" button permission_type {if 24|in_array:$user_groups}{else}discreet_on_hover{/if}">
            <i class="far {if 24|in_array:$user_groups}fa-check-square{else}fa-square{/if} fa-fw "></i>
            <span title="{t}Picker{/t}" class="padding_right_10">{t}Picker{/t}</span>
            </span>
            <span data-group_id="25" class=" button permission_type {if 25|in_array:$user_groups}{else}discreet_on_hover{/if}">
            <i class="far {if 25|in_array:$user_groups}fa-check-square{else}fa-square{/if} fa-fw "></i>
            <span title="{t}Packer{/t}" class="padding_right_10">{t}Packer{/t}</span>
            </span>


        </td>

    </tr>


    <tr style="border-top:1px solid #ccc;height: 37px">
        <td style="padding-top: 10px" class="icons"><i class="far fa-fw fa-users"></i> <i class="far fa-fw fa-shopping-cart"></i></td>

        <td style="padding-top: 10px" class="label">{t}Customer services and order processing{/t} </td>

        <td style="padding-top: 10px">
             <span data-group_id="16" class=" button permission_store_scope permission_type {if 16|in_array:$user_groups}{else}discreet_on_hover{/if}">
            <i class="far {if 16|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Worker permissions plus delete customers{/t}" class="padding_right_10">{t}Supervisor{/t}</span>
            </span>
            <span data-group_id="2" class=" button permission_store_scope permission_type {if 2|in_array:$user_groups}{else}discreet_on_hover{/if}">
            <i class="far {if 2|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Create, edit customers, process orders and create refunds & replacements{/t}" class="padding_right_10">{t}Worker{/t}</span>
            </span>


        </td>

    </tr>
    <tr>
        <td class="icons"><i class="far fa-fw fa-store-alt"></i> <i class="far fa-fw  fa-globe"></i></td>

        <td class="label">{t}Products and website{/t} </td>

        <td>
             <span data-group_id="18" class=" button permission_store_scope permission_type {if 18|in_array:$user_groups}{else}discreet_on_hover{/if}">
            <i class="far {if 18|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Worker permissions plus force products as offline or out of stock{/t}" class="padding_right_10">{t}Supervisor{/t}</span>
            </span>
            <span data-group_id="9" class=" button permission_store_scope permission_type {if 9|in_array:$user_groups}{else}discreet_on_hover{/if}">
            <i class="far {if 9|in_array:$user_groups}fa-dot-circle{else}fa-circle{/if} fa-fw "></i>
            <span title="{t}Create, edit products, send newsletters and marketing emails, create and edit webpages{/t}" class="padding_right_10">{t}Worker{/t}</span>
            </span>


        </td>

    </tr>

    <tr class="permission_stores
    {if 18|in_array:$user_groups or 9|in_array:$user_groups or 16|in_array:$user_groups or 2|in_array:$user_groups   }{else}invisible{/if}
">
        <td colspan="3" style="padding-top: 0px;;padding-bottom: 0px">
            <div style="margin: 5px 0px">
                {foreach from=$stores item=store}
                    <span data-store_key={$store['Store Key']}  class=" button permission_store {if $store['Store Key']|in_array:$user_stores}{else}discreet_on_hover{/if}"">
                    <i class="far {if $store['Store Key']|in_array:$user_stores}fa-check-square{else}fa-square{/if} fa-fw "></i>
                    <span title="{$store['Store Name']}" class="padding_right_10">{$store['Store Code']}</span>
                    </span>
                {/foreach}
            </div>

        </td>
    </tr>
    <tr class=" " style="border-top: 1px solid #ccc">
        <td colspan=3 class=" {if $mode=='new'}hide{/if} ">
            <span onclick="save_permissions()" data-user_key="{$user_key}" class="save">{t}Save permissions{/t}   <i class="save_icon fa fa-cloud  "></i></span>
            <span class="updated_msg hide success margin_left_10"><i class="fa fa-check"></i> {t}Updated{/t}</span>
        </td>
    </tr>

</table>

<script>

    $(document).on('click', '.permissions .permission_type', function (evt) {

        var icon = $(this).find('i')


        $('.permissions .save').addClass('changed valid')
        $('.permissions .updated_msg').addClass('hide')

        if (icon.hasClass('fa-square')) {
            icon.removeClass('fa-square').addClass('fa-check-square')
            $(this).removeClass('discreet_on_hover')

        } else if (icon.hasClass('fa-check-square')) {

            icon.addClass('fa-square').removeClass('fa-check-square')
            $(this).addClass('discreet_on_hover')

        } else if (icon.hasClass('fa-circle')) {


            $(this).closest('tr').find('i').removeClass('fa-dot-circle').addClass('fa-circle')
            $(this).closest('tr').find('.permission_type').addClass('discreet_on_hover')

            icon.removeClass('fa-circle').addClass('fa-dot-circle')
            $(this).removeClass('discreet_on_hover')


        } else if (icon.hasClass('fa-dot-circle')) {


            $(this).closest('tr').find('i').removeClass('fa-dot-circle').addClass('fa-circle')
            $(this).closest('tr').find('.permission_type').addClass('discreet_on_hover')

            icon.addClass('fa-circle').removeClass('fa-dot-circle')
            $(this).addClass('discreet_on_hover')


        }

        var permission_store_scope = false

        $('.permissions  .permission_store_scope i').each(function (i, obj) {
            if ($(obj).hasClass('fa-check-square') || $(obj).hasClass('fa-dot-circle')) {
                permission_store_scope = true
            }

        });


        if (permission_store_scope) {
            $('.permission_stores').removeClass('invisible')

        } else {
            $('.permission_stores').addClass('invisible')

        }


    });

    $(document).on('click', '.permissions .permission_store', function (evt) {

        var icon = $(this).find('i')

        $('.permissions .save').addClass('changed valid')

        $('.permissions .updated_msg').addClass('hide')

        if (icon.hasClass('fa-square')) {
            icon.removeClass('fa-square ').addClass('fa-check-square')
            $(this).removeClass('discreet_on_hover')

        } else if (icon.hasClass('fa-check-square')) {

            icon.addClass('fa-square ').removeClass('fa-check-square')
            $(this).addClass('discreet_on_hover')


        }


    });


    function save_permissions() {


        var save_button = $('.permissions .save')

        if (!save_button.hasClass('valid') || save_button.hasClass('wait')) {
            return;
        }


        save_button.addClass('wait').find('i.save_icon').addClass('fa-spin fa-spinner')

        var user_groups = [];
        var stores = [];

        $('.permissions  .permission_type i').each(function (i, obj) {
            if ($(obj).hasClass('fa-check-square') || $(obj).hasClass('fa-dot-circle')) {
                user_groups.push($(obj).closest('.permission_type').data('group_id'))
            }

        });
        $('.permissions  .permission_store i').each(function (i, obj) {
            if ($(obj).hasClass('fa-check-square') || $(obj).hasClass('fa-dot-circle')) {
                stores.push($(obj).closest('.permission_store').data('store_key'))
            }

        });

        value = JSON.stringify({
            user_groups: user_groups, stores: stores
        })

        var ajaxData = new FormData();

        ajaxData.append("tipo", 'edit_field')
        ajaxData.append("object", 'user')

        ajaxData.append("key", save_button.data('user_key'))
        ajaxData.append("value", value)
        ajaxData.append("field", 'Permissions')


        $.ajax({
            url: "/ar_edit.php", type: 'POST', data: ajaxData, dataType: 'json', cache: false, contentType: false, processData: false, complete: function () {
            }, success: function (data) {

                console.log(data)
                save_button.removeClass('wait').find('i.save_icon').removeClass('fa-spin fa-spinner')
                if (data.state == '200') {

                    save_button.removeClass('valid changed')

                    $('.permissions .updated_msg').removeClass('hide')

                } else if (data.state == '400') {
                    swal(data.msg);
                }


            }, error: function () {

            }
        });


    }


</script>