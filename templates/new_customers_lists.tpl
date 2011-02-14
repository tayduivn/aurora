{include file='header.tpl'}
<div id="bd" >
{include file='contacts_navigation.tpl'}


   
      <h2 style="clear:both">{t}New Customers List{/t} ({$store->get('Store Name')})</h2>
<div style="border:1px solid #ccc;padding:20px;width:690px">
      <table >
	<form>
		<tr><td colspan="2"><b>{t}Contacts who...{/t}</b></td></tr>
      <tr>
        <td>{t}First contacted between{/t}:</td>
        <td>
            <input id="v_calpop3" type="text" class="text" size="11" maxlength="10" name="from" value=""/><img   id="customer_first_contacted_from" class="calpop" src="art/icons/calendar_view_month.png" align="absbottom" alt=""   /> <span class="calpop">&rarr;</span> 
            <input id="v_calpop4" class="calpop"  size="11" maxlength="10"   type="text" class="text" size="8" name="to" value=""/><img   id="customer_first_contacted_to" class="calpop_to" src="art/icons/calendar_view_month.png" align="absbottom" alt=""   />
            <div id="customer_first_contacted_from_Container" style="position:absolute;display:none; z-index:2"></div>
            <div id="customer_first_contacted_to_Container" style="display:none; z-index:2;position:absolute"></div>
        </td>        
      </tr>
    <tr>
        <td>{t}have{/t}:</td>
        <td>
   <div id="have_options" default_cat=""   class="options" style="margin:5px 0">
     {foreach from=$have_options item=cat3 key=cat_key name=foo3}
     <span  class="catbox {if $cat3.selected}selected{/if}"  onclick="checkbox_changed_have(this)" id="have_{$cat_key}"  parent="have_" cat="{$cat_key}"  >{$cat3.name}</span>
     {/foreach}
    </div>
        </td>
        
    </tr>
    <tr>
        <td>{t}don't have{/t}:</td>
        <td>
         <div id="dont_have_options" default_cat=""   class="options" style="margin:5px 0">
     {foreach from=$have_options item=cat3 key=cat_key name=foo3}
     <span  class="catbox {if $cat3.selected}selected{/if}"  onclick="checkbox_changed_have(this)" id="dont_have_{$cat_key}" parent="dont_have_"  cat="{$cat_key}" >{$cat3.name}</span>
     {/foreach}
    </div>    
        </td>
        
    </tr>
    
	<tr><td colspan="2"><b>{t}Customers who ordered...{/t}</b></td></tr>
      <tr><td>{t}any of this product(s){/t}</td><td><input id="product_ordered_or" value="{$product_ordered_or}" style="width:500px" /></td><tr>
      <tr style="display:none"><td>{t}but didn't order this product(s){/t}</td><td><input id="product_not_ordered1" value="" style="width:400px" /></td><tr>
      <tr style="display:none"><td>{t}and did't receive this product(s){/t}</td><td><input id="product_not_received1" value="" size="40" /></td><tr>
      <tr>
        <td>{t}during this period{/t}:</td>
        <td>
            <input id="v_calpop1" type="text" class="text" size="11" maxlength="10" name="from" value=""/><img   id="product_ordered_or_from" class="calpop" src="art/icons/calendar_view_month.png" align="absbottom" alt=""   /> <span class="calpop">&rarr;</span> 
            <input id="v_calpop2" class="calpop"  size="11" maxlength="10"   type="text" class="text" size="8" name="to" value=""/><img   id="product_ordered_or_to" class="calpop_to" src="art/icons/calendar_view_month.png" align="absbottom" alt=""   />
            <div id="product_ordered_or_from_Container" style="position:absolute;display:none; z-index:2"></div>
            <div id="product_ordered_or_to_Container" style="display:none; z-index:2;position:absolute"></div>
        </td>
      </tr>
     
      </table>
      </form>
       </table>
</div> 
<div style="padding:20px;width:690px;text-align:right">
      <span  style="display:none;margin-left:20px;border:1px solid #ccc;padding:4px 5px;cursor:pointer" id="save_list">{t}Save List{/t}</span>
      <span  style="display:none;margin-left:20px;border:1px solid #ccc;padding:4px 5px;cursor:pointer" id="modify_search">{t}Modify Criteria{/t}</span>

      <span  style="margin-left:20px;border:1px solid #ccc;padding:4px 5px;cursor:pointer" id="submit_search">{t}Create List{/t}</span>
</div>

    <div style="padding:30px 40px;display:none" id="searching">
	{t}Search in progress{/t} <img src="art/progressbar.gif"/>
    </div>

    
    <div id="the_table" class="data_table" style="margin-top:20px;clear:both;display:none" >
    <span class="clean_table_title">Customers List</span>
 <div id="table_type">
         <a  style="float:right"  class="table_type state_details"  href="customers_lists_csv.php" >{t}Export (CSV){/t}</a>

     </div>


  <div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999"></div>

      <div id="short_menu" class="nodetails" style="clear:both;width:100%;margin-bottom:0px">
 
  <table style="float:left;margin:0 0 0 0px ;padding:0"  class="options" {if $customers==0 }style="display:none"{/if}>
	<tr>
	  <td  {if $view=='general'}class="selected"{/if} id="general" >{t}General{/t}</td>
	  <td {if $view=='contact'}class="selected"{/if}  id="contact"  >{t}Contact{/t}</td>
	  <td {if $view=='address'}class="selected"{/if}  id="address"  >{t}Address{/t}</td>
	  <td {if $view=='balance'}class="selected"{/if}  id="balance"  >{t}Balance{/t}</td>
	  <td {if $view=='rank'}class="selected"{/if}  id="rank"  >{t}Ranking{/t}</td>

	</tr>
      </table>
 
 <table style="float:left;margin:0 0 0 0px ;padding:0"  class="options" >
	<tr>
	  <td  {if $view=='general'}class="selected"{/if} id="general" >{t}General{/t}</td>
	  <td {if $view=='contact'}class="selected"{/if}  id="contact"  >{t}Contact{/t}</td>
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
