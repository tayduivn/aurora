{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 5 September 2016 at 13:17:09 GMT+8, Kuta, Bali, Indonesia
 Copyright (c) 2016, Inikoo

 Version 3
-->
*}


<div class="table_info" style="margin-top:2px">

    <div class=" square_button right " title="{t}Exit edit{/t}">
        <i onclick="exit_edit_table()" class="fa fa-sign-out fa-flip-horizontal fa-fw"></i>

    </div>
    <div id="show_export_dialog" class=" square_button right " title="{t}Export edit template{/t}">
        <i onclick="open_export_dialogs()" class="fa fa-cloud-download fa-fw"></i>

    </div>
    <div id="export_dialog_container" style="position:relative;float:right" class="  ">
        <span class="hide" id="export_queued_msg"><i class="fa background fa-spinner fa-spin"></i> {t}Queued{/t}</span>
        <div id="export_dialog" class="export_dialog hide">
            <table border=0 style="width:100%">
                <tr class="no_border">
                    <td class="export_progress_bar_container">
                        <a href="" id="download_excel" download hidden></a>
                        <span class="hide export_progress_bar_bg" id="export_progress_bar_bg_excel"></span>
                        <div class="hide export_progress_bar" id="export_progress_bar_excel"></div>
                        <div class="export_download hide" id="export_download_excel"> {t}Download{/t}</div>
                    </td>
                    <td class="width_20">
                        <i id="stop_export_table_excel" stop=0 onclick="stop_export('excel')"
                           class="fa button fa-hand-stop-o error hide" title="{t}Stop{/t}"></i>
                    </td>
                    <td id="export_table_excel" class="link" onclick="export_table('excel')"><i
                                class="fa fa-file-excel" title="Excel"></i>Excel
                    </td>
                </tr>
                <tr>
                    <td class="export_progress_bar_container"><a href="" id="download_csv" download hidden></a>
                        <span class="hide export_progress_bar_bg" id="export_progress_bar_bg_csv"></span>
                        <div class="hide export_progress_bar" id="export_progress_bar_csv"></div>
                        <div class="export_download hide " id="export_download_csv"> {t}Download{/t}</div>

                    </td>
                    <td class="width_20"><i id="stop_export_table_csv" onclick="stop_export('csv')"
                                            class="fa button fa-hand-stop-o error hide" title="{t}Stop{/t}"></i></td>
                    <td id="export_table_csv" class="link" onclick="export_table('csv')"><i class="fa fa-table"
                                                                                            title="{t}Comma Separated Value{/t}"></i>CSV
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class=""><i onclick="open_export_config()" class="fa fa-cogs button hide"></i></td>
                    <td>
                        <div onclick="hide_export_dialog()" class="button disabled"><i class="fa fa-times"
                                                                                       title="{t}Close dialog{/t}"></i>{t}Close{/t}
                        </div>
                    </td>
                </tr>
            </table>

        </div>
        <div id="export_dialog_config" class="export_dialog hide">
            {if isset($edit_fields)}
                <table>
                    <tr class="small_row ">
                        <td></td>
                        <td style="width_20" class="field_export ">
                            <i id="toggle_all_export_fields" onclick="toggle_all_export_fields(this)"
                               class="button fa-fw fa fa-square"></i>
                        </td>
                    </tr>
                    <tbody id="export_fields">
                    {foreach from=$edit_fields item=export_field key=_key}
                        <tr class="small_row">
                            <td>{$export_field.label}</td>
                            <td style="width_20" class="field_export">
                                <i id="field_export_{$_key}" onclick="toggle_export_field(this)" key="{$_key}"
                                   class="button fa-fw object_field far {if $export_field.show_for_new }fa-check-square{else}fa-square{/if}"></i>
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            {/if}
        </div>
    </div>


    <div id="table_buttons">
        {if (isset($table_buttons) and count(table_buttons)>0)  }

        {foreach from=$table_buttons item=button }


        {if isset($button.inline_new_object)}
        {include file="inline_new_object.tpl" data=$button.inline_new_object trigger={$button.id}}
        {/if}


        <div {if isset($button.id) and $button.id }id="{$button.id}"{/if} {if isset($button.attr)} {foreach from=$button.attr key=attr_key item=attr_value }{$attr_key}
        ="{$attr_value}" {/foreach}{/if} class="
        table_button square_button right {if isset($button.class)}{$button.class}{/if}
        " {if isset($button.reference) and $button.reference!=''}onclick="change_view('{$button.reference}')
        "{else if isset($button.change_tab) and $button.change_tab!=''}onclick=
        "change_view(state.request + '&tab={$button.change_tab}')"{/if} {if isset($button.title)}title="{$button.title}
        "{/if}>
        <i {if isset($button.id) and $button.id }id="icon_{$button.id}"{/if} class=" fa fa-{$button.icon} fa-fw"></i>
    </div>

    {if isset($button.add_item)}
        {include file="add_item.tpl" data=$button.add_item trigger={$button.id}}
    {/if}


    {if isset($button.inline_new_object)}
        <span id="inline_new_object_msg" class="invalid"></span>
    {else if isset($button.add_item)}
        <span id="inline_add_item_msg" class="invalid"></span>
    {/if}
    {/foreach}


    {/if}


    <div class="square_button right move_left">
        <form method="post" action="/ar_edit.php" enctype="multipart/form-data" novalidate>

            <input type="file" name="image_upload" id="file_upload" class="input_file" multiple/>
            <label for="file_upload"><i id="upload_icon"
                                        class="fa fa-cloud-upload fa-fw button very_discreet"></i></label>
        </form>
    </div>
    <span id="file_upload_msg" style="float:right;padding-right:10px"></span>
    <script>
        var droppedFiles = false;

        $('#file_upload').on('change', function (e) {
            upload_file()
        });

        function upload_file() {
            var ajaxData = new FormData();

            //var ajaxData = new FormData( );
            if (droppedFiles) {
                $.each(droppedFiles, function (i, file) {
                    ajaxData.append('files', file);
                });
            }


            $.each($('#file_upload').prop("files"), function (i, file) {
                ajaxData.append("files[" + i + "]", file);

            });


            ajaxData.append("tipo", 'edit_objects')
            ajaxData.append("parent", '{$parent}')
            ajaxData.append("parent_key", '{$parent_key}')
            ajaxData.append("objects", '{$objects}')

            $.ajax({
                url: "/ar_upload.php",
                type: 'POST',
                data: ajaxData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,


                complete: function () {

                },
                success: function (data) {


                    if (data.state == '200') {


                        change_view(state.request + '/upload/' + data.upload_key);


                    } else if (data.state == '400') {
                        $('#file_upload_msg').html(data.msg).addClass('error')
                    }


                },
                error: function () {

                }
            });
        }


    </script>


