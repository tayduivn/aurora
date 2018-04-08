﻿{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 12 March 2018 at 15:37:23 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3
-->
*}<!DOCTYPE HTML>
<html lang="en">
<head>
    {if $smarty.server.SERVER_NAME!='ecom.bali' and  $client_tag_google_manager_id!=''}
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){ w[l]=w[l]||[];w[l].push({ 'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{$client_tag_google_manager_id}');</script>
        <!-- End Google Tag Manager -->
    {/if}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <title>{$webpage->get('Webpage Browser Title')}</title>
    <meta name="description" content="{$webpage->get('Webpage Meta Description')}"/>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.2.0/css/swiper.min.css">
    <link href="/css/tablet.min.css?v6" rel="stylesheet" type="text/css">



    <script src="/theme_1/tablet/jquery.js"></script>
    <script src="/theme_1/tablet/plugins.js?v2"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.2.0/js/swiper.min.js"></script>
    <script src="/theme_1/tablet/custom.js?V2"></script>
    <script src="/js/sweetalert.min.js"></script>

    <script src="/theme_1/local/jquery-ui.js"></script>
    <script src="/js/sha256.js"></script>

    <script src="/theme_1/sky_forms/js/jquery.form.min.js"></script>
    <script src="/theme_1/sky_forms/js/jquery.validate.min.js"></script>
    <script src="/theme_1/sky_forms/js/additional-methods.min.js"></script>

    <script src="/js/aurora.js?20180319v3"></script>
    <script src="/js/validation.js"></script>

    <script src="/js/ordering.touch.js?20180115"></script>
    <script src="/js/braintree.js"></script>


</head>




