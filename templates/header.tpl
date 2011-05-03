<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang='en' xml:lang='en' xmlns="http://www.w3.org/1999/xhtml">
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{$title}</title>
    <link href="art/inikoo-icon.png" rel="shortcut icon" type="image/x-icon" />
    {foreach from=$css_files item=i }
    <link rel="stylesheet" href="{$i}" type="text/css" />
    {/foreach}	
    {foreach from=$js_files item=i }
    <script type="text/javascript" src="{$i}"></script>
    {/foreach}
    <script type="text/javascript">{$script}</script>
        
  </head>

  <body  class=" yui-skin-sam inikoo">
    <div id="{$page_layout}" class="{$box_layout}">
    
      <div id="hd" >
  	<div style="float:left;font-size:65%;margin-left:16px;" class='timezone'>{$timezone}</div>
  	<div style="float:right;font-size:77%;margin:0px 20px 0.15em 0">
	            <span id="top_message" style=";margin-right:30px"></span>
	            <a style=";margin-right:15px">{t}Help{/t}</a>
	            <span id="language_flag"><img src="art/flags/{$lang_country_code}.gif" alt="{$lang_country_code}"  /></span>
	            <span>{t}Hello{/t} {$user}</span>
	            <span><a style="margin-left:20px;" href="index.php?logout=1">{t}Logout{/t}</a></span>
	        </div>
	        
	        <h1 style="clear:both;padding-top:0;position:relative;top:-2px">{$my_name}<span style="font-size:70%;color:#f7fd98">@</span><span style="position:relative;bottom:3px;font-size:60%;color:#d7e12a">inikoo</span></h1>
	        
	        <div id="navsite" style="clear:right">
        	  <ul>
	            {foreach from=$nav_menu item=menu }
	            <li {if $menu[2]==$parent} class="selected"{/if}><a href="{$menu[1]}">{$menu[0]}</a></li>
	            {/foreach}
	          </ul>
        	</div> 
      </div> 
      
