<?php 
//global $found_in['url'];
global $found_in_label, $found_in_url;
//print $found_in_url;

$search_input=file_get_contents('../../inikoo_files/templates/search_input.html');
$menubar=file_get_contents('../../inikoo_files/templates/menubar.html');
$title='Tibetan Bowls and Artefacts';

$header_info='Please note this is a we supply wholesale we supply wholesale to the gift trade';

$found_in['url']=$found_in_url;
$found_in['label']=$found_in_label;
if(isset($found_in['url']))
$found_in="<a href='".$found_in['url']."'>".$found_in['label']."</a>";
else
$found_in='';
$header=<<<EOD

<div id="header_container" >

<div id="header">

<div style="height:55px">
<h1 style="position:relative;top:20px;padding:0;margin:0px 0 0 240px">$title</h1>
</div>
<div style="margin-left:230px" id="menu_bar">$menubar
</div>



<table  class="header_table" >
<tr>
<td class="found_in"><span >$found_in</span></td>
<td >$search_input</td>
<td rowspan="2" class="see_also"><span ><b>See also:</b></span> <span  >Chill Pilss</span><span  >Bath bombs</span><span style="" >Mini Incense sticks</span></td>
</tr>
</table>
</div>




EOD;

?>