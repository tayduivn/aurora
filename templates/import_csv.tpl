﻿{include file='header.tpl'} 
<div id="bd">
	<input type="hidden" id="search_type" value="{$search_type}" />
	{if $scope=='customers_store'} {include file='contacts_navigation.tpl'} 
	<div class="branch">
		<span>{if $user->get_number_stores()>1}<a href="customers_server.php">{t}Customers{/t}</a> &rarr; {/if}<a href="customers.php?store={$store->id}">{$store->get('Store Code')} {t}Customers{/t}</a> &rarr; {t}Import Customers{/t} (1/3)</span> 
	</div>
	<div id="top_page_menu" class="top_page_menu">
		<div class="buttons" style="float:left">
			<span class="main_title">{$main_title}</span>
		</div>
		<div class="buttons" style="float:right">
			<a class="negative" href="customers.php?store={$store->id}">{t}Cancel{/t}</a>
		</div>
		<div style="clear:both">
		</div>
	</div>
	{/if}
	{*} 
	<h3>
		Upload from External site 
	</h3>
	<div class="left3Quarters">
		<br />
		<form id="form" name="form" method="post" action="external_csv_verify.php?subject={$subject}&subject_key={$subject_key}" enctype="multipart/form-data">
			You have {$records} Records to verify. 
			<input type="hidden" name="form" value="form" />
			<div class="clear">
			</div>
			<ul class="formActions">
				<li> 
				<div class="bt">
					<input id="form:upload" type="submit" name="submit" value="Upload &amp; Preview" onclick="" />
				</div>
				</li>
			</ul>
		</form>
	</div>
	{/*} 

	<form id="form" name="form" method="post" action="import_csv_verify.php?subject={$subject}&subject_key={$subject_key}" enctype="multipart/form-data">
		<input type="hidden" name="form" value="form" />
		<table class="edit" style="margin-top:20px;width:100%" border=0>
			<tr>
				<td style="width:70px" class="label">{t}CSV File{/t}:</td>
				<td style="width:250px"> 
				<input style="width:100%" type="file"  id="fileUpload" name="fileUpload" />
				</td>
				<td class="error">
				{$showerror} 
				</td>
			</tr>
			
			<tr class="buttons">
				<td colspan="2"> 
				<div class="buttons">
					<button class="positive" id="form:upload" type="submit" name="submit">{t}Upload & Preview{/t}</button> 
				</div>
				</td>
			</tr>
		</table>
	</form>
</div>
{include file='footer.tpl'} 