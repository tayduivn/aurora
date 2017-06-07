{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 28 March 2017 at 17:45:30 GMT+8, Cyberjaya, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3
-->
*}
<style>


    input.input_file {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }
    .save{
        color:#0EBFE9;
    }
    .italic {
        font-style: italic
    }

    .control_panel{
        color:#444
    }

    .button {
        cursor:pointer

    }
    .editables_block {
        border: 1px solid transparent;
    }
        .editables_block:hover {
        border: 1px solid yellow;
    }

    .input_container{
        position:absolute;top:60px;left:10px;z-index: 100;border:1px solid #ccc;background-color: white;padding:10px 10px 10px 5px

    }


    .input_container input{
        width:400px
    }

    .editing{
        color:yellow;
    }

    .add_link, .add_item{
        opacity:.1
    }

    .qlinks:hover .add_link,.faddress:hover .add_item {
        opacity:.5;
        -webkit-transition-duration: 500ms;
        transition-duration:500ms;
    }

    .drag_mode, .block_mode{
        opacity: .7;
    }

    .drag_mode.on, .block_mode.on{
        opacity: 1;
    }

    #item_types div{
        padding:5px 5px;cursor:pointer;text-align: center;float:left;width:30px
    }

    #item_types div:hover i{
        color:#000
    }


    .block_type{
        padding:10px 20px

    }

    .block_type div{

        opacity: .5;
        cursor:pointer;

    }

    .block_type div:hover{

        opacity: 1;


    }


    .block_type div.selected{

        opacity: 1;
        color:#333

    }


    #social_links_control_center div{

        margin-bottom:2px;


    }

    .discreet_links_control_panel input{
        width:250px

    }


    /* real editinf css */


    #logo:hover{
        border:1px solid yellow;

    }


    /* real scructural css */

    #logo {

        background:url( web_image.php?id={$header_data['logo_image_key']}) no-repeat left top;
    }

</style>



<div id="input_container_link" class="input_container link_url hide  " style="">
<input  value="" placeholder="{t}http://... or webpage code{/t}">
</div>




<div id="copyright_bundle_control_center" class="input_container link_url  hide " style="">

    <div style="margin-bottom:5px">  <i  onClick="update_copyright_bundle_from_dialog()" style="position:relative;top:-5px" class="button fa fa-fw fa-window-close" aria-hidden="true"></i>  </div>


    <div>  <span>{t}Copyright owner{/t}</span>   <input  id="copyright_bundle_control_center_owner" value="" placeholder="{t}name{/t}"></div>

    <div style="border-bottom:1px solid #ccc;margin-bottom:5px">
        {t}Links{/t}
    </div>

<div class="discreet_links_control_panel">
    <div class="copyright_link">  <input class="label" value="" placeholder="{t}Link label{/t}">   <input  class="url"  value="" placeholder="{t}https://... or page code{/t}"></div>
    <div  class="copyright_link">  <input class="label" value="" placeholder="{t}Link label{/t}">   <input  class="url" value="" placeholder="{t}https://... or page code{/t}"></div>
    <div  class="copyright_link">  <input class="label" value="" placeholder="{t}Link label{/t}">   <input  class="url" value="" placeholder="{t}https://... or page code{/t}"></div>
    <div  class="copyright_link">  <input class="label" value="" placeholder="{t}Link label{/t}">   <input  class="url" value="" placeholder="{t}https://... or page code{/t}"></div>
    <div  class="copyright_link">  <input class="label" value="" placeholder="{t}Link label{/t}">   <input  class="url" value="" placeholder="{t}https://... or page code{/t}"></div>

</div>
</div>


<div id="social_links_control_center" class="input_container link_url hide  " style="">

    <div style="margin-bottom:5px">  <i  onClick="update_social_links_from_dialog()" style="position:relative;top:-5px" class="button fa fa-fw fa-window-close" aria-hidden="true"></i>  </div>

    <div>   <i icon="fa-facebook" class="button social_link fa fa-fw fa-facebook" aria-hidden="true"></i>  <input  value="" placeholder="https://... Facebook"></div>
    <div>   <i icon="fa-google-plus"  class="button social_link fa fa-fw fa-google-plus" aria-hidden="true"></i>  <input  value="" placeholder="https://... Google +"></div>
    <div>   <i icon="fa-instagram"  class="button social_link fa fa-fw fa-instagram" aria-hidden="true"></i>  <input  value="" placeholder="https://... Instagram"></div>
    <div>   <i icon="fa-linkedin"  class="button social_link fa fa-fw fa-linkedin" aria-hidden="true"></i>  <input  value="" placeholder="https://... Linkedin"></div>
    <div>   <i icon="fa-pinterest"  class="button social_link fa fa-fw fa-pinterest" aria-hidden="true"></i>  <input  value="" placeholder="https://... Pinterest"></div>
    <div>   <i icon="fa-snapchat"  class="button social_link fa fa-fw fa-snapchat" aria-hidden="true"></i>  <input  value="" placeholder="https://... Snapchat"></div>
    <div>   <i icon="fa-twitter"  class="button social_link fa fa-fw fa-twitter" aria-hidden="true"></i>  <input  value="" placeholder="https://... Twitter"></div>
    <div>   <i icon="fa-vk"  class="button social_link fa fa-fw fa-vk" aria-hidden="true"></i>  <input  value="" placeholder="https://... VK"></div>
    <div>   <i icon="fa-xing"  class="button social_link fa fa-fw fa-xing" aria-hidden="true"></i>  <input  value="" placeholder="https://... Xing"></div>
    <div>   <i icon="fa-youtube"  class="button social_link fa fa-fw fa-youtube" aria-hidden="true"></i>  <input  value="" placeholder="https://... Youtube"></div>




