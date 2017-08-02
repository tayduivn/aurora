﻿{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 31 July 2017 at 10:41:58 CEST, 
 Copyright (c) 2017, Inikoo

 Version 3
-->
*}{include file="theme_1/_head.theme_1.EcomB2B.tpl"}


<body xmlns="http://www.w3.org/1999/html">
{include file="analytics.tpl"}


<div class="wrapper_boxed">

    <div class="site_wrapper">

        {include file="theme_1/header.EcomB2B.tpl"}

        <div class="content_fullwidth less2">
            <div class="container">


                <div class="left_sidebar">

                    <div class="sidebar_widget">


                        <div class="sidebar_title"><h4 id="_customer_profile_title">{$content._customer_profile_title}</h4></div>

                        <ul class="arrows_list1">

                            <li>
                                <span block="_contact_details" onClick="change_block(this)" class="block_link  like_button  selected" style="cursor: pointer"  >
                                    <i class="fa fa-angle-right"></i>
                                    <span class="_contact_details_title">{$content._contact_details_title}</span>
                                </span>
                            </li>
                            <li>
                                <span  block="_login_details" onClick="change_block(this)" class="block_link  like_button"  style="cursor: pointer">
                                    <i class="fa fa-angle-right"></i>
                                    <span class="_login_details_title">{$content._login_details_title}</span>
                                </span>
                            </li>

                            <li>
                                <span  block="_invoice_address_details" onClick="change_block(this)" class="block_link like_button hide"  style="cursor: pointer">
                                    <i class="fa fa-angle-right"></i>
                                    <span class="_invoice_address_title">{$content._invoice_address_title}</span>
                                    </span>
                            </li>

                            <li>
                                <span  block="_delivery_addresses_details" onClick="change_block(this)" class="block_link like_button hide" style="cursor: pointer">
                                    <i class="fa fa-angle-right"></i>
                                    <span class="_delivery_addresses_title">{$content._delivery_addresses_title}</span>

                                    </span>
                            </li>



                        </ul>

                        <div class="clearfix marb3"></div>
                        <div class="hide sidebar_title"><h4 id="_customer_orders_title">{$content._customer_orders_title}</h4></div>

                        <ul class="hide arrows_list1">
                            <li>
                                <span class="block_link    selected">
                                    <i class="fa fa-angle-right"></i>
                                    <span class="_orders_title">{$content._orders_title}</span>
                                    <i block="_orders" onClick="change_block(this)" class="padding_left_10 fa like_button fa-arrow-right"></i>
                                </span>
                            </li>



                        </ul>


                    </div><!-- end section -->

                </div>


                <div class="content_right">


                    <div id="_contact_details" class="block reg_form">
                        <form id="contact_details" class="sky-form">
                            <header class="mirror_master" id="_contact_details_title">{$content._contact_details_title}</header>

                            <fieldset>


                                <section>
                                    <label class="label">{$content._company_label}</label>
                                    <label class="input">
                                        <i id="company" class="icon-append icon-briefcase"></i>
                                        <input class="register_field" type="text" name="company" value="{$customer->get('Customer Company Name')}" placeholder="{$content._company_placeholder}">
                                        <b id="_company_tooltip" class="tooltip tooltip-bottom-right">{$content._company_tooltip}</b>
                                    </label>
                                </section>


                                <section>
                                    <label class="label">{$content._contact_name_label}</label>
                                    <label class="input">
                                        <i id="contact_name" class="icon-append icon-user"></i>
                                        <input class="register_field" type="text" name="contact_name" value="{$customer->get('Customer Main Contact Name')}" placeholder="{$content._contact_name_placeholder}">
                                        <b id="_contact_name_tooltip" class="tooltip tooltip-bottom-right">{$content._contact_name_tooltip}</b>
                                    </label>
                                </section>


                                <section>
                                    <label class="label">{$content._mobile_label}</label>
                                    <label class="input">
                                        <i class="icon-append fa fa-mobile" ></i>
                                        <input class="register_field" type="text" name="mobile"  value="{$customer->get('Customer Main Plain Mobile')}"  placeholder="{$content._mobile_placeholder}">
                                        <b id="_mobile_tooltip" class="tooltip tooltip-bottom-right">{$content._mobile_tooltip}</b>
                                    </label>
                                </section>

                                <section>
                                    <label class="label">{$content._email_label}</label>
                                    <label class="input">
                                        <i class="icon-append fa fa-envelope-o"></i>
                                        <input class="register_field" type="email" name="email" id="_email_placeholder" value="{$customer->get('Customer Main Plain Email')}" placeholder="{$content._email_placeholder}">
                                        <b id="_email_tooltip" class="tooltip tooltip-bottom-right">{$content._email_tooltip}</b>
                                    </label>
                                </section>

                            </fieldset>


                            <fieldset>

                                <section>
                                    <label class="label">{$content._registration_number_label}</label>

                                    <label class="input">
                                        <i class="icon-append icon-gavel"><i class="fa fa-building-o" aria-hidden="true"></i>
                                        </i>
                                        <input class="register_field" type="text" name="registration_number"   value="{$customer->get('Customer Registration Number')}" placeholder="{$content._registration_number_placeholder}">
                                        <b id="_registration_number_tooltip" class="tooltip tooltip-bottom-right">{$content._registration_number_tooltip}</b>
                                    </label>
                                </section>

                                <section>
                                    <label class="label">{$content._tax_number_label}</label>

                                    <label class="input">
                                        <i id="_tax_number" onclick="show_edit_input(this)" class="icon-append icon-gavel"><i class="fa fa-gavel" aria-hidden="true"></i>
                                        </i>
                                        <input class="register_field" type="text" name="tax_number" id="_tax_number_placeholder"  value="{$customer->get('Customer Tax Number')}" placeholder="{$content._tax_number_placeholder}">
                                        <b id="_tax_number_tooltip" class="tooltip tooltip-bottom-right">{$content._tax_number_tooltip}</b>
                                    </label>
                                    <label class="label">{$customer->get('Tax Number Valid')}</label>

                                </section>


                            </fieldset>


                            <footer>
                                <button type="submit" class="button " id="_submit_label">{$content._save_contact_details_label}</button>
                            </footer>
                        </form>
                    </div>
                    <div id="_login_details" class="block hide reg_form">
                        <form id="login_details" class="sky-form">
                            <header class="mirror_master" id="_login_details_title">{$content._login_details_title}</header>

                            <fieldset>
                                <section>
                                    <label class="input">
                                        <span id="_username_info">{$content._username_info}
                                    </label>
                                </section>

                                <section>
                                    <label class="input">
                                        <i id="_password" onclick="show_edit_input(this)" class="icon-append icon-lock"></i>
                                        <input class="register_field" type="password" name="pwd" id="password" placeholder="{$content._password_placeholder}">
                                        <b id="_password_tooltip" class="tooltip tooltip-bottom-right">{$content._password_tooltip}</b>
                                    </label>
                                </section>

                                <section>
                                    <label class="input">
                                        <i id="_password_conform" onclick="show_edit_input(this)" class="icon-append icon-lock"></i>
                                        <input class="register_field ignore" type="password" name="password_confirm" placeholder="{$content._password_confirm_placeholder}">
                                        <b id="_password_conform_tooltip" class="tooltip tooltip-bottom-right">{$content._password_conform_tooltip}</b>
                                    </label>
                                </section>


                            </fieldset>
                            <footer>
                                <button type="submit" class="button " id="_submit_label">{$content._save_login_details_label}</button>

                            </footer>
                        </form>
                    </div>

                    <div id="_invoice_address_details" class="block hide reg_form">
                        <form action="" id="sky-form" class="sky-form">
                            <header class="mirror_master" id="_invoice_address_title">{$content._invoice_address_title}</header>


                            <fieldset>
                                <div class="row">
                                    <section class="col col-5">
                                        <label class="select">
                                            <select name="country">
                                                <option value="0" selected disabled>Country</option>
                                                <option value="244">Aaland Islands</option>
                                                <option value="1">Afghanistan</option>
                                                <option value="2">Albania</option>
                                                <option value="3">Algeria</option>
                                                <option value="4">American Samoa</option>
                                                <option value="5">Andorra</option>
                                                <option value="6">Angola</option>
                                                <option value="7">Anguilla</option>
                                                <option value="8">Antarctica</option>
                                                <option value="9">Antigua and Barbuda</option>
                                                <option value="10">Argentina</option>
                                                <option value="11">Armenia</option>
                                                <option value="12">Aruba</option>
                                                <option value="13">Australia</option>
                                                <option value="14">Austria</option>
                                                <option value="15">Azerbaijan</option>
                                                <option value="16">Bahamas</option>
                                                <option value="17">Bahrain</option>
                                                <option value="18">Bangladesh</option>
                                                <option value="19">Barbados</option>
                                                <option value="20">Belarus</option>
                                                <option value="21">Belgium</option>
                                                <option value="22">Belize</option>
                                                <option value="23">Benin</option>
                                                <option value="24">Bermuda</option>
                                                <option value="25">Bhutan</option>
                                                <option value="26">Bolivia</option>
                                                <option value="245">Bonaire, Sint Eustatius and Saba</option>
                                                <option value="27">Bosnia and Herzegovina</option>
                                                <option value="28">Botswana</option>
                                                <option value="29">Bouvet Island</option>
                                                <option value="30">Brazil</option>
                                                <option value="31">British Indian Ocean Territory</option>
                                                <option value="32">Brunei Darussalam</option>
                                                <option value="33">Bulgaria</option>
                                                <option value="34">Burkina Faso</option>
                                                <option value="35">Burundi</option>
                                                <option value="36">Cambodia</option>
                                                <option value="37">Cameroon</option>
                                                <option value="38">Canada</option>
                                                <option value="251">Canary Islands</option>
                                                <option value="39">Cape Verde</option>
                                                <option value="40">Cayman Islands</option>
                                                <option value="41">Central African Republic</option>
                                                <option value="42">Chad</option>
                                                <option value="43">Chile</option>
                                                <option value="44">China</option>
                                                <option value="45">Christmas Island</option>
                                                <option value="46">Cocos (Keeling) Islands</option>
                                                <option value="47">Colombia</option>
                                                <option value="48">Comoros</option>
                                                <option value="49">Congo</option>
                                                <option value="50">Cook Islands</option>
                                                <option value="51">Costa Rica</option>
                                                <option value="52">Cote D'Ivoire</option>
                                                <option value="53">Croatia</option>
                                                <option value="54">Cuba</option>
                                                <option value="246">Curacao</option>
                                                <option value="55">Cyprus</option>
                                                <option value="56">Czech Republic</option>
                                                <option value="237">Democratic Republic of Congo</option>
                                                <option value="57">Denmark</option>
                                                <option value="58">Djibouti</option>
                                                <option value="59">Dominica</option>
                                                <option value="60">Dominican Republic</option>
                                                <option value="61">East Timor</option>
                                                <option value="62">Ecuador</option>
                                                <option value="63">Egypt</option>
                                                <option value="64">El Salvador</option>
                                                <option value="65">Equatorial Guinea</option>
                                                <option value="66">Eritrea</option>
                                                <option value="67">Estonia</option>
                                                <option value="68">Ethiopia</option>
                                                <option value="69">Falkland Islands (Malvinas)</option>
                                                <option value="70">Faroe Islands</option>
                                                <option value="71">Fiji</option>
                                                <option value="72">Finland</option>
                                                <option value="74">France, skypolitan</option>
                                                <option value="75">French Guiana</option>
                                                <option value="76">French Polynesia</option>
                                                <option value="77">French Southern Territories</option>
                                                <option value="126">FYROM</option>
                                                <option value="78">Gabon</option>
                                                <option value="79">Gambia</option>
                                                <option value="80">Georgia</option>
                                                <option value="81">Germany</option>
                                                <option value="82">Ghana</option>
                                                <option value="83">Gibraltar</option>
                                                <option value="84">Greece</option>
                                                <option value="85">Greenland</option>
                                                <option value="86">Grenada</option>
                                                <option value="87">Guadeloupe</option>
                                                <option value="88">Guam</option>
                                                <option value="89">Guatemala</option>
                                                <option value="241">Guernsey</option>
                                                <option value="90">Guinea</option>
                                                <option value="91">Guinea-Bissau</option>
                                                <option value="92">Guyana</option>
                                                <option value="93">Haiti</option>
                                                <option value="94">Heard and Mc Donald Islands</option>
                                                <option value="95">Honduras</option>
                                                <option value="96">Hong Kong</option>
                                                <option value="97">Hungary</option>
                                                <option value="98">Iceland</option>
                                                <option value="99">India</option>
                                                <option value="100">Indonesia</option>
                                                <option value="101">Iran (Islamic Republic of)</option>
                                                <option value="102">Iraq</option>
                                                <option value="103">Ireland</option>
                                                <option value="104">Israel</option>
                                                <option value="105">Italy</option>
                                                <option value="106">Jamaica</option>
                                                <option value="107">Japan</option>
                                                <option value="240">Jersey</option>
                                                <option value="108">Jordan</option>
                                                <option value="109">Kazakhstan</option>
                                                <option value="110">Kenya</option>
                                                <option value="111">Kiribati</option>
                                                <option value="113">Korea, Republic of</option>
                                                <option value="114">Kuwait</option>
                                                <option value="115">Kyrgyzstan</option>
                                                <option value="116">Lao People's Democratic Republic</option>
                                                <option value="117">Latvia</option>
                                                <option value="118">Lebanon</option>
                                                <option value="119">Lesotho</option>
                                                <option value="120">Liberia</option>
                                                <option value="121">Libyan Arab Jamahiriya</option>
                                                <option value="122">Liechtenstein</option>
                                                <option value="123">Lithuania</option>
                                                <option value="124">Luxembourg</option>
                                                <option value="125">Macau</option>
                                                <option value="127">Madagascar</option>
                                                <option value="128">Malawi</option>
                                                <option value="129">Malaysia</option>
                                                <option value="130">Maldives</option>
                                                <option value="131">Mali</option>
                                                <option value="132">Malta</option>
                                                <option value="133">Marshall Islands</option>
                                                <option value="134">Martinique</option>
                                                <option value="135">Mauritania</option>
                                                <option value="136">Mauritius</option>
                                                <option value="137">Mayotte</option>
                                                <option value="138">Mexico</option>
                                                <option value="139">Micronesia, Federated States of</option>
                                                <option value="140">Moldova, Republic of</option>
                                                <option value="141">Monaco</option>
                                                <option value="142">Mongolia</option>
                                                <option value="242">Montenegro</option>
                                                <option value="143">Montserrat</option>
                                                <option value="144">Morocco</option>
                                                <option value="145">Mozambique</option>
                                                <option value="146">Myanmar</option>
                                                <option value="147">Namibia</option>
                                                <option value="148">Nauru</option>
                                                <option value="149">Nepal</option>
                                                <option value="150">Netherlands</option>
                                                <option value="151">Netherlands Antilles</option>
                                                <option value="152">New Caledonia</option>
                                                <option value="153">New Zealand</option>
                                                <option value="154">Nicaragua</option>
                                                <option value="155">Niger</option>
                                                <option value="156">Nigeria</option>
                                                <option value="157">Niue</option>
                                                <option value="158">Norfolk Island</option>
                                                <option value="112">North Korea</option>
                                                <option value="159">Northern Mariana Islands</option>
                                                <option value="160">Norway</option>
                                                <option value="161">Oman</option>
                                                <option value="162">Pakistan</option>
                                                <option value="163">Palau</option>
                                                <option value="247">Palestinian Territory, Occupied</option>
                                                <option value="164">Panama</option>
                                                <option value="165">Papua New Guinea</option>
                                                <option value="166">Paraguay</option>
                                                <option value="167">Peru</option>
                                                <option value="168">Philippines</option>
                                                <option value="169">Pitcairn</option>
                                                <option value="170">Poland</option>
                                                <option value="171">Portugal</option>
                                                <option value="172">Puerto Rico</option>
                                                <option value="173">Qatar</option>
                                                <option value="174">Reunion</option>
                                                <option value="175">Romania</option>
                                                <option value="176">Russian Federation</option>
                                                <option value="177">Rwanda</option>
                                                <option value="178">Saint Kitts and Nevis</option>
                                                <option value="179">Saint Lucia</option>
                                                <option value="180">Saint Vincent and the Grenadines</option>
                                                <option value="181">Samoa</option>
                                                <option value="182">San Marino</option>
                                                <option value="183">Sao Tome and Principe</option>
                                                <option value="184">Saudi Arabia</option>
                                                <option value="185">Senegal</option>
                                                <option value="243">Serbia</option>
                                                <option value="186">Seychelles</option>
                                                <option value="187">Sierra Leone</option>
                                                <option value="188">Singapore</option>
                                                <option value="189">Slovak Republic</option>
                                                <option value="190">Slovenia</option>
                                                <option value="191">Solomon Islands</option>
                                                <option value="192">Somalia</option>
                                                <option value="193">South Africa</option>
                                                <option value="194">South Georgia &amp; South Sandwich Islands</option>
                                                <option value="248">South Sudan</option>
                                                <option value="195">Spain</option>
                                                <option value="196">Sri Lanka</option>
                                                <option value="249">St. Barthelemy</option>
                                                <option value="197">St. Helena</option>
                                                <option value="250">St. Martin (French part)</option>
                                                <option value="198">St. Pierre and Miquelon</option>
                                                <option value="199">Sudan</option>
                                                <option value="200">Suriname</option>
                                                <option value="201">Svalbard and Jan Mayen Islands</option>
                                                <option value="202">Swaziland</option>
                                                <option value="203">Sweden</option>
                                                <option value="204">Switzerland</option>
                                                <option value="205">Syrian Arab Republic</option>
                                                <option value="206">Taiwan</option>
                                                <option value="207">Tajikistan</option>
                                                <option value="208">Tanzania, United Republic of</option>
                                                <option value="209">Thailand</option>
                                                <option value="210">Togo</option>
                                                <option value="211">Tokelau</option>
                                                <option value="212">Tonga</option>
                                                <option value="213">Trinidad and Tobago</option>
                                                <option value="214">Tunisia</option>
                                                <option value="215">Turkey</option>
                                                <option value="216">Turkmenistan</option>
                                                <option value="217">Turks and Caicos Islands</option>
                                                <option value="218">Tuvalu</option>
                                                <option value="219">Uganda</option>
                                                <option value="220">Ukraine</option>
                                                <option value="221">United Arab Emirates</option>
                                                <option value="222">United Kingdom</option>
                                                <option value="223">United States</option>
                                                <option value="224">United States Minor Outlying Islands</option>
                                                <option value="225">Uruguay</option>
                                                <option value="226">Uzbekistan</option>
                                                <option value="227">Vanuatu</option>
                                                <option value="228">Vatican City State (Holy See)</option>
                                                <option value="229">Venezuela</option>
                                                <option value="230">Viet Nam</option>
                                                <option value="231">Virgin Islands (British)</option>
                                                <option value="232">Virgin Islands (U.S.)</option>
                                                <option value="233">Wallis and Futuna Islands</option>
                                                <option value="234">Western Sahara</option>
                                                <option value="235">Yemen</option>
                                                <option value="238">Zambia</option>
                                                <option value="239">Zimbabwe</option>
                                                <select>
                                                    <i></i>
                                        </label>
                                    </section>

                                    <section class="col col-4">
                                        <label class="input">
                                            <input type="text" name="city" placeholder="City">
                                        </label>
                                    </section>

                                    <section class="col col-3">
                                        <label class="input">
                                            <input type="text" name="code" placeholder="Post code">
                                        </label>
                                    </section>
                                </div>

                                <section>
                                    <label for="file" class="input">
                                        <input type="text" name="address" placeholder="Address line 1">
                                    </label>
                                </section>

                                <section>
                                    <label for="file" class="input">
                                        <input type="text" name="address" placeholder="Address line 2">
                                    </label>
                                </section>

                                <section>
                                    <label for="file" class="input">
                                        <input type="text" name="address" placeholder="Address line 3">
                                    </label>
                                </section>


                            </fieldset>


                            <footer>
                                <button type="submit" class="like_button hide" id="_invoice_address_save_label">{$content._invoice_address_save_label}</button>
                            </footer>
                        </form>

                    </div>

                    <div id="_delivery_addresses_details" class="block hide reg_form">
                        <form action="" id="sky-form" class="sky-form">
                            <header class="mirror_master" id="_delivery_addresses_title">{$content._delivery_addresses_title}</header>


                            <section>
                                <label class="checkbox"><input onChange="change_delivery_addresses_same_as_invoice_label()" type="checkbox" checked name="subscription" id="subscription"><i></i> </label>
                                <span style="margin-left:27px;	" class="fake_form_checkbox" id="_delivery_addresses_same_as_invoice_label"
                                     >{$content._delivery_addresses_same_as_invoice_label}</span>


                            </section>


                            <fieldset id="new_delivery_address" class="hide">
                                <div class="row">
                                    <section class="col col-5">
                                        <label class="select">
                                            <select name="country">
                                                <option value="0" selected disabled>Country</option>
                                                <option value="244">Aaland Islands</option>
                                                <option value="1">Afghanistan</option>
                                                <option value="2">Albania</option>
                                                <option value="3">Algeria</option>
                                                <option value="4">American Samoa</option>
                                                <option value="5">Andorra</option>
                                                <option value="6">Angola</option>
                                                <option value="7">Anguilla</option>
                                                <option value="8">Antarctica</option>
                                                <option value="9">Antigua and Barbuda</option>
                                                <option value="10">Argentina</option>
                                                <option value="11">Armenia</option>
                                                <option value="12">Aruba</option>
                                                <option value="13">Australia</option>
                                                <option value="14">Austria</option>
                                                <option value="15">Azerbaijan</option>
                                                <option value="16">Bahamas</option>
                                                <option value="17">Bahrain</option>
                                                <option value="18">Bangladesh</option>
                                                <option value="19">Barbados</option>
                                                <option value="20">Belarus</option>
                                                <option value="21">Belgium</option>
                                                <option value="22">Belize</option>
                                                <option value="23">Benin</option>
                                                <option value="24">Bermuda</option>
                                                <option value="25">Bhutan</option>
                                                <option value="26">Bolivia</option>
                                                <option value="245">Bonaire, Sint Eustatius and Saba</option>
                                                <option value="27">Bosnia and Herzegovina</option>
                                                <option value="28">Botswana</option>
                                                <option value="29">Bouvet Island</option>
                                                <option value="30">Brazil</option>
                                                <option value="31">British Indian Ocean Territory</option>
                                                <option value="32">Brunei Darussalam</option>
                                                <option value="33">Bulgaria</option>
                                                <option value="34">Burkina Faso</option>
                                                <option value="35">Burundi</option>
                                                <option value="36">Cambodia</option>
                                                <option value="37">Cameroon</option>
                                                <option value="38">Canada</option>
                                                <option value="251">Canary Islands</option>
                                                <option value="39">Cape Verde</option>
                                                <option value="40">Cayman Islands</option>
                                                <option value="41">Central African Republic</option>
                                                <option value="42">Chad</option>
                                                <option value="43">Chile</option>
                                                <option value="44">China</option>
                                                <option value="45">Christmas Island</option>
                                                <option value="46">Cocos (Keeling) Islands</option>
                                                <option value="47">Colombia</option>
                                                <option value="48">Comoros</option>
                                                <option value="49">Congo</option>
                                                <option value="50">Cook Islands</option>
                                                <option value="51">Costa Rica</option>
                                                <option value="52">Cote D'Ivoire</option>
                                                <option value="53">Croatia</option>
                                                <option value="54">Cuba</option>
                                                <option value="246">Curacao</option>
                                                <option value="55">Cyprus</option>
                                                <option value="56">Czech Republic</option>
                                                <option value="237">Democratic Republic of Congo</option>
                                                <option value="57">Denmark</option>
                                                <option value="58">Djibouti</option>
                                                <option value="59">Dominica</option>
                                                <option value="60">Dominican Republic</option>
                                                <option value="61">East Timor</option>
                                                <option value="62">Ecuador</option>
                                                <option value="63">Egypt</option>
                                                <option value="64">El Salvador</option>
                                                <option value="65">Equatorial Guinea</option>
                                                <option value="66">Eritrea</option>
                                                <option value="67">Estonia</option>
                                                <option value="68">Ethiopia</option>
                                                <option value="69">Falkland Islands (Malvinas)</option>
                                                <option value="70">Faroe Islands</option>
                                                <option value="71">Fiji</option>
                                                <option value="72">Finland</option>
                                                <option value="74">France, skypolitan</option>
                                                <option value="75">French Guiana</option>
                                                <option value="76">French Polynesia</option>
                                                <option value="77">French Southern Territories</option>
                                                <option value="126">FYROM</option>
                                                <option value="78">Gabon</option>
                                                <option value="79">Gambia</option>
                                                <option value="80">Georgia</option>
                                                <option value="81">Germany</option>
                                                <option value="82">Ghana</option>
                                                <option value="83">Gibraltar</option>
                                                <option value="84">Greece</option>
                                                <option value="85">Greenland</option>
                                                <option value="86">Grenada</option>
                                                <option value="87">Guadeloupe</option>
                                                <option value="88">Guam</option>
                                                <option value="89">Guatemala</option>
                                                <option value="241">Guernsey</option>
                                                <option value="90">Guinea</option>
                                                <option value="91">Guinea-Bissau</option>
                                                <option value="92">Guyana</option>
                                                <option value="93">Haiti</option>
                                                <option value="94">Heard and Mc Donald Islands</option>
                                                <option value="95">Honduras</option>
                                                <option value="96">Hong Kong</option>
                                                <option value="97">Hungary</option>
                                                <option value="98">Iceland</option>
                                                <option value="99">India</option>
                                                <option value="100">Indonesia</option>
                                                <option value="101">Iran (Islamic Republic of)</option>
                                                <option value="102">Iraq</option>
                                                <option value="103">Ireland</option>
                                                <option value="104">Israel</option>
                                                <option value="105">Italy</option>
                                                <option value="106">Jamaica</option>
                                                <option value="107">Japan</option>
                                                <option value="240">Jersey</option>
                                                <option value="108">Jordan</option>
                                                <option value="109">Kazakhstan</option>
                                                <option value="110">Kenya</option>
                                                <option value="111">Kiribati</option>
                                                <option value="113">Korea, Republic of</option>
                                                <option value="114">Kuwait</option>
                                                <option value="115">Kyrgyzstan</option>
                                                <option value="116">Lao People's Democratic Republic</option>
                                                <option value="117">Latvia</option>
                                                <option value="118">Lebanon</option>
                                                <option value="119">Lesotho</option>
                                                <option value="120">Liberia</option>
                                                <option value="121">Libyan Arab Jamahiriya</option>
                                                <option value="122">Liechtenstein</option>
                                                <option value="123">Lithuania</option>
                                                <option value="124">Luxembourg</option>
                                                <option value="125">Macau</option>
                                                <option value="127">Madagascar</option>
                                                <option value="128">Malawi</option>
                                                <option value="129">Malaysia</option>
                                                <option value="130">Maldives</option>
                                                <option value="131">Mali</option>
                                                <option value="132">Malta</option>
                                                <option value="133">Marshall Islands</option>
                                                <option value="134">Martinique</option>
                                                <option value="135">Mauritania</option>
                                                <option value="136">Mauritius</option>
                                                <option value="137">Mayotte</option>
                                                <option value="138">Mexico</option>
                                                <option value="139">Micronesia, Federated States of</option>
                                                <option value="140">Moldova, Republic of</option>
                                                <option value="141">Monaco</option>
                                                <option value="142">Mongolia</option>
                                                <option value="242">Montenegro</option>
                                                <option value="143">Montserrat</option>
                                                <option value="144">Morocco</option>
                                                <option value="145">Mozambique</option>
                                                <option value="146">Myanmar</option>
                                                <option value="147">Namibia</option>
                                                <option value="148">Nauru</option>
                                                <option value="149">Nepal</option>
                                                <option value="150">Netherlands</option>
                                                <option value="151">Netherlands Antilles</option>
                                                <option value="152">New Caledonia</option>
                                                <option value="153">New Zealand</option>
                                                <option value="154">Nicaragua</option>
                                                <option value="155">Niger</option>
                                                <option value="156">Nigeria</option>
                                                <option value="157">Niue</option>
                                                <option value="158">Norfolk Island</option>
                                                <option value="112">North Korea</option>
                                                <option value="159">Northern Mariana Islands</option>
                                                <option value="160">Norway</option>
                                                <option value="161">Oman</option>
                                                <option value="162">Pakistan</option>
                                                <option value="163">Palau</option>
                                                <option value="247">Palestinian Territory, Occupied</option>
                                                <option value="164">Panama</option>
                                                <option value="165">Papua New Guinea</option>
                                                <option value="166">Paraguay</option>
                                                <option value="167">Peru</option>
                                                <option value="168">Philippines</option>
                                                <option value="169">Pitcairn</option>
                                                <option value="170">Poland</option>
                                                <option value="171">Portugal</option>
                                                <option value="172">Puerto Rico</option>
                                                <option value="173">Qatar</option>
                                                <option value="174">Reunion</option>
                                                <option value="175">Romania</option>
                                                <option value="176">Russian Federation</option>
                                                <option value="177">Rwanda</option>
                                                <option value="178">Saint Kitts and Nevis</option>
                                                <option value="179">Saint Lucia</option>
                                                <option value="180">Saint Vincent and the Grenadines</option>
                                                <option value="181">Samoa</option>
                                                <option value="182">San Marino</option>
                                                <option value="183">Sao Tome and Principe</option>
                                                <option value="184">Saudi Arabia</option>
                                                <option value="185">Senegal</option>
                                                <option value="243">Serbia</option>
                                                <option value="186">Seychelles</option>
                                                <option value="187">Sierra Leone</option>
                                                <option value="188">Singapore</option>
                                                <option value="189">Slovak Republic</option>
                                                <option value="190">Slovenia</option>
                                                <option value="191">Solomon Islands</option>
                                                <option value="192">Somalia</option>
                                                <option value="193">South Africa</option>
                                                <option value="194">South Georgia &amp; South Sandwich Islands</option>
                                                <option value="248">South Sudan</option>
                                                <option value="195">Spain</option>
                                                <option value="196">Sri Lanka</option>
                                                <option value="249">St. Barthelemy</option>
                                                <option value="197">St. Helena</option>
                                                <option value="250">St. Martin (French part)</option>
                                                <option value="198">St. Pierre and Miquelon</option>
                                                <option value="199">Sudan</option>
                                                <option value="200">Suriname</option>
                                                <option value="201">Svalbard and Jan Mayen Islands</option>
                                                <option value="202">Swaziland</option>
                                                <option value="203">Sweden</option>
                                                <option value="204">Switzerland</option>
                                                <option value="205">Syrian Arab Republic</option>
                                                <option value="206">Taiwan</option>
                                                <option value="207">Tajikistan</option>
                                                <option value="208">Tanzania, United Republic of</option>
                                                <option value="209">Thailand</option>
                                                <option value="210">Togo</option>
                                                <option value="211">Tokelau</option>
                                                <option value="212">Tonga</option>
                                                <option value="213">Trinidad and Tobago</option>
                                                <option value="214">Tunisia</option>
                                                <option value="215">Turkey</option>
                                                <option value="216">Turkmenistan</option>
                                                <option value="217">Turks and Caicos Islands</option>
                                                <option value="218">Tuvalu</option>
                                                <option value="219">Uganda</option>
                                                <option value="220">Ukraine</option>
                                                <option value="221">United Arab Emirates</option>
                                                <option value="222">United Kingdom</option>
                                                <option value="223">United States</option>
                                                <option value="224">United States Minor Outlying Islands</option>
                                                <option value="225">Uruguay</option>
                                                <option value="226">Uzbekistan</option>
                                                <option value="227">Vanuatu</option>
                                                <option value="228">Vatican City State (Holy See)</option>
                                                <option value="229">Venezuela</option>
                                                <option value="230">Viet Nam</option>
                                                <option value="231">Virgin Islands (British)</option>
                                                <option value="232">Virgin Islands (U.S.)</option>
                                                <option value="233">Wallis and Futuna Islands</option>
                                                <option value="234">Western Sahara</option>
                                                <option value="235">Yemen</option>
                                                <option value="238">Zambia</option>
                                                <option value="239">Zimbabwe</option>
                                                <select>
                                                    <i></i>
                                        </label>
                                    </section>

                                    <section class="col col-4">
                                        <label class="input">
                                            <input type="text" name="city" placeholder="City">
                                        </label>
                                    </section>

                                    <section class="col col-3">
                                        <label class="input">
                                            <input type="text" name="code" placeholder="Post code">
                                        </label>
                                    </section>
                                </div>

                                <section>
                                    <label for="file" class="input">
                                        <input type="text" name="address" placeholder="Address line 1">
                                    </label>
                                </section>

                                <section>
                                    <label for="file" class="input">
                                        <input type="text" name="address" placeholder="Address line 2">
                                    </label>
                                </section>

                                <section>
                                    <label for="file" class="input">
                                        <input type="text" name="address" placeholder="Address line 3">
                                    </label>
                                </section>


                            </fieldset>


                            <footer>
                                <button type="submit" class="like_button hide" id="_delivery_addresses_save_label">{$content._delivery_addresses_save_label}</button>
                            </footer>
                        </form>

                    </div>




                    <div id="_orders" class="block hide">

                        <h3 class="mirror_master" id="_orders_title">{$content._orders_title}</h3>

                        <table class="table">
                            <thead>
                            <tr>
                                <th>{t}Number{/t}</th>
                                <th>{t}Date{/t}</th>
                                <th>{t}Status{/t}</th>
                                <th class="text-right">{t}Total{/t}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="like_button">88792</td>
                                <td>{"yesterday"|date_format:"%A, %e %B %Y"}</td>
                                <td>{t}Dispatched{/t}</td>
                                <td class="text-right">£120.40</td>
                            </tr>
                            <tr>
                                <td class="like_button">88233</td>
                                <td>{"-50 days"|date_format:"%A, %e %B %Y"}</td>
                                <td>{t}Dispatched{/t}</td>
                                <td class="text-right">£600.00</td>
                            </tr>
                            <tr>
                                <td class="like_button">87989</td>
                                <td>{"-100 days"|date_format:"%A, %e %B %Y"}</td>
                                <td>{t}Dispatched{/t}</td>
                                <td class="text-right">£75.50</td>
                            </tr>
                            </tbody>
                        </table>


                    </div>


                </div>


            </div>
        </div>


        <div class="clearfix marb12"></div>

        {include file="theme_1/footer.EcomB2B.tpl"}

    </div>

