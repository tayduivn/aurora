{include file='header.tpl'}
<div id="bd" >
 {include file='orders_navigation.tpl'}

     <div style="border:1px solid #ccc;text-align:left;padding:10px;margin: 30px 0 10px 0">

       <div style="border:0px solid #ddd;width:400px;float:left"> 
         <h1 style="padding:0 0 10px 0">{t}Order{/t} {$order->get('Order Public ID')}</h1>

         <h2 style="padding:0">{$order->get('Order Customer Name')} (<a href="customer.php?id={$order->get("Order Customer Key")}">{$customer->get_formated_id()}</a>)</h2>
         {$contact}<br/>
           {if $tel!=''}{t}Tel{/t}: {$tel}<br/>{/if}
	 <div style="float:left;line-height: 1.0em;margin:5px 30px 0 0px;color:#444"><span style="font-weight:500;color:#000"><b>{$order->get('Order Customer Contact Name')}</b><br/>{$customer->get('Customer Main XHTML Address')}</div>
	 <div style="float:left;line-height: 1.0em;margin:5px 0 0 0px;color:#444"><span style="font-weight:500;color:#000">{t}Shipped to{/t}</span>:<br/>{$order->get('Order XHTML Ship Tos')}</div>
	 
	<div style="clear:both"></div>
       </div>
       
       <div style="border:0px solid #ddd;width:250px;float:right">
	 <table border=0  style="width:100%;border-top:1px solid #333;border-bottom:1px solid #333;width:100%,padding:0;margin:0;float:right;margin-left:0px" >
	  
	   <tr><td  class="aright" >{t}Ordered Amount{/t}</td><td width=100 class="aright">{$order->get('Total Amount')}</td></tr>
	    {if $order->get('Order Out of Stock Amount')!=0  }
	   <tr><td  class="aright" >{t}Out of Stock{/t}</td><td width=100 class="aright">-{$order->get('Out of Stock Amount')}</td></tr>
	   {/if}
	   
	   <tr><td colspan=2 style="font-size:70%;border-top:1px solid #ccc;border-bottom:1px solid #eee">{t}Invoiced Amounts (before tax){/t}</td></tr>
	  
	    {if $order->get('Order Invoiced Refund Net Amount')!=0}
        <tr><td  class="aright" ><i>{t}Other Order Refunds{/t}</i></td><td width=100 class="aright">{$order->get('Invoiced Refund Net Amount')}</td></tr>
	   {/if}
	  <tr><td  class="aright" >{t}Items{/t}</td><td width=100 class="aright">{$order->get('Invoiced Items Amount')}</td></tr>

	   	<tr><td  class="aright" >{t}Shipping{/t}</td><td width=100 class="aright">{$order->get('Invoiced Shipping Amount')}</td></tr>
	   	{if $order->get('Order Invoiced Charges Amount')!=0}
	   	<tr><td  class="aright" >{t}Charges{/t}</td><td width=100 class="aright">{$order->get('Invoiced Charges Amount')}</td></tr>
        {/if}
        {if $order->get('Order Net Refund Amount')!=0}
        <tr><td  class="aright" >{t}Net{/t}</td><td width=100 class="aright">{$order->get('Net Refund Amount')}</td></tr>
	   {/if}
	   
	   
	   
	   
	   <tr style="border-top:1px solid #bbb"><td  class="aright" style="border-top:1px solid #bbb">{t}Net{/t}</td><td width=100 class="aright">{$order->get('Invoiced Total Net Amount')}</td></tr>
	   <tr><td  class="aright" >{t}Tax{/t}</td><td width=100 class="aright">{$order->get('Invoiced Total Tax Amount')}</td></tr>

	   <tr><td  class="aright" >{t}Total{/t}</td><td width=100 class="aright"><b>{$order->get('Invoiced Total Amount')}</b></td></tr>
	   
	 </table>
       </div>

       <div style="border:0px solid red;width:230px;float:right">
	 {if $note}<div class="notes">{$note}</div>{/if}
	 <table border=0  style="border-top:1px solid #333;border-bottom:1px solid #333;width:100%,padding-right:0px;margin-right:30px;float:right" >
	   
	   <tr><td>{t}Order Date{/t}:</td><td class="aright">{$order->get('Date')}</td></tr>
	   
	   <tr><td>{t}Invoices{/t}:</td><td class="aright">{$order->get('Order XHTML Invoices')}</td></tr>
	   <tr><td>{t}Delivery Notes{/t}:</td><td class="aright">{$order->get('Order XHTML Delivery Notes')}</td></tr>
	 </table>
	 
       </div>
       
       
       <div style="clear:both"></div>
     </div>


<h2>{t}Post-Order Items{/t}</h2>
      <div  id="table1" class="dtable btable" style="margin-bottom:0;font-size:80%"></div>


<h2>{t}Ordered Items{/t}</h2>
      <div  id="table0" class="dtable btable" style="margin-bottom:0;font-size:80%"></div>

	    
    </div>
{if $items_out_of_stock}
<div style="clear:both;margin:30px 0" >
<h2>{t}Items Out of Stock{/t}</h2>
<div  id="table1" class="dtable btable" style="margin-bottom:0"></div>
</div>
{/if}
  </div>
</div>
</div> 
{include file='footer.tpl'}