</div>




<div id="block_type_1" class="input_container block_type  hide" style="">
    <div onClick="change_block_type(this)" class="type_items"><span>{t}Items{/t} <span class="italic">({t}Contact info{/t})</span></span></div>
    <div   onClick="change_block_type(this)" class="type_text"><span>{t}Text{/t} <span class="italic">({t}About us{/t})</span></span></div>
    <div onClick="change_block_type(this)"  class="type_links"><span>{t}Links{/t}</span></div>
    <div onClick="change_block_type(this)"  class="type_nothing"><span>{t}Nothing{/t}</span></div>

</div>

<div id="block_type_2" class="input_container block_type  hide" style="">
    <div onClick="change_block_type(this)" class="type_copyright_bundle"><span>{t}Copyright{/t}</span></div>
    <div  onClick="change_block_type(this)" class="type_social_links"><span>{t}Social icons{/t}</span></div>
    <div onClick="change_block_type(this)"  class="type_low_text"><span>{t}Text{/t}</span></div>
    <div onClick="change_block_type(this)"  class="type_low_nothing"><span>{t}Nothing{/t}</span></div>

</div>

<div id="item_types" class="input_container  hide  " style="">
    <div onClick="add_item_type(this)" ><i  class="button fa fa-fw fa-star" aria-hidden="true" label="{t}My company name{/t}"></i> </div>

    <div onClick="add_item_type(this)" ><i  class="button fa fa-fw fa-building-o" aria-hidden="true" label="{t}My company name{/t}"></i> </div>
    <div onClick="add_item_type(this)" ><i  class="button fa fa-fw fa-industry" aria-hidden="true" label="{t}My company name{/t}"></i> </div>

        <div onClick="add_item_type(this)" ><i  class="button fa fa-fw fa-map-marker" aria-hidden="true" label="110 Southmoor Road, Oxford OX2 6RB, UK"></i> </div>
        <div  onClick="add_item_type(this)"><i  class="button fa fa-fw  fa-phone" aria-hidden="true" label="+1-541-754-3010"></i> </div>
        <div  onClick="add_item_type(this)"><i  class="button fa fa-fw fa-mobile" aria-hidden="true" label="+1-541-754-3010"></i> </div>
        <div  onClick="add_item_type(this)"><i  class="button fa fa-fw fa-whatsapp" aria-hidden="true" label="+1-541-754-3010"	></i> </div>
        <div  onClick="add_item_type(this)"><i class="button fa fa-fw  fa-skype" aria-hidden="true" label="{t}Skype username{/t}"></i> </div>
        <div  onClick="add_item_type(this)"><i  class="button fa fa-fw  fa-envelope" aria-hidden="true" label="info@yourdomain.com"></i> </div>
        <div  onClick="add_item_type(this)"> <i  class="button fa fa-fw  fa-picture-o" aria-hidden="true"  label=""  ></i> </div>


</div>


<i id="delete_link" class="fa fa-trash hide editing button" aria-hidden="true" onClick="delete_link(this)" style="position:absolute" title="{t}Remove link{/t}" ></i>

<i id="delete_item" class="fa fa-trash hide editing button" aria-hidden="true" onClick="delete_item(this)" style="position:absolute" title="{t}Remove item{/t}" ></i>


<form id="change_image" class="hide" style="position:absolute;top:0;left:0" method="post" action="/ar_edit.php" enctype="multipart/form-data" novalidate>
    <input type="file" name="image_upload" id="file_upload" class="input_file" multiple/>
    <label for="file_upload">
        <i class=" fa fa-picture-o fa-fw button editing" aria-hidden="true" title="{t}Change image{/t}"></i>
    </label>
</form>


