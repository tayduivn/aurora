{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: Thu 3 Oct 2019 14:41:12 +0800 MYT, Kuala Lumpur, Malaysia
 Copyright (c) 2019, Inikoo

 Version 3
-->
*}


<div id="customer_client" class="subject_profile" style="padding-bottom: 0;border-bottom:none"
     data-customer_key="{$customer_client->get('Customer Key')}" data-customer_client_key="{$customer_client->id}" data-store_key="{$customer_client->get('Store Key')}">


    <div style="float: left;width: 590px;">

        <div class="{if $customer_client->get('Customer Client Name')|strlen <50 }hide{/if}">
            <h1 style="margin-bottom: 0;position: relative;top:-10px" class="Customer_Client_Name Subject_Name">{$customer_client->get('Customer Client Name')}</h1>
        </div>
        <div class="data_container" >

            <div class="data_field" style="min-width: 270px;">
                <i title="{t}Contact name{/t}" class="fa fa-fw  fa-male"></i><span class="Customer_Client_Main_Contact_Name">{$customer_client->get('Customer Client Main Contact Name')}</span>
            </div>
            <div class="data_field Customer_Client_Tax_Number_display {if !$customer_client->get('Customer Client Tax Number')}hide{/if}" style="min-width: 270px;">
                <i title="{t}Tax number{/t}" class="fal fa-fw fa-passport"></i><span
                        class="Customer_Client_Tax_Number_Formatted">{$customer_client->get('Tax Number Formatted')}</span>
            </div>
            <div class="data_field Customer_Client_Registration_Number_display {if !$customer_client->get('Customer Client Registration Number')}hide{/if}" style="min-width: 270px;">
                <i title="{t}Registration number{/t}" class="fal fa-fw fa-id-card"></i><span
                        class="Customer_Client_Registration_Number">{$customer_client->get('Registration Number')}</span>
            </div>

            <div style="min-height:80px;float:left;width:28px">
                <i class="fa fa-fw fa-map-marker-alt"></i>
            </div>
            <div class="Customer_Client_Contact_Address" style="float:left;min-width:242px">
                {$customer_client->get('Contact Address Formatted')}
            </div>

        </div>
        <div class="data_container" >
            <div id="Customer_Client_Main_Plain_Email_display"
                 class="data_field Subject_Email_display  {if !$customer_client->get('Customer Client Main Plain Email')}hide{/if}">
                <i class="fa fa-fw fa-at"></i> <span class="Subject_Email" id="Customer_Client_Other_Email_mailto">{if $customer_client->get('Customer Client Main Plain Email')}{mailto address=$customer_client->get('Main Plain Email')}{/if}</span>
            </div>

            <div id="Customer_Client_Other_Email_display" class="data_field hide">
                <i class="fa fa-fw fa-at discreet"></i> <span class="Customer_Client_Other_Email_mailto"></span>
            </div>
            <span id="display_telephones"></span> {if $customer_client->get('Customer Client Preferred Contact Number')=='Mobile'}
                <div id="Customer_Client_Main_Plain_Mobile_display"
                     class="data_field  Subject_Mobile_display  {if !$customer_client->get('Customer Client Main Plain Mobile')}hide{/if}">
                    <i class="far fa-fw fa-mobile"></i> <span
                            class="Customer_Client_Main_Plain_Mobile Subject_Mobile">{$customer_client->get('Main XHTML Mobile')}</span>
                </div>
                <div id="Customer_Client_Main_Plain_Telephone_display"
                     class="data_field Subject_Telephone_display {if !$customer_client->get('Customer Client Main Plain Telephone')}hide{/if}">
                    <i class="fa fa-fw fa-phone"></i> <span
                            class="Customer_Client_Main_Plain_Telephone Subject_Telephone">{$customer_client->get('Main XHTML Telephone')}</span>
                </div>
            {else}
                <div id="Customer_Client_Main_Plain_Telephone_display"
                     class="data_field Subject_Telephone_display {if !$customer_client->get('Customer Client Main Plain Telephone')}hide{/if}">
                    <i title="Telephone" class="fa fa-fw fa-phone"></i> <span  class="Customer_Client_Main_Plain_Telephone Subject_Telephone">{$customer_client->get('Main XHTML Telephone')}</span>
                </div>
                <div id="Customer_Client_Main_Plain_Mobile_display"
                     class="data_field Subject_Mobile_display {if !$customer_client->get('Customer Client Main Plain Mobile')}hide{/if}">
                    <i title="Mobile" class="fa fa-fw fa-mobile"></i> <span
                            class="Customer_Client_Main_Plain_Mobile Subject_Mobile">{$customer_client->get('Main XHTML Mobile')}</span>
                </div>
            {/if}
            <div id="Customer_Client_Main_Plain_FAX_display"
                 class="data_field {if !$customer_client->get('Customer Client Main Plain FAX')}hide{/if}">
                <i title="Fax" class="fa fa-fw fa-fax"></i> <span>{$customer_client->get('Main XHTML FAX')}</span>
            </div>


            <div id="Customer_Client_Other_Telephone_display" class="data_field hide">
                <i class="fa fa-fw fa-phone discreet"></i> <span></span>
            </div>

        </div>



        <div style="clear:both">
        </div>
    </div>


    <div style="float: right;width: 500px;">
        <div id="overviews">

            <table  class="overview">

                <tr class="Customer_Client_Sales_Representative_tr " >
                    <td>{t}Customer{/t} <i class="fal fa-level-up"></i> <i class="fal fa-user"></i> :</td>
                    <td class="Customer aright button" onclick="change_view('customers/{$customer->get('Customer Store Key')}/{$customer->id}')">{$customer->get('Name')}</td>
                </tr>


                <tr>
                    <td>{t}Contact since{/t}:</td>
                    <td class="aright">{$customer_client->get('Creation Date')}</td>
                </tr>



            </table>



        </div>
    </div>
    <div style="float: right;width: 310px;;margin-right: 20px">



    </div>
    <div style="clear: both"></div>
</div>





<div style="height: 10px;border-bottom:1px solid #ccc;padding: 0"></div>




<script>
    customer_email_width_hack($('#showcase_Customer_Client_Main_Plain_Email'));
</script>