{include file='header.tpl'}

<div id="bd" >
{include file='users_navigation.tpl'}



 
   
    <div class="data_table" style="margin-top:25px">
      <span class="clean_table_title">{t}Staff Users{/t}</span>
      <div  class="clean_table_caption"  style="clear:both;">
	<div style="float:left;"><div id="table_info0" class="clean_table_info"><span id="rtext0"></span> <span class="rtext_rpp" id="rtext_rpp0"></span> <span class="filter_msg"  id="filter_msg0"></span></div></div>
	<div class="clean_table_filter" id="clean_table_filter0"><div class="clean_table_info"><span id="filter_name0" class="filter_name" >{$filter_name0}</span>: <input style="border-bottom:none" id='f_input0' value="{$filter_value0}" size=10/><div id='f_container0'></div></div></div>
	<div class="clean_table_controls" style="" ><div><span  style="margin:0 5px" id="paginator0"></span></div></div>
      </div>
      <div  id="table0"   class="data_table_container dtable btable "> </div>
    </div>
    <div class="data_table" style="margin-top:25px;width:600px">
      <span class="clean_table_title">{t}Groups{/t}</span>
      <div  class="clean_table_caption"  style="clear:both;">
	<div style="float:left;"><div class="clean_table_info">{$table_info} <span class="filter_msg"  id="filter_msg1"></span></div></div>
	<div class="clean_table_filter" style="display:none"><div class="clean_table_info">{$filter_name}: <input style="border-bottom:none" id='f_input1' value="{$filter_value}" size=10/><div id='f_container1'></div></div></div>
	<div class="clean_table_controls" style="" ><div><span  style="margin:0 5px" id="paginator1"></span></div></div>
      </div>
      <div  id="table1"   class="data_table_container dtable btable "> </div>
    </div>
    
 
 <div id="yui-main">
       <div class="data_table" style="margin-top:25px">
      <span class="clean_table_title">{t}Staff User Login History{/t}</span>
         {include file='table_splinter.tpl' table_id=2 filter_name=$filter_name2 filter_value=$filter_value2  }
      <div  id="table2"   class="data_table_container dtable btable "> </div>
    </div>    
  </div>


</div> 

{include file='footer.tpl'}