<div>




    <ul  class="hide">

            <li id="link_stem_cell" class="item"><a href="#"><i class="fa fa-fw fa-angle-right link_icon" onClick="update_link(this)"></i> <span ondrop="return false;" contenteditable>{t}New link{/t}<span></span></a></li>

        <li id="item_email_stem_cell" ><i class="fa fa-fw fa-envelope"></i> <span contenteditable>info@yourdomain.com</span></li>
        <li id="item_stem_cell"><i class="fa fa-fw "></i> <span contenteditable></span></li>
        <li  id="item_image_stem_cell" ><img  onclick="edit_item_image(this)" src="theme_1/images/header-wmap.png" alt="" /></li>



    </ul>


    <div id="block_copyright_bundle_stem_cell" class="hide">
    <div onClick="edit_copyright_bundle(this)"  class="header_copyright_bundle">
        {t}Copyright{/t} © {"%Y"|strftime} <span class="copyright_bundle_owner">Aurora</span>. {t}All rights reserved{/t}. <span class="copyright_bundle_links"> <a class="copyright_bundle_link" href="#"> Terms of Use</a> | <a class="copyright_bundle_link" href="#"> Privacy Policy</a></span>
    </div>
    </div>
    <div id="block_low_text_stem_cell" class="hide">
        Bla bla bla
    </div>

    <div id="block_social_links_stem_cell" class="hide">
        <ul  onClick="edit_social_links(this)"  class="header_social_links">

                <li class="" icon="fa-facebook"  ><a href="#"><i class="fa fa-facebook"></i></a></li>
            <li class="" icon="fa-twitter"  ><a href="#"><i class="fa fa-twitter"></i></a></li>
            <li class="" icon="fa-linkedin"  ><a href="#"><i class="fa fa-linkedin"></i></a></li>


        </ul>
    </div>

    <div id="block_text_stem_cell" class="hide">

        <div class="header_block siteinfo">

            <h4 class="lmb" contenteditable>{t}About us{/t}</h4>

            <div contenteditable>
                <p>
                All the Lorem Ipsum generators on the Internet tend to repeat predefined
                </p>
                <br />
                <p>
                An chunks as necessary, making this the first true generator on the Internet. Lorem Ipsum as their default model text, and a search for lorem ipsum will uncover desktop publishing packages many purpose web sites.
                </p>
            </div>
        </div>
    </div>

    <div id="block_links_stem_cell" class="hide">
        <div class="header_block qlinks">
            <h4 class="lmb" contenteditable>{t}Useful Links{/t}</h4>
            <ul class="links_list">
                <li class="item"><a href="#"><i class="fa fa-fw fa-angle-right link_icon" onClick="update_link(this)"></i> <span class="item_label" ondrop="return false;" contenteditable>{t}Home Page Variations{/t}<span></span></a></li>
                <li class="item"><a href="#"><i class="fa fa-fw fa-angle-right link_icon" onClick="update_link(this)"></i> <span class="item_label" ondrop="return false;" contenteditable>{t}Awesome Products{/t}<span></span></a></li>
                <li class="item"><a href="#"><i class="fa fa-fw fa-angle-right link_icon" onClick="update_link(this)"></i> <span class="item_label" ondrop="return false;" contenteditable>{t}Features and Benefits{/t}<span></span></a></li>
                <li onClick="add_link(this)"  class="ui-state-disabled add_link"><a href="{$item.url}"><i class="fa fa-fw fa-plus editing link_icon" onClick="update_link(this)"></i> <span class="editing" ondrop="return false;" >{t}Add link{/t}<span></span></a></li>
            </ul>

        </div>


    </div>

    <div id="block_nothing_stem_cell" class="hide">

        <div class="header_block">&nbsp;</div>
    </div>
    <div id="block_low_nothing_stem_cell" class="hide">
        &nbsp;
    </div>

    <div id="block_items_stem_cell" class="hide">
        <ul class="header_block faddress">

                <li  class="item"><img  onclick="edit_item_image(this)" src="theme_1/images/header-logo.png" alt="" /></li>

                <li   class="item"><i onclick="edit_item(this)"  class="fa fa-fw fa-map-marker"></i> <span contenteditable>10 London Road, Oxford,  OX2 6RB, UK</span></li>

                    <li   class="item"><i onclick="edit_item(this)"  class="fa fa-fw fa-phone"></i> <span contenteditable>+1-541-754-3010</span></li>

                    <li  class="item"><i onclick="edit_item(this)" class="fa fa-fw fa-envelope"></i> <span contenteditable>info@yourdomain.com</span></li>
                <li  class="item"><img  onclick="edit_item_image(this)" src="theme_1/images/header-wmap.png" alt="" /></li>


            <li onClick="add_item(this)"  class="ui-state-disabled add_item"><a href="{$item.url}"><i class="fa fa-fw fa-plus editing link_icon" onClick="update_link(this)"></i> <span class="editing" ondrop="return false;" >{t}Add item{/t}<span></span></a></li>
        </ul>
    </div>



    <header id="header">

        <!-- Top header bar -->
        <div id="topHeader">

            <div class="wrapper">

                <div class="top_nav">
                    <div class="container">

                        <div class="left">

                            <!-- Logo -->
                            <a href="index.html" id="logo"></a>

                        </div><!-- end left -->

                        <div class="right hide">

                            <a href="mailto:info@yourwebsite.com"><i class="fa fa-envelope"></i>&nbsp; info@yourwebsite.com</a>
                            <i class="fa fa-phone-square"></i>&nbsp; +88 123 456 7890

                            <ul class="topsocial two">
                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                <li><a href="#"><i class="fa fa-youtube"></i></a></li>
                                <li><a href="#"><i class="fa fa-rss"></i></a></li>
                            </ul>

                        </div>

                        <div class="right ">


                                <div style="float:right;background-color: black;height:30px;width: 30px ;text-align: center">
                                    <i class="fa fa-search" style="color:#fff;font-size:20px;position: relative;top:4px" aria-hidden="true"></i></div>
                                <input style="width: 250px;float:right;border: 1px solid black;padding:2px"/>



                        </div>

                    </div>
                </div><!-- end top links -->

            </div>

        </div><!-- end top navigations -->


        <div id="trueHeader">

            <div class="wrapper">

                <div class="container">

                    <nav class="menu_main2">

                        <div class="navbar yamm navbar-default">

                            <div class="navbar-header">
                                <div class="navbar-toggle .navbar-collapse .pull-right " data-toggle="collapse" data-target="#navbar-collapse-1"  > <span>Menu</span>
                                    <button type="button" > <i class="fa fa-bars"></i></button>
                                </div>
                            </div>

                            <div id="navbar-collapse-1" class="navbar-collapse collapse">

                                <ul class="nav navbar-nav three">




                                    <li class="dropdown yamm-fw"><a href="index.html" data-toggle="dropdown" class="dropdown-toggle active">Catalogue</a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <div class="yamm-content">
                                                    <div class="row">

                                                        <ul class="col-sm-6 col-md-4 list-unstyled two">
                                                            <li>
                                                                <p>{t}Store Departments{/t}</p>
                                                            </li>

                                                            {foreach from=$store->get_categories('departments','menu') item=department}
                                                                <li><a href="{$department['url']}"><i class="fa fa-caret-right"></i> {$department['label']}</a> {if $department['new']}<b class="mitemnew">{t}New{/t}</b>{/if}</li>
                                                            {/foreach}



                                                        </ul>

                                                        <ul class="col-sm-6 col-md-4 list-unstyled two">
                                                            <li>
                                                                <p>{t}Web Departments{/t}</p>
                                                            </li>

                                                            {foreach from=$website->get_categories('departments','menu') item=department}
                                                                <li><a href="{$department['url']}"><i class="fa fa-caret-right"></i> {$department['label']}</a> {if $department['new']}<b class="mitemnew">{t}New{/t}</b>{/if}</li>
                                                            {/foreach}



                                                        </ul>

                                                        <ul class="col-sm-6 col-md-4 list-unstyled two">
                                                            <li>
                                                                <p>{t}Web Families{/t}</p>
                                                            </li>

                                                            {foreach from=$website->get_categories('families','menu') item=families}
                                                                <li><a href="{$families['url']}"><i class="fa fa-caret-right"></i> {$families['label']}</a> {if $families['new']}<b class="mitemnew">{t}New{/t}</b>{/if}</li>
                                                            {/foreach}



                                                        </ul>




                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="dropdown"><a href="#" data-toggle="dropdown" class="dropdown-toggle">Info pages</a>
                                        <ul class="dropdown-menu multilevel" role="menu">
                                            <li><a href="about.html">About Style 1</a></li>
                                            <li><a href="about2.html">About Style 2</a></li>
                                            <li><a href="about3.html">About Style 3</a></li>
                                            <li><a href="about4.html">About Style 4</a></li>
                                            <li><a href="about5.html">About Style 5</a></li>
                                            <li><a href="services.html">Services Style 1</a></li>
                                            <li><a href="services2.html">Services Style 2</a></li>
                                            <li><a href="services3.html">Services Style 3</a></li>
                                            <li><a href="services4.html">Services Style 4</a></li>
                                            <li><a href="services5.html">Services Style 5</a></li>
                                            <li><a href="team.html">Our Team Style 1</a></li>
                                            <li><a href="team2.html">Our Team Style 2</a></li>
                                            <li><a href="team3.html">Our Team Style 3</a></li>
                                            <li class="dropdown-submenu mul"> <a tabindex="-1" href="#">Multi Level Submenu +</a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="#">Menu Item 1</a></li>
                                                    <li><a href="#">Menu Item 2</a></li>
                                                    <li><a href="#">Menu Item 3</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="dropdown"><a href="#" data-toggle="dropdown" class="dropdown-toggle">Offers</a>
                                        <ul class="dropdown-menu multilevel" role="menu">
                                            <li><a href="index1.html">Slider Style 1</a></li>
                                            <li><a href="index5.html">Slider Style 2</a></li>
                                            <li><a href="index4.html">Slider Style 3</a></li>
                                            <li><a href="index-layout14.html">Slider Style 4</a></li>
                                            <li><a href="index10.html">Slider Style 5</a></li>
                                            <li><a href="index11.html">Slider Style 6</a></li>
                                            <li><a href="index8.html">Slider Style 7</a></li>
                                            <li><a href="index9.html">Slider Style 8</a></li>
                                            <li><a href="index7.html">Slider Style 9</a></li>
                                            <li><a href="index.html">Slider Style 10</a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown yamm-fw"> <a href="#" data-toggle="dropdown" class="dropdown-toggle">Features</a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <div class="yamm-content">
                                                    <div class="row">

                                                        <ul class="col-sm-6 col-md-4 list-unstyled two">
                                                            <li>
                                                                <p>Useful Pages</p>
                                                            </li>
                                                            <li><a href="left-sidebar.html"><i class="fa fa-angle-right"></i>Left Sidebar</a></li>
                                                            <li><a href="right-sidebar.html"><i class="fa fa-angle-right"></i>Right Sidebar</a></li>
                                                            <li><a href="left-nav.html"><i class="fa fa-angle-right"></i>Left Navigation</a></li>
                                                            <li><a href="right-nav.html"><i class="fa fa-angle-right"></i>Right Navigation</a></li>
                                                            <li><a href="login.html"><i class="fa fa-angle-right"></i>Login Form</a></li>
                                                            <li><a href="register.html"><i class="fa fa-angle-right"></i>Registration Form</a></li>
                                                            <li><a href="404.html"><i class="fa fa-angle-right"></i>404 Error Page</a></li>
                                                            <li><a href="faq.html"><i class="fa fa-angle-right"></i>FAQs Page</a></li>
                                                            <li><a href="video-bg.html"><i class="fa fa-angle-right"></i>Video Backgrounds</a></li>
                                                        </ul>

                                                        <ul class="col-sm-6 col-md-4 list-unstyled two">
                                                            <li>
                                                                <p>Diffrent Websites</p>
                                                            </li>
                                                            <li><a href="coming-soon.html" target="_blank"><i class="fa fa-angle-right"></i>Coming Soon</a></li>
                                                            <li><a href="history.html"><i class="fa fa-angle-right"></i>History Timeline</a></li>
                                                            <li><a href="index-layout14.html"><i class="fa fa-angle-right"></i>Video BG Slider</a></li>
                                                            <li><a href="template17.html"><i class="fa fa-angle-right"></i>Header Styles</a></li>
                                                            <li><a href="template18.html"><i class="fa fa-angle-right"></i>Header Styles</a></li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>Masonry Gallerys</a> </li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>Parallax Backgrounds</a> </li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>Background Videos</a> </li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>Create your Own</a> </li>
                                                        </ul>

                                                        <ul class="col-sm-6 col-md-4 list-unstyled two">
                                                            <li>
                                                                <p>More Features</p>
                                                            </li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>Mega Menu</a></li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>Diffrent Websites</a></li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>Cross Browser Check</a></li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>Premium Sliders</a></li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>Diffrent Slide Shows</a></li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>Video BG Effects</a></li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>100+ Feature Sections</a></li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>Use for any Website</a></li>
                                                            <li><a href="#"><i class="fa fa-angle-right"></i>Free Updates</a></li>
                                                        </ul>

                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="dropdown"><a href="#" data-toggle="dropdown" class="dropdown-toggle">Inspiration</a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li> <a href="portfolio-1.html">Single Item</a> </li>
                                            <li> <a href="portfolio-5.html">Portfolio Masonry</a> </li>
                                            <li> <a href="portfolio-4.html">Portfolio Columns 4</a> </li>
                                            <li> <a href="portfolio-3.html">Portfolio Columns 3</a> </li>
                                            <li> <a href="portfolio-2.html">Portfolio Columns 2</a> </li>
                                            <li> <a href="portfolio-6.html">Portfolio + Sidebar</a> </li>
                                            <li> <a href="portfolio-7.html">Portfolio Full Width</a> </li>
                                            <li> <a href="portfolio-8.html">Image Gallery</a> </li>
                                        </ul>
                                    </li>

                                    <li class="dropdown"> <a href="#" data-toggle="dropdown" class="dropdown-toggle">Blog </a>
                                        <ul class="dropdown-menu multilevel" role="menu">
                                            <li> <a href="blog-4.html">With Masonry</a> </li>
                                            <li> <a href="blog.html">With Large Image</a> </li>
                                            <li> <a href="blog-2.html">With Medium Image</a> </li>
                                            <li> <a href="blog-3.html">With Small Image</a> </li>
                                            <li> <a href="blog-post.html">Single Post</a> </li>
                                        </ul>
                                    </li>

                                    <li class="dropdown yamm-fw"> <a href="#" data-toggle="dropdown" class="dropdown-toggle">Shortcodes</a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <div class="yamm-content">
                                                    <div class="row">

                                                        <ul class="col-sm-6 col-md-4 list-unstyled two">
                                                            <li><a href="template1.html"><i class="fa fa-plus-square"></i> Accordion &amp; Toggle</a></li>
                                                            <li><a href="template2.html"><i class="fa fa-leaf"></i> Title Styles</a></li>
                                                            <li><a href="template3.html"><i class="fa fa-bars"></i> List of Dividers</a></li>
                                                            <li><a href="template4.html"><i class="fa fa-exclamation-triangle"></i> Boxes Alert</a></li>
                                                            <li><a href="template5.html"><i class="fa fa-hand-o-up"></i> List of Buttons</a></li>
                                                            <li><a href="template6.html"><i class="fa fa-cog"></i> Carousel Sliders</a></li>
                                                            <li><a href="template7.html"><i class="fa fa-file-text"></i> Page Columns</a></li>
                                                            <li><a href="template8.html"><i class="fa fa-rocket"></i> Animated Counters</a></li>
                                                            <li><a href="template17.html"><i class="fa fa-question"></i> Faqs Page</a></li>
                                                        </ul>

                                                        <ul class="col-sm-6 col-md-4 list-unstyled two">
                                                            <li><a href="template9.html"><i class="fa fa-pie-chart"></i> Pie Charts</a></li>
                                                            <li><a href="template10.html"><i class="fa fa-flag"></i> Font Icons</a></li>
                                                            <li><a href="template11.html"><i class="fa fa-umbrella"></i> Flip Boxes</a></li>
                                                            <li><a href="template12.html"><i class="fa fa-picture-o"></i> Image Frames</a></li>
                                                            <li><a href="template13.html"><i class="fa fa-table"></i> Pricing Tables</a></li>
                                                            <li><a href="template14.html"><i class="fa fa-line-chart"></i> Progress Bars</a></li>
                                                            <li><a href="template15.html"><i class="fa fa-toggle-on"></i> List of Tabs</a></li>
                                                            <li><a href="template16.html"><i class="fa fa-paper-plane"></i> Popover &amp; Tooltip</a></li>
                                                            <li><a href="template18.html"><i class="fa fa-play-circle"></i> Video Backgrounds</a></li>
                                                        </ul>

                                                        <ul class="col-sm-6 col-md-4 list-unstyled two">
                                                            <li>
                                                                <p>About Website</p>
                                                            </li>
                                                            <li class="dart">
                                                                <img src="http://placehold.it/230x80" alt="" class="rimg marb1" />
                                                                There are many variations passages available the majority have alteration in some form, by injected humour on randomised words if you are going to use a passage of lorem anything.
                                                            </li>
                                                        </ul>

                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="dropdown"><a href="#" data-toggle="dropdown" class="dropdown-toggle">Contact</a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li> <a href="contact.html">Contact Style 1</a> </li>
                                            <li> <a href="contact2.html">Contact Style 2</a> </li>
                                            <li> <a href="contact3.html">Contact Style 3</a> </li>
                                        </ul>
                                    </li>

                                </ul>

                            </div>

                        </div>

                    </nav><!-- end Navigation Menu -->

                    <div class="menu_right2">
                        <div class="search_hwrap two">


                        </div>
                    </div><!-- end search bar -->

                </div>

            </div>

        </div>

    </header>



    <script>

        var image=false;

    var current_editing_link_id=false;
