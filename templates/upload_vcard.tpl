﻿<html>
<head>
<title>{$title}</title>
<head>
{include file='header.tpl'}
<body>
<div id="bd" >

<div id="no_details_title" style="clear:left;{if $show_details}display:none;{/if}">
    <h1>Import Contacts From vCard File</h1>
  </div>
<br>
	<a  style='font-size: 14px; color:#3B5998; text-decoration: none;'>{$showerror} </a>
<strong><h3>Upload your File:</h3></strong>

<div class="prop">

	<lable class="import_level" for="fileUpload">vCard file:</label>
	<form enctype='multipart/form-data' action='' method='post'>
  	<input type='hidden' name='MAX_FILE_SIZE' value='2000000' />
  	<span><input id="vcard_file" type='file' name='file' size="50" class="input2" /></span>
</div>
   <div class="clear"></div>	

  <div class="bt">
  	<input type="submit" value="Upload & Preview" name="upload" id="upload_vcard" />
  </div>
   </form>


</div>
</body>
{include file='footer.tpl'}
</html>
