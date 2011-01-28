<html>
<head>


{include file='header.tpl'}

<script src="http://code.jquery.com/jquery-1.4.4.js"></script>
  
  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
	
  
<div id="bd" >
	<div style="padding:0 20px">
		{include file='marketing_navigation.tpl'}

		</head>



		<body onload="hide();">
		<div style="clear:left;margin:0 0px">
   		 <h1>{t}List{/t}</h1>
		</div>

	</div>
	
 	

	<div id="block_metrics" style="{if $view!='metrics'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
	</div>
	<div id="block_newsletter" style="{if $view!='newsletter'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
	</div>
	<div id="block_email" style="{if $view!='email'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">


  		<span   class="clean_table_title" style="">{t}Email Campaigns{/t}</span>


  		<div style="clear:both;margin:0 0px;padding:0 20px ;border-bottom:1px solid #999;margin-bottom:15px"></div>
    
   
 		{include file='table_splinter.tpl' table_id=0 filter_name=$filter_name0 filter_value=$filter_value0 no_filter=0  }
		<div  id="table0"   class="data_table_container dtable btable"> </div>


	</div>
	<div id="block_web_internal" style="{if $view!='web_internal'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
	</div>
	<div id="block_web" style="{if $view!='web'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
	</div>
	<div id="block_other" style="{if $view!='other'}display:none;{/if}clear:both;margin:20px 0 40px 0;padding:0 20px">
	</div>
	<div id="check_div">Please Check Your Entry And Try Again</div>
	<div id="left_panel">
		<a onClick="" style="text-decoration:none; padding:10px;"><div id="create_new_list">Create New List
		</div></a><br>
		<div id="signup_forms">Design Sign up forms
		</div><br>
		<input type="text" name="list_search" id="list_search" value="Search List Subscribers" onClick="empty_text();">
		<input type="submit" value="Go" class="list_search_button">

	</div>


	{literal}
 	<script>
   	 $("#create_new_list").click(function () {
     	 $('#list_or_group').slideDown("slow");
    	 });
	 function slideCrm()
	 {
      	 $('#list_or_group').slideDown("slow");
		}
	</script>
	
	
	{/literal}


	<div id="list_or_group" style="display:none;">
		New List?.....or Groups?<br><br>
		Do you really want to create a new list, or do you just want to sub-divide an existing list with groups?<br><br>
		<div style="padding-left:10px;">		
			<div class="list_bt" style=" float: left;">
  			<input type="button" value="Create List" name="create_list" id="create_list" onClick="show_list();"/>
  			</div>
					
			<div class="list_bt" id="create_group_div" style="padding-left:70px; width:170px">
  			<input type="button" value="Create Group" name="create_group" id="create_group" onClick="show_group();"/>
  			</div>
		</div>
	</div>
	<div id="group_div" class="main_div" style="display:none">
		Create Group<br><br>
		These groups go in which list?<br>
		<select>
  		<option value="choose">Choose a list</option>
 		</select> <br><br>
		How should we show group options on your signup form?<br>
		<select>
  		<option value="choose">Choose a list</option>
 		</select> <br><br>
		Group Title<br>
		<input type="text" name="group_title" id="group_title" class="av_text" style="width:670px;"">
		<div id="group_msg" class="invalid-error">Example: "Interested in ..." or "Food Preferences".</div>
		<div id="add_group"style="border:1px solid #AAAAAA; padding-left:10px; padding-right:10px; "></div>
		<a style="cursor: pointer; text-decoration:underline;" onClick="CreateTextbox()">+ add group</a><br><br>
		
		<div style="padding-left:10px;">		
			<div class="list_bt" style=" float: left;">
  			<input type="button" value="Save" name="save_group" id="save_group" onClick=""/>
  			</div>
			<div style="padding-left:70px;">		
			<div class="list_bt">
  			<input type="button" value="Cancel" name="cancel_group" id="cancel_group" onClick="document.getElementById('group_div').style.display = 'none';"/>
  			</div>
			</div>
		</div>
	</div>
	<div id="new_list">
		<form action="" method="post" name="list_form" onSubmit="return validate_form();">

		<div id="list_div" class="sub_head">List Name</div>
		<div id="name_div"><input type="text" name="list_name" id="list_name" class="av_text" style="width:670px;" onClick="show('list_msg');"></div>
		<div id="list_msg" class="invalid-error">Please enter a value</div>  <br>

		<div id="default_name_div" class="sub_head">Default from Name</div>
		<input type="text" name="default_name" id="default_name" class="av_text" style="width:670px;"  onClick="show('default_name_msg');">  
		<div id="default_name_msg" class="invalid-error">Use something recognizable</div> <br><br>

		<div id="email_div" class="sub_head">Default reply-to email</div>
		<input type="text" name="default_email" id="default_email" class="av_text" style="width:670px;"  onClick="show('email_msg');">  
		<div id="email_msg" class="invalid-error">This is the address, people will reply to.</div><br><br>
	
		<div ="subject_div" class="sub_head">Default subject</div>
		<input type="text" name="default_subject" id="default_subject" class="av_text" style="width:670px;"  onClick="show('subject_msg');">  
		<div id="subject_msg" class="invalid-error">Keep it relevent and non-spammy</div> <br><br>
	
	
		<div id="remind_div" class="sub_head">Remind people how they got on your list *</div>
		<div id="permission_list" style="width:250px; float: left;" >Copy permission reminder from other list</div> 
		<select onClick="show('remind_msg')">
  		<option value="choose">Choose a list</option>
 		
		</select> <br><br>
	
	
		<TEXTAREA id="description" NAME="description" COLS=88 ROWS=3 onClick="hide();" style="-moz-border-radius: 5px 5px 5px 5px; padding-right:5px; padding-left:5px; "></TEXTAREA>
		<div id="remind_msg" class="invalid-error">You are receiving this email because you opted at our website....</div> <br><br>


		<div id="info_div" class="sub_head">Is this the correct contact info for this list? why is this necessary?</div>
		<div id="contact_div" style="border:1px solid #AAAAAA; padding:10px; -moz-border-radius: 5px 5px 5px 5px; padding-right:5px; padding-left:5px; ">This address<br> will come form database<br>
			<div class="bt" id="edit_div"><input type="button" value="Edit" name="edit" id="edit" onClick="edit_contact();"/></div>
				
		</div>
		<div id="edit_contact_div"  style="border:1px solid #AAAAAA; padding:10px;">
			<div class="sub_head">Company</div>
			<input type="text" name="company_name" id="company_name"  class="av_text" style="width:649px;"><br><br>
			<div class="sub_head">Address</div>
			<input type="text" name="address" id="address" class="av_text"  style="width:649px;"><br><br>
			<input type="text" name="address2" id="address2" class="av_text" style="width:649px;"><br><br>
			<div class="sub_head">City</div>
			<input type="text" name="city" id="city" class="av_text" style="width:649px;"><br><br>
			<div class="sub_head">State</div>
			<input type="text" name="state" id="state" class="av_text" style="width:649px;"><br><br>
			<div class="sub_head">Zip/Postal Code</div>
			<input type="text" name="pin" id="pin" class="av_text" style="width:649px;"><br><br>
			<div class="sub_head">Country</div>
			<select >
  				<option value="choose">Choose Country</option>
 			</select> <br><br>
			<div class="sub_head">Phone</div>
			<input type="text" name="phone" id="phone" class="av_text" style="width:649px;"><br><br>
		</div>

		<br><br>

		<div id="email_me_div" class="sub_head">Email me at ******@****.com <a onClick="edit_email();" style="text-decoration:underline" >edit</a> when ...</div>
		<div id="edit_email_div" class="sub_head">Email me at <input type="text" id="edit_email" name="edit_email"style="-moz-border-radius: 5px 5px 5px 5px;">  when ...</div>
		<br>
		<hr>
		<INPUT TYPE=CHECKBOX NAME="mushrooms"   >People Subscribe
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<INPUT TYPE=CHECKBOX NAME="greenpeppers">People Unsubscribe <br><br><br>
		
		<div id="email_format_div" class="sub_head">Email Format</div>
		<hr>
		<INPUT TYPE=CHECKBOX NAME="mushrooms"   >People can pick Email format (HTML, plain-text, or Mobile)<br><br><br>

		<div id="list_data_div" class="sub_head">Auto-enhance list data</div>
		<hr>
		<INPUT TYPE=CHECKBOX NAME="mushrooms"   >Activate <a href="#">SocialPro</a><br><br>
		
		<div class="bt" style=" float: left;">
  		<input type="submit" value="Save" name="save_list" id="save_list" />
  		</div>
		
		<div style="padding-left:70px;">
		<div class="bt">
  		<input type="button" value="Cancel" name="cancel_list" id="cancel_list" onClick="document.location='marketing.php'; return false;"/>
  		</div>
		</div>
		</form>
	</div>
 	
	<br>
	
</div>

{include file='footer.tpl'}



</body>
</html>
