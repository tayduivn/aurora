{include file='header.tpl'} 
<div id="bd">
	{include file='locations_navigation.tpl'} 
			<input type="hidden" value="{$session_data}" id="session_data" />

	<input type="hidden" value="{$category_key}" id="category_key" />
	<input type="hidden" value="{$category->get('Category Root Key')}" id="root_category_key" />
	<input type="hidden" value="{$category->get('Category Branch Type')}" id="branch_type" />
	<input type="hidden" value="{t}Invalid Category Code{/t}" id="msg_invalid_category_code" />
	<input type="hidden" value="{t}Invalid Category Label{/t}" id="msg_invalid_category_label" />
	<input type="hidden" value="{$category_key}" id="parent_key" />
	<input type="hidden" value="category" id="parent" />
	<div class="branch">
		<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>&rarr; {if $user->get_number_warehouses()>1}<a href="warehouses.php">{t}Warehouses{/t}</a> &rarr; {/if}<a href="inventory.php?warehouse_id={$warehouse->id}">{t}Inventory{/t}</a> &rarr; <a href="part_categories.php?id=0&warehouse_id={$category->get('Category Warehouse Key')}">{t}Parts Categories{/t}</a> &rarr; <span id="branch_tree">{$category->get('Category XHTML Branch Tree')}</span> ({t}Editing{/t})</span> 
	</div>
	<div class="top_page_menu">
		<div class="buttons" style="float:left">
			{if isset($prev)}<img class="previous" onmouseover="this.src='art/previous_button.gif'" onmouseout="this.src='art/previous_button.png'" title="{$prev.title}" onclick="window.location='{$prev.link}'" src="art/previous_button.png" alt="{t}Previous{/t}" />{/if} <span class="main_title">{t}Editing Category{/t}: <span class="id" id="title_code">{$category->get('Category Code')}</span> {$category->get_icon()}</span> 
		</div>
		<div class="buttons" style="float:right">
			{if isset($next)}<img class="next" onmouseover="this.src='art/next_button.gif'" onmouseout="this.src='art/next_button.png'" title="{$next.title}" onclick="window.location='{$next.link}'" src="art/next_button.png" alt="{t}Next{/t}" />{/if} <button onclick="window.location='part_category.php?id={$category->id}'"><img src="art/icons/door_out.png" alt=""> {t}Exit Edit{/t}</button> <button style="{if !$create_subcategory}display:none{/if}" id="new_category"><img src="art/icons/add.png" alt=""> {t}New Subcategory{/t}</button> <button class="negative" id="delete_category"><img src="art/icons/delete.png" alt=""> {t}Delete{/t}</button> 
		</div>
		<div style="clear:both">
		</div>
	</div>
	<ul class="tabs" id="chooser_ul" style="clear:both">
		<li> <span class="item {if $edit=='description'}selected{/if}" id="description"> <span> {t}Description{/t}</span></span></li>
		<li> <span style="{if !$create_subcategory}display:none{/if}" class="item {if $edit=='subcategory'}selected{/if}" id="subcategory"> <span> {t}Subcategories{/t}</span></span></li>
		<li> <span style="" class="item {if $edit=='parts'}selected{/if}" id="parts"> <span> {t}Parts{/t} (<span id="number_category_subjects_assigned" class="number" style="float:none;display:inline;padding:0">{$category->get('Number Subjects')}</span>)</span></span></li>
		<li> <span class="item {if $edit=='no_assigned'}selected{/if}" id="no_assigned"> <span> {t}Parts no assigned{/t} (<span id="number_category_subjects_not_assigned" class="number" style="float:none;display:inline;padding:0">{$category->get('Subjects Not Assigned')}</span>)</span></span></li>
	</ul>
	<div class="tabbed_container">
		<div class="edit_block" style="min-height:300px;{if $edit!='description'   }display:none{/if}" id="d_description">
			<div style="display:none" id="new_category_messages" class="messages_block">
			</div>
			<table class="edit" style="width:100%">
				<tr class="title">
					<td colspan="3"> {t}Category Description{/t} </td>
				</tr>
				<tr class="first">
					<td class="label" style="width:100px">{t}Code{/t}:</td>
					<td style="text-align:left;width:350px"> 
					<div>
						<input style="text-align:left;width:100%" id="Category_Code" value="{$category->get('Category Code')}" ovalue="{$category->get('Category Code')}"> 
						<div id="Category_Code_Container">
						</div>
					</div>
					</td>
					<td id="Category_Code_msg" class="edit_td_alert"></td>
				</tr>
				<tr>
					<td class="label" style="width:100px">{t}Label{/t}:</td>
					<td style="text-align:left"> 
					<div>
						<input style="text-align:left;width:100%" id="Category_Label" value="{$category->get('Category Label')}" ovalue="{$category->get('Category Label')}"> 
						<div id="Category_Label_Container">
						</div>
					</div>
					</td>
					<td id="Category_Label_msg" class="edit_td_alert"></td>
				</tr>
				<tr class="buttons">
					<td colspan="2"> 
					<div class="buttons">
						<span id="wait_edit_category" style="display:none;float:right"><img src="art/loading.gif" /> {t}Processing Request{/t}</span> <button class="disabled" id="save_edit_category" onclick="save_edit_general_bulk('category')" class="positive">{t}Save{/t}</button> <button class="disabled" id="reset_edit_category" onclick="reset_edit_general('category')" class="negative">{t}Reset{/t}</button> 
					</div>
					</td>
				</tr>
				<tr style="height:10px">
					<td colspan="3"> </td>
				</tr>
			</table>
		</div>
		<div class="edit_block" style="min-height:300px;{if $edit!='subcategory'}display:none{/if}" id="d_subcategory">
			<span class="clean_table_title">{t}Subcategories{/t}</span> 
			<div class="table_top_bar space">
			</div>
			{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 no_filter=0 } 
			<div id="table0" class="data_table_container dtable btable" style="font-size:90%">
			</div>
		</div>
		<div class="edit_block" style="min-height:300px;{if $edit!='parts'}display:none{/if}" id="d_parts">
			<div class="buttons small left" style="border:1px solid white;margin-bottom:10px">
				<button id="check_all_assigned_subjects" onclick="check_all_assigned_subject()">{t}Check All{/t}</button> <button style="display:none" id="uncheck_all_assigned_subjects" onclick="uncheck_all_assigned_subject()">{t}Uncheck All{/t}</button> <span id="checked_assigned_subjects_dialog" style="display:none;float:left;margin-right:5px;margin-left:20px">{t}With seleced parts{/t} (<span id="number_checked_assigned_subjects"></span>): </span> 
				<div id="edit_subjects_buttons" style="display:none">
					<button id="checked_assigned_subjects_assign_to_category_button" onclick="assign_to_category_checked_assigned_subject()">{t}Move to other Category{/t}</button> <button id="checked_assigned_subjects_remove_from_category_button" onclick="remove_from_category_checked_assigned_subject()">{t}Remove from Category{/t}</button> <button id="show_subjects_edit_options_button">{t}Edit Parts{/t}</button> 
				</div>
				<span id="wait_checked_assigned_subjects_assign_to_category" style="display:none;float:left;margin-right:5px;margin-left:20px"><img src="art/loading.gif" /> {t}Processing Request{/t}</span> 
				<div style="clear:both">
				</div>
			</div>
			<div id="children_table" class="data_table">
				<span class="clean_table_title">{t}Parts in this category{/t}</span> 
				<div class="table_top_bar">
				</div>
				<div class="clusters">
					<div class="buttons small left cluster">
						<button class="{if $assigned_subjects_view=='category'}selected{/if}" id="assigned_subjects_view_category" name="category">{t}Category{/t}</button> 
						<button class="{if $assigned_subjects_view=='status'}selected{/if}" id="assigned_subjects_view_status" name="status">{t}Status{/t}</button> 
						<button class="{if $assigned_subjects_view=='name'}selected{/if}" id="assigned_subjects_view_name" name="name">{t}Name{/t}</button> 
						<button class="{if $assigned_subjects_view=='weight'}selected{/if}" id="assigned_subjects_view_weight" name="weight">{t}Properties{/t}</button> 
					</div>
					<div style="clear:both">
					</div>
				</div>
				{include file='table_splinter.tpl' table_id=2 filter_name=$filter_name2 filter_value=$filter_value2} 
				<div id="table2" class="data_table_container dtable btable" style="font-size:90%">
				</div>
			</div>
		</div>
		<div class="edit_block" id="d_no_assigned" style="min-height:300px;{if $edit!='no_assigned'}display:none;{/if}">
			<div class="buttons small left" style="border:1px solid white;margin-bottom:0px">
				<button id="check_all_no_assigned_subjects" onclick="check_all_no_assigned_subject()">{t}Check All{/t}</button> <button style="display:none" id="uncheck_all_no_assigned_subjects" onclick="uncheck_all_no_assigned_subject()">{t}Uncheck All{/t}</button> <span id="checked_no_assigned_subjects_dialog" style="display:none;float:left;margin-right:5px;margin-left:20px">{t}With seleced parts{/t} (<span id="number_checked_no_assigned_subjects"></span>): </span> <button style="display:none" id="checked_no_assigned_subjects_assign_to_category_button" onclick="assign_to_category_checked_no_assigned_subject()">{if $category->get('Category Branch Type')=='Head'}{t}Assign to this Category{/t}{else}{t}Assign to Category{/t}{/if}</button> <span id="wait_checked_no_assigned_subjects_assign_to_category" style="display:none;float:left;margin-right:5px;margin-left:20px"><img src="art/loading.gif" /> {t}Processing Request{/t}</span> 
				<div style="clear:both">
				</div>
			</div>
			<div id="children_table" class="data_table" style="clear:both;margin-top:10px">
				<span class="clean_table_title">{t}Parts not assigned{/t}</span> 
				<div class="table_top_bar space">
				</div>
				{include file='table_splinter.tpl' table_id=3 filter_name=$filter_name3 filter_value=$filter_value3} 
				<div id="table3" class="data_table_container dtable btable" style="font-size:90%">
				</div>
			</div>
		</div>
	</div>
	<div class="buttons small" style="margin-top:0">
		<button id="show_history" style="{if $show_history}display:none{/if};margin-right:0px" onclick="show_history()">{t}Show changelog{/t}</button> <button id="hide_history" style="{if !$show_history}display:none{/if};margin-right:0px" onclick="hide_history()">{t}Hide changelog{/t}</button> 
	</div>
	<div id="history_table" class="data_table" style="clear:both;{if !$show_history}display:none{/if}">
		<span class="clean_table_title">{t}Changelog{/t}</span> 
		<div id="table_type" class="table_type">
			<div style="font-size:90%" id="part_type_chooser">
				<span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $history_elements.Changes}selected{/if} label_part_Changes" id="elements_Changes" table_type="Changes">{t}Changes{/t} (<span id="elements_Changes_number">{$history_elements_number.Changes}</span>)</span> <span style="float:right;margin-left:20px" class=" table_type transaction_type state_details {if $history_elements.Assign}selected{/if} label_part_Assign" id="elements_Assign" table_type="Assign">{t}Assign{/t} (<span id="elements_Assign_number">{$history_elements_number.Assign}</span>)</span> 
			</div>
		</div>
		<div class="table_top_bar space">
		</div>
		{include file='table_splinter.tpl' table_id='1' filter_name=$filter_name1 filter_value=$filter_value1 } 
		<div id="table1" class="data_table_container dtable btable">
		</div>
	</div>
