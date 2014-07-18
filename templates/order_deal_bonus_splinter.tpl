	<table  style="float:right" border="0">
			{foreach from=$order->get_deal_bonus_items() key=bonus_deal_component_key item=component_order_promotion_bonus } 
			<tbody id="bonus_options_{$bonus_deal_component_key}">
				{foreach from=$component_order_promotion_bonus item=order_promotion_bonus } 
				<tr>
					<td style="padding-right:20px">{$order_promotion_bonus.code}</td>
					<td>{$order_promotion_bonus.name}</td>
					<td> 
					<img bonus_deal_component_key='{$bonus_deal_component_key}' id="order_promotion_bonus_checked_{$bonus_deal_component_key}_{$order_promotion_bonus.pid}" family_key="{$order_promotion_bonus.family_key}" product_key="{$order_promotion_bonus.product_key}" product_code="{$order_promotion_bonus.code}" pid="{$order_promotion_bonus.pid}" onclick="change_order_promotion_bonus(this,0)" style="{if !$order_promotion_bonus.selected}display:none{/if}" class="checkbox checkbox_checked" src="art/icons/checkbox_checked.png"> 
					<img bonus_deal_component_key='{$bonus_deal_component_key}' id="order_promotion_bonus_unchecked_{$bonus_deal_component_key}_{$order_promotion_bonus.pid}" family_key="{$order_promotion_bonus.family_key}" product_key="{$order_promotion_bonus.product_key}" product_code="{$order_promotion_bonus.code}" pid="{$order_promotion_bonus.pid}" onclick="change_order_promotion_bonus(this,1)" style="{if $order_promotion_bonus.selected}display:none{/if}" class="checkbox checkbox_unchecked" src="art/icons/checkbox_unchecked.png"> 
					</td>
				</tr>
				{/foreach} 
			</tbody>
			{/foreach} 
		</table>
		<div style="clear:both;height:10px">
		</div>