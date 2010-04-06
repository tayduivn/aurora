{include file="$head_template"}
 <body>
   <div id="container" >
     {include file="$home_header_template"}
     <div id="page_content">
       {include file="$left_menu_template"}
     
       <div id="central_content">
	 <div id="search" >
	   Search: <input type="text"/>
	 </div>

	 

           <div class="block" id="product_list_layout">
	     <table class="families">
	      {foreach from=$families item=family}
	      <tr><td><a href="family.php?code={$family.code}">{$family.code}</a></td><td><a href="family.php?id={$family.code}"   >{$family.name}</a></td></tr>
	      {/foreach}
	     </table>
	   </div>

	 

       </div>
       
     </div>


     {include  file="$footer_template"}
 </body>