</div>
<div id="filtermenu0" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu0 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',0)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu0" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu0 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},0)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu2" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu2 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',2)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu2" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu2 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},2)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="filtermenu3" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Filter options{/t}:</li>
			{foreach from=$filter_menu3 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_filter('{$menu.db_key}','{$menu.label}',3)"> {$menu.menu_label}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu3" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu3 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},3)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu4" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu4 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},4)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="rppmenu5" class="yuimenu">
	<div class="bd">
		<ul class="first-of-type">
			<li style="text-align:left;margin-left:10px;border-bottom:1px solid #ddd">{t}Rows per Page{/t}:</li>
			{foreach from=$paginator_menu5 item=menu } 
			<li class="yuimenuitem"><a class="yuimenuitemlabel" onclick="change_rpp({$menu},5)"> {$menu}</a></li>
			{/foreach} 
		</ul>
	</div>
</div>
<div id="dialog_subject_no_assigned_list">
	<div class="splinter_cell" style="padding:10px 15px 10px 0;border:none;width:650px">
		<div class="data_table">
			<span class="clean_table_title">{t}No Assigened Parts{/t}</span> {include file='table_splinter.tpl' table_id=4 filter_name=$filter_name4 filter_value=$filter_value4} 
			<div id="table4" class="data_table_container dtable btable">
			</div>
		</div>
	</div>
