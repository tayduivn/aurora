<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang='en' xml:lang='en' xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>{$title}</title>
	<link href="{$site->get_favicon_url()}" rel="shortcut icon" type="image/x-icon" />
	{foreach from=$css_files item=i } 
	<link rel="stylesheet" href="{$i}" type="text/css" />
	{/foreach} {foreach from=$js_files item=i } <script type="text/javascript" src="{$i}"></script> {/foreach} <style type="text/css">{$page->get_css()}</style> <script type="text/javascript">{$page->get_javascript()}</script> 
	<link rel="stylesheet" href="public_menu.css.php?id={$site->id}" type="text/css" />
<link rel="stylesheet" href="css/prettyPhoto.css" type="text/css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" src="public_menu.js.php?id={$site->id}"></script> 


{if $site->get('Site Search Method')=='Custome'}
	<link rel="stylesheet" href="public_search.css.php?id={$site->id}" type="text/css" />
	<script type="text/javascript" src="public_search.js.php?id={$site->id}"></script> 

{else}
	<link rel="stylesheet" href="css/bar_search.css" type="text/css" />
	<script type="text/javascript" src="js/bar_search.js"></script> 
{/if}



</head>
<body class="yui-skin-sam inikoo">
<input type="hidden" id="take_snapshot" value="{$take_snapshot}" />
<input type="hidden" id="update_heights" value="{$update_heights}" />
<input type="hidden" id="page_key" value="{$page->id}" />

<div id="doc4">

	<div id="hd" style="position: relative ;padding:0;margin:0;z-index:3">
		{*}{include file="header.tpl" }{*} {include file="string:{$page->get_header_template()}" } 
	</div>
	<div id="bd" style="position: relative ;z-index:1;">

{if $page->get('Page Store Content Display Type')=='Template'}
{include file="$type_content:$template_string" } 
{else}
		<div id="content" class="content" style="overflow-x:hidden;overflow-y:auto;position:relative;clear:both;width:100%;{if $type_content=='string'}height:{$page->get('Page Content Height')}px{/if}">
			{include file="$type_content:$template_string" } 
		</div>

{/if}
	</div>
	<div id="ft" style="position: relative ;z-index:2;{if $page->get('Page Footer Type')=='None'}display:none{/if}">	
		{include file="string:{$page->get_footer_template()}" } 
	</div>
</div>
</body>
</html>
