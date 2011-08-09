<link rel="stylesheet" type="text/css" href="../top_navigation.css" />
<script type="text/javascript" src="../external_libs/yui/2.9/build/utilities/utilities.js"></script>
<script type="text/javascript" src="../external_libs/yui/2.9/build/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="../external_libs/yui/2.9/build/json/json-min.js"></script>
<script type="text/javascript" src="../external_libs/yui/2.9/build/element/element-min.js"></script>

<script type="text/javascript" src="../js/sha256.js"></script>
<script type="text/javascript" src="../js/aes.js"></script>

<script type="text/javascript" src="../js/login.js"></script>
<script type="text/javascript" src="../basket.js"></script>

<?php if($logged_in){?>
<script type="text/javascript" src="../top_navigation_login.js.php"></script>
<?php }else{?>
<script type="text/javascript" src="../top_navigation_logout.js.php"></script>
<?php }?>

<input type="hidden" value="<?php echo $store_key?>" id="store_key">
<input type="hidden" value="<?php echo $site->id?>" id="site_key">

<?php include('/conf/checkout.php');?>

<div id="top_navigator" style="width:865px;vertical-align:bottom;">
<?php if($logged_in){?>


<div style="margin-left:10px;width:500px;float:left;text-align:left;xborder:1px solid red;">
 
<img style="height:22px;vertical-align:-6px;" src="../art/basket.jpg"/>
 Items: <span id="basket_items"><?php echo number($_SESSION['basket']['items'])?></span> Total: <span id="basket_total"><?php echo money($_SESSION['basket']['total'])?></span>  
 <span class="link" style="margin-left:15px;" id="see_basket"  link="<?php echo $ecommerce_basket?>" >See Basket</span> 
  <span class="link" style="margin-left:10px;"  id="checkout" link="<?php echo $ecommerce_checkout?>">Check Out</span> 

 
</div>

<span>Hello, <?php print $user->data['User Alias']." (".$customer->id.")"?></span>
 <span style="margin-left:15px;" class="link" id="logout">Log Out</span>
<img src="../art/gear.png" style="cursor:pointer;margin-left:10px;height:22px;vertical-align:-6px;">

<?php }else{ ?>
<button id="show_register_dialog">Register</button>
<button id="show_login_dialog">Log In</button>
<img src="../art/gear.png" style="visibility:hidden;height:22px;vertical-align:-6px;">

<?php } ?>

</div>