</div>
<div id="dialog_category_heads_list">
	<div class="splinter_cell" style="padding:10px 15px 10px 0;border:none;width:650px">
		<div class="data_table">
			<span class="clean_table_title">{t}Categories{/t}</span> {include file='table_splinter.tpl' table_id=5 filter_name=$filter_name5 filter_value=$filter_value5} 
			<div id="table5" class="data_table_container dtable btable">
			</div>
		</div>
	</div>
</div>
<div id="dialog_delete_category" style="padding:20px 10px 10px 10px;">
	<h2 style="padding-top:0px">
		{t}Delete Category{/t} 
	</h2>
	<p>
		{t}This operation cannot be undone{/t}.<br> {t}Would you like to proceed?{/t} 
	</p>
	<div style="display:none" id="deleting">
		<img src="art/loading.gif" alt=""> {t}Deleting category, wait please{/t} 
	</div>
	<div id="delete_category_buttons" class="buttons">
		<button id="save_delete_category" class="positive">{t}Yes, delete it!{/t}</button> <button id="cancel_delete_category" class="negative">{t}No i dont want to delete it{/t}</button> 
	</div>
</div>
<div id="dialog_delete_category_from_list" style="padding:5px 10px 10px 10px;">
	<input type="hidden" id="delete_from_list_category_key" value=''> 
	<h2 style="padding-top:0px">
		{t}Delete Category{/t} <span class="id" id="delete_from_list_category_code"></span> 
	</h2>
	<p>
		{t}This operation cannot be undone{/t}.<br> {t}Would you like to proceed?{/t} 
	</p>
	<div id='delete_category_msg_from_list'>
	</div>
	<div style="display:none" id="deleting_from_list">
		<img src="art/loading.gif" alt=""> {t}Deleting category, wait please{/t} 
	</div>
	<div id="delete_category_buttons_from_list" class="buttons">
		<button id="save_delete_category_from_list" onclick="save_delete_category_from_list()" class="positive">{t}Yes, delete it!{/t}</button> <button onclick="cancel_delete_category_from_list()" id="cancel_delete_category_from_list" class="negative">{t}No i dont want to delete it{/t}</button> 
	</div>
