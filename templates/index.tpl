{include file='header.tpl'} 
<div id="bd" style="padding:0px 0px">
	<div style="padding:0px 20px">
	{include file='top_search_splinter.tpl'} 
			<div class="branch">
				<span><a href="index.php"><img style="vertical-align:0px;margin-right:1px" src="art/icons/home.gif" alt="home" /></a>
			</div>
		<div style="clear:both;width:100%;border-bottom:1px solid #ccc;padding-bottom:3px">
			{if $number_of_dashboards> 1}<img onmouseover="this.src='art/previous_button.gif'" onmouseout="this.src='art/previous_button.png'" title="{t}Previous Dashboard{/t} {$prev.name}" onclick="window.location='index.php?dashboard_id={$prev.id}'" src="art/previous_button.png" alt="<" style="margin-right:10px;float:left;height:22px;cursor:pointer;position:relative;top:2px" />{/if} 
			<div class="buttons" style="float:left">
			</div>
			{if $number_of_dashboards> 1}<img onmouseover="this.src='art/next_button.gif'" onmouseout="this.src='art/next_button.png'" title="{t}Next Dashboard{/t} {$next.name}" onclick="window.location='index.php?dashboard_id={$next.id}'" src="art/next_button.png" alt=">" style="float:right;height:22px;cursor:pointer;position:relative;top:2px" />{/if} 
			<div class="buttons" style="float:right">
				<button onclick="window.location='edit_dashboard.php?id={$dashboard_key}'"><img src="art/icons/cog.png" alt=""> {t}Configure Dashboard{/t}</button> 
			</div>
			<div style="clear:both">
			</div>
		</div>
	</div>

		<div class="dashboard_blocks" style="margin-top:20px">
			{foreach from=$blocks key=key item=block} 
			<div class="{$block.class}" style="margin-bottom:30px">
				<iframe onload="changeHeight(this);" id="block_{$block.key}" src="{$block.src}&block_key={$block.key}" width="100%" {if $block.height}height="{$block.height}" {/if} frameborder="0" scrolling="no"> 
				<p>
					{t}Your browser does not support iframes{/t}. 
				</p>
				</iframe> 
			</div>
			{/foreach} 
		</div>
	</div>
	{include file='footer.tpl'} 