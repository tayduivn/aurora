{include file='header.tpl'}
<div id="bd" style="padding:0px">
<div style="clear:both" >
<h1 style="padding:40px;">Export Wizard - Step 2</h1>
<form action="export_data.php?subject=customer&subject_key={$customer_id}" method="POST" name="frm_export" onSubmit="return validate();">



<table style="margin-left:60px;" border="0" width="600">
<tr><td colspan="2" style="padding-bottom:20px;"><B>Select Fields to export</B></td></tr>
<div id="result">
{foreach from=$list key=list_key item=list_item name=foo}
<tr><td width="30%">
<input type="hidden" style="width:25px;" name="seq{$smarty.foreach.foo.index+1}" id="txt{$smarty.foreach.foo.index+1}" value="{$smarty.foreach.foo.index+1}" readonly="readonly"><input type="button" class="up_b" onClick=myfunc({$smarty.foreach.foo.index},{$smarty.foreach.foo.index-1}); />&nbsp;<input type="button" class="down_b" onClick=myfunc({$smarty.foreach.foo.index},{$smarty.foreach.foo.index+1}); /></td>
<td width="70%">{$list_key}</td>
</tr>
{/foreach}
</div>
<tr>
<td colspan="2" style="padding-top:20px;">
<input type="checkbox" id="header" name="header" value="header" checked="checked" /> Include Field Names in exported file
</td>
</tr/>
<tr>
<td colspan="2" style="padding-top:10px;">
<input type="checkbox" id="save" name="save" value="save" checked="checked" onClick=saveMap(); /> Save my Map for future
</td></tr>
<tr><td colspan="2" style="padding-top:20px;">
<table id="maps" style="paddingtop:5px; display:block;" width="600px;"><tr>
<td width="50%">Map Name:</td>
<td width="50%"><input type="text" id="map_name" name="map_name" value=""/></td></tr>
<tr><td>Map Description: </td>
<td><textarea id="map_desc" name="map_desc"></textarea></td></tr>
</table>
</td>
</tr/>

<tr>
<td colspan="2"><input type="button" name="prev" id="prev" class="prev" onClick=go("export_wizard.php?subject={$map_type}&subject_key={$customer_id}"); /> <input type="SUBMIT" name="SUBMIT" id="SUBMIT" class="export_b" value="" ></td>

</tr>
</table>


</form>
</div>
</div>
{include file='footer.tpl'}