var current_editing_item_id=false

    function open_block_type_options(element,option_id,current_block_type){





        var option_dialog=$('#'+option_id)


        var block=$(element).next('.header_block')

        block.uniqueId()
        var id= block.attr('id')


        option_dialog.removeClass('hide').offset({ top:$(element).offset().top-5, left:$(element).offset().left+20  }).attr('block_id',id)

        $('#'+option_id+' div').removeClass('selected')
        option_dialog.find('.type_'+current_block_type).addClass('selected')

    }


    function change_block_type(element) {




        var block_type = $(element).closest('.block_type')

        console.log( block_type.attr('block_id'))

        console.log($('#' + block_type.attr('block_id')))


        if ($(element).hasClass('type_text')) {
            $('#' + block_type.attr('block_id')).replaceWith($('#block_text_stem_cell').html())
        }else if ($(element).hasClass('type_low_text')) {
            $('#' + block_type.attr('block_id')).html($('#block_low_text_stem_cell').html())
        }else if ($(element).hasClass('type_social_links')) {
            $('#' + block_type.attr('block_id')).html($('#block_social_links_stem_cell').html())
        }else if ($(element).hasClass('type_copyright_bundle')) {
            $('#' + block_type.attr('block_id')).html($('#block_copyright_bundle_stem_cell').html())
        }else if ($(element).hasClass('type_links')) {
            $('#' + block_type.attr('block_id')).replaceWith($('#block_links_stem_cell').html())
        }else if ($(element).hasClass('type_items')) {
            $('#' + block_type.attr('block_id')).replaceWith($('#block_items_stem_cell').html())

            $('.faddress').sortable({
                disabled: false,
                items: "li:not(.ui-state-disabled)",
                connectWith: ".faddress"
            });


        }else if ($(element).hasClass('type_nothing')) {
            $('#' + block_type.attr('block_id')).replaceWith($('#block_nothing_stem_cell').html())
        }else if ($(element).hasClass('type_low_nothing')) {
            $('#' + block_type.attr('block_id')).html($('#block_low_nothing_stem_cell').html())
        }


        $('.sortable_container').sortable({
            disabled: false,
            update: function( event, ui ) {
                $(this).children().removeClass('last')
                $(this).children().last().addClass('last')


            }

        });

        block_type.addClass('hide')


    }


    function add_item_type(element){

    var icon=$(element).find('i')
        $('#item_types').addClass('hide')



        if(icon.hasClass('fa-picture-o')){

            var new_item= $("#item_image_stem_cell").clone()
        }else{

            var new_item= $("#item_stem_cell").clone()
            new_item.find('span').html(icon.attr('label'));
            new_item.find('i').attr('class',icon.attr('class'))
        }



    new_item.insertBefore($('#'+ $('#item_types').attr('anchor')));
        console.log('add_item_type')

    }

    function add_item(element){

        if( $('#item_types').hasClass('hide')) {
            $(element).uniqueId()
            $('#item_types').removeClass('hide').offset({
                top: $(element).offset().top - 55, left: $(element).offset().left + 20
            }).attr('anchor',$(element).attr('id'))
        }else{
            $('#item_types').addClass('hide')

        }

    }


    function edit_item(element){


        $(element).uniqueId()
        var id= $(element).attr('id')


        if( $('#delete_item').hasClass('hide')) {
            current_editing_item_id=id


            $('#delete_item').removeClass('hide').offset({ top: $(element).offset().top, left: $(element).offset().left - 20}).attr('item_id', id)
        }else{


            if(current_editing_item_id==id){
                $('#delete_item').addClass('hide')
            }else{
                current_editing_item_id=id
                $('#delete_item').removeClass('hide').offset({ top: $(element).offset().top, left: $(element).offset().left - 20}).attr('item_id', id)
            }

        }

    }

    function edit_item_image(element){
        $(element).uniqueId()
        var id= $(element).attr('id')

        if( $('#delete_item').hasClass('hide')) {
            current_editing_item_id = id

            $('#delete_item').removeClass('hide').offset({ top: $(element).offset().top, left: $(element).offset().left - 20}).attr('item_id', id)
            $('#change_image').removeClass('hide').offset({ top: $(element).offset().top+20, left: $(element).offset().left - 20}).attr('item_id', id)

         //   $('#change_image').removeClass('hide')

        }else{
            if(current_editing_item_id==id){
                $('#delete_item').addClass('hide')
                $('#change_image').addClass('hide')

            }else{
                current_editing_item_id=id
                $('#delete_item').removeClass('hide').offset({ top: $(element).offset().top, left: $(element).offset().left - 20}).attr('item_id', id)
                $('#change_image').removeClass('hide').offset({ top: $(element).offset().top+20, left: $(element).offset().left - 20}).attr('item_id', id)

            }

        }

    }




    function update_link(element){
        $(element).uniqueId()
        var id= $(element).attr('id')




        if($('#input_container_link').hasClass('hide')   ){
            current_editing_link_id=id

            $('#input_container_link').removeClass('hide').offset({ top:$(element).offset().top-55, left:$(element).offset().left+20  }).find('input').val($(element).closest('a').attr("href"))
            $('#delete_link').removeClass('hide').offset({ top:$(element).offset().top, left:$(element).offset().left-15  }).attr('link_id',id)
            $(element).addClass('editing fa-window-close').next('span').addClass('editing')
        }else{

            console.log(id)

            if(current_editing_link_id==id){
                $('#input_container_link').addClass('hide')
                $('#delete_link').addClass('hide')
                $(element).removeClass('editing fa-window-close').next('span').removeClass('editing')
            }else{
                $('#'+current_editing_link_id).removeClass('editing fa-window-close').next('span').removeClass('editing')
                current_editing_link_id=id

                $('#input_container_link').removeClass('hide').offset({ top:$(element).offset().top-55, left:$(element).offset().left+20  }).find('input').val($(element).closest('a').attr("href"))
                $('#delete_link').removeClass('hide').offset({ top:$(element).offset().top, left:$(element).offset().left-15  }).attr('link_id',id)
                $(element).addClass('editing fa-window-close').next('span').addClass('editing')

            }


        }



    }


    function change_drag_mode(element){

        if($(element).hasClass('on')){

            $('.links_list').sortable({
                disabled: true
            });

            $('.faddress').sortable({
                disabled: true
            });

            $('.sortable_container').sortable({
                disabled: true

            });
            $('.dragger').addClass('hide')
            $('.recycler').addClass('hide')
            $(element).removeClass('on')
            $(element).find('.fa-check-circle').addClass('invisible')

        }else{

            $('.links_list').sortable({
                disabled: false,
                items: "li:not(.ui-state-disabled)",
                connectWith: ".links_list"
            });

            $('.faddress').sortable({
                disabled: false,
                items: "li:not(.ui-state-disabled)",
                connectWith: ".faddress"
            });

            $('.sortable_container').sortable({
                disabled: false,
                update: function( event, ui ) {
                    $(this).children().removeClass('last')
                    $(this).children().last().addClass('last')


                }

            });
            $('.dragger').removeClass('hide')
            $('.recycler').removeClass('hide')
            $(element).addClass('on')
            $(element).find('.fa-check-circle').removeClass('invisible')




        }
    }