</div>
<span id="rtext" class="padding_left_10">{$title}</span>
</div>


<script>


    show_export_dialog();


    function exit_edit_table() {

        $('#object_showcase').removeClass('hide');
        $('#tabs').removeClass('hide');

        change_view(state.request + '&tab=' + state.tab.replace(/\_edit$/i, ""), {
            reload_showcase: 1
        })
    }

    function open_export_dialogs() {


        if ($('#export_dialog').hasClass('hide')) {
            show_export_dialog();
        } else {
            hide_export_dialog()
        }


    }




    function export_table(type) {


        $('#export_progress_bar_bg_' + type).removeClass('hide').html('&nbsp;' + $('#export_queued_msg').html())

        $('#export_table_excel').removeClass('link').addClass('disabled')
        $('#export_table_csv').removeClass('link').addClass('disabled')
        $('.field_export').removeClass('button').addClass('disabled')
        $('#stop_export_table_' + type).removeClass('hide')
        $('#stop_export_table_' + type).attr('stop', 0);

        var fields = []
        $('#export_dialog_config .field_export i.object_field').each(function (index, obj) {
            if ($(obj).hasClass('fa-check-square')) fields.push($(obj).attr('key'))
        });

        var request = "/ar_export_edit_template.php?parent={$export_parent}&parent_key={$export_parent_key}&parent_code={$export_parent_code}&objects={$objects}&fields=" + JSON.stringify(fields) + '&type=' + type + '&metadata=' + JSON.stringify({
                    store_key:{$store_key}})

        console.log(request)
        $.getJSON(request, function (data) {
            if (data.state == 200) {
                get_export_process_bar(data.fork_key, data.tipo, type,'');
            }
        })

    }


</script>



