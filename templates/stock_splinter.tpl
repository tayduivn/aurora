  <div id="Editor_audit" style="position:fixed;top:-200px;width:250px;padding:10px 10px 10px 0">
  <div style="display:none" class="hd"></div>
  <div class="bd dt-editor">
  
    <table class="edit" border=0 style="margin:0 auto">
    <tr class="title"><td colspan=2>{t}Audit{/t}</td></tr>
      <input type="hidden" id="audit_location_key" value=""/>
      <input type="hidden" id="audit_sku" value=""/>
              <input type="hidden" id="audit_record_index" value=""/>

      <tr style="height:10px"><td colspan=2></td></tr>
      <tr ><td>{t}Quantity{/t}:</td><td><input style="text-align:right;width:4em" type="text" id="qty_audit" /></td></tr>
     <tr><td>{t}Notes{/t}:</td><td><input type="text" id="note_audit" /></td></tr>
       <tr style="display:none" id="Editor_audit_wait" ><td colspan=2 style="text-align:right"><img src="art/loading.gif" alt=""> {t}Processing Request{/t}</td></tr>

 <tr id="Editor_audit_buttons"><td colspan=2><div class="buttons">
      <button onclick="save_audit();" class="positive">{t}Save{/t}</button>
      <button onclick="close_audit_dialog()" class="negative" >{t}Cancel{/t}</button>
    </div></td></tr>
    </table>
    
    
  </div>
</div>


 <div id="Editor_add_stock"  style="position:fixed;top:-200px;width:250px;padding:10px 10px 10px 0">
  <div style="display:none" class="hd"></div>
  <div class="bd dt-editor">
  
   <table class="edit" border=0 style="margin:0 auto">
    <tr class="title"><td colspan=2>{t}Add stock{/t}</td></tr>
      <input type="hidden" id="add_stock_location_key" value=""/>
      <input type="hidden" id="add_stock_sku" value=""/>
        <input type="hidden" id="add_record_index" value=""/>
         <tr style="height:10px"><td colspan=2></td></tr>
         
      <tr><td>{t}Quantity{/t}:</td><td><input style="text-align:right;width:4em" type="text" id="qty_add_stock" /> <span id="error_negative_number_in_add_stock" style="font-size:90%;color:red;display:none">{t}Add stock only{/t}</span></td></tr>
     <tr><td>{t}Notes{/t}:</td><td><input type="text" id="note_add_stock" /></td></tr>
		  <tr style="height:10px"><td colspan=2></td></tr>
		<tr>
		<td colspan=2 style="text-align:right">
		
		 <div class="buttons">
         <span id="add_stock_waiting" style="display:none"><img src="art/loading.gif"> {t}Processing Request{/t}</span>
         <button id="add_stock_save_btn" onclick="save_add_stock();" class="positive">{t}Save{/t}</button>
         <button id="add_stock_cancel_btn" onclick="close_add_stock_dialog()" class="negative" >{t}Cancel{/t}</button>
           </div>
    
    </td></tr>
    </table>
    
    
  </div>
</div>



<div id="Editor_lost_items"  style="position:fixed;top:-200px;width:300px;padding:10px 10px 10px 0">
  <div style="display:none" class="hd"></div>
  <div class="bd dt-editor">
  
       <table class="edit" border=0 style="width:100%;margin:0 auto">
        <tr class="title"><td colspan=2>{t}Lost/Broken Stock{/t}</td></tr>
         <tr style="height:10px"><td colspan=2></td></tr>
      <input type="hidden" id="lost_record_index" value=""/>
      <input type="hidden" id="lost_sku" value=""/>
      <input type="hidden" id="lost_location_key" value=""/>
      <tr>
            <input type="hidden" id="lost_type" value="Lost"/>

      <td colspan="2">
      <div class="buttons small" style="margin:auto">
       <button id="lost_type_other_out" onClick="change_lost_type('other_out')" >{t}Other Out{/t}</button>
      <button id="lost_type_broken"  onClick="change_lost_type('broken')">{t}Broken{/t}</button>
     <button id="lost_type_lost" class="selected"  onClick="change_lost_type('lost')">{t}Lost{/t}</button>
      </div>
      </td>
      </tr>
      <tr style="height:10px"><td colspan=2></td></tr>
      <tr><td class="label">{t}Quantity Lost{/t}:</td><td><input style="text-align:right;width:4em" type="text" id="qty_lost" /> {t}max{/t} <span onclick="set_all_lost()" id="lost_max_value" style="cursor:pointer"></span></td></tr>
        <tr id="error_negative_number_in_lost_stock" style="display:none;color:red"><td style="text-align:right" colspan=3>{t}Only positive quantities accepted{/t}</td></tr>

    
    
    <tr><td class="label">{t}Why?{/t}:</td><td><input type="text" id="lost_why" /></td></tr>
      <tr><td class="label">{t}Action{/t}:</td><td><input type="text" id="lost_action" /></td></tr>
	  <tr style="height:10px"><td colspan=2></td></tr>



	<tr><td colspan=2 style="text-align:right">
		<div class="buttons">
		         <span id="save_lost_waiting" style="display:none"><img src="art/loading.gif"> {t}Processing Request{/t}</span>

      <button id="save_lost_btn" onclick="save_lost_items();" class="positive">{t}Save{/t}</button>
      <button  id="cancel_lost_btn"  onclick="close_lost_dialog()" class="negative" >{t}Cancel{/t}</button>
    </div></td></tr>
    </table>
    
    
  </div>
