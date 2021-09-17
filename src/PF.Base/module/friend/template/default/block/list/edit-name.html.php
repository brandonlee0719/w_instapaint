<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="error_message" id="js_friend_list_edit_name_error" style="display:none;"></div>
<form class="form" method="post" action="#" onsubmit="$Core.processForm('#js_friend_list_edit_name_submit'); $(this).ajaxCall('friend.editListName', 'id={$aList.list_id}'); return false;">
	<input type="text" name="name" value="{$aList.name|clean}" size="40" class="form-control" autofocus/>
    <p class="help-block">
		{_p var='enter_the_name_of_your_custom_friends_list'}
	</p>
	<div class="p_top_4" id="js_friend_list_edit_name_submit">
		<ul class="table_clear_button">
			<li><input type="submit" value="{_p var='submit'}" class="btn btn-primary" /></li>
			<li class="table_clear_ajax"></li>
		</ul>
		<div class="clear"></div>
	</div>
</form>
