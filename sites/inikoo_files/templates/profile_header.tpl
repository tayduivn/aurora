<div class="top_page_menu" style="padding:10px 20px 5px 20px">
	<div class="buttons" style="float:left">
		<button {if $select == 'change_password'}class="selected"{/if} onclick="window.location='profile.php?view=change_password'"><img src="art/icons/key.png" alt=""> {t}Change Password{/t}</button> 
		<button style="display:none" {if $select == 'address_book'}class="selected"{/if} onclick="window.location='profile.php?view=address_book'"><img src="art/icons/book_addresses.png" alt=""> {t}Address Book{/t}</button> 
		<button style="display:none" {if $select == 'orders'}class="selected"{/if} onclick="window.location='profile.php?view=orders'"><img src="art/icons/table.png" alt=""> {t}Orders{/t}</button> 
		<button {if $select == 'contact'}class="selected"{/if} onclick="window.location='profile.php?view=contact'"><img src="art/icons/chart_pie.png" alt=""> {t}My Account{/t}</button> 
	</div>
	<div style="clear:both">
	</div>
</div>