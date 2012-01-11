{include file='header.tpl'}
<div id="bd"  style="padding:0px">
<div style="padding:0 20px">
{include file='locations_navigation.tpl'}
<div class="branch"> 
  <span >{if $user->get_number_warehouses()>1}<a href="warehouses.php">{t}Warehouses{/t}</a> &rarr; {/if}{t}Locations{/t}</span>
</div>
<div class="top_page_menu">
    <div class="buttons" style="float:right">
        {if $modify}
        <button  onclick="window.location='edit_warehouse.php?id={$warehouse->id}'" ><img src="art/icons/vcard_edit.png" alt=""> {t}Edit Warehouse{/t}</button>
        {/if}
    </div>
    <div class="buttons" style="float:left">
        <button  onclick="window.location='warehouse_stats.php?id={$warehouse->id}'" ><img src="art/icons/chart_pie.png" alt=""> {t}Statistics{/t}</button>
         <button  onclick="window.location='warehouse_map.php?id={$warehouse->id}'" ><img src="art/icons/application_view_gallery.png" alt=""> {t}Map{/t}</button>
        <button  onclick="window.location='parts_movements.php?id={$warehouse->id}'" ><img src="art/icons/arrow_switch.png" alt=""> {t}Movements{/t}</button>

 </div>
    <div style="clear:both"></div>
</div>



 <div style="clear:left;margin:0 0px">
    <h1>{t}Warehouse{/t}: {$warehouse->get('Warehouse Name')} ({$warehouse->get('Warehouse Code')})</h1>
  </div>

</div>

<ul class="tabs" id="chooser_ul" style="clear:both;margin-top:5px">

    <li> <span class="item {if $view=='locations'}selected{/if}"  id="locations">  <span> {t}Locations{/t}</span></span></li>
    <li> <span class="item {if $view=='areas'}selected{/if}"  id="areas">  <span> {t}Areas{/t}</span></span></li>
    <li style="display:none"> <span class="item {if $view=='shelfs'}selected{/if}"  id="shelfs">  <span> {t}Shelfs{/t}</span></span></li>
    <li style="display:none"> <span class="item {if $view=='map'}selected{/if}" id="map"  ><span>  {t}Map{/t}</span></span></li>
     <li style="display:none"> <span class="item {if $view=='movements'}selected{/if}"  id="movements">  <span> {t}Movements{/t}</span></span></li>
 <li style="display:none"> <span class="item {if $view=='stats'}selected{/if}"  id="stats">  <span> {t}Stats{/t}</span></span></li>
  </ul>
<div  style="clear:both;width:100%;border-bottom:1px solid #ccc"></div>

<div id="block_locations" style="{if $view!='locations'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">

 <div id="the_table0" class="data_table" style="margin:20px 0px;clear:both">
    <span class="clean_table_title">{t}Locations{/t}</span>

     {include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0  }
    <div  id="table0"   class="data_table_container dtable btable "> </div>
  </div>
</div>

<div id="block_areas" style="{if $view!='areas'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
 <div id="the_table1" class="data_table" style="margin:0px 0px;clear:both">
    <span class="clean_table_title">{t}Warehouse Areas{/t}</span>
{include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1  }
    <div  id="table1"   class="data_table_container dtable btable "> </div>
  </div>
</div>
      
<div id="block_map" style="{if $view!='map'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
  <div   style="border:1px solid #ccc;text-align:left;margin:0px;padding:20px;height:270px;width:600px;margin: 0 0 10px 0;float:left">
    <img   src="_warehouse.png" name="printable_map" />
    </div>
  </div>
</div>

<div id="block_shelfs" style="{if $view!='shelfs'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
</div>
<div id="block_movements" style="{if $view!='movements'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
</div>
<div id="block_stats" style="{if $view!='stats'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
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

<div id="filtermenu1" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">
       <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
      {foreach from=$filter_menu1 item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_filter('{$menu.db_key}','{$menu.label}',1)"> {$menu.menu_label}</a></li>
      {/foreach}
    </ul>
  </div>
</div>

<div id="rppmenu1" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">
       <li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
      {foreach from=$paginator_menu1 item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" onClick="change_rpp({$menu},1)"> {$menu}</a></li>
      {/foreach}
    </ul>
  </div>
</div>


{include file='footer.tpl'}