function add_link(element){


        var ul=$(element).closest('ul');

    var new_data= $("#link_stem_cell").clone();
    new_data.insertBefore($(element));
}


    function delete_link(element){
        $('#'+$(element).attr('link_id')).closest('li').remove()
        $('#input_container_link').addClass('hide')
        $('#delete_link').addClass('hide')
    }
    function delete_item(element){
        $('#'+$(element).attr('item_id')).closest('li').remove()
        $('#delete_item').addClass('hide')
    }



    $(document).on('click', 'a', function (e) {
        if (e.which == 1 && !e.metaKey && !e.shiftKey) {

            return false
        }
    })

        function  edit_social_links(element){


      //  if(! $('#social_links_control_center').hasClass('hide')){
        //    return
       // }

        var block=$(element)
            block.uniqueId()
            var id= block.attr('id')

            block.find('li').each(function(i, obj) {
                $('#social_links_control_center').find('.'+$(obj).attr('icon')).next('input').val($(obj).find('a').attr('href')   )
            });



        if($(element).closest('.one_half').hasClass('last')){
            $('#social_links_control_center').attr('block_id',id).removeClass('hide').offset({ top:block.offset().top -30-  $('#social_links_control_center').height() , left:block.offset().left+block.width()  - $('#social_links_control_center').width()  })

        }else{
            $('#social_links_control_center').attr('block_id',id).removeClass('hide').offset({ top:block.offset().top -30-  $('#social_links_control_center').height() , left:block.offset().left   })

        }







        }


        function update_social_links_from_dialog(){

          var block=$('#'+$('#social_links_control_center').attr('block_id'))
            $('#social_links_control_center').addClass('hide')
            social_links=''

           $('#social_links_control_center .social_link').each(function(i, obj) {
               if ($(obj).next('input').val() != '') {
                   social_links += ' <li class="" icon="' + $(obj).attr('icon') + '"  ><a href="' + $(obj).next('input').val() + '"><i class="fa ' + $(obj).attr('icon') + '"></i></a></li>'
               }
           })

            if(social_links==''){
                social_links='<i class="fa fa-plus editing" title="{t}Add social media link{/t}" aria-hidden="true"></i>  <span style="margin-left:5px" class="editing">{t}Add social media link{/t}</span>';
            }

            block.html(social_links)
        }


    function  edit_copyright_bundle(element){


            if($('#drag_mode').hasClass('on')){
                return;
            }


        if(! $('#copyright_bundle_control_center').hasClass('hide')){
            return
        }

        var block=$(element)
        block.uniqueId()
        var id= block.attr('id')

        block.find('.copyright_bundle_link').each(function(i, obj) {

          var link=  $( "#copyright_bundle_control_center .discreet_links_control_panel div:nth-child("+(i+1)+")" )

           // console.log( "#copyright_bundle_control_center .social_links_control_center:nth-child("+i+")")
          //  console.log( $("#copyright_bundle_control_center .discreet_links_control_panel div:nth-child(1)").html())
console.log(link.html())
            link.find('.label').val($(obj).html())
            link.find('.url').val($(obj).attr('href'))

            //      $('#social_links_control_center').find('.'+$(obj).attr('icon')).next('input').val($(obj).find('a').attr('href')   )
        });

        $('#copyright_bundle_control_center_owner').val(block.find('.copyright_bundle_owner').html())
        $('#copyright_bundle_control_center').attr('block_id',id).removeClass('hide').offset({ top:block.offset().top -30-  $('#copyright_bundle_control_center').height() , left:block.offset().left+block.width()  - $('#copyright_bundle_control_center').width()  })






    }


    function update_copyright_bundle_from_dialog(){

        var block=$('#'+$('#copyright_bundle_control_center').attr('block_id'))
        $('#copyright_bundle_control_center').addClass('hide')
        copyright_links=''


        block.find('.copyright_bundle_owner').html($('#copyright_bundle_control_center_owner').val())

        $('#copyright_bundle_control_center .copyright_link').each(function(i, obj) {
            if ($(obj).find('.label').val() != '' &&  $(obj).find('.url').val() != '') {
                copyright_links += '<a class="copyright_bundle_link" href="' + $(obj).find('.url').val() + '">' + $(obj).find('.label').val() + '</a>  | '
            }
        })

        copyright_links=copyright_links.replace(/ \| $/g, "");

        block.find('.copyright_bundle_links').html(copyright_links)
    }

    $("body").on('DOMSubtreeModified', ".header", function() {
        $('#save_button').addClass('save button')
    });

    function save_header(){

       if(! $('#save_button').hasClass('save')){
         //  return;
       }

        $('#save_button').find('i').addClass('fa-spinner fa-spin')


        var cols_main_4=[];
        var cols_copyright=[];

        $('header.header .header_block').each(function(i, obj) {



            if($(obj).hasClass('faddress')){

                var items =[];

                $(obj).find('.item').each(function(j, obj2) {

                    if($(obj2).hasClass('_logo')){


                        console.log($(obj2))

                       var img= $(obj2).find('img')


                        items.push({
                          type: "logo",
                           src: img.attr('src'),
                            title: img.attr('title')
                        });

                    }
                    else if($(obj2).hasClass('_text')){



                        items.push({
                            type: "text",
                            icon: $(obj2).attr('icon'),
                            text:  $(obj2).find('span').html(),
                        });

                    } else if($(obj2).hasClass('_email')){



                        items.push({
                            type: "email",
                            text:  $(obj2).find('span').html(),
                        });

                    }





                   //console.log(obj2)

                });





                cols_main_4.push(
                    {
                        'type':'address',
                        'items':items

                    }
                )

            }
            else if($(obj).hasClass('siteinfo')){

                cols_main_4.push(
                    {
                        'type':'text',
                        'header':$(obj).find('h4.lmb').html(),
                        'text':$(obj).find('div').html()
                    }
                )

            }
            else if($(obj).hasClass('qlinks')){


                var items=[]
                $(obj).find('.item').each(function(j, obj2) {

                        items.push({
                            url: $(obj2).find('a').attr('href'),
                            label: $(obj2).find('.item_label').html(),
                        });


                });


                cols_main_4.push(
                    {
                        'type':'links',
                        'header':$(obj).find('h4.lmb').html(),
                        'items':items
                    }
                )

            }
            else if($(obj).hasClass('nothing')){
                cols_main_4.push(
                    {
                        'type':'nothing'

                    }
                )
            }
            else if($(obj).hasClass('_copyright_text')){

                cols_copyright.push(
                    {
                        'type':'text',
                        'text':$(obj).html()
                    }
                )

            }
            else if($(obj).hasClass('_copyright_nothing')){

                cols_copyright.push(
                    {
                        'type':'nothing'
                    }
                )

            }

            else if($(obj).hasClass('_copyright_bundle')){


                var links=[]
                $(obj).find('.copyright_bundle_link').each(function(j, obj2) {

                    links.push({
                        url: $(obj2).attr('href'),
                        label: $(obj2).html(),
                    });


                });

                cols_copyright.push(
                    {
                        'type':'copyright_bundle',
                        'owner':$(obj).find('.copyright_bundle_owner').html(),
                    'links':links
                    }
                )

            }
            else if($(obj).hasClass('_social_links')){


                var items=[]
                $(obj).find('.social_link').each(function(j, obj2) {

                    items.push({
                        url: $(obj2).find('a').attr('href'),
                        icon: $(obj2).attr('icon'),
                    });


                });

                cols_copyright.push(
                    {
                        'type':'social_links',
                        'items':items
                    }
                )

            }
        })

        header_data={
            rows:[]}


        header_data.rows.push({
                'type':'main_4',
                'columns':cols_main_4
            }
        )
        header_data.rows.push({
            'type':'copyright',
            'columns':cols_copyright
    }
    )


     //   console.log(header_data)

        var request = '/ar_edit_website.php?tipo=save_header&header_key={$header_key}&header_data=' +encodeURIComponent(btoa(JSON.stringify(header_data)));


        $.getJSON(request, function (data) {


            $('#save_button').removeClass('save').find('i').removeClass('fa-spinner fa-spin')

        })


    }

    var droppedFiles = false;

    $('#file_upload').on('change', function (e) {


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





        ajaxData.append("tipo", 'upload_images')
        ajaxData.append("parent", 'website')
        ajaxData.append("parent_key", '{$website->id}')
        ajaxData.append("parent_object_scope", JSON.stringify({
            scope: 'header', scope_key: '{$header_key}'

        }))
        ajaxData.append("options", JSON.stringify({
            max_width: 180

        }))

        ajaxData.append("response_type", 'website')




     //   var image = $('#' + $('#image_edit_toolbar').attr('block') + ' img')


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

                console.log(data)

                if (data.state == '200') {

                    console.log('#'+$('#change_image').attr('item_id'))

                    $('#'+$('#change_image').attr('item_id')).attr('src',data.image_src).attr('web_image_key',data.web_image_key)








                } else if (data.state == '400') {

                }


            },
            error: function () {

            }
        });



    });



    </script>