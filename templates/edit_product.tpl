{include file='header.tpl'}
<div style="display:none; position:absolute; left:10px; top:200px; z-index:2" id="cal1Container"></div>


<div id="bd" >

<div id="sub_header">
{if $next.id>0}<span class="nav2 onright"><a href="edit_product.php?id={$next.id}">{t}Next{/t}</a></span>{/if}
{if $prev.id>0}<span class="nav2 onright" ><a href="edit_product.php?id={$prev.id}">{t}Previous{/t}</a></span>{/if}
<span class="nav2 onright" style="margin-left:20px"><a href="assets_family.php?id={$family_id}">{t}Up{/t}</a></span>
<span class="nav2 onright"><a href="assets_index.php">{t}Product index{/t}</a></span>
<span class="nav2"><a href="assets_tree.php">{$home}</a></span>
<span class="nav2"><a href="assets_department.php?id={$department_id}">{$department}</a></span>
<span class="nav2"><a href="assets_family.php?id={$family_id}">{$family}</a></span>
</div>
<div id="doc3" style="clear:both;" class="yui-g yui-t4" >
<div id="yui-main"> 
<h1>{$code} {$units}x {$description}</h1>
<div id="editor_chosser" >
<ul>
<li id="description" > <img src="art/icons/information.png"> {t}Description{/t}</li>
<li id="pictures"> <img src="art/icons/photos.png"> {t}Pictures{/t}</li>
<li id="prices" ><img src="art/icons/money_add.png"> {t}Price, Discounts{/t}</li>
<li id="suppliers" ><img src="art/icons/cog_add.png"> {t}Suppiers{/t}</li>
<li id="stock"><img src="art/icons/package_add.png"> {t}Stock, Location{/t}</li>
<li id="dimat"><img src="art/icons/shape_ungroup.png"> {t}Dimensions, Materials{/t}</li>
</ul>
</div> 


<div  {if !$edit_products_block=="pictures"}style="display:none"{/if}  class="edit_block" id="d_pictures">

</div>
<div  {if !$edit_products_block=="description"}style="display:none"{/if} class="edit_block" id="d_description">
  <form id="description">
    <input type="hidden" name="tipo" value="update_product">
    <input type="hidden" name="product_id" value="{$product_id}">
    <input type="hidden" id="v_cat" name="v_cat" value="{$v_cat}">

    
    <table style="margin:0;" border=0>
      <tr><td><img style="visibility:hidden"  id="c_categories" src="art/icons/accept.png" /></td>
	<td style="vertical-align: top;" >{t}Categories{/t}:</td>
<td style="vertical-align: top;" >
<table id="cat_list" style="border-right:1px solid #ccc;float:left;margin:0 20px 0 0 ">
{if $num_cat==0}<tr><td>{t}No assigned catories{/t}<td></td>{/if}
{foreach from=$cat key=cat_id item=i}
  <tr><td tipo="1" id="cat_{$cat_id}" saved="1" >{$i}</td><td onclick="delete_list_item('',{$cat_id})" ><img  id="cat_t_{$cat_id}" cat_id="{$cat_id}" style="cursor:pointer" src="art/icons/cross.png" /></td></tr>
{/foreach}
</table>


{if $num_cat_list==0}{t}No categories to choose{/t}{else}
<select name='cat' id="cat_select" prev="0">
 <option iname="">{t}Choose a category{/t}</option>
{foreach from=$cat_list key=myId item=i}
  <option {if !$i.show}disabled="disabled"{/if}  id="cat_o_{$myId}" iname="{$i.iname}"  parents="{$i.parents}" sname="{$i.name}"  cat_id="{$myId}"   >{$i.name}</option>
{/foreach}
</select>
<img box="cat_select" id="add_cat" style="position:relative;top:3px;cursor:pointer" src="art/icons/application_go_left.png"/>
{/if}

<span style="margin:0 0 0 25px;">{t}Browse Categories{/t}</span></td></tr>

      <tr><td><img style="visibility:hidden"  id="c_description" src="art/icons/accept.png" /></td>
	<td>{t}Description{/t}:</td><td><input     class=''     ovalue="{$description}"   name="v_description"    value="{$description}"  id="v_description" size="40"/></td><td id="m_description"></td></tr>
      <tr><td><img style="visibility:hidden"  id="c_sdescription" src="art/icons/accept.png" /></td>                               
	<td>{t}Short Description{/t}:</td><td><input  class=''  ovalue="{$sdescription}"  name="v_sdescription"  value="{$sdescription}" id="v_sdescription"  size="40"   /></td><td id="m_sdescription"></td></tr>
      <tr><td><img style="visibility:hidden"  id="c_details" src="art/icons/accept.png" /></td>
	<td>{t}Detailed Description{/t}:<td style="visibility:hidden" class="text_ok" id="i_details"><i>{t}Product details changed{/t}</i></td><td id="m_details"></td></tr>
      <tr><td></td><td colspan="2"><textarea id="v_details" name="v_details" rows="20" cols="100">{$details}</textarea>
      </td></tr>
      
    </table>
  </form>
</div>


</div>
<div class="yui-b">
<div  style="float:right;margin-top:10px;text-align:right">{include file='product_search.tpl'}</div>	 

<table  style="width:5em" class="but edit" >
<tr><td id="save" class="disabled">Save</td></tr>
<tr><td id="exit" class="ok" >Exit</td></tr>
</table>


</div>
</div>
