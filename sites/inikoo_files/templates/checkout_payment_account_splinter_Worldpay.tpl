 
<div style="{if $first}margin-left:0px{/if}" id="payment_account_container_Worldpay" class="payment_method_button glow" onclick="choose_payment_account('{$payment_service_provider_code}',{$payment_account_key})">
	<h2>
		<img style="margin-right:5px" src="art/credit_cards.png"> {t}Debit/Credit Card{/t} 
	</h2>
	<div>
		<div>
			<img style="position:absolute;top:55px;width:90px;;left:10px;float:left;border:0px solid red" src="art/credit_cards_worldpay.png"> <img style="position:absolute;top:65px;left:120px;width:85px;float:right;border:0px solid red" src="art/powered_by_wordlpay.gif"> 
		</div>
	</div>
</div>
<form name="_xclickWP" action="" method="POST" id="Worldpay_form" />
<input type="hidden" name="instId" value="" id="Worldpay_Payment_Account_ID"> 
<input type="hidden" name="cartId" value="" id="Worldpay_Payment_Account_Cart_ID"> 
<input type="hidden" name="currency" value="" id="Worldpay_Order_Currency"> 
<input type="hidden" name="name" value="" id="Worldpay_Customer_Main_Contact_Name"> 
<input type="hidden" name="email" value="" id="Worldpay_Customer_Main_Plain_Email"> 
<input type="hidden" name="MC_business" value="" id="Worldpay_Payment_Account_Business_Name"> 
<input type="hidden" name="MC_customerId" value="" id="Worldpay_Customer_Key"> 
<input type="hidden" name="address1" value="" id="Worldpay_Customer_Billing_Address_Line_1"> 
<input type="hidden" name="address2" value="" id="Worldpay_Customer_Billing_Address_Line_2"> 
<input type="hidden" name="address3" value="" id="Worldpay_Customer_Billing_Address_Line_3"> 
<input type="hidden" name="town" value="" id="Worldpay_Customer_Billing_Address_Town"> 
<input type="hidden" name="postcode" value="" id="Worldpay_Customer_Billing_Address_Postal_Code"> 
<input type="hidden" name="country" value="" id="Worldpay_Customer_Billing_Address_2_Alpha_Country_Code"> 
<input type="hidden" name="tel" value="" id="Worldpay_Customer_Main_Plain_Telephone"> 
<input type="hidden" name="normalAmount" value="" id="Worldpay_Order_Balance_Total_Amount1"> 
<input type="hidden" name="initialAmount" value="" id="Worldpay_Order_Balance_Total_Amount2"> 
<input type="hidden" name="amount" value="" id="Worldpay_Order_Balance_Total_Amount3"> 
<input type="hidden" name="desc" value="" id="Worldpay_Description"> 
<input type="hidden" name="signature" value="" id="Worldpay_signature"> 
<input type="hidden" name="testMode" value="100" id="Worldpay_Test_Mode"> 
<input type="hidden" name="option" value="0" id="Worldpay_option"> 
<input type="hidden" name="startDelayUnit" value="4" id="Worldpay_startDelayUnit"> 
<input type="hidden" name="startDelayMult" value="1" id="Worldpay_startDelayMult"> 
<input type="hidden" name="intervalMult" value="1" id="Worldpay_intervalMult"> 
<input type="hidden" name="intervalUnit" value="4" id="Worldpay_intervalUnit"> 
<input type="hidden" name="MC_PaymentAccountKey" value="" id="Worldpay_Payment_Service_Provider_Key"> 
<input type="hidden" name="MC_Payment_Key" value="" id="Worldpay_Payment_Key"> 
<input type="hidden" name="MC_Order_Key" value="" id="Worldpay_Order_Key"> 
<input type="hidden" name="MC_callback" value="" id="Worldpay_Callback_URL"> 



</form>
<div id="payment_account_info_Worldpay" style="display:none">
	<h2 style="padding-bottom:10px">
		{t}Debid/Credit Card Payment{/t}
	</h2>
	<h3>
		{t}Your payment will be processed securely by Worldpay{/t}.
	</h3>
	<p>
		{t}Click confirm to be taken to the payment gateway{/t}.
	</p>
</div>
