{include file='header.tpl'}

<div id="bd" >
{include file='users_navigation.tpl'}



  <div id="yui-main">
   
    <div class="data_table" style="margin-top:25px">
      <span class="clean_table_title">{t}User Login History{/t}</span>
         {include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0  }
      <div  id="table0"   class="data_table_container dtable btable "> </div>
    </div>
    
    
  </div>
</div> 

{include file='footer.tpl'}

