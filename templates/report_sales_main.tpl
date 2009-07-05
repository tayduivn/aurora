{include file='header.tpl'}
<div id="bd" >
<div id="sub_header">
{if $next}<span class="nav2 onright"><a href="report_sales.php?{$next.url}">{$next.title}</a></span>{/if}
{if $prev}<span class="nav2 onright" ><a href="report_sales.php?{$prev.url}">{$prev.title}</a></span>{/if}
{if $up}<span class="nav2 onright" style="margin-left:20px"><a href="report_sales.php?{$up.url}">{$up.title}</a></span>{/if}

<span class="nav2"><a href="reports.php">{t}Sales Reports{/t}</a></span>
<span class="nav2"><a href="assets_department.php?id={$department_id}">{$department}</a></span>
<span class="nav2"><a href="assets_family.php?id={$family_id}"></a></span>
</div>
     



<div class="cal_menu" >
<span>{$tipo_title}</span> <span id="period">{$period}</span>
{if $tipo=='y'}
<table  class="calendar_year">
<tr>
<td><a href="report_sales.php?tipo=m&y={$period}&m=1">{$m[0]}</a></td>
<td><a href="report_sales.php?tipo=m&y={$period}&m=2">{$m[1]}</a></td>
<td><a href="report_sales.php?tipo=m&y={$period}&m=3">{$m[2]}</a></td>
</tr><tr>
<td><a href="report_sales.php?tipo=m&y={$period}&m=4">{$m[3]}</a></td>
<td><a href="report_sales.php?tipo=m&y={$period}&m=5">{$m[4]}</a></td>
<td><a href="report_sales.php?tipo=m&y={$period}&m=6">{$m[5]}</a></td>
</tr><tr>
<td><a href="report_sales.php?tipo=m&y={$period}&m=7">{$m[6]}</a></td>
<td><a href="report_sales.php?tipo=m&y={$period}&m=8">{$m[7]}</a></td>
<td><a href="report_sales.php?tipo=m&y={$period}&m=9">{$m[8]}</a></td>
</tr><tr>
<td><a href="report_sales.php?tipo=m&y={$period}&m=10">{$m[9]}</a></td>
<td><a href="report_sales.php?tipo=m&y={$period}&m=11">{$m[10]}</a></td>
<td><a href="report_sales.php?tipo=m&y={$period}&m=12">{$m[11]}</a></td>
</tr>
</table>
{/if}
{if $tipo=='w' or $tipo=='m' or $tipo=='d'}
<table  class="calendar_year">
<tr>
<td>w</td>
<td>M</td>
<td>T</td>
<td>W</td>
<td>T</td>
<td>F</td>
<td>S</td>
<td>D</td>
</tr>
{foreach from=$w item=week}
<tr>
<td><a href="report_sales.php?tipo=w&y={$week.year}&w={$week.number}">{$week.number}</a></td>
<td><a href="report_sales.php?tipo=d&y={$week.year}&m={$week.m_mon}&d={$week.mon}">{$week.mon}</a></td>
<td><a href="report_sales.php?tipo=d&y={$week.year}&m={$week.m_tue}&d={$week.tue}">{$week.tue}</a></td>
<td><a href="report_sales.php?tipo=d&y={$week.year}&m={$week.m_wed}&d={$week.wed}">{$week.wed}</a></td>
<td><a href="report_sales.php?tipo=d&y={$week.year}&m={$week.m_thu}&d={$week.thu}">{$week.thu}</a></td>
<td><a href="report_sales.php?tipo=d&y={$week.year}&m={$week.m_fri}&d={$week.fri}">{$week.fri}</a></td>
<td><a href="report_sales.php?tipo=d&y={$week.year}&m={$week.m_sat}&d={$week.sat}">{$week.sat}</a></td>
<td><a href="report_sales.php?tipo=d&y={$week.year}&m={$week.m_sun}&d={$week.sun}">{$week.sun}</a></td>
</tr>
{/foreach}
</table>

{/if}

</div>

<h1 style="clear:left">{$title}</h1>
<table class="report_sales1">
<tr><td>{t}Store{/t}</td><td>{t}Invoices{/t}</td><td>{t}Net Sales{/t}</td><td>{t}Tax{/t}</td></tr>
{foreach from=$store_data   item=data }
<tr class="geo"><td class="label"> {$data.store}</td><td>{$data.invoices}</td><td>{$data.net}</td><td>{$data.tax_sales}</td></tr>
{/foreach}
</table>

</div>

{include file='footer.tpl'}