</div>

<div id="Editor_move_items"  style="position:fixed;top:-200px;padding:10px 10px 10px 0;width:300px;">
  <div style="display:none" class="hd"></div>
    <div class="bd dt-editor" >
           <table class="edit" border=0 style="margin:0 auto;width:270px;">
          
            <tr class="title"><td colspan=3>{t}Move Stock{/t} (<span id="move_sku_formated"></span>)</td></tr>
       	     

          
	    <input type="hidden" id="move_sku" value=0 >
	    <input type="hidden" id="move_record_index" value=0 >
	    <input type="hidden" id="move_other_location_key" value=0 >
	    <input type="hidden" id="move_this_location_key" value="{if isset($location)}{$location->id}{/if}" >

	 
	    <tr><td colspan="3" id="location_move_other_locations" ></td></tr>
	      <tr class="top" style="height:5px"><td colspan=3></td></tr>
	      	    

	    <tr >
	    <td id="this_location"  style="width:110px;text-align:right;padding-right:10px;"></td>
	    <td id="flow"  style="width:40px;text-align:center" onClick="change_move_flow()" flow="right"><img src="art/icons/arrow_right.png" /></td>
	    <td id="other_location" style="width:110px;text-align:left;padding:0px">
			<div id="location_move_to" style="width:80px;margin-left:0px;float:left">
			  <input id="location_move_to_input" type="text" style="width:80px;">
			  <div id="location_move_to_container" ></div>
			</div>
			<div id="location_move_from" style="margin-left:2px;display:none">
			  <input id="location_move_from_input" type="text"  value="">
			  <div id="location_move_from_container"></div>
			</div>


	      </td>
	    </tr>
	    <tr>
	      <td style="text-align:right;padding-right:10px;cursor:pointer" ovalue=""  id="move_stock_left" onclick="move_stock_right()"></td>
	      <td><input value='' style="width:45px;text-align:center" id="move_qty"  onkeyup="move_qty_changed()"   /></td>
	      <td style="padding-left:10px;cursor:pointer" id="move_stock_right"  ovalue="" onclick="move_stock_left()"></td>
	    </tr>
	    	      <tr  style="height:5px"><td colspan=3></td></tr>

	    <tr><td colspan=3><div class="buttons">
      <button id="move_items_btn" onclick="save_move_items();" class="positive">{t}Save{/t}</button>
      <button onclick="close_move_dialog()" class="negative" >{t}Cancel{/t}</button>
    </div></td></tr>
	  </table>
	  
    </div>
</div>


<div id="Editor_add_location" style="position:fixed;left:-1000px;width:200px;padding:20px 10px;height:140px">
  <div style="display:none" class="hd"></div>
    <div class="bd dt-editor" >
          <table border=0 style="width:100%;" >
          
         
          
	    <input type="hidden" id="add_location_sku" value=0 >
	     <input type="hidden" id="add_location_key" value=0 >

	    <tr><td>{t}Add Location{/t}</tr>
	    <tr style="height:30px">
	    
	    <td id="other_location" >
			
			<div id="add_location" >
			  <input id="add_location_input" type="text"  value="" style="width:100%" >
			  <div id="add_location_container"></div>
			</div>


	      </td>
	    </tr>
	    
	    <tr>
	    <td>
	     <div class="buttons">
	    <button style="display:none" onclick="save_add_location();" class="positive">{t}Save{/t}</button>
	    <button class="negative" onclick="close_add_location_dialog()" >{t}Cancel{/t}</button>
	  </div>
	    </td>
	    </tr>
	     <tr style="height:200px">
	     
	     </tr>
	   
	  </table>
	 
    </div>
</div>