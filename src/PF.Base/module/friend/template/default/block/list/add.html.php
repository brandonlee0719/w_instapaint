<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if $bIsLimit}
<div class="error_message" id="js_friend_list_add_error">
    {_p var="you_have_reached_your_limit"}
</div>
{else}
<div class="error_message" id="js_friend_list_add_error" style="display:none;"></div>
<form class="form" method="post" action="#" onsubmit="$Core.processForm('#js_friend_list_add_submit'); $(this).ajaxCall('friend.addList'); return false;">
	<input type="text" name="name" value="" size="40" class="form-control" autofocus/>
    <p class="help-block">
		{_p var='enter_the_name_of_your_custom_friends_list'}
	</p>
	<div class="p_top_4" id="js_friend_list_add_submit">
		<ul class="table_clear_button">
			<li><input type="submit" value="{_p var='submit'}" class="btn btn-primary" /></li>
			<li class="table_clear_ajax"></li>
		</ul>
		<div class="clear"></div>
	</div>
</form>
{/if}