<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form action="{url link='admincp.user.group.activitypoints' id=$aUserGroup.user_group_id}" method="post">
	<input type="hidden" name="val[igroup]" value="{$aUserGroup.user_group_id}">
	{foreach from=$aPoints key=sModule item=aPoint}
		<div class="form-group">
			<label>{_p var='user_setting_points_'$aPoint.module}</label>
            <input type="text" class="form-control" name="val[module][{$aPoint.setting_id}]" value="{$aPoint.value_actual}" />
		</div>
	{/foreach}
	<div class="form-group">
		<input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
	</div>
</form>