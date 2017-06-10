﻿{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 16 May 2017 at 18:46:27 GMT-5, CsMx, Mexico
 Copyright (c) 2016, Inikoo

 Version 3
-->
*}


{include file="theme_1/_head.theme_1.tpl"}


<body>

<div class="wrapper_boxed">

    <div class="site_wrapper">

        <div class="content_fullwidth less2">
            <div class="container">

                <div class="error_pagenotfound">

                    <strong id="_strong_title" contenteditable="true">{$content._strong_title}</strong>
                    <br/>
                    <b id="_title"  contenteditable="true">{$content._title}</b>

                    <em id="_text"  contenteditable="true">{$content._text}</em>

                    <p id="_home_guide"  contenteditable="true">{$content._home_guide}</p>

                    <div class="clearfix margin_top3"></div>

                    <a href="index.php" class="but_medium1"><span class="fa fa-home fa-lg"></span>&nbsp; <span id="_home_label"  contenteditable="true">{$content._home_label}</span></a>

                </div><!-- end error page notfound -->

            </div>






        <div class="clearfix marb12"></div>


    </div>
</div>


<script>

    $('[contenteditable=true]').on('input paste', function (event) {
        $('#save_button', window.parent.document).addClass('save button changed valid')
    });


    function save() {

        if (!$('#save_button', window.parent.document).hasClass('save')) {
            return;
        }

        $('#save_button', window.parent.document).find('i').addClass('fa-spinner fa-spin')


        content_data = {}

            $('[contenteditable=true]').each(function (i, obj) {


                content_data[$(obj).attr('id')] = $(obj).html()
            })


        var request = '/ar_edit_website.php?tipo=save_webpage_content&key={$webpage->id}&content_data=' + encodeURIComponent(Base64.encode(JSON.stringify(content_data)));


        console.log(request)


        $.getJSON(request, function (data) {


            $('#save_button', window.parent.document).removeClass('save').find('i').removeClass('fa-spinner fa-spin')

        })


    }

</script>

</body>

</html>

