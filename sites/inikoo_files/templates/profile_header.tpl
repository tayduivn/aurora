<div class="top_page_menu" style="padding:10px 20px 5px 20px;width:920px">
	<div class="buttons" style="float:left">
		<button style="display:none"  {if  $select == 'products'}class="selected"{/if} onclick="window.location='profile.php?view=products'"><img src="art/icons/bricks.png" alt=""> {t}Products Ordered{/t}</button> 

		<button style="display:xnone"  {if $select == 'orders'}class="selected"{/if} onclick="window.location='profile.php?view=orders'"><img src="art/icons/table.png" alt=""> {t}My Orders{/t}</button> 
		<button {if $select == 'change_password'}class="selected"{/if} onclick="window.location='profile.php?view=change_password'"><img src="art/icons/key.png" alt=""> {t}Change Password{/t}</button> 
		<button  {if $select == 'delivery_addresses'}class="selected"{/if} onclick="window.location='profile.php?view=delivery_addresses'"><img src="art/icons/lorry.png" alt=""> {t}Delivery Address{/t}</button> 
		<button  {if $select == 'billing_addresses'}class="selected"{/if} onclick="window.location='profile.php?view=billing_addresses'"><img src="art/icons/book.png" alt=""> {t}Billing Address{/t}</button> 

		<button {if $select == 'contact'}class="selected"{/if} onclick="window.location='profile.php?view=contact'"><img src="art/icons/chart_pie.png" alt=""> {t}My Account{/t}</button> 
	</div>
	<div style="clear:both">
	</div>
</div>