<?php if($logged_in){?>







<?php }else{?>

<div id="dialog_login"    class="dialog"    style="position:relative;left:515px;top:-22px;width:350px">
<input type="hidden" value="<?php echo $St?>" id="ep">
<h2>Login</h2>
<table style="margin-bottom:10px">


<tr id="login_from_email_found" style="display:none"><td class="label"></td><td>Email found in our database</td></tr>


<tr><td class="label">Email: </td><td><input id="login_handle"></td></tr>
<tr><td  class="label">Password: </td><td><input type="password"  id="login_password"></td></tr>
<tr class="button" style=""><td colspan="2"><span id="invalid_credentials" style="display:none">Wrong credentials!</span>  <button id="submit_login">Log In</button> <button id="hide_login_dialog">Close</button> </td></tr>
<tr class="link"><td colspan=2>Forgot your password? <span class="link"   id="link_forgot_password_from_login" >Click Here</span></td></tr>
<tr id="tr_link_register_from_login" class="link"><td colspan=2>First visit? <span class="link" id="link_register_from_login">Register Here</span></td></tr>
<tr id="tr_link_register_from_login2" class="link"><td colspan=2>Use another email, <span class="link" id="link_register_from_login2">Register Here</span></td></tr>

</table>
</div>

<div id="dialog_register"    class="dialog"    style="position:relative;left:515px;width:350px;top:-22px;">
<h2>Registration</h2>
<table>
<tr><td class="label">Email: </td><td><input id="register_email"></td></tr>
<tr class="button" ><td colspan=2 > <button id="submit_check_email">Continue</button> <button id="hide_register_dialog">Close</button></td></tr>


</table>
</div>


<div id="dialog_register_part_2"    class="dialog"    style="display:block;position:relative;left:450px;width:415px;top:-22px;">
<h2>Registration</h2>
<table>
<tr class="title" ><td colspan="3">Login info: </td></tr>

<tr><td class="label">Email: </td><td id="confirmed_register_email"></td><td style="width:10px"></td></tr>
<tr><td class="label">Password: </td><td><input type="password" id="register_password1"></td></tr>
<tr><td class="label">Confirm pwd: </td><td><input type="password" id="register_password2"></td></tr>
<input id="epw2" value="" type="hidden"/>

<tr class="title" ><td colspan="3">Contact Info: </td></tr>
<tr><td class="label">Contact Name: </td><td><input id="register_contact_name"></td></tr>

<tr><td class="label">Company: </td><td><input id="register_company_name"></td></tr>
<tr><td class="label">Telephone: </td><td><input id="register_telephone"></td></tr>

<tbody id="tbody_register_address"  >
<tr class="title" ><td colspan="3">Address: </td></tr>

<tr><td  class="label">Line 1: </td><td><input id="register_address_line1"/></td></tr>
<tr><td class="label">Line 2: </td><td><input id="register_address_line2"/></td></tr>
<tr><td class="label">Town: </td><td><input id="register_address_town"/></td></tr>
<tr><td class="label">Postal Code: </td><td><input id="register_address_postcode"/></td></tr>

<tr><td class="label">Country: </td><td><select size="1" id="register_address_country_2alpha_code" >
<option value="XX">Select One</option>
<option value="GB">United Kingdom</option>
<option value="XX">----------</option>
<option value="AF">Afghanistan</option>
<option value="AL">Albania</option>
<option value="DZ">Algeria</option>
<option value="AS">American Samoa</option>
<option value="AD">Andorra</option>
<option value="AO">Angola</option>
<option value="AI">Anguilla</option>
<option value="AQ">Antarctica</option>
<option value="AG">Antigua and Barbuda</option>
<option value="AR">Argentina</option>
<option value="AM">Armenia</option>
<option value="AW">Aruba</option>
<option value="AU">Australia</option>
<option value="AT">Austria</option>
<option value="AZ">Azerbaidjan</option>
<option value="BS">Bahamas</option>
<option value="BH">Bahrain</option>
<option value="BD">Bangladesh</option>
<option value="BB">Barbados</option>
<option value="BY">Belarus</option>
<option value="BE">Belgium</option>
<option value="BZ">Belize</option>
<option value="BJ">Benin</option>
<option value="BM">Bermuda</option>
<option value="BT">Bhutan</option>
<option value="BO">Bolivia</option>
<option value="BA">Bosnia-Herzegovina</option>
<option value="BW">Botswana</option>
<option value="BV">Bouvet Island</option>
<option value="BR">Brazil</option>
<option value="IO">British Indian Ocean Territory</option>
<option value="BN">Brunei Darussalam</option>
<option value="BG">Bulgaria</option>
<option value="BF">Burkina Faso</option>
<option value="BI">Burundi</option>
<option value="KH">Cambodia</option>
<option value="CM">Cameroon</option>
<option value="CA">Canada</option>

<option value="CV">Cape Verde</option>
<option value="KY">Cayman Islands</option>
<option value="CF">Central African Republic</option>
<option value="TD">Chad</option>
<option value="CL">Chile</option>
<option value="CN">China</option>
<option value="CX">Christmas Island</option>
<option value="CC">Cocos (Keeling) Islands</option>
<option value="CO">Colombia</option>
<option value="KM">Comoros</option>
<option value="CG">Congo</option>
<option value="CK">Cook Islands</option>
<option value="CR">Costa Rica</option>
<option value="HR">Croatia</option>
<option value="CU">Cuba</option>
<option value="CY">Cyprus</option>
<option value="CZ">Czech Republic</option>
<option value="DK">Denmark</option>
<option value="DJ">Djibouti</option>
<option value="DM">Dominica</option>
<option value="DO">Dominican Republic</option>
<option value="TP">East Timor</option>
<option value="EC">Ecuador</option>
<option value="EG">Egypt</option>
<option value="SV">El Salvador</option>
<option value="GQ">Equatorial Guinea</option>
<option value="ER">Eritrea</option>
<option value="EE">Estonia</option>
<option value="ET">Ethiopia</option>
<option value="FK">Falkland Islands</option>
<option value="FO">Faroe Islands</option>
<option value="FJ">Fiji</option>
<option value="FI">Finland</option>
<option value="CS">Former Czechoslovakia</option>
<option value="SU">Former USSR</option>
<option value="FR">France</option>
<option value="FX">France (European Territory)</option>
<option value="GF">French Guyana</option>
<option value="TF">French Southern Territories</option>
<option value="GA">Gabon</option>
<option value="GM">Gambia</option>
<option value="GE">Georgia</option>
<option value="DE">Germany</option>
<option value="GH">Ghana</option>
<option value="GI">Gibraltar</option>
<option value="GB">Great Britain</option>
<option value="GR">Greece</option>
<option value="GL">Greenland</option>
<option value="GD">Grenada</option>
<option value="GP">Guadeloupe (French)</option>
<option value="GU">Guam (USA)</option>
<option value="GT">Guatemala</option>
<option value="GN">Guinea</option>
<option value="GW">Guinea Bissau</option>
<option value="GY">Guyana</option>
<option value="HT">Haiti</option>
<option value="HM">Heard and McDonald Islands</option>
<option value="HN">Honduras</option>
<option value="HK">Hong Kong</option>
<option value="HU">Hungary</option>
<option value="IS">Iceland</option>
<option value="IN">India</option>
<option value="ID">Indonesia</option>
<option value="INT">International</option>
<option value="IR">Iran</option>
<option value="IQ">Iraq</option>
<option value="IE">Ireland</option>
<option value="IL">Israel</option>
<option value="IT">Italy</option>
<option value="CI">Ivory Coast (Cote D&#39;Ivoire)</option>
<option value="JM">Jamaica</option>
<option value="JP">Japan</option>
<option value="JO">Jordan</option>
<option value="KZ">Kazakhstan</option>
<option value="KE">Kenya</option>
<option value="KI">Kiribati</option>
<option value="KW">Kuwait</option>
<option value="KG">Kyrgyzstan</option>
<option value="LA">Laos</option>
<option value="LV">Latvia</option>
<option value="LB">Lebanon</option>
<option value="LS">Lesotho</option>
<option value="LR">Liberia</option>
<option value="LY">Libya</option>
<option value="LI">Liechtenstein</option>
<option value="LT">Lithuania</option>
<option value="LU">Luxembourg</option>
<option value="MO">Macau</option>
<option value="MK">Macedonia</option>
<option value="MG">Madagascar</option>
<option value="MW">Malawi</option>
<option value="MY">Malaysia</option>
<option value="MV">Maldives</option>
<option value="ML">Mali</option>
<option value="MT">Malta</option>
<option value="MH">Marshall Islands</option>
<option value="MQ">Martinique (French)</option>
<option value="MR">Mauritania</option>
<option value="MU">Mauritius</option>
<option value="YT">Mayotte</option>
<option value="MX">Mexico</option>
<option value="FM">Micronesia</option>
<option value="MD">Moldavia</option>
<option value="MC">Monaco</option>
<option value="MN">Mongolia</option>
<option value="MS">Montserrat</option>
<option value="MA">Morocco</option>
<option value="MZ">Mozambique</option>
<option value="MM">Myanmar</option>
<option value="NA">Namibia</option>
<option value="NR">Nauru</option>
<option value="NP">Nepal</option>
<option value="NL">Netherlands</option>
<option value="AN">Netherlands Antilles</option>
<option value="NT">Neutral Zone</option>
<option value="NC">New Caledonia (French)</option>
<option value="NZ">New Zealand</option>
<option value="NI">Nicaragua</option>
<option value="NE">Niger</option>
<option value="NG">Nigeria</option>
<option value="NU">Niue</option>
<option value="NF">Norfolk Island</option>
<option value="KP">North Korea</option>
<option value="MP">Northern Mariana Islands</option>
<option value="NO">Norway</option>
<option value="OM">Oman</option>
<option value="PK">Pakistan</option>
<option value="PW">Palau</option>
<option value="PA">Panama</option>
<option value="PG">Papua New Guinea</option>
<option value="PY">Paraguay</option>
<option value="PE">Peru</option>
<option value="PH">Philippines</option>
<option value="PN">Pitcairn Island</option>
<option value="PL">Poland</option>
<option value="PF">Polynesia (French)</option>
<option value="PT">Portugal</option>
<option value="PR">Puerto Rico</option>
<option value="QA">Qatar</option>
<option value="RE">Reunion (French)</option>
<option value="RO">Romania</option>
<option value="RU">Russian Federation</option>
<option value="RW">Rwanda</option>
<option value="GS">S. Georgia & S. Sandwich I.</option>
<option value="SH">Saint Helena</option>
<option value="KN">Saint Kitts & Nevis Anguilla</option>
<option value="LC">Saint Lucia</option>
<option value="PM">St Pierre and Miquelon</option>
<option value="ST">St Tome & Principe</option>
<option value="VC">St Vincent & Grenadines</option>
<option value="WS">Samoa</option>
<option value="SM">San Marino</option>
<option value="SA">Saudi Arabia</option>
<option value="SN">Senegal</option>
<option value="SC">Seychelles</option>
<option value="SL">Sierra Leone</option>
<option value="SG">Singapore</option>
<option value="SK">Slovak Republic</option>
<option value="SI">Slovenia</option>
<option value="SB">Solomon Islands</option>
<option value="SO">Somalia</option>
<option value="ZA">South Africa</option>
<option value="KR">South Korea</option>
<option value="ES">Spain</option>
<option value="LK">Sri Lanka</option>
<option value="SD">Sudan</option>
<option value="SR">Suriname</option>
<option value="SJ">Svalbard and Jan Mayen I.</option>
<option value="SZ">Swaziland</option>
<option value="SE">Sweden</option>
<option value="CH">Switzerland</option>
<option value="SY">Syria</option>
<option value="TJ">Tadjikistan</option>
<option value="TW">Taiwan</option>
<option value="TZ">Tanzania</option>
<option value="TH">Thailand</option>
<option value="TG">Togo</option>
<option value="TK">Tokelau</option>
<option value="TO">Tonga</option>
<option value="TT">Trinidad and Tobago</option>
<option value="TN">Tunisia</option>
<option value="TR">Turkey</option>
<option value="TM">Turkmenistan</option>
<option value="TC">Turks and Caicos Islands</option>
<option value="TV">Tuvalu</option>
<option value="UG">Uganda</option>
<option value="UA">Ukraine</option>
<option value="AE">United Arab Emirates</option>
<option value="GB">United Kingdom</option>
<option value="US">United States</option>

<option value="UY">Uruguay</option>

<option value="UM">USA Minor Outlying Islands</option>
<option value="UZ">Uzbekistan</option>
<option value="VU">Vanuatu</option>
<option value="VA">Vatican City State</option>
<option value="VE">Venezuela</option>
<option value="VN">Vietnam</option>
<option value="VG">Virgin Islands (British)</option>
<option value="VI">Virgin Islands (USA)</option>
<option value="WF">Wallis and Futuna Islands</option>
<option value="EH">Western Sahara</option>
<option value="YE">Yemen</option>
<option value="YU">Yugoslavia</option>
<option value="ZR">Zaire</option>
<option value="ZM">Zambia</option>
<option value="ZW">Zimbabwe</option>
</select></td></tr>

<tr class="title" ><td colspan="3">Other: </td></tr>

<?php
$categories=array();
include_once('classes/class.Category.php');

$sql=sprintf("select `Category Key` from `Category Dimension` where `Category Subject`='Customer' and `Category Deep`=1 and `Category Store Key`=%d",$store_key);
$res=mysql_query($sql);
while($row=mysql_fetch_assoc($res)){
$tmp=new Category($row['Category Key']);
$categories[$row['Category Key']]=$tmp;
}

foreach($categories as $cat_key=>$cat){
print '<tr><td class="label">'.$cat->get('Category Label').':</td><td><select id="'.$cat_key.'" cat_key="'.$cat_key.'"  onChange="update_category(this)">';
  $count=1;
  foreach($cat->get_children_objects() as $sub_cat_key=>$sub_cat){
  
  if($count==1){
    print ' <option  value="">Unknown</option>';
  }
  $count++;
  print ' <option value="'.$sub_cat->get('Category Key').'">'.$sub_cat->get('Category Label').'</option>';
  
    
       

};
 print ' </select>
  
 </td>   
</tr>';
}




?>


<tr><td  class="label">Catalogue (post): </td><td><input checked=checked onChange="change_allow(this)"  style="width:10px" type="radio" name="catalogue" value="Yes">Yes<input onChange="change_allow(this)"  style="width:10px" type="radio"  name="catalogue" value="No">No   </td></tr>
<tr><td  class="label">Offers by email & e-Newsletter: </td><td style="vertical-align:top;"><input checked=checked onChange="change_allow(this)" style="width:10px" type="radio" name="newsletter" value="Yes">Yes<input onChange="change_allow(this)"  style="width:10px" type="radio"  name="newsletter" value="No">No   </td></tr>


<tr>
<td class="label">



<img id="captcha" src="../securimage/securimage_show.php" alt="CAPTCHA Image" /> <br>
<a href="#" onclick="document.getElementById('captcha').src = '../securimage/securimage_show.php?' + Math.random(); return false">[Different Image]</a>

</td>
<td>
<input type="text" id="captcha_code" name="captcha_code" size="3" maxlength="3" />

</td>
</tr>

<tr  id="tr_register_part_2_buttons"  class="button" ><td colspan=2><span style="display:none" id="register_error_no_password">Please, create a password</span><span style="display:none" id="register_error_password_not_march">Passwords don't match</span><span style="display:none" id="register_error_password_too_short">Password is too short</span><span>
</span><button id="submit_register">Register</button> <button id="hide_register_part_2_dialog">Cancel</button></td></tr>
<tr id="tr_register_part_2_wait"  style="display:none" class="button" ><td colspan=2><img style="weight:24px" src="../art/wait.gif"> <span style="position:relative;top:-5px">Creating your account</span></td></tr>


</table>
</div>


<div id="dialog_forgot_password"    class="dialog"    style="position:relative;left:515px;width:350px;top:-22px;">
<h2>Forgotten password</h2>
<table  style="width:290px">
<tr><td  class="label">Email: </td><td><input id="forgot_password_handle"></td></tr>
<tr></tr>
<tr id="tr_forgot_password_buttons" class="button" style="">
<tr>
<td class="label">



<img id="captcha2" src="../securimage/securimage_show.php" alt="CAPTCHA Image" /> <br>
<a href="#" onclick="document.getElementById('captcha2').src = '../securimage/securimage_show.php?' + Math.random(); return false">[Different Image]</a>

</td>
<td>
<input type="text" id="captcha_code2" name="captcha_code2" size="3" maxlength="3" />

</td>
</tr>

<td><button id="submit_forgot_password">Continue</button> <button id="hide_forgot_password_dialog">Close</button></td></tr>
<tr id="tr_forgot_password_wait"  style="display:none" class="button" ><td colspan=2><img style="weight:24px" src="../art/wait.gif"> <span style="position:relative;top:-5px">processing request</span></td></tr>
<tr id="tr_forgot_password_send" style="display:none" class="button" style=""><td colspan=2>An email has send to you with instructions how to access your account <br><br><button style="margin-bottom:10px" id="hide_forgot_password_dialog2">Close</button></td></tr>
<tr id="tr_forgot_password_error" style="display:none" class="button" style=""><td colspan=2>Sorry, an automatic password reset could not be done, try later or call us.<br><br><button style="margin-bottom:10px" id="hide_forgot_password_dialog3">Close</button></td></tr>
<tr id="tr_forgot_password_not_found" style="display:none" class="button" style=""><td colspan=2>Sorry, that email is not in our records. <br><br><span class="link"   id="link_register_from_forgot_password" >Register here</span> <button style="margin-bottom:10px" id="hide_forgot_password_dialog4">Close</button></td></tr>

</table>
</div>
<?php }
include_once('header.php');

print $header;

 ?>



