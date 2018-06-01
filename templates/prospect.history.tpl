<style>
    .choose_dialog {
        padding: 10px 10px 17px 15px
    }

    .choose_dialog span {
        border: 1px solid #ccc;
        padding: 5px;
        margin-right: 10px;
    }
</style>


<span id="prospect_history_data" class="hide" data-key="{$prospect->id}"></span>

<div id="send_email_dialog" class="hide choose_dialog textarea_dialog">
    <span class="button unselectable log_email" onclick="open_log_dialog(this)" title="{t}Log email send with another application{/t}"><i class="fa fa-sticky-note"></i> {t}Log email{/t}</span>
    <span class="button unselectable" onclick="change_view('/prospects/{$prospect->get('Store Key')}/{$prospect->id}/email/new')"  title="{t}Write a custom made invitation email{/t}"><i class="fa fa-pen-alt"></i> {t}Personalized invitation{/t}</span>

    <span class="button unselectable" onclick="send_invitation(this)"  title="{t}Send invitation email from template{/t}"><i class="fa fa-paper-plane"></i> {t}Send invitation{/t}</span>

    <i class="fa fa-window-close" style="padding-top: 0px;position: relative;top:-5px" onclick="close_send_email_dialog()"></i>

</div>


<div id="log_email_dialog" class="hide  textarea_dialog " data-field="Log_Email">
    <div>
        <span>{t}Log email send with another application{/t}</span>
        <i  style="float: right;position: relative;top:-15px" class="fa fa-window-close fw " onclick="$(this).closest('.textarea_dialog').addClass('hide')"></i>
    </div>

    <textarea class="note" style="clear: both" placeholder="{t}Notes{/t} / {t}email content{/t}"></textarea><br>
    <i class="fa fa-cloud save  fa-fw" style="padding-top: 0px;margin-top: 10px" onclick="save_log(this)"></i>
</div>


<div id="log_call_dialog" class="hide  textarea_dialog " data-field="Log_Call">
    <div>
        <span>{t}Log phone invitation{/t}</span>
        <i  style="float: right;position: relative;top:-15px" class="fa fa-window-close fw " onclick="$(this).closest('.textarea_dialog').addClass('hide')"></i>
    </div>

    <textarea class="note" style="clear: both" placeholder="{t}Notes{/t} / {t}email content{/t}"></textarea><br>
    <i class="fa fa-cloud save  fa-fw" style="padding-top: 0px;margin-top: 10px" onclick="save_log(this)"></i>
</div>

<div id="log_post_dialog" class="hide  textarea_dialog " data-field="Log_Post">
    <div>
        <span>{t}Log mail send by post{/t}</span>
        <i  style="float: right;position: relative;top:-15px" class="fa fa-window-close fw " onclick="$(this).closest('.textarea_dialog').addClass('hide')"></i>
    </div>

    <textarea class="note" style="clear: both" placeholder="{t}Notes{/t} / {t}email content{/t}"></textarea><br>
    <i class="fa fa-cloud save  fa-fw" style="padding-top: 0px;margin-top: 10px" onclick="save_log(this)"></i>
</div>


