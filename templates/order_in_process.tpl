{include file='header.tpl'}
<div id="bd" >
  <div id="yui-main">
    <div class="yui-b">

      <div class="yui-b" style="border:1px solid #ccc;text-align:left;padding:10px;margin: 30px 0 10px 0">

       <div style="xborder:1px solid #ddd;width:350px;float:left"> 
        <h1 style="padding:0 0 10px 0">{t}Order{/t} {$order->get('Order Public ID')}</h1>
        <h2 style="padding:0"><a href="customer.php?id={$order->get('order customer key')}">{$order->get('order customer name')} (ID:{$customer->get('Customer ID')})</a></h2>
        {$contact}<br/>
           {if $tel!=''}{t}Tel{/t}: {$tel}<br/>{/if}
	<div style="float:left;line-height: 1.0em;margin:5px 0px;color:#444"><span style="font-weight:500;color:#000">{t}Paid by{/t}</span>:<br/><b>{$customer->get('Customer Main Contact Name')}</b><br/>{$customer->get('Customer Main XHTML Address')}</div>
	<div style="float:left;line-height: 1.0em;margin:5px 0 0 30px;color:#444"><span style="font-weight:500;color:#000">{t}Shipped to{/t}</span>:<br/>{$order->get('Order XHTML Ship Tos')}</div>
	{if $address_delbill!=''}<div style="float:left;line-height: 1.0em;margin:5px 0px;color:#444"><span style="font-weight:500;color:#000">{t}B&D Address{/t}</span>:<br/>{$address_delbill}</div>{/if}

	{if $address_del!=''}<div style="float:left;line-height: 1.0em;margin:5px 0px;color:#444"><span style="font-weight:500;color:#000">{t}Delivery Address{/t}</span>:<br/>{$address_del}</div>{/if}
	{if $address_bill!=''}<div style="float:left;line-height: 1.0em;margin:5px 0 5px 10px;color:#444"><span style="font-weight:500;color:#000">{t}Billing Address{/t}</span>:<br/>{$address_bill}</div>{/if}
<div style="clear:both"></div>
       </div>

       <div style="border:0px solid #ddd;width:300px;float:left">
       {if $note}<div class="notes">{$note}</div>{/if}


<table border=0  style="border-top:1px solid #333;border-bottom:1px solid #333;width:100%,padding-right:20px;margin:0 30px;float:right" >

<tr><td>{t}Order Date{/t}:</td><td class="aright">{$order->get('Order Date')}</td></tr>

<tr><td>{t}Invoices{/t}:</td><td class="aright">{$order->get('Order XHTML Invoices')}</td></tr>
<tr><td>{t}Delivery Notes{/t}:</td><td class="aright">{$order->get('Order XHTML Delivery Notes')}</td></tr>
</table>

      </div>





<div style="border:0px solid #ddd;width:250px;float:right">
<table border=0  style="width:100%;border-top:1px solid #333;border-bottom:1px solid #333;width:100%,padding:0;margin:0;float:right;margin-left:120px" >
  {if $order->get('Order Items Discount Amount')!=0 }
  <tr><td  class="aright" >{t}Items Gross Amount{/t}</td><td width=100 class="aright">{$order->get('Order Items Gross Amount')}</td></tr>
  <tr><td  class="aright" >{t}Discounts Amount{/t}</td><td width=100 class="aright">-{$order->get('Order Items Discount Amount')}</td></tr>

{/if}
  <tr><td  class="aright" >{t}Items Net Amount{/t}</td><td width=100 class="aright">{$order->get('Order Items Net Amount')}</td></tr>
	  <tr><td  class="aright" >{t}Credits{/t}</td><td width=100 class="aright">{$order->get('Order Items Net Amount')}</td></tr>
	  {if $other_charges_vateable  }<tr><td  class="aright" >{t}Charges{/t}</td><td width=100 class="aright">{$order->get('Order Total Tax Amount')}}</td></tr>{/if}
 <tr style="border-bottom:1px solid #777><td  class="aright" >{t}Shipping{/t}</td><td width=100 class="aright">{$order->get('Order Shipping Net Amount')}</td></tr>	
  <tr style="border-bottom:1px solid #777"><td  class="aright" >{t}Refunds{/t}</td><td width=100 class="aright">{$order->get('Order Shipping Net Amount')}</td></tr>
	  <tr><td  class="aright" >{t}Net{/t}</td><td width=100 class="aright">{$order->get('Order Total Net Amount')}</td></tr>


	  <tr style="border-bottom:1px solid #777"><td  class="aright" >{t}VAT{/t}</td><td width=100 class="aright">{$order->get('Order Total Tax Amount')}</td></tr>
	  <tr><td  class="aright" >{t}Total{/t}</td><td width=100 class="aright"><b>{$order->get('Order Total Amount')}</b></td></tr>

	</table>
      </div>


<div style="clear:both"></div>
      </div>



<h2>{t}Items{/t}</h2>
      <div  id="table0" class="dtable btable" style="margin-bottom:0"></div>

	    
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