</div>
<div id="dialog_edit_subjects" style="padding:10px 20px 0px 10px">
	<table class="edit" border="0" style="width:400px">
		<tbody id="dialog_edit_subjects_fields">
			<tr class="title">
				<td>{t}Edit selected parts{/t}:</td>
			</tr>
			<tr style="height:5px">
				<td></td>
			</tr>
			<tr id="edit_selected_parts_status_tr">
				<td class="label"> {t}Set status as{/t}: </td>
				<td> 
				<div class="buttons small left">
					<button id="edit_selected_parts_status_In_Use"> {t}In Use{/t}</button> <button id="edit_selected_parts_status_Not_In_Use"> {t}Not in use{/t}</button> 
				</div>
				</td>
			</tr>
			<tr id="edit_selected_parts_weight_tr">
				<td class="label"> {t}Set weight as{/t}: </td>
				<td> 
				<input value="" id="edit_selected_parts_weight" />
				Kg </td>
			</tr>
			<tr style="height:10px">
				<td> </td>
			</tr>
			<tr>
				<td colspan="2"> 
				<div class="buttons">
					<button id="save_edit_selected_parts" onclick="save_edit_selected_parts()" class="positive disabled"> {t}Save{/t}</button> <button style="display:none" id="cancel_edit_selected_parts" onclick="cancel_edit_selected_parts()" class="negative"> {t}Cancel{/t}</button> <button id="close_edit_selected_parts" onclick="close_edit_selected_parts()" class="negative"> {t}Close{/t}</button> 
				</div>
				</td>
			</tr>
		</tbody>
		<tbody id="dialog_edit_subjects_wait" style="display:none">
			<tr>
				<td colspan="2" style="text-align:right"> <img src="art/loading.gif" /> {t}Processing Request{/t} <span style="font-weight:800;font-size:120%;padding-left:10px"><span id="dialog_edit_subjects_wait_done"></span></span> </td>
			</tr>
		</tbody>
		<tbody id="dialog_edit_subjects_results" style="display:none">
			<tr class="title">
				<td colspan="2">{t}Edit results{/t}</td>
			</tr>
			<tr id="dialog_edit_subjects_parts_updated_tr">
				<td class="label">{t}Parts updated{/t}:</td>
				<td style="width:150px" id="dialog_edit_subjects_parts_updated"></td>
			</tr>
			<tr id="dialog_edit_subjects_parts_nochanged_tr" style="display:none">
				<td class="label">{t}Parts with no need to update{/t}:</td>
				<td id="dialog_edit_subjects_parts_nochanged"></td>
			</tr>
			<tr id="dialog_edit_subjects_parts_errors_tr" style="display:none">
				<td class="label">{t}Errors{/t}:</td>
				<td id="dialog_edit_subjects_parts_errors"></td>
			</tr>
			<tr style="height:10px">
				<td colspan="2"> </td>
			</tr>
			<tr>
				<td colspan="2"> 
				<div class="buttons">
					<button id="close_edit_selected_parts" onclick="close_edit_selected_parts()"> {t}Close{/t}</button> 
				</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
{include file='new_category_splinter.tpl'} {include file='footer.tpl'} 