<script>

    $("#show_send_email_dialog").on("click", function (evt) {

        show_send_email_dialog(this)
    });


    $('.open_log_dialog').on("click", function (evt) {
        open_log_dialog(this)
    });


    function show_send_email_dialog(element) {

        if ($('#send_email_dialog').hasClass('hide')) {


            $('#send_email_dialog').removeClass('hide')


            $('#history_note_value').focus()


            $('#note_type').addClass('fa-check-square').removeClass('fa-square')


            var position = $(element).position();


            $('#send_email_dialog').css({
                'left': position.left - $('#send_email_dialog').width() - $(element).width(), 'top': position.top
            })
            $('#send_email_dialog').attr('history_key', '')


        } else {
            close_send_email_dialog()
        }


    }


    function open_log_dialog(element) {




        if($(element).hasClass('log_call')){
            $('.textarea_dialog').addClass('hide')
            var  dialog=$('#log_call_dialog')
            dialog.removeClass('hide')
            var position = $(element).position();


            dialog.css({
                'left': position.left - dialog.width()-$(element).width(), 'top': position.top
            })



        }else if($(element).hasClass('log_post')){
            $('.textarea_dialog').addClass('hide')
            var  dialog=$('#log_post_dialog')
            dialog.removeClass('hide')
            var position = $(element).position();


            dialog.css({
                'left': position.left - dialog.width()-$(element).width(), 'top': position.top
            })



        }else if($(element).hasClass('log_email')){
            var  dialog=$('#log_email_dialog')
            $('.textarea_dialog').addClass('hide')
            $('#send_email_dialog').removeClass('hide')

            dialog.removeClass('hide')

            var position = $('#send_email_dialog').position();


            dialog.css({
                'left': position.left + $('#send_email_dialog').width() -dialog.width(), 'top': position.top
            })

            $('#send_email_dialog').addClass('hide')


        }





        dialog.find('.note').focus()




    }


    function close_send_email_dialog() {
        $('#send_email_dialog').addClass('hide')
    }


    function send_invitation(element){

        dialog_element = $(element).closest('.textarea_dialog')

        var save_button = $(element).find('i')

        if (save_button.hasClass('wait')) {
            return;
        }


        save_button.removeClass('fa-paper-plane valid changed').addClass('fa-spin fa-spinner wait')


        var ajaxData = new FormData();


        ajaxData.append("tipo", 'edit_field')
        ajaxData.append("object", 'Prospect')
        ajaxData.append("key", $('#prospect_history_data').data('key'))
        ajaxData.append("field", 'Send Invitation')
        ajaxData.append("value", '')


        $.ajax({
            url: "/ar_edit.php", type: 'POST', data: ajaxData, dataType: 'json', cache: false, contentType: false, processData: false,


            complete: function () {

            }, success: function (data) {


                if (data.state == '200') {




                    dialog_element.find('.note').val('')
                    save_button.removeClass('fa-spin fa-spinner wait').addClass('fa-cloud')
                    dialog_element.addClass('hide')


                    for (var key in data.update_metadata.class_html) {
                        $('.' + key).html(data.update_metadata.class_html[key])
                    }



                    for (var key in data.update_metadata.hide) {


                        $('.' + data.update_metadata.hide[key]).addClass('hide')
                    }

                    for (var key in data.update_metadata.show) {

                        $('.' + data.update_metadata.show[key]).removeClass('hide')
                    }




                    rows.fetch({
                        reset: true
                    });
                    get_elements_numbers(rows.tab, rows.parameters)


                } else if (data.state == '400') {

                    console.log('data')



                    var el = document.createElement("div")
                    el.innerHTML =data.msg


                    swal({
                            title: "{t}Error{/t}!",

                            icon: "error",
                            content :el


                        }
                        )

                    save_button.removeClass('fa-spin fa-spinner wait').addClass('fa-cloud valid changed')

                }


            }, error: function () {

            }
        });


    }

    function save_log(element) {

        dialog_element = $(element).closest('.textarea_dialog')

        var save_button = dialog_element.find('.save')

        if (save_button.hasClass('wait')) {
            return;
        }

        console.log(dialog_element)


        save_button.removeClass('fa-cloud valid changed').addClass('fa-spin fa-spinner wait')


        var ajaxData = new FormData();


        ajaxData.append("tipo", 'edit_field')
        ajaxData.append("object", 'Prospect')
        ajaxData.append("key", $('#prospect_history_data').data('key'))
        ajaxData.append("field", dialog_element.data('field'))
        ajaxData.append("value", dialog_element.find('.note').val())


        $.ajax({
            url: "/ar_edit.php", type: 'POST', data: ajaxData, dataType: 'json', cache: false, contentType: false, processData: false,


            complete: function () {

            }, success: function (data) {


                if (data.state == '200') {




                    dialog_element.find('.note').val('')
                    save_button.removeClass('fa-spin fa-spinner wait').addClass('fa-cloud')
                    dialog_element.addClass('hide')


                    for (var key in data.update_metadata.class_html) {
                        $('.' + key).html(data.update_metadata.class_html[key])
                    }



                    for (var key in data.update_metadata.hide) {


                        $('.' + data.update_metadata.hide[key]).addClass('hide')
                    }

                    for (var key in data.update_metadata.show) {

                        $('.' + data.update_metadata.show[key]).removeClass('hide')
                    }


                    console.log(state)

                    rows.fetch({
                        reset: true
                    });
                    get_elements_numbers(rows.tab, rows.parameters)


                } else if (data.state == '400') {
                    alert(data.msg)
                    save_button.removeClass('fa-spin fa-spinner wait').addClass('fa-cloud valid changed')

                }


            }, error: function () {

            }
        });

    }




    $('.note').on('input propertychange', function () {


        var save_button = $(this).closest('.textarea_dialog').find('.save')


        if ($(this).val() == '') {
            save_button.removeClass('changed valid')
        } else {
            save_button.addClass('changed valid')

        }

    });

</script>