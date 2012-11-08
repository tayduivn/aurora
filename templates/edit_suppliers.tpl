{include file='header.tpl'}
<div id="bd" >

 		{include file='suppliers_navigation.tpl'} 
		<div class="branch">
			<span><a href="suppliers.php">{t}Suppliers{/t}</a> &rarr; {t}Edit Suppliers{/t}</span> 
		</div>
		<div class="top_page_menu">
			<div class="buttons" style="float:left">
			</div>
			<div class="buttons">
							<button onclick="window.location='suppliers.php'"><img src="art/icons/door_out.png" alt=""> {t}Exit Edit{/t}</button> 

				<button onclick="window.location='new_supplier.php'"><img src="art/icons/add.png" alt=""> {t}Add Supplier{/t}</button> 
			</div>
			<div style="clear:both">
			</div>
		</div>
		
    <h1>{t}Edit Suppliers{/t}</h1>
  

  <ul class="tabs" id="chooser_ul" style="clear:both">

    <li> <span class="item {if $edit=='suppliers'}selected{/if}"  id="suppliers">  <span> {t}Suppliers{/t}</span></span></li>
   
  </ul>

 <div class="tabbed_container" > 
 


  <div  class="edit_block" style="{if $edit!="suppliers"}display:none{/if}"  id="d_suppliers">
  
  <div class="data_table" style="clear:both">
    <span class="clean_table_title">{t}Suppliers List{/t}</span>
    <div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999"></div>
    <table style="float:left;margin:0 0 0 0px ;padding:0"  class="options" >
      <tr><td  {if $view=='general'}class="selected"{/if} id="general" >{t}General{/t}</td>
	  <td {if $view=='products'}class="selected"{/if}  id="products"  >{t}Products{/t}</td>

      </tr>
      </table>
    
    {include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0}

    <div  id="table0"   class="data_table_container dtable btable"> </div>
  </div>

</div>

</div>

</div>

<div id="filtermenu0" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">
      <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
      {foreach from=$filter_menu0 item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_filter('{$menu.db_key}','{$menu.label}',0)"> {$menu.menu_label}</a></li>
      {/foreach}
    </ul>
  </div>
</div>

<div id="rppmenu0" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">
       <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
      {foreach from=$paginator_menu0 item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_rpp({$menu},0)"> {$menu}</a></li>
      {/foreach}
    </ul>
  </div>
</div>

{include file='footer.tpl'}
