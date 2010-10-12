{if $store_list_length>1}
<dl class="dropdown">
  <dt id="one-ddheader" onmouseover="ddMenu('one',1)" onmouseout="ddMenu('one',-1)" onclick="window.location='stores.php'" >{t}Customers{/t}</dt>
  <dd id="one-ddcontent" onmouseover="cancelHide('one')" onmouseout="ddMenu('one',-1)">
    <ul>
      {foreach from=$tree_list item=store }
      <li><a href="customers.php?store={$store.id}" class="underline">{$store.code}</a></li>
      {/foreach}
    </ul>
  </dd>
</dl>
{else}
<span class="nav2 onleft"><a href="customers.php">{t}Customers{/t}</a></span>
{/if}
 <span style="display:none" class="nav2 onleft"><a {if $nav_parent=='companies'}class="selected"{/if}  href="companies.php">{t}Companies{/t}</a></span>
<span  style="display:none"  class="nav2 onleft"><a  {if $nav_parent=='contacts'}class="selected"{/if}   href="contacts.php">{t}Personal Contacts{/t}</a></span>
<span class="nav2 onleft"><a  {if $nav_parent=='requests'}class="selected"{/if}   href="requests.php">{t}Requests{/t}</a></span>
<span class="nav2 onleft"><a  {if $nav_parent=='marketing'}class="selected"{/if}   href="marketing.php">{t}Marketing{/t}</a></span>


<div class="right_box">
  <div class="general_options">
    {foreach from=$general_options_list item=options }
    {if $options.tipo=="url"}
    <span onclick="window.location.href='{$options.url}'" >{$options.label}</span>
    {else}
    <span  id="{$options.id}" state="{$options.state}">{$options.label}</span>
    {/if}
    {/foreach}
  </div>
</div>
<table class="search"  border=0 style="{if $search_label==''}display:none{/if}">
<tr>
<td class="label" style="" >{$search_label}:</td>
<td class="form" style="">
<div id="search" class="asearch_container"  style=";float:left;{if !$search_scope}display:none{/if}">
  <input style="width:300px" class="search" id="{$search_scope}_search" value="" state="" name="search"/>
      <img style="position:relative;left:305px" align="absbottom" id="{$search_scope}_clean_search" class="submitsearch" src="art/icons/zoom.png">

    <div id="{$search_scope}_search_Container" style="display:none"></div>
</div>    
  
</td></tr>
</table>  
<div id="{$search_scope}_search_results" style="font-size:10px;float:right;background:#fff;border:1px solid #777;padding:10px;margin-top:0px;width:500px;position:absolute;z-index:20;top:-500px">
<table id="{$search_scope}_search_results_table"></table>
</div>





