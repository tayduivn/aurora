{include file='header.tpl'}
<div id="bd" >
{include file='contacts_navigation.tpl'}


   
      <h2 style="clear:both">{t}New Product List{/t} ({$store->get('Store Name')})</h2>
<div style="border:1px solid #ccc;padding:20px;width:870px">
<input type="hidden" id="store_id" value="{$store->id}">

<span id="error_no_name" style="display:none">{t}Please specify a name{/t}.</span>
      <table >
	<form>
		<tr><td colspan="2"><b>{t}Product Validity...{/t}</b></td></tr>
      <tr>
        <td>{t}Product valid between{/t}:</td>
        <td>
            <input id="v_calpop3" type="text" class="text" size="11" maxlength="10" name="from" value=""/><img   id="product_first_validated_from" class="calpop" src="art/icons/calendar_view_month.png" align="absbottom" alt=""   /> <span class="calpop">&rarr;</span> 
            <input id="v_calpop4" class="calpop"  size="11" maxlength="10"   type="text" class="text" size="8" name="to" value=""/><img   id="product_first_validated_to" class="calpop_to" src="art/icons/calendar_view_month.png" align="absbottom" alt=""   />
            <div id="product_first_validated_from_Container" style="position:absolute;display:none; z-index:2"></div>
            <div id="product_first_validated_to_Container" style="display:none; z-index:2;position:absolute"></div>
        </td>        
      </tr>
<tr><td colspan="2"><b>{t}Some features...{/t}</b></td></tr> 
     <tr>
        <td>{t}based in{/t}:</td>
        <td>
        <input id="geo_constraints" style="width:500px"/> 
        <div class="general_options" >
                <span id="family" class="state_details">{t}Family{/t}</span>
                <span id="department" class="state_details">{t}Department{/t}</span>
               

        </div>
        </td>
        
    </tr>  
      
  
{*	<tr><td colspan="2"><b>{t}Customers who ordered...{/t}</b></td></tr>     *}
      <tr><td>{t}any of this product(s){/t}</td><td><input id="product_ordered_or" value="" style="width:500px" />
      <div class="general_options" >
                <span id="brand" class="state_details">{t}Brand{/t}</span>
                <span id="tarrif" class="state_details">{t}Tarrif{/t}</span>
                <span id="special_characteristics" class="state_details">{t}Special Characteristics{/t}</span>
        </div>
      </td><tr>

      <tr style="display:none"><td>{t}but didn't order this product(s){/t}</td><td><input id="product_not_ordered1" value="" style="width:400px" /></td><tr>
      <tr style="display:none"><td>{t}and did't receive this product(s){/t}</td><td><input id="product_not_received1" value="" size="40" /></td><tr>

 {*     <tr>
        <td>{t}during this period{/t}:</td>
        <td>
            <input id="v_calpop1" type="text" class="text" size="11" maxlength="10" name="from" value=""/><img   id="product_ordered_or_from" class="calpop" src="art/icons/calendar_view_month.png" align="absbottom" alt=""   /> <span class="calpop">&rarr;</span> 
            <input id="v_calpop2" class="calpop"  size="11" maxlength="10"   type="text" class="text" size="8" name="to" value=""/><img   id="product_ordered_or_to" class="calpop_to" src="art/icons/calendar_view_month.png" align="absbottom" alt=""   />
            <div id="product_ordered_or_from_Container" style="position:absolute;display:none; z-index:2"></div>
            <div id="product_ordered_or_to_Container" style="display:none; z-index:2;position:absolute"></div>
        </td>
      </tr>

 *}    
      </table>
      </form>
       </table>
</div> 
<div style="padding:20px;width:890px;xtext-align:right">
<div id="save_dialog" style="width:600px;float:left;visibility:hidden">
 <div id="the_div" style="xdisplay:none;">    
	{t}Enter list name{/t} : <input type="text" name="list_name" id="list_name"> &nbsp;&nbsp;{t}Select List Type{/t} : <input type="radio" name="type" checked="checked" id="static" value="Static">&nbsp;{t}Static{/t} &nbsp;&nbsp;<input type="radio" name="type"  id="dynamic" value="Dynamic">&nbsp;{t}Dynamic{/t}
      </div>
<div id="save_list_msg"></div>
</div>
<div style="float:left">
      <span  style="display:none;margin-left:20px;border:1px solid #ccc;padding:4px 5px;cursor:pointer" id="save_list"  >{t}Save List{/t}</span>
      <span  style="display:none;margin-left:20px;border:1px solid #ccc;padding:4px 5px;cursor:pointer" id="modify_search" >{t}Redo List{/t}</span>
      <span  style="margin-left:20px;border:1px solid #ccc;padding:4px 5px;cursor:pointer" id="submit_search">{t}Create List{/t}</span>