</div>
<script>


    function change_block(element) {

        $('.block').addClass('hide')
        $('#' + $(element).attr('block')).removeClass('hide')

        $('.sidebar_widget .block_link').removeClass('selected')
        $(element).addClass('selected')
    }


    $( "#country_select" ).change(function() {

        var selected=$( "#country_select option:selected" )
       // console.log(selected.val())

        var request= "ar_web_addressing.php?tipo=address_format&country_code="+selected.val()+'&website_key={$website->id}'

        console.log(request)
        $.getJSON(request, function( data ) {
            console.log(data)
            $.each(data.hidden_fields, function(index, value) {
                $('#'+value).addClass('hide')
                $('#'+value).find('input').addClass('ignore')

            });

            $.each(data.used_fields, function(index, value) {
                $('#'+value).removeClass('hide')
                $('#'+value).find('input').removeClass('ignore')

            });

            $.each(data.labels, function(index, value) {
                $('#'+index).find('input').attr('placeholder',value)
                $('#'+index).find('b').html(value)

            });

            $.each(data.no_required_fields, function(index, value) {


               // console.log(value)

                    $('#'+value+' input').rules( "remove" );




            });

            $.each(data.required_fields, function(index, value) {
                console.log($('#'+value))
                //console.log($('#'+value+' input').rules())

                $('#'+value+' input').rules( "add", { required: true});

            });


        });


    });


    $("form").on('submit', function (e) {

        e.preventDefault();
        e.returnValue = false;

    });


    $("#contact_details").validate(
        {

            submitHandler: function(form)
            {


                var register_data={ }

                $("#contact_details input:not(.ignore)").each(function(i, obj) {
                    if(!$(obj).attr('name')==''){
                        register_data[$(obj).attr('name')]=$(obj).val()
                    }

                });

                $("#contact_details select:not(.ignore)").each(function(i, obj) {
                    if(!$(obj).attr('name')==''){


                        register_data[$(obj).attr('name')]=$(obj).val()
                    }

                });



                var ajaxData = new FormData();

                ajaxData.append("tipo", 'contact_details')
                ajaxData.append("data", JSON.stringify(register_data))


                $.ajax({
                    url: "/ar_web_profile.php", type: 'POST', data: ajaxData, dataType: 'json', cache: false, contentType: false, processData: false,
                    complete: function () {
                    }, success: function (data) {

                        console.log(data)

                        if (data.state == '200') {





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

                    email:
                        {
                            required: true,
                            email: true,
                            remote: {
                                url: "ar_web_validate.php",
                                data: {
                                    tipo:'validate_update_email'
                                }
                            }

                        },

                    contact_name:
                        {
                            required: true,

                        },
                    mobile:
                        {
                            required: true,

                        },


                },

            // Messages for form validation
            messages:
                {

                    email:
                        {
                            required: '{if empty($labels._validation_required)}{t}Required field{/t}{else}{$labels._validation_required|escape}{/if}',
                            email: '{if empty($labels._validation_email_invalid)}{t}Invalid email{/t}{else}{$labels._validation_email_invalid|escape}{/if}',
                            remote: '{if empty($labels._validation_handle_registered)}{t}Email address is already in registered{/t}{else}{$labels._validation_handle_registered|escape}{/if}',



                        },

                    contact_name:
                        {
                            required: '{if empty($labels._validation_required)}{t}Required field{/t}{else}{$labels._validation_required|escape}{/if}',
                        },
                    mobile:
                        {
                            required: '{if empty($labels._validation_required)}{t}Required field{/t}{else}{$labels._validation_required|escape}{/if}',
                        }




                },

            // Do not change code below
            errorPlacement: function(error, element)
            {
                error.insertAfter(element.parent());
            }
        });


    $("#login_details").validate(
        {

            submitHandler: function(form)
            {


                var register_data={ }

                $("#login_details input:not(.ignore)").each(function(i, obj) {
                    if(!$(obj).attr('name')==''){
                        register_data[$(obj).attr('name')]=$(obj).val()
                    }

                });

                $("#login_details select:not(.ignore)").each(function(i, obj) {
                    if(!$(obj).attr('name')==''){


                        register_data[$(obj).attr('name')]=$(obj).val()
                    }

                });


              //  register_data['pwd']=sha256_digest(register_data['pwd']);

                var ajaxData = new FormData();

                ajaxData.append("tipo", 'update_password')
                ajaxData.append("pwd", sha256_digest(register_data['pwd']))


                $.ajax({
                    url: "/ar_web_profile.php", type: 'POST', data: ajaxData, dataType: 'json', cache: false, contentType: false, processData: false,
                    complete: function () {
                    }, success: function (data) {

                        console.log(data)

                        if (data.state == '200') {




                        } else if (data.state == '400') {
                            swal("{t}Error{/t}!", data.msg, "error")
                        }



                    }, error: function () {

                    }
                });


            },

            // Rules for form validation
            rules: {


                password: {
                    required: true, minlength: 8


                }, password_confirm: {
                    required: true, minlength: 8, equalTo: "#password"
                },
            },

    // Messages for form validation
    messages:
    {


        password:
        {


            required: '{if empty($labels._validation_required)}{t}Required field{/t}{else}{$labels._validation_required|escape}{/if}',
            minlength: '{if empty($labels._validation_minlength_password)}{t}Enter at least 8 characters{/t}{else}{$labels._validation_minlength_password|escape}{/if}',



        },
        password_confirm:
        {
            required: '{if empty($labels._validation_required)}{t}Required field{/t}{else}{$labels._validation_required|escape}{/if}',
            equalTo: '{if empty($labels._validation_same_password)}{t}Enter the same password as above{/t}{else}{$labels._validation_same_password|escape}{/if}',

            minlength: '{if empty($labels._validation_minlength_password)}{t}Enter at least 8 characters{/t}{else}{$labels._validation_minlength_password|escape}{/if}',
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

