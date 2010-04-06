{include file="$head_template"}
 <body>
   <div id="container" >
     {include file="$home_header_template"}
     <div id="page_content">
       
     
       <div id="central_content">
	 <div id="search" >
	   Search: <input type="text"/>
	 </div>

	 

           <div class="block" id="product_list_layout">
	     <table class="products">
	      {foreach from=$products item=product}
	      <tr><td>{$product.code}</td><td>{$product.name}</td><td>{$product.price}</td></tr>
	      {/foreach}
	     </table>
	   </div>

	 

       </div>
       
     </div>


     {include  file="$footer_template"}
 </body>
