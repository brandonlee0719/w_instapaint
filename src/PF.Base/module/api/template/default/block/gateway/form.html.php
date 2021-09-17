<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if !PHPFOX_IS_AJAX}
    {template file='error.controller.display'}
{/if}
{plugin call='api.template_block_gateway_form_start'}
{if count($aGateways)}
	{foreach from=$aGateways name=gateways item=aGateway}
<form class="form" method="post" action="{$aGateway.form.url}"{if $aGateway.gateway_id == 'activitypoints'} onsubmit="$(this).ajaxCall('api.processActivityPayment'); return false;"{/if}
	{if $bIsThickBox}style="max-height: 400px; overflow: auto;"{/if}>
{foreach from=$aGateway.form.param key=sField item=sValue}
	<div><input type="hidden" name="{$sField}" value="{$sValue}" /></div>
{/foreach}
	<div class="{if is_int($phpfox.iteration.gateways/2)}row1{else}row2{/if}{if $phpfox.iteration.gateways == 1} row_first{/if}">
		<div class="gateway_title">{$aGateway.title}</div>
		<p class="help-block">
			{$aGateway.description}
		</p>
		<div class="p_4">
			{if $aGateway.gateway_id == 'activitypoints'}
			{_p var='purchase_points_info' yourpoints=$aGateway.yourpoints|number_format yourcost=$aGateway.yourcost|number_format}
			{/if}
			<input type="submit" value="{_p var='purchase_with_gateway_name' gateway_name=$aGateway.title}" class="btn btn-primary" />
		</div>
	</div>
</form>
{/foreach}
{else}
<p class="help-block">
	{_p var='opps_no_payment_gateways_have_been_set_up_yet'}
</p>
{/if}
{plugin call='api.template_block_gateway_form_end'}
