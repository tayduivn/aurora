<div class="sticky_notes" style="border-top: 1px solid #ccc">
    {include file="sticky_note.tpl" value=$supplier->get('Sticky Note') object="Supplier" key="{$supplier->id}" field="Supplier_Sticky_Note"  }
</div>

<div class="subject_profile supplier_subject_showcase " style="padding-top:10px">

    <div class="data_container block picture" stye="float:left">

        <div class="data_container">
            {assign "image_key" $supplier->get_main_image_key()}
            <div id="main_image" style="width: 180px;height: 180px;" class="wraptocenter main_image {if $image_key==''}hide{/if}">
                <img style="max-width: 170px;max-height: 170px;" src="/{if $image_key}image.php?id={$image_key}&amp;s=170x170{else}art/nopic.png{/if}"> </span>
            </div>
            {include file='upload_main_image.tpl' object='Supplier' parent_object_scope="Profile"  key=$supplier->id class="{if $image_key!=''}hide{/if}"}
        </div>

    </div>

    <div id="contact_data">
        <div style="width:100%">

            <ul class="tags">
                {foreach from=$supplier->get_category_data() item=item key=key}
                    <li><span class="button" onClick="change_view('category/{$item.category_key}')">{$item.value}</span>
                    </li>
                {/foreach}
            </ul>


        </div>




        <div class="data_container" style="clear:both;max-width: 350px">


            <div class="categories hide">

                {foreach from=$supplier->get_category_data() item=item key=key}
                    <tr>
                        <td>{$item.root_label}:</td>
                        <td>{$item.value}</td>
                    </tr>
                {/foreach}

            </div>




            <div class="data_field">
                <h1 class="Supplier_Name">
                    {$supplier->get('Name')}
                </h1>
                <span class="italic discreet Supplier_Nickname">{$supplier->get('Nickname')}</span>
            </div>
            <div class="data_field {if $supplier->get('Supplier Main Contact Name')==''}hide{/if}">
                <i title="{t}Contact name{/t}" class="fa fa-user"></i> <span
                        class="Supplier_Main_Contact_Name">{$supplier->get('Main Contact Name')}</span>
            </div>
            <div class="data_container" style=";margin-top:10px">
                <div style="min-height:80px;float:left;width:28px;">
                    <i class="fa fa-map-marker-alt"></i>
                </div>
                <div style="float:left;width:272px" class="Supplier_Contact_Address">
                    {$supplier->get('Contact Address')}
                </div>
            </div>
        </div>
        <div class="data_container" style="max-width: 270px;">
            <div id="Supplier_Main_Plain_Email_display"
                 class="data_field   {if !$supplier->get('Supplier Main Plain Email')}hide{/if}">
                <i class="fa fa-fw fa-at"></i> <span
                        id="Supplier_Other_Email_mailto">{if $supplier->get('Supplier Main Plain Email')}{mailto address=$supplier->get('Main Plain Email')}{/if}</span>
            </div>
            {foreach $supplier->get_other_emails_data() key=other_email_key item=other_email}
                <div id="Supplier_Other_Email_{$other_email_key}_display" class="data_field ">
                    <i class="fa fa-fw fa-at discreet"></i> <span
                            id="Supplier_Other_Email_{$other_email_key}_mailto">{mailto address=$other_email.email}</span>
                </div>
            {/foreach}
            <div id="Supplier_Other_Email_display" class="data_field hide">
                <i class="fa fa-fw fa-at discreet"></i> <span class="Supplier_Other_Email_mailto"></span>
            </div>
            <span id="display_telephones"></span> {if $supplier->get('Supplier Preferred Contact Number')=='Mobile'}
                <div id="Supplier_Main_Plain_Mobile_display" title="{t}Mobile{/t}"
                     class="data_field {if !$supplier->get('Supplier Main Plain Mobile')}hide{/if}">
                    <i class="far fa-fw fa-mobile"></i> <span
                            class="Supplier_Main_Plain_Mobile">{$supplier->get('Main Plain Mobile')}</span>
                </div>
                <div id="Supplier_Main_Plain_Telephone_display"
                     class="data_field {if !$supplier->get('Supplier Main Plain Telephone')}hide{/if}">
                    <i class="fa fa-fw fa-phone"></i> <span
                            class="Supplier_Main_Plain_Telephone">{$supplier->get('Main Plain Telephone')}</span>
                </div>
            {else}
                <div id="Supplier_Main_Plain_Telephone_display"
                     class="data_field {if !$supplier->get('Supplier Main Plain Telephone')}hide{/if}">
                    <i title="Telephone" class="fa fa-fw fa-phone"></i> <span
                            class="Supplier_Main_Plain_Telephone">{$supplier->get('Main Plain Telephone')}</span>
                </div>
                <div id="Supplier_Main_Plain_Mobile_display"
                     class="data_field {if !$supplier->get('Supplier Main Plain Mobile')}hide{/if}">
                    <i title="Mobile" class="fa fa-fw fa-mobile"></i> <span
                            class="Supplier_Main_Plain_Mobile">{$supplier->get('Main Plain Mobile')}</span>
                </div>
            {/if}


            <div id="Supplier_QQ_display"
                 class="data_field {if !$supplier->get('Supplier QQ')}hide{/if}">
                <i title="QQ" class="fab fa-fw fa-qq"></i> <span class="Supplier_QQ">{$supplier->get('QQ')}</span>
            </div>

            <div id="Supplier_Main_Plain_FAX_display"
                 class="data_field {if !$supplier->get('Supplier Main Plain FAX')}hide{/if}">
                <i title="Fax" class="fa fa-fw fa-fax"></i> <span>{$supplier->get('Main Plain FAX')}</span>
            </div>
            {foreach $supplier->get_other_telephones_data() key=other_telephone_key item=other_telephone}
                <div id="Supplier_Other_Telephone_{$other_telephone_key}_display" class="data_field ">
                    <i class="fa fa-fw fa-phone discreet"></i> <span>{$other_telephone.formatted_telephone}</span>
                </div>
            {/foreach}
            <div id="Supplier_Other_Telephone_display" class="data_field hide">
                <i class="fa fa-fw fa-phone discreet"></i> <span></span>
            </div>
        </div>
        <div style="clear:both">
        </div>

        <div style="clear:both">
        </div>
    </div>
    <div id="info">
        <div id="overviews">
            <table class="overview">
                <tr>
                    <td>{t}Contact since{/t}:</td>
                    <td>{$supplier->get('Valid From')}</td>
                </tr>
                <tr class="Supplier_Valid_To {if $supplier->get('Supplier Type')!='Archived'}hide{/if}">
                    <td><i class="fa fa-archive" aria-hidden="true"></i> {t}Archived{/t}:</td>
                    <td>{$supplier->get('Valid To')}</td>
                </tr>
                <tr>
                    <td>{t}Currency{/t}:</td>
                    <td class="Supplier_Default_Currency_Code"
                        title="{$supplier->get('Default Currency')}">{$supplier->get('Default Currency Code')}</td>
                </tr>
                <tr>
                    <td>{t}Products origin{/t}:</td>
                    <td class="Supplier_Products_Origin_Country_Code">{$supplier->get('Products Origin Country Code')}</td>
                </tr>
                <tr>
                    <td>{t}Delivery time{/t}:</td>
                    <td class="Delivery_Time">{$supplier->get('Delivery Time')}</td>
                </tr>

            </table>

        </div>
    </div>
    <div style="clear:both">
    </div>
</div>

<script>
    function email_width_hack() {
        var email_length = $('#showcase_Supplier_Main_Plain_Email').text().length

        if (email_length > 30) {
            $('#showcase_Supplier_Main_Plain_Email').css("font-size", "90%");
        }
    }

    email_width_hack();

</script>