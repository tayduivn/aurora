{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 7 March 2018 at 13:57:07 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3
-->
*}

{if isset($field.scope)}{assign "scope" $field.scope}{else}{assign "scope" ""}{/if}


<input class="Asset_Key" id="Asset_Key"  value="" type="hidden">
<span class="Asset_Select" onclick="change_asset(this)"></span>
<input class="Asset_Select_value" value="" ovalue="" placeholder="{t}Product{/t}/{t}Category{/t}" parent_key="{$store_key}"  parent="store" scope="assets_on_sale">
<div class="search_results_container">
    <table class="results" border="1">
        <tr class="hide search_result_template" field="" value="" formatted_value="" onclick="select_dropdown_asset(this)">
            <td class="code"></td>
            <td style="width:85%" class="label"></td>
        </tr>
    </table>
</div>


<script>
    {if $mode=='new'}

    change_asset_in_edit_selection()
    {/if}




    function delayed_on_change_assets_dropdown_select_field(object, timeout) {
        //var field = object.attr('id');
        //var field_element = $('#' + field);
        var new_value = $(object).val()


        /*
         key_scope = {
         type: 'dropdown_select',
         field: field_element.attr('field')
         };
         */

        window.clearTimeout(object.data("timeout"));

        object.data("timeout", setTimeout(function () {

            get_asset_dropdown_select(object, new_value)
        }, timeout));
    }

    function get_asset_dropdown_select(object, new_value) {

        var parent_key = $(object).attr('parent_key')
        var parent = $(object).attr('parent')
        var scope = $(object).attr('scope')


        var request = '/ar_find.php?tipo=find_objects&query=' + fixedEncodeURIComponent(new_value) + '&scope=' + scope + '&parent=' + parent + '&parent_key=' + parent_key + '&state=' + JSON.stringify(state)
        console.log(request)
        $.getJSON(request, function (data) {

            var results_container = $(object).closest('td').find('.search_results_container')


            if (data.number_results > 0) {
                results_container.removeClass('hide').addClass('show')
            } else {


                results_container.addClass('hide').removeClass('show')
                //  $('#' + field).val('')
                // on_changed_value(field, '')
            }


            $(" .result").remove();

            var first = true;

            for (var result_key in data.results) {


                var clone = results_container.find('.search_result_template').clone()
                //         clone.prop('id', field + '_result_' + result_key);


                clone.addClass('result').removeClass('hide search_result_template')
                clone.attr('value', data.results[result_key].value)
                clone.attr('formatted_value', data.results[result_key].formatted_value)


                //  console.log(data.results[result_key].metadata)
                clone.data('metadata', data.results[result_key].metadata)


                //    clone.attr('field', field)
                if (first) {
                    clone.addClass('selected')
                    first = false
                }

                clone.children(".code").html(data.results[result_key].code)

                clone.children(".label").html(data.results[result_key].description)


                results_container.find(".results").append(clone)
                //console.log(results_container.find(".results"))
                //   console.log($('#' + field + '_result_' + result_key).data('metadata'))
            }

        })


    }


    function change_asset(element){
        $(element).html('')
        $(element).closest('td').find('.Asset_Key').val('')

        $(element).closest('td').find('.search_results_container').removeClass('hide')
        $(element).closest('td').find('.Asset_Select_value').removeClass('hide').val('')

    }

    function select_dropdown_asset(element) {



        $(element).closest('td').find('.Asset_Select').html($(element).attr('formatted_value')).removeClass('hide')

        $(element).closest('td').find('.Asset_Key').val($(element).attr('value'))


        $(element).closest('td').find('.search_results_container').addClass('hide')
        $(element).closest('td').find('.Asset_Select_value').addClass('hide')

        $(element).closest('td').find('.search_results_container').find('tr.result').remove()


        on_change_asset_list(element)


        {if $scope=='Mailshot'}
        $('#Scope_Type_field').removeClass('hide').addClass('activated')

        var form_validation = get_form_validation_state()
        process_form_validation(form_validation)




        {/if}

    }

    $("#Asset_container").on("input.Asset_Select_value propertychange", function (evt) {

        var delay = 100;
        if (window.event && event.type == "propertychange" && event.propertyName != "value") return;
        delayed_on_change_assets_dropdown_select_field($(evt.target), delay)
    });


    function change_asset_in_edit_selection() {


        on_change_asset_list()

    }

    function save_this_asset_lists() {
        save_field($('#fields').attr('object'), $('#fields').attr('key'), 'Product_Assets')
    }


    $(document).on('input propertychange', '.asset_list_value', function (evt) {
        on_change_asset_list()
    });

    function on_change_asset_list(element) {




    }




    function post_save_product_assets(data) {
        $('assets_list_items').html(data.update_metadata.assets_list_items)
    }

</script>