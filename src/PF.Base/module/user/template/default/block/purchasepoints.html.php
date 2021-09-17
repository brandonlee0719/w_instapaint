<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_purchase_points">
	<form method="post" action="#" class="form">
		{_p var='how_many_points_would_you_like_to_purchase'}
		<select name="purchase" onchange="$(this).ajaxCall('user.processPurchasePoints');">
			<option value="">{_p var='select'}:</option>
			{foreach from=$aPurchasePoints item=iPurchasePoint}
			<option value="{$iPurchasePoint.id}">{$iPurchasePoint.cost}</option>
			{/foreach}
		</select>
	</form>
</div>