<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="warning">
	{_p var='member_snoop_text' user_name=$user_name full_name=$full_name user_link=$user_link}
	<br /><br />
	<form action="{url link='admincp.user.snoop' user=$aUser.user_id}" method="post" class="form">
		<input type="hidden" name="action" value="proceed">
		<a class="button linkAway" href="{url link='admincp'}">{_p var='abort_log_in_as_this_user'} </a>
		- <input type="submit" class="btn-primary btn" value="{_p var='log'}">
	</form>
</div>
