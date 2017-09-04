{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 30 August 2017 at 17:52:41 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3
-->
*}

<div class="outter-elements">
    <div class="decoration decoration-margins"></div>


<div class="footer footer-dark">
    <a href="index.html" class="footer-logo"></a>
    <p class="footer-text"   >
        <span onclick="location.href='tel:{$store->get('Telephone')}';"><i class="fa fa-phone padding_right_10" aria-hidden="true"></i> {$store->get('Telephone')}</span><br>
        <span onclick="location.href='mailto:{$store->get('Email')}';"><i class="fa fa-envelope-o padding_right_10" aria-hidden="true"></i> {$store->get('Email')}</span>


    </p>






    <div class="footer-socials">

        <a href="#" class="show-share-bottom"><i class="ion-android-share-alt"></i></a>
    </div>
    <p class="copyright-text">&copy; Copyright <span class="copyright-year"></span>. {t}All rights reserved{/t}</p>
</div>

</div>