{include file='header.tpl'}
<div id="bd" style="padding:0px">
<div style="padding:0 20px;height:50px">

<input type="hidden" value="{$user_id}" id="user_id" name="user_id">

<div  class="branch" style="clear:left;"> 
  <span ><a href="index.php">{t}Dashboard{/t}</a>  &rarr;  {t}Dashboard Configuration{/t}</span>
</div>

<div style="clear:both;width:100%;border-bottom:1px solid #ccc;padding-bottom:3px;margin-top:7px">
    <div class="buttons" style="float:right">
    </div>
    <div class="buttons" style="float:left">
            <button  onclick="window.location='index.php'" ><img src="art/icons/door_out.png" alt=""> {t}Exit Configuration{/t}</button>

	

    </div>
    <div style="clear:both"></div>


</div>



    <div style="clear:both"></div>
     {include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0  }

    <div  id="table0"  style="font-size:90%"  class="data_table_container dtable btable "> </div>


</div>
</div>
{include file='footer.tpl'}
