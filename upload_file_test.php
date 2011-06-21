<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>


    <meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Simple Uploader Example With Button UI</title>

<style type="text/css">
/*margin and padding on body element
  can introduce errors in determining
  element position and are not recommended;
  we turn them off as a foundation for YUI
  CSS treatments. */
body {
	margin:0;
	padding:0;
}
</style>

<link rel="stylesheet" type="text/css" href="external_libs/yui/2.9/build/fonts/fonts-min.css" />
<script type="text/javascript" src="external_libs/yui/2.9/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="external_libs/yui/2.9/build/element/element-min.js"></script>

<script type="text/javascript" src="external_libs/yui/2.9/build/uploader/uploader-min.js"></script>
<script type="text/javascript" src="upload_common.js.php"></script>

<!--there is no custom header content for this example-->

</head>

<body class="yui-skin-sam">


<h1>Simple Uploader Example With Button UI</h1>

<div class="exampleIntro">
	<p>This example is a demonstration of the <a href="../../uploader/">YUI Uploader Control</a>'s features.</p>

<p><strong>Note:</strong> The YUI Uploader Control requires Flash Player 9.0.45 or higher. The latest version of Flash Player is available at the <a href="http://www.adobe.com/go/getflashplayer">Adobe Flash Player Download Center</a>.</p>
<p><strong>Note:</strong> The YUI Uploader Control requires the uploader.swf Flash file that is distributed as part of the YUI package, in the uploader/assets folder. Copy the uploader.swf to your server and set the YAHOO.Uploader.SWFURL variable to its full path.</p>
<p><strong>Note:</strong> This example uses a server-side script to accept file uploads. The script used does not open or store any data sent to it. Nonetheless, when trying out the example, do not send any sensitive or private data. Do not exceed file size of 10 MB.			
</div>

<!--BEGIN SOURCE CODE FOR EXAMPLE =============================== -->

<style type="text/css">
	.uploadButton a, .clearButton a {
		display:block;
		width:100px;
		height:40px;
		text-decoration: none;
		margin-left:5px;
	}
	
	.uploadButton a {
		background: url("art/uploadFileButton.png") 0 0 no-repeat;
	}
	
	.clearButton a {
		background: url("art/clearListButton.png") 0 0 no-repeat;
	}
	
    .uploadButton a:visited, .clearButton a:visited {
		background-position: 0 0;
	}
	
    .uploadButton a:hover, .clearButton a:hover {	
		background-position: 0 -40px;
	}
	
    .uploadButton a:active, .clearButton a:active {
		background-position: 0 -80px;
	}
</style>

<div>
	<div id="fileProgress" style="border: black 1px solid; width:300px; height:40px;float:left">
		<div id="fileName" style="text-align:center; margin:5px; font-size:15px; width:290px; height:25px; overflow:hidden"></div>
		<div id="progressBar" style="width:300px;height:5px;background-color:#CCCCCC"></div>
	</div>
<div id="uploaderUI" style="width:100px;height:40px;margin-left:5px;float:left"></div>
<div class="uploadButton" style="float:left"><a class="rolloverButton" href="#" onClick="upload(); return false;"></a></div>
<div class="clearButton" style="float:left"><a class="rolloverButton" href="#" onClick="handleClearFiles(); return false;"></a></div>
</div>




<!--END SOURCE CODE FOR EXAMPLE =============================== -->


<!--MyBlogLog instrumentation-->

</body>
</html>