<div id="footer">
<a style="margin-left:0px" href="terms_use.php">{t}Terms of use{/t}</a>
<a style="margin-left:10px;display:none" href="report_issue.php?t=bug">{t}Report a problem{/t}</a>
<a style="margin-left:10px;display:none" href="report_issue.php?t=feature">{t}Request a feature{/t}</a>

<div class='adv' style="margin-top:2px">{t}Powered by Inikoo{/t}</div>
</div> 
<div id="langmenu" class="yuimenu">
  <div class="bd">
    <ul class="first-of-type">
      {foreach from=$lang_menu item=menu }
      <li class="yuimenuitem"><a class="yuimenuitemlabel" href="{$menu[0]}"><img style="position:relative;top:-3.5px" src="art/flags/{$menu[1]}.gif"/ > {$menu[2]}</a></li>
      {/foreach}
    </ul>
  </div>
</div>
</div>
</body>
</html>