</div>
</div>




    <div style="padding:30px 40px;display:none" id="searching">
	{t}Search in progress{/t} <img style="margin-left:20px;position:relative;top:5px "src="art/progressbar.gif"/>
    </div>

    
    <div id="the_table" class="data_table" style="margin-top:20px;clear:both;display:none" >
    <span class="clean_table_title">Products List</span>
{*     <div id="table_type">
       	 <span  id="export_csv0" style="float:right;margin-left:20px"  class="table_type state_details" tipo="products" >{t}Export (CSV){/t}</span>
     </div>
*}

  <div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999"></div>

      <div id="short_menu" class="nodetails" style="clear:both;width:100%;margin-bottom:0px">
 
  <table style="float:left;margin:0 0 0 0px ;padding:0"  class="options" >
	<tr>
	  <td  {if $view=='general'}class="selected"{/if} id="general" >{t}General{/t}</td>
	  <td {if $view=='contact'}class="selected"{/if}  id="contact"  >{t}Contact{/t}</td>
	  <td {if $view=='address'}class="selected"{/if}  id="address"  >{t}Address{/t}</td>
	  <td {if $view=='balance'}class="selected"{/if}  id="balance"  >{t}Balance{/t}</td>
	  <td {if $view=='rank'}class="selected"{/if}  id="rank"  >{t}Ranking{/t}</td>

	</tr>
      </table>
 
 
      
    </div>



 
 {include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 no_filter=true }
     	<div  id="table0"   style="font-size:90%" class="data_table_container dtable btable "> </div>


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
<div class="search_box" style="margin-top:30px;font-size:90%;display:none" id="the_search_box" >

   <table>
     <tr><td colspan="" style="text-align:right;border-bottom:1px solid #ccc" >Search over:</td></tr>
     <tr><td style="text-align:right">{t}All Customers{/t}</td><td><input checked="checked" name="geo_group" id="geo_group_all" value="all" type="radio"></td></tr>
     <tr><td style="text-align:right">{$home} {t}Customers{/t}</td><td><input  name="geo_group"  id="geo_group_home" value="home" type="radio"></td></tr>
     <tr><td style="text-align:right">{t}Foreign Customers{/t}</td><td><input  name="geo_group"  id="geo_group_nohome" value="nohome" type="radio"></td></tr>
     <tr><td colspan="" style="text-align:right;border-bottom:1px solid #ccc;height:30px;vertical-align:bottom" >Only Customers:</td></tr>
     <tr><td style="text-align:right">{t}with Email{/t}</td><td><input   id="with_email"  type="checkbox"></td></tr>
     <tr><td style="text-align:right">{t}with Telephone{/t}</td><td><input    id="with_tel"  type="checkbox"></td></tr>

   </table>
 </div>
{include file='footer.tpl'}
<div id="dialog_wregion_list">
    <div class="splinter_cell" style="padding:10px 15px 10px 0;border:none">
        <div id="the_table" class="data_table" >
            <span class="clean_table_title">{t}World Regions{/t}</span>
            {include file='table_splinter.tpl' table_id=1 filter_name=$filter_name1 filter_value=$filter_value1}
            <div  id="table1"   class="data_table_container dtable btable "> </div>
        </div>
    </div>
 </div>



<div id="dialog_country_list">
    <div class="splinter_cell" style="padding:10px 15px 10px 0;border:none">
        <div id="the_table" class="data_table" >
            <span class="clean_table_title">{t}Country List{/t}</span>
            {include file='table_splinter.tpl' table_id=2 filter_name=$filter_name2 filter_value=$filter_value2}
            <div  id="table2"   class="data_table_container dtable btable "> </div>
        </div>
    </div>
 </div>


<div id="dialog_postal_code_list">
    <div class="splinter_cell" style="padding:10px 15px 10px 0;border:none">
        <div id="the_table" class="data_table" >
            <span class="clean_table_title">{t}Postal Code List{/t}</span>
            {include file='table_splinter.tpl' table_id=3 filter_name=$filter_name3 filter_value=$filter_value3}
            <div  id="table3"   class="data_table_container dtable btable "> </div>
        </div>
    </div>
 </div>
 
 
<div id="dialog_city_list">
    <div class="splinter_cell" style="padding:10px 15px 10px 0;border:none">
        <div id="the_table" class="data_table" >
            <span class="clean_table_title">{t}Cities{/t}</span>
            {include file='table_splinter.tpl' table_id=4 filter_name=$filter_name4 filter_value=$filter_value4}
            <div  id="table4"   class="data_table_container dtable btable "> </div>
        </div>
    </div>
 </div> 
 
 <div id="dialog_department_list">
    <div class="splinter_cell" style="padding:10px 15px 10px 0;border:none">
        <div id="the_table" class="data_table" >
            <span class="clean_table_title">{t}Department List{/t}</span>
            {include file='table_splinter.tpl' table_id=5 filter_name=$filter_name5 filter_value=$filter_value5}
            <div  id="table5"   class="data_table_container dtable btable "> </div>
        </div>
    </div>
 </div>
 
 <div id="dialog_family_list">
    <div class="splinter_cell" style="padding:10px 15px 10px 0;border:none">
        <div id="the_table" class="data_table" >
            <span class="clean_table_title">{t}Family List{/t}</span>
            {include file='table_splinter.tpl' table_id=6 filter_name=$filter_name6 filter_value=$filter_value6}
            <div  id="table6"   class="data_table_container dtable btable "> </div>
        </div>
    </div>
 </div>
 
 <div id="dialog_product_list">
    <div class="splinter_cell" style="padding:10px 15px 10px 0;border:none">
        <div id="the_table" class="data_table" >
            <span class="clean_table_title">{t}Product List{/t}</span>
            {include file='table_splinter.tpl' table_id=7 filter_name=$filter_name7 filter_value=$filter_value7}
            <div  id="table7"   class="data_table_container dtable btable "> </div>
        </div>
    </div>
 </div>
 
 <div id="dialog_category_list">
    <div class="splinter_cell" style="padding:10px 15px 10px 0;border:none">
        <div id="the_table" class="data_table" >
            <span class="clean_table_title">{t}Category List{/t}</span>
            {include file='table_splinter.tpl' table_id=8 filter_name=$filter_name8 filter_value=$filter_value8}
            <div  id="table8"   class="data_table_container dtable btable "> </div>
        </div>
    </div>
 </div>
 

 
