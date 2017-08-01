﻿{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 9 July 2017 at 18:52:48 GMT+8, Cyberjaya, Malaysia
 Copyright (c) 2017, Inikoo

 Version 3
-->
*}


{include file="theme_1/_head.theme_1.EcomB2B.tpl"}

{if isset($labels.validation_required) and $labels.validation_required!=''}{assign "validation_required" $labels.validation_required }{else}{assign "validation_required"  $labels_fallback.validation_required  }{/if}
{if isset($labels.validation_same_password) and $labels.validation_same_password!=''}{assign "validation_same_password" $labels.validation_same_password }{else}{assign "validation_same_password"  $labels_fallback.validation_same_password  }{/if}
{if isset($labels.validation_minlength_password) and $labels.validation_minlength_password!=''}{assign "validation_minlength_password" $labels.validation_minlength_password }{else}{assign "validation_minlength_password"  $labels_fallback.validation_minlength_password  }{/if}
{if isset($labels.validation_password_missing) and $labels.validation_password_missing!=''}{assign "validation_password_missing" $labels.validation_password_missing }{else}{assign "validation_password_missing"  $labels_fallback.validation_password_missing  }{/if}



<body xmlns="http://www.w3.org/1999/html">


<div class="wrapper_boxed">

    <div class="site_wrapper">

        {include file="theme_1/header.EcomB2B.tpl"}

        <div class="content_fullwidth less2">
            <div class="container">


                <div class="password_reset_form" >

                    


                    <form action="" id="password_reset_form" class="sky-form {if $form_error!=''}submited{/if}">

                        <header id="_title" >{$content._title}</header>


                        <fieldset>

                            <section>
                                <label id="_password" class="input " editor="password_input_editor" ">
                                    <i class="icon-append icon-lock" style="cursor:pointer"></i>
                                    <input id="password"   type="password" name="password" placeholder="{$content._password_placeholder}">
                                    <b class="tooltip tooltip-bottom-right">{$content._password_tooltip}</b>
                                </label>
                            </section>

                            <section>
                                <label id="_password_confirm" class="input " editor="password_confirm_input_editor" >
                                    <i class="icon-append icon-lock" style="cursor:pointer"></i>
                                    <input   type="password" name="password_confirm" placeholder="{$content._password_confirm_placeholder}">
                                    <b class="tooltip tooltip-bottom-right">{$content._password_confirm_tooltip}</b>
                                </label>
                            </section>

                        </fieldset>


                        <footer>


                            <button type="submit" name="submit" class="button" id="_submit_label" >{$content._submit_label}</button>


                        </footer>


                        <div class="message {if $form_error!=''}error{/if}">


                            <i class="fa {if $form_error!=''}fa-exclamation{else}fa-check{/if}"></i>


                            <span id="password_reset_success_msg"  class=" {if $form_error!=''}hide{/if}">{$content.password_reset_success_msg}</span>
                            <span id="password_reset_expired_token_error_msg"   class=" {if $form_error!='selector_expired'}hide{/if}" >{$content.password_reset_expired_token_error_msg}</span>
                            <span id="password_reset_error_msg"  class=" {if !($form_error=='wrong_hash' or  $form_error=='selector_not_found')}hide{/if}" >{$content.password_reset_error_msg}</span>
                            <span   class=" {if $form_error!='logged_in' }hide{/if}" >{$content.password_reset_logged_in_error_msg}</span>

                            <br>
                            <a href="/login.sys?fp" class=" {if ($form_error=='' or $form_error=='logged_in')   }hide{/if}">{$content.password_reset_go_back}</a>
                            <a href='/' class=" {if !($form_error=='' or $form_error=='logged_in')   }hide{/if}">{$content.password_reset_go_home}</a>


                        </div>

                    </form>

                </div>
                
                


            </div>
        </div>


        <div class="clearfix marb12"></div>

        {include file="theme_1/footer.EcomB2B.tpl"}


    </div>

</div>
<script>








    $("form").on('submit', function (e) {

        e.preventDefault();
        e.returnValue = false;

    });


    $("#password_reset_form").validate({

            submitHandler: function(form)
            {

                var ajaxData = new FormData();

                ajaxData.append("tipo", 'update_password')
                ajaxData.append("pwd", sha256_digest($('#password').val()))


                $.ajax({
                    url: "/ar_web_profile.php", type: 'POST', data: ajaxData, dataType: 'json', cache: false, contentType: false, processData: false,
                    complete: function () {
                    }, success: function (data) {



                        if (data.state == '200') {
                            $('#password_reset_form').addClass('submited')

                        } else if (data.state == '400') {
                            swal("{t}Error{/t}!", data.msg, "error")
                        }



                    }, error: function () {

                    }
                });


            },

            // Rules for form validation
            rules:
                {

                   
                    password:
                        {
                            required: true,
                            minlength: 8
                        },
                    password_confirm:
                        {
                            required: true,
                            minlength: 8,
                            equalTo: "#password"
                        }

                },

            // Messages for form validation
            messages:
                {

                    password:
                        {
                            required: '{$validation_required|escape}',
                            minlength: '{$validation_minlength_password|escape}',

                        },
                    password_confirm:
                        {
                            required: '{$validation_required|escape}',
                            equalTo: '{$validation_same_password|escape}',
                            minlength: '{$validation_minlength_password|escape}',
                        }
                },

            // Do not change code below
            errorPlacement: function(error, element)
            {
                error.insertAfter(element.parent());
            }
        });






</script>

</body>

</html>

