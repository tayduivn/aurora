{include file='header.tpl'}
<div id="bd" style="padding:0px">
<div style="clear:both" >
<h1 style="color:red;font-size:400%">DONT USE THIS</h1>
<h1 style="color:red;font-size:400%">USE THE DEFAULT MAP</h1>

<h1 style="padding:40px;">{t}Export Wizard{/t}</h1>
<form action="export_wizard_step2.php?subject={$subject}&subject_key={$subject_key}" method="POST" name="frm_export" onSubmit="return validate1({$param});">
<table style="margin-left:60px;" border="1" width="600">
<tr><td colspan="2"><B>Select Fields to export</B></td></tr>
{foreach from=$list key=list_key item=list_item name=foo}
<tr><td><input type="checkbox" name="fld[]" id="fld{$smarty.foreach.foo.index}" value="{$list_key}"></td>
<td>{$list_key}</td>
</tr>
{/foreach}

<tr>
<td colspan="2"><input type="button" name="prev" id="prev" class="prev" onClick=go("{$prev_path}"); />&nbsp;<input type="SUBMIT" name="SUBMIT" id="SUBMIT" class="next" value=""> <input type="button" name="return" id="return" class="return" value="" onClick=go("{$return_path}"); ></td>

</tr>
</table>
</form>
</div>
</div>
</div>
{include file='footer.tpl'}
