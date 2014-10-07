{include file='header.tpl'} 
<div id="bd">
	<h1>
		{t}New Product{/t} 
	</h1>
	<div id="contact_messages_div">
		<span id="contact_messages"></span> 
	</div>
	<div>
		<div id="results" style="margin-top:0px;float:right;width:600px;">
		</div>
		<div style="float:left;width:600px;">
			<table class="edit" border="0" style="width:100%;margin-bottom:0px">
				<input type="hidden" value="" id="store_key" />
				<input type="hidden" value="" id="family_key" />
				<input type="hidden" value="{$part_id}" id="part_id" />
				<tr class="title">
					<td colspan="3">{t}Product Info{/t}</td>
				</tr>
				<tr style="display:none">
					<td style="width:200px" class="label">{t}Store{/t}:</td>
					<td style="width:370px"> 
					<div>
						<input style="width:100%" id="store_code" changed="0" type='text' maxlength="255" class='text' value="" />
						<div id="store_code_Container">
						</div>
					</div>
					</td>
					<td id="store_list" onclick="view_store_list(this)">{t}List{/t}</td>
					<td id="store_code_msg" class="edit_td_alert"></td>
				</tr>
				<tr>
					<td style="width:200px" class="label">{t}Family{/t}:</td>
					<td style="width:370px"> 
					<div>
						<input style="width:100%" id="family_code" changed="0" type='text' maxlength="255" class='text' value="" />
						<div id="family_code_Container">
						</div>
					</div>
					</td>
					<td id="family_list" onclick="view_family_list(this)">{t}List{/t}</td>
					<td id="family_code_msg" class="edit_td_alert"></td>
				</tr>
				<tr>
					<td style="width:200px" class="label">{t}Code{/t}:</td>
					<td style="width:370px"> 
					<div>
						<input style="width:100%" id="product_code" changed="0" type='text' maxlength="255" class='text' value="" />
						<div id="product_code_Container">
						</div>
					</div>
					</td>
					<td id="product_code_msg" class="edit_td_alert"></td>
				</tr>
				<tr>
					<td style="width:200px" class="label">{t}Name{/t}:</td>
					<td style="width:370px"> 
					<div>
						<input style="width:100%" id="product_name" changed="0" type='text' maxlength="255" class='text' value="" />
						<div id="product_name_Container">
						</div>
					</div>
					</td>
					<td id="product_name_msg" class="edit_td_alert"></td>
				</tr>
				<tr>
					<td style="width:200px" class="label">{t}Weight{/t}:</td>
					<td style="width:370px"> 
					<div>
						<input style="width:100%" id="product_weight" changed="0" type='text' maxlength="255" class='text' value="" />
						<div id="product_weight_Container">
						</div>
					</div>
					</td>
					<td id="product_weight_msg" class="edit_td_alert"></td>
				</tr>
				<tr>
					<td style="width:200px" class="label">{t}Special Characteristics{/t}:</td>
					<td style="width:370px"> 
					<div>
						<input style="width:100%" id="special_characteristics" changed="0" type='text' maxlength="255" class='text' value="" />
						<div id="special_characteristics_Container">
						</div>
					</div>
					</td>
					<td id="special_characteristics_msg" class="edit_td_alert"></td>
				</tr>
				<tr>
					<td style="width:200px" class="label">{t}Description{/t}:</td>
					<td style="width:370px"> 
					<div>
						<input style="width:100%" id="product_description" changed="0" type='text' maxlength="255" class='text' value="" />
						<div id="product_description_Container">
						</div>
					</div>
					</td>
					<td id="product_description_msg" class="edit_td_alert"></td>
				</tr>
			</table>
			<table class="options" style="float:right;padding:0;margin:0">
				<tr>
					<div class="buttons">
						<button style="margin-right:10px;visibility:" id="save_new_product" class="positive disabled">{t}Save{/t}</button> <button style="margin-right:10px;visibility:" id="reset_new_product" class="negative">{t}Reset{/t}</button> 
					</div>
				</tr>
			</table>
		</div>
		<div style="clear:both;height:40px">
		</div>
	</div>
	<div class="star_rating" id="star_rating_template" style="display:none">
		<img class="star" src="art/icons/star_dim.png" /><img class="star" src="art/icons/star_dim.png" /><img class="star" src="art/icons/star_dim.png" /><img class="star" src="art/icons/star_dim.png" /><img class="star" src="art/icons/star_dim.png" /> 
	</div>
</div>
<div id="dialog_store_list">
	<div class="splinter_cell" style="padding:10px 15px 10px 0;border:none">
		<div id="the_table" class="data_table">
			<span class="clean_table_title">{t}Store List{/t}</span> {include file='table_splinter.tpl' table_id=0 filter_name=$filter_name2 filter_value=$filter_value2} 
			<div id="table0" class="data_table_container dtable btable">
			</div>
		</div>
	</div>
</div>
<div id="dialog_family_list">
	<div class="splinter_cell" style="padding:10px 15px 10px 0;border:none">
		<div id="the_table" class="data_table">
			<span class="clean_table_title">{t}Family List{/t}</span> {include file='table_splinter.tpl' table_id=2 filter_name=$filter_name2 filter_value=$filter_value2} 
			<div id="table2" class="data_table_container dtable btable">
			</div>
		</div>
	</div>
</div>
< {include file='footer.tpl'}